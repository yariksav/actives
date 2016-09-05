<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use yii;
use yariksav\actives\helpers\SimpleImage;

class ImageUploadControl extends FileUploadControl {

    public $storage;
    public $sizes = [
        'original'=>800,
        'large'=>256,
        'normal'=>129,
        'small'=>64
    ];

    public function build() {
        return array_merge(parent::build(), [
            'maxSize'=>max(array_values($this->sizes))
        ]);
    }

    function decodeFile($content) {
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $content['content']));
    }

    function renderFile($name, $content) {
        parent::renderFile($name, $content);
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