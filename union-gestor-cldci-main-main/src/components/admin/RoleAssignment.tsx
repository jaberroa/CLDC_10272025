import { useState, useEffect } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { UserCog, Plus, Trash2, Shield, Users } from "lucide-react";
import type { Database } from "@/integrations/supabase/types";

type AppRole = Database["public"]["Enums"]["app_role"];

interface User {
  id: string;
  nombre_completo: string;
  email: string;
}

interface Organization {
  id: string;
  nombre: string;
  codigo: string;
  tipo: string;
}

interface UserRoleData {
  id: string;
  user_id: string;
  role: AppRole;
  organizacion_id: string | null;
  usuario_nombre: string;
  usuario_email: string;
  organizacion_nombre?: string;
}

interface RoleAssignmentProps {
  users: User[];
  organizations: Organization[];
  onRoleChange: () => void;
}

const ROLES: { value: AppRole; label: string; description: string }[] = [
  { value: 'admin', label: 'Administrador', description: 'Acceso completo al sistema' },
  { value: 'moderador', label: 'Moderador', description: 'Gestión de organización específica' },
  { value: 'miembro', label: 'Miembro', description: 'Acceso básico de usuario' }
];

export const RoleAssignment = ({ users, organizations, onRoleChange }: RoleAssignmentProps) => {
  const [selectedUser, setSelectedUser] = useState<string>("");
  const [selectedRole, setSelectedRole] = useState<AppRole | "">("");
  const [selectedOrganization, setSelectedOrganization] = useState<string>("");
  const [existingRoles, setExistingRoles] = useState<UserRoleData[]>([]);
  const [loading, setLoading] = useState(true);
  const [assigning, setAssigning] = useState(false);

  useEffect(() => {
    fetchExistingRoles();
  }, []);

  const fetchExistingRoles = async () => {
    try {
      // Obtener roles básicos
      const { data: roles, error: rolesError } = await supabase
        .from('user_roles')
        .select('id, user_id, role, organizacion_id');

      if (rolesError) throw rolesError;

      // Obtener información de usuarios
      const { data: profiles, error: profilesError } = await supabase
        .from('profiles')
        .select('id, nombre_completo, email');

      if (profilesError) throw profilesError;

      // Obtener información de organizaciones
      const { data: orgs, error: orgsError } = await supabase
        .from('organizaciones')
        .select('id, nombre');

      if (orgsError) throw orgsError;

      // Combinar los datos
      const enrichedRoles: UserRoleData[] = (roles || []).map(role => {
        const profile = profiles?.find(p => p.id === role.user_id);
        const organization = role.organizacion_id ? orgs?.find(o => o.id === role.organizacion_id) : null;
        
        return {
          id: role.id,
          user_id: role.user_id,
          role: role.role,
          organizacion_id: role.organizacion_id,
          usuario_nombre: profile?.nombre_completo || 'Usuario Desconocido',
          usuario_email: profile?.email || '',
          organizacion_nombre: organization?.nombre
        };
      });

      // Ordenar por nombre de usuario
      enrichedRoles.sort((a, b) => a.usuario_nombre.localeCompare(b.usuario_nombre));
      
      setExistingRoles(enrichedRoles);
    } catch (error) {
      console.error('Error fetching existing roles:', error);
      toast({
        title: "Error",
        description: "No se pudieron cargar los roles existentes",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const assignRole = async () => {
    if (!selectedUser || !selectedRole) {
      toast({
        title: "Error",
        description: "Selecciona un usuario y un rol",
        variant: "destructive",
      });
      return;
    }

    // Validar si es rol de moderador y requiere organización
    if (selectedRole === 'moderador' && !selectedOrganization) {
      toast({
        title: "Error",
        description: "Los moderadores deben estar asignados a una organización",
        variant: "destructive",
      });
      return;
    }

    setAssigning(true);
    try {
      const { error } = await supabase
        .from('user_roles')
        .insert({
          user_id: selectedUser,
          role: selectedRole as AppRole,
          organizacion_id: selectedRole === 'moderador' ? selectedOrganization : null
        });

      if (error) throw error;

      toast({
        title: "Rol asignado",
        description: "El rol ha sido asignado correctamente",
      });

      // Limpiar formulario
      setSelectedUser("");
      setSelectedRole("");
      setSelectedOrganization("");
      
      // Refrescar datos
      fetchExistingRoles();
      onRoleChange();
    } catch (error: any) {
      console.error('Error assigning role:', error);
      toast({
        title: "Error",
        description: error.message?.includes('unique') 
          ? "Este usuario ya tiene este rol asignado" 
          : "No se pudo asignar el rol",
        variant: "destructive",
      });
    } finally {
      setAssigning(false);
    }
  };

  const removeRole = async (roleId: string) => {
    try {
      const { error } = await supabase
        .from('user_roles')
        .delete()
        .eq('id', roleId);

      if (error) throw error;

      toast({
        title: "Rol removido",
        description: "El rol ha sido removido correctamente",
      });

      fetchExistingRoles();
      onRoleChange();
    } catch (error) {
      console.error('Error removing role:', error);
      toast({
        title: "Error",
        description: "No se pudo remover el rol",
        variant: "destructive",
      });
    }
  };

  const getRoleBadgeVariant = (role: AppRole) => {
    switch (role) {
      case 'admin':
        return 'default';
      case 'moderador':
        return 'secondary';
      default:
        return 'outline';
    }
  };

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="text-center py-8">Cargando gestión de roles...</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Asignar nuevo rol */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Plus className="h-5 w-5" />
            Asignar Nuevo Rol
          </CardTitle>
          <CardDescription>
            Asigna roles y permisos a los usuarios del sistema
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <Label htmlFor="user-select">Usuario</Label>
              <Select value={selectedUser} onValueChange={setSelectedUser}>
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar usuario" />
                </SelectTrigger>
                <SelectContent>
                  {users.map((user) => (
                    <SelectItem key={user.id} value={user.id}>
                      <div className="flex flex-col">
                        <span className="font-medium">{user.nombre_completo}</span>
                        <span className="text-xs text-muted-foreground">{user.email}</span>
                      </div>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div>
              <Label htmlFor="role-select">Rol</Label>
              <Select value={selectedRole} onValueChange={(value: AppRole) => setSelectedRole(value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Seleccionar rol" />
                </SelectTrigger>
                <SelectContent>
                  {ROLES.map((role) => (
                    <SelectItem key={role.value} value={role.value}>
                      <div className="flex flex-col">
                        <span className="font-medium">{role.label}</span>
                        <span className="text-xs text-muted-foreground">{role.description}</span>
                      </div>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            {selectedRole === 'moderador' && (
              <div>
                <Label htmlFor="org-select">Organización</Label>
                <Select value={selectedOrganization} onValueChange={setSelectedOrganization}>
                  <SelectTrigger>
                    <SelectValue placeholder="Seleccionar organización" />
                  </SelectTrigger>
                  <SelectContent>
                    {organizations.map((org) => (
                      <SelectItem key={org.id} value={org.id}>
                        <div className="flex flex-col">
                          <span className="font-medium">{org.nombre}</span>
                          <span className="text-xs text-muted-foreground">{org.codigo} - {org.tipo}</span>
                        </div>
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            )}
          </div>

          <div className="flex justify-end">
            <Button onClick={assignRole} disabled={assigning}>
              {assigning ? "Asignando..." : "Asignar Rol"}
            </Button>
          </div>
        </CardContent>
      </Card>

      {/* Lista de roles existentes */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Users className="h-5 w-5" />
            Roles Actuales ({existingRoles.length})
          </CardTitle>
          <CardDescription>
            Administra los roles y permisos existentes en el sistema
          </CardDescription>
        </CardHeader>
        <CardContent>
          {existingRoles.length > 0 ? (
            <div className="space-y-3">
              {existingRoles.map((roleData) => (
                <div key={roleData.id} className="flex items-center justify-between p-4 border rounded-lg">
                  <div className="flex items-center gap-4">
                    <Shield className="h-5 w-5 text-muted-foreground" />
                    <div className="space-y-1">
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{roleData.usuario_nombre}</span>
                        <Badge variant={getRoleBadgeVariant(roleData.role)}>
                          {ROLES.find(r => r.value === roleData.role)?.label || roleData.role}
                        </Badge>
                      </div>
                      <div className="flex items-center gap-2 text-sm text-muted-foreground">
                        <span>{roleData.usuario_email}</span>
                        {roleData.organizacion_nombre && (
                          <>
                            <span>•</span>
                            <span>{roleData.organizacion_nombre}</span>
                          </>
                        )}
                      </div>
                    </div>
                  </div>
                  
                  <AlertDialog>
                    <AlertDialogTrigger asChild>
                      <Button variant="outline" size="sm" className="text-destructive hover:text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>¿Remover rol?</AlertDialogTitle>
                        <AlertDialogDescription>
                          ¿Estás seguro de que deseas remover el rol "{ROLES.find(r => r.value === roleData.role)?.label}" 
                          de {roleData.usuario_nombre}? Esta acción no se puede deshacer.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Cancelar</AlertDialogCancel>
                        <AlertDialogAction
                          onClick={() => removeRole(roleData.id)}
                          className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        >
                          Remover
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8 text-muted-foreground">
              <UserCog className="h-12 w-12 mx-auto mb-4 opacity-50" />
              <p>No hay roles asignados en el sistema</p>
              <p className="text-sm">Comienza asignando roles a los usuarios</p>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
};