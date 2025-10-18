import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { toast } from "sonner";

const vehicleSchema = z.object({
  plate: z.string().min(6, "La placa debe tener al menos 6 caracteres"),
  model: z.string().min(2, "El modelo es requerido"),
  capacity_weight: z.coerce.number().min(100, "Capacidad de peso mínima: 100kg"),
  capacity_volume: z.coerce.number().min(1, "Capacidad de volumen mínima: 1m³"),
  driver_id: z.string().optional()
});

type VehicleFormData = z.infer<typeof vehicleSchema>;

interface CreateVehicleFormProps {
  onSuccess: () => void;
}

// Mock drivers data
const mockDrivers = [
  { id: "1", name: "Carlos Mendoza" },
  { id: "2", name: "Ana Rodriguez" },
  { id: "3", name: "Luis Fernández" },
  { id: "4", name: "María Silva" }
];

export function CreateVehicleForm({ onSuccess }: CreateVehicleFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const form = useForm<VehicleFormData>({
    resolver: zodResolver(vehicleSchema),
    defaultValues: {
      capacity_weight: 1500,
      capacity_volume: 8.0
    }
  });

  const onSubmit = async (data: VehicleFormData) => {
    setIsSubmitting(true);
    try {
      // TODO: Implement vehicle creation with Supabase
      console.log("Creating vehicle:", data);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      toast.success("Vehículo registrado exitosamente");
      onSuccess();
    } catch (error) {
      toast.error("Error al registrar el vehículo");
      console.error("Error creating vehicle:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="plate"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Placa del Vehículo</FormLabel>
                <FormControl>
                  <Input placeholder="ABC-123" {...field} />
                </FormControl>
                <FormDescription>
                  Formato: ABC-123 o ABC123
                </FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
          
          <FormField
            control={form.control}
            name="model"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Modelo</FormLabel>
                <FormControl>
                  <Input placeholder="Ford Transit" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="capacity_weight"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Capacidad de Peso (kg)</FormLabel>
                <FormControl>
                  <Input type="number" step="50" placeholder="1500" {...field} />
                </FormControl>
                <FormDescription>
                  Peso máximo que puede transportar
                </FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
          
          <FormField
            control={form.control}
            name="capacity_volume"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Capacidad de Volumen (m³)</FormLabel>
                <FormControl>
                  <Input type="number" step="0.5" placeholder="8.0" {...field} />
                </FormControl>
                <FormDescription>
                  Volumen máximo de carga
                </FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <FormField
          control={form.control}
          name="driver_id"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Conductor Asignado (Opcional)</FormLabel>
              <Select onValueChange={field.onChange} value={field.value}>
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder="Seleccionar conductor" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectItem value="">Sin asignar</SelectItem>
                  {mockDrivers.map((driver) => (
                    <SelectItem key={driver.id} value={driver.id}>
                      {driver.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormDescription>
                Puedes asignar un conductor específico a este vehículo
              </FormDescription>
              <FormMessage />
            </FormItem>
          )}
        />

        <div className="flex justify-end gap-4">
          <Button type="button" variant="outline" onClick={onSuccess}>
            Cancelar
          </Button>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Registrando..." : "Registrar Vehículo"}
          </Button>
        </div>
      </form>
    </Form>
  );
}