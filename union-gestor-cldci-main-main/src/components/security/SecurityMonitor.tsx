import { useEffect } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { useAuth } from '@/components/auth/AuthProvider';
import { useToast } from '@/hooks/use-toast';

interface SecurityEvent {
  type: 'sensitive_data_access' | 'login_attempt' | 'unauthorized_access';
  details: string;
  timestamp: Date;
  userId?: string;
}

export const SecurityMonitor = () => {
  const { user } = useAuth();
  const { toast } = useToast();

  useEffect(() => {
    if (!user) return;

    // Log user session start
    logSecurityEvent({
      type: 'login_attempt',
      details: `User ${user.email} logged in successfully`,
      timestamp: new Date(),
      userId: user.id
    });

    // Monitor for suspicious activities
    const monitorUserActivity = () => {
      // Check for rapid page navigation (potential bot activity)
      let pageViewCount = 0;
      const pageViewWindow = 60000; // 1 minute

      const handlePageView = () => {
        pageViewCount++;
        if (pageViewCount > 10) {
          logSecurityEvent({
            type: 'unauthorized_access',
            details: 'Suspicious rapid page navigation detected',
            timestamp: new Date(),
            userId: user.id
          });
          
          toast({
            title: "Actividad Sospechosa Detectada",
            description: "Se ha detectado navegación rápida inusual",
            variant: "destructive",
          });
        }
      };

      // Reset counter every minute
      const resetCounter = setInterval(() => {
        pageViewCount = 0;
      }, pageViewWindow);

      // Listen for navigation events
      const originalPushState = window.history.pushState;
      window.history.pushState = function(...args) {
        handlePageView();
        return originalPushState.apply(this, args);
      };

      return () => {
        clearInterval(resetCounter);
        window.history.pushState = originalPushState;
      };
    };

    const cleanup = monitorUserActivity();

    return cleanup;
  }, [user, toast]);

  return null; // This is a monitoring component with no UI
};

// Security event logging utility
const logSecurityEvent = async (event: SecurityEvent) => {
  try {
    console.log('[Security Event]', event);
    
    // Store security events in the new audit log table
    await supabase.from('security_audit_log').insert({
      user_id: event.userId || null,
      action: event.type,
      resource_type: 'security_monitor',
      success: true,
      user_agent: navigator.userAgent,
      additional_data: {
        details: event.details,
        timestamp: event.timestamp.toISOString()
      }
    });
  } catch (error) {
    console.error('Failed to log security event:', error);
    // Continue with fallback logging
    await storeAuditLog({
      event_type: event.type,
      details: event.details,
      user_id: event.userId
    });
  }
};

// Fallback audit logging for critical events
const storeAuditLog = async (auditData: any) => {
  try {
    // Enhanced audit logging with proper database storage
    await supabase.from('security_audit_log').insert({
      user_id: auditData.user_id || null,
      action: auditData.event_type || 'unknown',
      resource_type: 'audit_fallback',
      success: true,
      additional_data: auditData
    });
  } catch (error) {
    console.error('Failed to store audit log:', error);
  }
};

export { logSecurityEvent };