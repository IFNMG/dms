<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \Yii::t('app', 'Documents');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/jquery-ui.js');
$this->registerCssFile('@web/css/jquery-ui.css');
?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">

<script>
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = 'language' + '=' + '<?php echo $lang; ?>' + ';expires=' + expires.toUTCString();
</script>

<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Documents');?>
            <small><?php echo \Yii::t('app', 'List');?></small>
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
                <?php if($permission->add == 1){ ?>
                <a class="btn my-btn1 pull-left col-lg-1" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/add"]); ?>" class="btn btn-primary btn-flat pull-right">
                    <?php echo \Yii::t('app', 'Add');?>
                </a>
                <?php } ?>
            </div>
            <?php //$vendorList = \app\models\Vendor::find()->orderBy(['name' => SORT_ASC])->andWhere(['status'=>550001, 'is_delete'=>1])->all();?>
            <?php $documentTypeList = \app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>49, 'is_delete'=>1])->all();?>
            <?php $departmentList = \app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>45, 'is_delete'=>1])->all();?>
            <?php 
                
                $department = 2300001;
                $UserObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
                if($UserObj){
                    if($UserObj->department_id != ''){
                        $department = $UserObj->department_id;
                    }
                }
            ?>
            
            <div class="box-header with-border">
               
                <div style="margin:0;" class="row">
                    
                    <?php //if($department == 2300001){ ?>
                    <div class="col-lg-2 form-group formControl7">
                        <label style="margin-right: 10px;">Department</label>
                        <select id="department_id" class="form-control">
                        <?php                            
                        if($departmentList) {
                            echo "<option value=''>".\Yii::t('app', '--Department--')."</option>";
                             foreach($departmentList as $post){?>
                                  <option value='<?= $post->id; ?>'><?= $post->value; ?></option>
                             <?php }
                        } else {
                            echo "<option value=''>".\Yii::t('app', '--Department--')."</option>";
                        }
                        ?>
                        </select>   
                        <p class="help-block help-block-error"></p>
                    </div>
                    <?php //} ?>
                    
                    <!--div class="col-lg-2 form-group" style="width: 14%;">
                        <label style="margin-right: 10px;">Document Name</label>
                        <input type="text" id="document_name" name="" placeholder="Document Name" class="form-control">
                        <p class="help-block help-block-error"></p>
                    </div-->
                    
                    <div class="col-lg-2 form-group formControl7">
                        <label style="margin-right: 10px;">Uploaded By</label>
                        <input type="text" id="uploaded_by" name="" placeholder="Uploaded By" class="form-control">
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    <div class="col-lg-2 form-group formControl7">
                        <label style="margin-right: 10px;">Document Type</label>
                        <select id="document_type" class="form-control">
                        <?php                            
                        if($documentTypeList) {
                            echo "<option value=''>".\Yii::t('app', '--Document Type--')."</option>";
                             foreach($documentTypeList as $post){?>
                                  <option value='<?= $post->id; ?>'><?= $post->value; ?></option>
                             <?php }
                        } else {
                            echo "<option value=''>".\Yii::t('app', '--Document Type--')."</option>";
                        }
                        ?>
                        </select>   
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    <div class="col-lg-2 form-group agreement formControl7">
                        <label style="margin-right: 10px;">Vendor</label>
                        <input type="text" id="vendor_id" name="" placeholder="Vendor Name" class="form-control">
                        <input type="hidden" id="vendor_id_hidden">
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    <div class="col-lg-2 form-group formControl7">
                        <label style="margin-right: 10px;">Valid From</label>
                        <input type="text" id="valid_from" name="" placeholder="From Date" class="form-control" readonly="readonly" >
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    <div class="col-lg-2 form-group formControl7">
                        <label style="margin-right: 10px;">Valid Till</label>
                        <input type="text" id="valid_till" name="" placeholder="To Date" class="form-control" readonly="readonly" >
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                   
                    
                    
                    
                    <div class="col-lg-1 form-group formControl7" style="min-width:183px !important;">
                        <label style="margin-right: 10px;">Status</label>
                        <?php
                        $statusList = \app\models\Lookups::find()->select(['id', 'value'])->orderBy(['id' => SORT_ASC])->andWhere(['is_delete'=>1, 'type'=>51])->all();
                        if($statusList){?>
                            <select class="form-control" id="document_status">
                                <option value="">--Status--</option>
                            <?php foreach($statusList as $status){ ?>
                                <?php if($status->id != 2600006){ ?>
                                    <option value="<?php echo $status->id; ?>"><?php echo $status->value; ?></option>
                                <?php } ?>
                            <?php } ?>
                            </select>
                        <?php }?>
                    </div>
                    
                    <?php //if($department != 2300001){ ?>
                    <!--div class="col-lg-3 form-group" style="width: 14%;">
                        <div class="checkbox" style="margin-top: 28px;">
                            <label for="document-is_locked" style="">
                                <input type="checkbox" value="" class="" id="applicable_to_all" name="applicable_to_all">
                                Applicable to all
                            </label>
                        </div>
                    </div-->
                    <?php //} ?>
                    
                    <a id="viewList" style="margin-top:25px;" class="btn my-btn commanLinkStyle">Search</a>
                    <a onclick="reset();" style="margin-top:25px;" class="btn my-btn commanLinkStyle">Reset</a>
                </div>
                
                
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                
                
                <div class="form-group tableDiv" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo \Yii::t('app', 'Department');?></th>
                                <th><?php echo \Yii::t('app', 'Type');?></th>
                                <th><?php echo \Yii::t('app', 'Vendor/Process Name');?></th>
                                <th><?php echo \Yii::t('app', 'Uploaded By');?></th>
                                <th style="text-align: right;"><?php echo \Yii::t('app', 'Uploaded On');?></th>
                                <th><?php echo \Yii::t('app', 'Status');?></th>
                                <th><?php echo \Yii::t('app', 'Download');?></th>
                                <th style="text-align: left;"><?php echo \Yii::t('app', 'Document Name');?></th>
                                <th style="text-align: right;"><?php echo \Yii::t('app', 'Version');?></th>
                                <th><?php echo \Yii::t('app', 'Action');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                
                
              
            </div><!-- /.box-body -->
            
            <div class="box-footer">
               
            </div>
            
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->


  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js" ></script>
