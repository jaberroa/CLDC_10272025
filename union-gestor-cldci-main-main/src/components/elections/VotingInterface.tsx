import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { toast } from "sonner";
import { Vote, Check, AlertTriangle } from "lucide-react";

interface Election {
  id: string;
  cargo: string;
  candidatos: any;
  estado: string;
  fecha_fin: string;
}

interface VotingInterfaceProps {
  electionId: string;
  userId: string;
}

export const VotingInterface = ({ electionId, userId }: VotingInterfaceProps) => {
  const [election, setElection] = useState<Election | null>(null);
  const [selectedCandidate, setSelectedCandidate] = useState<string>("");
  const [hasVoted, setHasVoted] = useState(false);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchElectionAndVoteStatus();
  }, [electionId, userId]);

  const fetchElectionAndVoteStatus = async () => {
    try {
      // Fetch election details
      const { data: electionData, error: electionError } = await supabase
        .from('elecciones')
        .select('*')
        .eq('id', electionId)
        .single();

      if (electionError) throw electionError;
      setElection(electionData);

      // Check if user has already voted
      const { data: userInfo, error: userError } = await supabase
        .from('miembros')
        .select('id')
        .eq('user_id', userId)
        .single();

      if (userError) throw userError;

      const { data: voteData, error: voteError } = await supabase
        .from('votos')
        .select('id')
        .eq('eleccion_id', electionId)
        .eq('elector_id', userInfo.id)
        .maybeSingle();

      if (voteError && voteError.code !== 'PGRST116') throw voteError;
      setHasVoted(!!voteData);
    } catch (error) {
      console.error('Error fetching election data:', error);
      toast.error('Error al cargar la información de la elección');
    } finally {
      setLoading(false);
    }
  };

  const submitVote = async () => {
    if (!selectedCandidate || !election) return;

    setSubmitting(true);
    try {
      // Get user's member ID
      const { data: userInfo, error: userError } = await supabase
        .from('miembros')
        .select('id')
        .eq('user_id', userId)
        .single();

      if (userError) throw userError;

      // Create vote hash for security
      const voteHash = btoa(`${electionId}-${selectedCandidate}-${Date.now()}`);

      // Insert vote
      const { error: voteError } = await supabase
        .from('votos')
        .insert({
          eleccion_id: electionId,
          elector_id: userInfo.id,
          candidato_id: selectedCandidate,
          voto_hash: voteHash,
          modalidad: 'virtual',
          verificado: true
        });

      if (voteError) throw voteError;

      // Update election vote count using the database function
      const { error: updateError } = await supabase.rpc('increment_vote_count', {
        election_id: electionId
      });

      if (updateError) {
        console.error('Error updating vote count:', updateError);
        // Don't throw error as vote was already recorded
      }

      toast.success('Voto registrado exitosamente');
      setHasVoted(true);
    } catch (error) {
      console.error('Error submitting vote:', error);
      toast.error('Error al registrar el voto');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return <div className="flex justify-center p-8">Cargando...</div>;
  }

  if (!election) {
    return (
      <Alert>
        <AlertTriangle className="h-4 w-4" />
        <AlertDescription>
          No se pudo cargar la información de la elección.
        </AlertDescription>
      </Alert>
    );
  }

  if (election.estado !== 'activa') {
    return (
      <Alert>
        <AlertTriangle className="h-4 w-4" />
        <AlertDescription>
          Esta elección no está activa actualmente.
        </AlertDescription>
      </Alert>
    );
  }

  if (hasVoted) {
    return (
      <Card>
        <CardContent className="text-center py-8">
          <Check className="w-12 h-12 mx-auto mb-4 text-green-500" />
          <h3 className="text-lg font-semibold mb-2">Voto Registrado</h3>
          <p className="text-muted-foreground">
            Su voto ha sido registrado exitosamente para la elección de {election.cargo}.
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Vote className="w-5 h-5" />
          Votación: {election.cargo}
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-6">
        <Alert>
          <AlertTriangle className="h-4 w-4" />
          <AlertDescription>
            Su voto es secreto y no puede ser modificado una vez enviado.
          </AlertDescription>
        </Alert>

        <div className="space-y-4">
          <h3 className="font-medium">Seleccione su candidato:</h3>
          <RadioGroup 
            value={selectedCandidate} 
            onValueChange={setSelectedCandidate}
          >
            {(Array.isArray(election.candidatos) ? election.candidatos : [])?.map((candidate: any) => (
              <div key={candidate.id} className="flex items-start space-x-3 p-4 border rounded-lg hover:bg-muted/50">
                <RadioGroupItem value={candidate.id} id={candidate.id} className="mt-1" />
                <div className="flex-1">
                  <Label htmlFor={candidate.id} className="font-medium cursor-pointer">
                    {candidate.nombre}
                  </Label>
                  {candidate.propuesta && (
                    <p className="text-sm text-muted-foreground mt-1">
                      {candidate.propuesta}
                    </p>
                  )}
                </div>
              </div>
            ))}
          </RadioGroup>
        </div>

        <Button 
          onClick={submitVote}
          disabled={!selectedCandidate || submitting}
          className="w-full"
          size="lg"
        >
          {submitting ? 'Registrando voto...' : 'Confirmar Voto'}
        </Button>
      </CardContent>
    </Card>
  );
};