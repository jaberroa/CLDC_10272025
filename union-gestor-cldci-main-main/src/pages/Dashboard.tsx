import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { ModuleCard } from "@/components/dashboard/ModuleCard";
import { StatsCard } from "@/components/dashboard/StatsCard";
import { ActivityFeed } from "@/components/dashboard/ActivityFeed";
import { DistributionChart } from "@/components/dashboard/DistributionChart";
import { 
  UserPlus, 
  Users, 
  Vote, 
  BarChart3, 
  Plug,
  FileText,
  Award,
  Crown,
  Database,
  CreditCard,
  BookOpen,
  UserCheck,
  GraduationCap,
  Building2,
  Calendar
} from "lucide-react";
import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const Dashboard = () => {
  const [stats, setStats] = useState({
    miembrosActivos: 5,
    organizaciones: 86,
    proximaAsamblea: "Sin asambleas programadas",
    organizacionesPorTipo: [] as { tipo: string; cantidad: number; ejemplos: string }[],
  });

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      // Use secure function for dashboard stats
      const { data: dashboardStats, error: statsError } = await supabase.rpc('get_dashboard_stats');
      
      if (statsError) throw statsError;

      // Fetch próxima asamblea
      const { data: asambleas } = await supabase
        .from('asambleas')
        .select('fecha_asamblea, titulo')
        .gte('fecha_asamblea', new Date().toISOString().split('T')[0])
        .order('fecha_asamblea', { ascending: true })
        .limit(1);

      // Estructura organizativa del CLDC según los estatutos - Artículo 24
      const organizacionesPorTipo = [
        {
          tipo: "Órganos de Dirección",
          cantidad: 3,
          ejemplos: "Asamblea General de Delegados, Consejo Directivo Nacional, Presidencia"
        },
        {
          tipo: "Órganos Consultivos",
          cantidad: 3,
          ejemplos: "Consejo Consultivo de Ex Presidentes, Comité de Ética y Disciplina, Comisión Electoral"
        },
        {
          tipo: "Órganos Operativos",
          cantidad: 8,
          ejemplos: "Dirección Ejecutiva, Dirección de Formación y Desarrollo Profesional, Dirección de Tecnología e Innovación Digital, Dirección de Comunicación y Relaciones Públicas"
        },
        {
          tipo: "Consejo Directivo Nacional",
          cantidad: 12,
          ejemplos: "Presidente, Vicepresidente, Director General, Director de Finanzas, Director de Comunicación, Director de Tecnología, etc."
        },
        {
          tipo: "Seccionales Provinciales y Regionales",
          cantidad: 32,
          ejemplos: "Una por provincia dominicana - Comité Ejecutivo: Coordinador, Secretario, Tesorero, Vocal de Comunicación, Vocal de Formación"
        },
        {
          tipo: "Seccionales de la Diáspora",
          cantidad: 8,
          ejemplos: "Representaciones internacionales - Comité Ejecutivo: Coordinador, Secretario, Tesorero, Vocal de Comunicación, Vocal de Integración Cultural"
        },
        {
          tipo: "Coordinación de Asociaciones Afiliadas",
          cantidad: 15,
          ejemplos: "Asociaciones con 15-30 miembros: 2 delegados, 31-50 miembros: 3 delegados, +50 miembros: 4 delegados"
        },
        {
          tipo: "Direcciones Especializadas",
          cantidad: 5,
          ejemplos: "Dirección de Asuntos Legales y Gremiales, Dirección de Deporte y Recreación, Dirección de Programas Estudiantiles, Dirección de Asuntos de la Diáspora"
        }
      ];

      setStats({
        miembrosActivos: dashboardStats?.[0]?.total_miembros_activos || 5,
        organizaciones: organizacionesPorTipo.reduce((total, tipo) => total + tipo.cantidad, 0),
        proximaAsamblea: asambleas?.[0]?.titulo || "Sin asambleas programadas",
        organizacionesPorTipo,
      });
    } catch (error) {
      console.error('Error fetching stats:', error);
      toast({
        title: "Error",
        description: "Error al cargar las estadísticas",
        variant: "destructive",
      });
    }
  };

  const modules = [
    { 
      icon: BarChart3, 
      title: "Dashboard", 
      to: "/dashboard",
      colorClass: "bg-module-dashboard text-white"
    },
    { 
      icon: UserCheck, 
      title: "Registro", 
      to: "/registro",
      colorClass: "bg-module-registro text-white"
    },
    { 
      icon: Users, 
      title: "Miembros", 
      to: "/miembros",
      colorClass: "bg-module-miembros text-white"
    },
    { 
      icon: Vote, 
      title: "Elecciones", 
      to: "/elecciones",
      colorClass: "bg-module-elecciones text-white"
    },
    { 
      icon: FileText, 
      title: "Documentos Legales", 
      to: "/documentos-legales",
      colorClass: "bg-primary text-white"
    },
    { 
      icon: Award, 
      title: "Premios", 
      to: "/premios",
      colorClass: "bg-warning text-white"
    },
    { 
      icon: CreditCard, 
      title: "Transparencia", 
      to: "/transparencia",
      colorClass: "bg-success text-white"
    },
    { 
      icon: Crown, 
      title: "Directiva", 
      to: "/directiva",
      colorClass: "bg-module-asambleas text-white"
    },
    { 
      icon: BarChart3, 
      title: "Reportes", 
      to: "/reportes",
      colorClass: "bg-module-reportes text-white"
    },
    { 
      icon: Plug, 
      title: "Integraciones", 
      to: "/integraciones",
      colorClass: "bg-module-integraciones text-white"
    },
    { 
      icon: BookOpen, 
      title: "Formación Profesional", 
      to: "/formacion-profesional",
      colorClass: "bg-green-600 text-white"
    },
    { 
      icon: GraduationCap, 
      title: "Diagnóstico", 
      to: "/diagnostico",
      colorClass: "bg-accent text-white"
    }
  ];

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Dashboard", item: "https://cldci.com/dashboard" }
  ]);

  return (
    <main className="min-h-screen bg-gradient-to-br from-primary-dark via-primary to-blue-600 text-white">
      <SEO
        title="Dashboard - CÍRCULO DE LOCUTORES COLEGIADOS, INC."
        description="Panel de control y gestión integral para la modernización tecnológica al servicio de los locutores profesionales."
      />
      <StructuredData data={breadcrumbData} />

      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">
            Dashboard Principal
          </h1>
          <p className="text-xl text-blue-200 max-w-2xl mx-auto">
            Centro de control del sistema de gestión CLDCI
          </p>
        </div>

        {/* Modules Grid */}
        <div className="mb-12">
          <h2 className="text-2xl font-bold mb-6 text-center">Módulos del Sistema</h2>
          <p className="text-center text-blue-200 mb-8">Selecciona un módulo para acceder a sus funcionalidades</p>
          
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-w-6xl mx-auto">
            {modules.map((module, index) => (
              <ModuleCard
                key={index}
                icon={module.icon}
                title={module.title}
                to={module.to}
                colorClass={module.colorClass}
              />
            ))}
          </div>
        </div>

        {/* Stats Overview */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <StatsCard
            title="Miembros Activos"
            value={stats.miembrosActivos.toString()}
            icon={Users}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
          <StatsCard
            title="Organizaciones"
            value={stats.organizaciones.toString()}
            icon={Building2}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
          <StatsCard
            title="Próxima Asamblea"
            value={stats.proximaAsamblea}
            icon={Calendar}
            colorClass="bg-white/10 backdrop-blur-sm border-blue-300/20 text-white"
          />
        </div>

        {/* Dashboard Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Distribution Chart */}
          <div className="bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20 p-6">
            <DistributionChart />
          </div>

          {/* Activity Feed */}
          <div className="bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20 p-6">
            <ActivityFeed />
          </div>
        </div>

        {/* Organization Types */}
        <div className="mt-12">
          <h2 className="text-2xl font-bold mb-8 text-center">
            Tipos de Organizaciones en la Federación
          </h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {stats.organizacionesPorTipo.map((org, index) => (
              <Card key={index} className="bg-white/10 backdrop-blur-sm border-blue-300/20">
                <CardHeader>
                  <CardTitle className="text-white flex items-center justify-between">
                    {org.tipo}
                    <span className="text-yellow-400 text-2xl font-bold">{org.cantidad}</span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-blue-200 text-sm leading-relaxed">
                    {org.ejemplos}
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </main>
  );
};

export default Dashboard;