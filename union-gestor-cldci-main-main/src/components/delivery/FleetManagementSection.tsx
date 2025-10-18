import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Plus, Search, Truck, User, Settings, MapPin, Clock } from "lucide-react";
import { CreateVehicleForm } from "./CreateVehicleForm";
import { CreateDriverForm } from "./CreateDriverForm";

interface Vehicle {
  id: string;
  plate: string;
  model: string;
  capacity_weight: number;
  capacity_volume: number;
  driver_name?: string;
  is_active: boolean;
  status: 'available' | 'in_use' | 'maintenance';
}

interface Driver {
  id: string;
  name: string;
  email: string;
  phone: string;
  license_number: string;
  vehicle_plate?: string;
  is_active: boolean;
  status: 'available' | 'on_route' | 'break' | 'offline';
  current_route?: string;
}

const mockVehicles: Vehicle[] = [
  {
    id: "1",
    plate: "ABC-123",
    model: "Ford Transit",
    capacity_weight: 1500,
    capacity_volume: 8.5,
    driver_name: "Carlos Mendoza",
    is_active: true,
    status: "in_use"
  },
  {
    id: "2",
    plate: "DEF-456",
    model: "Mercedes Sprinter",
    capacity_weight: 2000,
    capacity_volume: 12.0,
    driver_name: "Ana Rodriguez",
    is_active: true,
    status: "in_use"
  },
  {
    id: "3",
    plate: "GHI-789",
    model: "Iveco Daily",
    capacity_weight: 3000,
    capacity_volume: 15.0,
    is_active: true,
    status: "available"
  },
  {
    id: "4",
    plate: "JKL-012",
    model: "Volkswagen Crafter",
    capacity_weight: 1800,
    capacity_volume: 10.0,
    is_active: false,
    status: "maintenance"
  }
];

const mockDrivers: Driver[] = [
  {
    id: "1",
    name: "Carlos Mendoza",
    email: "carlos.mendoza@empresa.com",
    phone: "+54 11 1234-5678",
    license_number: "B-12345678",
    vehicle_plate: "ABC-123",
    is_active: true,
    status: "on_route",
    current_route: "Ruta Norte - Mañana"
  },
  {
    id: "2",
    name: "Ana Rodriguez",
    email: "ana.rodriguez@empresa.com",
    phone: "+54 11 2345-6789",
    license_number: "B-23456789",
    vehicle_plate: "DEF-456",
    is_active: true,
    status: "on_route",
    current_route: "Ruta Sur - Tarde"
  },
  {
    id: "3",
    name: "Luis Fernández",
    email: "luis.fernandez@empresa.com",
    phone: "+54 11 3456-7890",
    license_number: "B-34567890",
    is_active: true,
    status: "available"
  },
  {
    id: "4",
    name: "María Silva",
    email: "maria.silva@empresa.com",
    phone: "+54 11 4567-8901",
    license_number: "B-45678901",
    is_active: false,
    status: "offline"
  }
];

const getVehicleStatusBadge = (status: Vehicle['status']) => {
  const configs = {
    available: { variant: "default" as const, label: "Disponible", color: "bg-green-100 text-green-800" },
    in_use: { variant: "default" as const, label: "En Uso", color: "bg-blue-100 text-blue-800" },
    maintenance: { variant: "secondary" as const, label: "Mantenimiento", color: "bg-orange-100 text-orange-800" }
  };
  
  const config = configs[status];
  return <Badge className={config.color}>{config.label}</Badge>;
};

const getDriverStatusBadge = (status: Driver['status']) => {
  const configs = {
    available: { variant: "default" as const, label: "Disponible", color: "bg-green-100 text-green-800" },
    on_route: { variant: "default" as const, label: "En Ruta", color: "bg-blue-100 text-blue-800" },
    break: { variant: "secondary" as const, label: "Descanso", color: "bg-yellow-100 text-yellow-800" },
    offline: { variant: "secondary" as const, label: "Desconectado", color: "bg-gray-100 text-gray-800" }
  };
  
  const config = configs[status];
  return <Badge className={config.color}>{config.label}</Badge>;
};

