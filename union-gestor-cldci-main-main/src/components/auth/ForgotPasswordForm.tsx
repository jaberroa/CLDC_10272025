import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/hooks/use-toast';
import { useAuth } from './AuthProvider';
import { Loader2, ArrowLeft, Mail } from 'lucide-react';

interface ForgotPasswordFormProps {
  onBack: () => void;
}

export const ForgotPasswordForm = ({ onBack }: ForgotPasswordFormProps) => {
  const { resetPassword } = useAuth();
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);
  const [email, setEmail] = useState('');
  const [emailSent, setEmailSent] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      await resetPassword(email);
      setEmailSent(true);
      toast({
        title: "¡Correo enviado!",
        description: "Revisa tu bandeja de entrada para restablecer tu contraseña.",
      });
    } catch (error: any) {
      toast({
        title: "Error",
        description: error.message || "Error al enviar el correo de recuperación",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  if (emailSent) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 p-4">
        <Card className="w-full max-w-md bg-white/10 backdrop-blur-sm border border-blue-300/20">
          <CardHeader className="text-center">
            <div className="mx-auto mb-4 w-12 h-12 rounded-full bg-yellow-400/20 flex items-center justify-center">
              <Mail className="h-6 w-6 text-yellow-400" />
            </div>
            <CardTitle className="text-2xl font-bold text-white">
              Correo Enviado
            </CardTitle>
            <p className="text-blue-200">
              Te hemos enviado un enlace para restablecer tu contraseña
            </p>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-center space-y-2">
              <p className="text-sm text-blue-200">
                Revisa tu bandeja de entrada en:
              </p>
              <p className="font-semibold text-white">{email}</p>
              <p className="text-xs text-blue-300">
                Si no ves el correo, revisa tu carpeta de spam
              </p>
            </div>
            
            <div className="flex flex-col space-y-2">
              <Button 
                onClick={() => {
                  setEmailSent(false);
                  setEmail('');
                }}
                variant="outline"
                className="w-full bg-white/10 border-blue-300/20 text-white hover:bg-white/20"
              >
                Intentar con otro correo
              </Button>
              <Button onClick={onBack} variant="ghost" className="w-full text-blue-200 hover:text-white hover:bg-white/10">
                <ArrowLeft className="mr-2 h-4 w-4" />
                Volver al inicio de sesión
              </Button>
            </div>
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
            Recuperar Contraseña
          </CardTitle>
          <p className="text-center text-blue-200">
            Ingresa tu correo electrónico para recibir un enlace de recuperación
          </p>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="email" className="text-white">Correo Electrónico</Label>
              <Input
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="tu@correo.com"
                className="bg-white/10 border-blue-300/20 text-white placeholder:text-blue-200"
                required
              />
            </div>
            
            <Button type="submit" className="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-semibold" disabled={loading || !email}>
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Enviar Enlace de Recuperación
            </Button>
            
            <Button 
              type="button" 
              onClick={onBack} 
              variant="ghost" 
              className="w-full text-blue-200 hover:text-white hover:bg-white/10"
            >
              <ArrowLeft className="mr-2 h-4 w-4" />
              Volver al inicio de sesión
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};