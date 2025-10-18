import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Plus, Edit, Trash2, Users, Building, Gavel, Settings, MapPin } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface Organo {
  id: string;
  nombre: string;
  tipo_organo: string;
  descripcion: string;
  funciones: string[];
  nivel_jerarquico: number;
  activo: boolean;
}

interface Cargo {
  id: string;
  nombre_cargo: string;
  descripcion: string;
  nivel_autoridad: number;
  activo: boolean;
  organo_id: string;
}

const OrganosManager = () => {
  const [organos, setOrganos] = useState<Organo[]>([]);
  const [cargos, setCargos] = useState<Cargo[]>([]);
  const [selectedOrgano, setSelectedOrgano] = useState<string>("all");
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  // Form states
  const [nuevoOrgano, setNuevoOrgano] = useState({
    nombre: "",
    tipo_organo: "direccion" as const,
    descripcion: "",
    funciones: "",
    nivel_jerarquico: 1
  });

  const [nuevoCargo, setNuevoCargo] = useState({
    nombre_cargo: "",
    descripcion: "",
    nivel_autoridad: 1,
    organo_id: ""
  });

  useEffect(() => {
    fetchOrganos();
    fetchCargos();
  }, []);

  const fetchOrganos = async () => {
    try {
      const { data, error } = await supabase
        .from('organos_cldc')
        .select('*')
        .order('tipo_organo', { ascending: true })
        .order('nivel_jerarquico', { ascending: true });

      if (error) throw error;
      setOrganos(data || []);
    } catch (error) {
      console.error('Error fetching órganos:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar los órganos",
        variant: "destructive"
      });
    }
  };

  const fetchCargos = async () => {
    try {
      const { data, error } = await supabase
        .from('cargos_organos')
        .select('*')
        .order('nivel_autoridad', { ascending: true });

      if (error) throw error;
      setCargos(data || []);
    } catch (error) {
      console.error('Error fetching cargos:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar los cargos",
        variant: "destructive"
      });
    } finally {
      setLoading(false);
    }
  };

  const handleCreateOrgano = async () => {
    try {
      const funcionesArray = nuevoOrgano.funciones.split('\n').filter(f => f.trim());
      
      const { data, error } = await supabase
        .from('organos_cldc')
        .insert([{
          nombre: nuevoOrgano.nombre,
          tipo_organo: nuevoOrgano.tipo_organo,
          descripcion: nuevoOrgano.descripcion,
          funciones: funcionesArray,
          nivel_jerarquico: nuevoOrgano.nivel_jerarquico,
          activo: true
        }])
        .select()
        .single();

      if (error) throw error;

      setOrganos([...organos, data]);
      setNuevoOrgano({
        nombre: "",
        tipo_organo: "direccion",
        descripcion: "",
        funciones: "",
        nivel_jerarquico: 1
      });

      toast({
        title: "Éxito",
        description: "Órgano creado correctamente"
      });
    } catch (error) {
      console.error('Error creating órgano:', error);
      toast({
        title: "Error",
        description: "No se pudo crear el órgano",
        variant: "destructive"
      });
    }
  };

  const handleCreateCargo = async () => {
    try {
      const { data, error } = await supabase
        .from('cargos_organos')
        .insert([{
          nombre_cargo: nuevoCargo.nombre_cargo,
          descripcion: nuevoCargo.descripcion,
          nivel_autoridad: nuevoCargo.nivel_autoridad,
          organo_id: nuevoCargo.organo_id,
          activo: true
        }])
        .select()
        .single();

      if (error) throw error;

      setCargos([...cargos, data]);
      setNuevoCargo({
        nombre_cargo: "",
        descripcion: "",
        nivel_autoridad: 1,
        organo_id: ""
      });

      toast({
        title: "Éxito",
        description: "Cargo creado correctamente"
      });
    } catch (error) {
      console.error('Error creating cargo:', error);
      toast({
        title: "Error",
        description: "No se pudo crear el cargo",
        variant: "destructive"
      });
    }
  };

  const getIconForTipo = (tipo: string) => {
    switch (tipo) {
      case 'direccion':
        return <Building className="h-4 w-4" />;
      case 'consultivo':
        return <Gavel className="h-4 w-4" />;
      case 'operativo':
        return <Settings className="h-4 w-4" />;
      case 'territorial':
        return <MapPin className="h-4 w-4" />;
      default:
        return <Users className="h-4 w-4" />;
    }
  };

  const getBadgeVariant = (tipo: string) => {
    switch (tipo) {
      case 'direccion':
        return 'default';
      case 'consultivo':
        return 'secondary';
      case 'operativo':
        return 'outline';
      case 'territorial':
        return 'destructive';
      default:
        return 'default';
    }
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  return (
    <div className="space-y-6">
      <Tabs defaultValue="organos" className="w-full">
        <TabsList className="grid w-full grid-cols-2">
          <TabsTrigger value="organos">Órganos CLDC</TabsTrigger>
          <TabsTrigger value="cargos">Cargos</TabsTrigger>
        </TabsList>

        <TabsContent value="organos" className="space-y-6">
          {/* Lista de Órganos */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users className="h-5 w-5" />
                Órganos de la Estructura CLDC
              </CardTitle>
              <CardDescription>
                Gestiona los órganos de dirección, consultivos, operativos y territoriales
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {organos.map((organo) => (
                  <Card key={organo.id} className="p-4">
                    <div className="space-y-3">
                      <div className="flex items-start justify-between">
                        <div className="flex items-center gap-2">
                          {getIconForTipo(organo.tipo_organo)}
                          <h3 className="font-semibold text-sm">{organo.nombre}</h3>
                        </div>
                        <Badge variant={getBadgeVariant(organo.tipo_organo) as any}>
                          {organo.tipo_organo}
                        </Badge>
                      </div>
                      
                      <p className="text-xs text-muted-foreground">{organo.descripcion}</p>
                      
                      <div className="space-y-1">
                        <p className="text-xs font-medium">Funciones:</p>
                        <ul className="text-xs space-y-1">
                          {organo.funciones?.slice(0, 2).map((funcion, idx) => (
                            <li key={idx} className="text-muted-foreground">• {funcion}</li>
                          ))}
                          {organo.funciones?.length > 2 && (
                            <li className="text-xs text-muted-foreground">
                              ... y {organo.funciones.length - 2} más
                            </li>
                          )}
                        </ul>
                      </div>

                      <div className="flex justify-between items-center text-xs text-muted-foreground">
                        <span>Nivel: {organo.nivel_jerarquico}</span>
                        <div className="flex gap-1">
                          <Button size="sm" variant="ghost" className="h-6 w-6 p-0">
                            <Edit className="h-3 w-3" />
                          </Button>
                        </div>
                      </div>
                    </div>
                  </Card>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Crear Nuevo Órgano */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Plus className="h-5 w-5" />
                Crear Nuevo Órgano
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="nombre">Nombre del Órgano</Label>
                  <Input
                    id="nombre"
                    value={nuevoOrgano.nombre}
                    onChange={(e) => setNuevoOrgano({...nuevoOrgano, nombre: e.target.value})}
                    placeholder="Ej: Dirección de Comunicación"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo de Órgano</Label>
                  <Select
                    value={nuevoOrgano.tipo_organo}
                    onValueChange={(value) => setNuevoOrgano({...nuevoOrgano, tipo_organo: value as any})}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="direccion">Dirección</SelectItem>
                      <SelectItem value="consultivo">Consultivo</SelectItem>
                      <SelectItem value="operativo">Operativo</SelectItem>
                      <SelectItem value="territorial">Territorial</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="descripcion">Descripción</Label>
                  <Textarea
                    id="descripcion"
                    value={nuevoOrgano.descripcion}
                    onChange={(e) => setNuevoOrgano({...nuevoOrgano, descripcion: e.target.value})}
                    placeholder="Descripción del órgano y su propósito"
                  />
                </div>

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="funciones">Funciones (una por línea)</Label>
                  <Textarea
                    id="funciones"
                    value={nuevoOrgano.funciones}
                    onChange={(e) => setNuevoOrgano({...nuevoOrgano, funciones: e.target.value})}
                    placeholder={`Función 1\nFunción 2\nFunción 3`}
                    rows={4}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="nivel">Nivel Jerárquico</Label>
                  <Input
                    id="nivel"
                    type="number"
                    min="1"
                    value={nuevoOrgano.nivel_jerarquico}
                    onChange={(e) => setNuevoOrgano({...nuevoOrgano, nivel_jerarquico: parseInt(e.target.value) || 1})}
                  />
                </div>
              </div>

              <Button onClick={handleCreateOrgano} className="w-full">
                <Plus className="h-4 w-4 mr-2" />
                Crear Órgano
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="cargos" className="space-y-6">
          {/* Lista de Cargos */}
          <Card>
            <CardHeader>
              <CardTitle>Cargos por Órgano</CardTitle>
              <CardDescription>
                Visualiza y gestiona los cargos de cada órgano
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-2 mb-4">
                <Label htmlFor="filter-organo">Filtrar por Órgano</Label>
                <Select value={selectedOrgano} onValueChange={setSelectedOrgano}>
                  <SelectTrigger>
                    <SelectValue placeholder="Seleccionar órgano" />
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

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {cargos
                  .filter(cargo => selectedOrgano === "all" || cargo.organo_id === selectedOrgano)
                  .map((cargo) => {
                    const organo = organos.find(o => o.id === cargo.organo_id);
                    return (
                      <Card key={cargo.id} className="p-4">
                        <div className="space-y-2">
                          <div className="flex items-start justify-between">
                            <h3 className="font-semibold text-sm">{cargo.nombre_cargo}</h3>
                            <Badge variant="outline" className="text-xs">
                              Nivel {cargo.nivel_autoridad}
                            </Badge>
                          </div>
                          
                          <p className="text-xs text-muted-foreground">
                            {organo?.nombre}
                          </p>
                          
                          <p className="text-xs text-muted-foreground">
                            {cargo.descripcion}
                          </p>
                        </div>
                      </Card>
                    );
                  })
                }
              </div>
            </CardContent>
          </Card>

          {/* Crear Nuevo Cargo */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Plus className="h-5 w-5" />
                Crear Nuevo Cargo
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="cargo-nombre">Nombre del Cargo</Label>
                  <Input
                    id="cargo-nombre"
                    value={nuevoCargo.nombre_cargo}
                    onChange={(e) => setNuevoCargo({...nuevoCargo, nombre_cargo: e.target.value})}
                    placeholder="Ej: Director"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="cargo-organo">Órgano</Label>
                  <Select
                    value={nuevoCargo.organo_id}
                    onValueChange={(value) => setNuevoCargo({...nuevoCargo, organo_id: value})}
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

                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="cargo-descripcion">Descripción</Label>
                  <Textarea
                    id="cargo-descripcion"
                    value={nuevoCargo.descripcion}
                    onChange={(e) => setNuevoCargo({...nuevoCargo, descripcion: e.target.value})}
                    placeholder="Descripción del cargo y sus responsabilidades"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="cargo-nivel">Nivel de Autoridad</Label>
                  <Input
                    id="cargo-nivel"
                    type="number"
                    min="1"
                    value={nuevoCargo.nivel_autoridad}
                    onChange={(e) => setNuevoCargo({...nuevoCargo, nivel_autoridad: parseInt(e.target.value) || 1})}
                  />
                </div>
              </div>

              <Button 
                onClick={handleCreateCargo} 
                className="w-full"
                disabled={!nuevoCargo.nombre_cargo || !nuevoCargo.organo_id}
              >
                <Plus className="h-4 w-4 mr-2" />
                Crear Cargo
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default OrganosManager;