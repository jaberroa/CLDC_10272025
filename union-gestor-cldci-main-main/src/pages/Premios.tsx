import { SEO } from "@/components/seo/SEO";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Progress } from "@/components/ui/progress";
import { ArrowLeft, Trophy, Star, Mic, Award, Users, Vote, Calendar, FileText, Eye, Download } from "lucide-react";
import { Link } from "react-router-dom";
import { useState } from "react";

const Premios = () => {
  const [categoriaSeleccionada, setCategoriaSeleccionada] = useState("microfono_oro");
  const [periodoActivo, setPeriodoActivo] = useState("2024");

  // Categorías de premios
  const categoriasPremios = [
    { 
      id: "microfono_oro", 
      nombre: "Micrófono de Oro", 
      descripcion: "Reconocimiento al locutor destacado del año",
      icon: Mic,
      color: "text-yellow-600"
    },
    { 
      id: "salon_fama", 
      nombre: "Salón de la Fama", 
      descripcion: "Reconocimiento a la trayectoria excepcional",
      icon: Star,
      color: "text-purple-600"
    },
    { 
      id: "cabina_locutor", 
      nombre: "Cabina del Locutor", 
      descripcion: "Mejor espacio de locución del año",
      icon: Trophy,
      color: "text-blue-600"
    },
    { 
      id: "locutor_joven", 
      nombre: "Locutor Joven Prometedor", 
      descripcion: "Reconocimiento a nuevos talentos",
      icon: Award,
      color: "text-green-600"
    },
    { 
      id: "trayectoria", 
      nombre: "Trayectoria Profesional", 
      descripcion: "Por años de servicio a la comunicación",
      icon: Star,
      color: "text-orange-600"
    }
  ];

  // Datos de ejemplo para postulaciones
  const postulaciones = [
    { 
      id: 1, 
      categoria: "microfono_oro", 
      candidato: "Ana María González", 
      postulante: "CLDCI Santo Domingo", 
      votos: 45, 
      estado: "activa",
      fecha: "2024-01-15"
    },
    { 
      id: 2, 
      categoria: "microfono_oro", 
      candidato: "Carlos Eduardo Pérez", 
      postulante: "CLDCI Santiago", 
      votos: 38, 
      estado: "activa",
      fecha: "2024-01-18"
    },
    { 
      id: 3, 
      categoria: "salon_fama", 
      candidato: "María José Rodríguez", 
      postulante: "Asociación Locutores Deportivos", 
      votos: 67, 
      estado: "activa",
      fecha: "2024-01-10"
    },
    { 
      id: 4, 
      categoria: "cabina_locutor", 
      candidato: "Radio Estrella FM", 
      postulante: "Gremio Productores", 
      votos: 23, 
      estado: "activa",
      fecha: "2024-01-20"
    }
  ];

  // Ganadores anteriores
  const ganadoresAnteriores = [
    { año: "2023", categoria: "Micrófono de Oro", ganador: "Luis Alberto Santos", organizacion: "CLDCI La Vega" },
    { año: "2023", categoria: "Salón de la Fama", ganador: "Carmen Elena Martínez", organizacion: "CLDCI Santo Domingo" },
    { año: "2022", categoria: "Micrófono de Oro", ganador: "Roberto Miguel Vargas", organizacion: "CLDCI Santiago" },
    { año: "2022", categoria: "Cabina del Locutor", ganador: "Radio Capital", organizacion: "CLDCI Distrito Nacional" }
  ];

  const totalVotantes = 85;
  const votosEmitidos = postulaciones.reduce((total, p) => total + p.votos, 0);
  const participacion = Math.round((votosEmitidos / totalVotantes) * 100);

  const getEstadoBadge = (estado: string) => {
    const variants = {
      activa: "default",
      cerrada: "secondary",
      ganador: "destructive"
    } as const;
    return variants[estado as keyof typeof variants] || "secondary";
  };

  return (
    <main className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO 
        title="Premios y Reconocimientos – CLDCI" 
        description="Sistema de premios, postulaciones y votaciones para Micrófono de Oro, Salón de la Fama y otros reconocimientos." 
      />
      
      <div className="container mx-auto px-6 py-12">
        <div className="flex items-center gap-4 mb-6">
          <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
            <ArrowLeft className="h-4 w-4" />
          </Link>
          <h1 className="text-3xl font-bold text-white">Premios y Reconocimientos</h1>
        </div>

        <Tabs defaultValue="postular" className="space-y-6">
          <TabsList className="grid w-full grid-cols-4 bg-white/10 backdrop-blur-sm border-white/20">
            <TabsTrigger value="postular" className="text-white data-[state=active]:bg-white/20">Postular</TabsTrigger>
            <TabsTrigger value="votaciones" className="text-white data-[state=active]:bg-white/20">Votaciones</TabsTrigger>
            <TabsTrigger value="resultados" className="text-white data-[state=active]:bg-white/20">Resultados</TabsTrigger>
            <TabsTrigger value="historial" className="text-white data-[state=active]:bg-white/20">Historial</TabsTrigger>
          </TabsList>

          <TabsContent value="postular" className="space-y-6">
            <div className="grid lg:grid-cols-2 gap-6">
              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2 text-white">
                    <Trophy className="w-5 h-5 text-yellow-400" />
                    Nueva Postulación
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="categoria" className="text-white">Categoría del Premio</Label>
                    <Select value={categoriaSeleccionada} onValueChange={setCategoriaSeleccionada}>
                      <SelectTrigger className="bg-white/10 border-white/20 text-white">
                        <SelectValue placeholder="Seleccionar categoría" />
                      </SelectTrigger>
                      <SelectContent>
                        {categoriasPremios.map((categoria) => (
                          <SelectItem key={categoria.id} value={categoria.id}>
                            <div className="flex items-center gap-2">
                              <categoria.icon className={`w-4 h-4 ${categoria.color}`} />
                              {categoria.nombre}
                            </div>
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <p className="text-xs text-blue-200 mt-1">
                      {categoriasPremios.find(c => c.id === categoriaSeleccionada)?.descripcion}
                    </p>
                  </div>
                  
                  <div>
                    <Label htmlFor="candidato" className="text-white">Nombre del Candidato/Entidad</Label>
                    <Input 
                      id="candidato" 
                      placeholder="Nombre completo del postulado"
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                    />
                  </div>

                  <div>
                    <Label htmlFor="organizacion" className="text-white">Organización que Postula</Label>
                    <Select>
                      <SelectTrigger className="bg-white/10 border-white/20 text-white">
                        <SelectValue placeholder="Seleccionar organización" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="seccional_sd">CLDCI Santo Domingo</SelectItem>
                        <SelectItem value="seccional_stgo">CLDCI Santiago</SelectItem>
                        <SelectItem value="asociacion_dep">Asociación Locutores Deportivos</SelectItem>
                        <SelectItem value="gremio_prod">Gremio de Productores</SelectItem>
                        <SelectItem value="sindicato">Sindicato de Trabajadores</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <div>
                    <Label htmlFor="justificacion" className="text-white">Justificación de la Postulación</Label>
                    <Textarea 
                      id="justificacion" 
                      placeholder="Describa los méritos y logros que justifican esta postulación..."
                      rows={4}
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                    />
                  </div>

                  <div>
                    <Label htmlFor="documentos" className="text-white">Documentos de Respaldo (PDF)</Label>
                    <Input 
                      id="documentos" 
                      type="file" 
                      accept="application/pdf" 
                      multiple 
                      className="bg-white/10 border-white/20 text-white file:bg-yellow-400 file:text-blue-900 file:border-0 file:rounded file:px-3 file:py-1 file:mr-3"
                    />
                    <p className="text-xs text-blue-200 mt-1">
                      Puede adjuntar certificados, cartas de recomendación, etc.
                    </p>
                  </div>
                </CardContent>
                <CardFooter>
                  <Button className="w-full bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold">
                    Enviar Postulación
                  </Button>
                </CardFooter>
              </Card>

              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white">Categorías de Premios {periodoActivo}</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {categoriasPremios.map((categoria) => (
                    <div key={categoria.id} className="p-4 border border-white/20 rounded-lg hover:bg-white/5 transition-colors">
                      <div className="flex items-start gap-3">
                        <categoria.icon className={`w-6 h-6 ${categoria.color} mt-1`} />
                        <div className="flex-1">
                          <h4 className="font-semibold mb-1 text-white">{categoria.nombre}</h4>
                          <p className="text-sm text-blue-200 mb-2">
                            {categoria.descripcion}
                          </p>
                          <div className="flex items-center gap-2 text-xs">
                            <span className="bg-yellow-400/20 text-yellow-400 px-2 py-1 rounded">
                              {postulaciones.filter(p => p.categoria === categoria.id).length} postulaciones
                            </span>
                            <span className="text-blue-200">
                              Cierre: 31 Mar 2024
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="votaciones" className="space-y-6">
            <div className="grid lg:grid-cols-3 gap-6 mb-6">
              <Card className="text-center bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardContent className="p-4">
                  <div className="text-2xl font-bold text-blue-300">{totalVotantes}</div>
                  <p className="text-sm text-blue-200">Miembros Habilitados</p>
                </CardContent>
              </Card>
              <Card className="text-center bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardContent className="p-4">
                  <div className="text-2xl font-bold text-green-400">{votosEmitidos}</div>
                  <p className="text-sm text-blue-200">Votos Emitidos</p>
                </CardContent>
              </Card>
              <Card className="text-center bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardContent className="p-4">
                  <div className="text-2xl font-bold text-yellow-400">{participacion}%</div>
                  <p className="text-sm text-blue-200">Participación</p>
                </CardContent>
              </Card>
            </div>

            <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-white">
                  <Vote className="w-5 h-5 text-blue-300" />
                  Postulaciones para Votación
                </CardTitle>
                <div className="flex items-center gap-2">
                  <Progress value={participacion} className="flex-1" />
                  <span className="text-sm text-blue-200">{participacion}% participación</span>
                </div>
              </CardHeader>
              <CardContent className="space-y-4">
                {postulaciones.map((postulacion) => {
                  const categoria = categoriasPremios.find(c => c.id === postulacion.categoria);
                  return (
                    <div key={postulacion.id} className="border border-white/20 rounded-lg p-4 bg-white/5">
                      <div className="flex items-center justify-between mb-3">
                        <div className="flex items-center gap-3">
                          {categoria && <categoria.icon className={`w-5 h-5 ${categoria.color}`} />}
                          <div>
                            <h4 className="font-semibold text-white">{postulacion.candidato}</h4>
                            <p className="text-sm text-blue-200">
                              {categoria?.nombre} • Postulado por {postulacion.postulante}
                            </p>
                          </div>
                        </div>
                        <Badge 
                          variant={getEstadoBadge(postulacion.estado) as any}
                          className="bg-green-400/20 text-green-400"
                        >
                          {postulacion.estado}
                        </Badge>
                      </div>
                      
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-4">
                          <div className="text-lg font-bold text-blue-300">
                            {postulacion.votos} votos
                          </div>
                          <Progress value={(postulacion.votos / totalVotantes) * 100} className="w-32" />
                        </div>
                        <div className="flex gap-2">
                          <Button size="sm" variant="outline" className="border-white/20 text-white hover:bg-white/10">
                            <Eye className="w-3 h-3 mr-1" />
                            Ver Detalles
                          </Button>
                          <Button size="sm" className="bg-yellow-400 text-blue-900 hover:bg-yellow-500">
                            <Vote className="w-3 h-3 mr-1" />
                            Votar
                          </Button>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="resultados" className="space-y-6">
            <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-white">
                  <Trophy className="w-5 h-5 text-yellow-400" />
                  Resultados Votación {periodoActivo}
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center mb-6 p-6 bg-white/5 rounded-lg">
                  <Calendar className="w-12 h-12 mx-auto mb-3 text-blue-300" />
                  <h3 className="text-lg font-semibold mb-2 text-white">Votación en Proceso</h3>
                  <p className="text-blue-200 mb-4">
                    La votación estará abierta hasta el 31 de Marzo de 2024
                  </p>
                  <div className="flex items-center justify-center gap-4 text-sm">
                    <span className="bg-yellow-400/20 text-yellow-400 px-3 py-1 rounded">
                      Faltan 15 días
                    </span>
                    <span className="text-blue-200">
                      {participacion}% de participación actual
                    </span>
                  </div>
                </div>

                <div className="text-center">
                  <Button variant="outline" className="border-white/20 text-white hover:bg-white/10">
                    <Download className="w-4 h-4 mr-2" />
                    Descargar Reporte Parcial
                  </Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="historial" className="space-y-6">
            <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-white">
                  <FileText className="w-5 h-5 text-blue-300" />
                  Historial de Ganadores
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {ganadoresAnteriores.map((ganador, index) => (
                    <div key={index} className="flex items-center justify-between p-4 border border-white/20 rounded-lg bg-white/5">
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center">
                          <Trophy className="w-6 h-6 text-yellow-400" />
                        </div>
                        <div>
                          <h4 className="font-semibold text-white">{ganador.ganador}</h4>
                          <p className="text-sm text-blue-200">
                            {ganador.categoria} {ganador.año} • {ganador.organizacion}
                          </p>
                        </div>
                      </div>
                      <Badge variant="secondary" className="bg-white/10 text-white">
                        {ganador.año}
                      </Badge>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </main>
  );
};

export default Premios;