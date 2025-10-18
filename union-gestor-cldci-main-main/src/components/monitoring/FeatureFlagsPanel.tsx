/**
 * Feature Flags Management Panel
 * Admin interface for managing feature flags
 */

import { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Settings, RotateCcw, Download, Upload } from 'lucide-react';
import {
  getAllFeatureFlags,
  toggleFeatureFlag,
  resetFeatureFlags,
  exportFeatureFlags,
  importFeatureFlags,
  type FeatureFlag,
} from '@/lib/monitoring/feature-flags';
import { toast } from '@/hooks/use-toast';

export const FeatureFlagsPanel = () => {
  const [flags, setFlags] = useState<FeatureFlag[]>([]);

  const loadFlags = () => {
    setFlags(getAllFeatureFlags());
  };

  useEffect(() => {
    loadFlags();
  }, []);

  const handleToggle = (flagKey: string) => {
    const newState = toggleFeatureFlag(flagKey);
    loadFlags();
    
    toast({
      title: 'Feature Flag Updated',
      description: `Feature has been ${newState ? 'enabled' : 'disabled'}`,
    });
  };

  const handleReset = () => {
    resetFeatureFlags();
    loadFlags();
    
    toast({
      title: 'Feature Flags Reset',
      description: 'All feature flags have been reset to default values',
    });
  };

  const handleExport = () => {
    const json = exportFeatureFlags();
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'feature-flags.json';
    a.click();
    URL.revokeObjectURL(url);
    
    toast({
      title: 'Feature Flags Exported',
      description: 'Feature flags have been downloaded as JSON',
    });
  };

  const handleImport = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = async (e) => {
      const file = (e.target as HTMLInputElement).files?.[0];
      if (!file) return;
      
      const text = await file.text();
      if (importFeatureFlags(text)) {
        loadFlags();
        toast({
          title: 'Feature Flags Imported',
          description: 'Feature flags have been successfully imported',
        });
      } else {
        toast({
          title: 'Import Failed',
          description: 'Invalid feature flags JSON file',
          variant: 'destructive',
        });
      }
    };
    input.click();
  };

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center justify-between">
          <div>
            <CardTitle className="flex items-center gap-2">
              <Settings className="h-5 w-5" />
              Feature Flags
            </CardTitle>
            <CardDescription>
              Manage feature toggles and experimental features
            </CardDescription>
          </div>
          <div className="flex items-center gap-2">
            <Button variant="outline" size="sm" onClick={handleExport}>
              <Download className="h-4 w-4 mr-2" />
              Export
            </Button>
            <Button variant="outline" size="sm" onClick={handleImport}>
              <Upload className="h-4 w-4 mr-2" />
              Import
            </Button>
            <Button variant="outline" size="sm" onClick={handleReset}>
              <RotateCcw className="h-4 w-4 mr-2" />
              Reset
            </Button>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div className="space-y-4">
          {flags.map((flag) => (
            <div
              key={flag.key}
              className="flex items-start justify-between p-4 border rounded-lg"
            >
              <div className="flex-1 space-y-1">
                <div className="flex items-center gap-2">
                  <h4 className="font-semibold">{flag.name}</h4>
                  <Badge variant={flag.enabled ? 'default' : 'secondary'}>
                    {flag.enabled ? 'Enabled' : 'Disabled'}
                  </Badge>
                </div>
                <p className="text-sm text-muted-foreground">{flag.description}</p>
                <code className="text-xs bg-muted px-2 py-1 rounded">{flag.key}</code>
              </div>
              <Switch
                checked={flag.enabled}
                onCheckedChange={() => handleToggle(flag.key)}
              />
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  );
};
