<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title = \yii::t('app', 'Add New Template');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/common.js');

?>
<!--script src="http://cdn.ckeditor.com/4.5.7/standard-all/ckeditor.js"></script-->


<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
             <?php echo \yii::t('app', 'Manage Templates'); ?>
            <small><?php echo \yii::t('app', 'Update'); ?></small>
            
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
            
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>


                <div class="row">
                    <div class="col-lg-2">
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left"><?php echo \yii::t('app', 'Go Back'); ?></a>         
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            
                            <div class="col-lg-2">
                                
                            </div>
                            <div class="col-lg-8">
                                <?php 
                                    $event = \app\models\Lookups::find()->select(['value', 'id'])->where(['id'=>$model->event_id])->one();
                                ?>
                                <h3 class="profile-username text-center"><?php echo \yii::t('app', $event['value']); ?></h3><br/>
                                <?php 
                                $language = \app\models\Lookups::find()->select(['value', 'id'])->where(['id'=>$model->language])->one()
                                ?>
                                <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Language'); ?>:</strong><?php echo \yii::t('app', $language['value']); ?></p><br/>
                                <?php if($model->status == LookupCodes::L_COMMON_STATUS_DISABLED){ ?>
                                <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Status'); ?>:</strong><?php echo \yii::t('app', 'Disabled'); ?></p><br/>
                                <?php } ?>
                            </div>
                            <div class="col-lg-2">
                                <?php  if($permission->view == 1){ ?>
                                    <!--<a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/view", 'event'=>$model->event_id, 'lang'=>$model->language]); ?>" style="float: right;" class="btn btn-primary"><?php echo \yii::t('app', 'Preview'); ?></a>-->
                                <button onclick="callPrev();" type="button"  data-toggle="modal" data-target="#myModal" class="btn btn-sm btn-success">Preview</button>
                                <?php } ?>
                            </div>
                        </div>
                    
                        <?php $form = ActiveForm::begin(['id' => 'template-form', 'options' => ['enctype' => 'multipart/form-data'],'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/adminuser/template/add'])]); ?>
                        <div class="row" style="padding: 0px 15px 0px 15px;">
                            <?= $form->field($model, 'subject')->textInput(['placeholder' => \yii::t('app', 'Subject')])->label(\yii::t('app', 'Subject')); ?>
                        </div>
                        
                        <div class="row" style="padding: 0px 15px 0px 15px;">
                            <div class="col-lg-4">
                                    <?= $form->field($model, 'attachment')
                                    ->fileInput(['placeholder' => \Yii::t('app', 'Attachment')])
                                    ->label(\Yii::t('app', 'Attachment')); ?>
                            </div>
                            <?php if($model->attachment != ''){?>
                                <a target="_blank" title="Download Attachment" style=" margin-top: 15px;" class="btn btn-info btn-circle" href="<?php echo \Yii::getAlias('@web').'/uploads/'.$model->attachment; ?>">
                                    <i class="glyphicon glyphicon-download-alt"></i>
                                </a>
                            <?php } ?>
                            
                            <?php if($model->attachment != ''){?>
                                <a title="Remove Attachment" class="btn btn-warning btn-circle" style=" margin-top: 15px;" id="remove_attachment" onclick="removeAttachment('<?php echo $model->event_id; ?>','<?php echo $model->language; ?>');" >
                                    <i class="fa fa-times"></i>
                                </a>
                            <?php } ?>
                        </div>
                        
                        <div class="row"  style="padding: 0px 15px 15px 15px;">
                            <?= $form->field($model, 'content')->textarea(['id'=>'content'])->label(\yii::t('app', 'Content')); ?>
                        </div>
                        <?= $form->field($model, 'event_id')->hiddenInput()->label(false); ?>
                        <?= $form->field($model, 'language')->hiddenInput()->label(false); ?>
                        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                        
                        <div class="box-footer">
                            <a onclick="submitForm();" class="btn btn-success btn-flat pull-right"><?php echo \Yii::t('app', 'Save'); ?></a>         
                            <?php //echo Html::submitButton( \yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
                            
                            <?php if($permission->change_status == 1){?>
                                <?php if($model->status == LookupCodes::L_COMMON_STATUS_DISABLED){ 
                                    $text = 'Activate';
                                    $status = LookupCodes::L_COMMON_STATUS_ENABLED;
                                } else {
                                    $text = 'Deactivate';
                                    $status = LookupCodes::L_COMMON_STATUS_DISABLED;
                                }?>
                                <?php if($model->id != ''){ ?>    
                                <a style="margin-right: 10px;" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/activatedeactivate", 'id'=>$model->id, 'status'=>$status]); ?>" class="btn btn-primary btn-flat pull-right"><?php echo \yii::t('app',  $text);?></a>
                                <?php } ?>    
                            <?php } ?> 
                        </div>
                        
                        
                        <?php ActiveForm::end(); ?>
                        </div>
                    
                        <div class="col-lg-2">

                        </div>
                        </div><!-- /.box-body -->
                
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                    
                    <div id="myModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">                        
                                <div class="modal-body">
                                    <div class="clr"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                </div>
        
        
        
     

	<script>
                
                
		//CKEDITOR.replace( 'EmailTemplates[content]', {
                CKEDITOR.replace( 'content', {
			extraPlugins: 'placeholder',
                        height: 220,
                        //filebrowserBrowseUrl: '/browser/browse.php',
                        //filebrowserUploadUrl: '/uploader/upload.php'
		} );
	
            function callPrev(){         
                var content = CKEDITOR.instances['content'].getData();
                $(".modal-body").html(content);
                return false;
            }
            
            
            function submitForm(){
                document.getElementById('template-form').submit();
            }
            
            
    function removeAttachment(event, lang){
        if(event != '' && lang != ''){
            var retVal = confirm("Do you want to continue ?");
            if( retVal == true ){
                $.ajax({
                    type:'post',
                    data:{
                        event: event,
                        lang: lang
                    },
                    url : '<?= Yii::$app->urlManager->createUrl(["index.php/adminuser/template/delete"])?>',
                    success:function(status) {
                        var parsedData = JSON.parse(status);
                        if(parsedData.CODE == 200){
                            alert(parsedData.MESSAGE);
                            location.reload();
                        } else {
                            alert(parsedData.MESSAGE);
                        }
                    }
                });
            }
        }
    }    
        </script>