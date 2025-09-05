<?php

namespace HtmlBladeRuntime;

use Illuminate\Database\Eloquent\Model;

class FileMeta {
    public $relativePathName;
    public $data;

    public function __construct(string $relativePathName, array $data = []) {
        $this->relativePathName = $relativePathName;
        $this->data = $data;
    }
}