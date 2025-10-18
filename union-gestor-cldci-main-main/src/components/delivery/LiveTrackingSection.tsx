import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { MapPin, Navigation, AlertCircle, MessageSquare, Phone, Search, Filter, Truck, Clock } from "lucide-react";

interface DeliveryUpdate {
  id: string;
  order_number: string;
  customer_name: string;
  address: string;
  driver_name: string;
  status: 'pending' | 'in_progress' | 'delivered' | 'failed';
  last_update: string;
  estimated_arrival: string;
  lat?: number;
  lng?: number;
  notes?: string;
}

const mockDeliveries: DeliveryUpdate[] = [
  {
    id: "1",
    order_number: "ORD-001",
    customer_name: "María García",
    address: "Av. Libertador 1234, CABA",
    driver_name: "Carlos Mendoza",
    status: "in_progress",
    last_update: "10:45",
    estimated_arrival: "11:15",
    lat: -34.5875,
    lng: -58.3974,
    notes: "En camino, sin incidencias"
  },
  {
    id: "2",
    order_number: "ORD-005",
    customer_name: "Juan Pérez",
    address: "Santa Fe 567, CABA",
    driver_name: "Carlos Mendoza",
    status: "pending",
    last_update: "10:30",
    estimated_arrival: "11:45"
  },
  {
    id: "3",
    order_number: "ORD-003",
    customer_name: "Roberto Silva",
    address: "San Telmo 123, CABA",
    driver_name: "Ana Rodriguez",
    status: "delivered",
    last_update: "09:30",
    estimated_arrival: "09:30",
    notes: "Entregado exitosamente"
  },
  {
    id: "4",
    order_number: "ORD-007",
    customer_name: "Carmen López",
    address: "La Boca 456, CABA",
    driver_name: "Ana Rodriguez",
    status: "failed",
    last_update: "14:20",
    estimated_arrival: "14:00",
    notes: "Cliente ausente, reprogramar entrega"
  }
];

const mockDrivers = [
  { id: "1", name: "Carlos Mendoza", vehicle: "ABC-123", status: "active", lat: -34.5875, lng: -58.3974, route: "Ruta Norte" },
  { id: "2", name: "Ana Rodriguez", vehicle: "DEF-456", status: "active", lat: -34.6118, lng: -58.3960, route: "Ruta Sur" },
  { id: "3", name: "Luis Fernández", vehicle: "GHI-789", status: "break", lat: -34.6037, lng: -58.3816, route: "Ruta Centro" }
];

const getStatusBadge = (status: string) => {
  const configs = {
    pending: { variant: "secondary" as const, label: "Pendiente", color: "bg-gray-100 text-gray-800" },
    in_progress: { variant: "default" as const, label: "En Progreso", color: "bg-blue-100 text-blue-800" },
    delivered: { variant: "default" as const, label: "Entregado", color: "bg-green-100 text-green-800" },
    failed: { variant: "destructive" as const, label: "Fallido", color: "bg-red-100 text-red-800" }
  };
  
  const config = configs[status as keyof typeof configs] || configs.pending;
  return <Badge className={config.color}>{config.label}</Badge>;
};

