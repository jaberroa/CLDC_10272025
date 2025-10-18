import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { CheckCircle, XCircle, Loader, RefreshCw } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

interface TestResult {
  module: string;
  status: 'loading' | 'success' | 'error';
  count: number;
  error?: string;
}

const SystemTest = () => {
  const [testResults, setTestResults] = useState<TestResult[]>([
    { module: 'Órganos CLDC', status: 'loading', count: 0 },
    { module: 'Cargos', status: 'loading', count: 0 },
    { module: 'Miembros Directivos', status: 'loading', count: 0 },
    { module: 'Asambleas Generales', status: 'loading', count: 0 },
    { module: 'Seccionales', status: 'loading', count: 0 },
    { module: 'Asociaciones', status: 'loading', count: 0 },
    { module: 'Miembros Activos', status: 'loading', count: 0 },
  ]);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();

  useEffect(() => {
    runSystemTests();
  }, []);

  const runSystemTests = async () => {
    setLoading(true);
    const results: TestResult[] = [];

    try {
      // Test Órganos CLDC
      const organosResult = await supabase
        .from('organos_cldc')
        .select('id', { count: 'exact' })
        .eq('activo', true);
      results.push({ 
        module: 'Órganos CLDC', 
        status: organosResult.error ? 'error' : 'success', 
        count: organosResult.count || 0,
        error: organosResult.error?.message
      });

      // Test Cargos
      const cargosResult = await supabase
        .from('cargos_organos')
        .select('id', { count: 'exact' })
        .eq('activo', true);
      results.push({ 
        module: 'Cargos', 
        status: cargosResult.error ? 'error' : 'success', 
        count: cargosResult.count || 0,
        error: cargosResult.error?.message
      });

      // Test Miembros Directivos
      const directivosResult = await supabase
        .from('miembros_directivos')
        .select('id', { count: 'exact' })
        .eq('estado', 'activo');
      results.push({ 
        module: 'Miembros Directivos', 
        status: directivosResult.error ? 'error' : 'success', 
        count: directivosResult.count || 0,
        error: directivosResult.error?.message
      });

      // Test Asambleas Generales
      const asambleasResult = await supabase
        .from('asambleas_generales')
        .select('id', { count: 'exact' });
      results.push({ 
        module: 'Asambleas Generales', 
        status: asambleasResult.error ? 'error' : 'success', 
        count: asambleasResult.count || 0,
        error: asambleasResult.error?.message
      });

      // Test Seccionales
      const seccionalesResult = await supabase
        .from('seccionales')
        .select('id', { count: 'exact' })
        .eq('estado', 'activa');
      results.push({ 
        module: 'Seccionales', 
        status: seccionalesResult.error ? 'error' : 'success', 
        count: seccionalesResult.count || 0,
        error: seccionalesResult.error?.message
      });

      // Test Asociaciones
      const asociacionesResult = await supabase
        .from('organizaciones')
        .select('id', { count: 'exact' })
        .in('tipo', ['gremio', 'asociacion', 'filial', 'sindicato', 'otra_entidad']);
      results.push({ 
        module: 'Asociaciones', 
        status: asociacionesResult.error ? 'error' : 'success', 
        count: asociacionesResult.count || 0,
        error: asociacionesResult.error?.message
      });

      // Test Miembros Activos
      const miembrosResult = await supabase
        .from('miembros')
        .select('id', { count: 'exact' })
        .eq('estado_membresia', 'activa');
      results.push({ 
        module: 'Miembros Activos', 
        status: miembrosResult.error ? 'error' : 'success', 
        count: miembrosResult.count || 0,
        error: miembrosResult.error?.message
      });

    } catch (err) {
      console.error('Error running system tests:', err);
    }

    setTestResults(results);
    setLoading(false);

    const successCount = results.filter(r => r.status === 'success').length;
    const errorCount = results.filter(r => r.status === 'error').length;

    if (errorCount === 0) {
      toast({
        title: "✅ Sistema Funcional",
        description: `Todos los ${successCount} módulos están funcionando correctamente`
      });
    } else {
      toast({
        title: "⚠️ Problemas Detectados",
        description: `${errorCount} módulo(s) con errores de ${results.length} total`,
        variant: "destructive"
      });
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'loading':
        return <Loader className="h-4 w-4 animate-spin" />;
      case 'success':
        return <CheckCircle className="h-4 w-4 text-green-500" />;
      case 'error':
        return <XCircle className="h-4 w-4 text-red-500" />;
      default:
        return null;
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'loading':
        return <Badge variant="outline">Cargando...</Badge>;
      case 'success':
        return <Badge variant="default">Funcionando</Badge>;
      case 'error':
        return <Badge variant="destructive">Error</Badge>;
      default:
        return null;
    }
  };

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center justify-between">
            <span>Estado del Sistema CLDC</span>
            <Button
              variant="outline"
              size="sm"
              onClick={runSystemTests}
              disabled={loading}
            >
              <RefreshCw className={`h-4 w-4 mr-2 ${loading ? 'animate-spin' : ''}`} />
              Actualizar
            </Button>
          </CardTitle>
          <CardDescription>
            Verificación de funcionamiento de todos los módulos del sistema organizacional
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {testResults.map((result, index) => (
              <Card key={index} className="p-4">
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      {getStatusIcon(result.status)}
                      <h3 className="font-semibold text-sm">{result.module}</h3>
                    </div>
                    {getStatusBadge(result.status)}
                  </div>
                  
                  <div className="space-y-1">
                    <p className="text-2xl font-bold text-primary">{result.count}</p>
                    <p className="text-xs text-muted-foreground">
                      registros activos
                    </p>
                  </div>

                  {result.error && (
                    <p className="text-xs text-red-500 bg-red-50 p-2 rounded">
                      {result.error}
                    </p>
                  )}
                </div>
              </Card>
            ))}
          </div>

          {/* Summary */}
          <div className="mt-6 p-4 bg-muted rounded-lg">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="font-semibold">Resumen del Sistema</h3>
                <p className="text-sm text-muted-foreground">
                  Estado general de funcionamiento
                </p>
              </div>
              <div className="text-right">
                <p className="text-lg font-bold">
                  {testResults.filter(r => r.status === 'success').length}/
                  {testResults.length}
                </p>
                <p className="text-xs text-muted-foreground">módulos OK</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Test Instructions */}
      <Card>
        <CardHeader>
          <CardTitle>Funcionalidades Disponibles</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <h4 className="font-semibold text-sm">✅ Módulos Implementados</h4>
              <ul className="text-xs space-y-1 text-muted-foreground">
                <li>• Gestión de Órganos CLDC</li>
                <li>• Administración de Cargos</li>
                <li>• Miembros Directivos</li>
                <li>• Asambleas Generales</li>
                <li>• Seccionales Territoriales</li>
                <li>• Asociaciones Afiliadas</li>
              </ul>
            </div>
            
            <div className="space-y-2">
              <h4 className="font-semibold text-sm">🔧 Funcionalidades</h4>
              <ul className="text-xs space-y-1 text-muted-foreground">
                <li>• Crear, editar y eliminar registros</li>
                <li>• Filtrado por tipo y estado</li>
                <li>• Estados vacíos informativos</li>
                <li>• Validación de datos</li>
                <li>• Integración con Supabase</li>
                <li>• Interfaz responsive</li>
              </ul>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default SystemTest;