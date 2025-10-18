import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";
import { Trophy, Medal, Award } from "lucide-react";

interface Election {
  id: string;
  cargo: string;
  candidatos: any;
  resultados: any;
  votos_totales: number;
}

interface ElectionResultsProps {
  election: Election;
}

export const ElectionResults = ({ election }: ElectionResultsProps) => {
  // Parse results data
  const results = election.resultados || {};
  const candidates = election.candidatos || [];
  const totalVotes = election.votos_totales || 0;

  // Calculate results
  const candidateResults = candidates.map((candidate: any) => {
    const votes = results[candidate.id] || 0;
    const percentage = totalVotes > 0 ? (votes / totalVotes) * 100 : 0;
    return {
      ...candidate,
      votes,
      percentage
    };
  }).sort((a: any, b: any) => b.votes - a.votes);

  const getPositionIcon = (index: number) => {
    switch (index) {
      case 0: return <Trophy className="w-5 h-5 text-yellow-500" />;
      case 1: return <Medal className="w-5 h-5 text-gray-400" />;
      case 2: return <Award className="w-5 h-5 text-orange-600" />;
      default: return null;
    }
  };

  const getPositionBadge = (index: number) => {
    switch (index) {
      case 0: return <Badge className="bg-yellow-500">Ganador</Badge>;
      case 1: return <Badge variant="secondary">2do Lugar</Badge>;
      case 2: return <Badge variant="outline">3er Lugar</Badge>;
      default: return null;
    }
  };

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h2 className="text-2xl font-bold mb-2">{election.cargo}</h2>
        <p className="text-muted-foreground">
          Total de votos: <span className="font-semibold">{totalVotes}</span>
        </p>
      </div>

      <div className="space-y-4">
        {candidateResults.map((candidate: any, index: number) => (
          <Card key={candidate.id} className={index === 0 ? 'border-yellow-200 bg-yellow-50/50' : ''}>
            <CardContent className="p-4">
              <div className="flex items-center justify-between mb-3">
                <div className="flex items-center gap-3">
                  {getPositionIcon(index)}
                  <div>
                    <h3 className="font-semibold text-lg">{candidate.nombre}</h3>
                    {candidate.propuesta && (
                      <p className="text-sm text-muted-foreground">{candidate.propuesta}</p>
                    )}
                  </div>
                </div>
                <div className="text-right">
                  {getPositionBadge(index)}
                </div>
              </div>
              
              <div className="space-y-2">
                <div className="flex justify-between items-center text-sm">
                  <span>{candidate.votes} votos</span>
                  <span className="font-semibold">{candidate.percentage.toFixed(1)}%</span>
                </div>
                <Progress 
                  value={candidate.percentage} 
                  className="h-3"
                />
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {totalVotes === 0 && (
        <Card>
          <CardContent className="text-center py-8">
            <p className="text-muted-foreground">
              No se han registrado votos para esta elección.
            </p>
          </CardContent>
        </Card>
      )}
    </div>
  );
};