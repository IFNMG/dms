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

<?php $agreementTypeList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>52, 'is_delete'=>1])->all(), 'id', 'value');?>    
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
               
                   
                  
                    <div class="space-bottom">   

                        
              
                    <label for="" class="control-label">Document Type</label>
                    <select id="document_type" class="form-control " onchange="showForm(this);">
                    <?php 
                    if($model->old_id != ''){
                        if($documentTypeList) {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                            foreach($documentTypeList as $post){ ?>
                                <option value='<?= $post->id; ?>'><?= $post->value; ?></option>
                            <?php }
                        } else {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                        }
                    } else {
                        if($documentTypeList) {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                            foreach($documentTypeList as $post){ 
                                if($post->id != 2500004){
                        ?>
                                <option value='<?= $post->id; ?>'><?= $post->value; ?></option>
                            <?php } }
                        } else {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                        }
                    }
                    ?>
                    </select>   
                    
                
                    </div>
                            

    <?= $form->field($model, 'version')->hiddenInput()->label(false); ?><div>
        
    
        <div class="text-muted1"><?= $form->field($model, 'name')->textInput(['maxlength'=>50, 'placeholder' =>\Yii::t('app', 'Document Name'), 'class'=>'form-control agreement policy sop']) ; ?></div>
    
        <div class="text-muted1 space-bottom-small">
        <?= $form->field($model, 'document_path')->fileInput(['placeholder' =>\Yii::t('app', 'Document'), 'class'=>' agreement policy sop']) ; ?>
        <?php 
            $icon = '';
            if($model->id != ''){
                if($model->document_path != ''){
                    $facade = new \app\facades\dms\DocumentFacade();
                    $icon = $facade->getIcon($model->document_type);
                    $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                    $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img class="small-image" alt="" src='.$icon.' /></a>';
                    echo $download;
                }
            }
        ?>
        </div>
        
        
        <div class="text-muted1 space-bottom-small">
        <?= $form->field($model, 'scanned_document_path')->fileInput(['placeholder' =>\Yii::t('app', 'Scanned Document'), 'class'=>' agreement policy sop']) ; ?>
        <?php 
            $icon1 = '';
            if($model->id != ''){
                if($model->scanned_document_path != ''){
                    $facade1 = new \app\facades\dms\DocumentFacade();
                    $icon1 = $facade1->getIcon($model->scanned_document_type);
                    $path1 = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->scanned_document_path;
                    $download1 = '<a target="_blank" href="'.$path1.'" style="cursor: pointer;" title="Click to download"><img class="small-image" alt="" src='.$icon1.' /></a>';
                    echo $download1;
                }
            }
        ?>
        </div>
        
        
        
       <div class="text-muted1">  
    <?php //if($department == 2300001){ ?>
        <?= $form->field($departmentObj, 'department_id')->dropDownList($departmentList, ['prompt' =>\Yii::t('app', '--Select Department--'), 'class'=>'form-control agreement policy sop', 'multiple'=>'multiple']);?>
    <?php //} else { ?>
        <?php //$model->department_id = $department; ?>
        <?php  //echo $form->field($departmentObj, 'department_id')->hiddenInput()->label(false); ?>
        
    <?php //} ?></div>
   
    <div class="text-muted1"><?= $form->field($model, 'agreement_type_id')->dropDownList($agreementTypeList, ['prompt' =>\Yii::t('app', '--Select Agreement Type--'), 'class'=>'form-control agreement policy sop']);?></div>
    <div class="text-muted1"><?= $form->field($model, 'vendor_id')->dropDownList($vendorArr, ['prompt' =>\Yii::t('app', '--Select Vendor--'), 'class'=>'form-control agreement']);?></div>

   
    
     <div class="text-muted1"><?= $form->field($model, 'valid_from')->textInput(['placeholder' =>\Yii::t('app', 'Valid From'), 'class'=>'form-control agreement policy sop', 'readonly'=>'readonly'])->label(\Yii::t('app', 'Valid From')) ; ?></div>
     <div class="text-muted1"><?= $form->field($model, 'valid_till')->textInput(['placeholder' =>\Yii::t('app', 'Valid Till'), 'class'=>'form-control agreement policy sop', 'readonly'=>'readonly'])->label(\Yii::t('app', 'Valid Till')) ; ?></div>


     <div class="text-muted1"><?= $form->field($model, 'scope_of_work')->textInput(['placeholder' =>\Yii::t('app', 'Scope of work'), 'class'=>'form-control agreement']) ; ?></div>

     <div class="text-muted1"><?= $form->field($model, 'payment_terms')->dropDownList($paymentTermsList, ['prompt' =>\Yii::t('app', '--Select Payment Term--'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Payment Terms'));?></div>
    
    
     <div class="text-muted1"><?= $form->field($model, 'fee')->textInput(['maxlength'=>20, 'placeholder' =>\Yii::t('app', 'Fee'), 'class'=>'form-control agreement'])->label(\Yii::t('app', 'Fee')) ; ?></div>

     <div class="text-muted1"><?= $form->field($model, 'comments')->textarea(['placeholder' =>\Yii::t('app', 'Comments'), 'class'=>'form-control policy agreement sop', 'maxlength'=>500])->label(\Yii::t('app', 'Comments')) ; ?></div>
       <div class="text-muted1"><?= $form->field($model, 'policy_header')->textInput(['placeholder' =>\Yii::t('app', 'Policy Header'), 'class'=>'form-control policy', 'maxlength'=>50])->label(\Yii::t('app', 'Policy Header')) ; ?></div>
  <div class="text-muted"><?= $form->field($model, 'process_name')->textInput(['maxlength'=>250, 'placeholder' =>\Yii::t('app', 'Process name'), 'class'=>'form-control sop'])->label(\Yii::t('app', 'Process Name')) ; ?></div>
     <div class="text-muted1">
        <?php //echo $form->field($model, 'is_locked')->checkbox(array('label'=>'Applicable to all departments', 'class'=>'', 'labelOptions'=>array('style'=>''))); ?>
</div>
<div class="text-muted1"><?= $form->field($model, 'id')->hiddenInput()->label(false); ?></div>

<div class="text-muted1"><?= $form->field($model, 'old_id')->hiddenInput()->label(false); ?></div>
        <div class="text-muted1"><?= $form->field($model, 'document_type_id')->hiddenInput()->label(false); ?></div>


                   </div>
                        
                        <div class="box-footer">
                            
                        <a onclick="location.reload()" class="btn my-btn pull-left">Reset</a>         
                            <a onclick="submitForm();" class="btn my-btn pull-right"><?php echo \Yii::t('app', 'Save'); ?></a> 
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>" name="go-back" class="back-button"></a>         
                                    
                            <?php //echo Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right save_button', 'name' => 'create-user-button']) ?>
                       

                   
                          
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                        
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                <!-- </div> -->
        
        
<script>
    
    <?php
    
        if(isset($selectedDepartmentArr) && $selectedDepartmentArr != ''){
    ?>
    var selectedValues = new Array();
    <?php
        foreach($selectedDepartmentArr as $selectedDepartment){
    ?>
        selectedValues.push('<?php echo $selectedDepartment;?>');
    <?php }  ?>
        $('#documentdepartments-department_id').val(selectedValues);
    <?php }  ?>
    
    <?php
    if($model->id != '' || $model->old_id != ''){
        ?>
            
            $('#document_type').val('<?php echo $model->document_type_id;?>');
            setTimeout(function() {
                $('#document_type').trigger('change');
            }, 200);
            
           
             $('#document-vendor_id').val('<?php echo $model->vendor_id;?>');
            setTimeout(function() {
                $('#document-vendor_id').trigger('change');
            }, 200);
    <?php } ?>
        
    var globalFlag = 1;
    
    
    $('body').on('change', '#document-document_path', function(){
        if (this.files && this.files[0]) {
            var avatar = $(this).val();
            //var extension = avatar.split('.').pop().toUpperCase();
            //if (extension === "APK"){
                if(this.files[0].size <= 40000000){
                    globalFlag = 1;
                } else {
                    globalFlag = 0;
                    alert('Up to 40MB file size is allowed.');
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
        var department = $('#documentdepartments-department_id').val();
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
        var agreementType = $('#document-agreement_type_id').val();

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
            $('#documentdepartments-department_id').next('.help-block-error').text('Please select department');
            $('#documentdepartments-department_id').parent('.form-group').addClass('has-error');
        } else {
            $('#documentdepartments-department_id').next('.help-block-error').text('');
            $('#documentdepartments-department_id').parent('.form-group').removeClass('has-error');
        }
        
        if(name == ''){
            flag = 0;
            $('#document-name').next('.help-block-error').text('Name cannot be blank.');
            $('#document-name').parent('.form-group').addClass('has-error');
        } else {
            $('#document-name').next('.help-block-error').text('');
            $('#document-name').parent('.form-group').removeClass('has-error');
        }
            
        
        
        if(documentType == 2500001 || documentType == 2500004 ||  documentType == 2500005){
            if(vendor == ''){
                flag = 0;
                $('#document-vendor_id').next('.help-block-error').text('Please select vendor');
                $('#document-vendor_id').parent('.form-group').addClass('has-error');
            } else {
                $('#document-vendor_id').next('.help-block-error').text('');
                $('#document-vendor_id').parent('.form-group').removeClass('has-error');
            }
            
            if(agreementType == ''){
                flag = 0;
                $('#document-agreement_type_id').next('.help-block-error').text('Please select agreement type.');
                $('#document-agreement_type_id').parent('.form-group').addClass('has-error');
            } else {
                $('#document-agreement_type_id').next('.help-block-error').text('');
                $('#document-agreement_type_id').parent('.form-group').removeClass('has-error');
            }
            
            if(scope == ''){
                flag = 0;
                $('#document-scope_of_work').next('.help-block-error').text('Please enter scope of work.');
                $('#document-scope_of_work').parent('.form-group').addClass('has-error');
            } else {
                if(scope.length < 8){
                    $('#document-scope_of_work').next('.help-block-error').text('Scope of Work should contain at least 25 characters.');
                    $('#document-scope_of_work').parent('.form-group').addClass('has-error');
                } else if (scope.length > 1000){
                    $('#document-scope_of_work').next('.help-block-error').text('Scope of Work should contain at most 1000 characters.');
                    $('#document-scope_of_work').parent('.form-group').addClass('has-error');
                } else {
                    $('#document-scope_of_work').next('.help-block-error').text('');
                    $('#document-scope_of_work').parent('.form-group').removeClass('has-error');    
                }
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
                   
                   <?php if($model->id != '' || $model->old_id != ''){?>
                       $('#document_type').attr('disabled', true);
                       $('#document_type').val('<?php echo $model->document_type_id;?>');
                       
                       //$('#document-vendor_id').attr('disabled', true);
                       $('#document-vendor_id').val('<?php echo $model->vendor_id;?>');
                       
                       //$('#document-department_id').attr('disabled', true);
                       //$('#document-department_id').val('<?php //echo $model->department_id;?>');
                   <?php } ?>    
                       
                   var option = $(e).val();
                   if(option == 2500001 || option == 2500004 || option == 2500005){
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
