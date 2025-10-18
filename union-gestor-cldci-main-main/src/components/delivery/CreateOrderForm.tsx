import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Switch } from "@/components/ui/switch";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { MapPin, Package, Clock, User } from "lucide-react";
import { toast } from "sonner";

const orderSchema = z.object({
  customer_name: z.string().min(2, "El nombre del cliente es requerido"),
  customer_email: z.string().email("Email inválido").optional().or(z.literal("")),
  customer_phone: z.string().min(8, "Teléfono requerido"),
  pickup_address: z.string().optional(),
  delivery_address: z.string().min(5, "Dirección de entrega es requerida"),
  preferred_time_start: z.string().optional(),
  preferred_time_end: z.string().optional(),
  priority: z.enum(["low", "normal", "high", "urgent"]),
  weight: z.coerce.number().min(0).optional(),
  volume: z.coerce.number().min(0).optional(),
  requires_signature: z.boolean(),
  requires_pin: z.boolean(),
  pin_code: z.string().optional(),
  notes: z.string().optional(),
  delivery_instructions: z.string().optional()
});

type OrderFormData = z.infer<typeof orderSchema>;

interface CreateOrderFormProps {
  onSuccess: () => void;
}

export function CreateOrderForm({ onSuccess }: CreateOrderFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const form = useForm<OrderFormData>({
    resolver: zodResolver(orderSchema),
    defaultValues: {
      priority: "normal",
      requires_signature: false,
      requires_pin: false
    }
  });

  const watchRequiresPin = form.watch("requires_pin");

  const onSubmit = async (data: OrderFormData) => {
    setIsSubmitting(true);
    try {
      // TODO: Implement order creation with Supabase
      console.log("Creating order:", data);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      toast.success("Pedido creado exitosamente");
      onSuccess();
    } catch (error) {
      toast.error("Error al crear el pedido");
      console.error("Error creating order:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Customer Information */}
          <Card>
            <CardHeader className="pb-3">
              <CardTitle className="flex items-center gap-2 text-base">
                <User className="h-4 w-4" />
                Información del Cliente
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="customer_name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nombre Completo</FormLabel>
                    <FormControl>
                      <Input placeholder="Ej: María García" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="customer_email"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Email (opcional)</FormLabel>
                    <FormControl>
                      <Input placeholder="cliente@email.com" type="email" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="customer_phone"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Teléfono</FormLabel>
                    <FormControl>
                      <Input placeholder="+54 11 1234-5678" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </CardContent>
          </Card>

          {/* Delivery Information */}
          <Card>
            <CardHeader className="pb-3">
              <CardTitle className="flex items-center gap-2 text-base">
                <MapPin className="h-4 w-4" />
                Información de Entrega
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={form.control}
                name="pickup_address"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Dirección de Recogida (opcional)</FormLabel>
                    <FormControl>
                      <Input placeholder="Dirección donde recoger el paquete" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="delivery_address"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Dirección de Entrega</FormLabel>
                    <FormControl>
                      <Input placeholder="Dirección completa de entrega" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="preferred_time_start"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Hora Inicio</FormLabel>
                      <FormControl>
                        <Input type="time" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                
                <FormField
                  control={form.control}
                  name="preferred_time_end"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Hora Fin</FormLabel>
                      <FormControl>
                        <Input type="time" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Package and Delivery Details */}
        <Card>
          <CardHeader className="pb-3">
            <CardTitle className="flex items-center gap-2 text-base">
              <Package className="h-4 w-4" />
              Detalles del Paquete
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <FormField
                control={form.control}
                name="priority"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Prioridad</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Seleccionar prioridad" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="low">Baja</SelectItem>
                        <SelectItem value="normal">Normal</SelectItem>
                        <SelectItem value="high">Alta</SelectItem>
                        <SelectItem value="urgent">Urgente</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="weight"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Peso (kg)</FormLabel>
                    <FormControl>
                      <Input type="number" step="0.1" placeholder="2.5" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="volume"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Volumen (m³)</FormLabel>
                    <FormControl>
                      <Input type="number" step="0.01" placeholder="0.05" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <FormField
                  control={form.control}
                  name="requires_signature"
                  render={({ field }) => (
                    <FormItem className="flex flex-row items-center justify-between rounded-lg border p-3">
                      <div className="space-y-0.5">
                        <FormLabel>Requiere Firma</FormLabel>
                        <FormDescription className="text-sm">
                          El destinatario debe firmar al recibir
                        </FormDescription>
                      </div>
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                    </FormItem>
                  )}
                />
                
                <FormField
                  control={form.control}
                  name="requires_pin"
                  render={({ field }) => (
                    <FormItem className="flex flex-row items-center justify-between rounded-lg border p-3">
                      <div className="space-y-0.5">
                        <FormLabel>Requiere PIN</FormLabel>
                        <FormDescription className="text-sm">
                          Código PIN para confirmar entrega
                        </FormDescription>
                      </div>
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                    </FormItem>
                  )}
                />
              </div>

              {watchRequiresPin && (
                <FormField
                  control={form.control}
                  name="pin_code"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Código PIN</FormLabel>
                      <FormControl>
                        <Input placeholder="1234" maxLength={6} {...field} />
                      </FormControl>
                      <FormDescription>
                        Código de 4-6 dígitos para confirmar la entrega
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              )}
            </div>

            <Separator />

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="notes"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Notas Internas</FormLabel>
                    <FormControl>
                      <Textarea 
                        placeholder="Notas para el operador..."
                        className="resize-none"
                        {...field} 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              
              <FormField
                control={form.control}
                name="delivery_instructions"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Instrucciones de Entrega</FormLabel>
                    <FormControl>
                      <Textarea 
                        placeholder="Instrucciones para el conductor..."
                        className="resize-none"
                        {...field} 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4">
          <Button type="button" variant="outline" onClick={onSuccess}>
            Cancelar
          </Button>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Creando..." : "Crear Pedido"}
          </Button>
        </div>
      </form>
    </Form>
  );
}