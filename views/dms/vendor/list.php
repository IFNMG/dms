<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \Yii::t('app', 'Vendor Management');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/jquery-ui.js');
$this->registerCssFile('@web/css/jquery-ui.css');
?>



<script>
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = 'language' + '=' + '<?php echo $lang; ?>' + ';expires=' + expires.toUTCString();
</script>

<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', $this->title);?>
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
                <a class="btn btn-success btn-flat pull-left col-lg-1" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/vendor/add"]); ?>" class="btn btn-primary btn-flat pull-right">
                    <?php echo \Yii::t('app', 'Add');?>
                </a>
                <?php } ?>
            </div>
            
            
            
            <div class="box-header with-border">
               
                <div style="padding: 15px 0px 0px 20px;" class="row">
                    
                    <div class="col-lg-4 form-group">
                        <label style="margin-right: 10px;">Vendor Code</label>
                        <input type="text" id="vendor_code" name="" placeholder="Vendor Code" class="form-control">
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    <div class="col-lg-4 form-group">
                        <label style="margin-right: 10px;">Vendor Name</label>
                        <input type="text" id="vendor_name" name="" placeholder="Vendor Name" class="form-control">
                        <p class="help-block help-block-error"></p>
                    </div>
                    
                    
                    
                    
                    <div class="col-lg-2 form-group">
                        <label style="margin-right: 10px;">Status</label>
                        <?php
                        $statusList = \app\models\Lookups::find()->select(['id', 'value'])->orderBy(['id' => SORT_ASC])->andWhere(['is_delete'=>1, 'type'=>10])->all();
                        if($statusList){?>
                            <select class="form-control" id="vendor_status">
                                <option value="">--Select Status--</option>
                            <?php foreach($statusList as $status){ ?>
                                <option value="<?php echo $status->id; ?>"><?php echo $status->value; ?></option>
                            <?php } ?>
                            </select>
                        <?php }?>
                    </div>
                    
                    
                    <a id="viewList" style="padding:11px 12px; margin-top: 26px; " class="btn my-btn ">Search</a>
                    <a onclick="reset();" style="padding:11px 12px; margin-top: 26px; " class="btn my-btn ">Reset</a>
                </div>
                
                
            </div>
            
            
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                
                
                <div class="form-group tableDiv" style="padding: 15px 15px 0px 20px; display:none;">
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style=""><?php echo \Yii::t('app', 'Vendor Code');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Vendor Name');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Status');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Change Status');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Added On');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Last Modified On');?></th>
                                <th style=""><?php echo \Yii::t('app', 'Last Modified By');?></th>
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


  
<script>
function reset(){
        window.location="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/vendor/list"]); ?>";
    }
    
    function changeVendorStatus(id){       
        
        var retVal="";       
        retVal = confirm("Do you want to continue ?");
        var status = $('#vendor_status_change_'+id).val();
        
        if(status != ""){
            if( retVal == true ){
                $.ajax({
                   type:'post',
                   data:{
                       id: id,
                       status: status
                   },
                   url : '<?php echo Yii::getAlias('@web'). '/index.php/dms/vendor/activatedeactivate'; ?>',
                   success:function(data) {
                       $('#viewList').trigger('click');
                        //if(status == 550001){
                        //    var label = '<label id=vendor_'.id.'>Disabled</label>';
                        //} else {
                        //    var label = '<label id=vendor_'.id.'>Enabled</label>';
                        //}
                        //$('#vendor_'+id).html(label);

                    }
                });
            }
        }
    }
    
    $(document).ready(function() {
        //$('#viewList').trigger('click');
        
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
    });
    $('.tableDiv').show();
    
    $('#viewList').click(function () {
        var name = $('#vendor_name').val();
        var code = $('#vendor_code').val();
        var status = $('#vendor_status').val();
        
        
        var table = $('#example').DataTable();
        table.destroy();
        $('#example').DataTable( {
            "processing": true,
            "serverSide": true,
            "bFilter": false,
            "ajax": {
                "url": "viewlist",
                "data": function ( d) {
                    d.name = name;
                    d.code = code;
                    d.status = status;
                }
            }
        });
            
    }); 
    
    
    
    
    </script>