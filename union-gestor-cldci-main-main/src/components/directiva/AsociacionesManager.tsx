import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Plus, Edit, Trash2, Users, Phone, Mail, MapPin, Globe, Building2, Award } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface AsociacionMiembro {
  id: string;
  nombre: string;
  tipo: string;
  codigo: string;
  pais: string;
  provincia: string | null;
  ciudad: string | null;
  direccion: string | null;
  telefono: string | null;
  email: string | null;
  organizacion_padre_id: string | null;
  miembros_minimos: number;
  fecha_fundacion: string | null;
  estado_adecuacion: string;
  estatutos_url: string | null;
  actas_fundacion_url: string | null;
  created_at: string;
  updated_at: string;
}

interface Miembro {
  id: string;
  nombre_completo: string;
  email: string;
}

const AsociacionesManager = () => {
  const [asociaciones, setAsociaciones] = useState<AsociacionMiembro[]>([]);
  const [miembros, setMiembros] = useState<Miembro[]>([]);
  const [selectedTipo, setSelectedTipo] = useState<string>("all");
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  // Form state
  const [nuevaAsociacion, setNuevaAsociacion] = useState({
    nombre: "",
    tipo: "gremio" as const,
    codigo: "",
    pais: "República Dominicana",
    provincia: "",
    ciudad: "",
    direccion: "",
    telefono: "",
    email: "",
    presidente_id: "",
    fecha_fundacion: "",
    estatutos_url: "",
    actas_fundacion_url: ""
  });

  const [editingAsociacion, setEditingAsociacion] = useState<string | null>(null);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      await Promise.all([
        fetchAsociaciones(),
        fetchMiembros()
      ]);
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchAsociaciones = async () => {
    try {
      const { data, error } = await supabase
        .from('organizaciones')
        .select('*')
        .in('tipo', ['gremio', 'sindicato', 'asociacion', 'filial', 'otra_entidad'])
        .order('tipo')
        .order('nombre');

      if (error) throw error;
      setAsociaciones(data || []);
    } catch (error) {
      console.error('Error fetching asociaciones:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar las asociaciones",
        variant: "destructive"
      });
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

  const handleCreateAsociacion = async () => {
    try {
      const { data, error } = await supabase
        .from('organizaciones')
        .insert({
          nombre: nuevaAsociacion.nombre,
          tipo: nuevaAsociacion.tipo,
          codigo: nuevaAsociacion.codigo,
          pais: nuevaAsociacion.pais,
          provincia: nuevaAsociacion.provincia || null,
          ciudad: nuevaAsociacion.ciudad || null,
          direccion: nuevaAsociacion.direccion || null,
          telefono: nuevaAsociacion.telefono || null,
          email: nuevaAsociacion.email || null,
          fecha_fundacion: nuevaAsociacion.fecha_fundacion || null,
          estatutos_url: nuevaAsociacion.estatutos_url || null,
          actas_fundacion_url: nuevaAsociacion.actas_fundacion_url || null,
          estado_adecuacion: 'pendiente'
        });

      if (error) throw error;

      await fetchAsociaciones();
      setNuevaAsociacion({
        nombre: "",
        tipo: "gremio" as const,
        codigo: "",
        pais: "República Dominicana",
        provincia: "",
        ciudad: "",
        direccion: "",
        telefono: "",
        email: "",
        presidente_id: "",
        fecha_fundacion: "",
        estatutos_url: "",
        actas_fundacion_url: ""
      });

      toast({
        title: "Éxito",
        description: "Asociación creada correctamente"
      });
    } catch (error) {
      console.error('Error creating asociacion:', error);
      toast({
        title: "Error",
        description: "No se pudo crear la asociación",
        variant: "destructive"
      });
    }
  };

  const handleDeleteAsociacion = async (id: string) => {
    try {
      const { error } = await supabase
        .from('organizaciones')
        .delete()
        .eq('id', id);

      if (error) throw error;

      await fetchAsociaciones();
      toast({
        title: "Éxito",
        description: "Asociación eliminada correctamente"
      });
    } catch (error) {
      console.error('Error deleting asociacion:', error);
      toast({
        title: "Error",
        description: "No se pudo eliminar la asociación",
        variant: "destructive"
      });
    }
  };

  const getEstadoBadgeVariant = (estado: string) => {
    switch (estado) {
      case 'adecuada':
        return 'default';
      case 'pendiente':
        return 'secondary';
      case 'rechazada':
        return 'destructive';
      default:
        return 'outline';
    }
  };

  const getTipoBadgeVariant = (tipo: string) => {
    switch (tipo) {
      case 'asociacion':
        return 'default';
      case 'gremio':
        return 'secondary';
      case 'filial':
        return 'outline';
      case 'sindicato':
        return 'destructive';
      default:
        return 'outline';
    }
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  const filteredAsociaciones = selectedTipo && selectedTipo !== "all"
    ? asociaciones.filter(a => a.tipo === selectedTipo)
    : asociaciones;

  return (
    <div className="space-y-6">
      {/* Filtros */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Building2 className="h-5 w-5" />
            Asociaciones Afiliadas, Gremios y Otras Organizaciones
          </CardTitle>
          <CardDescription>
            Gestiona las organizaciones afiliadas y vinculadas al CLDC
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-2 mb-6">
            <Label htmlFor="filter-tipo">Filtrar por Tipo de Organización</Label>
            <Select value={selectedTipo} onValueChange={setSelectedTipo}>
              <SelectTrigger>
                <SelectValue placeholder="Todos los tipos" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos los tipos</SelectItem>
                <SelectItem value="asociacion">Asociaciones Afiliadas</SelectItem>
                <SelectItem value="gremio">Gremios</SelectItem>
                <SelectItem value="filial">Filiales</SelectItem>
                <SelectItem value="sindicato">Sindicatos</SelectItem>
                <SelectItem value="otra_entidad">Otras Entidades</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {/* Lista de Asociaciones */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {filteredAsociaciones.map((asociacion) => (
              <Card key={asociacion.id} className="p-4">
                <div className="space-y-3">
                  <div className="flex items-start justify-between">
                    <div className="space-y-1 flex-1">
                      <div className="flex items-center gap-2">
                        <h3 className="font-semibold text-sm">{asociacion.nombre}</h3>
                        <Badge variant={getTipoBadgeVariant(asociacion.tipo) as any}>
                          {asociacion.tipo}
                        </Badge>
                      </div>
                      <p className="text-xs text-muted-foreground">
                        Código: {asociacion.codigo}
                      </p>
                    </div>

                    <Badge variant={getEstadoBadgeVariant(asociacion.estado_adecuacion) as any}>
                      {asociacion.estado_adecuacion}
                    </Badge>
                  </div>

                  <div className="space-y-1">
                    <div className="flex items-center gap-1 text-xs">
                      <MapPin className="h-3 w-3" />
                      <span>{asociacion.ciudad}, {asociacion.provincia}</span>
                    </div>
                    <div className="flex items-center gap-1 text-xs">
                      <Globe className="h-3 w-3" />
                      <span>{asociacion.pais}</span>
                    </div>
                  </div>

                  <div className="flex items-center gap-4 text-xs text-muted-foreground">
                    {asociacion.email && (
                      <div className="flex items-center gap-1">
                        <Mail className="h-3 w-3" />
                        <span className="truncate">{asociacion.email}</span>
                      </div>
                    )}
                    {asociacion.telefono && (
                      <div className="flex items-center gap-1">
                        <Phone className="h-3 w-3" />
                        <span>{asociacion.telefono}</span>
                      </div>
                    )}
                  </div>

                  {asociacion.fecha_fundacion && (
                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Award className="h-3 w-3" />
                      <span>Fundada: {new Date(asociacion.fecha_fundacion).toLocaleDateString()}</span>
                    </div>
                  )}

                  <div className="flex justify-end gap-1">
                    <Button
                      size="sm"
                      variant="ghost"
                      className="h-6 w-6 p-0"
                      onClick={() => setEditingAsociacion(asociacion.id)}
                    >
                      <Edit className="h-3 w-3" />
                    </Button>
                    <Button
                      size="sm"
                      variant="ghost"
                      className="h-6 w-6 p-0 text-destructive hover:text-destructive"
                      onClick={() => handleDeleteAsociacion(asociacion.id)}
                    >
                      <Trash2 className="h-3 w-3" />
                    </Button>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Agregar Nueva Asociación */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Plus className="h-5 w-5" />
            Agregar Nueva Organización Afiliada
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="nombre">Nombre de la Organización</Label>
              <Input
                id="nombre"
                value={nuevaAsociacion.nombre}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, nombre: e.target.value})}
                placeholder="Asociación Provincial de..."
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="tipo">Tipo de Organización</Label>
              <Select
                value={nuevaAsociacion.tipo}
                onValueChange={(value) => setNuevaAsociacion({...nuevaAsociacion, tipo: value as any})}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar tipo" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="asociacion">Asociación Afiliada</SelectItem>
                  <SelectItem value="gremio">Gremio</SelectItem>
                  <SelectItem value="filial">Filial</SelectItem>
                  <SelectItem value="sindicato">Sindicato</SelectItem>
                  <SelectItem value="otra_entidad">Otra Entidad</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="codigo">Código</Label>
              <Input
                id="codigo"
                value={nuevaAsociacion.codigo}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, codigo: e.target.value})}
                placeholder="APCLD-001"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="pais">País</Label>
              <Input
                id="pais"
                value={nuevaAsociacion.pais}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, pais: e.target.value})}
                placeholder="República Dominicana"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="provincia">Provincia</Label>
              <Input
                id="provincia"
                value={nuevaAsociacion.provincia}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, provincia: e.target.value})}
                placeholder="Santo Domingo"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="ciudad">Ciudad</Label>
              <Input
                id="ciudad"
                value={nuevaAsociacion.ciudad}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, ciudad: e.target.value})}
                placeholder="Santo Domingo"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                value={nuevaAsociacion.email}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, email: e.target.value})}
                placeholder="info@asociacion.org.do"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="telefono">Teléfono</Label>
              <Input
                id="telefono"
                value={nuevaAsociacion.telefono}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, telefono: e.target.value})}
                placeholder="+1 809-555-0123"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="fecha-fundacion">Fecha de Fundación</Label>
              <Input
                id="fecha-fundacion"
                type="date"
                value={nuevaAsociacion.fecha_fundacion}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, fecha_fundacion: e.target.value})}
              />
            </div>

            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="direccion">Dirección</Label>
              <Textarea
                id="direccion"
                value={nuevaAsociacion.direccion}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, direccion: e.target.value})}
                placeholder="Dirección completa de la organización"
                rows={2}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="estatutos-url">URL Estatutos</Label>
              <Input
                id="estatutos-url"
                value={nuevaAsociacion.estatutos_url}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, estatutos_url: e.target.value})}
                placeholder="https://..."
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="actas-url">URL Actas de Fundación</Label>
              <Input
                id="actas-url"
                value={nuevaAsociacion.actas_fundacion_url}
                onChange={(e) => setNuevaAsociacion({...nuevaAsociacion, actas_fundacion_url: e.target.value})}
                placeholder="https://..."
              />
            </div>
          </div>

          <Button 
            onClick={handleCreateAsociacion} 
            className="w-full"
            disabled={!nuevaAsociacion.nombre || !nuevaAsociacion.codigo}
          >
            <Plus className="h-4 w-4 mr-2" />
            Agregar Organización
          </Button>
        </CardContent>
      </Card>
    </div>
  );
};

export default AsociacionesManager;