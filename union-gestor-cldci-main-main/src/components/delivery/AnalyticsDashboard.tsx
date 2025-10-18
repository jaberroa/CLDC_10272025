import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { BarChart3, TrendingUp, Clock, MapPin, Star, Download, Calendar } from "lucide-react";

interface KPICard {
  title: string;
  value: string;
  change: string;
  trend: 'up' | 'down' | 'stable';
  icon: React.ReactNode;
}

const kpiData: KPICard[] = [
  {
    title: "Entregas Completadas",
    value: "94.5%",
    change: "+2.1%",
    trend: "up",
    icon: <MapPin className="h-4 w-4" />
  },
  {
    title: "Tiempo Promedio",
    value: "23 min",
    change: "-5 min",
    trend: "up",
    icon: <Clock className="h-4 w-4" />
  },
  {
    title: "Distancia Optimizada",
    value: "847 km",
    change: "-12%",
    trend: "up",
    icon: <BarChart3 className="h-4 w-4" />
  },
  {
    title: "Satisfacción Cliente",
    value: "4.7/5",
    change: "+0.2",
    trend: "up",
    icon: <Star className="h-4 w-4" />
  }
];

const mockDeliveryData = [
  { route: "Ruta Norte", driver: "Carlos Mendoza", deliveries: 12, completed: 11, failed: 1, avgTime: "22 min", distance: "45.2 km" },
  { route: "Ruta Sur", driver: "Ana Rodriguez", deliveries: 8, completed: 8, failed: 0, avgTime: "25 min", distance: "32.8 km" },
  { route: "Ruta Centro", driver: "Luis Fernández", deliveries: 15, completed: 14, failed: 1, avgTime: "19 min", distance: "28.5 km" }
];

const mockIncidentData = [
  { reason: "Cliente ausente", count: 8, percentage: 45 },
  { reason: "Dirección incorrecta", count: 5, percentage: 28 },
  { reason: "Producto dañado", count: 3, percentage: 17 },
  { reason: "Otros", count: 2, percentage: 10 }
];

