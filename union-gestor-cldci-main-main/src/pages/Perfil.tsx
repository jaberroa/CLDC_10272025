import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { FileUploader } from "@/components/ui/file-uploader";
import { useAuth } from "@/components/auth/AuthProvider";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/components/ui/use-toast";
import { SEO } from "@/components/seo/SEO";
import { StructuredData } from "@/components/seo/StructuredData";
import { generateBreadcrumbSchema, generatePersonSchema } from "@/lib/seo/structured-data";
import { RoleAssignment } from "@/components/admin/RoleAssignment";
import { 
  User, 
  Mail, 
  Phone, 
  Calendar, 
  Shield, 
  Settings, 
  Camera,
  Key,
  Bell,
  Database,
  ArrowLeft,
  UserCog
} from "lucide-react";

interface UserProfile {
  id: string;
  email: string;
  nombre_completo: string;
  telefono: string | null;
  avatar_url: string | null;
  created_at: string;
  updated_at: string;
}

interface UserRole {
  role: string;
  organizacion_id: string | null;
  organizaciones?: {
    nombre: string;
  };
}

interface Organization {
  id: string;
  nombre: string;
  codigo: string;
  tipo: string;
}

const Perfil = () => {
  const navigate = useNavigate();
  const { user, signOut } = useAuth();
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [userRoles, setUserRoles] = useState<UserRole[]>([]);
  const [loading, setLoading] = useState(true);
  const [updating, setUpdating] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false);
  const [organizations, setOrganizations] = useState<Organization[]>([]);
  const [allUsers, setAllUsers] = useState<any[]>([]);
  
  // Estados para formularios
  const [nombreCompleto, setNombreCompleto] = useState("");
  const [telefono, setTelefono] = useState("");
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  useEffect(() => {
    if (user) {
      fetchProfile();
      fetchUserRoles();
      checkAdminStatus();
    }
  }, [user]);

  const checkAdminStatus = async () => {
    try {
      const { data, error } = await supabase.rpc('has_role', {
        _user_id: user?.id,
        _role: 'admin'
      });
      
      if (!error) {
        setIsAdmin(data);
        if (data) {
          fetchOrganizations();
          fetchAllUsers();
        }
      }
    } catch (error) {
      console.error('Error checking admin status:', error);
    }
  };

  const fetchOrganizations = async () => {
    try {
      const { data, error } = await supabase
        .from('organizaciones')
        .select('id, nombre, codigo, tipo')
        .order('nombre');
      
      if (!error && data) {
        setOrganizations(data);
      }
    } catch (error) {
      console.error('Error fetching organizations:', error);
    }
  };

  const fetchAllUsers = async () => {
    try {
      const { data, error } = await supabase
        .from('profiles')
        .select(`
          id,
          nombre_completo,
          email
        `)
        .order('nombre_completo');
      
      if (!error && data) {
        setAllUsers(data);
      }
    } catch (error) {
      console.error('Error fetching users:', error);
    }
  };

  const fetchProfile = async () => {
    try {
      const { data, error } = await supabase
        .from('profiles')
        .select('*')
        .eq('id', user?.id)
        .single();

      if (error) throw error;
      
      setProfile(data);
      setNombreCompleto(data.nombre_completo || "");
      setTelefono(data.telefono || "");
    } catch (error) {
      console.error('Error fetching profile:', error);
      toast({
        title: "Error",
        description: "No se pudo cargar el perfil del usuario",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const fetchUserRoles = async () => {
    try {
      const { data, error } = await supabase
        .from('user_roles')
        .select(`
          role,
          organizacion_id
        `)
        .eq('user_id', user?.id);

      if (error) throw error;
      
      // Obtener nombres de organizaciones por separado
      const rolesWithOrgs = await Promise.all(
        (data || []).map(async (role) => {
          if (role.organizacion_id) {
            const { data: orgData } = await supabase
              .from('organizaciones')
              .select('nombre')
              .eq('id', role.organizacion_id)
              .single();
            
            return {
              ...role,
              organizaciones: orgData
            };
          }
          return role;
        })
      );
      
      setUserRoles(rolesWithOrgs);
    } catch (error) {
      console.error('Error fetching user roles:', error);
    }
  };

  const updateProfile = async () => {
    if (!user) return;
    
    setUpdating(true);
    try {
      const { error } = await supabase
        .from('profiles')
        .update({
          nombre_completo: nombreCompleto,
          telefono: telefono,
          updated_at: new Date().toISOString(),
        })
        .eq('id', user.id);

      if (error) throw error;

      toast({
        title: "Perfil actualizado",
        description: "Tu información personal ha sido actualizada correctamente",
      });
      
      fetchProfile();
    } catch (error) {
      console.error('Error updating profile:', error);
      toast({
        title: "Error",
        description: "No se pudo actualizar el perfil",
        variant: "destructive",
      });
    } finally {
      setUpdating(false);
    }
  };

  const updatePassword = async () => {
    if (newPassword !== confirmPassword) {
      toast({
        title: "Error",
        description: "Las contraseñas no coinciden",
        variant: "destructive",
      });
      return;
    }

    if (newPassword.length < 6) {
      toast({
        title: "Error",
        description: "La contraseña debe tener al menos 6 caracteres",
        variant: "destructive",
      });
      return;
    }

    try {
      const { error } = await supabase.auth.updateUser({
        password: newPassword
      });

      if (error) throw error;

      toast({
        title: "Contraseña actualizada",
        description: "Tu contraseña ha sido cambiada correctamente",
      });
      
      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");
    } catch (error) {
      console.error('Error updating password:', error);
      toast({
        title: "Error",
        description: "No se pudo actualizar la contraseña",
        variant: "destructive",
      });
    }
  };

  const handleAvatarUpload = async (files: any[]) => {
    if (files.length > 0 && user) {
      try {
        const file = files[0];
        // Actualizar la URL del avatar en la base de datos
        const { error } = await supabase
          .from('profiles')
          .update({
            avatar_url: file.url,
            updated_at: new Date().toISOString(),
          })
          .eq('id', user.id);

        if (error) throw error;

        toast({
          title: "Avatar actualizado",
          description: "Tu foto de perfil ha sido actualizada correctamente",
        });
        
        // Recargar el perfil para mostrar la nueva imagen
        fetchProfile();
      } catch (error) {
        console.error('Error updating avatar:', error);
        toast({
          title: "Error",
          description: "No se pudo actualizar la foto de perfil",
          variant: "destructive",
        });
      }
    }
  };

  const handleSignOut = async () => {
    try {
      await signOut();
      toast({
        title: "Sesión cerrada",
        description: "Has cerrado sesión correctamente",
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "Error al cerrar sesión",
        variant: "destructive",
      });
    }
  };

  if (loading) {
    return (
      <div className="container mx-auto p-6">
        <div className="text-center py-8">Cargando perfil...</div>
      </div>
    );
  }

  if (!profile) {
    return (
      <div className="container mx-auto p-6">
        <div className="text-center py-8">No se encontró información del perfil</div>
      </div>
    );
  }

  const breadcrumbData = generateBreadcrumbSchema([
    { name: "Inicio", item: "https://cldci.com/" },
    { name: "Mi Perfil", item: "https://cldci.com/perfil" }
  ]);

  const personData = generatePersonSchema({
    name: profile.nombre_completo,
    email: profile.email
  });

  return (
    <div className="container mx-auto p-6 space-y-6">
      <SEO 
        title="Mi Perfil | CLDCI"
        description="Gestiona tu perfil de usuario, configuración de cuenta y preferencias en el sistema CLDCI."
      />
      <StructuredData data={breadcrumbData} />
      <StructuredData data={personData} />
      
      <div className="space-y-4">
        <div className="flex items-center gap-4">
          <Button 
            variant="outline" 
            size="sm"
            onClick={() => navigate(-1)}
            className="flex items-center gap-2"
          >
            <ArrowLeft className="h-4 w-4" />
            Volver
          </Button>
        </div>
        
        <div className="space-y-2">
          <h1 className="text-3xl font-bold">Mi Perfil</h1>
          <p className="text-muted-foreground">
            Gestiona tu información personal y configuración de cuenta
          </p>
        </div>
      </div>

      <Tabs defaultValue="perfil" className="space-y-6">
        <TabsList className={`grid w-full ${isAdmin ? 'grid-cols-5' : 'grid-cols-4'}`}>
          <TabsTrigger value="perfil">Información Personal</TabsTrigger>
          <TabsTrigger value="seguridad">Seguridad</TabsTrigger>
          <TabsTrigger value="roles">Roles y Permisos</TabsTrigger>
          {isAdmin && (
            <TabsTrigger value="gestion-roles">Gestión de Roles</TabsTrigger>
          )}
          <TabsTrigger value="configuracion">Configuración</TabsTrigger>
        </TabsList>

        <TabsContent value="perfil" className="space-y-6">
          <div className="grid lg:grid-cols-3 gap-6">
            {/* Información básica */}
            <Card className="lg:col-span-2">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Información Personal
                </CardTitle>
                <CardDescription>
                  Actualiza tu información personal y datos de contacto
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="email">Email</Label>
                    <Input
                      id="email"
                      type="email"
                      value={profile.email}
                      disabled
                      className="bg-muted"
                    />
                    <p className="text-xs text-muted-foreground mt-1">
                      El email no se puede cambiar
                    </p>
                  </div>
                  <div>
                    <Label htmlFor="nombre">Nombre Completo</Label>
                    <Input
                      id="nombre"
                      value={nombreCompleto}
                      onChange={(e) => setNombreCompleto(e.target.value)}
                      placeholder="Tu nombre completo"
                    />
                  </div>
                </div>
                
                <div>
                  <Label htmlFor="telefono">Teléfono</Label>
                  <Input
                    id="telefono"
                    value={telefono}
                    onChange={(e) => setTelefono(e.target.value)}
                    placeholder="+1 809 XXX-XXXX"
                  />
                </div>

                <Separator />

                <div className="flex items-center gap-4 text-sm text-muted-foreground">
                  <div className="flex items-center gap-1">
                    <Calendar className="h-4 w-4" />
                    Creado: {new Date(profile.created_at).toLocaleDateString()}
                  </div>
                  <div className="flex items-center gap-1">
                    <Database className="h-4 w-4" />
                    Actualizado: {new Date(profile.updated_at).toLocaleDateString()}
                  </div>
                </div>

                <div className="flex justify-end">
                  <Button onClick={updateProfile} disabled={updating}>
                    {updating ? "Actualizando..." : "Guardar Cambios"}
                  </Button>
                </div>
              </CardContent>
            </Card>

            {/* Avatar y acciones rápidas */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Camera className="h-5 w-5" />
                  Foto de Perfil
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="flex flex-col items-center space-y-4">
                  <Avatar className="w-24 h-24">
                    <AvatarImage src={profile.avatar_url || ""} alt={profile.nombre_completo} />
                    <AvatarFallback className="text-lg">
                      {profile.nombre_completo.split(' ').map(n => n[0]).join('').slice(0, 2)}
                    </AvatarFallback>
                  </Avatar>
                  
                  <div className="w-full">
                    <FileUploader
                      maxFiles={1}
                      maxSizePerFile={5}
                      acceptedTypes={['.jpg', '.jpeg', '.png', '.gif']}
                      bucketName="fotos"
                      folderPath="avatars"
                      onFilesUploaded={handleAvatarUpload}
                    />
                  </div>
                </div>

                <Separator />

                <div className="space-y-2">
                  <Button 
                    variant="outline" 
                    className="w-full"
                    onClick={handleSignOut}
                  >
                    Cerrar Sesión
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="seguridad" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Key className="h-5 w-5" />
                Cambiar Contraseña
              </CardTitle>
              <CardDescription>
                Actualiza tu contraseña para mantener tu cuenta segura
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="current-password">Contraseña Actual</Label>
                <Input
                  id="current-password"
                  type="password"
                  value={currentPassword}
                  onChange={(e) => setCurrentPassword(e.target.value)}
                  placeholder="Tu contraseña actual"
                />
              </div>
              
              <div>
                <Label htmlFor="new-password">Nueva Contraseña</Label>
                <Input
                  id="new-password"
                  type="password"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  placeholder="Mínimo 6 caracteres"
                />
              </div>
              
              <div>
                <Label htmlFor="confirm-password">Confirmar Nueva Contraseña</Label>
                <Input
                  id="confirm-password"
                  type="password"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  placeholder="Repite la nueva contraseña"
                />
              </div>

              <div className="flex justify-end">
                <Button 
                  onClick={updatePassword}
                  disabled={!newPassword || !confirmPassword}
                >
                  Cambiar Contraseña
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="roles" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Roles y Permisos
              </CardTitle>
              <CardDescription>
                Roles asignados y permisos en el sistema
              </CardDescription>
            </CardHeader>
            <CardContent>
              {userRoles.length > 0 ? (
                <div className="space-y-3">
                  {userRoles.map((roleData, index) => (
                    <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                      <div className="space-y-1">
                        <div className="flex items-center gap-2">
                          <Badge variant={roleData.role === 'admin' ? 'default' : 'secondary'}>
                            {roleData.role}
                          </Badge>
                          {roleData.organizaciones && (
                            <span className="text-sm text-muted-foreground">
                              {roleData.organizaciones.nombre}
                            </span>
                          )}
                        </div>
                        <p className="text-xs text-muted-foreground">
                          {roleData.role === 'admin' && 'Acceso completo al sistema'}
                          {roleData.role === 'moderador' && 'Gestión de organización específica'}
                          {roleData.role === 'user' && 'Acceso básico de usuario'}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8 text-muted-foreground">
                  <Shield className="h-12 w-12 mx-auto mb-4 opacity-50" />
                  <p>No tienes roles asignados actualmente</p>
                  <p className="text-sm">Contacta al administrador para solicitar permisos</p>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {isAdmin && (
          <TabsContent value="gestion-roles" className="space-y-6">
            <RoleAssignment 
              users={allUsers}
              organizations={organizations}
              onRoleChange={() => {
                fetchUserRoles();
                fetchAllUsers();
              }}
            />
          </TabsContent>
        )}

        <TabsContent value="configuracion" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="h-5 w-5" />
                Configuración de Cuenta
              </CardTitle>
              <CardDescription>
                Preferencias y configuración general
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="space-y-1">
                  <div className="flex items-center gap-2">
                    <Bell className="h-4 w-4" />
                    <span className="font-medium">Notificaciones por Email</span>
                  </div>
                  <p className="text-sm text-muted-foreground">
                    Recibir notificaciones importantes por correo electrónico
                  </p>
                </div>
                <input type="checkbox" className="toggle" defaultChecked />
              </div>

              <div className="flex items-center justify-between p-3 border rounded-lg">
                <div className="space-y-1">
                  <div className="flex items-center gap-2">
                    <Mail className="h-4 w-4" />
                    <span className="font-medium">Boletín Informativo</span>
                  </div>
                  <p className="text-sm text-muted-foreground">
                    Recibir actualizaciones y noticias del CLDCI
                  </p>
                </div>
                <input type="checkbox" className="toggle" />
              </div>

              <Separator />

              <div className="text-center py-4">
                <p className="text-sm text-muted-foreground mb-4">
                  ¿Necesitas ayuda con tu cuenta?
                </p>
                <Button variant="outline">
                  Contactar Soporte
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default Perfil;