import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { Calendar, Plus, Trash2 } from "lucide-react";

interface Padron {
  id: string;
  periodo: string;
  descripcion: string;
  total_electores: number;
  activo: boolean;
}

interface Candidate {
  id: string;
  nombre: string;
  propuesta?: string;
  foto_url?: string;
}

interface CreateElectionFormProps {
  organizationId: string;
  onElectionCreated: () => void;
}

export const CreateElectionForm = ({ organizationId, onElectionCreated }: CreateElectionFormProps) => {
  const [padrones, setPadrones] = useState<Padron[]>([]);
  const [candidates, setCandidates] = useState<Candidate[]>([{ id: '1', nombre: '' }]);
  const [loading, setLoading] = useState(false);

  const form = useForm({
    defaultValues: {
      cargo: "",
      fecha_inicio: "",
      fecha_fin: "",
      modalidad: "presencial",
      padron_id: "",
    }
  });

  useEffect(() => {
    fetchPadrones();
  }, [organizationId]);

  const fetchPadrones = async () => {
    try {
      const { data, error } = await supabase
        .from('padrones_electorales')
        .select('*')
        .eq('organizacion_id', organizationId)
        .eq('activo', true)
        .order('created_at', { ascending: false });

      if (error) throw error;
      setPadrones(data || []);
    } catch (error) {
      console.error('Error fetching padrones:', error);
      toast.error('Error al cargar los padrones electorales');
    }
  };

  const addCandidate = () => {
    setCandidates([...candidates, { id: Date.now().toString(), nombre: '' }]);
  };

  const removeCandidate = (id: string) => {
    if (candidates.length > 1) {
      setCandidates(candidates.filter(c => c.id !== id));
    }
  };

  const updateCandidate = (id: string, field: keyof Candidate, value: string) => {
    setCandidates(candidates.map(c => 
      c.id === id ? { ...c, [field]: value } : c
    ));
  };

  const onSubmit = async (values: any) => {
    setLoading(true);
    try {
      // Validar candidatos
      const validCandidates = candidates.filter(c => c.nombre.trim());
      if (validCandidates.length < 2) {
        toast.error('Se requieren al menos 2 candidatos');
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

      const { error } = await supabase
        .from('elecciones')
        .insert({
          cargo: values.cargo,
          fecha_inicio: values.fecha_inicio,
          fecha_fin: values.fecha_fin,
          modalidad: values.modalidad,
          padron_id: values.padron_id,
          candidatos: validCandidates as any,
          estado: 'programada'
        });

      if (error) throw error;

      toast.success('Elección creada exitosamente');
      form.reset();
      setCandidates([{ id: '1', nombre: '' }]);
      onElectionCreated();
    } catch (error) {
      console.error('Error creating election:', error);
      toast.error('Error al crear la elección');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Calendar className="w-5 h-5" />
          Nueva Elección
        </CardTitle>
      </CardHeader>
      <CardContent>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="cargo"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Cargo a elegir</FormLabel>
                    <FormControl>
                      <Input 
                        placeholder="Ej: Presidente, Secretario General..." 
                        {...field} 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="padron_id"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Padrón Electoral</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Seleccionar padrón" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {padrones.map((padron) => (
                          <SelectItem key={padron.id} value={padron.id}>
                            {padron.periodo} - {padron.total_electores} electores
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
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
                      <Input type="datetime-local" {...field} />
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
                      <Input type="datetime-local" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="modalidad"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Modalidad</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="presencial">Presencial</SelectItem>
                        <SelectItem value="virtual">Virtual</SelectItem>
                        <SelectItem value="hibrida">Híbrida</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium">Candidatos</h3>
                <Button 
                  type="button" 
                  variant="outline" 
                  size="sm"
                  onClick={addCandidate}
                >
                  <Plus className="w-4 h-4 mr-2" />
                  Agregar Candidato
                </Button>
              </div>

              {candidates.map((candidate, index) => (
                <div key={candidate.id} className="flex gap-2 items-start">
                  <div className="flex-1">
                    <Input
                      placeholder={`Nombre del candidato ${index + 1}`}
                      value={candidate.nombre}
                      onChange={(e) => updateCandidate(candidate.id, 'nombre', e.target.value)}
                    />
                  </div>
                  <div className="flex-1">
                    <Textarea
                      placeholder="Propuesta (opcional)"
                      value={candidate.propuesta || ''}
                      onChange={(e) => updateCandidate(candidate.id, 'propuesta', e.target.value)}
                      rows={1}
                    />
                  </div>
                  {candidates.length > 1 && (
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() => removeCandidate(candidate.id)}
                    >
                      <Trash2 className="w-4 h-4" />
                    </Button>
                  )}
                </div>
              ))}
            </div>

            <Button type="submit" disabled={loading} className="w-full">
              {loading ? 'Creando...' : 'Crear Elección'}
            </Button>
          </form>
        </Form>
      </CardContent>
    </Card>
  );
};