<?php
/**
 * Created by PhpStorm.
 * User: ZhangLe
 * Date: 2019/2/13
 * Time: 10:29
 */

namespace yii2\cropimage\assets;


use yii\web\AssetBundle;

class CropImageAsset extends AssetBundle
{
    public $css = [
        'css/cropper.css',
        'css/style.css'
    ];
    public $js = [
        'js/cropper.js',
        'js/init.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    /**
     * 初始化：sourcePath赋值
     * @see \yii\web\AssetBundle::init()
     */
    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR . 'static';
    }
}