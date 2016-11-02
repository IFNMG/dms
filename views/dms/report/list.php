<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \Yii::t('app', 'Redocument4rt');
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
            <?php echo \Yii::t('app', ucfirst($type));?>
            <small><?php echo \Yii::t('app', 'Redocument4rt');?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    
    <?php
    
    $redocument4rtType = 2500001;
    if($type == 'document1'){
        $redocument4rtType = 2500001;
    } else if($type == 'document2'){
        $redocument4rtType = 2500005;
    }else if($type == 'document3'){
        $redocument4rtType = 2500002;
    } else if($type == 'document4'){
        $redocument4rtType = 2500003;
    } 
    
    ?>
    
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            
            <?php //$documentTypeList = \app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>49, 'is_delete'=>1])->all();?>
            <?php $departmentList = \app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>45, 'is_delete'=>1])->all();?>
            <?php //$vendorList = \app\models\Vendor::find()->orderBy(['name' => SORT_ASC])->andWhere(['status'=>550001, 'is_delete'=>1])->all();?>
            <?php 
                $department = 2300001;
                $UserObj = \app\models\AdminPersonal::find()->select(['department_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
                if($UserObj){
                    if($UserObj->department_id != ''){
                        $department = $UserObj->department_id;
                    }
                }
            ?>
            
            <div class="box-header with-border">
               
                <div style="padding: 15px 0px 0px 20px;" class="row">
                    
                    <!--div class="col-lg-2 form-group">
                        <label style="margin-right: 10px;">Document Type</label>
                        <select id="document_type" class="form-control" onchange="showForm(this);">
                        <?php                            
                        /*
                        if($documentTypeList) {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                             foreach($documentTypeList as $document4st){?>
                                  <option value='<?= $document4st->id; ?>'><?= $document4st->value; ?></option>
                             <?php }
                        } else {
                            echo "<option value=''>".\Yii::t('app', '-- Select Document Type --')."</option>";
                        }
                         * 
                         */
                        ?>
                        </select>   
                        <p class="help-block help-block-error"></p>
                    </div-->
                    
                    <form action="<?php echo Yii::$app->urlManager->createUrl(["index.php/dms/redocument4rt/exdocument4rt"]); ?>" method="document4st" id="filter-form">
                        <?php //if($department == 2300001){ ?>
                        <div style="display: none;" class="col-lg-2 form-group document1 document4  document3 document2">
                            <label style="margin-right: 10px;">Department</label>
                            <select id="department_id" class="form-control" name="department_id">
                            <?php                            
                            if($departmentList) {
                                echo "<option value=''>".\Yii::t('app', '-- Select Department --')."</option>";
                                 foreach($departmentList as $document4st){?>
                                      <option value='<?= $document4st->id; ?>'><?= $document4st->value; ?></option>
                                 <?php }
                            } else {
                                echo "<option value=''>".\Yii::t('app', '-- Select Department --')."</option>";
                            }
                            ?>
                            </select>   
                            <p class="help-block help-block-error"></p>
                        </div>
                        <?php //} ?>

                        <div class="col-lg-2 form-group document1 document4" style="width: 15%;">
                            <label style="margin-right: 10px;">Vendor</label>
                            <input type="text" id="vendor_id" name="vendor_id" placeholder="Vendor Name" class="form-control">
                            <input type="hidden" id="vendor_id_hidden" name="vendor_id_hidden">
                            <p class="help-block help-block-error"></p>
                        </div>




                        <div style="display: none;" class="col-lg-2 form-group document2">
                            <label style="margin-right: 10px;">Policy Header</label>
                            <input type="text" id="document2_header" name="document2_header" placeholder="Policy Header" class="form-control">
                            <p class="help-block help-block-error"></p>
                        </div>

                        <div style="display: none;" class="col-lg-2 form-group document3">
                            <label style="margin-right: 10px;">Process Name</label>
                            <input type="text" id="process_name" name="process_name" placeholder="Process Name" class="form-control">
                            <p class="help-block help-block-error"></p>
                        </div>


                        <div style="display: none;" class="col-lg-2 form-group document1 document2 document3 document4">
                            <label style="margin-right: 10px;">Valid From</label>
                            <input type="text" id="valid_from" name="valid_from" placeholder="From Date" class="form-control" readonly="readonly" >
                            <p class="help-block help-block-error"></p>
                        </div>

                        <div style="display: none;" class="col-lg-2 form-group document1 document2 document3 document4">
                            <label style="margin-right: 10px;">Valid Till</label>
                            <input type="text" id="valid_till" name="valid_till" placeholder="To Date" class="form-control" readonly="readonly" >
                            <p class="help-block help-block-error"></p>
                        </div>
                        <input  name="document_type" id="document_type" value="<?php echo $redocument4rtType; ?>" type="hidden">
                    
                    </form>
                    
                    
                    
                    
                    <a id="viewList" style="padding:11px 12px; margin-top: 26px; " class="btn my-btn ">Search</a>
                    <a onclick="reset();" style="padding:11px 12px; margin-top: 26px; " class="btn my-btn ">Reset</a>

                        
                </div>
                
                
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <a onclick="Exdocument4rt()" class="btn btn-success btn-flat pull-right col-lg-2">Exdocument4rt To Excel</a>
                
                
                <div class="form-group document1_Div" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="document1" class="display" cellspacing="0" width="100%" style="display:none;">
                        <thead>
                            <tr>
                                <th><?php echo \Yii::t('app', 'Document Name');?></th>
                                <th><?php echo \Yii::t('app', 'Department');?></th>
                                <th><?php echo \Yii::t('app', 'Vendor Name');?></th>
                                <th><?php echo \Yii::t('app', 'Vendor Code');?></th>
                                <th><?php echo \Yii::t('app', 'Scope of Work');?></th>
                                <th><?php echo \Yii::t('app', 'Valid From');?></th>
                                <th><?php echo \Yii::t('app', 'Valid Till');?></th>
                                <th><?php echo \Yii::t('app', 'Expiry Status');?></th>
                                <th><?php echo \Yii::t('app', 'Payment Terms');?></th>
                                <th><?php echo \Yii::t('app', 'Fee');?></th>
                                <th><?php echo \Yii::t('app', 'Uploaded By');?></th>
                                <th><?php echo \Yii::t('app', 'Download');?></th>
                                <th><?php echo \Yii::t('app', 'Action');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div class="form-group document4_Div" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="document4" class="display" cellspacing="0" width="100%" style="display:none;">
                        <thead>
                            <tr>
                                <th><?php echo \Yii::t('app', 'Document Name');?></th>
                                <th><?php echo \Yii::t('app', 'Department');?></th>
                                <th><?php echo \Yii::t('app', 'Vendor Name');?></th>
                                <th><?php echo \Yii::t('app', 'Vendor Code');?></th>
                                <th><?php echo \Yii::t('app', 'Scope of Work');?></th>
                                <th><?php echo \Yii::t('app', 'Valid From');?></th>
                                <th><?php echo \Yii::t('app', 'Valid Till');?></th>
                                <th><?php echo \Yii::t('app', 'Expiry Status');?></th>
                                <th><?php echo \Yii::t('app', 'Payment Terms');?></th>
                                <th><?php echo \Yii::t('app', 'Fee');?></th>
                                <th><?php echo \Yii::t('app', 'Uploaded By');?></th>
                                <th><?php echo \Yii::t('app', 'Download');?></th>
                                <th><?php echo \Yii::t('app', 'Action');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div class="form-group document2_Div" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="document2" class="display" cellspacing="0" width="100%" style="display:none;">
                        <thead>
                            <tr>
                                <th><?php echo \Yii::t('app', 'Document Name');?></th>
                                <th><?php echo \Yii::t('app', 'Department');?></th>
                                <th><?php echo \Yii::t('app', 'Polcy Header');?></th>
                                <th><?php echo \Yii::t('app', 'Valid From');?></th>
                                <th><?php echo \Yii::t('app', 'Valid Till');?></th>
                                <th><?php echo \Yii::t('app', 'Approved By');?></th>
                                <th><?php echo \Yii::t('app', 'Download');?></th>
                                <th><?php echo \Yii::t('app', 'Action');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div class="form-group document3_Div" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="document3" class="display" cellspacing="0" width="100%" style="display:none;">
                        <thead>
                            <tr>
                                <th><?php echo \Yii::t('app', 'Document Name');?></th>
                                <th><?php echo \Yii::t('app', 'Department');?></th>
                                <th><?php echo \Yii::t('app', 'Process Name');?></th>
                                <th><?php echo \Yii::t('app', 'Prepared By');?></th>
                                <th><?php echo \Yii::t('app', 'Download');?></th>
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
    
    $("#vendor_id").autocomplete({
        search: function () {},
        source: function (request, resdocument4nse)
        {
            $.ajax(
            {
                url: '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/getvendor"]); ?>',
                dataType: "json",
                data:{
                    term: request.term,
                },
                success: function (data){
                    resdocument4nse(data);
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
    
    
    function showForm(){
        var option = '<?php echo $redocument4rtType;?>';
        if(option == 2500001){
            $('.document2').hide();
            $('.document3').hide();
            $('.document4').hide();
            $('.document1').show();
            $('#viewList').show();
            $('#reset').show();
            $('#document-document_type_id').val(option);
        } else if(option == 2500005){
            $('.document2').hide();
            $('.document3').hide();
            $('.document1').hide();
            $('.document4').show();
            $('#viewList').show();
            $('#reset').show();
            $('#document-document_type_id').val(option);
        } else if(option == 2500002){
            $('.document1').hide();
            $('.document3').hide();
            $('.document4').hide();
            $('.document2').show();
            $('#viewList').show();
            $('#reset').show();
            $('#document-document_type_id').val(option);
        } else if(option == 2500003){
            $('.document1').hide();
            $('.document2').hide();
            $('.document4').hide();
            $('.document3').show();
            $('#viewList').show();
            $('#reset').show();
            $('#document-document_type_id').val(option);
        } else {
            $('.document1').hide();
             $('.document2').hide();
             $('.document3').hide();
             $('.document4').hide();
             $('#viewList').hide();
             $('#reset').hide();
             $('#document-document_type_id').val('');
        }

    }
               
               
    function reset(){
        window.location="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/redocument4rt/list?type=$type"]); ?>";
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
        showForm();
        $('#'+'<?php echo $type; ?>').DataTable( {
            "processing": true,
            "serverSide": true,
            "bFilter": false,
            "ajax": {
                "url": "viewlist",
                "data": function (d) {
                    d.document_type = '<?php echo $redocument4rtType; ?>';
                }
            }
        });
        
        $('.'+'<?php echo $type;?>_Div').show();
        $('#'+'<?php echo $type;?>').show();
    });
    
    $('.tableDiv').show();
    
    
    function Exdocument4rt(){
        
        document.getElementById('filter-form').submit();
        
    }
    
    
    
    $('#viewList').click(function () {
        
        $('.document1_Div').hide();
        $('.document2_Div').hide();
        $('.document3_Div').hide();
        $('.document4_Div').hide();
        
        var flag = 1;
        var document_type = '<?php echo $redocument4rtType; ?>';
        var department = $('#department_id').val();
        var vendor = $('#vendor_id_hidden').val();
        var valid_from = $('#valid_from').val();
        var valid_till = $('#valid_till').val();
        var document_status = $('#document_status').val();
        var document2_header = $('#document2_header').val();
        var process_name = $('#process_name').val();
        var tableName = 'document1';
        var divName = 'document1_Div';
        
        if(document_type == 2500001){
            tableName = 'document1';
            divName = 'document1_Div';
        } else if(document_type == 2500005){
            tableName = 'document4';
            divName = 'document4_Div';
        }else if(document_type == 2500002){
            tableName = 'document2';
            divName = 'document2_Div';
        } else if(document_type == 2500003){
            tableName = 'document3';
            divName = 'document3_Div';
        }
        
        if(flag == 1){
        
            var table = $('#'+tableName).DataTable();
            table.destroy();
            $('#'+tableName).DataTable( {
                "processing": true,
                "serverSide": true,
                "bFilter": false,
                "ajax": {
                    "url": "viewlist",
                    "data": function ( d) {
                        d.department = department;
                        d.document_type = document_type;
                        d.valid_from = valid_from;
                        d.valid_till = valid_till;
                        d.vendor = vendor;
                        d.document_status = document_status;
                        d.document2_header = document2_header;
                        d.process_name = process_name;
                    }
                }
            });
            $('.'+divName).show();
            $('#'+tableName).show();
        }
    }); 
    
    
    
    
    </script>