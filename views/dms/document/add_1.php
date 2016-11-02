<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \Yii::t('app', 'Add New');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/jquery-ui.js');
$this->registerCssFile('@web/css/jquery-ui.css');

?>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Documents');?>
            <?php if($model->id) { ?>
            <small><?php echo \Yii::t('app', 'Update');?></small>
            <?php } else { ?>
            <small><?php echo \Yii::t('app', 'Add New');?></small>
            <?php }  ?>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    
<?php $documentTypeList = \app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>49, 'is_delete'=>1])->all();?>
<?php $departmentList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>45, 'is_delete'=>1])->all(), 'id', 'value');?>
<?php $vendorList = \app\models\Vendor::find()->orderBy(['name' => SORT_ASC])->andWhere(['status'=>550001, 'is_delete'=>1])->all();?>
<?php
$vendorArr = array();
foreach($vendorList as $key=>$vendor){
    $vendorArr[$vendor->id] = $vendor->name.'-'.$vendor->code;
}

?>    
<?php $paymentTermsList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>50, 'is_delete'=>1])->all(), 'id', 'value');?>
<?php 
    $department = 2300001;
    $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
    if($userObj){
        if($userObj->department_id != ''){
            $department = $userObj->department_id;
        }
    }
    
    
?>
    
    
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
            </div>
            <?php $form = ActiveForm::begin(['id' => 'vendor-form', 
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/add'])]); ?>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
               
                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        

                        
                <div class="row">
                    <label for="" class="control-label">Document Type</label>
                    <select id="document_type" class="form-control " onchange="showForm(this);">
                    <?php                            
                    if($documentTypeList) {
                        echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                         foreach($documentTypeList as $post){?>
                              <option value='<?= $post->id; ?>'><?= $post->value; ?></option>
                         <?php }
                    } else {
                        echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                    }
                    ?>
                    </select>                 
                </div>    

                        
<div class="row">
    <?= $form->field($model, 'version')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'document_type_id')->hiddenInput()->label(false); ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength'=>50, 'placeholder' =>\Yii::t('app', 'Document Name'), 'class'=>'form-control agreement policy sop'])->label(\Yii::t('app', 'Document Name')) ; ?>
    
    <?= $form->field($model, 'document_path')->fileInput(['placeholder' =>\Yii::t('app', 'Document Path'), 'class'=>' agreement policy sop'])->label(\Yii::t('app', 'Document Path')) ; ?>

    
    
    <?php 
    $icon = '';
    if($model->id != ''){
        if($model->document_path != ''){
            if($model->document_type == 'application/vnd.oasis.opendocument.text' || $model->document_type == 'application/msword' || $model->document_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                $icon = '/dms/web/images/word.png';
            } else if($model->document_type == 'application/pdf'){
                $icon = '/dms/web/images/pdf.png';
            } else if($model->document_type == 'image/png'){
                $icon = '/dms/web/images/png.png';
            } else if($model->document_type == 'image/jpeg' || $model->document_type == 'image/jpg'){
                $icon = '/dms/web/images/jpeg.png';
            } else if($model->document_type == 'application/vnd.ms-excel' || $model->document_type == 'application/vnd.oasis.opendocument.spreadsheet' || $model->document_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                $icon = '/dms/web/images/excel.png';
            } else if($model->document_type == 'application/vnd.ms-powerpoint' || $model->document_type == 'application/vnd.oasis.opendocument.presentation'){
                $icon = '/dms/web/images/ppt.png';
            }

            $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
            $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 18px;" /></a>';
            echo $download;
        }
    }
    ?>
        
    
    <?php if($department == 2300001){ ?>
        <?= $form->field($model, 'department_id')->dropDownList($departmentList, ['prompt' =>\Yii::t('app', '--Select Department--'), 'class'=>'form-control agreement policy sop'])->label(\Yii::t('app', 'Department'));?>
    <?php } else { ?>
        <?php $model->department_id = $department; ?>
        <?= $form->field($model, 'department_id')->hiddenInput()->label(false); ?>
        
    <?php } ?>
    
    <?= $form->field($model, 'vendor_id')->dropDownList($vendorArr, ['prompt' =>\Yii::t('app', '--Select Vendor--'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Vendor'));?>

    <?= $form->field($model, 'process_name')->textInput(['maxlength'=>250, 'placeholder' =>\Yii::t('app', 'Process name'), 'class'=>'form-control sop'])->label(\Yii::t('app', 'Process Name')) ; ?>
    
    <?= $form->field($model, 'valid_from')->textInput(['placeholder' =>\Yii::t('app', 'Valid From'), 'class'=>'form-control agreement policy sop', 'readonly'=>'readonly'])->label(\Yii::t('app', 'Valid From')) ; ?>
    <?= $form->field($model, 'valid_till')->textInput(['placeholder' =>\Yii::t('app', 'Valid Till'), 'class'=>'form-control agreement policy sop', 'readonly'=>'readonly'])->label(\Yii::t('app', 'Valid Till')) ; ?>


    <?= $form->field($model, 'scope_of_work')->textInput(['maxlength'=>50, 'placeholder' =>\Yii::t('app', 'Scope of work'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Scope of work')) ; ?>

    <?= $form->field($model, 'payment_terms')->dropDownList($paymentTermsList, ['prompt' =>\Yii::t('app', '--Select Payment Term--'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Payment Terms'));?>
    
    
    <?= $form->field($model, 'fee')->textInput(['maxlength'=>20, 'placeholder' =>\Yii::t('app', 'Fee'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Fee')) ; ?>

    <?= $form->field($model, 'policy_header')->textInput(['placeholder' =>\Yii::t('app', 'Policy Header'), 'class'=>'form-control policy', 'maxlength'=>50])->label(\Yii::t('app', 'Policy Header')) ; ?>
    
    <?= $form->field($model, 'comments')->textarea(['placeholder' =>\Yii::t('app', 'Comments'), 'class'=>'form-control policy agreement sop', 'maxlength'=>500])->label(\Yii::t('app', 'Comments')) ; ?>

    <?= $form->field($model, 'is_locked')->checkbox(array('label'=>'Applicable to all departments', 'class'=>'', 'labelOptions'=>array('style'=>''))); ?>


</div>
                        
                        <div class="box-footer">
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                            <a style="margin-left: 20px;" onclick="location.reload()" class="btn btn-success btn-flat pull-left">Reset</a>         
                            <a onclick="submitForm();" class="btn btn-success btn-flat pull-right save_button"><?php echo \Yii::t('app', 'Save'); ?></a>         
                            <?php //echo Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right save_button', 'name' => 'create-user-button']) ?>
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
    
    <?php
    if($model->id != ''){
        ?>
            $('#document_type').val('<?php echo $model->document_type_id;?>');
            setTimeout(function() {
            $('#document_type').trigger('change');
          }, 200);
            
    <?php } ?>
        
    var globalFlag = 1;
    
    
    $('body').on('change', '#document-document_path', function(){
        if (this.files && this.files[0]) {
            var avatar = $(this).val();
            //var extension = avatar.split('.').pop().toUpperCase();
            //if (extension === "APK"){
                if(this.files[0].size <= 1000000){
                    globalFlag = 1;
                } else {
                    globalFlag = 0;
                    alert('Up to 1MB file size is allowed.');
                    this.value = null;
                }
            //} else {
            //    alert('Only APK Format is allowed.');
            //    globalFlag = 0;
            //    this.value = null;
                //$('#applications-apk').next('.help-block-error').html('Only APK Format is allowed.');
                //$('#applications-apk').parent('.form-group').addClass('has-error');
            //}
        }
    });
    
    function submitForm(){
        var flag = 1;
        var documentType = $('#document_type').val();
        var department = $('#document-department_id').val();
        var vendor = $('#document-vendor_id').val();
        var from = $('#document-valid_from').val();
        var till = $('#document-valid_till').val();
        var scope = $('#document-scope_of_work').val();
        var process = $('#document-process_name').val();
        var paymentTerms = $('#document-payment_terms').val();
        var fee = $('#document-fee').val();
        var policyHeader = $('#document-policy_header').val();
        var name = $('#document-name').val();
        var doc = $('#document-document_path').val();

        if(documentType == ''){
            flag = 0;
            alert('Please select type of document.');
        }
        
        <?php if($model->id == '') { ?>
        if(doc == ''){
            flag = 0;
            $('#document-document_path').next('.help-block-error').text('Please upload the document.');
            $('#document-document_path').parent('.form-group').addClass('has-error');
        } else {
            $('#document-document_path').next('.help-block-error').text('');
            $('#document-document_path').parent('.form-group').removeClass('has-error');
        }
        <?php } ?>
        
        if(department == ''){
            flag = 0;
            $('#document-department_id').next('.help-block-error').text('Please select department');
            $('#document-department_id').parent('.form-group').addClass('has-error');
        } else {
            $('#document-department_id').next('.help-block-error').text('');
            $('#document-department_id').parent('.form-group').removeClass('has-error');
        }
        
        if(name == ''){
            flag = 0;
            $('#document-name').next('.help-block-error').text('Name cannot be blank.');
            $('#document-name').parent('.form-group').addClass('has-error');
        } else {
            $('#document-name').next('.help-block-error').text('');
            $('#document-name').parent('.form-group').removeClass('has-error');
        }
            
        
        
        if(documentType == 2500001){
            if(vendor == ''){
                flag = 0;
                $('#document-vendor_id').next('.help-block-error').text('Please select vendor');
                $('#document-vendor_id').parent('.form-group').addClass('has-error');
            } else {
                $('#document-vendor_id').next('.help-block-error').text('');
                $('#document-vendor_id').parent('.form-group').removeClass('has-error');
            }
            
            if(scope == ''){
                flag = 0;
                $('#document-scope_of_work').next('.help-block-error').text('Please enter scope of work.');
                $('#document-scope_of_work').parent('.form-group').addClass('has-error');
            } else {
                $('#document-scope_of_work').next('.help-block-error').text('');
                $('#document-scope_of_work').parent('.form-group').removeClass('has-error');
            }
            
            if(paymentTerms == ''){
                flag = 0;
                $('#document-payment_terms').next('.help-block-error').text('Please select payment term.');
                $('#document-payment_terms').parent('.form-group').addClass('has-error');
            } else {
                $('#document-payment_terms').next('.help-block-error').text('');
                $('#document-payment_terms').parent('.form-group').removeClass('has-error');
            }
            
            
            if(fee == ''){
                flag = 0;
                $('#document-fee').next('.help-block-error').text('Please provide fee amount.');
                $('#document-fee').parent('.form-group').addClass('has-error');
            } else {
                $('#document-fee').next('.help-block-error').text('');
                $('#document-fee').parent('.form-group').removeClass('has-error');
            }
        }
        
        if(documentType == 2500001 || documentType == 2500002){
            if(from == ''){
                flag = 0;
                $('#document-valid_from').next('.help-block-error').text('Please select from date.');
                $('#document-valid_from').parent('.form-group').addClass('has-error');
            } else {
                $('#document-valid_from').next('.help-block-error').text('');
                $('#document-valid_from').parent('.form-group').removeClass('has-error');
            }
            
            if(till == ''){
                flag = 0;
                $('#document-valid_till').next('.help-block-error').text('Please select to date.');
                $('#document-valid_till').parent('.form-group').addClass('has-error');
            } else {
                $('#document-valid_till').next('.help-block-error').text('');
                $('#document-valid_till').parent('.form-group').removeClass('has-error');
            }
        }
        
        if(documentType == 2500003){
            if(process == ''){
                flag = 0;
                $('#document-process_name').next('.help-block-error').text('Please enter process name.');
                $('#document-process_name').parent('.form-group').addClass('has-error');
            } else {
                $('#document-process_name').next('.help-block-error').text('');
                $('#document-process_name').parent('.form-group').removeClass('has-error');
            }
        }
        
        if(documentType == 2500002){
            if(policyHeader == ''){
                flag = 0;
                $('#document-policy_header').next('.help-block-error').text('Please provide policy header.');
                $('#document-policy_header').parent('.form-group').addClass('has-error');
            } else {
                $('#document-policy_header').next('.help-block-error').text('');
                $('#document-policy_header').parent('.form-group').removeClass('has-error');
            }
        }
        
        
        
        
        if(flag == 1 && globalFlag == 1){
           document.getElementById('vendor-form').submit();
        }
   }
    
               
               
        $(function() {
            $(function () {
                $("#document-valid_from").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    //maxDate: new Date,
                    onSelect: function (selected) {
                        var dt = new Date(selected);
                        dt.setDate(dt.getDate() + 1);
                        $("#document-valid_till").datepicker("option", "minDate", dt);
                    }
                });
                $("#document-valid_till").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    //maxDate: new Date,
                    onSelect: function (selected) {
                        var dt = new Date(selected);
                        dt.setDate(dt.getDate() - 1);
                        $("#document-valid_from").datepicker("option", "maxDate", dt);
                    }
                });
                //}).datepicker("setDate", "0");
            });
        });
               
               
               $('.save_button').hide();
               $('.agreement').closest('div').hide();
               $('.policy').closest('div').hide();
               $('.sop').closest('div').hide();
               
               
               function showForm(e){
                   
                   $('.form-group').removeClass('has-error');
                   
                   <?php if($model->id == ''){?>
                        $('.agreement').val('');
                        $('.policy').val('');
                        $('.sop').val('');
                   <?php }?>
                   
                   <?php if($model->id != ''){?>
                       $('#document_type').attr('disabled', true);
                       $('#document_type').val('<?php echo $model->document_type_id;?>');
                       
                       //$('#document-department_id').attr('disabled', true);
                       //$('#document-department_id').val('<?php echo $model->department_id;?>');
                   <?php } ?>    
                       
                   var option = $(e).val();
                   if(option == 2500001){
                       $('.policy').closest('div').hide();
                       $('.sop').closest('div').hide();
                       $('.agreement').closest('div').show();
                       $('.save_button').show();
                       $('#document-document_type_id').val(option);
                   } else if(option == 2500002){
                       $('.agreement').closest('div').hide();
                       $('.sop').closest('div').hide();
                       $('.policy').closest('div').show();
                       $('.save_button').show();
                       $('#document-document_type_id').val(option);
                   } else if(option == 2500003){
                       $('.agreement').closest('div').hide();
                       $('.policy').closest('div').hide();
                       $('.sop').closest('div').show();
                       $('.save_button').show();
                       $('#document-document_type_id').val(option);
                   } else {
                       $('.agreement').closest('div').hide();
                        $('.policy').closest('div').hide();
                        $('.sop').closest('div').hide();
                        $('.save_button').hide();
                        $('#document-document_type_id').val('');
                   }
                    
               }
               
               
               
               
               </script>