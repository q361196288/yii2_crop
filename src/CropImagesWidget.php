<?php
/**
 * Created by PhpStorm.
 * User: ZhangLe
 * Date: 2019/2/13
 * Time: 10:18
 */

namespace yii2\cropimage;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\widgets\InputWidget;
use yii2\cropimage\assets\CropImageAsset;

class CropImagesWidget extends InputWidget
{
    public $chooseButtonClass = ['class' => 'btn-default'];
    public $deaultImages = "";
    public $avatar_id; //图片ID
    public $image_id; // 画布ID
    public $input_id; // 文件域ID
    public $modal_id; // 弹层ID
    public $upload_url; //上传地址
    public $tooltip; //文件域外层信息
    public $width; // 初始宽度
    public $height; //初始高度
    public $formData = ""; // 提交表单额外数据 key=>value格式
    private $_view;
    private $src;

    public function init()
    {
        parent::init();
        $this->_view = $this->getView();
        $src = $this->getDefaultSrc($this->model, $this->attribute);
        $this->RegisterResource();
        $this->initElementId();
        $this->initConfig();
    }

    public function run()
    {
        if ($this->hasModel()) {
            $model = $this->model;
            $attribute = $this->attribute;
            $html = $this->renderInput($model, $attribute);
            $html .= $this->renderImage($model, $attribute);
            echo $html;
        }
    }

    //初始化配置
    public function initConfig()
    {
        if (!$this->upload_url) {
            throw new InvalidConfigException("The upload url cannot be empty", 1);
        }
        $config['avatar_id'] = $this->avatar_id;
        $config['image_id'] = $this->image_id;
        $config['input_id'] = $this->input_id;
        $config['modal_id'] = $this->modal_id;
        $config['upload_url'] = $this->upload_url;
        $config['tooltip'] = $this->tooltip;
        $config['width'] = $this->width;
        $config['height'] = $this->height;
        $config['formData'] = $this->formData;
        $js_config = Json::htmlEncode($config);
        $js = <<<JS
            var config = {$config};
            $('#{$config['modal_id']}').crop_upload({$js_config});
JS;
        $this->_view->registerJs($js);
    }

    //初始化元素ID
    public function initElementId()
    {
        $id = md5($this->options['id']);
        $this->avatar_id = $this->avatar_id ?: $id . "_avatar";
        $this->image_id = $this->image_id ?: $id . "_image";
        $this->input_id = $this->input_id ?: $id . "_input";
        $this->modal_id = $this->modal_id ?: $id . "_modal";
        $this->tooltip = $this->tooltip ?: $id . "_tooltip";
        $this->width = $this->width ?: 0;
        $this->height = $this->height ?: 0;
    }

    //获取默认图片地址
    public function getDefaultSrc($model, $attribute)
    {
        $this->src = $this->deaultImages;
        if (($value = $model->$attribute)) {
            $this->src = $this->_validateUrl($value) ? $value : Yii::$app->params['domain'] . $value;
        }
        if (!empty($this->value)) {
            $this->src = $this->_validateUrl($this->value) ? $this->value : Yii::$app->params['domain'] . $this->value;
        }
        return $this->src;
    }

    public function renderInput($model, $attribute)
    {
        Html::addCssClass($this->chooseButtonClass, "btn $this->modal_id");
        $eles = [];
        $eles[] = Html::activeTextInput($model, $attribute, ['class' => 'form-control', 'readonly' => true]);
        $eles[] = Html::tag('label', Html::tag('div', '选择图片', $this->chooseButtonClass) . Html::fileInput('image', "", ['id' => $this->input_id, 'class' => 'sr-only', "accept" => "image/*"]), ['class' => 'input-group-btn', 'data-toggle' =>  $this->tooltip]);
        return Html::tag('div', implode("\n", $eles), ['class' => 'input-group']);
    }

    public function renderImage($model, $attribute)
    {
        $imges = [];
        $imges[] = Html::img($this->src, ['class' => 'img-responsive img-thumbnail cus-img', 'id' => $this->avatar_id]);
        $imges[] = Html::tag('em', 'x', ['class' => 'close delImage', 'title' => '删除这张图片']);
        return Html::tag('div', implode("\n", $imges), ['class' => 'input-group', 'style' => 'margin-top:.5em;']);
    }

    //检查图片是否为全路径
    private function _validateUrl($value)
    {
        $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';
        $validSchemes = ['http', 'https'];
        $pattern = str_replace('{schemes}', '(' . implode('|', $validSchemes) . ')', $pattern);
        if (!preg_match($pattern, $value)) {
            return false;
        }
        return true;
    }

    //注册资源
    public function RegisterResource()
    {
        CropImageAsset::register($this->_view);
    }
}