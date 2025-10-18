import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Route, Zap, MapPin, Clock, Package, Truck, Settings } from "lucide-react";
import { toast } from "sonner";

interface RouteStop {
  id: string;
  order_number: string;
  customer_name: string;
  address: string;
  estimated_time: string;
  priority: 'low' | 'normal' | 'high' | 'urgent';
  status: 'pending' | 'completed';
}

interface OptimizedRoute {
  id: string;
  name: string;
  driver: string;
  vehicle: string;
  stops: RouteStop[];
  total_distance: number;
  estimated_duration: number;
  status: 'planned' | 'active' | 'completed';
}

const mockRoutes: OptimizedRoute[] = [
  {
    id: "1",
    name: "Ruta Norte - Mañana",
    driver: "Carlos Mendoza",
    vehicle: "ABC-123",
    stops: [
      {
        id: "1",
        order_number: "ORD-001",
        customer_name: "María García",
        address: "Av. Libertador 1234, CABA",
        estimated_time: "09:30",
        priority: "high",
        status: "pending"
      },
      {
        id: "2",
        order_number: "ORD-005",
        customer_name: "Juan Pérez",
        address: "Santa Fe 567, CABA",
        estimated_time: "10:15",
        priority: "normal",
        status: "pending"
      },
      {
        id: "3",
        order_number: "ORD-008",
        customer_name: "Laura Martinez",
        address: "Palermo 890, CABA",
        estimated_time: "11:00",
        priority: "urgent",
        status: "pending"
      }
    ],
    total_distance: 45.2,
    estimated_duration: 180,
    status: "planned"
  },
  {
    id: "2",
    name: "Ruta Sur - Tarde",
    driver: "Ana Rodriguez",
    vehicle: "DEF-456",
    stops: [
      {
        id: "4",
        order_number: "ORD-003",
        customer_name: "Roberto Silva",
        address: "San Telmo 123, CABA",
        estimated_time: "14:00",
        priority: "normal",
        status: "pending"
      },
      {
        id: "5",
        order_number: "ORD-007",
        customer_name: "Carmen López",
        address: "La Boca 456, CABA",
        estimated_time: "15:30",
        priority: "high",
        status: "pending"
      }
    ],
    total_distance: 32.8,
    estimated_duration: 150,
    status: "planned"
  }
];

const mockUnassignedOrders = [
  { id: "6", order_number: "ORD-009", customer: "Diego Fernández", address: "Belgrano 789", priority: "normal" },
  { id: "7", order_number: "ORD-010", customer: "Sofia Ruiz", address: "Recoleta 321", priority: "high" },
  { id: "8", order_number: "ORD-011", customer: "Miguel Torres", address: "Villa Crespo 654", priority: "urgent" }
];

const getPriorityColor = (priority: string) => {
  const colors = {
    low: "bg-blue-100 text-blue-800",
    normal: "bg-gray-100 text-gray-800",
    high: "bg-orange-100 text-orange-800",
    urgent: "bg-red-100 text-red-800"
  };
  return colors[priority as keyof typeof colors] || colors.normal;
};

