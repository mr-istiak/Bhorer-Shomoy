<?php

function appPath($path = '') {
    return __DIR__.($path ? '/'.$path : '');
}
function basePath($path = '') {
    return __DIR__.'/..'.($path ? '/'.$path : '');
}
function publicPath($path = '') {
    return basePath('public'.($path ? '/'.$path : ''));
} 

function enToBnNumber($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return str_replace($en, $bn, $number);
}

function viteInput(array $paths)
{  
    $devServerRunning = false;
    $host = 'localhost';
    $port = 5173;
    $timeout = 0.2;

    $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (is_resource($connection)) {
        $devServerRunning = true;
        fclose($connection);
    }

    $outputs = [];

    if ($devServerRunning) {
        // Load Vite HMR client first
        echo '<script type="module" src="http://' . $host . ':' . $port . '/@vite/client"></script>';

        foreach ($paths as $path) {
            $url = "http://{$host}:{$port}/{$path}";
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if (in_array($ext, ['js', 'ts'])) {
                echo '<script type="module" src="' . $url . '"></script>';
            } elseif ($ext === 'css') {
                echo '<link rel="stylesheet" href="' . $url . '">';
            }
        }
    } else {
        // Production: load from manifest
        $manifestPath = publicPath('build/.vite/manifest.json');
        if (!file_exists($manifestPath)) {
            throw new \Exception('Vite manifest not found: ' . $manifestPath);
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        foreach ($paths as $path) {
            if (!isset($manifest[$path])) continue;

            $file = $manifest[$path]['file'];
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if ($ext === 'js') {
                echo '<script defer type="module" src="/build/' . $file . '"></script>';
            } elseif ($ext === 'css') {
                echo '<link rel="stylesheet" href="/build/' . $file . '">';
            }
        }
    }

    return $outputs;
}

function useClass(array $classes) {
    $joinedClass = [];
    foreach ($classes as $class => $condition) {
        if($condition) {
            $joinedClass[] = $class;
        }
    }
    return implode(' ', $joinedClass);
}

function useUrl(string $slug, $full = false) {
    return ($full ? config('app.parent_url') : '') . '/'.$slug;
}

function formatBnTime($time) {
    return enToBnNumber(\Carbon\Carbon::parse($time)->locale('bn')->translatedFormat('d F Y, A h:i'));
}