<?php

namespace HtmlBladeRuntime;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Str;
use Symfony\Component\Finder\SplFileInfo;

class DynamicMeta 
{
    private SplFileInfo $file;
    public static string $regax = '/\[([A-Za-z_]+)\]/';
    private Collection $data;
    private string $model;
    private array|FileMeta $meta;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param Model $model
     * Set $this->model
     * Set $this->data
     * @return DynamicMeta
     */
    public function set(Model $model) : DynamicMeta
    {
        $this->model = $model::class;
        if(!class_exists($this->model)) throw new \Exception('The model class does not exist');
        $this->data = Collection::make([$model]);
        return $this;
    }

    public function generate() : DynamicMeta
    {
        $this->meta = $this->generateFileMeta();
        return $this;
    }

    private function getRegaxMatchs(string $dynamicString) : array | string
    {
        if(count(explode(' ', $dynamicString)) > 1) throw new \Exception('The name of the file should not contain any spaces');
        if(!preg_match_all(self::$regax, $dynamicString, $matches)) return $dynamicString;
        return $matches;
    }

    private function generateData(string $modelBaseName)
    {
        $this->model = 'App\\Models\\'. $modelBaseName;
        if(!class_exists($this->model)) throw new \Exception('The model class does not exist');
        $this->data = $this->model::public()->get();
    }

    private function resolveDynamicString(string $templateString, array $partsTemplate, array $regaxMatch, string $suffix = '') : array
    {
        $strings = [];
        if(!isset($this->data)) throw new \Exception('No model is mentioned in the file name');
        foreach ($this->data->all() as $_index => $model) {
            $baseNameParts = $partsTemplate;
            $strings[$_index] = $templateString;
            foreach ($baseNameParts as $index => $part) {
                $part = explode('_', $part);
                $baseNameParts[$index] = $model;
                foreach ($part as $key => $_part) {
                    if($baseNameParts[$index] instanceof Collection) {
                        $baseNameParts[$index] = $baseNameParts[$index]->map(fn ($item) => $item->$_part)->all();
                    } else {
                        $baseNameParts[$index] = $baseNameParts[$index]->$_part;
                    }
                }
            }
            $transform = true;
            foreach ($regaxMatch[0] as $index => $match) {
                $replacer = $baseNameParts[$index];                
                if(is_array($baseNameParts[$index])) {
                    $transform = false;
                    $replacer = json_encode($baseNameParts[$index]);
                }
                $strings[$_index] = str_replace($match, $replacer, $strings[$_index]);
            }
            if($transform) $strings[$_index] = trim(Str::kebab($strings[$_index])) . $suffix;
        }
        return $strings;
    }

    private function generateDynamicBaseName() : array | string | null
    {
        $dynamicBaseName = $this->file->getBasename('.blade.php');
        $matches = $this->getRegaxMatchs($dynamicBaseName);
        if(gettype($matches) === 'string') {
            return trim(Str::kebab($matches)) . '.html'; ;
        }

        $dynamicBaseNameParts = $matches[1];
        if(!isset($this->model) || !$this->model || !class_exists($this->model) || !isset($this->data) || !$this->data) {
            $this->generateData(explode('_', $dynamicBaseNameParts[0])[0]);
        }
        if(explode('_', $dynamicBaseNameParts[0])[0] !== class_basename($this->model)) throw new \Exception('The model class does not match the file name');

        $dynamicBaseNameFirstPart = explode('_', $dynamicBaseNameParts[0]);
        array_shift($dynamicBaseNameFirstPart);
        $dynamicBaseNameParts[0] = join('_', $dynamicBaseNameFirstPart);

        return $this->resolveDynamicString($dynamicBaseName, $dynamicBaseNameParts, $matches, '.html');
    }

    private function concat(string $separator, string $first, string $second) : string
    {
        return ($first ? $first . $separator : '' ). $second;
    }

    private function resolveRelativePath() : array | string
    {
        $relativePath = $this->file->getRelativePath();
        if(!$relativePath) return '';
        if(!preg_match(self::$regax, $relativePath)) return trim(Str::kebab($relativePath)); //$relativePath;
        $relativePathParts = explode('/', $relativePath);
        $resolvedRelativePath = [];
        foreach ($relativePathParts as $index => $partTemplateString) {
            $matches = $this->getRegaxMatchs($partTemplateString);
            if(gettype($matches) === 'string') {
                $resolvedRelativePath[$index] = $matches;
                continue;
            }
            $resolvedDynamicString = $this->resolveDynamicString($partTemplateString, $matches[1], $matches);
            foreach ($resolvedDynamicString as $key => $value) {
                $resolvedRelativePath[$key] = isset($resolvedRelativePath[$key]) ? ($resolvedRelativePath[$key] . '/'. $value) : $value;
            }
        }
        return $resolvedRelativePath;
    }

    private function generateFileMeta() : array | FileMeta
    {
        $baseName = $this->generateDynamicBaseName();
        $relativePath = $this->resolveRelativePath();
        if((gettype($baseName) === 'string') && (gettype($relativePath) === 'string')) return new FileMeta($this->concat('/', $relativePath, $baseName));
        $fileMetadata = [];
        foreach ($baseName as $index => $value) {
            $singalRelativePath = is_array($relativePath) ? $relativePath[$index] : $relativePath;
            if(isset($singalRelativePath) && $singalRelativePath && (json_decode($singalRelativePath) !== null) && is_array(json_decode($singalRelativePath))) {
                $array = json_decode($singalRelativePath);
                if(count($array) > 0) {
                    foreach (json_decode($singalRelativePath) as $_value) {
                        $fileMetadata[] = new FileMeta($this->concat('/', $_value, $value), [
                            'model' => $this->data->all()[$index]
                        ]);
                    }
                } else {
                    $fileMetadata[] = new FileMeta($this->concat('/', '', $value), [
                            'model' => $this->data->all()[$index]
                        ]);
                }
                continue;
            }
            $fileMetadata[] = new FileMeta($this->concat('/', $singalRelativePath, $value), [
                'model' => $this->data->all()[$index]
            ]);
        }

        return $fileMetadata;
    }

    public function get() : array | FileMeta
    {
        return $this->meta;
    }

    public function writeOn(callable $callback) : void
    {
        if($this->meta instanceof FileMeta) {
            $callback($this->meta);
            return;
        }
        foreach ($this->meta as $meta) {
            $callback($meta);
        }
    }
}