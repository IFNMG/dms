<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \yii::t('app', 'Country');
$this->params['breadcrumbs'][] = $this->title;
$smallTitle="Admin";
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Country');?>
            <?php if($model->id!=""){$smallTitle="Edit";}else{$smallTitle="Add";}?>
            <small><?= \yii::t('app', $smallTitle);?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
            </div>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'manageadd-form',
                        'action'=>'',
                        'options' => ['class' => 'form-horizontal',
                             'enctype' => 'multipart/form-data',
                            ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-4'],
                        ],
            ]);
            ?> 
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>

 <?php   
    $statusList= yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS])->all(), 'id', 'value');           
?>
 

                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">                         
                                <?=
                                    $form->field($model, 'id', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->hiddenInput()->label(FALSE);
                                ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'value', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'Name')])->label(\yii::t('app', 'Name'));
                                ?>
                            </div>
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'iso_code', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'ISO Code')])->label(\yii::t('app', 'ISO Code'));
                                ?>
                            </div>
                        </div>
                        <div class="row">                           
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'isd_code', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'ISD Code')])->label(\yii::t('app', 'ISD Code'));
                                ?>
                            </div>  
                             <div class="col-lg-6">
                                <?=
                                $form->field($model, 'exit_code', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'Exit Code')])->label(\yii::t('app', 'Exit Code'));
                                ?>
                            </div> 
                        </div>
                        
                        <div class="row">                            
                            <div class="col-lg-6">
                                <?= $form->field($model, 'flag_url',
                                        [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"
                                ]
                                        )->fileInput()->label(\yii::t('app', 'Flag')) ?>
                                <?php $img=Yii::$app->params['UPLOAD_URL'].$model['flag_url']; ?>
                                <?php 
                                $src=Yii::$app->request->baseUrl.'/images/flag.png';
                                if($model['flag_url']!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$model['flag_url'])){
                                    $src=Yii::$app->urlManager->createUrl($img);
                                }
                                    ?>
                                <img class="img-circle" id="flag-img" src="<?=$src;?>" width="50" height="50"/>    
                                
                            </div>
                            
                            <div class="col-lg-6">
                                <?php if($statusList){
                                            foreach($statusList as $key=>$value){
                                                $statusList[$key]= \Yii::t('app', $value);
                                            }
                                        }?>
                               <?= $form->field($model, 'status', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($statusList, 
						['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Status'));
                                ?>
                            </div>
                        </div>
                  
                        
                        <div class="box-footer">
                            <?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/country/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
<?= Html::submitButton(\yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-country-button']) ?>
                                </div>
                        
                                
                            </div>
                            <div class="col-lg-2">

                            </div>
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                <!-- </div> -->
<script>
    function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#flag-img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
 $(document).ready(function() { 
$("#countries-flag_url").change(function(){
    readURL(this);
});
 });
</script> 