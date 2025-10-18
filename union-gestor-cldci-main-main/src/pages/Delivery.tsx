import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { RouteOptimizerSection } from "@/components/delivery/RouteOptimizerSection";
import { LiveTrackingSection } from "@/components/delivery/LiveTrackingSection";
import { OrderManagementSection } from "@/components/delivery/OrderManagementSection";
import { FleetManagementSection } from "@/components/delivery/FleetManagementSection";
import { AnalyticsDashboard } from "@/components/delivery/AnalyticsDashboard";
import { Truck, MapPin, Package, BarChart3, Users, Route } from "lucide-react";
import { useAuth } from "@/components/auth/AuthProvider";
import { SEO } from "@/components/seo/SEO";

export default function Delivery() {
  const { user } = useAuth();

  return (
    <>
      <SEO 
        title="Gestión de Entregas - Sistema de Reparto Última Milla"
        description="Plataforma completa para gestión de entregas de última milla con optimización de rutas, seguimiento en tiempo real y análisis de rendimiento."
      />
      
      <div className="container mx-auto py-6 space-y-6">
        <div className="flex items-center space-x-2 mb-6">
          <Truck className="h-8 w-8 text-primary" />
          <div>
            <h1 className="text-3xl font-bold">Gestión de Entregas</h1>
            <p className="text-muted-foreground">
              Sistema integral de gestión de entregas de última milla
            </p>
          </div>
        </div>

        <Tabs defaultValue="orders" className="space-y-4">
          <TabsList className="grid w-full grid-cols-6">
            <TabsTrigger value="orders" className="flex items-center gap-2">
              <Package className="h-4 w-4" />
              Pedidos
            </TabsTrigger>
            <TabsTrigger value="routes" className="flex items-center gap-2">
              <Route className="h-4 w-4" />
              Rutas
            </TabsTrigger>
            <TabsTrigger value="tracking" className="flex items-center gap-2">
              <MapPin className="h-4 w-4" />
              Seguimiento
            </TabsTrigger>
            <TabsTrigger value="fleet" className="flex items-center gap-2">
              <Truck className="h-4 w-4" />
              Flota
            </TabsTrigger>
            <TabsTrigger value="drivers" className="flex items-center gap-2">
              <Users className="h-4 w-4" />
              Conductores
            </TabsTrigger>
            <TabsTrigger value="analytics" className="flex items-center gap-2">
              <BarChart3 className="h-4 w-4" />
              Análisis
            </TabsTrigger>
          </TabsList>

          <TabsContent value="orders" className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Pedidos Pendientes</CardTitle>
                  <Package className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">24</div>
                  <p className="text-xs text-muted-foreground">+2 desde ayer</p>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">En Tránsito</CardTitle>
                  <Truck className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">12</div>
                  <p className="text-xs text-muted-foreground">4 rutas activas</p>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Entregados Hoy</CardTitle>
                  <MapPin className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">45</div>
                  <p className="text-xs text-muted-foreground">95% éxito</p>
                </CardContent>
              </Card>
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">Incidencias</CardTitle>
                  <BarChart3 className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">3</div>
                  <p className="text-xs text-muted-foreground">2 resueltas</p>
                </CardContent>
              </Card>
            </div>
            <OrderManagementSection />
          </TabsContent>

          <TabsContent value="routes" className="space-y-4">
            <RouteOptimizerSection />
          </TabsContent>

          <TabsContent value="tracking" className="space-y-4">
            <LiveTrackingSection />
          </TabsContent>

          <TabsContent value="fleet" className="space-y-4">
            <FleetManagementSection />
          </TabsContent>

          <TabsContent value="drivers" className="space-y-4">
            <div className="grid gap-4">
              <Card>
                <CardHeader>
                  <CardTitle>Gestión de Conductores</CardTitle>
                  <CardDescription>
                    Administra tu equipo de conductores y su disponibilidad
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  {/* Driver management component will go here */}
                  <div className="text-center py-8 text-muted-foreground">
                    Gestión de conductores en desarrollo
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="analytics" className="space-y-4">
            <AnalyticsDashboard />
          </TabsContent>
        </Tabs>
      </div>
    </>
  );
}