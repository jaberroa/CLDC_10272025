import { SEO } from "@/components/seo/SEO";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { Separator } from "@/components/ui/separator";
import { CreditCard, Mail, Phone, Settings, Webhook, Database, Globe, ArrowLeft, Users, DollarSign } from "lucide-react";
import { Link } from "react-router-dom";

const Integraciones = () => {
  return (
    <main className="container mx-auto py-10">
      <SEO 
        title="Integraciones y Personalización – CLDCI" 
        description="Configure enlaces con plataformas de pago, comunicación y adaptaciones según reglamento del CLDCI." 
      />
      
      <div className="flex items-center gap-4 mb-6">
        <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10">
          <ArrowLeft className="h-4 w-4" />
        </Link>
        <h1 className="text-3xl font-bold">Integraciones y Personalización</h1>
      </div>
      
      <div className="grid lg:grid-cols-2 gap-6">
        {/* Plataformas de Pago */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="w-5 h-5 text-module-integraciones" />
              Plataformas de Pago
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="stripe">Stripe</Label>
                <p className="text-sm text-muted-foreground">Procesamiento de pagos internacionales</p>
              </div>
              <Switch id="stripe" />
            </div>
            <Separator />
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="paypal">PayPal</Label>
                <p className="text-sm text-muted-foreground">Pagos con cuenta PayPal</p>
              </div>
              <Switch id="paypal" />
            </div>
            <Separator />
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="local-payment">Pagos Locales RD</Label>
                <p className="text-sm text-muted-foreground">Integración con bancos dominicanos</p>
              </div>
              <Switch id="local-payment" />
            </div>
            <Separator />
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="circle-transfer">Transferencia a Cuentas del Círculo</Label>
                <p className="text-sm text-muted-foreground">Transferencias entre miembros del CLDCI</p>
              </div>
              <Switch id="circle-transfer" />
            </div>
            
            <div className="mt-4 space-y-3">
              <div>
                <Label htmlFor="webhook-url">URL de Webhook</Label>
                <Input id="webhook-url" placeholder="https://api.cldci.org/webhooks/payments" />
              </div>
              <Button className="w-full">
                <Webhook className="w-4 h-4 mr-2" />
                Configurar Webhooks
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Comunicación */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Mail className="w-5 h-5 text-module-integraciones" />
              Plataformas de Comunicación
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="email-marketing">Email Marketing</Label>
                <p className="text-sm text-muted-foreground">Mailchimp, SendGrid</p>
              </div>
              <Switch id="email-marketing" />
            </div>
            <Separator />
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="sms">SMS/WhatsApp</Label>
                <p className="text-sm text-muted-foreground">Notificaciones por mensaje</p>
              </div>
              <Switch id="sms" />
            </div>
            <Separator />
            <div className="flex items-center justify-between">
              <div>
                <Label htmlFor="push-notifications">Push Notifications</Label>
                <p className="text-sm text-muted-foreground">Notificaciones web</p>
              </div>
              <Switch id="push-notifications" defaultChecked />
            </div>

            <div className="mt-4 space-y-3">
              <div>
                <Label htmlFor="smtp-server">Servidor SMTP</Label>
                <Input id="smtp-server" placeholder="smtp.cldci.org" />
              </div>
              <div>
                <Label htmlFor="api-key">API Key de Comunicación</Label>
                <Input id="api-key" type="password" placeholder="••••••••••••••••" />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Personalización CLDCI */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Settings className="w-5 h-5 text-module-integraciones" />
              Adaptaciones CLDCI
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <Label htmlFor="reglamento">Reglamento Vigente</Label>
              <p className="text-sm text-muted-foreground mb-2">Personalización según estatutos del CLDCI</p>
              <Input id="reglamento" type="file" accept=".pdf" />
            </div>
            <Separator />
            <div>
              <Label htmlFor="cuota-anual">Cuota Anual (RD$)</Label>
              <Input id="cuota-anual" type="number" placeholder="2500" />
            </div>
            <div>
              <Label htmlFor="periodo-fiscal">Período Fiscal</Label>
              <Input id="periodo-fiscal" placeholder="Enero - Diciembre" />
            </div>
            <div>
              <Label htmlFor="quorum-minimo">Quórum Mínimo (%)</Label>
              <Input id="quorum-minimo" type="number" placeholder="60" />
            </div>
          </CardContent>
        </Card>

        {/* Transferencias del Círculo */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="w-5 h-5 text-module-integraciones" />
              Transferencias del Círculo CLDCI
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="bg-primary/10 p-4 rounded-lg border border-primary/20">
              <h4 className="font-semibold mb-2 flex items-center gap-2">
                <DollarSign className="w-4 h-4" />
                Sistema de Transferencias Internas
              </h4>
              <p className="text-sm text-muted-foreground mb-3">
                Permite transferencias de fondos entre miembros del CLDCI para cuotas, servicios y beneficios mutuos.
              </p>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span>• Transferencias entre miembros</span>
                  <span className="text-green-600">✓ Disponible</span>
                </div>
                <div className="flex justify-between">
                  <span>• Pago de cuotas</span>
                  <span className="text-green-600">✓ Disponible</span>
                </div>
                <div className="flex justify-between">
                  <span>• Servicios profesionales</span>
                  <span className="text-green-600">✓ Disponible</span>
                </div>
                <div className="flex justify-between">
                  <span>• Comisión del sistema</span>
                  <span className="font-medium">2.5%</span>
                </div>
              </div>
            </div>

            <div className="space-y-3">
              <div>
                <Label htmlFor="circle-wallet">Billetera del Círculo</Label>
                <Input id="circle-wallet" placeholder="Cuenta principal para transferencias" />
              </div>
              <div>
                <Label htmlFor="commission-rate">Tasa de Comisión (%)</Label>
                <Input id="commission-rate" type="number" placeholder="2.5" step="0.1" />
              </div>
              <div>
                <Label htmlFor="daily-limit">Límite Diario (RD$)</Label>
                <Input id="daily-limit" type="number" placeholder="50000" />
              </div>
            </div>

            <div className="flex items-center justify-between p-3 bg-muted rounded-lg">
              <div>
                <Label htmlFor="auto-approve">Auto-aprobar transferencias</Label>
                <p className="text-xs text-muted-foreground">Transferencias menores a RD$5,000</p>
              </div>
              <Switch id="auto-approve" defaultChecked />
            </div>

            <Button className="w-full">
              <Users className="w-4 h-4 mr-2" />
              Configurar Sistema de Transferencias
            </Button>
          </CardContent>
        </Card>

        {/* APIs y Webhooks */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Database className="w-5 h-5 text-module-integraciones" />
              APIs y Conexiones
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="bg-muted p-4 rounded-lg">
              <h4 className="font-semibold mb-2">API REST del Sistema</h4>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span>Endpoint Base:</span>
                  <code className="bg-background px-2 py-1 rounded">api.cldci.org/v1</code>
                </div>
                <div className="flex justify-between">
                  <span>Autenticación:</span>
                  <code className="bg-background px-2 py-1 rounded">Bearer Token</code>
                </div>
                <div className="flex justify-between">
                  <span>Rate Limit:</span>
                  <code className="bg-background px-2 py-1 rounded">1000/hour</code>
                </div>
              </div>
            </div>

            <div>
              <Label htmlFor="external-api">API Externa</Label>
              <Input id="external-api" placeholder="https://external-service.com/api" />
            </div>

            <Button className="w-full" variant="outline">
              <Globe className="w-4 h-4 mr-2" />
              Generar Documentación API
            </Button>
          </CardContent>
        </Card>
      </div>

      {/* Resumen de Configuración */}
      <Card className="mt-6">
        <CardHeader>
          <CardTitle>Estado de Integraciones</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid md:grid-cols-3 gap-4">
            <div className="text-center p-4 bg-success/10 rounded-lg border border-success/20">
              <div className="text-2xl font-bold text-success">4</div>
              <div className="text-sm text-muted-foreground">Activas</div>
            </div>
            <div className="text-center p-4 bg-warning/10 rounded-lg border border-warning/20">
              <div className="text-2xl font-bold text-warning">2</div>
              <div className="text-sm text-muted-foreground">Pendientes</div>
            </div>
            <div className="text-center p-4 bg-muted rounded-lg border">
              <div className="text-2xl font-bold text-muted-foreground">1</div>
              <div className="text-sm text-muted-foreground">Inactivas</div>
            </div>
          </div>
          
          <div className="mt-4 text-sm text-muted-foreground">
            <p>✅ Sistema adaptado al reglamento CLDCI</p>
            <p>✅ Notificaciones por email configuradas</p>
            <p>✅ Transferencias del círculo habilitadas</p>
            <p>⚠️ Configuración de pagos locales pendiente</p>
          </div>
        </CardContent>
      </Card>
    </main>
  );
};

export default Integraciones;