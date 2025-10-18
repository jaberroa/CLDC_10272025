import { useState, useEffect } from "react";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { Loader2 } from "lucide-react";

interface CreateCursoDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSuccess: () => void;
}

export const CreateCursoDialog = ({ open, onOpenChange, onSuccess }: CreateCursoDialogProps) => {
  const [loading, setLoading] = useState(false);
  const [organizaciones, setOrganizaciones] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    titulo: "",
    descripcion: "",
    categoria: "general",
    modalidad: "presencial",
    nivel: "basico",
    duracion_horas: 40,
    precio: 0,
    instructor: "",
    organizacion_id: "",
    fecha_inicio: "",
    fecha_fin: "",
    capacidad_maxima: 30,
    lugar: "",
    enlace_virtual: "",
    requisitos: "",
    objetivos: ""
  });

  useEffect(() => {
    if (open) {
      fetchOrganizaciones();
    }
  }, [open]);

  const fetchOrganizaciones = async () => {
    try {
      const { data, error } = await supabase
        .from('organizaciones')
        .select('id, nombre')
        .order('nombre');

      if (error) throw error;
      setOrganizaciones(data || []);
    } catch (error) {
      console.error('Error fetching organizaciones:', error);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error('No user found');

      const requisitosArray = formData.requisitos 
        ? formData.requisitos.split('\n').filter(r => r.trim())
        : [];
      
      const objetivosArray = formData.objetivos 
        ? formData.objetivos.split('\n').filter(o => o.trim())
        : [];

      const { error } = await supabase.from('cursos').insert({
        ...formData,
        requisitos: requisitosArray,
        objetivos: objetivosArray,
        created_by: user.id,
        estado: 'programado'
      });

      if (error) throw error;

      toast({
        title: "¡Éxito!",
        description: "Curso creado correctamente",
      });

      onSuccess();
      
      // Reset form
      setFormData({
        titulo: "",
        descripcion: "",
        categoria: "general",
        modalidad: "presencial",
        nivel: "basico",
        duracion_horas: 40,
        precio: 0,
        instructor: "",
        organizacion_id: "",
        fecha_inicio: "",
        fecha_fin: "",
        capacidad_maxima: 30,
        lugar: "",
        enlace_virtual: "",
        requisitos: "",
        objetivos: ""
      });
    } catch (error) {
      console.error('Error creating curso:', error);
      toast({
        title: "Error",
        description: "Error al crear el curso",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto bg-slate-900 text-white border-slate-700">
        <DialogHeader>
          <DialogTitle>Crear Nuevo Curso</DialogTitle>
          <DialogDescription className="text-slate-400">
            Completa la información para crear un nuevo curso de formación profesional
          </DialogDescription>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="titulo">Título del Curso *</Label>
              <Input
                id="titulo"
                value={formData.titulo}
                onChange={(e) => setFormData(prev => ({ ...prev, titulo: e.target.value }))}
                required
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>

            <div>
              <Label htmlFor="instructor">Instructor *</Label>
              <Input
                id="instructor"
                value={formData.instructor}
                onChange={(e) => setFormData(prev => ({ ...prev, instructor: e.target.value }))}
                required
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>
          </div>

          <div>
            <Label htmlFor="descripcion">Descripción</Label>
            <Textarea
              id="descripcion"
              value={formData.descripcion}
              onChange={(e) => setFormData(prev => ({ ...prev, descripcion: e.target.value }))}
              className="bg-slate-800 border-slate-600 text-white"
              rows={3}
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <Label htmlFor="categoria">Categoría</Label>
              <Select value={formData.categoria} onValueChange={(value) => setFormData(prev => ({ ...prev, categoria: value }))}>
                <SelectTrigger className="bg-slate-800 border-slate-600 text-white">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="bg-slate-800 border-slate-600">
                  <SelectItem value="general">General</SelectItem>
                  <SelectItem value="tecnico">Técnico</SelectItem>
                  <SelectItem value="comunicacion">Comunicación</SelectItem>
                  <SelectItem value="tecnologia">Tecnología</SelectItem>
                  <SelectItem value="liderazgo">Liderazgo</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <Label htmlFor="nivel">Nivel</Label>
              <Select value={formData.nivel} onValueChange={(value) => setFormData(prev => ({ ...prev, nivel: value }))}>
                <SelectTrigger className="bg-slate-800 border-slate-600 text-white">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="bg-slate-800 border-slate-600">
                  <SelectItem value="basico">Básico</SelectItem>
                  <SelectItem value="intermedio">Intermedio</SelectItem>
                  <SelectItem value="avanzado">Avanzado</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <Label htmlFor="modalidad">Modalidad</Label>
              <Select value={formData.modalidad} onValueChange={(value) => setFormData(prev => ({ ...prev, modalidad: value }))}>
                <SelectTrigger className="bg-slate-800 border-slate-600 text-white">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent className="bg-slate-800 border-slate-600">
                  <SelectItem value="presencial">Presencial</SelectItem>
                  <SelectItem value="virtual">Virtual</SelectItem>
                  <SelectItem value="hibrida">Híbrida</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <Label htmlFor="duracion_horas">Duración (horas)</Label>
              <Input
                id="duracion_horas"
                type="number"
                value={formData.duracion_horas}
                onChange={(e) => setFormData(prev => ({ ...prev, duracion_horas: parseInt(e.target.value) || 40 }))}
                min="1"
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>

            <div>
              <Label htmlFor="precio">Precio (RD$)</Label>
              <Input
                id="precio"
                type="number"
                value={formData.precio}
                onChange={(e) => setFormData(prev => ({ ...prev, precio: parseFloat(e.target.value) || 0 }))}
                min="0"
                step="0.01"
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>

            <div>
              <Label htmlFor="capacidad_maxima">Capacidad Máxima</Label>
              <Input
                id="capacidad_maxima"
                type="number"
                value={formData.capacidad_maxima}
                onChange={(e) => setFormData(prev => ({ ...prev, capacidad_maxima: parseInt(e.target.value) || 30 }))}
                min="1"
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="fecha_inicio">Fecha de Inicio *</Label>
              <Input
                id="fecha_inicio"
                type="datetime-local"
                value={formData.fecha_inicio}
                onChange={(e) => setFormData(prev => ({ ...prev, fecha_inicio: e.target.value }))}
                required
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>

            <div>
              <Label htmlFor="fecha_fin">Fecha de Fin *</Label>
              <Input
                id="fecha_fin"
                type="datetime-local"
                value={formData.fecha_fin}
                onChange={(e) => setFormData(prev => ({ ...prev, fecha_fin: e.target.value }))}
                required
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>
          </div>

          <div>
            <Label htmlFor="organizacion_id">Organización</Label>
            <Select value={formData.organizacion_id} onValueChange={(value) => setFormData(prev => ({ ...prev, organizacion_id: value }))}>
              <SelectTrigger className="bg-slate-800 border-slate-600 text-white">
                <SelectValue placeholder="Seleccionar organización" />
              </SelectTrigger>
              <SelectContent className="bg-slate-800 border-slate-600">
                {organizaciones.map((org) => (
                  <SelectItem key={org.id} value={org.id}>
                    {org.nombre}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {formData.modalidad === 'presencial' && (
            <div>
              <Label htmlFor="lugar">Lugar</Label>
              <Input
                id="lugar"
                value={formData.lugar}
                onChange={(e) => setFormData(prev => ({ ...prev, lugar: e.target.value }))}
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>
          )}

          {(formData.modalidad === 'virtual' || formData.modalidad === 'hibrida') && (
            <div>
              <Label htmlFor="enlace_virtual">Enlace Virtual</Label>
              <Input
                id="enlace_virtual"
                type="url"
                value={formData.enlace_virtual}
                onChange={(e) => setFormData(prev => ({ ...prev, enlace_virtual: e.target.value }))}
                className="bg-slate-800 border-slate-600 text-white"
              />
            </div>
          )}

          <div>
            <Label htmlFor="requisitos">Requisitos (uno por línea)</Label>
            <Textarea
              id="requisitos"
              value={formData.requisitos}
              onChange={(e) => setFormData(prev => ({ ...prev, requisitos: e.target.value }))}
              className="bg-slate-800 border-slate-600 text-white"
              rows={3}
              placeholder="Requisito 1&#10;Requisito 2&#10;Requisito 3"
            />
          </div>

          <div>
            <Label htmlFor="objetivos">Objetivos (uno por línea)</Label>
            <Textarea
              id="objetivos"
              value={formData.objetivos}
              onChange={(e) => setFormData(prev => ({ ...prev, objetivos: e.target.value }))}
              className="bg-slate-800 border-slate-600 text-white"
              rows={3}
              placeholder="Objetivo 1&#10;Objetivo 2&#10;Objetivo 3"
            />
          </div>

          <div className="flex justify-end space-x-2 pt-4">
            <Button
              type="button"
              variant="outline"
              onClick={() => onOpenChange(false)}
              className="border-slate-600 text-slate-300 hover:bg-slate-800"
            >
              Cancelar
            </Button>
            <Button
              type="submit"
              disabled={loading}
              className="bg-primary hover:bg-primary/80"
            >
              {loading && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
              Crear Curso
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
};