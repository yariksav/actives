<?php

namespace yariksav\actives\base;

//use yii;
//use Closure;
use yii\base\Object;

class File extends Object
{
    public $name;
    //protected $folder = '';
    public $content;

    public $basePath;
    public $baseUrl;

    public $fileName;
    public $fileExt;
    public $fileType;
    public $action;
//
//    public function getName() {
//        return $this->name;
//    }
//
//    public function setName($value) {
//        $this->name = $value;
//        $this->extension = pathinfo($value)['extension'];
//        $this->fileName = uniqid().'.'.$this->extension;
//    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($value) {
        $this->path = $this->addSlash($value);
    }

//    public function getFolder() {
//        return $this->folder;
//    }
//
//    public function setFolder($value) {
//        $this->folder = $this->addSlash($value);
//    }

    protected function addSlash($path) {
        if (strlen($path) && substr($path, -1) !== '/') {
            $path .= '/';
        }
        return $path;
    }
//
//    public function fullUrl() {
//        return $this->url.$this->name;
//    }
//
//    public function fullPath() {
//        return $this->path.$this->name;
//    }

    public function save($fileName = null, $content = null) {
        if (!$content) {
            $content = $this->content;
        }
        if (!$fileName) {
            $name = $this->name;
        }

        if (!$content) {
            return;
        }

        $directory = pathinfo($this->basePath.$fileName, PATHINFO_DIRNAME);
        if(!is_dir($directory)) {
            mkdir($directory);
        }
        if (!is_writable($directory)) {
            throw new yii\base\Exception('Directory is not writable');
        }

        $file = fopen($this->basePath.$fileName, "w");
        fwrite($file, $content);
        fclose($file);
    }


}