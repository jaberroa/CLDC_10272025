import QRCode from "react-qr-code";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type Member = {
  id: string;
  nombre: string;
  seccional: string;
  cargo?: string;
};

export const MemberCard = ({ id, nombre, seccional, cargo }: Member) => {
  return (
    <Card className="max-w-sm shadow-[var(--shadow-elev)]">
      <CardHeader>
        <CardTitle className="text-lg">Carnet Digital</CardTitle>
      </CardHeader>
      <CardContent className="grid grid-cols-2 gap-4 items-center">
        <div>
          <p className="font-semibold">{nombre}</p>
          <p className="text-sm text-muted-foreground">{seccional}</p>
          {cargo && <p className="text-sm text-muted-foreground">{cargo}</p>}
        </div>
        <div className="justify-self-end p-2 bg-card rounded-md">
          <QRCode value={`cldci:member:${id}`} size={96} bgColor="transparent" fgColor="hsl(var(--foreground))" />
        </div>
      </CardContent>
    </Card>
  );
};