export function LiveTrackingSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState<string>("all");
  const [selectedDelivery, setSelectedDelivery] = useState<DeliveryUpdate | null>(null);

  const filteredDeliveries = mockDeliveries.filter(delivery => {
    const matchesSearch = 
      delivery.order_number.toLowerCase().includes(searchTerm.toLowerCase()) ||
      delivery.customer_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      delivery.driver_name.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === "all" || delivery.status === statusFilter;
    
    return matchesSearch && matchesStatus;
  });

  return (
    <div className="space-y-6">
      {/* Real-time statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Conductores Activos</CardTitle>
            <Truck className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">3</div>
            <p className="text-xs text-muted-foreground">2 en ruta, 1 en descanso</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">En Tránsito</CardTitle>
            <Navigation className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">12</div>
            <p className="text-xs text-muted-foreground">En 3 rutas activas</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Entregas Hoy</CardTitle>
            <MapPin className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">45</div>
            <p className="text-xs text-muted-foreground">95% completadas</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Incidencias</CardTitle>
            <AlertCircle className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">2</div>
            <p className="text-xs text-muted-foreground">Requieren atención</p>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="deliveries" className="space-y-4">
        <TabsList>
          <TabsTrigger value="deliveries">Entregas en Tiempo Real</TabsTrigger>
          <TabsTrigger value="drivers">Conductores</TabsTrigger>
          <TabsTrigger value="map">Mapa en Vivo</TabsTrigger>
        </TabsList>

        <TabsContent value="deliveries" className="space-y-4">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle>Seguimiento de Entregas</CardTitle>
                  <CardDescription>
                    Monitor en tiempo real del estado de todas las entregas
                  </CardDescription>
                </div>
                <div className="flex gap-2">
                  <Button variant="outline" size="sm">
                    <AlertCircle className="h-4 w-4 mr-2" />
                    Ver Incidencias
                  </Button>
                </div>
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              {/* Filters */}
              <div className="flex items-center gap-4">
                <div className="relative flex-1">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <Input
                    placeholder="Buscar por pedido, cliente o conductor..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10"
                  />
                </div>
                <Select value={statusFilter} onValueChange={setStatusFilter}>
                  <SelectTrigger className="w-40">
                    <SelectValue placeholder="Estado" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Todos</SelectItem>
                    <SelectItem value="pending">Pendiente</SelectItem>
                    <SelectItem value="in_progress">En Progreso</SelectItem>
                    <SelectItem value="delivered">Entregado</SelectItem>
                    <SelectItem value="failed">Fallido</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {/* Deliveries list */}
              <div className="space-y-3">
                {filteredDeliveries.map((delivery) => (
                  <div 
                    key={delivery.id} 
                    className="flex items-center gap-4 p-4 border rounded-lg hover:bg-muted/50 cursor-pointer transition-colors"
                    onClick={() => setSelectedDelivery(delivery)}
                  >
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="font-medium">{delivery.order_number}</span>
                        {getStatusBadge(delivery.status)}
                        {delivery.status === 'failed' && (
                          <AlertCircle className="h-4 w-4 text-destructive" />
                        )}
                      </div>
                      <div className="text-sm text-muted-foreground">
                        <div>{delivery.customer_name} • {delivery.address}</div>
                        <div className="flex items-center gap-4 mt-1">
                          <span className="flex items-center gap-1">
                            <Truck className="h-3 w-3" />
                            {delivery.driver_name}
                          </span>
                          <span className="flex items-center gap-1">
                            <Clock className="h-3 w-3" />
                            ETA: {delivery.estimated_arrival}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div className="text-right space-y-1">
                      <div className="text-sm text-muted-foreground">
                        Última actualización: {delivery.last_update}
                      </div>
                      <div className="flex gap-1">
                        <Button size="sm" variant="outline">
                          <MapPin className="h-3 w-3 mr-1" />
                          Ubicar
                        </Button>
                        <Button size="sm" variant="outline">
                          <MessageSquare className="h-3 w-3 mr-1" />
                          Mensaje
                        </Button>
                        <Button size="sm" variant="outline">
                          <Phone className="h-3 w-3 mr-1" />
                          Llamar
                        </Button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="drivers" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Conductores en Tiempo Real</CardTitle>
              <CardDescription>
                Estado actual de todos los conductores
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {mockDrivers.map((driver) => (
                  <div key={driver.id} className="flex items-center gap-4 p-4 border rounded-lg">
                    <div className="flex items-center justify-center w-10 h-10 bg-primary text-primary-foreground rounded-full">
                      {driver.name.split(' ').map(n => n[0]).join('')}
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{driver.name}</span>
                        <Badge variant={driver.status === 'active' ? 'default' : 'secondary'}>
                          {driver.status === 'active' ? 'Activo' : 'Descanso'}
                        </Badge>
                      </div>
                      <div className="text-sm text-muted-foreground">
                        {driver.vehicle} • {driver.route}
                      </div>
                    </div>
                    <div className="flex gap-2">
                      <Button size="sm" variant="outline">
                        <MapPin className="h-3 w-3 mr-1" />
                        Ubicar
                      </Button>
                      <Button size="sm" variant="outline">
                        <MessageSquare className="h-3 w-3 mr-1" />
                        Mensaje
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="map" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Mapa en Tiempo Real</CardTitle>
              <CardDescription>
                Visualización en vivo de todos los conductores y entregas
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="bg-muted/30 rounded-lg flex items-center justify-center h-96">
                <div className="text-center">
                  <MapPin className="h-12 w-12 text-muted-foreground mx-auto mb-2" />
                  <p className="text-muted-foreground">
                    Mapa interactivo en desarrollo
                  </p>
                  <p className="text-sm text-muted-foreground mt-1">
                    Aquí se mostrará la ubicación en tiempo real de conductores y rutas
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}