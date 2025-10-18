import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { toast } from "sonner";

const driverSchema = z.object({
  name: z.string().min(2, "El nombre es requerido"),
  email: z.string().email("Email inválido"),
  phone: z.string().min(8, "Teléfono requerido"),
  license_number: z.string().min(5, "Número de licencia requerido"),
  vehicle_id: z.string().optional()
});

type DriverFormData = z.infer<typeof driverSchema>;

interface CreateDriverFormProps {
  onSuccess: () => void;
}

// Mock vehicles data
const mockVehicles = [
  { id: "1", plate: "ABC-123", model: "Ford Transit" },
  { id: "2", plate: "DEF-456", model: "Mercedes Sprinter" },
  { id: "3", plate: "GHI-789", model: "Iveco Daily" },
  { id: "4", plate: "JKL-012", model: "Volkswagen Crafter" }
];

export function CreateDriverForm({ onSuccess }: CreateDriverFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const form = useForm<DriverFormData>({
    resolver: zodResolver(driverSchema),
    defaultValues: {}
  });

  const onSubmit = async (data: DriverFormData) => {
    setIsSubmitting(true);
    try {
      // TODO: Implement driver creation with Supabase
      console.log("Creating driver:", data);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      toast.success("Conductor registrado exitosamente");
      onSuccess();
    } catch (error) {
      toast.error("Error al registrar el conductor");
      console.error("Error creating driver:", error);
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
            name="name"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Nombre Completo</FormLabel>
                <FormControl>
                  <Input placeholder="Carlos Mendoza" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
          
          <FormField
            control={form.control}
            name="email"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                  <Input type="email" placeholder="conductor@empresa.com" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="phone"
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
          
          <FormField
            control={form.control}
            name="license_number"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Número de Licencia</FormLabel>
                <FormControl>
                  <Input placeholder="B-12345678" {...field} />
                </FormControl>
                <FormDescription>
                  Licencia de conducir vigente
                </FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <FormField
          control={form.control}
          name="vehicle_id"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Vehículo Asignado (Opcional)</FormLabel>
              <Select onValueChange={field.onChange} value={field.value}>
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder="Seleccionar vehículo" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectItem value="">Sin asignar</SelectItem>
                  {mockVehicles.map((vehicle) => (
                    <SelectItem key={vehicle.id} value={vehicle.id}>
                      {vehicle.plate} - {vehicle.model}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormDescription>
                Puedes asignar un vehículo específico a este conductor
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
            {isSubmitting ? "Registrando..." : "Registrar Conductor"}
          </Button>
        </div>
      </form>
    </Form>
  );
}