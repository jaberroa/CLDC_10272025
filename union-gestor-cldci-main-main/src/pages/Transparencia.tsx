import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { FileUploader } from "@/components/ui/file-uploader";
import { FileText, Upload, Download, Eye, Calendar, Users, DollarSign, Target, BarChart3, ArrowLeft } from "lucide-react";
import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Link } from "react-router-dom";

const Transparencia = () => {
  const [selectedOrganization, setSelectedOrganization] = useState("todos");
  const [selectedYear, setSelectedYear] = useState(new Date().getFullYear().toString());

  // Datos simulados para demostración
  const organizations = [
    { id: "1", name: "Federación Nacional de Locutores", type: "Nacional" },
    { id: "2", name: "Seccional Caracas", type: "Seccional" },
    { id: "3", name: "Seccional Maracaibo", type: "Seccional" },
    { id: "4", name: "Seccional Valencia", type: "Seccional" }
  ];

  const operationalPlans = [
    {
      id: "1",
      organization: "Federación Nacional",
      year: "2024",
      status: "Aprobado",
      objectives: 8,
      budget: 250000,
      progress: 65,
      lastUpdate: "2024-03-15"
    },
    {
      id: "2", 
      organization: "Seccional Caracas",
      year: "2024",
      status: "En Revisión",
      objectives: 6,
      budget: 120000,
      progress: 45,
      lastUpdate: "2024-03-10"
    }
  ];

  const transparencyReports = [
    {
      id: "1",
      title: "Informe Financiero Q1 2024",
      type: "Financiero",
      organization: "Federación Nacional",
      date: "2024-03-31",
      status: "Publicado"
    },
    {
      id: "2",
      title: "Ejecución Presupuestaria 2023",
      type: "Presupuestario", 
      organization: "Federación Nacional",
      date: "2024-01-15",
      status: "Publicado"
    },
    {
      id: "3",
      title: "Plan de Actividades Q2 2024",
      type: "Operativo",
      organization: "Seccional Caracas",
      date: "2024-04-01",
      status: "Borrador"
    }
  ];

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Transparencia", item: "https://cldci.com/transparencia" }
  ]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO 
        title="Transparencia y Rendición de Cuentas | CLDCI"
        description="Portal de transparencia con planes operativos anuales, informes financieros y rendición de cuentas de todos los organismos de la federación."
      />
      <StructuredData data={breadcrumbData} />
      <StructuredData data={breadcrumbData} />
      
      <div className="container mx-auto p-6 space-y-6">
        <div className="flex items-center gap-4 mb-6">
          <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
            <ArrowLeft className="h-4 w-4" />
          </Link>
          <div className="space-y-2">
            <h1 className="text-3xl font-bold text-white">Transparencia y Rendición de Cuentas</h1>
            <p className="text-blue-200">
              Portal de transparencia con información pública sobre la gestión de todos los organismos de la federación
            </p>
          </div>
        </div>

      <Tabs defaultValue="planes" className="space-y-6">
        <TabsList className="grid w-full grid-cols-5">
          <TabsTrigger value="planes">Planes Operativos</TabsTrigger>
          <TabsTrigger value="informes">Informes</TabsTrigger>
          <TabsTrigger value="contabilidad">Contabilidad</TabsTrigger>
          <TabsTrigger value="presupuestos">Presupuestos</TabsTrigger>
          <TabsTrigger value="cargar">Cargar Documentos</TabsTrigger>
        </TabsList>

        <TabsContent value="planes" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="h-5 w-5" />
                Planes Operativos Anuales
              </CardTitle>
              <CardDescription>
                Consulta y gestiona los planes operativos anuales de cada organismo
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <Label htmlFor="org-filter">Organismo</Label>
                  <Select value={selectedOrganization} onValueChange={setSelectedOrganization}>
                    <SelectTrigger>
                      <SelectValue placeholder="Todos los organismos" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="todos">Todos los organismos</SelectItem>
                      {organizations.map((org) => (
                        <SelectItem key={org.id} value={org.id}>
                          {org.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label htmlFor="year-filter">Año</Label>
                  <Select value={selectedYear} onValueChange={setSelectedYear}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="2024">2024</SelectItem>
                      <SelectItem value="2023">2023</SelectItem>
                      <SelectItem value="2022">2022</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="flex items-end">
                  <Button className="w-full">
                    <Target className="h-4 w-4 mr-2" />
                    Nuevo Plan
                  </Button>
                </div>
              </div>

              <div className="grid gap-4">
                {operationalPlans.map((plan) => (
                  <Card key={plan.id} className="border-l-4 border-l-primary">
                    <CardContent className="p-4">
                      <div className="flex items-center justify-between">
                        <div className="space-y-2">
                          <div className="flex items-center gap-2">
                            <h3 className="font-semibold">{plan.organization} - {plan.year}</h3>
                            <Badge variant={plan.status === "Aprobado" ? "default" : "secondary"}>
                              {plan.status}
                            </Badge>
                          </div>
                          <div className="flex items-center gap-4 text-sm text-muted-foreground">
                            <div className="flex items-center gap-1">
                              <Target className="h-4 w-4" />
                              {plan.objectives} objetivos
                            </div>
                            <div className="flex items-center gap-1">
                              <DollarSign className="h-4 w-4" />
                              ${plan.budget.toLocaleString()}
                            </div>
                            <div className="flex items-center gap-1">
                              <BarChart3 className="h-4 w-4" />
                              {plan.progress}% ejecutado
                            </div>
                            <div className="flex items-center gap-1">
                              <Calendar className="h-4 w-4" />
                              {plan.lastUpdate}
                            </div>
                          </div>
                        </div>
                        <div className="flex items-center gap-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4 mr-2" />
                            Ver
                          </Button>
                          <Button variant="outline" size="sm">
                            <Download className="h-4 w-4 mr-2" />
                            Descargar
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

        <TabsContent value="informes" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5" />
                Informes de Transparencia
              </CardTitle>
              <CardDescription>
                Informes financieros, operativos y de gestión de todos los organismos
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4">
                {transparencyReports.map((report) => (
                  <Card key={report.id}>
                    <CardContent className="p-4">
                      <div className="flex items-center justify-between">
                        <div className="space-y-2">
                          <div className="flex items-center gap-2">
                            <h3 className="font-semibold">{report.title}</h3>
                            <Badge variant="outline">{report.type}</Badge>
                            <Badge variant={report.status === "Publicado" ? "default" : "secondary"}>
                              {report.status}
                            </Badge>
                          </div>
                          <div className="flex items-center gap-4 text-sm text-muted-foreground">
                            <div className="flex items-center gap-1">
                              <Users className="h-4 w-4" />
                              {report.organization}
                            </div>
                            <div className="flex items-center gap-1">
                              <Calendar className="h-4 w-4" />
                              {report.date}
                            </div>
                          </div>
                        </div>
                        <div className="flex items-center gap-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4 mr-2" />
                            Ver
                          </Button>
                          <Button variant="outline" size="sm">
                            <Download className="h-4 w-4 mr-2" />
                            Descargar
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

        <TabsContent value="contabilidad" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <DollarSign className="h-5 w-5" />
                Contabilidad por Organismo
              </CardTitle>
              <CardDescription>
                Estados financieros, libros contables y registros por organismo
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="org-contable">Organismo</Label>
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
                  <Label htmlFor="periodo-contable">Período</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar período" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="2024">2024</SelectItem>
                      <SelectItem value="2023">2023</SelectItem>
                      <SelectItem value="2022">2022</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid gap-4">
                <Card className="border-l-4 border-l-green-500">
                  <CardContent className="p-4">
                    <div className="flex items-center justify-between">
                      <div className="space-y-2">
                        <h3 className="font-semibold">Balance General - Federación Nacional</h3>
                        <p className="text-sm text-muted-foreground">Período: Enero - Marzo 2024</p>
                        <div className="flex items-center gap-4 text-sm">
                          <div className="flex items-center gap-1">
                            <DollarSign className="h-4 w-4 text-green-600" />
                            Activos: $450,000
                          </div>
                          <div className="flex items-center gap-1">
                            <DollarSign className="h-4 w-4 text-red-600" />
                            Pasivos: $125,000
                          </div>
                          <div className="flex items-center gap-1">
                            <DollarSign className="h-4 w-4 text-blue-600" />
                            Patrimonio: $325,000
                          </div>
                        </div>
                      </div>
                      <div className="flex items-center gap-2">
                        <Button variant="outline" size="sm">
                          <Eye className="h-4 w-4 mr-2" />
                          Ver
                        </Button>
                        <Button variant="outline" size="sm">
                          <Download className="h-4 w-4 mr-2" />
                          Descargar
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="presupuestos" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <DollarSign className="h-5 w-5" />
                Ejecución Presupuestaria
              </CardTitle>
              <CardDescription>
                Seguimiento de la ejecución presupuestaria por organismo
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="text-center py-8 text-muted-foreground">
                <DollarSign className="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p>Funcionalidad de seguimiento presupuestario en desarrollo</p>
                <p className="text-sm">Próximamente: gráficos de ejecución, comparativos y alertas</p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="cargar" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Upload className="h-5 w-5" />
                Cargar Documentos
              </CardTitle>
              <CardDescription>
                Carga planes operativos, informes y documentos de transparencia
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="doc-org">Organismo</Label>
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
                  <Label htmlFor="doc-type">Tipo de Documento</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="plan">Plan Operativo Anual</SelectItem>
                      <SelectItem value="financiero">Informe Financiero</SelectItem>
                      <SelectItem value="operativo">Informe Operativo</SelectItem>
                      <SelectItem value="presupuesto">Ejecución Presupuestaria</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              
              <div>
                <Label htmlFor="doc-title">Título del Documento</Label>
                <Input placeholder="Ej: Plan Operativo Anual 2024" />
              </div>
              
              <div>
                <Label htmlFor="doc-description">Descripción</Label>
                <Textarea 
                  placeholder="Descripción breve del contenido del documento"
                  rows={3}
                />
              </div>
              
              <div>
                <Label>Documentos</Label>
                <FileUploader
                  maxFiles={5}
                  maxSizePerFile={10}
                  acceptedTypes={[
                    '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
                    '.txt', '.rtf', '.odt', '.ods', '.odp', '.csv',
                    '.jpg', '.jpeg', '.png', '.gif', '.bmp'
                  ]}
                  bucketName="expedientes"
                  folderPath="transparencia"
                  onFilesUploaded={(files) => {
                    console.log('Archivos cargados:', files);
                  }}
                />
              </div>
              
              <div className="flex justify-end gap-2">
                <Button variant="outline">Cancelar</Button>
                <Button>
                  <Upload className="h-4 w-4 mr-2" />
                  Cargar Documento
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

export default Transparencia;