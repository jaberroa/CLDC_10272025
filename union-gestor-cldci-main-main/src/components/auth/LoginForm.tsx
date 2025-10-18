import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useToast } from '@/hooks/use-toast';
import { useAuth } from './AuthProvider';
import { ForgotPasswordForm } from './ForgotPasswordForm';
import { Loader2 } from 'lucide-react';
import cldcLogo from '@/assets/cldc-logo.png';

export const LoginForm = () => {
  const { signIn, signUp } = useAuth();
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);
  const [showForgotPassword, setShowForgotPassword] = useState(false);
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    confirmPassword: '',
    nombre: '',
  });

  if (showForgotPassword) {
    return <ForgotPasswordForm onBack={() => setShowForgotPassword(false)} />;
  }

  const handleSignIn = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await signIn(formData.email, formData.password);
      toast({
        title: "¡Bienvenido!",
        description: "Has iniciado sesión correctamente.",
      });
    } catch (error: any) {
      toast({
        title: "Error de autenticación",
        description: error.message || "Error al iniciar sesión",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSignUp = async (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.password !== formData.confirmPassword) {
      toast({
        title: "Error",
        description: "Las contraseñas no coinciden",
        variant: "destructive",
      });
      return;
    }
    
    setLoading(true);
    try {
      await signUp(formData.email, formData.password, {
        full_name: formData.nombre,
      });
      toast({
        title: "¡Registro exitoso!",
        description: "Revisa tu email para confirmar tu cuenta.",
      });
    } catch (error: any) {
      toast({
        title: "Error de registro",
        description: error.message || "Error al crear la cuenta",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Test credentials removed for security in production

  return (
    <div className="min-h-screen flex flex-col bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <div className="flex-1 flex items-center justify-center p-4">
        <div className="w-full max-w-lg space-y-8">
          {/* Entity Title */}
          <div className="text-center">
            <h1 className="text-3xl font-bold text-white mb-2">
              Círculo de Locutores Dominicanos Colegiados, Inc.
            </h1>
          </div>

          {/* Logo and Welcome Section */}
          <div className="text-center space-y-6">
            <div className="flex justify-center">
              <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-blue-300/20">
                <img 
                  src={cldcLogo} 
                  alt="CLDC Logo" 
                  className="w-24 h-24 object-contain"
                />
              </div>
            </div>
            <div className="space-y-2">
              <h2 className="text-2xl font-bold text-yellow-400">
                Bienvenido a la Plataforma de Gestión Institucional
              </h2>
            </div>
          </div>

        {/* Login Card */}
        <Card className="w-full bg-white/10 backdrop-blur-sm border border-blue-300/20">
          <CardHeader className="text-center">
            <CardTitle className="text-xl text-white">Acceso al Sistema</CardTitle>
          </CardHeader>
        <CardContent>
          <Tabs defaultValue="signin" className="w-full">
            <TabsList className="grid w-full grid-cols-2 bg-blue-800/50 border border-blue-300/20">
              <TabsTrigger value="signin" className="data-[state=active]:bg-yellow-400 data-[state=active]:text-blue-900">Iniciar Sesión</TabsTrigger>
              <TabsTrigger value="signup" className="data-[state=active]:bg-yellow-400 data-[state=active]:text-blue-900">Registrarse</TabsTrigger>
            </TabsList>
            
            <TabsContent value="signin">
              <form onSubmit={handleSignIn} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="signin-email" className="text-white">Email</Label>
                  <Input
                    id="signin-email"
                    name="email"
                    type="email"
                    value={formData.email}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signin-password" className="text-white">Contraseña</Label>
                  <Input
                    id="signin-password"
                    name="password"
                    type="password"
                    value={formData.password}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <Button type="submit" className="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold" disabled={loading}>
                  {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Iniciar Sesión
                </Button>
                
                <div className="text-center">
                  <Button
                    type="button"
                    variant="link"
                    className="text-sm text-blue-200 hover:text-yellow-400"
                    onClick={() => setShowForgotPassword(true)}
                  >
                    ¿Olvidaste tu contraseña?
                  </Button>
                </div>
              </form>
            </TabsContent>
            
            <TabsContent value="signup">
              <form onSubmit={handleSignUp} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="signup-nombre" className="text-white">Nombre Completo</Label>
                  <Input
                    id="signup-nombre"
                    name="nombre"
                    value={formData.nombre}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signup-email" className="text-white">Email</Label>
                  <Input
                    id="signup-email"
                    name="email"
                    type="email"
                    value={formData.email}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="signup-password" className="text-white">Contraseña</Label>
                  <Input
                    id="signup-password"
                    name="password"
                    type="password"
                    value={formData.password}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="confirm-password" className="text-white">Confirmar Contraseña</Label>
                  <Input
                    id="confirm-password"
                    name="confirmPassword"
                    type="password"
                    value={formData.confirmPassword}
                    onChange={handleChange}
                    className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                    required
                  />
                </div>
                <Button type="submit" className="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold" disabled={loading}>
                  {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Registrarse
                </Button>
              </form>
            </TabsContent>
          </Tabs>

          {/* Test user credentials section removed for security in production */}
        </CardContent>
        </Card>
        </div>
      </div>
      
      {/* Footer */}
      <footer className="bg-black/20 backdrop-blur-sm border-t border-blue-300/20 py-4">
        <div className="container mx-auto text-center">
          <p className="text-sm text-blue-200">
            © {new Date().getFullYear()} Círculo de Locutores Dominicanos Colegiados, Inc. — CLDCI
          </p>
        </div>
      </footer>
    </div>
  );
};