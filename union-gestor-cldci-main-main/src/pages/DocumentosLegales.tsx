import { SEO } from "@/components/seo/SEO";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ArrowLeft, FileText, Gavel, ScrollText, Scale, Calendar, Download, Eye } from "lucide-react";
import { Link } from "react-router-dom";
import { useState } from "react";

const DocumentosLegales = () => {
  const [tipoDocumento, setTipoDocumento] = useState("estatuto");

  // Datos de ejemplo para los documentos legales
  const documentosRecientes = [
    { id: 1, tipo: "estatuto", titulo: "Estatuto del CLDCI", fecha: "2023-11-30", estado: "vigente" },
    { id: 2, tipo: "ley", titulo: "Ley 123-24 de Comunicación", fecha: "2024-01-15", estado: "vigente" },
    { id: 3, tipo: "decreto", titulo: "Decreto 456-24 Reglamentario", fecha: "2024-02-10", estado: "vigente" },
    { id: 4, tipo: "resolucion", titulo: "Resolución 789-24 CLDCI", fecha: "2024-03-05", estado: "vigente" },
    { id: 5, tipo: "acta", titulo: "Acta Asamblea General 2024", fecha: "2024-02-20", estado: "aprobada" },
    { id: 6, tipo: "reglamento", titulo: "Reglamento Interno CLDCI", fecha: "2023-12-15", estado: "vigente" },
  ];

  const tiposDocumento = [
    { value: "estatuto", label: "Estatuto", icon: ScrollText },
    { value: "acta", label: "Acta de Asamblea", icon: Calendar },
    { value: "ley", label: "Ley", icon: Scale },
    { value: "decreto", label: "Decreto", icon: Gavel },
    { value: "reglamento", label: "Reglamento", icon: ScrollText },
    { value: "resolucion", label: "Resolución", icon: FileText },
  ];

  const getEstadoBadge = (estado: string) => {
    const variants = {
      vigente: "default",
      aprobada: "default", 
      pendiente: "secondary",
      derogada: "destructive"
    } as const;
    return variants[estado as keyof typeof variants] || "secondary";
  };

  return (
    <main className="container mx-auto py-10">
      <SEO 
        title="Documentos Legales – CLDCI" 
        description="Gestión integral de documentos legales: leyes, decretos, reglamentos, resoluciones y actas de asambleas." 
      />
      
      <div className="flex items-center gap-4 mb-6">
        <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10">
          <ArrowLeft className="h-4 w-4" />
        </Link>
        <h1 className="text-3xl font-bold">Documentos Legales</h1>
      </div>

      <Tabs defaultValue="gestionar" className="space-y-6">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="gestionar">Gestionar Documentos</TabsTrigger>
          <TabsTrigger value="repositorio">Repositorio Legal</TabsTrigger>
          <TabsTrigger value="asambleas">Asambleas y Quórum</TabsTrigger>
        </TabsList>

        <TabsContent value="gestionar" className="space-y-6">
          <div className="grid lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <FileText className="w-5 h-5 text-primary" />
                  Crear Documento Legal
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label htmlFor="tipo">Tipo de Documento</Label>
                  <Select value={tipoDocumento} onValueChange={setTipoDocumento}>
                    <SelectTrigger>
                      <SelectValue placeholder="Seleccionar tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      {tiposDocumento.map((tipo) => (
                        <SelectItem key={tipo.value} value={tipo.value}>
                          <div className="flex items-center gap-2">
                            <tipo.icon className="w-4 h-4" />
                            {tipo.label}
                          </div>
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                
                <div>
                  <Label htmlFor="titulo">Título del Documento</Label>
                  <Input 
                    id="titulo" 
                    placeholder={
                      tipoDocumento === "estatuto" ? "Ej: Estatuto del CLDCI" :
                      tipoDocumento === "acta" ? "Ej: Acta Asamblea General 2024" :
                      tipoDocumento === "ley" ? "Ej: Ley 123-24 de Comunicación" :
                      tipoDocumento === "decreto" ? "Ej: Decreto 456-24 Reglamentario" :
                      tipoDocumento === "reglamento" ? "Ej: Reglamento Interno CLDCI" :
                      "Ej: Resolución 789-24 CLDCI"
                    } 
                  />
                </div>

                <div>
                  <Label htmlFor="fecha">Fecha</Label>
                  <Input id="fecha" type="date" />
                </div>

                <div>
                  <Label htmlFor="resumen">Descripción/Resumen</Label>
                  <Textarea 
                    id="resumen" 
                    placeholder={
                      tipoDocumento === "estatuto" ? "Marco normativo general, estructura organizativa, derechos y deberes..." :
                      tipoDocumento === "acta" ? "Acuerdos, resoluciones, asistentes..." :
                      "Descripción del contenido y alcance del documento"
                    }
                  />
                </div>

                <div>
                  <Label htmlFor="archivo">Adjuntar Documento (PDF)</Label>
                  <Input id="archivo" type="file" accept="application/pdf" />
                </div>
              </CardContent>
              <CardFooter>
                <Button className="w-full">
                  Guardar Documento
                </Button>
              </CardFooter>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Documentos Recientes</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {documentosRecientes.slice(0, 5).map((doc) => (
                  <div key={doc.id} className="flex items-center justify-between p-3 bg-muted rounded-lg">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        {(() => {
                          const TipoIcon = tiposDocumento.find(t => t.value === doc.tipo)?.icon || FileText;
                          return <TipoIcon className="w-4 h-4 text-muted-foreground" />;
                        })()}
                        <span className="font-medium text-sm">{doc.titulo}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className="text-xs text-muted-foreground">{doc.fecha}</span>
                        <Badge variant={getEstadoBadge(doc.estado)} className="text-xs">
                          {doc.estado}
                        </Badge>
                      </div>
                    </div>
                    <div className="flex gap-1">
                      <Button size="sm" variant="ghost">
                        <Eye className="w-3 h-3" />
                      </Button>
                      <Button size="sm" variant="ghost">
                        <Download className="w-3 h-3" />
                      </Button>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="repositorio" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <ScrollText className="w-5 h-5 text-primary" />
                Repositorio de Documentos Legales
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Resumen por tipos */}
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                {tiposDocumento.map((tipo) => {
                  const count = documentosRecientes.filter(doc => doc.tipo === tipo.value).length;
                  return (
                    <div key={tipo.value} className="p-4 border rounded-lg hover:bg-muted/50 transition-colors cursor-pointer">
                      <div className="flex items-center gap-3 mb-2">
                        <tipo.icon className="w-6 h-6 text-primary" />
                        <h4 className="font-semibold">{tipo.label}s</h4>
                      </div>
                      <p className="text-sm text-muted-foreground mb-2">
                        {count} documento{count !== 1 ? 's' : ''} disponible{count !== 1 ? 's' : ''}
                      </p>
                      <Button variant="outline" size="sm" className="w-full">
                        Ver Todos
                      </Button>
                    </div>
                  );
                })}
              </div>

              {/* Lista completa de documentos */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold">Todos los Documentos</h3>
                <div className="grid gap-3">
                  {documentosRecientes.map((doc) => (
                    <Card key={doc.id} className="hover:shadow-sm transition-shadow">
                      <CardContent className="p-4">
                        <div className="flex items-center justify-between">
                          <div className="flex-1">
                            <div className="flex items-center gap-2 mb-2">
                              {(() => {
                                const TipoIcon = tiposDocumento.find(t => t.value === doc.tipo)?.icon || FileText;
                                return <TipoIcon className="w-5 h-5 text-primary" />;
                              })()}
                              <h4 className="font-semibold">{doc.titulo}</h4>
                              <Badge variant={getEstadoBadge(doc.estado)}>
                                {doc.estado}
                              </Badge>
                            </div>
                            <div className="flex items-center gap-4 text-sm text-muted-foreground">
                              <span className="flex items-center gap-1">
                                <Calendar className="w-4 h-4" />
                                {doc.fecha}
                              </span>
                              <span className="capitalize">
                                {tiposDocumento.find(t => t.value === doc.tipo)?.label || doc.tipo}
                              </span>
                            </div>
                          </div>
                          <div className="flex items-center gap-2">
                            <Button variant="outline" size="sm">
                              <Eye className="w-4 h-4 mr-2" />
                              Ver
                            </Button>
                            <Button variant="outline" size="sm">
                              <Download className="w-4 h-4 mr-2" />
                              Descargar
                            </Button>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="asambleas" className="space-y-6">
          <div className="grid lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className="w-5 h-5 text-primary" />
                  Control de Quórum
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="text-sm text-muted-foreground space-y-2">
                  <p>• Registro de asistencia presencial y virtual.</p>
                  <p>• Cálculo automático de quórum mínimo requerido.</p>
                  <p>• Exportación de listados para auditoría.</p>
                  <p>• Validación de miembros habilitados para votar.</p>
                </div>
                
                <div className="bg-muted p-4 rounded-lg">
                  <h5 className="font-semibold mb-2">Próxima Asamblea</h5>
                  <div className="text-sm space-y-1">
                    <p><strong>Fecha:</strong> 15 de Marzo, 2024</p>
                    <p><strong>Tipo:</strong> Asamblea General Ordinaria</p>
                    <p><strong>Quórum requerido:</strong> 60% (15 miembros)</p>
                    <p><strong>Modalidad:</strong> Híbrida</p>
                  </div>
                </div>
              </CardContent>
              <CardFooter>
                <Button className="w-full">
                  Gestionar Asistencia
                </Button>
              </CardFooter>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Historial de Asambleas</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {documentosRecientes.filter(doc => doc.tipo === "acta").map((acta) => (
                  <div key={acta.id} className="flex items-center justify-between p-3 bg-muted rounded-lg">
                    <div>
                      <p className="font-medium text-sm">{acta.titulo}</p>
                      <p className="text-xs text-muted-foreground">{acta.fecha}</p>
                    </div>
                    <div className="flex gap-1">
                      <Button size="sm" variant="outline">
                        <Eye className="w-3 h-3 mr-1" />
                        Ver
                      </Button>
                      <Button size="sm" variant="outline">
                        <Download className="w-3 h-3 mr-1" />
                        PDF
                      </Button>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </main>
  );
};

export default DocumentosLegales;
