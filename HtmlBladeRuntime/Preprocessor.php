<?php

namespace HtmlBladeRuntime;

use Illuminate\Contracts\View\View as ViewType;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use voku\helper\HtmlMin;

class Preprocessor
{
    private ViewType $view;
    private string $html = '';
    private $filePath = null;

    /**
     * Creates a new instance of the Preprocessor, optionally with a blade string
     * to process.
     *
     * @param string $bladeString
     */
    public function __construct()
    {
        Blade::anonymousComponentPath('/home/mristiak/sites/bhorershomoy/src/components');
        return $this;
    }

    /**
     * Gets a property of the preprocessor.
     *
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        switch ($name) {
            case 'view':
                return $this->view;
                break;
            case 'html':
                return $this->html;
                break;
        }
    }

    /**
     * Set the blade string to the contents of the given file.
     *
     * @param string $path
     * @return $this
     */
    public function file($path) {
        $this->filePath = $path;
        return $this;
    }

    /**
     * Compiles the blade string to a php string.
     *
     * @return $this
     */
    private function compileBlade(array $data = []) {
        if(!$this->filePath) throw new \Exception('No file path provided');
        $this->view = View::file($this->filePath, $data);
        return $this;
   }  

    /**
     * Evaluate the php string and store the output in the html property.
     *
     * @return $this
     *
     * @throws \Exception
     */
   private function render() {
        if(!$this->view) throw new \Exception('No php string provided');
        $this->html = $this->view->render();
        return $this;
   }

    /**
     * Compile the blade string into a php string and evaluate the php string into the html property.
     *
     * @return $this
     *
     * @throws \Exception
     */
   public function compile(array $data = []) {
        $this->compileBlade($data)->render();
        return $this;
   }

    /**
     * Returns the compiled and evaluated html string.
     *
     * @return string
     */
   public function get() {
        return $this->html;
   }

    public function minify() {
        $minifier = new HtmlMin();
        $this->html = $minifier->minify($this->html);
        return $this;
    }

    /**
     * Saves the compiled and evaluated html string to the given file path.
     *
     * @param string $path
     * @return $this
     */
    public function toFile($path) {
        $this->minify();
        $directory = dirname($path);
        if(!File::exists($directory)) File::makeDirectory($directory, 0755, true);      
        File::put($path, $this->html);
        return $this;
    }
}
