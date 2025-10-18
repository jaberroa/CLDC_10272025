import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { toast } from "sonner";
import { Users, FileUp, Check, X } from "lucide-react";

interface Member {
  id: string;
  nombre_completo: string;
  numero_carnet: string;
  estado_membresia: string;
  fecha_ingreso: string;
}

interface ElectoralRegistryFormProps {
  organizationId: string;
  onRegistryCreated: () => void;
}

export const ElectoralRegistryForm = ({ organizationId, onRegistryCreated }: ElectoralRegistryFormProps) => {
  const [members, setMembers] = useState<Member[]>([]);
  const [selectedMembers, setSelectedMembers] = useState<Set<string>>(new Set());
  const [loading, setLoading] = useState(false);
  const [loadingMembers, setLoadingMembers] = useState(true);

  const form = useForm({
    defaultValues: {
      periodo: "",
      descripcion: "",
      fecha_inicio: "",
      fecha_fin: "",
    }
  });

  useEffect(() => {
    fetchMembers();
  }, [organizationId]);

  const fetchMembers = async () => {
    try {
      const { data, error } = await supabase
        .from('miembros')
        .select('id, nombre_completo, numero_carnet, estado_membresia, fecha_ingreso')
        .eq('organizacion_id', organizationId)
        .eq('estado_membresia', 'activa')
        .order('nombre_completo');

      if (error) throw error;
      setMembers(data || []);
      // Por defecto, seleccionar todos los miembros activos
      setSelectedMembers(new Set(data?.map(m => m.id) || []));
    } catch (error) {
      console.error('Error fetching members:', error);
      toast.error('Error al cargar los miembros');
    } finally {
      setLoadingMembers(false);
    }
  };

  const toggleMember = (memberId: string) => {
    const newSelected = new Set(selectedMembers);
    if (newSelected.has(memberId)) {
      newSelected.delete(memberId);
    } else {
      newSelected.add(memberId);
    }
    setSelectedMembers(newSelected);
  };

  const onSubmit = async (values: any) => {
    setLoading(true);
    try {
      if (selectedMembers.size === 0) {
        toast.error('Debe seleccionar al menos un miembro para el padrón');
        setLoading(false);
        return;
      }

      // Validar fechas
      const fechaInicio = new Date(values.fecha_inicio);
      const fechaFin = new Date(values.fecha_fin);
      if (fechaFin <= fechaInicio) {
        toast.error('La fecha de fin debe ser posterior a la fecha de inicio');
        setLoading(false);
        return;
      }

      // Crear el padrón electoral
      const { data: padronData, error: padronError } = await supabase
        .from('padrones_electorales')
        .insert({
          periodo: values.periodo,
          descripcion: values.descripcion,
          fecha_inicio: values.fecha_inicio,
          fecha_fin: values.fecha_fin,
          organizacion_id: organizationId,
          total_electores: selectedMembers.size,
          activo: true
        })
        .select()
        .single();

      if (padronError) throw padronError;

      // Crear los electores
      const electores = Array.from(selectedMembers).map(memberId => ({
        padron_id: padronData.id,
        miembro_id: memberId,
        elegible: true
      }));

      const { error: electoresError } = await supabase
        .from('electores')
        .insert(electores);

      if (electoresError) throw electoresError;

      toast.success('Padrón electoral creado exitosamente');
      form.reset();
      setSelectedMembers(new Set());
      onRegistryCreated();
    } catch (error) {
      console.error('Error creating electoral registry:', error);
      toast.error('Error al crear el padrón electoral');
    } finally {
      setLoading(false);
    }
  };

  if (loadingMembers) {
    return <div className="flex justify-center p-8">Cargando miembros...</div>;
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Users className="w-5 h-5" />
          Crear Padrón Electoral
        </CardTitle>
      </CardHeader>
      <CardContent>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="periodo"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Período</FormLabel>
                    <FormControl>
                      <Input 
                        placeholder="Ej: 2025-2028" 
                        {...field} 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="descripcion"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Descripción</FormLabel>
                    <FormControl>
                      <Textarea 
                        placeholder="Descripción del padrón electoral"
                        rows={2}
                        {...field} 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="fecha_inicio"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Fecha de Inicio</FormLabel>
                    <FormControl>
                      <Input type="date" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="fecha_fin"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Fecha de Fin</FormLabel>
                    <FormControl>
                      <Input type="date" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium">Seleccionar Electores</h3>
                <Badge variant="outline">
                  {selectedMembers.size} de {members.length} seleccionados
                </Badge>
              </div>

              <div className="max-h-60 overflow-y-auto border rounded-lg p-4 space-y-2">
                {members.map((member) => (
                  <div 
                    key={member.id} 
                    className={`flex items-center justify-between p-2 rounded cursor-pointer transition-colors ${
                      selectedMembers.has(member.id) 
                        ? 'bg-primary/10 border border-primary/20' 
                        : 'hover:bg-muted'
                    }`}
                    onClick={() => toggleMember(member.id)}
                  >
                    <div>
                      <p className="font-medium">{member.nombre_completo}</p>
                      <p className="text-sm text-muted-foreground">
                        Carnet: {member.numero_carnet}
                      </p>
                    </div>
                    <div className="flex items-center gap-2">
                      <Badge variant="secondary">
                        {member.estado_membresia}
                      </Badge>
                      {selectedMembers.has(member.id) ? (
                        <Check className="w-5 h-5 text-primary" />
                      ) : (
                        <X className="w-5 h-5 text-muted-foreground" />
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <Button type="submit" disabled={loading} className="w-full">
              {loading ? 'Creando...' : 'Crear Padrón Electoral'}
            </Button>
          </form>
        </Form>
      </CardContent>
    </Card>
  );
};