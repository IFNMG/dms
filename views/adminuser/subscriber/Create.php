 
<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title =  \yii::t('app', 'Manage Subscribers');
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $this->title ;?>
             <?php if($model->id!=""){$smallTitle="Edit";}else{$smallTitle="Add";}?>
                <small><?= \yii::t('app', $smallTitle);?></small>
            
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

 <?php $roleList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['parent_id'=>  LookupCodes::L_USER_TYPE_SUBSCRIBER,'is_delete'=>1])->all(), 'id', 'value');?>
 <?php $genderList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>LookupTypeCodes::LT_GENDER,'is_delete'=>1])->all(), 'id', 'value');?>
 <?php $maritalStatusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>LookupTypeCodes::LT_MARITAL_STATUS,'is_delete'=>1])->all(), 'id', 'value');?>
 <?php $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_USER_STATUS,'is_delete'=>1])->all(), 'id', 'value');?>
 <?php $countryList = yii\helpers\ArrayHelper::map(\app\models\Countries::find()->where(['is_delete'=>1])->all(), 'id', 'value');?>  
    <?php
    $stateList=array();
    if($model->id!="" && $model->country!=""){
            $stateList = yii\helpers\ArrayHelper::map(\app\models\States::find()->where(['country_id'=>$model->country])->all(), 'id', 'value');    
    }
    ?>
     <?php 
        if($roleList){
            foreach($roleList as $key=>$value){
                $roleList[$key]= \Yii::t('app', $value);
            }
        }
        if($genderList){
            foreach($genderList as $key=>$value){
                $genderList[$key]= \Yii::t('app', $value);
            }
        }
        if($maritalStatusList){
            foreach($maritalStatusList as $key=>$value){
                $maritalStatusList[$key]= \Yii::t('app', $value);
            }
        }
        if($statusList){
            foreach($statusList as $key=>$value){
                $statusList[$key]= \Yii::t('app', $value);
            }
        }
    ?>

 <?php $roleList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['parent_id'=>  LookupCodes::L_USER_TYPE_SUBSCRIBER,'type'=>1])->all(), 'id', 'value');?>
 <?php $genderList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_GENDER])->all(), 'id', 'value');?>
 <?php $maritalStatusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_MARITAL_STATUS])->all(), 'id', 'value');?>
 <?php $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_USER_STATUS])->all(), 'id', 'value');?>
     <?php 
        if($roleList){
            foreach($roleList as $key=>$value){
                $roleList[$key]= \Yii::t('app', $value);
            }
        }
        if($genderList){
            foreach($genderList as $key=>$value){
                $genderList[$key]= \Yii::t('app', $value);
            }
        }
        if($maritalStatusList){
            foreach($maritalStatusList as $key=>$value){
                $maritalStatusList[$key]= \Yii::t('app', $value);
            }
        }
        if($statusList){
            foreach($statusList as $key=>$value){
                $statusList[$key]= \Yii::t('app', $value);
            }
        }
    ?>
    

    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
            </div>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'manageadd-form',
                        'options' => 
                            ['class' => 'form-horizontal',
                             'enctype' => 'multipart/form-data',  
                            ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-4'],
                        ],
                        ]); ?>
            
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                
                <?php 
                $userImg=Yii::$app->request->baseUrl.'/images/user2-160x160.jpg';
                if($model->id!="" && $model->image_path!="" &&  file_exists(Yii::$app->params['UPLOAD_PATH'].$model->image_path)){ 
                  $userImg=Yii::$app->urlManager->createUrl(Yii::$app->params['UPLOAD_URL'].$model->image_path);
                }
                
                
                
                ?>
              
                    <div class="row  text-center image-wrapper">
                        <div class="col-lg-12">
                            <?php $img=Yii::$app->params['UPLOAD_URL'].$model['image_path']; ?>
                            <?php if($model['image_path']!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$model['image_path'])){?>
                            <img width="160" height="160" id="user-img" class="img-circle" src="<?=Yii::$app->urlManager->createUrl($img);?>" alt="User Image" />    
                            <?php }else{ ?>
                            <img alt="User Image" width="160" height="160" id="user-img"  class="img-circle" src="<?=Yii::$app->request->baseUrl.'/images/user2-160x160.jpg';?>">
                            <?php } ?>
                            <span class="glyphicon glyphicon-pencil browse-image"></span>
                             <?= $form->field($model, 'image_path',[
                                            'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"
                              ])->fileInput(['style'=>'display:none;'])->label(FALSE) ?>
                            <p style="color:#a94442" class="help-block help-block-error" id="image_path_msg"></p>
                        </div>
                    </div>
                    
              
           
                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'first_name', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                ->textInput(['placeholder' =>\yii::t('app', 'First Name')])->label(\yii::t('app', 'First Name'))
                                ?>
