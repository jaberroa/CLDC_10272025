import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const activities = [
  { text: "Nuevo miembro registrado", time: "hace 2h", color: "bg-success" },
  { text: "Asamblea programada", time: "hace 4h", color: "bg-primary" },
  { text: "Cuota pendiente", time: "hace 1d", color: "bg-warning" },
];

const ActivityFeed = () => {
  return (
    <div>
      <h3 className="text-xl font-semibold text-white mb-4">Actividades Recientes</h3>
      <div className="space-y-3">
        {activities.map((activity, index) => (
          <div key={index} className="flex items-center space-x-3">
            <div className={`w-2 h-2 ${activity.color} rounded-full`} />
            <span className="text-sm text-blue-200 flex-1">{activity.text}</span>
            <span className="text-xs text-blue-300">{activity.time}</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export { ActivityFeed };