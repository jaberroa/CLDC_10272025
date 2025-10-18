import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { toast } from "sonner";
import { Calendar, Users, Vote, Eye } from "lucide-react";
import { format } from "date-fns";
import { es } from "date-fns/locale";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { ElectionResults } from "./ElectionResults";

interface Election {
  id: string;
  cargo: string;
  fecha_inicio: string;
  fecha_fin: string;
  estado: string;
  modalidad: string;
  candidatos: any;
  resultados: any;
  votos_totales: number;
  padron_id: string;
}

interface ElectionsListProps {
  organizationId: string;
}

export const ElectionsList = ({ organizationId }: ElectionsListProps) => {
  const [elections, setElections] = useState<Election[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchElections();
  }, [organizationId]);

  const fetchElections = async () => {
    try {
      const { data, error } = await supabase
        .from('elecciones')
        .select(`
          *,
          padrones_electorales!inner(organizacion_id)
        `)
        .eq('padrones_electorales.organizacion_id', organizationId)
        .order('fecha_inicio', { ascending: false });

      if (error) throw error;
      setElections(data || []);
    } catch (error) {
      console.error('Error fetching elections:', error);
      toast.error('Error al cargar las elecciones');
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (estado: string) => {
    switch (estado) {
      case 'programada': return 'default';
      case 'activa': return 'success';
      case 'finalizada': return 'secondary';
      case 'cancelada': return 'destructive';
      default: return 'default';
    }
  };

  const getStatusText = (estado: string) => {
    switch (estado) {
      case 'programada': return 'Programada';
      case 'activa': return 'En curso';
      case 'finalizada': return 'Finalizada';
      case 'cancelada': return 'Cancelada';
      default: return estado;
    }
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando elecciones...</div>;
  }

  if (elections.length === 0) {
    return (
      <Card>
        <CardContent className="text-center py-8">
          <Vote className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
          <p className="text-muted-foreground">
            No hay elecciones registradas para esta organización.
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="space-y-4">
      {elections.map((election) => (
        <Card key={election.id}>
          <CardHeader>
            <div className="flex justify-between items-start">
              <div>
                <CardTitle className="text-lg">{election.cargo}</CardTitle>
                <div className="flex items-center gap-4 mt-2 text-sm text-muted-foreground">
                  <div className="flex items-center gap-1">
                    <Calendar className="w-4 h-4" />
                    {format(new Date(election.fecha_inicio), 'dd/MM/yyyy', { locale: es })}
                    {' - '}
                    {format(new Date(election.fecha_fin), 'dd/MM/yyyy', { locale: es })}
                  </div>
                  <div className="flex items-center gap-1">
                    <Users className="w-4 h-4" />
                    {election.votos_totales} votos
                  </div>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <Badge variant={getStatusColor(election.estado) as any}>
                  {getStatusText(election.estado)}
                </Badge>
                <Badge variant="outline">
                  {election.modalidad === 'presencial' ? 'Presencial' : 
                   election.modalidad === 'virtual' ? 'Virtual' : 'Híbrida'}
                </Badge>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex justify-between items-center">
              <div>
                {election.candidatos && (
                  <p className="text-sm text-muted-foreground">
                    {Array.isArray(election.candidatos) ? election.candidatos.length : 0} candidatos
                  </p>
                )}
              </div>
              <div className="flex gap-2">
                {election.estado === 'finalizada' && election.resultados && (
                  <Dialog>
                    <DialogTrigger asChild>
                      <Button variant="outline" size="sm">
                        <Eye className="w-4 h-4 mr-2" />
                        Ver Resultados
                      </Button>
                    </DialogTrigger>
                    <DialogContent className="max-w-2xl">
                      <DialogHeader>
                        <DialogTitle>Resultados - {election.cargo}</DialogTitle>
                      </DialogHeader>
                      <ElectionResults election={election} />
                    </DialogContent>
                  </Dialog>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  );
};