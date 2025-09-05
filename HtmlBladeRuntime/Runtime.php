<?php

namespace HtmlBladeRuntime;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Str;

class Runtime extends BaseRuntime
{
    public static function clean() {
        self::deleteRecusively(self::publicPath());
    }

    protected static function run(array $bladeFiles, ?callable $callback = null)
    {
        require_once self::path('app/app.php');
        $prerocessor = new Preprocessor();
        foreach ($bladeFiles as $srcFile) {
            $dynamicMeta = new DynamicMeta($srcFile);
            if($callback) $dynamicMeta = $callback($dynamicMeta);
            $dynamicMeta->generate()->writeOn(function (FileMeta $meta) use ($srcFile, $prerocessor) {
                $destination = self::publicPath($meta->relativePathName);
                $prerocessor->file($srcFile->getRealPath())->compile($meta->data)->toFile($destination);
            });
        }
    }

    public static function build() {
        self::clean();
        $bladeFiles = self::glob();
        if (!File::exists(self::publicPath())) File::makeDirectory(self::publicPath());
        self::makeIndexDotPhp();
        self::run($bladeFiles);
        Artisan::call('view:clear');
    }

    public static function create(Model $model)
    {
        $modelName = class_basename($model::class);
        $srcFiles = self::getSrcFilesFor($modelName);
        self::run($srcFiles, function (DynamicMeta $meta) use ($model) : DynamicMeta
        {
            return $meta->set($model);
        });
    }

    public static function createAll(array|Collection $models)
    {
        foreach ($models as $model) {
            self::create($model);
        }
    }
    public static function update(Model $newModel, Model $oldModel) 
    {
        self::delete($oldModel);
        self::create($newModel);
    }

    public static function updateAll(array|Collection $newModels, array|Collection $oldModels) 
    {
        self::deleteAll($oldModels);
        self::createAll($newModels);
    }

    public static function delete(Model $model) 
    {
        $srcFiles = self::getSrcFilesFor(class_basename($model::class));
        foreach ($srcFiles as $srcFile) {
            $dynamicMeta = new DynamicMeta($srcFile);
            $metas = $dynamicMeta->set($model)->generate()->get();
            if($metas instanceof FileMeta) throw new \Exception('The file cannot be deleted');
            foreach ($metas as $meta) {
                File::delete(self::publicPath($meta->relativePathName));
            }
        }
    }

    public static function deleteAll(array|Collection $models)
    {
        foreach ($models as $model) {
            self::delete($model);
        }
    }

    public static function close()
    {
        $files = collect(File::files(self::viewsPath()));
        $files = $files->filter(function ($file) {
            return str_ends_with($file->getFilename(), '.blade.php') && !preg_match(DynamicMeta::$regax, $file->getBasename('.blade.php'));
        });
        $files->each(function ($file) {
            File::delete(self::publicPath(Str::kebab($file->getBasename('.blade.php')) . '.html'));
        });
        self::deleteEmptyDirs(self::viewsPath());
        self::run($files->all());
    }
}