
    
<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;


    $this->registerJsFile('@web/js/common.js');


$this->title = \yii::t('app', 'Lookup Type');
$this->params['breadcrumbs'][] = $this->title;
$smallTitle="Admin";
?>


<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $this->title;?>
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
                        'options' => ['class' => 'form-horizontal',],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-4'],
                        ],
            ]);
            ?> 
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>

 <?php
 if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
      $parentList = yii\helpers\ArrayHelper::map(\app\models\LookupTypes::find()->where(['is_delete'=>1])->all(), 'id', 'value');    
 }else{
      $parentList = yii\helpers\ArrayHelper::map(\app\models\LookupTypes::find()->where(['type_of_lookup_type'=>'0','is_delete'=>1])->all(), 'id', 'value');    
 }
  
   $statusList=array('1'=>\yii::t('app', 'Enabled'),'0'=>\yii::t('app', 'Disabled'));
    
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
                                    $model->value= \yii::t('app', $model->value); 
                                    $url=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/lookuptype/checkshortcodeexist"]);                                
                                ?>
                                <?php if($model->id==""){?>
                                <?=
                                $form->field($model, 'value', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Name'),'onkeyup'=>'createSlug("lookuptypes-value","lookuptypes-short_code","LT_")','onblur'=>'checkSlugExist("lookuptypes-short_code","'.$url.'")'])->label(\yii::t('app', 'Name'));
                                ?>
                                <?php }else{
                                    echo $form->field($model, 'value', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Name')])->label(\yii::t('app', 'Name'));
                                } ?>
                            </div>
                            <?php 
                        
                           if($parentList){
                                            foreach($parentList as $key=>$value){
                                                $parentList[$key]= \Yii::t('app', $value);
                                            }
                                        }
                             ?>                           
                            <div class="col-lg-6">
                               <?= $form->field($model, 'parent_id', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($parentList, 
						['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Parent'));
                                ?>
                            </div>
                        </div>
                         <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                            <div class="row">
                                <div class="col-lg-6">
                                    <?=$form->field($model,'type_of_lookup_type', [
                                        'template' => "{label}\n<div class=\"col-sm-6\">{input}{error}{hint}</div>"])->radioList(array(0=>\yii::t('app', 'User managed'),1=>\yii::t('app', 'System managed')))->label(\yii::t('app', 'Seed Data Type'))?>

                                </div>
                                <div class="col-lg-6">
                                    <?=$form->field($model,'sync_to_mobile', [
                                        'template' => "{label}\n<div class=\"col-sm-6\">{input}{error}{hint}</div>"])->radioList(array(0=>\yii::t('app', 'No'),1=>\yii::t('app', 'Yes')))->label(\yii::t('app', 'Sync To Mobile'))?>

                                </div>                          
                            </div>
                         <?php }else{
                             ?>                        
                        <?=
                               $form->field($model, 'type_of_lookup_type')->hiddenInput()->label(FALSE);
                            ?>
                          <?=
                                $form->field($model, 'sync_to_mobile')->hiddenInput()->label(FALSE);
                            ?>
                        <?php } ?>
                        <div class="row">
                              <div class="col-lg-6">
                               <?= $form->field($model, 'status', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList($statusList, 
						['prompt'=>\yii::t('app', '--Select--')])->label(\yii::t('app', 'Status'));
                                ?>
                            </div>
                            <div class="col-lg-6">
                               <?= $form->field($model, 'short_code', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' =>\yii::t('app', 'Short Code'),'readonly'=>TRUE])->label(\yii::t('app', 'Short Code'));
                                ?>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookuptype/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
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
       