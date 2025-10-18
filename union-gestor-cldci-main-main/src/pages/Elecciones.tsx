import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema } from "@/lib/seo/structured-data";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ArrowLeft, Vote, Users, Calendar, BarChart3 } from "lucide-react";
import { Link } from "react-router-dom";
import { toast } from "sonner";
import { ElectionsList } from "@/components/elections/ElectionsList";
import { CreateElectionForm } from "@/components/elections/CreateElectionForm";
import { ElectoralRegistryForm } from "@/components/elections/ElectoralRegistryForm";
import { VotingInterface } from "@/components/elections/VotingInterface";
import { useAuth } from "@/components/auth/AuthProvider";
import { ModuleTester } from "@/components/testing/ModuleTester";

const Elecciones = () => {
  const { user } = useAuth();
  const [userRole, setUserRole] = useState<string>('');
  const [userOrganization, setUserOrganization] = useState<string>('');
  const [activeElections, setActiveElections] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user) {
      fetchUserInfo();
    }
  }, [user]);

  const fetchUserInfo = async () => {
    try {
      // Get user role and organization
      const { data: roleData, error: roleError } = await supabase
        .from('user_roles')
        .select('role, organizacion_id')
        .eq('user_id', user?.id)
        .single();

      if (roleError && roleError.code !== 'PGRST116') throw roleError;

      if (roleData) {
        setUserRole(roleData.role);
        setUserOrganization(roleData.organizacion_id);
        
        // Fetch active elections for voting
        const { data: electionsData, error: electionsError } = await supabase
          .from('elecciones')
          .select(`
            *,
            padrones_electorales!inner(organizacion_id)
          `)
          .eq('estado', 'activa')
          .eq('padrones_electorales.organizacion_id', roleData.organizacion_id);

        if (electionsError) throw electionsError;
        setActiveElections(electionsData || []);
      }
    } catch (error) {
      console.error('Error fetching user info:', error);
      toast.error('Error al cargar información del usuario');
    } finally {
      setLoading(false);
    }
  };

  const canManageElections = userRole === 'admin' || userRole === 'moderador';

  if (loading) {
    return <div className="flex justify-center items-center h-screen">Cargando...</div>;
  }

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Elecciones", item: "https://cldci.com/elecciones" }
  ]);

  return (
    <main className="container mx-auto py-10">
      <SEO title="Elecciones – CLDCI" description="Sistema de gestión electoral con votación segura y auditable." />
      <StructuredData data={breadcrumbData} />
      <div className="flex items-center gap-4 mb-6">
        <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10">
          <ArrowLeft className="h-4 w-4" />
        </Link>
        <h1 className="text-3xl font-bold">Elecciones y Votación</h1>
      </div>

      {!user ? (
        <div className="text-center py-12">
          <Vote className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
          <h3 className="text-lg font-semibold mb-2">Acceso Restringido</h3>
          <p className="text-muted-foreground mb-4">
            Debe iniciar sesión para acceder al sistema electoral.
          </p>
          <Link to="/auth" className="inline-flex items-center justify-center rounded-md bg-primary text-primary-foreground px-4 py-2">
            Iniciar Sesión
          </Link>
        </div>
      ) : (
        <Tabs defaultValue={activeElections.length > 0 ? "vote" : "elections"}>
          <TabsList className="grid w-full grid-cols-5">
            {activeElections.length > 0 && (
              <TabsTrigger value="vote" className="flex items-center gap-2">
                <Vote className="w-4 h-4" />
                Votar
              </TabsTrigger>
            )}
            <TabsTrigger value="elections" className="flex items-center gap-2">
              <BarChart3 className="w-4 h-4" />
              Elecciones
            </TabsTrigger>
            {canManageElections && (
              <>
                <TabsTrigger value="create" className="flex items-center gap-2">
                  <Calendar className="w-4 h-4" />
                  Nueva Elección
                </TabsTrigger>
                <TabsTrigger value="registry" className="flex items-center gap-2">
                  <Users className="w-4 h-4" />
                  Crear Padrón
                </TabsTrigger>
              </>
            )}
            <TabsTrigger value="test" className="flex items-center gap-2">
              Pruebas
            </TabsTrigger>
          </TabsList>

          {activeElections.length > 0 && (
            <TabsContent value="vote" className="space-y-6">
              <h2 className="text-2xl font-semibold">Elecciones Activas</h2>
              {activeElections.map((election) => (
                <VotingInterface 
                  key={election.id} 
                  electionId={election.id} 
                  userId={user.id} 
                />
              ))}
            </TabsContent>
          )}

          <TabsContent value="elections">
            <div className="space-y-6">
              <h2 className="text-2xl font-semibold">Historial de Elecciones</h2>
              <ElectionsList organizationId={userOrganization} />
            </div>
          </TabsContent>

          {canManageElections && (
            <>
              <TabsContent value="create">
                <div className="space-y-6">
                  <h2 className="text-2xl font-semibold">Crear Nueva Elección</h2>
                  <CreateElectionForm 
                    organizationId={userOrganization}
                    onElectionCreated={fetchUserInfo}
                  />
                </div>
              </TabsContent>

              <TabsContent value="registry">
                <div className="space-y-6">
                  <h2 className="text-2xl font-semibold">Crear Padrón Electoral</h2>
                  <ElectoralRegistryForm 
                    organizationId={userOrganization}
                    onRegistryCreated={fetchUserInfo}
                  />
                </div>
              </TabsContent>
            </>
          )}

          <TabsContent value="test">
            <div className="space-y-6">
              <h2 className="text-2xl font-semibold">Pruebas del Sistema</h2>
              <ModuleTester organizationId={userOrganization} />
            </div>
          </TabsContent>
        </Tabs>
      )}
    </main>
  );
};

export default Elecciones;
