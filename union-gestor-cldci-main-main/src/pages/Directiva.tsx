import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Users, Phone, Mail, MapPin, Calendar, Crown, Star, ArrowLeft, Building2, Gavel, Settings, Globe, Edit, Plus } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { FileUploader } from "@/components/ui/file-uploader";
import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Link } from "react-router-dom";
import OrganosManager from "@/components/directiva/OrganosManager";
import MiembrosDirectivosManager from "@/components/directiva/MiembrosDirectivosManager";
import AsambleaManager from "@/components/directiva/AsambleaManager";
import SeccionalesManager from "@/components/directiva/SeccionalesManager";
import AsociacionesManager from "@/components/directiva/AsociacionesManager";
import SystemTest from "@/components/directiva/SystemTest";

const Directiva = () => {
  const [selectedOrganization, setSelectedOrganization] = useState("nacional");

  // Estructura organizativa del CLDC según Artículo 24 
  const estructuraOrganizativa = {
    organos_direccion: [
      "Asamblea General de Delegados",
      "Consejo Directivo Nacional", 
      "Presidencia"
    ],
    organos_consultivos: [
      "Consejo Consultivo de Ex Presidentes",
      "Comité de Ética y Disciplina",
      "Comisión Electoral"
    ],
    organos_operativos: [
      "Dirección Ejecutiva",
      "Dirección de Formación y Desarrollo Profesional", 
      "Dirección de Tecnología e Innovación Digital",
      "Dirección de Comunicación y Relaciones Públicas",
      "Dirección de Asuntos Legales y Gremiales",
      "Dirección de Deporte y Recreación",
      "Dirección de Programas Estudiantiles y Voluntariado",
      "Dirección de Asuntos de la Diáspora"
    ],
    organos_territoriales: [
      "Seccionales Provinciales y Regionales",
      "Seccionales de la Diáspora", 
      "Coordinación de Asociaciones Afiliadas"
    ]
  };

  // Composición de la Asamblea General según Artículo 25
  const composicionAsamblea = {
    delegados_por_membresia: {
      "15-30": 2,
      "31-50": 3,
      "50+": 4
    },
    otros_delegados: [
      "Un delegado por cada seccional provincial o regional",
      "Un delegado por cada seccional de la diáspora"
    ]
  };

  // Tipos de Asambleas según Artículo 26
  const tiposAsambleas = {
    ordinarias: "Se celebran una vez al año, en el mes de marzo",
    extraordinarias: "Se celebran cuando lo requieran asuntos urgentes o de especial importancia"
  };

  // Consejo Directivo Nacional según Artículo 31
  const consejoDirectivoNacional = [
    {
      id: "1",
      nombre: "Licdo. Bismarck Morales",
      cargo: "Presidente",
      foto: "/placeholder.svg",
      semblanza: "Presidente del CLDC, máximo representante del gremio de locutores y comunicadores dominicanos.",
      email: "presidente@cldc.org.do",
      telefono: "+1 809-555-0123",
      periodo: "2025-2028",
      esPresidente: true
    },
    {
      id: "2", 
      nombre: "Bolonia E. Jaime Santana",
      cargo: "Vicepresidente",
      foto: "/placeholder.svg",
      semblanza: "Vicepresidente del CLDC, segunda autoridad institucional y coordinador ejecutivo.",
      email: "vicepresidente@cldc.org.do",
      telefono: "+1 809-555-0124",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "3",
      nombre: "Giovanni Matos", 
      cargo: "Director General",
      foto: "/placeholder.svg",
      semblanza: "Director General del CLDC, responsable de la administración y coordinación general.",
      email: "directorgeneral@cldc.org.do",
      telefono: "+1 809-555-0125",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "4",
      nombre: "Aridio Castillo",
      cargo: "Director de Finanzas",
      foto: "/placeholder.svg", 
      semblanza: "Director de Finanzas del CLDC, encargado de la gestión financiera y presupuestaria.",
      email: "finanzas@cldc.org.do",
      telefono: "+1 809-555-0126",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "5",
      nombre: "Ana Delfi Ulloa",
      cargo: "Director de Comunicación",
      foto: "/placeholder.svg",
      semblanza: "Director de Comunicación del CLDC, responsable de la estrategia comunicacional.",
      email: "comunicacion@cldc.org.do",
      telefono: "+1 809-555-0127",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "6",
      nombre: "Víctor Peña",
      cargo: "Director de Tecnología",
      foto: "/placeholder.svg",
      semblanza: "Director de Tecnología del CLDC, encargado de innovación digital y sistemas.",
      email: "tecnologia@cldc.org.do",
      telefono: "+1 809-555-0128",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "7",
      nombre: "Luis Alberto Perdomo",
      cargo: "Director de Formación Profesional",
      foto: "/placeholder.svg",
      semblanza: "Director de Formación Profesional del CLDC, coordinador de capacitación y desarrollo.",
      email: "formacion@cldc.org.do",
      telefono: "+1 809-555-0129",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "8",
      nombre: "Stephanie Guerrero",
      cargo: "Director de Asuntos Legales",
      foto: "/placeholder.svg",
      semblanza: "Director de Asuntos Legales del CLDC, responsable de temas jurídicos y gremiales.",
      email: "legal@cldc.org.do",
      telefono: "+1 809-555-0130",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "9",
      nombre: "Sugey de Jesús",
      cargo: "Director de Relaciones Internacionales",
      foto: "/placeholder.svg",
      semblanza: "Director de Relaciones Internacionales del CLDC, encargado de cooperación internacional.",
      email: "internacional@cldc.org.do",
      telefono: "+1 809-555-0131",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "10",
      nombre: "Luis Peralta López",
      cargo: "Director de Deporte y Recreación",
      foto: "/placeholder.svg",
      semblanza: "Director de Deporte y Recreación del CLDC, coordinador de actividades deportivas y recreativas.",
      email: "deportes@cldc.org.do",
      telefono: "+1 809-555-0132",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "11",
      nombre: "Dionis Díaz",
      cargo: "Director de Programas Estudiantiles y Voluntariado",
      foto: "/placeholder.svg",
      semblanza: "Director de Programas Estudiantiles y Voluntariado del CLDC, coordinador de programas juveniles.",
      email: "estudiantil@cldc.org.do",
      telefono: "+1 809-555-0133",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "12",
      nombre: "Danilo Almanzar",
      cargo: "Director de Asuntos de la Diáspora",
      foto: "/placeholder.svg",
      semblanza: "Director de Asuntos de la Diáspora del CLDC, enlace con locutores dominicanos en el exterior.",
      email: "diaspora@cldc.org.do",
      telefono: "+1 809-555-0134",
      periodo: "2025-2028",
      esPresidente: false
    }
  ];

  // Comité de Ética y Disciplina según Artículo 49
  const comiteEticaDisciplina = [
    {
      id: "13",
      nombre: "Bienvenido A. Celados Sepúlveda",
      cargo: "Presidente Comité de Ética y Disciplina",
      foto: "/placeholder.svg",
      semblanza: "Presidente del Comité de Ética y Disciplina del CLDC, encargado de velar por el cumplimiento del código de ética profesional.",
      email: "etica@cldc.org.do",
      telefono: "+1 809-555-0136",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "14",
      nombre: "Román Ozoria Cepeda",
      cargo: "Miembro Comité de Ética y Disciplina",
      foto: "/placeholder.svg",
      semblanza: "Miembro del Comité de Ética y Disciplina del CLDC, responsable de procedimientos disciplinarios.",
      email: "etica2@cldc.org.do",
      telefono: "+1 809-555-0137",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "15",
      nombre: "Gabino Elias Salazar Rojas",
      cargo: "Miembro Comité de Ética y Disciplina",
      foto: "/placeholder.svg",
      semblanza: "Miembro del Comité de Ética y Disciplina del CLDC, especialista en formación ética.",
      email: "etica3@cldc.org.do",
      telefono: "+1 809-555-0138",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "16",
      nombre: "María Elena Rodríguez",
      cargo: "Miembro Comité de Ética y Disciplina",
      foto: "/placeholder.svg",
      semblanza: "Miembro del Comité de Ética y Disciplina del CLDC, experta en regulaciones profesionales.",
      email: "etica4@cldc.org.do",
      telefono: "+1 809-555-0139",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "17",
      nombre: "Carlos Antonio Mejía",
      cargo: "Miembro Comité de Ética y Disciplina",
      foto: "/placeholder.svg",
      semblanza: "Miembro del Comité de Ética y Disciplina del CLDC, coordinador de sanciones disciplinarias.",
      email: "etica5@cldc.org.do",
      telefono: "+1 809-555-0140",
      periodo: "2025-2028",
      esPresidente: false
    }
  ];

  // Comisión Electoral según Artículo 50
  const comisionElectoral = [
    {
      id: "18",
      nombre: "Dr. Roberto Fernández",
      cargo: "Presidente Comisión Electoral",
      foto: "/placeholder.svg",
      semblanza: "Presidente de la Comisión Electoral del CLDC, experto en procesos electorales y democracia interna.",
      email: "electoral@cldc.org.do",
      telefono: "+1 809-555-0141",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "19",
      nombre: "Licda. Carmen Vásquez",
      cargo: "Miembro Comisión Electoral",
      foto: "/placeholder.svg",
      semblanza: "Miembro de la Comisión Electoral del CLDC, especialista en verificación de requisitos de candidatos.",
      email: "electoral2@cldc.org.do",
      telefono: "+1 809-555-0142",
      periodo: "2025-2028",
      esPresidente: false
    },
    {
      id: "20",
      nombre: "Ing. Miguel Torres",
      cargo: "Miembro Comisión Electoral",
      foto: "/placeholder.svg",
      semblanza: "Miembro de la Comisión Electoral del CLDC, coordinador de logística electoral y proclamación de resultados.",
      email: "electoral3@cldc.org.do",
      telefono: "+1 809-555-0143",
      periodo: "2025-2028",
      esPresidente: false
    }
  ];

  // Consejo Consultivo de Ex Presidentes según Artículo 48
  const consejoExPresidentes = [
    {
      id: "21",
      nombre: "Dr. Carlos Ventura",
      cargo: "Ex Presidente CLDC (2019-2022)",
      foto: "/placeholder.svg",
      semblanza: "Ex Presidente del CLDC, asesor estratégico en temas institucionales y de continuidad política.",
      email: "expresidente1@cldc.org.do",
      telefono: "+1 809-555-0144",
      periodo: "Vitalicio",
      esPresidente: false
    },
    {
      id: "22",
      nombre: "Ana Daisy Guerrero",
      cargo: "Ex Presidenta CLDC (2016-2019)",
      foto: "/placeholder.svg",
      semblanza: "Ex Presidenta del CLDC, mediadora en conflictos internos y representante protocolar.",
      email: "expresidente2@cldc.org.do",
      telefono: "+1 809-555-0145",
      periodo: "Vitalicio",
      esPresidente: false
    },
    {
      id: "23",
      nombre: "Lic. Pedro Martínez",
      cargo: "Ex Presidente CLDC (2013-2016)",
      foto: "/placeholder.svg",
      semblanza: "Ex Presidente del CLDC, consejero en asuntos estratégicos y desarrollo institucional.",
      email: "expresidente3@cldc.org.do",
      telefono: "+1 809-555-0146",
      periodo: "Vitalicio",
      esPresidente: false
    }
  ];

  // Seccionales Provinciales según Artículo 51
  const seccionalSantiago = [
    {
      id: "24",
      nombre: "Pedro Luis García",
      cargo: "Coordinador Seccional Santiago",
      foto: "/placeholder.svg",
      semblanza: "Coordinador de la Seccional Santiago del CLDC, líder regional con 20 años de experiencia en radio.",
      email: "santiago@cldc.org.do",
      telefono: "+1 809-555-0200",
      periodo: "2025-2028"
    },
    {
      id: "25", 
      nombre: "Carmen Teresa Silva",
      cargo: "Secretaria Seccional Santiago",
      foto: "/placeholder.svg",
      semblanza: "Secretaria de la Seccional Santiago, especialista en producción radiofónica y capacitación.",
      email: "secretaria.santiago@cldc.org.do", 
      telefono: "+1 809-555-0201",
      periodo: "2025-2028"
    },
    {
      id: "26",
      nombre: "José Ramírez",
      cargo: "Tesorero Seccional Santiago",
      foto: "/placeholder.svg",
      semblanza: "Tesorero de la Seccional Santiago, encargado de la gestión financiera regional.",
      email: "tesorero.santiago@cldc.org.do",
      telefono: "+1 809-555-0202",
      periodo: "2025-2028"
    }
  ];

  // Seccionales de la Diáspora según Artículo 52
  const seccionalNuevaYork = [
    {
      id: "27",
      nombre: "María González",
      cargo: "Coordinadora Seccional Nueva York",
      foto: "/placeholder.svg",
      semblanza: "Coordinadora de la Seccional Nueva York del CLDC, representante de locutores dominicanos en Estados Unidos.",
      email: "nuevayork@cldc.org.do",
      telefono: "+1 212-555-0300",
      periodo: "2025-2028"
    },
    {
      id: "28",
      nombre: "Rafael Jiménez",
      cargo: "Vocal de Integración Cultural",
      foto: "/placeholder.svg",
      semblanza: "Vocal de Integración Cultural, especialista en preservación de la identidad dominicana en la diáspora.",
      email: "cultura.nuevayork@cldc.org.do",
      telefono: "+1 212-555-0301",
      periodo: "2025-2028"
    }
  ];

  const organizations = [
    { id: "nacional", name: "Consejo Directivo Nacional" },
    { id: "etica", name: "Comité de Ética y Disciplina" },
    { id: "electoral", name: "Comisión Electoral" },
    { id: "expresidentes", name: "Consejo Consultivo de Ex Presidentes" },
    { id: "santiago", name: "Seccional Santiago" },
    { id: "nuevayork", name: "Seccional Nueva York (Diáspora)" }
  ];

  const getCurrentDirectiva = () => {
    if (selectedOrganization === "nacional") return consejoDirectivoNacional;
    if (selectedOrganization === "etica") return comiteEticaDisciplina;
    if (selectedOrganization === "electoral") return comisionElectoral;
    if (selectedOrganization === "expresidentes") return consejoExPresidentes;
    if (selectedOrganization === "santiago") return seccionalSantiago;
    if (selectedOrganization === "nuevayork") return seccionalNuevaYork;
    return [];
  };

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Directiva", item: "https://cldci.com/directiva" }
  ]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO 
        title="Miembros Directivos | CLDC"
        description="Conoce a los miembros de la junta directiva nacional y seccionales del Colegio de Locutores de la República Dominicana."
      />
      <StructuredData data={breadcrumbData} />
      
      <div className="container mx-auto p-6 space-y-6">
        <div className="flex items-center gap-4 mb-6">
          <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
            <ArrowLeft className="h-4 w-4" />
          </Link>
          <div className="space-y-2">
            <h1 className="text-3xl font-bold text-white">Miembros Directivos</h1>
            <p className="text-blue-200">
              Directivas de la federación nacional y seccionales del CLDC
            </p>
          </div>
        </div>

      <Tabs defaultValue="estructura" className="space-y-6">
        <TabsList className="grid w-full grid-cols-7">
          <TabsTrigger value="estructura">Estructura</TabsTrigger>
          <TabsTrigger value="organos">Órganos</TabsTrigger>
          <TabsTrigger value="directivos">Directivos</TabsTrigger>
          <TabsTrigger value="asambleas">Asambleas</TabsTrigger>
          <TabsTrigger value="seccionales">Seccionales</TabsTrigger>
          <TabsTrigger value="asociaciones">Asociaciones</TabsTrigger>
          <TabsTrigger value="test">Sistema</TabsTrigger>
        </TabsList>

        <TabsContent value="estructura" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users className="h-5 w-5" />
                Estructura Organizativa CLDC
              </CardTitle>
              <CardDescription>
                Estructura según Artículo 24 - Órganos de dirección, consultivos, operativos y territoriales
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Información de la estructura organizativa */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Card className="p-4">
                  <h3 className="font-semibold text-sm mb-2">Composición Asamblea General</h3>
                  <div className="text-xs space-y-1">
                    <p><strong>Delegados por membresía:</strong></p>
                    <ul className="ml-2 space-y-1">
                      <li>• 15-30 miembros: 2 delegados</li>
                      <li>• 31-50 miembros: 3 delegados</li>
                      <li>• 50+ miembros: 4 delegados</li>
                    </ul>
                    <p className="mt-2"><strong>Otros delegados:</strong></p>
                    <ul className="ml-2 space-y-1">
                      <li>• Un delegado por seccional provincial/regional</li>
                      <li>• Un delegado por seccional de la diáspora</li>
                    </ul>
                  </div>
                </Card>
                
                <Card className="p-4">
                  <h3 className="font-semibold text-sm mb-2">Tipos de Asambleas</h3>
                  <div className="text-xs space-y-2">
                    <div>
                      <p><strong>Ordinarias:</strong></p>
                      <p className="ml-2">Se celebran una vez al año, en marzo</p>
                    </div>
                    <div>
                      <p><strong>Extraordinarias:</strong></p>
                      <p className="ml-2">Para asuntos urgentes o de especial importancia</p>
                    </div>
                  </div>
                </Card>
              </div>
              <div>
                <Label htmlFor="org-directiva">Organismo</Label>
                <Select value={selectedOrganization} onValueChange={setSelectedOrganization}>
                  <SelectTrigger className="w-full md:w-64">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {organizations.map((org) => (
                      <SelectItem key={org.id} value={org.id}>
                        {org.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="grid gap-6">
                {getCurrentDirectiva().map((miembro) => (
                  <Card key={miembro.id} className={`${('esPresidente' in miembro && miembro.esPresidente) ? 'border-2 border-primary bg-primary/5' : ''}`}>
                    <CardContent className="p-6">
                      <div className="flex flex-col md:flex-row gap-4">
                        <div className="flex-shrink-0">
                          <Avatar className="w-24 h-24 mx-auto md:mx-0">
                            <AvatarImage src={miembro.foto} alt={miembro.nombre} />
                            <AvatarFallback className="text-lg font-semibold">
                              {miembro.nombre.split(' ').map(n => n[0]).join('').slice(0, 2)}
                            </AvatarFallback>
                          </Avatar>
                        </div>
                        
                        <div className="flex-grow space-y-3">
                          <div className="text-center md:text-left">
                            <div className="flex items-center justify-center md:justify-start gap-2 mb-1">
                              <h3 className="text-xl font-bold">{miembro.nombre}</h3>
                              {'esPresidente' in miembro && miembro.esPresidente && <Crown className="h-5 w-5 text-yellow-600" />}
                            </div>
                            <div className="flex items-center justify-center md:justify-start gap-2">
                              <Badge variant="default" className="font-medium">
                                {miembro.cargo}
                              </Badge>
                              <Badge variant="outline">
                                {miembro.periodo}
                              </Badge>
                            </div>
                          </div>
                          
                          <p className="text-muted-foreground text-sm leading-relaxed">
                            {miembro.semblanza}
                          </p>
                          
                          <div className="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <div className="flex items-center gap-2">
                              <Mail className="h-4 w-4 text-muted-foreground" />
                              <span>{miembro.email}</span>
                            </div>
                            <div className="flex items-center gap-2">
                              <Phone className="h-4 w-4 text-muted-foreground" />
                              <span>{miembro.telefono}</span>
                            </div>
                          </div>
                        </div>
                        
                        <div className="flex-shrink-0 flex flex-row md:flex-col gap-2">
                          <Button variant="outline" size="sm" className="flex-1 md:flex-none">
                            <Edit className="h-4 w-4 mr-2" />
                            Editar
                          </Button>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="organos">
          <OrganosManager />
        </TabsContent>

        <TabsContent value="directivos">
          <MiembrosDirectivosManager />
        </TabsContent>

        <TabsContent value="asambleas">
          <AsambleaManager />
        </TabsContent>

        <TabsContent value="seccionales">
          <SeccionalesManager />
        </TabsContent>

        <TabsContent value="asociaciones">
          <AsociacionesManager />
        </TabsContent>

        <TabsContent value="test">
          <SystemTest />
        </TabsContent>

        <TabsContent value="gestionar" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Plus className="h-5 w-5" />
                Gestionar Directivos
              </CardTitle>
              <CardDescription>
                Agregar o modificar información de los miembros directivos
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="nuevo-organismo">Organismo</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar organismo" />
                    </SelectTrigger>
                    <SelectContent>
                      {organizations.map((org) => (
                        <SelectItem key={org.id} value={org.id}>
                          {org.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label htmlFor="nuevo-cargo">Cargo</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar cargo" />
                    </SelectTrigger>
                     <SelectContent>
                       <SelectItem value="presidente">Presidente</SelectItem>
                       <SelectItem value="vicepresidente">Vicepresidente</SelectItem>
                       <SelectItem value="director-general">Director General</SelectItem>
                       <SelectItem value="director-finanzas">Director de Finanzas</SelectItem>
                       <SelectItem value="director-comunicacion">Director de Comunicación</SelectItem>
                       <SelectItem value="director-tecnologia">Director de Tecnología</SelectItem>
                       <SelectItem value="director-formacion">Director de Formación Profesional</SelectItem>
                       <SelectItem value="director-legal">Director de Asuntos Legales</SelectItem>
                       <SelectItem value="director-internacional">Director de Relaciones Internacionales</SelectItem>
                       <SelectItem value="director-deportes">Director de Deporte y Recreación</SelectItem>
                       <SelectItem value="director-estudiantil">Director de Programas Estudiantiles y Voluntariado</SelectItem>
                       <SelectItem value="director-diaspora">Director de Asuntos de la Diáspora</SelectItem>
                     </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="nuevo-nombre">Nombre Completo</Label>
                  <Input placeholder="Nombre y apellido" />
                </div>
                <div>
                  <Label htmlFor="nuevo-periodo">Período</Label>
                  <Input placeholder="Ej: 2023-2025" />
                </div>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="nuevo-email">Email</Label>
                  <Input type="email" placeholder="email@cldci.org" />
                </div>
                <div>
                  <Label htmlFor="nuevo-telefono">Teléfono</Label>
                  <Input placeholder="+58 XXX XXX-XXXX" />
                </div>
              </div>
              
              <div>
                <Label htmlFor="nueva-semblanza">Semblanza Profesional</Label>
                <Textarea 
                  placeholder="Descripción de la trayectoria y experiencia profesional"
                  rows={4}
                />
              </div>
              
              <div>
                <Label htmlFor="nueva-foto">Fotografía</Label>
                <FileUploader
                  maxFiles={1}
                  maxSizePerFile={5}
                  acceptedTypes={['.jpg', '.jpeg', '.png', '.gif', '.bmp']}
                  bucketName="expedientes"
                  folderPath="directiva/fotos"
                  onFilesUploaded={(files) => {
                    console.log('Foto cargada:', files);
                  }}
                />
              </div>
              
              <div className="flex justify-end gap-2">
                <Button variant="outline">Cancelar</Button>
                <Button>
                  <Plus className="h-4 w-4 mr-2" />
                  Agregar Directivo
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
      </div>
    </div>
  );
};

export default Directiva;