export function FleetManagementSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [isVehicleDialogOpen, setIsVehicleDialogOpen] = useState(false);
  const [isDriverDialogOpen, setIsDriverDialogOpen] = useState(false);

  const filteredVehicles = mockVehicles.filter(vehicle =>
    vehicle.plate.toLowerCase().includes(searchTerm.toLowerCase()) ||
    vehicle.model.toLowerCase().includes(searchTerm.toLowerCase()) ||
    vehicle.driver_name?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const filteredDrivers = mockDrivers.filter(driver =>
    driver.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    driver.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    driver.vehicle_plate?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      {/* Fleet Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Vehículos Totales</CardTitle>
            <Truck className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">4</div>
            <p className="text-xs text-muted-foreground">3 activos, 1 en mantenimiento</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Conductores</CardTitle>
            <User className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">4</div>
            <p className="text-xs text-muted-foreground">2 en ruta, 1 disponible</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Capacidad Total</CardTitle>
            <Settings className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">8.3t</div>
            <p className="text-xs text-muted-foreground">45.5 m³ de volumen</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Utilización</CardTitle>
            <MapPin className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">75%</div>
            <p className="text-xs text-muted-foreground">3 de 4 vehículos en uso</p>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="vehicles" className="space-y-4">
        <TabsList>
          <TabsTrigger value="vehicles">Vehículos</TabsTrigger>
          <TabsTrigger value="drivers">Conductores</TabsTrigger>
        </TabsList>

        <TabsContent value="vehicles" className="space-y-4">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle className="flex items-center gap-2">
                    <Truck className="h-5 w-5" />
                    Gestión de Vehículos
                  </CardTitle>
                  <CardDescription>
                    Administra la flota de vehículos y su estado
                  </CardDescription>
                </div>
                <Dialog open={isVehicleDialogOpen} onOpenChange={setIsVehicleDialogOpen}>
                  <DialogTrigger asChild>
                    <Button className="flex items-center gap-2">
                      <Plus className="h-4 w-4" />
                      Agregar Vehículo
                    </Button>
                  </DialogTrigger>
                  <DialogContent className="max-w-2xl">
                    <DialogHeader>
                      <DialogTitle>Registrar Nuevo Vehículo</DialogTitle>
                      <DialogDescription>
                        Ingresa los datos del vehículo para agregarlo a la flota
                      </DialogDescription>
                    </DialogHeader>
                    <CreateVehicleForm onSuccess={() => setIsVehicleDialogOpen(false)} />
                  </DialogContent>
                </Dialog>
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  placeholder="Buscar por placa, modelo o conductor..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>

              <div className="rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Placa</TableHead>
                      <TableHead>Modelo</TableHead>
                      <TableHead>Capacidad</TableHead>
                      <TableHead>Conductor</TableHead>
                      <TableHead>Estado</TableHead>
                      <TableHead>Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {filteredVehicles.map((vehicle) => (
                      <TableRow key={vehicle.id}>
                        <TableCell className="font-medium">{vehicle.plate}</TableCell>
                        <TableCell>{vehicle.model}</TableCell>
                        <TableCell>
                          <div className="text-sm">
                            <div>{vehicle.capacity_weight}kg</div>
                            <div className="text-muted-foreground">{vehicle.capacity_volume}m³</div>
                          </div>
                        </TableCell>
                        <TableCell>
                          {vehicle.driver_name ? (
                            <div className="flex items-center gap-1">
                              <User className="h-3 w-3" />
                              {vehicle.driver_name}
                            </div>
                          ) : (
                            <span className="text-muted-foreground">Sin asignar</span>
                          )}
                        </TableCell>
                        <TableCell>{getVehicleStatusBadge(vehicle.status)}</TableCell>
                        <TableCell>
                          <div className="flex gap-2">
                            <Button variant="ghost" size="sm">
                              Editar
                            </Button>
                            <Button variant="ghost" size="sm">
                              <MapPin className="h-3 w-3" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="drivers" className="space-y-4">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle className="flex items-center gap-2">
                    <User className="h-5 w-5" />
                    Gestión de Conductores
                  </CardTitle>
                  <CardDescription>
                    Administra el equipo de conductores
                  </CardDescription>
                </div>
                <Dialog open={isDriverDialogOpen} onOpenChange={setIsDriverDialogOpen}>
                  <DialogTrigger asChild>
                    <Button className="flex items-center gap-2">
                      <Plus className="h-4 w-4" />
                      Agregar Conductor
                    </Button>
                  </DialogTrigger>
                  <DialogContent className="max-w-2xl">
                    <DialogHeader>
                      <DialogTitle>Registrar Nuevo Conductor</DialogTitle>
                      <DialogDescription>
                        Ingresa los datos del conductor para agregarlo al equipo
                      </DialogDescription>
                    </DialogHeader>
                    <CreateDriverForm onSuccess={() => setIsDriverDialogOpen(false)} />
                  </DialogContent>
                </Dialog>
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  placeholder="Buscar por nombre, email o vehículo..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>

              <div className="rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Conductor</TableHead>
                      <TableHead>Contacto</TableHead>
                      <TableHead>Licencia</TableHead>
                      <TableHead>Vehículo</TableHead>
                      <TableHead>Estado</TableHead>
                      <TableHead>Ruta Actual</TableHead>
                      <TableHead>Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {filteredDrivers.map((driver) => (
                      <TableRow key={driver.id}>
                        <TableCell>
                          <div className="flex items-center gap-3">
                            <div className="flex items-center justify-center w-8 h-8 bg-primary text-primary-foreground rounded-full text-sm">
                              {driver.name.split(' ').map(n => n[0]).join('')}
                            </div>
                            <div>
                              <div className="font-medium">{driver.name}</div>
                              <div className="text-sm text-muted-foreground">{driver.email}</div>
                            </div>
                          </div>
                        </TableCell>
                        <TableCell>
                          <div className="text-sm">{driver.phone}</div>
                        </TableCell>
                        <TableCell>
                          <div className="text-sm">{driver.license_number}</div>
                        </TableCell>
                        <TableCell>
                          {driver.vehicle_plate ? (
                            <div className="flex items-center gap-1">
                              <Truck className="h-3 w-3" />
                              {driver.vehicle_plate}
                            </div>
                          ) : (
                            <span className="text-muted-foreground">Sin asignar</span>
                          )}
                        </TableCell>
                        <TableCell>{getDriverStatusBadge(driver.status)}</TableCell>
                        <TableCell>
                          {driver.current_route ? (
                            <div className="flex items-center gap-1 text-sm">
                              <Clock className="h-3 w-3" />
                              {driver.current_route}
                            </div>
                          ) : (
                            <span className="text-muted-foreground">-</span>
                          )}
                        </TableCell>
                        <TableCell>
                          <div className="flex gap-2">
                            <Button variant="ghost" size="sm">
                              Editar
                            </Button>
                            <Button variant="ghost" size="sm">
                              <MapPin className="h-3 w-3" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}