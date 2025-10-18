import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { toast } from "@/hooks/use-toast";
import { Plus, Search, BookOpen, Clock, Users, MapPin, Calendar, DollarSign } from "lucide-react";
import { CreateCursoDialog } from "./CreateCursoDialog";
import { format } from "date-fns";
import { es } from "date-fns/locale";

interface Curso {
  id: string;
  titulo: string;
  descripcion: string;
  categoria: string;
  modalidad: string;
  nivel: string;
  duracion_horas: number;
  precio: number;
  instructor: string;
  fecha_inicio: string;
  fecha_fin: string;
  capacidad_maxima: number;
  inscritos_count: number;
  lugar: string;
  enlace_virtual: string;
  estado: string;
  organizacion_id: string;
  created_at: string;
}

interface CursosManagerProps {
  onStatsChange?: () => void;
}

export const CursosManager = ({ onStatsChange }: CursosManagerProps) => {
  const [cursos, setCursos] = useState<Curso[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [showCreateDialog, setShowCreateDialog] = useState(false);
  const [userRole, setUserRole] = useState<string>("");

  useEffect(() => {
    fetchCursos();
    checkUserRole();
  }, []);

  const checkUserRole = async () => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (user) {
        const { data: roles } = await supabase
          .from('user_roles')
          .select('role')
          .eq('user_id', user.id);
        
        if (roles && roles.length > 0) {
          setUserRole(roles[0].role);
        }
      }
    } catch (error) {
      console.error('Error checking user role:', error);
    }
  };

  const fetchCursos = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('cursos')
        .select('*')
        .order('fecha_inicio', { ascending: false });

      if (error) throw error;
      setCursos(data || []);
      onStatsChange?.();
    } catch (error) {
      console.error('Error fetching cursos:', error);
      toast({
        title: "Error",
        description: "Error al cargar los cursos",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleInscribirse = async (cursoId: string) => {
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) {
        toast({
          title: "Error",
          description: "Debes estar autenticado para inscribirte",
          variant: "destructive",
        });
        return;
      }

      // Obtener el miembro asociado al usuario
      const { data: miembros } = await supabase
        .from('miembros')
        .select('id')
        .eq('user_id', user.id)
        .limit(1);

      if (!miembros || miembros.length === 0) {
        toast({
          title: "Error",
          description: "No se encontró tu perfil de miembro",
          variant: "destructive",
        });
        return;
      }

      const { error } = await supabase
        .from('inscripciones_cursos')
        .insert({
          curso_id: cursoId,
          miembro_id: miembros[0].id,
          estado_inscripcion: 'activa'
        });

      if (error) {
        if (error.code === '23505') {
          toast({
            title: "Información",
            description: "Ya estás inscrito en este curso",
            variant: "default",
          });
        } else {
          throw error;
        }
        return;
      }

      toast({
        title: "¡Éxito!",
        description: "Te has inscrito correctamente al curso",
      });

      // Actualizar el contador de inscritos
      await supabase
        .from('cursos')
        .update({ 
          inscritos_count: cursos.find(c => c.id === cursoId)!.inscritos_count + 1 
        })
        .eq('id', cursoId);

      fetchCursos();
    } catch (error) {
      console.error('Error inscribing in curso:', error);
      toast({
        title: "Error",
        description: "Error al inscribirse en el curso",
        variant: "destructive",
      });
    }
  };

  const filteredCursos = cursos.filter(curso =>
    curso.titulo.toLowerCase().includes(searchTerm.toLowerCase()) ||
    curso.categoria.toLowerCase().includes(searchTerm.toLowerCase()) ||
    curso.instructor?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const getEstadoBadgeColor = (estado: string) => {
    switch (estado) {
      case 'programado':
        return 'bg-blue-500';
      case 'en_curso':
        return 'bg-green-500';
      case 'finalizado':
        return 'bg-gray-500';
      case 'cancelado':
        return 'bg-red-500';
      default:
        return 'bg-gray-500';
    }
  };

  const getNivelBadgeColor = (nivel: string) => {
    switch (nivel) {
      case 'basico':
        return 'bg-green-500';
      case 'intermedio':
        return 'bg-yellow-500';
      case 'avanzado':
        return 'bg-red-500';
      default:
        return 'bg-gray-500';
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="text-white">Cargando cursos...</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header y controles */}
      <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div className="flex-1 max-w-md">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
            <Input
              placeholder="Buscar cursos..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10 bg-white/10 border-white/20 text-white placeholder:text-gray-400"
            />
          </div>
        </div>
        
        {(userRole === 'admin' || userRole === 'moderador') && (
          <Button
            onClick={() => setShowCreateDialog(true)}
            className="bg-primary hover:bg-primary/80"
          >
            <Plus className="w-4 h-4 mr-2" />
            Crear Curso
          </Button>
        )}
      </div>

      {/* Lista de cursos */}
      <div className="grid gap-6">
        {filteredCursos.length === 0 ? (
          <Card className="bg-white/5 border-white/20">
            <CardContent className="text-center py-12">
              <BookOpen className="w-16 h-16 mx-auto mb-4 text-gray-400" />
              <h3 className="text-xl font-semibold text-white mb-2">
                No hay cursos disponibles
              </h3>
              <p className="text-gray-400">
                {searchTerm 
                  ? "No se encontraron cursos que coincidan con tu búsqueda"
                  : "Aún no se han creado cursos"}
              </p>
            </CardContent>
          </Card>
        ) : (
          filteredCursos.map((curso) => (
            <Card key={curso.id} className="bg-white/5 border-white/20 hover:bg-white/10 transition-colors">
              <CardHeader>
                <div className="flex justify-between items-start">
                  <div className="flex-1">
                    <CardTitle className="text-white text-xl mb-2">
                      {curso.titulo}
                    </CardTitle>
                    <CardDescription className="text-gray-300 mb-4">
                      {curso.descripcion}
                    </CardDescription>
                    <div className="flex flex-wrap gap-2">
                      <Badge className={`${getEstadoBadgeColor(curso.estado)} text-white`}>
                        {curso.estado.replace('_', ' ').toUpperCase()}
                      </Badge>
                      <Badge className={`${getNivelBadgeColor(curso.nivel)} text-white`}>
                        {curso.nivel.toUpperCase()}
                      </Badge>
                      <Badge variant="outline" className="text-white border-white/30">
                        {curso.categoria}
                      </Badge>
                      <Badge variant="outline" className="text-white border-white/30">
                        {curso.modalidad}
                      </Badge>
                    </div>
                  </div>
                  {curso.precio > 0 && (
                    <div className="text-right">
                      <div className="flex items-center text-yellow-400 font-bold text-lg">
                        <DollarSign className="w-4 h-4 mr-1" />
                        {curso.precio.toLocaleString()}
                      </div>
                    </div>
                  )}
                </div>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                  <div className="flex items-center text-gray-300">
                    <Clock className="w-4 h-4 mr-2" />
                    <span>{curso.duracion_horas}h</span>
                  </div>
                  <div className="flex items-center text-gray-300">
                    <Users className="w-4 h-4 mr-2" />
                    <span>{curso.inscritos_count}/{curso.capacidad_maxima}</span>
                  </div>
                  <div className="flex items-center text-gray-300">
                    <Calendar className="w-4 h-4 mr-2" />
                    <span>
                      {format(new Date(curso.fecha_inicio), 'dd MMM yyyy', { locale: es })}
                    </span>
                  </div>
                  {curso.modalidad === 'presencial' && curso.lugar && (
                    <div className="flex items-center text-gray-300">
                      <MapPin className="w-4 h-4 mr-2" />
                      <span className="truncate">{curso.lugar}</span>
                    </div>
                  )}
                </div>

                {curso.instructor && (
                  <div className="mb-4">
                    <p className="text-sm text-gray-400">Instructor:</p>
                    <p className="text-white font-medium">{curso.instructor}</p>
                  </div>
                )}

                <div className="flex justify-between items-center">
                  <div className="text-sm text-gray-400">
                    Fecha límite: {format(new Date(curso.fecha_fin), 'dd MMM yyyy', { locale: es })}
                  </div>
                  
                  {curso.estado === 'programado' && curso.inscritos_count < curso.capacidad_maxima && (
                    <Button
                      onClick={() => handleInscribirse(curso.id)}
                      className="bg-green-600 hover:bg-green-700 text-white"
                    >
                      Inscribirse
                    </Button>
                  )}
                </div>
              </CardContent>
            </Card>
          ))
        )}
      </div>

      {/* Dialog para crear curso */}
      <CreateCursoDialog
        open={showCreateDialog}
        onOpenChange={setShowCreateDialog}
        onSuccess={() => {
          fetchCursos();
          setShowCreateDialog(false);
        }}
      />
    </div>
  );
};