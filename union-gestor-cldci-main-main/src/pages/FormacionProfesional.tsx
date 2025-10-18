import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { CursosManager } from "@/components/formacion/CursosManager";
import { DiplomadosManager } from "@/components/formacion/DiplomadosManager";
import { MisInscripciones } from "@/components/formacion/MisInscripciones";
import { 
  GraduationCap, 
  BookOpen, 
  Award, 
  Users,
  Calendar,
  Clock
} from "lucide-react";
import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { StatsCard } from "@/components/dashboard/StatsCard";

const FormacionProfesional = () => {
  const [stats, setStats] = useState({
    cursosActivos: 0,
    diplomadosDisponibles: 0,
    misInscripciones: 0,
    certificadosObtenidos: 0
  });

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      // Obtener estadísticas de cursos
      const { data: cursos } = await supabase
        .from('cursos')
        .select('id')
        .eq('estado', 'programado');

      // Obtener estadísticas de diplomados
      const { data: diplomados } = await supabase
        .from('diplomados')
        .select('id')
        .eq('estado', 'programado');

      // Obtener mis inscripciones
      const { data: miembros } = await supabase
        .from('miembros')
        .select('id')
        .eq('user_id', (await supabase.auth.getUser()).data.user?.id);

      if (miembros && miembros.length > 0) {
        const miembroId = miembros[0].id;
        
        const { data: inscripcionesCursos } = await supabase
          .from('inscripciones_cursos')
          .select('id, certificado_obtenido')
          .eq('miembro_id', miembroId);

        const { data: inscripcionesDiplomados } = await supabase
          .from('inscripciones_diplomados')
          .select('id, diploma_obtenido')
          .eq('miembro_id', miembroId);

        const totalInscripciones = (inscripcionesCursos?.length || 0) + (inscripcionesDiplomados?.length || 0);
        const certificadosCursos = inscripcionesCursos?.filter(i => i.certificado_obtenido).length || 0;
        const diplomasObtenidos = inscripcionesDiplomados?.filter(i => i.diploma_obtenido).length || 0;

        setStats({
          cursosActivos: cursos?.length || 0,
          diplomadosDisponibles: diplomados?.length || 0,
          misInscripciones: totalInscripciones,
          certificadosObtenidos: certificadosCursos + diplomasObtenidos
        });
      } else {
        setStats({
          cursosActivos: cursos?.length || 0,
          diplomadosDisponibles: diplomados?.length || 0,
          misInscripciones: 0,
          certificadosObtenidos: 0
        });
      }
    } catch (error) {
      console.error('Error fetching formacion stats:', error);
    }
  };

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Formación Profesional", item: "https://cldci.com/formacion-profesional" }
  ]);

  return (
    <main className="min-h-screen bg-gradient-to-br from-primary-dark via-primary to-blue-600 text-white">
      <SEO
        title="Formación Profesional - CÍRCULO DE LOCUTORES COLEGIADOS, INC."
        description="Módulo de formación profesional con cursos y diplomados para el desarrollo de competencias en locución profesional."
      />
      <StructuredData data={breadcrumbData} />

      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="text-center mb-12">
          <div className="flex items-center justify-center mb-4">
            <GraduationCap className="w-12 h-12 mr-4" />
            <h1 className="text-4xl md:text-5xl font-bold">
              Formación Profesional
            </h1>
          </div>
          <p className="text-xl text-blue-200 max-w-3xl mx-auto">
            Desarrolla tus competencias profesionales con nuestros cursos especializados y programas de diplomado
          </p>
        </div>

        {/* Stats Overview */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
          <StatsCard
            title="Cursos Activos"
            value={stats.cursosActivos.toString()}
            icon={BookOpen}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
          <StatsCard
            title="Diplomados Disponibles"
            value={stats.diplomadosDisponibles.toString()}
            icon={Award}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
          <StatsCard
            title="Mis Inscripciones"
            value={stats.misInscripciones.toString()}
            icon={Users}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
          <StatsCard
            title="Certificados Obtenidos"
            value={stats.certificadosObtenidos.toString()}
            icon={Award}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
        </div>

        {/* Main Content */}
        <div className="bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20 p-6">
          <Tabs defaultValue="cursos" className="w-full">
            <TabsList className="grid w-full grid-cols-4 bg-white/20">
              <TabsTrigger value="cursos" className="text-white data-[state=active]:bg-white data-[state=active]:text-primary">
                <BookOpen className="w-4 h-4 mr-2" />
                Cursos
              </TabsTrigger>
              <TabsTrigger value="diplomados" className="text-white data-[state=active]:bg-white data-[state=active]:text-primary">
                <Award className="w-4 h-4 mr-2" />
                Diplomados
              </TabsTrigger>
              <TabsTrigger value="inscripciones" className="text-white data-[state=active]:bg-white data-[state=active]:text-primary">
                <Users className="w-4 h-4 mr-2" />
                Mis Inscripciones
              </TabsTrigger>
              <TabsTrigger value="certificados" className="text-white data-[state=active]:bg-white data-[state=active]:text-primary">
                <Award className="w-4 h-4 mr-2" />
                Certificados
              </TabsTrigger>
            </TabsList>

            <TabsContent value="cursos" className="mt-6">
              <Card className="bg-white/5 border-white/20">
                <CardHeader>
                  <CardTitle className="text-white flex items-center">
                    <BookOpen className="w-5 h-5 mr-2" />
                    Gestión de Cursos
                  </CardTitle>
                  <CardDescription className="text-blue-200">
                    Administra los cursos de formación profesional disponibles
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <CursosManager onStatsChange={fetchStats} />
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="diplomados" className="mt-6">
              <Card className="bg-white/5 border-white/20">
                <CardHeader>
                  <CardTitle className="text-white flex items-center">
                    <Award className="w-5 h-5 mr-2" />
                    Gestión de Diplomados
                  </CardTitle>
                  <CardDescription className="text-blue-200">
                    Administra los programas de diplomado disponibles
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <DiplomadosManager onStatsChange={fetchStats} />
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="inscripciones" className="mt-6">
              <Card className="bg-white/5 border-white/20">
                <CardHeader>
                  <CardTitle className="text-white flex items-center">
                    <Users className="w-5 h-5 mr-2" />
                    Mis Inscripciones
                  </CardTitle>
                  <CardDescription className="text-blue-200">
                    Consulta y gestiona tus inscripciones activas
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <MisInscripciones onStatsChange={fetchStats} />
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="certificados" className="mt-6">
              <Card className="bg-white/5 border-white/20">
                <CardHeader>
                  <CardTitle className="text-white flex items-center">
                    <Award className="w-5 h-5 mr-2" />
                    Mis Certificados y Diplomas
                  </CardTitle>
                  <CardDescription className="text-blue-200">
                    Descarga y consulta tus certificados obtenidos
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="text-center py-12">
                    <Award className="w-16 h-16 mx-auto mb-4 text-yellow-400" />
                    <h3 className="text-xl font-semibold text-white mb-2">
                      Certificados y Diplomas
                    </h3>
                    <p className="text-blue-200 mb-4">
                      Aquí encontrarás todos tus certificados y diplomas obtenidos
                    </p>
                    <p className="text-sm text-blue-300">
                      Esta sección se completará con la funcionalidad de descarga de certificados
                    </p>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </main>
  );
};

export default FormacionProfesional;