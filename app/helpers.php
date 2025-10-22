<?php

if (!function_exists('vite_asset')) {
    /**
     * Genera la URL para un asset de Vite
     */
    function vite_asset(string $path): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath)) {
            // En desarrollo, retornar el path original
            return asset($path);
        }
        
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (isset($manifest[$path])) {
            return asset('build/' . $manifest[$path]['file']);
        }
        
        // Si no se encuentra en el manifest, retornar el path original
        return asset($path);
    }
}

if (!function_exists('vite_assets')) {
    /**
     * Genera el HTML completo para un entry de Vite
     */
    function vite_assets(string $entry): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath)) {
            return '';
        }
        
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (!isset($manifest[$entry])) {
            return '';
        }
        
        $entryData = $manifest[$entry];
        $html = '';
        
        // CSS files
        if (isset($entryData['css'])) {
            foreach ($entryData['css'] as $css) {
                $html .= '<link rel="stylesheet" href="' . asset('build/' . $css) . '">' . "\n";
            }
        }
        
        // JS file
        if (isset($entryData['file'])) {
            $html .= '<script type="module" src="' . asset('build/' . $entryData['file']) . '"></script>' . "\n";
        }
        
        return $html;
    }
}


