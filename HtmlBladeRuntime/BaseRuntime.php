<?php

namespace HtmlBladeRuntime;

use Illuminate\Support\Facades\File;

class BaseRuntime
{   
    /**
     * Return the absolute path to the given path relative to the parent directory.
     *
     * @param string $path
     * @return string
     */
    public static function path($path = '') {
        return base_path(config('app.parent_path').($path ? '/'.$path : ''));
    }

    public static function viewsPath($path = '') {
        return self::path('src/views'.($path ? '/'.$path : ''));
    }

    public static function publicPath($path = '') {
        return self::path('public'.($path ? '/'.$path : ''));
    }
    public static function glob() {
        $bladeFiles = File::allFiles(self::viewsPath());
        $bladeFiles = array_filter($bladeFiles, fn($file) => str_ends_with($file->getFilename(), '.blade.php'));     
        return $bladeFiles;   
    }
    protected static function makeIndexDotPhp() {
        if(!File::exists(self::path('app/index.php'))) throw new \Exception('index.php not found');
        File::copy(self::path('app/index.php'), self::publicPath('index.php'));
    }

    protected static function deleteRecusively($dirPath, $deletingExtentions = ['php', 'html'])
    {
        if (!File::exists($dirPath)) return;
        // Loop through all files and subdirectories
        foreach (File::allFiles($dirPath) as $file) {
            if (in_array($file->getExtension(), $deletingExtentions)) {
                File::delete($file->getRealPath());
            }
        }
        // Now check directories (recursively from deepest level)
        $dirs = File::directories($dirPath);
        foreach ($dirs as $dir) {
            self::deleteRecusively($dir);
            // After recursion, if the directory is empty, delete it
            if (count(File::files($dir)) === 0 && count(File::directories($dir)) === 0) {
                File::deleteDirectory($dir);
            }
        }
    }

    protected static function deleteEmptyDirs($path)
    {
        // loop through all directories under the path
        foreach (File::directories($path) as $dir) {
            // recurse first (handle nested dirs)
            self::deleteEmptyDirs($dir);

            // after recursion, check if the directory is empty
            if (count(File::allFiles($dir)) === 0 && count(File::directories($dir)) === 0) {
                File::deleteDirectory($dir);
            }
        }
    }

    protected static function getSrcFilesFor(string $modelBaseName) : array
    {
        $pattern = '/\[' . preg_quote($modelBaseName, '/') . '(?:_[A-Za-z0-9_]*)?\][A-Za-z0-9_.-]*\.blade\.php$/';
        $files = File::allFiles(self::viewsPath());
        $matched = collect($files)->filter(function ($file) use ($pattern) {
            return preg_match($pattern, $file->getFilename());
        })->values()->all();
        return $matched;
    }
}