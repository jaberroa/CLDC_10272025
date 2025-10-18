import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Plus, Edit, MapPin, Globe, Users, Phone, Mail, Calendar } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface Seccional {
  id: string;
  nombre: string;
  tipo: string;
  pais: string;
  provincia: string | null;
  ciudad: string | null;
  direccion: string | null;
  telefono: string | null;
  email: string | null;
  coordinador_id: string | null;
  miembros_count: number;
  fecha_fundacion: string | null;
  estado: string;
  coordinador?: {
    nombre_completo: string;
    email: string;
  };
}

interface ComiteEjecutivo {
  id: string;
  seccional_id: string;
  miembro_id: string;
  cargo: string;
  fecha_inicio: string;
  fecha_fin: string | null;
  periodo: string;
  activo: boolean;
  miembro?: {
    nombre_completo: string;
    email: string;
  };
}

const SeccionalesManager = () => {
  const [seccionales, setSeccionales] = useState<Seccional[]>([]);
  const [comites, setComites] = useState<ComiteEjecutivo[]>([]);
  const [selectedSeccional, setSelectedSeccional] = useState<string>("");
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  // Form states
  const [nuevaSeccional, setNuevaSeccional] = useState({
    nombre: "",
    tipo: "provincial" as const,
    pais: "República Dominicana",
    provincia: "",
    ciudad: "",
    direccion: "",
    telefono: "",
    email: "",
    fecha_fundacion: ""
  });

  const [nuevoMiembroComite, setNuevoMiembroComite] = useState({
    seccional_id: "",
    miembro_id: "",
    cargo: "",
    fecha_inicio: "",
    fecha_fin: "",
    periodo: ""
  });

  useEffect(() => {
    fetchSeccionales();
    if (selectedSeccional) {
      fetchComiteEjecutivo(selectedSeccional);
    }
  }, [selectedSeccional]);

  const fetchSeccionales = async () => {
    try {
      // Use secure function to get public seccional info without contact details
      const { data: publicData, error: publicError } = await supabase
        .rpc('get_public_seccionales');

      if (publicError) throw publicError;

      // For users with proper roles, also fetch contact details
      const seccionalIds = publicData?.map(s => s.id) || [];
      const seccionalesWithContact = [];

      for (const seccional of publicData || []) {
        try {
          // Try to get contact details if user has permission
          const { data: contactData, error: contactError } = await supabase
            .rpc('get_seccional_contact_details', { seccional_id: seccional.id });

          if (contactData && contactData.length > 0) {
            // User has permission to see contact details
            seccionalesWithContact.push({
              ...seccional,
              telefono: contactData[0].telefono,
              email: contactData[0].email,
              direccion: contactData[0].direccion
            });
          } else {
            // User doesn't have permission, show without contact details
            seccionalesWithContact.push({
              ...seccional,
              telefono: null,
              email: null,
              direccion: null
            });
          }
        } catch (error) {
          // User doesn't have permission to see contact details
          seccionalesWithContact.push({
            ...seccional,
            telefono: null,
            email: null,
            direccion: null
          });
        }
      }

      // Get coordinator info
      const { data: coordinatorData, error: coordinatorError } = await supabase
        .from('miembros')
        .select('id, nombre_completo, email')
        .in('id', seccionalesWithContact.map(s => s.coordinador_id).filter(Boolean));

      if (!coordinatorError && coordinatorData) {
        const coordinatorMap = new Map(coordinatorData.map(c => [c.id, c]));
        seccionalesWithContact.forEach(seccional => {
          if (seccional.coordinador_id) {
            seccional.coordinador = coordinatorMap.get(seccional.coordinador_id);
          }
        });
      }

      setSeccionales(seccionalesWithContact);
    } catch (error) {
      console.error('Error fetching seccionales:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar las seccionales",
        variant: "destructive"
      });
    } finally {
      setLoading(false);
    }
  };

  const fetchComiteEjecutivo = async (seccionalId: string) => {
    try {
      const { data, error } = await supabase
        .from('comites_ejecutivos_seccionales')
        .select(`
          *,
          miembro:miembros(nombre_completo, email)
        `)
        .eq('seccional_id', seccionalId)
        .eq('activo', true)
        .order('cargo');

      if (error) throw error;
      setComites(data || []);
    } catch (error) {
      console.error('Error fetching comité ejecutivo:', error);
      toast({
        title: "Error",
        description: "No se pudo cargar el comité ejecutivo",
        variant: "destructive"
      });
    }
  };

  const handleCreateSeccional = async () => {
    try {
      const { data, error } = await supabase
        .from('seccionales')
        .insert([{
          nombre: nuevaSeccional.nombre,
          tipo: nuevaSeccional.tipo,
          pais: nuevaSeccional.pais,
          provincia: nuevaSeccional.provincia || null,
          ciudad: nuevaSeccional.ciudad || null,
          direccion: nuevaSeccional.direccion || null,
          telefono: nuevaSeccional.telefono || null,
          email: nuevaSeccional.email || null,
          fecha_fundacion: nuevaSeccional.fecha_fundacion || null,
          estado: 'en_formacion',
          miembros_count: 0
        }])
        .select()
        .single();

      if (error) throw error;

      setSeccionales([...seccionales, data]);
      setNuevaSeccional({
        nombre: "",
        tipo: "provincial",
        pais: "República Dominicana",
        provincia: "",
        ciudad: "",
        direccion: "",
        telefono: "",
        email: "",
        fecha_fundacion: ""
      });

      toast({
        title: "Éxito",
        description: "Seccional creada correctamente"
      });
    } catch (error) {
      console.error('Error creating seccional:', error);
      toast({
        title: "Error",
        description: "No se pudo crear la seccional",
        variant: "destructive"
      });
    }
  };

  const handleCreateMiembroComite = async () => {
    try {
      const { data, error } = await supabase
        .from('comites_ejecutivos_seccionales')
        .insert([{
          seccional_id: nuevoMiembroComite.seccional_id,
          miembro_id: nuevoMiembroComite.miembro_id,
          cargo: nuevoMiembroComite.cargo,
          fecha_inicio: nuevoMiembroComite.fecha_inicio,
          fecha_fin: nuevoMiembroComite.fecha_fin || null,
          periodo: nuevoMiembroComite.periodo,
          activo: true
        }]);

      if (error) throw error;

      if (selectedSeccional) {
        fetchComiteEjecutivo(selectedSeccional);
      }

      setNuevoMiembroComite({
        seccional_id: "",
        miembro_id: "",
        cargo: "",
        fecha_inicio: "",
        fecha_fin: "",
        periodo: ""
      });

      toast({
        title: "Éxito",
        description: "Miembro del comité agregado correctamente"
      });
    } catch (error) {
      console.error('Error creating miembro comité:', error);
      toast({
        title: "Error",
        description: "No se pudo agregar el miembro al comité",
        variant: "destructive"
      });
    }
  };

  const getEstadoBadgeVariant = (estado: string) => {
    switch (estado) {
      case 'activa':
        return 'default';
      case 'inactiva':
        return 'secondary';
      case 'en_formacion':
        return 'outline';
      default:
        return 'default';
    }
  };

  const getTipoBadgeVariant = (tipo: string) => {
    switch (tipo) {
      case 'provincial':
        return 'default';
      case 'regional':
        return 'secondary';
      case 'diaspora':
        return 'destructive';
      default:
        return 'outline';
    }
  };

  const getIconForTipo = (tipo: string) => {
    return tipo === 'diaspora' ? <Globe className="h-4 w-4" /> : <MapPin className="h-4 w-4" />;
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  return (
    <div className="space-y-6">
      <Tabs defaultValue="seccionales" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="seccionales">Seccionales</TabsTrigger>
          <TabsTrigger value="comites">Comités Ejecutivos</TabsTrigger>
          <TabsTrigger value="nueva">Nueva Seccional</TabsTrigger>
        </TabsList>

        <TabsContent value="seccionales" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <MapPin className="h-5 w-5" />
                Seccionales Provinciales, Regionales y de la Diáspora
              </CardTitle>
              <CardDescription>
                Gestiona las representaciones territoriales del CLDC
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {seccionales.map((seccional) => (
                  <Card key={seccional.id} className="p-4">
                    <div className="space-y-3">
                      <div className="flex items-start justify-between">
                        <div className="space-y-1">
                          <div className="flex items-center gap-2">
                            {getIconForTipo(seccional.tipo)}
                            <h3 className="font-semibold text-sm">{seccional.nombre}</h3>
                          </div>
                          <div className="flex gap-1">
                            <Badge variant={getTipoBadgeVariant(seccional.tipo) as any} className="text-xs">
                              {seccional.tipo}
                            </Badge>
                            <Badge variant={getEstadoBadgeVariant(seccional.estado) as any} className="text-xs">
                              {seccional.estado}
                            </Badge>
                          </div>
                        </div>
                      </div>

                      <div className="space-y-2 text-xs text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Globe className="h-3 w-3" />
                          <span>{seccional.pais}</span>
                        </div>
                        
                        {seccional.provincia && (
                          <div className="flex items-center gap-1">
                            <MapPin className="h-3 w-3" />
                            <span>{seccional.provincia}{seccional.ciudad && `, ${seccional.ciudad}`}</span>
                          </div>
                        )}

                        {seccional.coordinador && (
                          <div className="flex items-center gap-1">
                            <Users className="h-3 w-3" />
                            <span>Coordinador: {seccional.coordinador.nombre_completo}</span>
                          </div>
                        )}

                        <div className="flex items-center gap-1">
                          <Users className="h-3 w-3" />
                          <span>Miembros: {seccional.miembros_count}</span>
                        </div>

                        {seccional.email ? (
                          <div className="flex items-center gap-1">
                            <Mail className="h-3 w-3" />
                            <span className="truncate">{seccional.email}</span>
                          </div>
                        ) : (
                          <div className="flex items-center gap-1 text-muted-foreground/50">
                            <Mail className="h-3 w-3" />
                            <span className="text-xs italic">Email protegido</span>
                          </div>
                        )}

                        {seccional.telefono ? (
                          <div className="flex items-center gap-1">
                            <Phone className="h-3 w-3" />
                            <span>{seccional.telefono}</span>
                          </div>
                        ) : (
                          <div className="flex items-center gap-1 text-muted-foreground/50">
                            <Phone className="h-3 w-3" />
                            <span className="text-xs italic">Teléfono protegido</span>
                          </div>
                        )}

                        {seccional.fecha_fundacion && (
                          <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>Fundada: {new Date(seccional.fecha_fundacion).toLocaleDateString()}</span>
                          </div>
                        )}
                      </div>

                      <div className="flex justify-end gap-1">
                        <Button
                          size="sm"
                          variant="outline"
                          onClick={() => setSelectedSeccional(seccional.id)}
                        >
                          Ver Comité
                        </Button>
                        <Button size="sm" variant="ghost" className="h-6 w-6 p-0">
                          <Edit className="h-3 w-3" />
                        </Button>
                      </div>
                    </div>
                  </Card>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="comites" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Comités Ejecutivos Seccionales</CardTitle>
              <CardDescription>
                Gestiona los miembros de los comités ejecutivos de cada seccional
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="select-seccional">Seleccionar Seccional</Label>
                  <Select value={selectedSeccional} onValueChange={setSelectedSeccional}>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar seccional" />
                    </SelectTrigger>
                    <SelectContent>
                      {seccionales.map((seccional) => (
                        <SelectItem key={seccional.id} value={seccional.id}>
                          {seccional.nombre} ({seccional.tipo})
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                {selectedSeccional && (
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      {comites.map((miembro) => (
                        <Card key={miembro.id} className="p-4">
                          <div className="space-y-3">
                            <div className="space-y-1">
                              <h3 className="font-semibold text-sm">
                                {miembro.miembro?.nombre_completo}
                              </h3>
                              <Badge variant="outline" className="text-xs">
                                {miembro.cargo}
                              </Badge>
                            </div>
                            
                            <div className="space-y-1 text-xs text-muted-foreground">
                              <p>Período: {miembro.periodo}</p>
                              <p>Desde: {new Date(miembro.fecha_inicio).toLocaleDateString()}</p>
                              {miembro.fecha_fin && (
                                <p>Hasta: {new Date(miembro.fecha_fin).toLocaleDateString()}</p>
                              )}
                            </div>

                            {miembro.miembro?.email && (
                              <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                <Mail className="h-3 w-3" />
                                <span className="truncate">{miembro.miembro.email}</span>
                              </div>
                            )}
                          </div>
                        </Card>
                      ))}
                    </div>

                    {/* Agregar nuevo miembro al comité */}
                    <Card>
                      <CardHeader>
                        <CardTitle className="text-lg">Agregar Miembro al Comité</CardTitle>
                      </CardHeader>
                      <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div className="space-y-2">
                            <Label>Cargo</Label>
                            <Select
                              value={nuevoMiembroComite.cargo}
                              onValueChange={(value) => setNuevoMiembroComite({...nuevoMiembroComite, cargo: value})}
                            >
                              <SelectTrigger>
                                <SelectValue placeholder="Seleccionar cargo" />
                              </SelectTrigger>
                              <SelectContent>
                                <SelectItem value="Coordinador Seccional">Coordinador Seccional</SelectItem>
                                <SelectItem value="Secretario">Secretario</SelectItem>
                                <SelectItem value="Tesorero">Tesorero</SelectItem>
                                <SelectItem value="Vocal de Comunicación">Vocal de Comunicación</SelectItem>
                                <SelectItem value="Vocal de Formación">Vocal de Formación</SelectItem>
                                <SelectItem value="Vocal de Integración Cultural">Vocal de Integración Cultural</SelectItem>
                              </SelectContent>
                            </Select>
                          </div>

                          <div className="space-y-2">
                            <Label>Período</Label>
                            <Input
                              value={nuevoMiembroComite.periodo}
                              onChange={(e) => setNuevoMiembroComite({...nuevoMiembroComite, periodo: e.target.value})}
                              placeholder="2025-2028"
                            />
                          </div>

                          <div className="space-y-2">
                            <Label>Fecha de Inicio</Label>
                            <Input
                              type="date"
                              value={nuevoMiembroComite.fecha_inicio}
                              onChange={(e) => setNuevoMiembroComite({...nuevoMiembroComite, fecha_inicio: e.target.value})}
                            />
                          </div>

                          <div className="space-y-2">
                            <Label>Fecha de Fin (opcional)</Label>
                            <Input
                              type="date"
                              value={nuevoMiembroComite.fecha_fin}
                              onChange={(e) => setNuevoMiembroComite({...nuevoMiembroComite, fecha_fin: e.target.value})}
                            />
                          </div>
                        </div>

                        <Button
                          onClick={() => {
                            setNuevoMiembroComite({...nuevoMiembroComite, seccional_id: selectedSeccional});
                            handleCreateMiembroComite();
                          }}
                          className="w-full"
                          disabled={!nuevoMiembroComite.cargo || !nuevoMiembroComite.fecha_inicio || !nuevoMiembroComite.periodo}
                        >
                          <Plus className="h-4 w-4 mr-2" />
                          Agregar Miembro
                        </Button>
                      </CardContent>
                    </Card>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="nueva" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Plus className="h-5 w-5" />
                Crear Nueva Seccional
              </CardTitle>
              <CardDescription>
                Registra una nueva seccional provincial, regional o de la diáspora
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="nombre">Nombre de la Seccional</Label>
                  <Input
                    id="nombre"
                    value={nuevaSeccional.nombre}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, nombre: e.target.value})}
                    placeholder="Ej: Seccional Santiago"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo</Label>
                  <Select
                    value={nuevaSeccional.tipo}
                    onValueChange={(value) => setNuevaSeccional({...nuevaSeccional, tipo: value as any})}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="provincial">Provincial</SelectItem>
                      <SelectItem value="regional">Regional</SelectItem>
                      <SelectItem value="diaspora">Diáspora</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="pais">País</Label>
                  <Input
                    id="pais"
                    value={nuevaSeccional.pais}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, pais: e.target.value})}
                    placeholder="Ej: República Dominicana"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="provincia">Provincia/Estado</Label>
                  <Input
                    id="provincia"
                    value={nuevaSeccional.provincia}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, provincia: e.target.value})}
                    placeholder="Ej: Santiago"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="ciudad">Ciudad</Label>
                  <Input
                    id="ciudad"
                    value={nuevaSeccional.ciudad}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, ciudad: e.target.value})}
                    placeholder="Ej: Santiago de los Caballeros"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="fecha_fundacion">Fecha de Fundación</Label>
                  <Input
                    id="fecha_fundacion"
                    type="date"
                    value={nuevaSeccional.fecha_fundacion}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, fecha_fundacion: e.target.value})}
                  />
                </div>

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="direccion">Dirección</Label>
                  <Input
                    id="direccion"
                    value={nuevaSeccional.direccion}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, direccion: e.target.value})}
                    placeholder="Dirección completa de la seccional"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="telefono">Teléfono</Label>
                  <Input
                    id="telefono"
                    value={nuevaSeccional.telefono}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, telefono: e.target.value})}
                    placeholder="+1 809-555-0100"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">Email</Label>
                  <Input
                    id="email"
                    type="email"
                    value={nuevaSeccional.email}
                    onChange={(e) => setNuevaSeccional({...nuevaSeccional, email: e.target.value})}
                    placeholder="santiago@cldc.org.do"
                  />
                </div>
              </div>

              <Button 
                onClick={handleCreateSeccional} 
                className="w-full"
                disabled={!nuevaSeccional.nombre || !nuevaSeccional.tipo || !nuevaSeccional.pais}
              >
                <Plus className="h-4 w-4 mr-2" />
                Crear Seccional
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default SeccionalesManager;