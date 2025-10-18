import { ResetPasswordForm } from '@/components/auth/ResetPasswordForm';
import { SEO } from '@/components/seo/SEO';

export default function ResetPassword() {
  return (
    <>
      <SEO 
        title="Restablecer Contraseña - Sistema CLDCI"
        description="Restablece tu contraseña para acceder al sistema de gestión CLDCI"
      />
      <ResetPasswordForm />
    </>
  );
}