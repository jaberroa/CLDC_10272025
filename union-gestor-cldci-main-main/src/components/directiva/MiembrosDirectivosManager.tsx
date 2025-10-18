import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { FileUploader } from "@/components/ui/file-uploader";
import { Plus, Edit, Trash2, Crown, Users, Phone, Mail, Calendar } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface MiembroDirectivo {
  id: string;
  miembro_id: string;
  organo_id: string;
  cargo_id: string;
  fecha_inicio: string;
  fecha_fin: string | null;
  periodo: string;
  estado: string;
  semblanza: string;
  es_presidente: boolean;
  foto_url: string | null;
  email_institucional: string | null;
  telefono_institucional: string | null;
  miembro?: {
    nombre_completo: string;
    email: string;
    telefono: string;
  };
  organo?: {
    nombre: string;
    tipo_organo: string;
  };
  cargo?: {
    nombre_cargo: string;
  };
}

interface Organo {
  id: string;
  nombre: string;
  tipo_organo: string;
}

interface Cargo {
  id: string;
  nombre_cargo: string;
  organo_id: string;
}

interface Miembro {
  id: string;
  nombre_completo: string;
  email: string;
}

const MiembrosDirectivosManager = () => {
  const [miembrosDirectivos, setMiembrosDirectivos] = useState<MiembroDirectivo[]>([]);
  const [organos, setOrganos] = useState<Organo[]>([]);
  const [cargos, setCargos] = useState<Cargo[]>([]);
  const [miembros, setMiembros] = useState<Miembro[]>([]);
  const [selectedOrgano, setSelectedOrgano] = useState<string>("all");
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  // Form state
  const [nuevoDirectivo, setNuevoDirectivo] = useState({
    miembro_id: "",
    organo_id: "",
    cargo_id: "",
    fecha_inicio: "",
    fecha_fin: "",
    periodo: "",
    semblanza: "",
    es_presidente: false,
    email_institucional: "",
    telefono_institucional: ""
  });

  const [editingDirectivo, setEditingDirectivo] = useState<string | null>(null);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      await Promise.all([
        fetchMiembrosDirectivos(),
        fetchOrganos(),
        fetchCargos(),
        fetchMiembros()
      ]);
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchMiembrosDirectivos = async () => {
    try {
      // Use secure function to get public board member info without contact details
      const { data: publicData, error: publicError } = await supabase
        .rpc('get_public_miembros_directivos');

      if (publicError) throw publicError;

      // For users with proper roles, also fetch contact details
      const directivosWithContact = [];

      for (const directivo of publicData || []) {
        try {
          // Try to get contact details if user has permission
          const { data: contactData, error: contactError } = await supabase
            .rpc('get_miembro_directivo_contact_details', { miembro_directivo_id: directivo.id });

          if (contactData && contactData.length > 0) {
            // User has permission to see contact details
            directivosWithContact.push({
              ...directivo,
              email_institucional: contactData[0].email_institucional,
              telefono_institucional: contactData[0].telefono_institucional
            });
          } else {
            // User doesn't have permission, show without contact details
            directivosWithContact.push({
              ...directivo,
              email_institucional: null,
              telefono_institucional: null
            });
          }
        } catch (error) {
          // User doesn't have permission to see contact details
          directivosWithContact.push({
            ...directivo,
            email_institucional: null,
            telefono_institucional: null
          });
        }
      }

      // Get related data
      const [miembrosData, organosData, cargosData] = await Promise.all([
        supabase
          .from('miembros')
          .select('id, nombre_completo, email, telefono')
          .in('id', directivosWithContact.map(d => d.miembro_id).filter(Boolean)),
        supabase
          .from('organos_cldc')
          .select('id, nombre, tipo_organo')
          .in('id', directivosWithContact.map(d => d.organo_id).filter(Boolean)),
        supabase
          .from('cargos_organos')
          .select('id, nombre_cargo')
          .in('id', directivosWithContact.map(d => d.cargo_id).filter(Boolean))
      ]);

      // Create maps for efficient lookups
      const miembrosMap = new Map((miembrosData.data || []).map(m => [m.id, m]));
      const organosMap = new Map((organosData.data || []).map(o => [o.id, o]));
      const cargosMap = new Map((cargosData.data || []).map(c => [c.id, c]));

      // Merge the data
      const enrichedDirectivos = directivosWithContact.map(directivo => ({
        ...directivo,
        miembro: miembrosMap.get(directivo.miembro_id),
        organo: organosMap.get(directivo.organo_id),
        cargo: cargosMap.get(directivo.cargo_id)
      }));

      // Sort by presidente first, then by fecha_inicio
      enrichedDirectivos.sort((a, b) => {
        if (a.es_presidente && !b.es_presidente) return -1;
        if (!a.es_presidente && b.es_presidente) return 1;
        return new Date(b.fecha_inicio).getTime() - new Date(a.fecha_inicio).getTime();
      });

      setMiembrosDirectivos(enrichedDirectivos);
    } catch (error) {
      console.error('Error fetching miembros directivos:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar los miembros directivos",
        variant: "destructive"
      });
    }
  };

  const fetchOrganos = async () => {
    try {
      const { data, error } = await supabase
        .from('organos_cldc')
        .select('id, nombre, tipo_organo')
        .eq('activo', true)
        .order('tipo_organo');

      if (error) throw error;
      setOrganos(data || []);
    } catch (error) {
      console.error('Error fetching órganos:', error);
    }
  };

  const fetchCargos = async () => {
    try {
      const { data, error } = await supabase
        .from('cargos_organos')
        .select('id, nombre_cargo, organo_id')
        .eq('activo', true)
        .order('nivel_autoridad');

      if (error) throw error;
      setCargos(data || []);
    } catch (error) {
      console.error('Error fetching cargos:', error);
    }
  };

  const fetchMiembros = async () => {
    try {
      const { data, error } = await supabase
        .from('miembros')
        .select('id, nombre_completo, email')
        .eq('estado_membresia', 'activa')
        .order('nombre_completo');

      if (error) throw error;
      setMiembros(data || []);
    } catch (error) {
      console.error('Error fetching miembros:', error);
    }
  };

  const handleCreateDirectivo = async () => {
    try {
      const { data, error } = await supabase
        .from('miembros_directivos')
        .insert([{
          miembro_id: nuevoDirectivo.miembro_id,
          organo_id: nuevoDirectivo.organo_id,
          cargo_id: nuevoDirectivo.cargo_id,
          fecha_inicio: nuevoDirectivo.fecha_inicio,
          fecha_fin: nuevoDirectivo.fecha_fin || null,
          periodo: nuevoDirectivo.periodo,
          semblanza: nuevoDirectivo.semblanza,
          es_presidente: nuevoDirectivo.es_presidente,
          email_institucional: nuevoDirectivo.email_institucional,
          telefono_institucional: nuevoDirectivo.telefono_institucional,
          estado: 'activo'
        }]);

      if (error) throw error;

      await fetchMiembrosDirectivos();
      setNuevoDirectivo({
        miembro_id: "",
        organo_id: "",
        cargo_id: "",
        fecha_inicio: "",
        fecha_fin: "",
        periodo: "",
        semblanza: "",
        es_presidente: false,
        email_institucional: "",
        telefono_institucional: ""
      });

      toast({
        title: "Éxito",
        description: "Miembro directivo agregado correctamente"
      });
    } catch (error) {
      console.error('Error creating directivo:', error);
      toast({
        title: "Error",
        description: "No se pudo agregar el miembro directivo",
        variant: "destructive"
      });
    }
  };

  const handleDeleteDirectivo = async (id: string) => {
    try {
      const { error } = await supabase
        .from('miembros_directivos')
        .update({ estado: 'inactivo' })
        .eq('id', id);

      if (error) throw error;

      await fetchMiembrosDirectivos();
      toast({
        title: "Éxito",
        description: "Miembro directivo desactivado correctamente"
      });
    } catch (error) {
      console.error('Error deleting directivo:', error);
      toast({
        title: "Error",
        description: "No se pudo desactivar el miembro directivo",
        variant: "destructive"
      });
    }
  };

  const getCargosForOrgano = (organoId: string) => {
    return cargos.filter(cargo => cargo.organo_id === organoId);
  };

  const getEstadoBadgeVariant = (estado: string) => {
    switch (estado) {
      case 'activo':
        return 'default';
      case 'inactivo':
        return 'secondary';
      case 'suspendido':
        return 'destructive';
      default:
        return 'outline';
    }
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  const filteredDirectivos = selectedOrgano && selectedOrgano !== "all"
    ? miembrosDirectivos.filter(d => d.organo_id === selectedOrgano)
    : miembrosDirectivos;

  return (
    <div className="space-y-6">
      {/* Filtros */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Users className="h-5 w-5" />
            Miembros Directivos Actuales
          </CardTitle>
          <CardDescription>
            Gestiona los miembros actuales de todos los órganos del CLDC
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2 mb-6">
            <Label htmlFor="filter-organo">Filtrar por Órgano</Label>
            <Select value={selectedOrgano} onValueChange={setSelectedOrgano}>
              <SelectTrigger>
                <SelectValue placeholder="Todos los órganos" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos los órganos</SelectItem>
                {organos.map((organo) => (
                  <SelectItem key={organo.id} value={organo.id}>
                    {organo.nombre}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Lista de Directivos */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {filteredDirectivos.length === 0 ? (
              <div className="col-span-full text-center py-8">
                <Crown className="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                <h3 className="text-lg font-medium mb-2">No hay miembros directivos</h3>
                <p className="text-muted-foreground mb-4">
                  Comienza agregando el primer miembro directivo a los órganos del CLDC
                </p>
              </div>
            ) : (
              filteredDirectivos.map((directivo) => (
                <Card key={directivo.id} className={`p-4 ${directivo.es_presidente ? 'ring-2 ring-yellow-400' : ''}`}>
                  <div className="space-y-3">
                    <div className="flex items-start justify-between">
                      <div className="flex items-center gap-3">
                        <Avatar className="h-12 w-12">
                          <AvatarImage src={directivo.foto_url || ''} />
                          <AvatarFallback>
                            {directivo.miembro?.nombre_completo?.split(' ').map(n => n[0]).join('') || 'MD'}
                          </AvatarFallback>
                        </Avatar>
                        
                        <div className="space-y-1">
                          <div className="flex items-center gap-1">
                            <h3 className="font-semibold text-sm">
                              {directivo.miembro?.nombre_completo}
                            </h3>
                            {directivo.es_presidente && (
                              <Crown className="h-4 w-4 text-yellow-500" />
                            )}
                          </div>
                          <p className="text-xs text-muted-foreground">
                            {directivo.cargo?.nombre_cargo}
                          </p>
                        </div>
                      </div>

                      <Badge variant={getEstadoBadgeVariant(directivo.estado) as any}>
                        {directivo.estado}
                      </Badge>
                    </div>

                    <div className="space-y-1">
                      <p className="text-xs font-medium">{directivo.organo?.nombre}</p>
                      <p className="text-xs text-muted-foreground">
                        Período: {directivo.periodo}
                      </p>
                    </div>

                    {directivo.semblanza && (
                      <p className="text-xs text-muted-foreground line-clamp-2">
                        {directivo.semblanza}
                      </p>
                    )}

                    <div className="flex items-center gap-4 text-xs text-muted-foreground">
                      {directivo.email_institucional ? (
                        <div className="flex items-center gap-1">
                          <Mail className="h-3 w-3" />
                          <span className="truncate">{directivo.email_institucional}</span>
                        </div>
                      ) : (
                        <div className="flex items-center gap-1 text-muted-foreground/50">
                          <Mail className="h-3 w-3" />
                          <span className="text-xs italic">Email protegido</span>
                        </div>
                      )}
                      {directivo.telefono_institucional ? (
                        <div className="flex items-center gap-1">
                          <Phone className="h-3 w-3" />
                          <span>{directivo.telefono_institucional}</span>
                        </div>
                      ) : (
                        <div className="flex items-center gap-1 text-muted-foreground/50">
                          <Phone className="h-3 w-3" />
                          <span className="text-xs italic">Teléfono protegido</span>
                        </div>
                      )}
                    </div>

                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Calendar className="h-3 w-3" />
                      <span>Desde: {new Date(directivo.fecha_inicio).toLocaleDateString()}</span>
                    </div>

                    <div className="flex justify-end gap-1">
                      <Button
                        size="sm"
                        variant="ghost"
                        className="h-6 w-6 p-0"
                        onClick={() => setEditingDirectivo(directivo.id)}
                      >
                        <Edit className="h-3 w-3" />
                      </Button>
                      <Button
                        size="sm"
                        variant="ghost"
                        className="h-6 w-6 p-0 text-destructive hover:text-destructive"
                        onClick={() => handleDeleteDirectivo(directivo.id)}
                      >
                        <Trash2 className="h-3 w-3" />
                      </Button>
                    </div>
                  </div>
                </Card>
              ))
            )}
          </div>
        </CardContent>
      </Card>

      {/* Agregar Nuevo Directivo */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Plus className="h-5 w-5" />
            Agregar Nuevo Miembro Directivo
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="miembro">Miembro</Label>
              <Select
                value={nuevoDirectivo.miembro_id}
                onValueChange={(value) => setNuevoDirectivo({...nuevoDirectivo, miembro_id: value})}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar miembro" />
                </SelectTrigger>
                <SelectContent>
                  {miembros.map((miembro) => (
                    <SelectItem key={miembro.id} value={miembro.id}>
                      {miembro.nombre_completo}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="organo">Órgano</Label>
              <Select
                value={nuevoDirectivo.organo_id}
                onValueChange={(value) => setNuevoDirectivo({...nuevoDirectivo, organo_id: value, cargo_id: ""})}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar órgano" />
                </SelectTrigger>
                <SelectContent>
                  {organos.map((organo) => (
                    <SelectItem key={organo.id} value={organo.id}>
                      {organo.nombre}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="cargo">Cargo</Label>
              <Select
                value={nuevoDirectivo.cargo_id}
                onValueChange={(value) => setNuevoDirectivo({...nuevoDirectivo, cargo_id: value})}
                disabled={!nuevoDirectivo.organo_id}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar cargo" />
                </SelectTrigger>
                <SelectContent>
                  {getCargosForOrgano(nuevoDirectivo.organo_id).map((cargo) => (
                    <SelectItem key={cargo.id} value={cargo.id}>
                      {cargo.nombre_cargo}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="periodo">Período</Label>
              <Input
                id="periodo"
                value={nuevoDirectivo.periodo}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, periodo: e.target.value})}
                placeholder="Ej: 2025-2028"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="fecha-inicio">Fecha de Inicio</Label>
              <Input
                id="fecha-inicio"
                type="date"
                value={nuevoDirectivo.fecha_inicio}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, fecha_inicio: e.target.value})}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="fecha-fin">Fecha de Fin (opcional)</Label>
              <Input
                id="fecha-fin"
                type="date"
                value={nuevoDirectivo.fecha_fin}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, fecha_fin: e.target.value})}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="email-inst">Email Institucional</Label>
              <Input
                id="email-inst"
                type="email"
                value={nuevoDirectivo.email_institucional}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, email_institucional: e.target.value})}
                placeholder="presidente@cldc.org.do"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="telefono-inst">Teléfono Institucional</Label>
              <Input
                id="telefono-inst"
                value={nuevoDirectivo.telefono_institucional}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, telefono_institucional: e.target.value})}
                placeholder="+1 809-555-0123"
              />
            </div>

            <div className="flex items-center space-x-2 md:col-span-2">
              <input
                type="checkbox"
                id="es-presidente"
                checked={nuevoDirectivo.es_presidente}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, es_presidente: e.target.checked})}
                className="rounded"
              />
              <Label htmlFor="es-presidente">Es Presidente/Máxima Autoridad</Label>
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="semblanza">Semblanza</Label>
              <Textarea
                id="semblanza"
                value={nuevoDirectivo.semblanza}
                onChange={(e) => setNuevoDirectivo({...nuevoDirectivo, semblanza: e.target.value})}
                placeholder="Descripción profesional y logros del directivo"
                rows={3}
              />
            </div>
          </div>

          <Button 
            onClick={handleCreateDirectivo} 
            className="w-full"
            disabled={!nuevoDirectivo.miembro_id || !nuevoDirectivo.organo_id || !nuevoDirectivo.cargo_id || !nuevoDirectivo.fecha_inicio || !nuevoDirectivo.periodo}
          >
            <Plus className="h-4 w-4 mr-2" />
            Agregar Miembro Directivo
          </Button>
        </CardContent>
      </Card>
    </div>
  );
};

export default MiembrosDirectivosManager;