import { Card, CardContent } from "@/components/ui/card";
import { LucideIcon } from "lucide-react";

interface StatsCardProps {
  title: string;
  value: string;
  icon: LucideIcon;
  colorClass: string;
}

const StatsCard = ({ title, value, icon: Icon, colorClass }: StatsCardProps) => {
  return (
    <Card className={`${colorClass} border-opacity-20`}>
      <CardContent className="p-4">
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm font-medium opacity-80">{title}</p>
            <p className="text-2xl font-bold">{value}</p>
          </div>
          <Icon className="w-8 h-8 opacity-60" />
        </div>
      </CardContent>
    </Card>
  );
};

export { StatsCard };