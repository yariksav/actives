<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use app\models\Image;
use yariksav\actives\base\File;
use yii;
use yariksav\actives\base\SimpleImage;

class ImageUploadControl extends FileUploadControl {

    public $maxSize;
    public $quality = 1;

    public function build() {
        return array_merge(parent::build(), [
            'maxSize'=>$this->maxSize,
            'quality'=>$this->quality
        ]);
    }

    function decodeFile($config) {
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $config['content']));
    }

    public function validate() {
        if (!$this->files) {
            return 'Need file';
        }
    }

    function renderFile(File $file) {
//        $path = $file->path;
//        $file->folder = 'original';
//        $file->save();
//
//        $imageHelper = new SimpleImage();
//        $imageHelper->load($file->fullPath());
//
//        $image = new Image();
//        $image->fill($file);
//        $image->width = $imageHelper->getWidth();
//        $image->height = $imageHelper->getHeight();
//        $image->save();




//        $image->resize(256, 256);
//        $image->save($path . "256." . $ext);
//        parent::renderFile($file);
        //TODO
//        $path = $this->getFilePath($name);
//
//        $this->saveFile($path, $content);
//        //return $path;
//
//        $ext = pathinfo($name, PATHINFO_EXTENSION);;
//        $image = new SimpleImage();
//        $image->load($path . $originalFileName);
//        $image->resize(256, 256);
//        $image->save($path . "256." . $ext);

    }

}
//yii migrate/up --migrationPath=@app/modules/modName/migrations