export function AnalyticsDashboard() {
  return (
    <div className="space-y-6">
      {/* Header with filters */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold">Análisis y Reportes</h2>
          <p className="text-muted-foreground">
            Métricas de rendimiento y análisis detallado de operaciones
          </p>
        </div>
        <div className="flex items-center gap-4">
          <Select defaultValue="7d">
            <SelectTrigger className="w-40">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="1d">Hoy</SelectItem>
              <SelectItem value="7d">Últimos 7 días</SelectItem>
              <SelectItem value="30d">Último mes</SelectItem>
              <SelectItem value="custom">Personalizado</SelectItem>
            </SelectContent>
          </Select>
          <Button variant="outline" className="flex items-center gap-2">
            <Download className="h-4 w-4" />
            Exportar
          </Button>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {kpiData.map((kpi, index) => (
          <Card key={index}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">{kpi.title}</CardTitle>
              {kpi.icon}
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{kpi.value}</div>
              <div className="flex items-center gap-1">
                <TrendingUp className={`h-3 w-3 ${kpi.trend === 'up' ? 'text-green-600' : 'text-red-600'}`} />
                <span className={`text-xs ${kpi.trend === 'up' ? 'text-green-600' : 'text-red-600'}`}>
                  {kpi.change} desde ayer
                </span>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <Tabs defaultValue="performance" className="space-y-4">
        <TabsList>
          <TabsTrigger value="performance">Rendimiento</TabsTrigger>
          <TabsTrigger value="routes">Rutas</TabsTrigger>
          <TabsTrigger value="incidents">Incidencias</TabsTrigger>
          <TabsTrigger value="customers">Satisfacción</TabsTrigger>
        </TabsList>

        <TabsContent value="performance" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Entregas por Día</CardTitle>
                <CardDescription>
                  Evolución de entregas completadas en los últimos 7 días
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="h-64 flex items-center justify-center bg-muted/30 rounded">
                  <div className="text-center">
                    <BarChart3 className="h-8 w-8 text-muted-foreground mx-auto mb-2" />
                    <p className="text-muted-foreground">Gráfico de entregas por día</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Tiempos de Entrega</CardTitle>
                <CardDescription>
                  Distribución de tiempos de entrega promedio
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="h-64 flex items-center justify-center bg-muted/30 rounded">
                  <div className="text-center">
                    <Clock className="h-8 w-8 text-muted-foreground mx-auto mb-2" />
                    <p className="text-muted-foreground">Gráfico de tiempos de entrega</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="routes" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Rendimiento por Ruta</CardTitle>
              <CardDescription>
                Análisis detallado del desempeño de cada ruta
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {mockDeliveryData.map((route, index) => (
                  <div key={index} className="flex items-center gap-4 p-4 border rounded-lg">
                    <div className="flex items-center justify-center w-10 h-10 bg-primary text-primary-foreground rounded-full">
                      {index + 1}
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="font-medium">{route.route}</span>
                        <Badge variant="outline">{route.driver}</Badge>
                      </div>
                      <div className="text-sm text-muted-foreground">
                        {route.deliveries} entregas • {route.distance} • {route.avgTime} promedio
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-sm">
                        <span className="text-green-600 font-medium">{route.completed} completadas</span>
                        {route.failed > 0 && (
                          <span className="text-red-600 ml-2">{route.failed} fallidas</span>
                        )}
                      </div>
                      <div className="text-xs text-muted-foreground">
                        {Math.round((route.completed / route.deliveries) * 100)}% éxito
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="incidents" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Motivos de Incidencias</CardTitle>
                <CardDescription>
                  Principales razones de entregas fallidas
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  {mockIncidentData.map((incident, index) => (
                    <div key={index} className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <div className="w-3 h-3 bg-primary rounded-full" />
                        <span className="text-sm">{incident.reason}</span>
                      </div>
                      <div className="text-right">
                        <div className="text-sm font-medium">{incident.count}</div>
                        <div className="text-xs text-muted-foreground">{incident.percentage}%</div>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Tendencia de Incidencias</CardTitle>
                <CardDescription>
                  Evolución de incidencias en los últimos 30 días
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="h-48 flex items-center justify-center bg-muted/30 rounded">
                  <div className="text-center">
                    <TrendingUp className="h-8 w-8 text-muted-foreground mx-auto mb-2" />
                    <p className="text-muted-foreground">Gráfico de tendencia de incidencias</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="customers" className="space-y-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Satisfacción del Cliente</CardTitle>
                <CardDescription>
                  Calificaciones promedio de los últimos 30 días
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <span>Calificación promedio</span>
                    <div className="flex items-center gap-1">
                      <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                      <span className="font-medium">4.7/5</span>
                    </div>
                  </div>
                  <div className="space-y-2">
                    {[5, 4, 3, 2, 1].map((stars) => (
                      <div key={stars} className="flex items-center gap-2">
                        <span className="text-sm w-8">{stars}★</span>
                        <div className="flex-1 bg-muted rounded-full h-2">
                          <div 
                            className="bg-primary h-2 rounded-full" 
                            style={{ width: `${stars === 5 ? 75 : stars === 4 ? 20 : 3}%` }}
                          />
                        </div>
                        <span className="text-sm text-muted-foreground w-8">
                          {stars === 5 ? '75%' : stars === 4 ? '20%' : '3%'}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Comentarios Recientes</CardTitle>
                <CardDescription>
                  Últimos comentarios de clientes
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="border-l-2 border-primary pl-4">
                    <div className="flex items-center gap-2 mb-1">
                      <div className="flex">
                        {[1, 2, 3, 4, 5].map((star) => (
                          <Star key={star} className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                        ))}
                      </div>
                      <span className="text-xs text-muted-foreground">hace 2 horas</span>
                    </div>
                    <p className="text-sm">"Excelente servicio, llegó a tiempo y el conductor muy amable."</p>
                    <p className="text-xs text-muted-foreground mt-1">- María García</p>
                  </div>

                  <div className="border-l-2 border-primary pl-4">
                    <div className="flex items-center gap-2 mb-1">
                      <div className="flex">
                        {[1, 2, 3, 4].map((star) => (
                          <Star key={star} className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                        ))}
                        <Star className="h-3 w-3 text-gray-300" />
                      </div>
                      <span className="text-xs text-muted-foreground">hace 5 horas</span>
                    </div>
                    <p className="text-sm">"Bueno en general, aunque llegó 10 minutos tarde."</p>
                    <p className="text-xs text-muted-foreground mt-1">- Carlos López</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}