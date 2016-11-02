<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->registerJsFile('@web/js/common.js');

$this->title = \yii::t('app', 'Lookup');
$this->params['breadcrumbs'][] = $this->title;
$smallTitle="Admin";
?>


<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $this->title;?>
           <?php if($model->id!=""){$smallTitle="Edit";}else{$smallTitle="Add";}?>
           <small><?php echo \yii::t('app', $smallTitle); ?></small>
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
    $parentList=array();
    if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
        $lookupTypeList = yii\helpers\ArrayHelper::map(\app\models\LookupTypes::find()->where(['is_delete'=>1])->all(), 'id', 'value');    
    }else{
        $lookupTypeList = yii\helpers\ArrayHelper::map(\app\models\LookupTypes::find()->where(['is_delete'=>1,'type_of_lookup_type'=>0])->all(), 'id', 'value');    
    }
    
    $statusList= yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS])->all(), 'id', 'value');       
    if($model->id!="" && Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){        
            $parentList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>$model->type0->parent_id])->all(), 'id', 'value');    
    }elseif($model->id!=""){
            $parentList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>$model->type0->parent_id,'is_seed_data'=>0])->all(), 'id', 'value');    
    }
    
    ?>
 

    <?php 
    $src=Yii::$app->request->baseUrl.'/images/lookups.png';
    if($model['image_path']!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$model['image_path'])){
        $src=Yii::$app->urlManager->createUrl($img=Yii::$app->params['UPLOAD_URL'].$model['image_path']);
    }
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
                                <?php 
                                $lookupTypeList1 = array();
                                if($lookupTypeList){
                                            foreach($lookupTypeList as $key=>$value){
                                                if($key == 45 || $key == 46 || $key == 50){
                                                    $lookupTypeList1[$key]= \Yii::t('app', $value);
                                                }
                                            }
                                            
                                        }?>
                               <?= $form->field($model, 'type', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                                ->dropDownList($lookupTypeList1, 
						['prompt'=>\yii::t('app', '--Select--'),
                                                  'onchange'=>'
             $.get("'.Yii::$app->urlManager->createUrl('index.php/adminuser/lookup/loadparent?id=').
           '"+$(this).val(),function( data ) 
                   {
                              $( "select#lookups-parent_id" ).html( data );
                            });
                        '  ])->label(\yii::t('app', 'Lookup Type'));
                                ?>
                            </div>
                            <div class="col-lg-6">
                                <?php if($parentList){
                                            foreach($parentList as $key=>$value){
                                                $parentList[$key]= \Yii::t('app', $value);
                                            }
                                        }?>
                               <?= $form->field($model, 'parent_id', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList( $parentList,
						['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Parent'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?php if($model->id==""){
                                    $url=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/lookup/checkshortcodeexist"]);                                
                                    ?>
                                <?=
                                $form->field($model, 'value', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Name'),'onblur'=>'checkSlugExist("lookups-short_code","'.$url.'")','id'=>'lookup-value-add'])->label(\yii::t('app', 'Name'));
                                ?>
                                <?php }else{
                                    echo $form->field($model, 'value', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Name')])->label(\yii::t('app', 'Name'));
                                } ?>                                
                            </div>
                             <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                            <div class="col-lg-6">
                                <?=$form->field($model,'is_seed_data', [
                                    'template' => "{label}\n<div class=\"col-sm-6\">{input}{error}{hint}</div>"])
                                        ->radioList(
                                                array(0=>\yii::t('app', 'User managed'),1=>\yii::t('app', 'System managed'))
                                                )->label(\yii::t('app', 'Seed Data Type'))?>
                                
                            </div> 
                             <?php }else{?> 
                                 <div class="col-lg-6">                                     
                                <?= $form->field($model, 'image_path',[
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"
                                ])->fileInput()->label(\yii::t('app', 'Image')) ?>
                              <img class="img-circle" id="lookup-img" src="<?=$src;?>" width="50" height="50"/>                                   
                                </div>
                                 <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'description', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textarea(['placeholder' =>\yii::t('app',  'Description'),'style'=>'resize:none;'])->label(\yii::t('app',  'Description'));
                                ?>                                
                            </div>
                            <div class="col-lg-6">
                                 <?php if($statusList){
                                            foreach($statusList as $key=>$value){
                                                $statusList[$key]= \Yii::t('app', $value);
                                            }
                                        }?>
                               <?= $form->field($model, 'status', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($statusList, 
						['prompt'=>\yii::t('app',  '--Select--')
                                                    
                                                    ])->label(\yii::t('app',  'Status'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'info1', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textarea(['placeholder' => \yii::t('app',  'Information1'),'style'=>'resize:none;'])->label(\yii::t('app',  'Information1'));
                                ?>                                
                            </div>
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'info2', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textarea(['placeholder' => \yii::t('app',  'Information2'),'style'=>'resize:none;'])->label(\yii::t('app',  'Information2'));
                                ?>                                
                            </div>
                        </div>
                        <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'image_path',[
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"
                                ])->fileInput()->label(\yii::t('app', 'Image')) ?>
                       
                                <img class="img-circle" id="lookup-img" src="<?=$src;?>" width="50" height="50"/>                                   
                          
                            </div>
                             <div class="col-lg-6">
                               <?= $form->field($model, 'short_code', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Short Code'),'readonly'=>TRUE])->label(\yii::t('app', 'Short Code'));
                                ?>
                            </div>
                        </div>
                        <?php }else{?>
                                       
                               <?=
                                    $form->field($model, 'is_seed_data', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->hiddenInput()->label(FALSE);
                                ?>
                        <?php } ?>
                        
                        <div class="box-footer">
                            <?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
<?= Html::submitButton(\yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-lookuptype-button']) ?>
                                </div>
                        
                                
                            </div>
                            <div class="col-lg-2">

                            </div>
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                </div>
<script>
    function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#lookup-img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
 $(document).ready(function() { 
    $("#lookups-image_path").change(function(){
        readURL(this);
    });
    
    $("#lookup-value-add, #lookups-type").on("keyup blur change",function(){
       var source =$("#lookup-value-add").val(); 
       var destination =$("#lookups-short_code").val();
       var prefix="L_";
       if($("#lookups-type").val()!=""){
            prefix +=$("#lookups-type option:selected").text()+'_';
        }
       createSlug('lookup-value-add','lookups-short_code',prefix);       
    });
    
   
    
 });
</script> 