import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { ResponsiveContainer, BarChart, Bar, XAxis, YAxis, Tooltip, PieChart, Pie, Cell } from "recharts";
import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { Download, RefreshCw, ArrowLeft } from "lucide-react";
import { Link } from "react-router-dom";

const Reportes = () => {
  const [data, setData] = useState({
    miembrosPorProvincia: [],
    estadisticasGenerales: {
      totalMiembros: 0,
      miembrosActivos: 0,
      organizaciones: 0,
      transacciones: 0
    },
    transaccionesPorMes: []
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchReportData();
  }, []);

  const fetchReportData = async () => {
    setLoading(true);
    try {
      // Get current user
      const { data: { user } } = await supabase.auth.getUser();
      
      if (!user) {
        throw new Error('User not authenticated');
      }

      // Use secure function for member stats by province
      const { data: membersByProvince } = await supabase.rpc('get_member_stats_by_province', {
        requesting_user_id: user.id
      });

      // Fetch organizaciones
      const { data: organizaciones } = await supabase
        .from('organizaciones')
        .select('id');

      // Fetch transacciones
      const { data: transacciones } = await supabase
        .from('transacciones_financieras')
        .select('*');

      // Process member stats by province (data already comes processed from the secure function)
      const miembrosPorProvincia = membersByProvince?.map(stat => ({
        name: stat.provincia || 'Sin especificar',
        miembros: Number(stat.member_count)
      })) || [];

      // Transacciones por mes
      const transaccionesPorMes = {};
      transacciones?.forEach(t => {
        const fecha = new Date(t.fecha);
        const mes = `${fecha.getFullYear()}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}`;
        if (!transaccionesPorMes[mes]) {
          transaccionesPorMes[mes] = { mes, ingresos: 0, egresos: 0 };
        }
        if (t.tipo === 'ingreso') {
          transaccionesPorMes[mes].ingresos += parseFloat(t.monto.toString());
        } else {
          transaccionesPorMes[mes].egresos += parseFloat(t.monto.toString());
        }
      });

      setData({
        miembrosPorProvincia,
        estadisticasGenerales: {
          totalMiembros: membersByProvince?.reduce((sum, stat) => sum + Number(stat.member_count), 0) || 0,
          miembrosActivos: membersByProvince?.reduce((sum, stat) => sum + Number(stat.active_count), 0) || 0,
          organizaciones: organizaciones?.length || 0,
          transacciones: transacciones?.length || 0
        },
        transaccionesPorMes: Object.values(transaccionesPorMes)
      });

    } catch (error) {
      console.error('Error fetching report data:', error);
      toast({
        title: "Error",
        description: "Error al cargar los datos del reporte",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const COLORS = ['hsl(var(--primary))', 'hsl(var(--secondary))', 'hsl(var(--accent))', 'hsl(var(--muted))'];

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Reportes", item: "https://cldci.com/reportes" }
  ]);

  return (
    <main className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO title="Reportes y Estadísticas – CLDCI" description="Indicadores por seccional, período y participación." />
      <StructuredData data={breadcrumbData} />
      
      <div className="container mx-auto py-10">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-4">
            <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
              <ArrowLeft className="h-4 w-4" />
            </Link>
            <h1 className="text-3xl font-bold text-white">Reportes y Estadísticas</h1>
          </div>
          <div className="flex gap-2">
            <Button variant="outline" onClick={fetchReportData} disabled={loading} className="border-white/20 bg-white/10 text-white hover:bg-white/20">
              <RefreshCw className={`w-4 h-4 mr-2 ${loading ? 'animate-spin' : ''}`} />
              Actualizar
            </Button>
            <Button className="bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold">
              <Download className="w-4 h-4 mr-2" />
              Exportar
            </Button>
          </div>
        </div>

      {/* Estadísticas Generales */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <Card>
          <CardContent className="p-4">
            <div className="text-2xl font-bold text-primary">{data.estadisticasGenerales.totalMiembros}</div>
            <p className="text-sm text-muted-foreground">Total Miembros</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="text-2xl font-bold text-success">{data.estadisticasGenerales.miembrosActivos}</div>
            <p className="text-sm text-muted-foreground">Miembros Activos</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="text-2xl font-bold text-warning">{data.estadisticasGenerales.organizaciones}</div>
            <p className="text-sm text-muted-foreground">Organizaciones</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="text-2xl font-bold text-accent">{data.estadisticasGenerales.transacciones}</div>
            <p className="text-sm text-muted-foreground">Transacciones</p>
          </CardContent>
        </Card>
      </div>

      <div className="grid lg:grid-cols-2 gap-6">
        {/* Gráfico de miembros por provincia */}
        <Card>
          <CardHeader>
            <CardTitle>Miembros por Provincia</CardTitle>
          </CardHeader>
          <CardContent className="h-80">
            {loading ? (
              <div className="flex items-center justify-center h-full">
                <p>Cargando datos...</p>
              </div>
            ) : (
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={data.miembrosPorProvincia}>
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="miembros" fill="hsl(var(--accent))" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            )}
          </CardContent>
        </Card>

        {/* Gráfico de transacciones por mes */}
        <Card>
          <CardHeader>
            <CardTitle>Transacciones Financieras por Mes</CardTitle>
          </CardHeader>
          <CardContent className="h-80">
            {loading ? (
              <div className="flex items-center justify-center h-full">
                <p>Cargando datos...</p>
              </div>
            ) : (
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={data.transaccionesPorMes}>
                  <XAxis dataKey="mes" />
                  <YAxis />
                  <Tooltip formatter={(value, name) => [
                    `RD$ ${Number(value).toLocaleString()}`, 
                    name === 'ingresos' ? 'Ingresos' : 'Egresos'
                  ]} />
                  <Bar dataKey="ingresos" fill="hsl(var(--success))" />
                  <Bar dataKey="egresos" fill="hsl(var(--destructive))" />
                </BarChart>
              </ResponsiveContainer>
            )}
          </CardContent>
        </Card>
      </div>
      </div>
    </main>
  );
};

export default Reportes;
