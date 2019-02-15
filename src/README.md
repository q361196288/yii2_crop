# yii2-webuploader
==========================

此扩展集成cropper图片裁切上传插件，旨在更好的处理 Yii2 framework 图片上传的前端问题，目前仅支持单图上传。

## 安装


推荐使用composer进行安装

```
$ php composer.phar require  crop/yii2-cropimage

```

## 使用

视图文件

单图
```php
<?php 
// ActiveForm
echo $form->field($model, 'username')->widget(\yii2\cropimage\CropImagesWidget::className(),['upload_url' => \yii\helpers\Url::toRoute(['site/upload']),'width' => 305,'height' => 230]); 

// 非 ActiveForm
echo '<label class="control-label">图片</label>';
echo \yii2\cropimage\CropImagesWidget::widget([
    'model' => $model,
    'attribute' => 'file',
    'upload_url' => \yii\helpers\Url::toRoute(['site/upload'])
]);
?>
```

控制器
upload_url上传控制器，控制器需要返回的数据格式如下
```php
// 错误时
{"code": 1, "msg": "error"}

// 正确时， 其中 attachment 指的是保存在数据库中的路径，url 是该图片在web可访问的地址
{"code": 0, "url": "http://domain/图片地址", "attachment": "图片地址"}
```


## 许可

**yii2-webuploader** is released under the MIT License. See the bundled `LICENSE.md` for details.
