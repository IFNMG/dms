<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('app', 'Manage Users');
$this->params['breadcrumbs'][] = $this->title;
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Manage Users');?>
            <small><?php echo \Yii::t('app', 'Add');?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    
    <?php $departmentList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>45, 'is_delete'=>1])->all(), 'id', 'value');?>
    
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
            </div>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'manageadd-form',
                        'options' => ['class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',  
                            ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-4'],
                        ],
                        'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/adminuser/admin/add'])]); ?>
            
            <div class="box-body">

            <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                
                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'firstName', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app','First Name')])->label( \yii::t('app','First Name'));
                                ?>
</div>
                                    <div class="col-lg-6">
                                <?=
                                $form->field($model, 'lastName', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app','Last Name')])->label( \yii::t('app','Last Name'));
                                ?>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?php if($types){
                                            foreach($types as $key=>$value){
                                                $types[$key]= \Yii::t('app', $value);
                                            }
                                        }?>
                                        
                                        
                                <?=
                                $form->field($model, 'role', ['template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($types)->label( \yii::t('app','Role'));?> 
</div>
                                    <div class="col-lg-6">
<?=
$form->field($model, 'email', [
    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>  \yii::t('app','Email')])->label( \yii::t('app','Email'));
                                        ?> </div></div>
                        
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'department', ['template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                                ->dropDownList($departmentList, 
                                                ['prompt' =>\Yii::t('app', '--Select Department--'),  'onchange'=>'
                $.post( "'.Yii::$app->urlManager->createUrl('index.php/adminuser/admin/subdepartment?id=').'"+$(this).val(), function( data ) {
                  $( "select#manageuseraddform-sub_department" ).html( data );
                });
            '])
                                                ->label( \yii::t('app','Department'));?> 
                                    </div>
                                    
                                    
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'sub_department', ['template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList(['prompt' =>\Yii::t('app', '--Select Sub Department--')])->label( \yii::t('app','Sub Department'));?> 
                                    </div>
                                </div>
                        
                        
                                <div class="box-footer">
                                    <?php echo Html::a(\Yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/admin/list"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
<?= Html::submitButton( \yii::t('app','Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
                                </div>

                            </div>
                            <div class="col-lg-2">

                            </div>
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                <!-- </div> -->
<style>
.image-wrapper {
    position:relative;
}
.browse-image {
    position: absolute;
    left: 0;
    right: 0;
    top: 42%;
    color: #ffffff;
    font-size: 24px;
    display:none;
    /*display:block;*/
    /*height:30px;*/
    /*width:30px;*/
    margin: 0 auto;
}
.browse-image:hover {
    cursor:pointer;
}
</style>
<script>
    function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#user-img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
 $(document).ready(function() { 
    $("#manageuseraddform-image_path").change(function(){
       var ext = $("#manageuseraddform-image_path").val().split('.').pop().toLowerCase();
       var arrValues = ["png","jpg", "jpeg"];
       if(arrValues.indexOf(ext) <= -1){  
           $("#image_path_msg").text('Only files with these extensions are allowed: png, jpg, jpeg.');
           $(this).val("");
           return false;
       }
       $("#image_path_msg").text('');
       readURL(this);
    });
    
    $( ".image-wrapper img, .browse-image" ).hover(
        function() {
          $('.browse-image').css('display','inline-block');
        }, function() {
          $('.browse-image').css('display','none');
        }
      );
      
    $('.browse-image').click(function(){
        $('.browse-image').addClass('visible');
        $('#manageuseraddform-image_path').click();
    });
    
 });
</script>  