<script>
    /*
    $(function() {
        $( "#vendor_id" ).autocomplete({
           source: '<?php //echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/getvendor"]); ?>',
           minLength:2
        });
    });
     */    
       
    $("#vendor_id").autocomplete({
        search: function () {},
        source: function (request, response)
        {
            $.ajax(
            {
                url: '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/getvendor"]); ?>',
                dataType: "json",
                data:{
                    term: request.term,
                },
                success: function (data){
                    response(data);
                }
            });
        },
        minLength: 0,
        select: function(event, ui) {
            event.preventDefault();
            $("#vendor_id").val(ui.item.label);
            $('#vendor_id_hidden').val(ui.item.value);
        },
        focus: function(event, ui) {
            event.preventDefault();
            $("#vendor_id").val(ui.item.label);
            $('#vendor_id_hidden').val(ui.item.value);
        }
    });
    
    
    
    function alertMe(id){
        var url = '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/alertme"]); ?>';
               
        var retVal="";       
            retVal = confirm("Do you want to continue ?");
            
        if( retVal == true ){
                $.ajax({
                   type:'post',
                   data:{
                       id: id,
                   },
                   url : url,
                   success:function(status) {
                        var parsedData = JSON.parse(status);
                        if(parsedData.CODE == 200){
                            $('#alert_'+id).html(parsedData.DATA);
                            alert(parsedData.MESSAGE);
                        } else if(parsedData.CODE == 100) {
                            alert(parsedData.MESSAGE);
                        }
                    }
                });
            }
       
    }
    
    function reset(){
        window.location="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>";
    }
    
    $(function() {
            $(function () {
                $("#valid_from").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    //maxDate: new Date,
                    onSelect: function (selected) {
                        var dt = new Date(selected);
                        dt.setDate(dt.getDate() + 1);
                        $("#valid_till").datepicker("option", "minDate", dt);
                    }
                });
                $("#valid_till").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    //maxDate: new Date,
                    onSelect: function (selected) {
                        var dt = new Date(selected);
                        dt.setDate(dt.getDate() - 1);
                        $("#valid_from").datepicker("option", "maxDate", dt);
                    }
                });
                //}).datepicker("setDate", "0");
            });
        });
    
    $(document).ready(function() {
        
       
        <?php if($source == 'list'){?>
            $('#example').DataTable( {
                "processing": true,
                "serverSide": true,
                "bFilter": false,
                "ajax": {
                    "url": "viewlist",
                    "data": function ( d) {
                     }
                }
            });
        <?php } else { ?>
            
            <?php if($scenario == 'agreement'){ ?>
                $('#document_type').val(2500001);
            <?php } else if($scenario == 'policy'){ ?>
                $('#document_type').val(2500002);
            <?php } else if($scenario == 'sop'){ ?>
                $('#document_type').val(2500003);
            <?php } else if($scenario == 'expiring'){ ?>
                var date = new Date();
                
                $("#valid_from").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                }).datepicker("setDate", new Date(date.getFullYear(), date.getMonth(), 1));
                
                $("#valid_till").datepicker({
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                }).datepicker("setDate", new Date(date.getFullYear(), date.getMonth() + 1, 0));
            <?php }?>
                
                
                var userRole = '<?php echo $UserObj->user->role;?>';
                
                setTimeout(function() {
                    if(userRole == 100001 || userRole == 100004 || userRole == 100005){
                        $('#document_status').val(2600001);
                    } else if(userRole == 100008){
                        $('#document_status').val(2600002);
                    }
                    $('#document_type').trigger('change');
                    $('#document_status').trigger('change');
            
                }, 200);
                
            
                
            $('#example').DataTable( {
                "processing": true,
                "serverSide": true,
                "bFilter": false,
                "ajax": {
                    "url": "dashboardlist",
                    "data": function (d) {
                        d.scenario = '<?php echo $scenario; ?>';
                        d.term = '<?php echo $term; ?>';
                    }
                }
            });
        <?php } ?>
    });
    
    $('.tableDiv').show();
    
    $('#viewList').click(function () {
        var flag = 1;
        var department = $('#department_id').val();
        var uploaded_by = $('#uploaded_by').val();
        var document_type = $('#document_type').val();
        var valid_from = $('#valid_from').val();
        var valid_till = $('#valid_till').val();
        var document_status = $('#document_status').val();
        var vendor_id = $('#vendor_id').val();
        var vendor_id_hidden = $('#vendor_id_hidden').val();
        var applicable_to_all = 0;
        if($('#applicable_to_all').is(':checked')){
            applicable_to_all = 1;
        }
        
        if(vendor_id != ''){
            vendor_id = vendor_id_hidden;
        } else {
            vendor_id = '';
        }
        
        /*
        if(valid_from != '' && valid_till == ''){
            flag = 0;
            alert('Please select to date.');
        } else if(valid_from == '' && valid_till != ''){
            flag = 0;
            alert('Please select from date.');
        }
        */
        
        if(flag == 1){
        
            var table = $('#example').DataTable();
            table.destroy();
            $('#example').DataTable( {
                "processing": true,
                "serverSide": true,
                "bFilter": false,
                "ajax": {
                    "url": "viewlist",
                    "data": function ( d) {
                        d.department = department;
                        d.uploaded_by = uploaded_by;
                        d.document_type = document_type;
                        d.valid_from = valid_from;
                        d.valid_till = valid_till;
                        d.document_status = document_status;
                        d.applicable_to_all = applicable_to_all;
                        d.vendor_id = vendor_id;
                    }
                }
            });
        }
    }); 
    
    
    function permanentArchive(id, url, e){
        
        if(id != ''){
            var url = url; 
            var retVal = confirm("Do you want to continue ?");
            if( retVal == true ){
                $.ajax({
                    type:'post',
                    data:{
                        id: id,
                        
                    },
                    url:url,
                    success:function(status) {
                        var table = $('#listing-table').DataTable();
                        $('#viewList').trigger('click');

                        /*
                        var parsedData = JSON.parse(status);                       
                        if(parsedData.CODE == 200){                            
                            $('#tr_'+e).parents('tr').remove();
                            table
                                .row( $(e).parents('tr') )
                                .remove()
                                .draw();
                        } else if(parsedData.CODE == 100) {
                            alert(parsedData.MESSAGE);
                        }*/
                        alert('Archived Successfully');
                    }
                });
              
            } 
            
            
       }   
    }
    
    
    </script>