</div>
                                    <div class="col-lg-6">
                                <?=
                                $form->field($model, 'last_name', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                ->textInput(['placeholder' =>\yii::t('app', 'Last Name')])->label(\yii::t('app', 'Last Name'));
                                ?>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                <?=
                                $form->field($model, 'role', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList(
                                        $roleList)->label(\yii::t('app', 'Role'));
                                ?> 
</div>
                                    <div class="col-lg-6">
                                        <?php       
                                        $options=['placeholder' => 'Email'];
                                        if($model->id!=""){
                                            $options['disabled']='true';
                                        }?>
<?=
$form->field($model, 'email', [
    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput($options)->label(\yii::t('app', 'Email'))
                                        ?> </div></div>
                                <div class="row">
                                    <div class="col-lg-6">
                                         <?php       
                                        $options=['placeholder' => 'Phone'];
                                        if($model->id!=""){
                                            $options['disabled']='true';
                                        }?>
                                        <?=
                                        $form->field($model, 'phone', [
                                            'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                                ->textInput($options)->label(\yii::t('app', 'Phone'))
                                        ?> 
                                    </div>
                                    <div class="col-lg-6">
                                         <?=
                                        $form->field($model, 'gender', [
                                            'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                        ->dropDownList($genderList,['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Gender'));
                                        ?> 
                                    </div>
                                   
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?=
                                        $form->field($model, 'marital_status', [
                                            'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                        ->dropDownList($maritalStatusList,['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Marital Status'));
                                        ?> 
                                    </div>
                                    <div class="col-lg-6">
                                    <?=
                                $form->field($model, 'address', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => 'Address']);
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'country', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($countryList, 
						['prompt'=>'---Select---',
                                                 'onchange'=>'
             $.get("'.Yii::$app->urlManager->createUrl('index.php/adminuser/city/loadstate?id=').
           '"+$(this).val(),function( data )                    {
                              $( "select#subscriberform-state" ).html( data );
                            });
                        '  ])->label('Country');
                                ?>
                                    </div>
                                   <div class="col-lg-6">
                               <?= $form->field($model, 'state', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList( $stateList,
						['prompt'=>'---Select---',
                                                 'onchange'=>'
             $.get("'.Yii::$app->urlManager->createUrl('index.php/adminuser/city/loadcity?id=').
           '"+$(this).val(),function( data )                    {
                              $( "select#subscriberform-city" ).html( data );
                            });
                        '   ])->label('State');
                                ?>
                            </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                               <?= $form->field($model, 'city', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList( $stateList,
						['prompt'=>'---Select---'
                                                ])->label('City');
                                ?>
                            </div>
                                    <div class="col-lg-6">
                                        <?=
                                        $form->field($model, 'status', [
                                            'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                        ->dropDownList($statusList,['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Status'));
                                        ?> 
                                    </div>
                                </div>
                        
                            
                                    
                        
                                <div class="box-footer">
                                       <?php echo Html::a(\Yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/subscriber/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
<?= Html::submitButton(\yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
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
    $("#subscriberform-image_path").change(function(){
       var ext = $("#subscriberform-image_path").val().split('.').pop().toLowerCase();
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
        $('#subscriberform-image_path').click();
    });    
 });
</script>   