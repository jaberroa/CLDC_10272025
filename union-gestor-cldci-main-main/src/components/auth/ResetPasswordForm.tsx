import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/hooks/use-toast';
import { useAuth } from './AuthProvider';
import { Loader2, CheckCircle2 } from 'lucide-react';
import { supabase } from '@/integrations/supabase/client';
import { useNavigate } from 'react-router-dom';

export const ResetPasswordForm = () => {
  const { updatePassword } = useAuth();
  const { toast } = useToast();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [formData, setFormData] = useState({
    password: '',
    confirmPassword: '',
  });

  useEffect(() => {
    // Listen for auth state changes to handle the reset password flow
    const { data: { subscription } } = supabase.auth.onAuthStateChange(
      async (event) => {
        if (event === 'PASSWORD_RECOVERY') {
          // User is in password recovery mode
          toast({
            title: "Recuperación de contraseña",
            description: "Ahora puedes establecer tu nueva contraseña.",
          });
        }
      }
    );

    return () => subscription.unsubscribe();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (formData.password !== formData.confirmPassword) {
      toast({
        title: "Error",
        description: "Las contraseñas no coinciden",
        variant: "destructive",
      });
      return;
    }

    if (formData.password.length < 6) {
      toast({
        title: "Error",
        description: "La contraseña debe tener al menos 6 caracteres",
        variant: "destructive",
      });
      return;
    }

    setLoading(true);
    
    try {
      await updatePassword(formData.password);
      setSuccess(true);
      toast({
        title: "¡Contraseña actualizada!",
        description: "Tu contraseña ha sido cambiada exitosamente.",
      });
      
      // Redirect to main page after 2 seconds
      setTimeout(() => {
        navigate('/');
      }, 2000);
    } catch (error: any) {
      toast({
        title: "Error",
        description: error.message || "Error al actualizar la contraseña",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  if (success) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 p-4">
        <Card className="w-full max-w-md bg-white/10 backdrop-blur-sm border border-blue-300/20">
          <CardHeader className="text-center">
            <div className="mx-auto mb-4 w-12 h-12 rounded-full bg-green-400/20 flex items-center justify-center">
              <CheckCircle2 className="h-6 w-6 text-green-400" />
            </div>
            <CardTitle className="text-2xl font-bold text-white">
              ¡Contraseña Actualizada!
            </CardTitle>
            <p className="text-blue-200">
              Tu contraseña ha sido cambiada exitosamente
            </p>
          </CardHeader>
          <CardContent className="text-center">
            <p className="text-sm text-blue-200">
              Serás redirigido automáticamente...
            </p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 p-4">
      <Card className="w-full max-w-md bg-white/10 backdrop-blur-sm border border-blue-300/20">
        <CardHeader>
          <CardTitle className="text-center text-2xl font-bold text-white">
            Nueva Contraseña
          </CardTitle>
          <p className="text-center text-blue-200">
            Ingresa tu nueva contraseña
          </p>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="password" className="text-white">Nueva Contraseña</Label>
              <Input
                id="password"
                name="password"
                type="password"
                value={formData.password}
                onChange={handleChange}
                placeholder="Mínimo 6 caracteres"
                className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                required
                minLength={6}
              />
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="confirmPassword" className="text-white">Confirmar Nueva Contraseña</Label>
              <Input
                id="confirmPassword"
                name="confirmPassword"
                type="password"
                value={formData.confirmPassword}
                onChange={handleChange}
                placeholder="Repite la contraseña"
                className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                required
                minLength={6}
              />
            </div>
            
            <div className="text-xs text-blue-200 space-y-1">
              <p>Tu contraseña debe:</p>
              <ul className="list-disc list-inside ml-2 space-y-1">
                <li>Tener al menos 6 caracteres</li>
                <li>Contener una combinación de letras y números</li>
              </ul>
            </div>
            
            <Button 
              type="submit" 
              className="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold" 
              disabled={loading || !formData.password || !formData.confirmPassword}
            >
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Actualizar Contraseña
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};