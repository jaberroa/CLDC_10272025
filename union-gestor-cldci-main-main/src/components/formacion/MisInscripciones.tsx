import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { toast } from "@/hooks/use-toast";
import { BookOpen, Award, Clock, Calendar, Download, Eye } from "lucide-react";
import { format } from "date-fns";
import { es } from "date-fns/locale";

interface InscripcionCurso {
  id: string;
  curso_id: string;
  estado_inscripcion: string;
  fecha_inscripcion: string;
  asistencia_porcentaje: number;
  calificacion_final: number | null;
  certificado_obtenido: boolean;
  certificado_url: string | null;
  cursos: {
    titulo: string;
    descripcion: string;
    duracion_horas: number;
    instructor: string;
    fecha_inicio: string;
    fecha_fin: string;
    estado: string;
  };
}

interface InscripcionDiplomado {
  id: string;
  diplomado_id: string;
  estado_inscripcion: string;
  fecha_inscripcion: string;
  promedio_general: number | null;
  creditos_obtenidos: number;
  diploma_obtenido: boolean;
  diploma_url: string | null;
  diplomados: {
    titulo: string;
    descripcion: string;
    duracion_meses: number;
    coordinador_academico: string;
    fecha_inicio: string;
    fecha_fin: string;
    estado: string;
    creditos_academicos: number;
  };
}

interface MisInscripcionesProps {
  onStatsChange?: () => void;
}

