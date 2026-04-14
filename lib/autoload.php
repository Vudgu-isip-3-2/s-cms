<?php
// autoload.php

spl_autoload_register(function ($fullClass) {
    $libDir = __DIR__ . '/';
    
    if (strpos($fullClass, '\\') !== false) {
        $relativePath = str_replace('\\', '/', $fullClass) . '.php';
        $file = $libDir . $relativePath;
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    $className = $fullClass;
    
    if (!is_dir($libDir)) return;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($libDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            if ($file->getFilename() === 'autoload.php') continue;
            
            $content = file_get_contents($file->getPathname());
            
            if (preg_match('/\bclass\s+' . preg_quote($className, '/') . '\b/i', $content)) {
                require_once $file->getPathname();
                return;
            }
        }
    }
});