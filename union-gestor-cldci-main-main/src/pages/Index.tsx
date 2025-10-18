import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateOrganizationSchema, generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import {
  UserPlus, 
  Users, 
  Vote, 
  Calendar, 
  BarChart3, 
  Plug,
  CheckCircle,
  FileText,
  Award,
  Crown,
  Database,
  CreditCard,
  Bookmark,
  BookOpen,
  Settings,
  Shield,
  UserCheck,
  GraduationCap
} from "lucide-react";
import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { Link } from "react-router-dom";
import cldcLogo from "@/assets/cldc-logo.png";

const Index = () => {
  const [activeModule, setActiveModule] = useState<string | null>(null);
  const [stats, setStats] = useState({
    miembrosActivos: 0,
    organizaciones: 0,
    proximaAsamblea: "Cargando...",
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
        miembrosActivos: dashboardStats?.[0]?.total_miembros_activos || 0,
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

  const features = [
    { 
      icon: Database, 
      title: "Censo y actualización de datos",
      description: "Mantén tu perfil profesional actualizado en tiempo real",
      to: "/miembros"
    },
    { 
      icon: UserCheck, 
      title: "Registro y adecuación",
      description: "Proceso de registro y validación de miembros nuevos",
      to: "/registro-adecuacion"
    },
    { 
      icon: Users, 
      title: "Perfil profesional",
      description: "Gestiona tu información personal y profesional",
      to: "/perfil"
    },
    { 
      icon: Crown, 
      title: "Directiva institucional",
      description: "Estructura organizacional y cargos directivos",
      to: "/directiva"
    },
    { 
      icon: Vote, 
      title: "Votaciones electrónicas",
      description: "Sistema seguro para elecciones y consultas internas",
      to: "/elecciones"
    },
    { 
      icon: CreditCard, 
      title: "Transparencia financiera",
      description: "Estado de cuenta, pagos y transparencia institucional",
      to: "/transparencia"
    },
    { 
      icon: FileText, 
      title: "Documentos legales",
      description: "Estatutos, reglamentos y marco legal institucional",
      to: "/documentos-legales"
    },
    { 
      icon: BarChart3, 
      title: "Reportes y estadísticas",
      description: "Análisis de datos y reportes institucionales",
      to: "/reportes"
    },
    { 
      icon: Award, 
      title: "Reconocimientos y premios",
      description: "Sistema de méritos y reconocimientos profesionales",
      to: "/premios"
    },
    { 
      icon: Plug, 
      title: "Integraciones digitales",
      description: "Conexiones con plataformas externas y APIs",
      to: "/integraciones"
    },
    { 
      icon: GraduationCap, 
      title: "Formación profesional",
      description: "Programas de capacitación y desarrollo profesional",
      to: "/diagnostico"
    }
  ];

  const organizationData = generateOrganizationSchema({
    name: "CÍRCULO DE LOCUTORES COLEGIADOS, INC.",
    description: "Organización profesional de locutores dominicanos",
    url: "https://cldci.com"
  });

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" }
  ]);

  return (
    <main className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO
        title="El Corazón Digital - CÍRCULO DE LOCUTORES COLEGIADOS, INC."
        description="Plataforma de gestión integral para la modernización tecnológica al servicio de los locutores profesionales."
        jsonLd={organizationData}
      />
      <StructuredData data={breadcrumbData} />

      {/* Hero Section */}
      <div className="container mx-auto px-6 py-12">
        <div className="text-center mb-16">
          <h1 className="text-5xl md:text-6xl font-bold mb-4">
            El Corazón <span className="text-yellow-400">Digital</span>: Nuestra Plataforma de Gestión
          </h1>
          <p className="text-xl text-blue-200 max-w-3xl mx-auto">
            Modernización tecnológica al servicio de los locutores
          </p>
        </div>

        {/* Main Content */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-16">
          {/* Organization Image */}
          <div className="relative">
            <div className="rounded-2xl overflow-hidden shadow-2xl border-2 border-blue-300/30 bg-white/10 backdrop-blur-sm p-8">
              <img 
                src={cldcLogo} 
                alt="CÍRCULO DE LOCUTORES COLEGIADOS, INC."
                className="w-full h-auto max-w-md mx-auto"
              />
            </div>
            <div className="absolute bottom-4 left-4 right-4 bg-black/50 backdrop-blur-sm rounded-lg p-3">
              <p className="text-white text-sm font-medium text-center">
                Prototipo CLDCI - Portal de Miembros
              </p>
            </div>
          </div>

          {/* Features List */}
          <div className="space-y-4">
            <h2 className="text-3xl font-bold mb-8">Módulos de la plataforma:</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto pr-2">
              {features.map((feature, index) => (
                <Link
                  key={index}
                  to={feature.to}
                  className="flex items-start space-x-3 p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20 hover:bg-white/20 transition-all duration-300 group"
                >
                  <div className="flex-shrink-0">
                    <div className="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                      <feature.icon className="w-5 h-5 text-blue-900" />
                    </div>
                  </div>
                  <div className="min-w-0 flex-1">
                    <h3 className="text-base font-semibold text-white mb-1 truncate">{feature.title}</h3>
                    <p className="text-blue-200 text-xs leading-tight">{feature.description}</p>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </div>

        {/* Quote Section */}
        <div className="text-center py-12 px-8 bg-white/10 backdrop-blur-sm rounded-2xl border border-blue-300/20">
          <blockquote className="text-2xl md:text-3xl font-light italic text-blue-100 mb-4">
            "Este es el futuro, hoy! Una plataforma que nos conecta y fortalece como institución"
          </blockquote>
        </div>

        {/* Quick Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
          <div className="text-center p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20">
            <div className="text-3xl font-bold text-yellow-400 mb-2">{stats.miembrosActivos}</div>
            <div className="text-blue-200">Miembros Activos</div>
          </div>
          <div className="text-center p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20">
            <div className="text-3xl font-bold text-yellow-400 mb-2">{stats.organizaciones}</div>
            <div className="text-blue-200">Organizaciones</div>
          </div>
          <div className="text-center p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-blue-300/20">
            <div className="text-3xl font-bold text-yellow-400 mb-2">100%</div>
            <div className="text-blue-200">Digital</div>
          </div>
        </div>
      </div>
    </main>
  );
};

export default Index;