export const MisInscripciones = ({ onStatsChange }: MisInscripcionesProps) => {
  const [inscripcionesCursos, setInscripcionesCursos] = useState<InscripcionCurso[]>([]);
  const [inscripcionesDiplomados, setInscripcionesDiplomados] = useState<InscripcionDiplomado[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchInscripciones();
  }, []);

  const fetchInscripciones = async () => {
    try {
      setLoading(true);
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      // Obtener el miembro asociado al usuario
      const { data: miembros } = await supabase
        .from('miembros')
        .select('id')
        .eq('user_id', user.id)
        .limit(1);

      if (!miembros || miembros.length === 0) return;

      const miembroId = miembros[0].id;

      // Obtener inscripciones a cursos
      const { data: cursosData, error: cursosError } = await supabase
        .from('inscripciones_cursos')
        .select(`
          *,
          cursos (
            titulo,
            descripcion,
            duracion_horas,
            instructor,
            fecha_inicio,
            fecha_fin,
            estado
          )
        `)
        .eq('miembro_id', miembroId)
        .order('fecha_inscripcion', { ascending: false });

      if (cursosError) throw cursosError;

      // Obtener inscripciones a diplomados
      const { data: diplomadosData, error: diplomadosError } = await supabase
        .from('inscripciones_diplomados')
        .select(`
          *,
          diplomados (
            titulo,
            descripcion,
            duracion_meses,
            coordinador_academico,
            fecha_inicio,
            fecha_fin,
            estado,
            creditos_academicos
          )
        `)
        .eq('miembro_id', miembroId)
        .order('fecha_inscripcion', { ascending: false });

      if (diplomadosError) throw diplomadosError;

      setInscripcionesCursos(cursosData || []);
      setInscripcionesDiplomados(diplomadosData || []);
      onStatsChange?.();
    } catch (error) {
      console.error('Error fetching inscripciones:', error);
      toast({
        title: "Error",
        description: "Error al cargar tus inscripciones",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const getEstadoBadgeColor = (estado: string) => {
    switch (estado) {
      case 'activa':
        return 'bg-green-500';
      case 'finalizada':
        return 'bg-blue-500';
      case 'cancelada':
        return 'bg-red-500';
      case 'suspendida':
        return 'bg-yellow-500';
      default:
        return 'bg-gray-500';
    }
  };

  const getProgresoColor = (porcentaje: number) => {
    if (porcentaje >= 80) return 'text-green-400';
    if (porcentaje >= 60) return 'text-yellow-400';
    return 'text-red-400';
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="text-white">Cargando tus inscripciones...</div>
      </div>
    );
  }

  if (inscripcionesCursos.length === 0 && inscripcionesDiplomados.length === 0) {
    return (
      <Card className="bg-white/5 border-white/20">
        <CardContent className="text-center py-12">
          <BookOpen className="w-16 h-16 mx-auto mb-4 text-gray-400" />
          <h3 className="text-xl font-semibold text-white mb-2">
            No tienes inscripciones activas
          </h3>
          <p className="text-gray-400">
            Inscríbete en cursos y diplomados para comenzar tu formación profesional
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="space-y-8">
      {/* Inscripciones a Cursos */}
      {inscripcionesCursos.length > 0 && (
        <div>
          <h3 className="text-xl font-semibold text-white mb-4 flex items-center">
            <BookOpen className="w-5 h-5 mr-2" />
            Mis Cursos ({inscripcionesCursos.length})
          </h3>
          <div className="grid gap-4">
            {inscripcionesCursos.map((inscripcion) => (
              <Card key={inscripcion.id} className="bg-white/5 border-white/20">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <CardTitle className="text-white text-lg mb-2">
                        {inscripcion.cursos.titulo}
                      </CardTitle>
                      <CardDescription className="text-gray-300 mb-3">
                        {inscripcion.cursos.descripcion}
                      </CardDescription>
                      <div className="flex flex-wrap gap-2">
                        <Badge className={`${getEstadoBadgeColor(inscripcion.estado_inscripcion)} text-white`}>
                          {inscripcion.estado_inscripcion.toUpperCase()}
                        </Badge>
                        <Badge variant="outline" className="text-white border-white/30">
                          {inscripcion.cursos.estado}
                        </Badge>
                      </div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div className="text-sm">
                      <p className="text-gray-400">Duración:</p>
                      <p className="text-white">{inscripcion.cursos.duracion_horas} horas</p>
                    </div>
                    <div className="text-sm">
                      <p className="text-gray-400">Instructor:</p>
                      <p className="text-white">{inscripcion.cursos.instructor}</p>
                    </div>
                    <div className="text-sm">
                      <p className="text-gray-400">Fecha de inscripción:</p>
                      <p className="text-white">
                        {format(new Date(inscripcion.fecha_inscripcion), 'dd MMM yyyy', { locale: es })}
                      </p>
                    </div>
                  </div>

                  {inscripcion.asistencia_porcentaje > 0 && (
                    <div className="mb-4">
                      <div className="flex justify-between items-center mb-2">
                        <span className="text-sm text-gray-400">Asistencia:</span>
                        <span className={`text-sm font-medium ${getProgresoColor(inscripcion.asistencia_porcentaje)}`}>
                          {inscripcion.asistencia_porcentaje}%
                        </span>
                      </div>
                      <div className="w-full bg-gray-700 rounded-full h-2">
                        <div 
                          className="bg-blue-500 h-2 rounded-full transition-all duration-300"
                          style={{ width: `${inscripcion.asistencia_porcentaje}%` }}
                        />
                      </div>
                    </div>
                  )}

                  <div className="flex justify-between items-center">
                    <div className="text-sm text-gray-400">
                      {inscripcion.calificacion_final && (
                        <span>Calificación: <span className="text-white font-medium">{inscripcion.calificacion_final}</span></span>
                      )}
                    </div>
                    
                    {inscripcion.certificado_obtenido && inscripcion.certificado_url && (
                      <Button
                        onClick={() => window.open(inscripcion.certificado_url!, '_blank')}
                        className="bg-green-600 hover:bg-green-700 text-white"
                        size="sm"
                      >
                        <Download className="w-4 h-4 mr-2" />
                        Descargar Certificado
                      </Button>
                    )}
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      )}

      {/* Inscripciones a Diplomados */}
      {inscripcionesDiplomados.length > 0 && (
        <div>
          <h3 className="text-xl font-semibold text-white mb-4 flex items-center">
            <Award className="w-5 h-5 mr-2" />
            Mis Diplomados ({inscripcionesDiplomados.length})
          </h3>
          <div className="grid gap-4">
            {inscripcionesDiplomados.map((inscripcion) => (
              <Card key={inscripcion.id} className="bg-white/5 border-white/20">
                <CardHeader>
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <CardTitle className="text-white text-lg mb-2">
                        {inscripcion.diplomados.titulo}
                      </CardTitle>
                      <CardDescription className="text-gray-300 mb-3">
                        {inscripcion.diplomados.descripcion}
                      </CardDescription>
                      <div className="flex flex-wrap gap-2">
                        <Badge className={`${getEstadoBadgeColor(inscripcion.estado_inscripcion)} text-white`}>
                          {inscripcion.estado_inscripcion.toUpperCase()}
                        </Badge>
                        <Badge variant="outline" className="text-white border-white/30">
                          {inscripcion.diplomados.estado}
                        </Badge>
                        <Badge variant="outline" className="text-yellow-400 border-yellow-400/50">
                          {inscripcion.diplomados.creditos_academicos} Créditos
                        </Badge>
                      </div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div className="text-sm">
                      <p className="text-gray-400">Duración:</p>
                      <p className="text-white">{inscripcion.diplomados.duracion_meses} meses</p>
                    </div>
                    <div className="text-sm">
                      <p className="text-gray-400">Coordinador:</p>
                      <p className="text-white">{inscripcion.diplomados.coordinador_academico}</p>
                    </div>
                    <div className="text-sm">
                      <p className="text-gray-400">Fecha de inscripción:</p>
                      <p className="text-white">
                        {format(new Date(inscripcion.fecha_inscripcion), 'dd MMM yyyy', { locale: es })}
                      </p>
                    </div>
                  </div>

                  {inscripcion.creditos_obtenidos > 0 && (
                    <div className="mb-4">
                      <div className="flex justify-between items-center mb-2">
                        <span className="text-sm text-gray-400">Progreso de Créditos:</span>
                        <span className="text-sm font-medium text-blue-400">
                          {inscripcion.creditos_obtenidos}/{inscripcion.diplomados.creditos_academicos}
                        </span>
                      </div>
                      <div className="w-full bg-gray-700 rounded-full h-2">
                        <div 
                          className="bg-blue-500 h-2 rounded-full transition-all duration-300"
                          style={{ 
                            width: `${(inscripcion.creditos_obtenidos / inscripcion.diplomados.creditos_academicos) * 100}%` 
                          }}
                        />
                      </div>
                    </div>
                  )}

                  <div className="flex justify-between items-center">
                    <div className="text-sm text-gray-400">
                      {inscripcion.promedio_general && (
                        <span>Promedio: <span className="text-white font-medium">{inscripcion.promedio_general}</span></span>
                      )}
                    </div>
                    
                    {inscripcion.diploma_obtenido && inscripcion.diploma_url && (
                      <Button
                        onClick={() => window.open(inscripcion.diploma_url!, '_blank')}
                        className="bg-green-600 hover:bg-green-700 text-white"
                        size="sm"
                      >
                        <Download className="w-4 h-4 mr-2" />
                        Descargar Diploma
                      </Button>
                    )}
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};