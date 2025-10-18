import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const distributionData = [
  { name: "Santo Domingo", count: 456, percentage: 75 },
  { name: "Santiago", count: 234, percentage: 50 },
  { name: "La Vega", count: 156, percentage: 33 },
];

const DistributionChart = () => {
  return (
    <div>
      <h3 className="text-xl font-semibold text-white mb-4">Distribución por Provincias</h3>
      <div className="space-y-3">
        {distributionData.map((item) => (
          <div key={item.name} className="flex justify-between items-center">
            <span className="text-sm text-blue-200">{item.name}</span>
            <div className="flex items-center">
              <div className="w-24 h-2 bg-white/20 rounded-full mr-2">
                <div 
                  className="h-full bg-yellow-400 rounded-full" 
                  style={{ width: `${item.percentage}%` }}
                />
              </div>
              <span className="text-sm font-medium text-white">{item.count}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export { DistributionChart };