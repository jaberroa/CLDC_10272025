import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus, Upload, Search, Filter, Clock, MapPin, Package } from "lucide-react";
import { CreateOrderForm } from "./CreateOrderForm";

interface Order {
  id: string;
  order_number: string;
  customer_name: string;
  delivery_address: string;
  status: 'pending' | 'assigned' | 'in_transit' | 'delivered' | 'failed';
  priority: 'low' | 'normal' | 'high' | 'urgent';
  preferred_time_start?: string;
  preferred_time_end?: string;
  weight?: number;
  notes?: string;
}

const mockOrders: Order[] = [
  {
    id: "1",
    order_number: "ORD-001",
    customer_name: "María García",
    delivery_address: "Av. Libertador 1234, CABA",
    status: "pending",
    priority: "high",
    preferred_time_start: "09:00",
    preferred_time_end: "12:00",
    weight: 2.5,
    notes: "Llamar antes de llegar"
  },
  {
    id: "2",
    order_number: "ORD-002",
    customer_name: "Carlos Rodriguez",
    delivery_address: "San Martín 567, La Plata",
    status: "in_transit",
    priority: "normal",
    weight: 1.2
  },
  {
    id: "3",
    order_number: "ORD-003",
    customer_name: "Ana López",
    delivery_address: "Corrientes 890, CABA",
    status: "delivered",
    priority: "urgent",
    preferred_time_start: "14:00",
    preferred_time_end: "18:00",
    weight: 3.1
  }
];

const getStatusBadge = (status: Order['status']) => {
  const variants = {
    pending: { variant: "secondary" as const, label: "Pendiente" },
    assigned: { variant: "outline" as const, label: "Asignado" },
    in_transit: { variant: "default" as const, label: "En Tránsito" },
    delivered: { variant: "default" as const, label: "Entregado" },
    failed: { variant: "destructive" as const, label: "Fallido" }
  };
  
  const config = variants[status];
  return <Badge variant={config.variant}>{config.label}</Badge>;
};

const getPriorityBadge = (priority: Order['priority']) => {
  const colors = {
    low: "bg-blue-100 text-blue-800",
    normal: "bg-gray-100 text-gray-800", 
    high: "bg-orange-100 text-orange-800",
    urgent: "bg-red-100 text-red-800"
  };
  
  const labels = {
    low: "Baja",
    normal: "Normal",
    high: "Alta", 
    urgent: "Urgente"
  };
  
  return (
    <Badge className={colors[priority]}>
      {labels[priority]}
    </Badge>
  );
};

export function OrderManagementSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);

  const filteredOrders = mockOrders.filter(order =>
    order.order_number.toLowerCase().includes(searchTerm.toLowerCase()) ||
    order.customer_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    order.delivery_address.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center justify-between">
          <div>
            <CardTitle className="flex items-center gap-2">
              <Package className="h-5 w-5" />
              Gestión de Pedidos
            </CardTitle>
            <CardDescription>
              Administra y rastrea todos los pedidos de entrega
            </CardDescription>
          </div>
          <div className="flex gap-2">
            <Button variant="outline" className="flex items-center gap-2">
              <Upload className="h-4 w-4" />
              Importar CSV
            </Button>
            <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
              <DialogTrigger asChild>
                <Button className="flex items-center gap-2">
                  <Plus className="h-4 w-4" />
                  Nuevo Pedido
                </Button>
              </DialogTrigger>
              <DialogContent className="max-w-3xl">
                <DialogHeader>
                  <DialogTitle>Crear Nuevo Pedido</DialogTitle>
                  <DialogDescription>
                    Ingresa los detalles del pedido para programar la entrega
                  </DialogDescription>
                </DialogHeader>
                <CreateOrderForm onSuccess={() => setIsCreateDialogOpen(false)} />
              </DialogContent>
            </Dialog>
          </div>
        </div>
      </CardHeader>
      <CardContent className="space-y-4">
        <div className="flex items-center gap-4">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Buscar por número de pedido, cliente o dirección..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10"
            />
          </div>
          <Button variant="outline" className="flex items-center gap-2">
            <Filter className="h-4 w-4" />
            Filtros
          </Button>
        </div>

        <div className="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Pedido</TableHead>
                <TableHead>Cliente</TableHead>
                <TableHead>Dirección</TableHead>
                <TableHead>Estado</TableHead>
                <TableHead>Prioridad</TableHead>
                <TableHead>Ventana Horaria</TableHead>
                <TableHead>Peso (kg)</TableHead>
                <TableHead>Acciones</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredOrders.map((order) => (
                <TableRow key={order.id}>
                  <TableCell className="font-medium">{order.order_number}</TableCell>
                  <TableCell>{order.customer_name}</TableCell>
                  <TableCell className="max-w-xs truncate">
                    <div className="flex items-center gap-1">
                      <MapPin className="h-3 w-3 text-muted-foreground" />
                      {order.delivery_address}
                    </div>
                  </TableCell>
                  <TableCell>{getStatusBadge(order.status)}</TableCell>
                  <TableCell>{getPriorityBadge(order.priority)}</TableCell>
                  <TableCell>
                    {order.preferred_time_start && order.preferred_time_end ? (
                      <div className="flex items-center gap-1 text-sm">
                        <Clock className="h-3 w-3" />
                        {order.preferred_time_start} - {order.preferred_time_end}
                      </div>
                    ) : (
                      <span className="text-muted-foreground">Flexible</span>
                    )}
                  </TableCell>
                  <TableCell>{order.weight || '-'}</TableCell>
                  <TableCell>
                    <Button variant="ghost" size="sm">
                      Ver Detalles
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>
  );
}