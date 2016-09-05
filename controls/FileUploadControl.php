<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use yii;

class FileUploadControl extends Control {

    public $multiple = false;
    public $fileName;

    public function build() {
        return array_merge(parent::build(), [
            'multiple' => $this->multiple
        ]);
    }

    public function getFilePath($fileName) {
        if (is_callable($this->fileName)) {
            return call_user_func($this->fileName, $fileName);
        } else {
            return Yii::getAlias('@runtime') . "/test/" . $fileName;
        }
    }

    public function update($value) {
        foreach($value as $key=>$file) {
            $content = $this->decodeFile($file);
            $path = $key.'.'.pathinfo($file['name'])['extension'];

            $this->renderFile($path, $content);
        }
//        throw new yii\base\Exception('test');
    }

    function renderFile($name, $content) {
        $path = $this->getFilePath($name);
        $this->saveFile($path, $content);
        return $path;
    }

    function saveFile($path, $content) {
        $file = fopen($path, "w");
        fwrite($file, $content);
        fclose($file);
    }

    function decodeFile($file) {
        if (isset($file['deflated'])) {
            return gzinflate(base64_decode($file['content']));
        } else {
            return base64_decode($file['content']);
        }
    }
}