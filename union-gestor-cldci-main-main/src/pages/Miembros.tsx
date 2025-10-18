import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useMemo, useState, useEffect } from "react";
import { CreditCard, FileText, Download, QrCode, Users, Mail, MessageCircle, ArrowLeft, Shield } from "lucide-react";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { Link } from "react-router-dom";

interface MiembroPublico {
  id: string;
  nombre_completo: string;
  profesion: string;
  estado_membresia: string;
  tipo_membresia: 'fundador' | 'activo' | 'pasivo' | 'honorifico' | 'estudiante' | 'diaspora';
  organizacion_id: string;
  numero_carnet: string;
  fecha_ingreso?: string;
  foto_url?: string;
  // Campos específicos según clasificación
  fecha_fundacion?: string;
  motivo_suspension?: string;
  fecha_suspension?: string;
  institucion_educativa?: string;
  pais_residencia?: string;
  reconocimiento_detalle?: string;
}

interface MiembroCompleto extends MiembroPublico {
  email: string;
  telefono: string;
  cedula?: string;
  direccion?: string;
  fecha_nacimiento?: string;
  fecha_ingreso: string;
  foto_url: string;
  organizaciones?: {
    nombre: string;
  };
}

type Miembro = MiembroPublico | MiembroCompleto;

const Miembros = () => {
  const [q, setQ] = useState("");
  const [tipoFilter, setTipoFilter] = useState<string>("todos");
  const [selectedMember, setSelectedMember] = useState<string | null>(null);
  const [miembros, setMiembros] = useState<Miembro[]>([]);
  const [miembrosCompletos, setMiembrosCompletos] = useState<Miembro[]>([]);
  const [loading, setLoading] = useState(true);
  const [userRole, setUserRole] = useState<string | null>(null);

  useEffect(() => {
    fetchUserRole();
    fetchMiembros();
  }, []);

  const fetchUserRole = async () => {
    try {
      const { data, error } = await supabase
        .from('user_roles')
        .select('role')
        .single();
      
      if (error && error.code !== 'PGRST116') throw error;
      setUserRole(data?.role || null);
    } catch (error) {
      console.error('Error fetching user role:', error);
    }
  };

  const fetchMiembros = async () => {
    try {
      // Get user's current organizations first
      const { data: userOrgs, error: userOrgError } = await supabase
        .from('user_roles')
        .select('organizacion_id, role')
        .eq('user_id', (await supabase.auth.getUser()).data.user?.id);

      if (userOrgError) throw userOrgError;

      const currentUserRole = userOrgs?.[0]?.role;
      setUserRole(currentUserRole || null);

      if (currentUserRole === 'admin' || currentUserRole === 'moderador') {
        // For admins and moderators, get full data with proper RLS
        const { data, error } = await supabase
          .from('miembros')
          .select(`
            *,
            organizaciones:organizacion_id (
              nombre
            )
          `);

        if (error) throw error;
        // Ensure data includes tipo_membresia field
        const miembrosWithDefaults = (data || []).map(m => ({
          ...m,
          tipo_membresia: m.tipo_membresia || 'activo' as const
        }));
        setMiembros(miembrosWithDefaults);
        setMiembrosCompletos(miembrosWithDefaults);
      } else {
        // For regular users, use the secure function to get only public data
        if (userOrgs?.[0]?.organizacion_id) {
          const { data, error } = await supabase.rpc('get_safe_member_info', {
            org_id: userOrgs[0].organizacion_id
          });
          if (error) throw error;
          // Add default tipo_membresia for compatibility
          const miembrosWithDefaults = (data || []).map(m => ({
            ...m,
            tipo_membresia: 'activo' as const
          }));
          setMiembros(miembrosWithDefaults);
        }
      }
    } catch (error) {
      console.error('Error fetching miembros:', error);
      toast({
        title: "Error",
        description: "Error al cargar los miembros",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };
  
  const filtered = useMemo(() => {
    let result = miembros.filter((m) => 
      `${m.nombre_completo} ${m.profesion}`.toLowerCase().includes(q.toLowerCase())
    );
    
    if (tipoFilter !== "todos") {
      result = result.filter(m => m.tipo_membresia === tipoFilter);
    }
    
    return result;
  }, [q, miembros, tipoFilter]);

  const getTipoMembresiaLabel = (tipo: string) => {
    const labels = {
      'fundador': 'Fundador',
      'activo': 'Activo',
      'pasivo': 'Pasivo',
      'honorifico': 'Honorífico',
      'estudiante': 'Estudiante',
      'diaspora': 'Diáspora'
    };
    return labels[tipo as keyof typeof labels] || tipo;
  };

  const getTipoMembresiaVariant = (tipo: string): "default" | "destructive" | "secondary" | "outline" => {
    const variants: Record<string, "default" | "destructive" | "secondary" | "outline"> = {
      'fundador': 'default',
      'activo': 'default',
      'pasivo': 'secondary',
      'honorifico': 'outline',
      'estudiante': 'secondary',
      'diaspora': 'outline'
    };
    return variants[tipo] || 'default';
  };

  const isAdmin = userRole === 'admin';
  const isModerator = userRole === 'moderador';
  const canViewSensitiveData = isAdmin || isModerator;

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Gestión de Miembros", item: "https://cldci.com/miembros" }
  ]);

  return (
    <main className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO title="Gestión de Miembros – CLDCI" description="Base de datos integral, historial de pagos, certificaciones y comunicación interna." />
      <StructuredData data={breadcrumbData} />
      
      <div className="container mx-auto py-10">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-4">
            <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
              <ArrowLeft className="h-4 w-4" />
            </Link>
            <h1 className="text-3xl font-bold text-white">Gestión de Miembros</h1>
          </div>
          <Button className="bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold">
            <Users className="w-4 h-4 mr-2" />
            Nuevo Miembro
          </Button>
        </div>

      <Tabs defaultValue="lista" className="space-y-6">
        <TabsList className="grid w-full grid-cols-4">
          <TabsTrigger value="lista">Lista de Miembros</TabsTrigger>
          <TabsTrigger value="pagos">Historial de Pagos</TabsTrigger>
          <TabsTrigger value="certificaciones">Certificaciones</TabsTrigger>
          <TabsTrigger value="comunicacion">Comunicación</TabsTrigger>
        </TabsList>

        <TabsContent value="lista" className="space-y-6">
          <div className="flex gap-4 mb-6 flex-wrap">
            <Input 
              placeholder="Buscar por nombre, organización o profesión" 
              value={q} 
              onChange={(e) => setQ(e.target.value)}
              className="max-w-md"
            />
            <Select value={tipoFilter} onValueChange={setTipoFilter}>
              <SelectTrigger className="w-[200px]">
                <SelectValue placeholder="Filtrar por tipo" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="todos">Todos los tipos</SelectItem>
                <SelectItem value="fundador">Miembros Fundadores</SelectItem>
                <SelectItem value="activo">Miembros Activos</SelectItem>
                <SelectItem value="pasivo">Miembros Pasivos</SelectItem>
                <SelectItem value="honorifico">Miembros Honoríficos</SelectItem>
                <SelectItem value="estudiante">Miembros Estudiantes</SelectItem>
                <SelectItem value="diaspora">Miembros de la Diáspora</SelectItem>
              </SelectContent>
            </Select>
            <Button variant="outline">
              <Download className="w-4 h-4 mr-2" />
              Exportar Lista
            </Button>
          </div>

          {loading ? (
            <div className="text-center py-8">
              <p>Cargando miembros...</p>
            </div>
          ) : (
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filtered.map((m) => {
                const isCompleteData = 'email' in m;
                return (
                  <Card key={m.id} className="cursor-pointer hover:shadow-elevated transition-all duration-200" onClick={() => setSelectedMember(m.id)}>
                    <CardHeader className="pb-3">
                      <div className="flex justify-between items-start">
                        <CardTitle className="text-lg flex items-center gap-2">
                          {m.nombre_completo}
                          {!canViewSensitiveData && (
                            <Shield className="w-4 h-4 text-muted-foreground" />
                          )}
                        </CardTitle>
                        <div className="flex flex-col gap-1">
                          <Badge variant={m.estado_membresia === 'activo' ? "default" : "destructive"}>
                            {m.estado_membresia === 'activo' ? "Activo" : m.estado_membresia}
                          </Badge>
                          <Badge variant={getTipoMembresiaVariant(m.tipo_membresia)}>
                            {getTipoMembresiaLabel(m.tipo_membresia)}
                          </Badge>
                        </div>
                      </div>
                      {isCompleteData && m.organizaciones && (
                        <p className="text-sm text-muted-foreground">{m.organizaciones.nombre}</p>
                      )}
                      <p className="text-sm font-medium text-primary">{m.profesion}</p>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2 text-sm">
                        {canViewSensitiveData && isCompleteData && (
                          <p><strong>Email:</strong> {m.email}</p>
                        )}
                        <p><strong>Carnet:</strong> {m.numero_carnet}</p>
                        {m.fecha_ingreso && (
                          <p><strong>Ingreso:</strong> {new Date(m.fecha_ingreso).toLocaleDateString()}</p>
                        )}
                        
                        {/* Información específica según tipo de membresía */}
                        {m.tipo_membresia === 'fundador' && m.fecha_fundacion && (
                          <p><strong>Asamblea Constitutiva:</strong> {new Date(m.fecha_fundacion).toLocaleDateString()}</p>
                        )}
                        {m.tipo_membresia === 'pasivo' && m.motivo_suspension && (
                          <p><strong>Motivo:</strong> {m.motivo_suspension}</p>
                        )}
                        {m.tipo_membresia === 'estudiante' && m.institucion_educativa && (
                          <p><strong>Institución:</strong> {m.institucion_educativa}</p>
                        )}
                        {m.tipo_membresia === 'diaspora' && m.pais_residencia && (
                          <p><strong>Residencia:</strong> {m.pais_residencia}</p>
                        )}
                        {m.tipo_membresia === 'honorifico' && m.reconocimiento_detalle && (
                          <p><strong>Reconocimiento:</strong> {m.reconocimiento_detalle}</p>
                        )}
                        
                        <div className="flex flex-wrap gap-1 mt-2">
                          <Badge variant="secondary" className="text-xs">
                            {m.profesion}
                          </Badge>
                        </div>
                        {!canViewSensitiveData && (
                          <div className="mt-2 p-2 bg-muted rounded-md">
                            <p className="text-xs text-muted-foreground flex items-center gap-1">
                              <Shield className="w-3 h-3" />
                              Información personal protegida
                            </p>
                          </div>
                        )}
                      </div>
                      <div className="flex gap-2 mt-4">
                        <Button size="sm" variant="outline">
                          <QrCode className="w-3 h-3 mr-1" />
                          Carnet QR
                        </Button>
                        {canViewSensitiveData && (
                          <Button size="sm" variant="outline">
                            <FileText className="w-3 h-3 mr-1" />
                            Certificado
                          </Button>
                        )}
                      </div>
                    </CardContent>
                  </Card>
                );
              })}
            </div>
          )}
        </TabsContent>

        <TabsContent value="pagos" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <CreditCard className="w-5 h-5 text-module-miembros" />
                Historial de Pagos y Cuotas
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid md:grid-cols-4 gap-4 mb-6">
                <div className="text-center p-4 bg-success/10 rounded-lg border border-success/20">
                  <div className="text-2xl font-bold text-success">{miembros.filter(m => m.estado_membresia === 'activo').length}</div>
                  <div className="text-sm text-muted-foreground">Miembros activos</div>
                </div>
                <div className="text-center p-4 bg-blue/10 rounded-lg border border-blue/20">
                  <div className="text-2xl font-bold text-blue">{miembros.filter(m => m.tipo_membresia === 'fundador').length}</div>
                  <div className="text-sm text-muted-foreground">Miembros fundadores</div>
                </div>
                <div className="text-center p-4 bg-purple/10 rounded-lg border border-purple/20">
                  <div className="text-2xl font-bold text-purple">{miembros.filter(m => m.tipo_membresia === 'diaspora').length}</div>
                  <div className="text-sm text-muted-foreground">Miembros diáspora</div>
                </div>
                <div className="text-center p-4 bg-warning/10 rounded-lg border border-warning/20">
                  <div className="text-2xl font-bold text-warning">{miembros.filter(m => m.estado_membresia !== 'activo').length}</div>
                  <div className="text-sm text-muted-foreground">Membresías pendientes</div>
                </div>
              </div>
              
              <div className="space-y-3">
                <h4 className="font-semibold">Pagos Recientes</h4>
                <div className="space-y-2">
                  {[
                    { miembro: "Ana Pérez", monto: "RD$ 2,500", fecha: "2024-02-15", concepto: "Cuota Anual 2024" },
                    { miembro: "Carlos Rodríguez", monto: "RD$ 500", fecha: "2024-02-14", concepto: "Cuota Trimestral" },
                    { miembro: "María López", monto: "RD$ 2,500", fecha: "2024-02-12", concepto: "Cuota Anual 2024" },
                  ].map((pago, idx) => (
                    <div key={idx} className="flex justify-between items-center p-3 bg-muted rounded-lg">
                      <div>
                        <p className="font-medium">{pago.miembro}</p>
                        <p className="text-sm text-muted-foreground">{pago.concepto}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-medium">{pago.monto}</p>
                        <p className="text-sm text-muted-foreground">{pago.fecha}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="certificaciones" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="w-5 h-5 text-module-miembros" />
                Gestión Documental y Certificaciones
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <h4 className="font-semibold mb-3">Certificaciones Disponibles</h4>
                  <div className="space-y-2">
                    {[
                      "Locución Comercial",
                      "Locución Deportiva", 
                      "Locución Musical",
                      "Protocolo y Ceremonial",
                      "Comunicación Corporativa"
                    ].map((cert, idx) => (
                      <div key={idx} className="flex justify-between items-center p-2 bg-muted rounded">
                        <span className="text-sm">{cert}</span>
                        <Button size="sm" variant="outline">Emitir</Button>
                      </div>
                    ))}
                  </div>
                </div>
                
                <div>
                  <h4 className="font-semibold mb-3">Certificados Emitidos Recientes</h4>
                  <div className="space-y-2">
                    {[
                      { miembro: "Ana Pérez", certificado: "Locución Comercial", fecha: "2024-01-15" },
                      { miembro: "Juan Martínez", certificado: "Protocolo", fecha: "2024-01-10" },
                      { miembro: "María López", certificado: "Locución Musical", fecha: "2024-01-08" },
                    ].map((cert, idx) => (
                      <div key={idx} className="p-2 bg-muted rounded text-sm">
                        <p className="font-medium">{cert.miembro}</p>
                        <p className="text-muted-foreground">{cert.certificado} - {cert.fecha}</p>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="comunicacion" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Mail className="w-5 h-5 text-module-miembros" />
                Comunicación Interna Segmentada
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid md:grid-cols-2 gap-6">
                <div>
                  <h4 className="font-semibold mb-3">Crear Comunicación</h4>
                  <div className="space-y-3">
                    <div>
                      <label className="text-sm font-medium">Destinatarios</label>
                      <select className="w-full p-2 border border-border rounded-md text-sm">
                        <option>Todos los miembros</option>
                        <option>Por seccional</option>
                        <option>Solo directivos</option>
                        <option>Cuotas pendientes</option>
                      </select>
                    </div>
                    <div>
                      <label className="text-sm font-medium">Canal</label>
                      <div className="flex gap-2 mt-1">
                        <Button size="sm" variant="outline">
                          <Mail className="w-3 h-3 mr-1" />
                          Email
                        </Button>
                        <Button size="sm" variant="outline">
                          <MessageCircle className="w-3 h-3 mr-1" />
                          SMS
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 className="font-semibold mb-3">Estadísticas de Comunicación</h4>
                  <div className="space-y-2">
                    <div className="flex justify-between p-2 bg-muted rounded text-sm">
                      <span>Emails enviados (mes)</span>
                      <span className="font-medium">1,234</span>
                    </div>
                    <div className="flex justify-between p-2 bg-muted rounded text-sm">
                      <span>Tasa de apertura</span>
                      <span className="font-medium">68%</span>
                    </div>
                    <div className="flex justify-between p-2 bg-muted rounded text-sm">
                      <span>SMS enviados (mes)</span>
                      <span className="font-medium">456</span>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
      </div>
    </main>
  );
};

export default Miembros;
