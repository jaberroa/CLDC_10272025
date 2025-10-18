import { Card } from "@/components/ui/card";
import { LucideIcon } from "lucide-react";
import { Link } from "react-router-dom";

interface ModuleCardProps {
  icon: LucideIcon;
  title: string;
  to: string;
  colorClass: string;
}

const ModuleCard = ({ icon: Icon, title, to, colorClass }: ModuleCardProps) => {
  return (
    <Card className="hover:shadow-elevated transition-all duration-200 hover:-translate-y-1">
      <Link to={to} className={`${colorClass} text-white p-4 rounded-lg hover:opacity-90 transition-opacity flex flex-col items-center text-center block`}>
        <Icon className="w-6 h-6 mx-auto mb-2" />
        <div className="text-sm font-medium">{title}</div>
      </Link>
    </Card>
  );
};

export { ModuleCard };