export function RouteOptimizerSection() {
  const [routes, setRoutes] = useState<OptimizedRoute[]>(mockRoutes);
  const [isOptimizing, setIsOptimizing] = useState(false);

  const handleOptimizeRoutes = async () => {
    setIsOptimizing(true);
    try {
      // Simulate route optimization
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      toast.success("Rutas optimizadas exitosamente");
      toast.info("Se crearon 2 rutas optimizadas con un ahorro estimado del 15% en tiempo y distancia");
    } catch (error) {
      toast.error("Error al optimizar rutas");
    } finally {
      setIsOptimizing(false);
    }
  };

  const handlePublishRoutes = () => {
    toast.success("Rutas enviadas a los conductores");
    toast.info("Los conductores recibirán una notificación con sus rutas asignadas");
  };

  return (
    <div className="space-y-6">
      {/* Header with optimization controls */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle className="flex items-center gap-2">
                <Route className="h-5 w-5" />
                Optimización de Rutas
              </CardTitle>
              <CardDescription>
                Planifica y optimiza las rutas de entrega para maximizar la eficiencia
              </CardDescription>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" className="flex items-center gap-2">
                <Settings className="h-4 w-4" />
                Configuración
              </Button>
              <Button 
                onClick={handleOptimizeRoutes}
                disabled={isOptimizing}
                className="flex items-center gap-2"
              >
                <Zap className="h-4 w-4" />
                {isOptimizing ? "Optimizando..." : "Optimizar Rutas"}
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="text-center p-4 bg-muted/50 rounded-lg">
              <div className="text-2xl font-bold text-primary">24</div>
              <div className="text-sm text-muted-foreground">Pedidos Pendientes</div>
            </div>
            <div className="text-center p-4 bg-muted/50 rounded-lg">
              <div className="text-2xl font-bold text-green-600">2</div>
              <div className="text-sm text-muted-foreground">Rutas Planificadas</div>
            </div>
            <div className="text-center p-4 bg-muted/50 rounded-lg">
              <div className="text-2xl font-bold text-blue-600">78.0 km</div>
              <div className="text-sm text-muted-foreground">Distancia Total</div>
            </div>
            <div className="text-center p-4 bg-muted/50 rounded-lg">
              <div className="text-2xl font-bold text-orange-600">5.5h</div>
              <div className="text-sm text-muted-foreground">Tiempo Estimado</div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Tabs defaultValue="routes" className="space-y-4">
        <TabsList>
          <TabsTrigger value="routes">Rutas Optimizadas</TabsTrigger>
          <TabsTrigger value="unassigned">Pedidos Sin Asignar</TabsTrigger>
        </TabsList>

        <TabsContent value="routes" className="space-y-4">
          {routes.map((route, index) => (
            <Card key={route.id}>
              <CardHeader className="pb-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="flex items-center justify-center w-8 h-8 bg-primary text-primary-foreground rounded-full text-sm font-bold">
                      {index + 1}
                    </div>
                    <div>
                      <CardTitle className="text-lg">{route.name}</CardTitle>
                      <CardDescription className="flex items-center gap-4">
                        <span className="flex items-center gap-1">
                          <Truck className="h-3 w-3" />
                          {route.driver} • {route.vehicle}
                        </span>
                        <span className="flex items-center gap-1">
                          <MapPin className="h-3 w-3" />
                          {route.total_distance} km
                        </span>
                        <span className="flex items-center gap-1">
                          <Clock className="h-3 w-3" />
                          {Math.floor(route.estimated_duration / 60)}h {route.estimated_duration % 60}m
                        </span>
                      </CardDescription>
                    </div>
                  </div>
                  <div className="flex gap-2">
                    <Badge variant={route.status === 'planned' ? 'secondary' : 'default'}>
                      {route.status === 'planned' ? 'Planificada' : 'Activa'}
                    </Badge>
                    <Button size="sm" variant="outline">
                      Ver en Mapa
                    </Button>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  {route.stops.map((stop, stopIndex) => (
                    <div key={stop.id} className="flex items-center gap-3 p-3 bg-muted/30 rounded-lg">
                      <div className="flex items-center justify-center w-6 h-6 bg-background border-2 border-primary text-primary rounded-full text-xs font-bold">
                        {stopIndex + 1}
                      </div>
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <span className="font-medium">{stop.order_number}</span>
                          <Badge className={getPriorityColor(stop.priority)} variant="secondary">
                            {stop.priority === 'urgent' ? 'Urgente' : 
                             stop.priority === 'high' ? 'Alta' :
                             stop.priority === 'normal' ? 'Normal' : 'Baja'}
                          </Badge>
                        </div>
                        <div className="text-sm text-muted-foreground">
                          {stop.customer_name} • {stop.address}
                        </div>
                      </div>
                      <div className="text-right">
                        <div className="flex items-center gap-1 text-sm font-medium">
                          <Clock className="h-3 w-3" />
                          {stop.estimated_time}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          ))}

          {routes.length > 0 && (
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">¿Confirmar rutas optimizadas?</h3>
                    <p className="text-sm text-muted-foreground">
                      Las rutas se enviarán a los conductores y comenzará el seguimiento en tiempo real
                    </p>
                  </div>
                  <Button onClick={handlePublishRoutes} className="flex items-center gap-2">
                    <Package className="h-4 w-4" />
                    Enviar a Conductores
                  </Button>
                </div>
              </CardContent>
            </Card>
          )}
        </TabsContent>

        <TabsContent value="unassigned" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Pedidos Sin Asignar</CardTitle>
              <CardDescription>
                Estos pedidos están pendientes de ser incluidos en una ruta
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {mockUnassignedOrders.map((order) => (
                  <div key={order.id} className="flex items-center gap-3 p-3 border rounded-lg">
                    <Package className="h-4 w-4 text-muted-foreground" />
                    <div className="flex-1">
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{order.order_number}</span>
                        <Badge className={getPriorityColor(order.priority)} variant="secondary">
                          {order.priority === 'urgent' ? 'Urgente' : 
                           order.priority === 'high' ? 'Alta' : 'Normal'}
                        </Badge>
                      </div>
                      <div className="text-sm text-muted-foreground">
                        {order.customer} • {order.address}
                      </div>
                    </div>
                    <Button size="sm" variant="outline">
                      Asignar a Ruta
                    </Button>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}