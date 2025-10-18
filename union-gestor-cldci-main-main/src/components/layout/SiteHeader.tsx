import { Link } from "react-router-dom";
import { Bell, User, LogOut } from "lucide-react";
import { Button } from "@/components/ui/button";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { useAuth } from "@/components/auth/AuthProvider";
import { toast } from "@/components/ui/use-toast";
import cldcLogo from "@/assets/cldc-logo.png";

export const SiteHeader = () => {
  const { user, signOut } = useAuth();

  const handleSignOut = async () => {
    try {
      await signOut();
      toast({
        title: "Sesión cerrada",
        description: "Has cerrado sesión correctamente.",
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "Error al cerrar sesión",
        variant: "destructive",
      });
    }
  };

  return (
    <header className="bg-gradient-primary text-primary-foreground shadow-lg">
      <div className="container mx-auto px-4 py-4 flex items-center justify-between">
        <Link to="/" className="flex items-center space-x-3">
          <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center p-1.5">
            <img 
              src={cldcLogo} 
              alt="CÍRCULO DE LOCUTORES COLEGIADOS, INC." 
              className="w-full h-full object-contain"
              loading="lazy"
            />
          </div>
          <div className="flex flex-col">
            <h1 className="text-lg font-bold leading-tight">CÍRCULO DE LOCUTORES COLEGIADOS, INC.</h1>
            <p className="text-xs text-primary-foreground/80 leading-tight">Prototipo CLDCI - Portal de Miembros</p>
          </div>
        </Link>
        <div className="flex items-center space-x-4">
          <Bell className="w-5 h-5 cursor-pointer hover:text-primary-foreground/80 transition-colors" />
          {user && (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="sm" className="text-primary-foreground hover:text-primary-foreground/80">
                  <div className="flex items-center space-x-2">
                    <User className="w-4 h-4" />
                    <span className="text-sm hidden md:inline">{user.email}</span>
                  </div>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-56">
                <DropdownMenuLabel>Mi Cuenta</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem asChild>
                  <Link to="/perfil" className="w-full flex items-center">
                    <User className="w-4 h-4 mr-2" />
                    Ver Perfil
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem 
                  onClick={handleSignOut}
                  className="text-red-600 focus:text-red-600"
                >
                  <LogOut className="w-4 h-4 mr-2" />
                  Cerrar Sesión
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          )}
        </div>
      </div>
    </header>
  );
};