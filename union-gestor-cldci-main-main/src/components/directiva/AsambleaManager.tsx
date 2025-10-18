import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Plus, Edit, Users2, Calendar, MapPin, Video, FileText, Check } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface AsambleaGeneral {
  id: string;
  tipo_asamblea: string;
  fecha_convocatoria: string;
  fecha_celebracion: string;
  lugar: string;
  modalidad: string;
  enlace_virtual: string | null;
  quorum_minimo: number;
  asistentes_count: number;
  quorum_alcanzado: boolean;
  tema_principal: string;
  orden_dia: string[];
  acta_url: string | null;
  estado: string;
}

interface DelegadoAsamblea {
  id: string;
  asamblea_id: string;
  miembro_id: string;
  tipo_delegado: string;
  organizacion_origen_id: string | null;
  presente: boolean;
  observaciones: string | null;
  miembro?: {
    nombre_completo: string;
    email: string;
  };
}

const AsambleaManager = () => {
  const [asambleas, setAsambleas] = useState<AsambleaGeneral[]>([]);
  const [delegados, setDelegados] = useState<DelegadoAsamblea[]>([]);
  const [selectedAsamblea, setSelectedAsamblea] = useState<string>("");
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  // Form states
  const [nuevaAsamblea, setNuevaAsamblea] = useState({
    tipo_asamblea: "ordinaria",
    fecha_convocatoria: "",
    fecha_celebracion: "",
    lugar: "",
    modalidad: "presencial",
    enlace_virtual: "",
    quorum_minimo: 50,
    tema_principal: "",
    orden_dia: ""
  });

  useEffect(() => {
    fetchAsambleas();
    if (selectedAsamblea) {
      fetchDelegados(selectedAsamblea);
    }
  }, [selectedAsamblea]);

  const fetchAsambleas = async () => {
    try {
      const { data, error } = await supabase
        .from('asambleas_generales')
        .select('*')
        .order('fecha_celebracion', { ascending: false });

      if (error) throw error;
      setAsambleas(data || []);
    } catch (error) {
      console.error('Error fetching asambleas:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar las asambleas",
        variant: "destructive"
      });
    } finally {
      setLoading(false);
    }
  };

  const fetchDelegados = async (asambleaId: string) => {
    try {
      const { data, error } = await supabase
        .from('delegados_asamblea')
        .select(`
          *,
          miembro:miembros(nombre_completo, email)
        `)
        .eq('asamblea_id', asambleaId)
        .order('tipo_delegado');

      if (error) throw error;
      setDelegados(data || []);
    } catch (error) {
      console.error('Error fetching delegados:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar los delegados",
        variant: "destructive"
      });
    }
  };

  const handleCreateAsamblea = async () => {
    try {
      const ordenDiaArray = nuevaAsamblea.orden_dia.split('\n').filter(item => item.trim());
      
      const { data, error } = await supabase
        .from('asambleas_generales')
        .insert([{
          tipo_asamblea: nuevaAsamblea.tipo_asamblea,
          fecha_convocatoria: nuevaAsamblea.fecha_convocatoria,
          fecha_celebracion: nuevaAsamblea.fecha_celebracion,
          lugar: nuevaAsamblea.lugar,
          modalidad: nuevaAsamblea.modalidad,
          enlace_virtual: nuevaAsamblea.enlace_virtual || null,
          quorum_minimo: nuevaAsamblea.quorum_minimo,
          tema_principal: nuevaAsamblea.tema_principal,
          orden_dia: ordenDiaArray,
          estado: 'convocada'
        }])
        .select()
        .single();

      if (error) throw error;

      setAsambleas([data, ...asambleas]);
      setNuevaAsamblea({
        tipo_asamblea: "ordinaria",
        fecha_convocatoria: "",
        fecha_celebracion: "",
        lugar: "",
        modalidad: "presencial",
        enlace_virtual: "",
        quorum_minimo: 50,
        tema_principal: "",
        orden_dia: ""
      });

      toast({
        title: "Éxito",
        description: "Asamblea General convocada correctamente"
      });
    } catch (error) {
      console.error('Error creating asamblea:', error);
      toast({
        title: "Error",
        description: "No se pudo convocar la asamblea",
        variant: "destructive"
      });
    }
  };

  const updateAsistencia = async (delegadoId: string, presente: boolean) => {
    try {
      const { error } = await supabase
        .from('delegados_asamblea')
        .update({ presente })
        .eq('id', delegadoId);

      if (error) throw error;

      // Update local state
      setDelegados(delegados.map(d => 
        d.id === delegadoId ? { ...d, presente } : d
      ));

      // Update asistentes count
      if (selectedAsamblea) {
        const asistentesCount = delegados.filter(d => 
          d.id === delegadoId ? presente : d.presente
        ).length;

        await supabase
          .from('asambleas_generales')
          .update({ 
            asistentes_count: asistentesCount,
            quorum_alcanzado: asistentesCount >= (asambleas.find(a => a.id === selectedAsamblea)?.quorum_minimo || 0)
          })
          .eq('id', selectedAsamblea);

        // Refresh asambleas
        fetchAsambleas();
      }

      toast({
        title: "Éxito",
        description: `Asistencia ${presente ? 'registrada' : 'removida'} correctamente`
      });
    } catch (error) {
      console.error('Error updating asistencia:', error);
      toast({
        title: "Error",
        description: "No se pudo actualizar la asistencia",
        variant: "destructive"
      });
    }
  };

  const getEstadoBadgeVariant = (estado: string) => {
    switch (estado) {
      case 'convocada':
        return 'default';
      case 'celebrada':
        return 'secondary';
      case 'suspendida':
        return 'destructive';
      case 'cancelada':
        return 'outline';
      default:
        return 'default';
    }
  };

  const getTipoBadgeVariant = (tipo: string) => {
    return tipo === 'ordinaria' ? 'default' : 'secondary';
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  return (
    <div className="space-y-6">
      <Tabs defaultValue="asambleas" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="asambleas">Asambleas</TabsTrigger>
          <TabsTrigger value="delegados">Delegados</TabsTrigger>
          <TabsTrigger value="nueva">Nueva Asamblea</TabsTrigger>
        </TabsList>

        <TabsContent value="asambleas" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users2 className="h-5 w-5" />
                Asambleas Generales de Delegados
              </CardTitle>
              <CardDescription>
                Gestiona las asambleas ordinarias y extraordinarias del CLDC
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {asambleas.map((asamblea) => (
                  <Card key={asamblea.id} className="p-4">
                    <div className="space-y-3">
                      <div className="flex items-start justify-between">
                        <div className="space-y-1">
                          <div className="flex items-center gap-2">
                            <Badge variant={getTipoBadgeVariant(asamblea.tipo_asamblea) as any}>
                              {asamblea.tipo_asamblea}
                            </Badge>
                            <Badge variant={getEstadoBadgeVariant(asamblea.estado) as any}>
                              {asamblea.estado}
                            </Badge>
                          </div>
                          <h3 className="font-semibold text-sm">{asamblea.tema_principal}</h3>
                        </div>
                        {asamblea.quorum_alcanzado && (
                          <Check className="h-5 w-5 text-green-500" />
                        )}
                      </div>

                      <div className="space-y-2 text-xs text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Calendar className="h-3 w-3" />
                          <span>Convocada: {new Date(asamblea.fecha_convocatoria).toLocaleDateString()}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Calendar className="h-3 w-3" />
                          <span>Celebración: {new Date(asamblea.fecha_celebracion).toLocaleDateString()}</span>
                        </div>
                        
                        {asamblea.modalidad === 'presencial' || asamblea.modalidad === 'mixta' ? (
                          <div className="flex items-center gap-1">
                            <MapPin className="h-3 w-3" />
                            <span>{asamblea.lugar}</span>
                          </div>
                        ) : null}
                        
                        {asamblea.modalidad === 'virtual' || asamblea.modalidad === 'mixta' ? (
                          <div className="flex items-center gap-1">
                            <Video className="h-3 w-3" />
                            <span>Virtual</span>
                          </div>
                        ) : null}
                      </div>

                      <div className="space-y-2">
                        <div className="flex justify-between text-xs">
                          <span>Quórum: {asamblea.quorum_minimo}</span>
                          <span>Asistentes: {asamblea.asistentes_count}</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <div 
                            className={`h-2 rounded-full ${asamblea.quorum_alcanzado ? 'bg-green-500' : 'bg-blue-500'}`}
                            style={{ 
                              width: `${Math.min((asamblea.asistentes_count / asamblea.quorum_minimo) * 100, 100)}%` 
                            }}
                          />
                        </div>
                      </div>

                      <div className="space-y-1">
                        <p className="text-xs font-medium">Orden del día:</p>
                        <ul className="text-xs space-y-1">
                          {asamblea.orden_dia?.slice(0, 2).map((item, idx) => (
                            <li key={idx} className="text-muted-foreground">• {item}</li>
                          ))}
                          {asamblea.orden_dia?.length > 2 && (
                            <li className="text-xs text-muted-foreground">
                              ... y {asamblea.orden_dia.length - 2} puntos más
                            </li>
                          )}
                        </ul>
                      </div>

                      <div className="flex justify-end gap-1">
                        <Button
                          size="sm"
                          variant="outline"
                          onClick={() => setSelectedAsamblea(asamblea.id)}
                        >
                          Ver Delegados
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

        <TabsContent value="delegados" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Delegados de Asamblea</CardTitle>
              <CardDescription>
                Gestiona la asistencia y participación de los delegados
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="select-asamblea">Seleccionar Asamblea</Label>
                  <Select value={selectedAsamblea} onValueChange={setSelectedAsamblea}>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar asamblea" />
                    </SelectTrigger>
                    <SelectContent>
                      {asambleas.map((asamblea) => (
                        <SelectItem key={asamblea.id} value={asamblea.id}>
                          {asamblea.tema_principal} - {new Date(asamblea.fecha_celebracion).toLocaleDateString()}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                {selectedAsamblea && (
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      {delegados.map((delegado) => (
                        <Card key={delegado.id} className="p-4">
                          <div className="space-y-3">
                            <div className="flex items-start justify-between">
                              <div className="space-y-1">
                                <h3 className="font-semibold text-sm">
                                  {delegado.miembro?.nombre_completo}
                                </h3>
                                <Badge variant="outline" className="text-xs">
                                  {delegado.tipo_delegado.replace('_', ' ')}
                                </Badge>
                              </div>
                              <div className="flex items-center gap-2">
                                <input
                                  type="checkbox"
                                  checked={delegado.presente}
                                  onChange={(e) => updateAsistencia(delegado.id, e.target.checked)}
                                  className="rounded"
                                />
                                <span className="text-xs">Presente</span>
                              </div>
                            </div>
                            
                            <p className="text-xs text-muted-foreground">
                              {delegado.miembro?.email}
                            </p>

                            {delegado.observaciones && (
                              <p className="text-xs text-muted-foreground">
                                {delegado.observaciones}
                              </p>
                            )}
                          </div>
                        </Card>
                      ))}
                    </div>
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
                Convocar Nueva Asamblea General
              </CardTitle>
              <CardDescription>
                Convoca una asamblea ordinaria o extraordinaria
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo de Asamblea</Label>
                  <Select
                    value={nuevaAsamblea.tipo_asamblea}
                    onValueChange={(value) => setNuevaAsamblea({...nuevaAsamblea, tipo_asamblea: value as any})}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="ordinaria">Ordinaria</SelectItem>
                      <SelectItem value="extraordinaria">Extraordinaria</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="modalidad">Modalidad</Label>
                  <Select
                    value={nuevaAsamblea.modalidad}
                    onValueChange={(value) => setNuevaAsamblea({...nuevaAsamblea, modalidad: value as any})}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="presencial">Presencial</SelectItem>
                      <SelectItem value="virtual">Virtual</SelectItem>
                      <SelectItem value="mixta">Mixta</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="fecha-convocatoria">Fecha de Convocatoria</Label>
                  <Input
                    id="fecha-convocatoria"
                    type="datetime-local"
                    value={nuevaAsamblea.fecha_convocatoria}
                    onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, fecha_convocatoria: e.target.value})}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="fecha-celebracion">Fecha de Celebración</Label>
                  <Input
                    id="fecha-celebracion"
                    type="datetime-local"
                    value={nuevaAsamblea.fecha_celebracion}
                    onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, fecha_celebracion: e.target.value})}
                  />
                </div>

                {(nuevaAsamblea.modalidad === 'presencial' || nuevaAsamblea.modalidad === 'mixta') && (
                  <div className="space-y-2">
                    <Label htmlFor="lugar">Lugar</Label>
                    <Input
                      id="lugar"
                      value={nuevaAsamblea.lugar}
                      onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, lugar: e.target.value})}
                      placeholder="Ej: Auditorio CLDC, Santo Domingo"
                    />
                  </div>
                )}

                {(nuevaAsamblea.modalidad === 'virtual' || nuevaAsamblea.modalidad === 'mixta') && (
                  <div className="space-y-2">
                    <Label htmlFor="enlace-virtual">Enlace Virtual</Label>
                    <Input
                      id="enlace-virtual"
                      value={nuevaAsamblea.enlace_virtual}
                      onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, enlace_virtual: e.target.value})}
                      placeholder="https://zoom.us/j/..."
                    />
                  </div>
                )}

                <div className="space-y-2">
                  <Label htmlFor="quorum">Quórum Mínimo</Label>
                  <Input
                    id="quorum"
                    type="number"
                    min="1"
                    value={nuevaAsamblea.quorum_minimo}
                    onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, quorum_minimo: parseInt(e.target.value) || 50})}
                  />
                </div>

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="tema-principal">Tema Principal</Label>
                  <Input
                    id="tema-principal"
                    value={nuevaAsamblea.tema_principal}
                    onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, tema_principal: e.target.value})}
                    placeholder="Ej: Asamblea General Ordinaria 2025"
                  />
                </div>

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="orden-dia">Orden del Día (uno por línea)</Label>
                  <Textarea
                    id="orden-dia"
                    value={nuevaAsamblea.orden_dia}
                    onChange={(e) => setNuevaAsamblea({...nuevaAsamblea, orden_dia: e.target.value})}
                    placeholder={`1. Verificación del quórum\n2. Lectura y aprobación del acta anterior\n3. Informe de la presidencia\n4. Informe financiero\n5. Varios`}
                    rows={6}
                  />
                </div>
              </div>

              <Button 
                onClick={handleCreateAsamblea} 
                className="w-full"
                disabled={!nuevaAsamblea.tipo_asamblea || !nuevaAsamblea.fecha_convocatoria || !nuevaAsamblea.fecha_celebracion || !nuevaAsamblea.tema_principal}
              >
                <Plus className="h-4 w-4 mr-2" />
                Convocar Asamblea General
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default AsambleaManager;