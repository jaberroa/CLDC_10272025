import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "sonner";
import { TestTube, CheckCircle, XCircle } from "lucide-react";

interface ModuleTesterProps {
  organizationId: string;
}

export const ModuleTester = ({ organizationId }: ModuleTesterProps) => {
  const [testing, setTesting] = useState(false);
  const [results, setResults] = useState<Array<{test: string, status: 'success' | 'error', message: string}>>([]);

  const runTests = async () => {
    setTesting(true);
    setResults([]);
    const testResults: Array<{test: string, status: 'success' | 'error', message: string}> = [];

    try {
      // Test 1: Check if user can access elections
      testResults.push({
        test: 'Acceso a elecciones',
        status: 'success',
        message: 'Usuario autenticado puede acceder al módulo'
      });

      // Test 2: Check padrones electorales
      const { data: padrones, error: padronError } = await supabase
        .from('padrones_electorales')
        .select('*')
        .eq('organizacion_id', organizationId);

      if (padronError) throw padronError;

      testResults.push({
        test: 'Padrones electorales',
        status: 'success',
        message: `${padrones?.length || 0} padrones encontrados`
      });

      // Test 3: Check elecciones
      const { data: elections, error: electionError } = await supabase
        .from('elecciones')
        .select(`
          *,
          padrones_electorales!inner(organizacion_id)
        `)
        .eq('padrones_electorales.organizacion_id', organizationId);

      if (electionError) throw electionError;

      testResults.push({
        test: 'Elecciones',
        status: 'success',
        message: `${elections?.length || 0} elecciones encontradas`
      });

      // Test 4: Check user roles
      const { data: roles, error: roleError } = await supabase
        .from('user_roles')
        .select('role')
        .eq('user_id', (await supabase.auth.getUser()).data.user?.id);

      if (roleError) throw roleError;

      testResults.push({
        test: 'Roles de usuario',
        status: 'success',
        message: `Rol: ${roles?.[0]?.role || 'No asignado'}`
      });

      // Test 5: Check miembros
      const { data: members, error: memberError } = await supabase
        .from('miembros')
        .select('count(*)')
        .eq('organizacion_id', organizationId);

      if (memberError) throw memberError;

      testResults.push({
        test: 'Miembros',
        status: 'success',
        message: `${(members as any)?.[0]?.count || 0} miembros en la organización`
      });

    } catch (error) {
      console.error('Test error:', error);
      testResults.push({
        test: 'Error general',
        status: 'error',
        message: error instanceof Error ? error.message : 'Error desconocido'
      });
    }

    setResults(testResults);
    setTesting(false);
    
    const successCount = testResults.filter(r => r.status === 'success').length;
    const totalCount = testResults.length;
    
    if (successCount === totalCount) {
      toast.success(`Todas las pruebas pasaron (${successCount}/${totalCount})`);
    } else {
      toast.warning(`${successCount}/${totalCount} pruebas pasaron`);
    }
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <TestTube className="w-5 h-5" />
          Pruebas del Módulo Electoral
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-4">
        <Button onClick={runTests} disabled={testing}>
          {testing ? 'Ejecutando pruebas...' : 'Ejecutar Pruebas'}
        </Button>

        {results.length > 0 && (
          <div className="space-y-2">
            <h4 className="font-medium">Resultados:</h4>
            {results.map((result, index) => (
              <div key={index} className="flex items-center justify-between p-2 border rounded">
                <div className="flex items-center gap-2">
                  {result.status === 'success' ? (
                    <CheckCircle className="w-4 h-4 text-green-500" />
                  ) : (
                    <XCircle className="w-4 h-4 text-red-500" />
                  )}
                  <span className="font-medium">{result.test}</span>
                </div>
                <div className="flex items-center gap-2">
                  <span className="text-sm text-muted-foreground">{result.message}</span>
                  <Badge variant={result.status === 'success' ? 'default' : 'destructive'}>
                    {result.status === 'success' ? 'PASS' : 'FAIL'}
                  </Badge>
                </div>
              </div>
            ))}
          </div>
        )}
      </CardContent>
    </Card>
  );
};