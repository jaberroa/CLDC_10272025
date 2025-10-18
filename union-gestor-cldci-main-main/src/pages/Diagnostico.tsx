import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { SEO } from "@/components/seo/SEO";
import { ModuleTester } from "@/components/testing/ModuleTester";
import { ArrowLeft } from "lucide-react";
import { Link } from "react-router-dom";
import { useAuth } from "@/components/auth/AuthProvider";

const Diagnostico = () => {
  const { user } = useAuth();
  const [organizationId, setOrganizationId] = useState<string>("");

  useEffect(() => {
    if (user) {
      fetchUserOrganization();
    }
  }, [user]);

  const fetchUserOrganization = async () => {
    try {
      const { data, error } = await supabase
        .from('user_roles')
        .select('organizacion_id')
        .eq('user_id', user?.id)
        .single();

      if (error && error.code !== 'PGRST116') throw error;
      
      if (data?.organizacion_id) {
        setOrganizationId(data.organizacion_id);
      } else {
        // Fallback to first organization if no role found
        const { data: orgData, error: orgError } = await supabase
          .from('organizaciones')
          .select('id')
          .limit(1)
          .single();
        
        if (!orgError && orgData) {
          setOrganizationId(orgData.id);
        }
      }
    } catch (error) {
      console.error('Error fetching organization:', error);
    }
  };

  return (
    <main className="container mx-auto py-10">
      <SEO 
        title="Diagnóstico del Sistema – CLDCI" 
        description="Herramienta de diagnóstico para probar todos los módulos del sistema CLDCI con diferentes roles de usuario." 
      />
      
      <div className="flex items-center gap-4 mb-6">
        <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10">
          <ArrowLeft className="h-4 w-4" />
        </Link>
        <div>
          <h1 className="text-3xl font-bold">Diagnóstico del Sistema</h1>
          <p className="text-muted-foreground">
            Herramienta para probar y diagnosticar todos los módulos del sistema con diferentes roles
          </p>
        </div>
      </div>

      {organizationId ? (
        <ModuleTester organizationId={organizationId} />
      ) : (
        <div className="text-center py-8 text-muted-foreground">
          Cargando información de la organización...
        </div>
      )}
    </main>
  );
};

export default Diagnostico;