<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use yariksav\actives\base\File;
use yii;

class FileUploadControl extends Control {

    public $multiple = false;

    public $basePath;
    public $baseUrl;
    public $fileName;
    public $label;
    public $render;
    public $files = [];

    public function init() {
        $this->label = Yii::t('actives', 'Drop files here or click to upload');
    }

    public function build() {
        return array_merge(parent::build(), [
            'multiple' => $this->multiple,
            'label'=>$this->label
        ]);
    }

    public function update($value) {
        foreach($value as $key=>$fileConfig) {
            $fileConfig['content'] = $this->decodeFile($fileConfig);
            $fileConfig['fileExt'] = strtolower(pathinfo($fileConfig['fileName'])['extension']);
            //$fileConfig['fileName'] = $fileConfig['key'].'.'.$fileConfig['fileExt'];
            unset($fileConfig['key']);
            $file = new File($fileConfig);
            $file->name = uniqid().'.'.$file->fileExt;
            $file->basePath = is_callable($this->basePath) ? call_user_func($this->basePath, $file) : $this->basePath;
            $file->baseUrl = is_callable($this->baseUrl) ? call_user_func($this->baseUrl, $file) : $this->baseUrl;
//            $file->fileName = is_callable($this->fileName) ? call_user_func($this->fileName, $file) : $this->fileName;
//            if (!$file->fileName) {
//                $file->fileName = $this->createFileName($file);
//            }
            $this->files[$key] = $file;
//            $this->renderFile($file);
        }
        parent::update($this->files);
    }

//    function renderFile(File $file) {
//        $file->save();
//        if (is_callable($this->render)) {
//            call_user_func($this->render, $file);
//        }
//        return $file;
//    }


    function decodeFile($file) {
        if (isset($file['deflated'])) {
            return gzinflate(base64_decode($file['content']));
            unset($file['deflated']);
        } else {
            return base64_decode($file['content']);
        }
    }
}