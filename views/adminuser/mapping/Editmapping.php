<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title = \yii::t('app', 'Role-Permission Mapping');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Role-Permission Mapping');?>
            <small><?php echo \yii::t('app', 'List');?></small>
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
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left"><?php echo \yii::t('app', 'Go Back');?></a>         
                    </div>
                    <div class="col-lg-8">
                        <h3 class="profile-username text-center"><?php echo \Yii::t('app',$role->value) ; ?></h3><br/>
                        <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'User Type') ; ?>:</strong> <?php if($role->parent) { echo \yii::t('app', $role->parent->value) ; } ?></p><br/>
                        <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Description') ; ?>:</strong> <?php echo \yii::t('app', $role->description); ?></p><br/>
                    </div>
                    <div class="col-lg-2">
                        <?php  if($permission->view == 1){ ?>
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/view", 'Id'=>$role->id]); ?>" style="float: right;" class="btn btn-primary"><?php echo \yii::t('app', 'View');?></a>
                        <?php } ?>
                        
                    </div>
                </div>
                
                
                
                <?php
                
                foreach($model as $type){ ?>
                <div class="section">
                <br>
                    
                    <?php if($type['id'] == LookupCodes::L_PERMISSION_TYPES_MENU_LEVEL){ ?>
                        <div class="col-md-12 well">
                            <h2 style="margin: 0px; width: 50%; float: left;">
                                <?php echo \Yii::t('app', $type['value']) ;?> 
                            </h2>
                            <a id="check_<?php echo $type['id'] ;?>" class="btn btn-primary pull-right checkClass"><?php echo \yii::t('app', 'Check All');?></a>
                            <a id="uncheck_<?php echo $type['id'] ;?>" class="btn btn-primary pull-right uncheckClass" style="margin-right: 10px;"><?php echo \yii::t('app', 'Uncheck All');?></a>
                        </div>
                
                        <table class="table table-hover" id="permission-list1">
                        <thead>
                            <tr>
                                <th><?php echo \yii::t('app', 'Permission');?></th>
                                <th><?php echo \yii::t('app', 'Active');?></th>
                                <th><?php echo \yii::t('app', 'Add');?></th>
                                <th><?php echo \yii::t('app', 'Edit');?></th>
                                <th><?php echo \yii::t('app', 'Delete');?></th>
                                <th><?php echo \yii::t('app', 'View');?></th>
                                <th><?php echo \yii::t('app', 'List');?></th>
                                <th><?php echo \yii::t('app', 'Change Status');?></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $tempArr = array();
                                foreach($type['PermissionList'] as $list){ 
                                $permissionId = $list['id'];
                                array_push($tempArr, $permissionId);
                                
                                ?>
                            
                            <?php if($list['level'] == 1){
                                    $bgcolor = 'background-color:transparent'; 
                                    $border = "";
                                  } else if($list['level'] == 2){
                                    $bgcolor = 'background-color:#ecf0f5;'; 
                                    $border = "border-left: 50px solid transparent;";
                                  } else if($list['level'] == 3){
                                    $bgcolor = 'background-color:#E6E6E6;'; 
                                    $border = "border-left: 100px solid transparent;";
                                  } else if($list['level'] == 4){
                                    $bgcolor = 'background-color:#D8D8D8;'; 
                                    $border = "border-left: 100px solid transparent;";
                                  } else { 
                                    $bgcolor = 'background-color:transparent'; 
                                    $border = "";
                                  } 
                                  
                                  /*
                                if($list['level'] != 1){
                                    $margin = $list['level']*50;
                                    $margin = $margin.'px';
                                    $bgcolor = 'background-color:#ecf0f5;'; 
                                    $border = "border-left: $margin solid transparent;";
                                } else {
                                    $bgcolor = 'background-color:transparent'; 
                                    $border = "";
                                }
                                   * 
                                   */
                            ?>
                            <tr class="parent_<?php echo $list['parentId']; ?>" id="tr_<?php echo $permissionId;?>" style="<?php echo $bgcolor; ?><?php echo $border;?>">
                                <td><?php echo \Yii::t('app', $list['value']) ;?></td>
                                <td style="">
                                    <input class="defaultClass commonClass" <?php if($list['obj']['default'] == 1){ echo 'checked'; }; ?> type="checkbox" id="default_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input type="hidden" value="<?php echo $list['obj']['mapping_id']; ?>" id="mapping_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $role->id; ?>" id="role_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $permissionId; ?>" id="permission_<?php echo $permissionId; ?>">
                                    <input class="commonClass" <?php if($list['obj']['add'] == 1){ echo 'checked'; }; ?> type="checkbox" id="add_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['edit'] == 1){ echo 'checked'; }; ?> type="checkbox" id="edit_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['delete'] == 1){ echo 'checked'; }; ?> type="checkbox" id="delete_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['view'] == 1){ echo 'checked'; }; ?> type="checkbox" id="view_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['list'] == 1){ echo 'checked'; }; ?> type="checkbox" id="list_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['change_status'] == 1){ echo 'checked'; }; ?> type="checkbox" id="change_status_<?php echo $permissionId; ?>" value=""/>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    <?php }  else { ?>
                        <div class="col-md-12 well">
                            <h2 style="margin: 0px; width: 50%; float: left;">
                                <?php echo \Yii::t('app', $type['value']) ;?> 
                            </h2>
                        </div>
                        <table class="table table-hover" id="permission-list2" style="width: 38.5%;">
                        <thead>
                            <tr>
                                <th><?php echo \yii::t('app', 'Permission');?></th>
                                <th><?php echo \yii::t('app', 'Active');?></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php 
                                foreach($type['PermissionList'] as $list){ 
                                $permissionId = $list['id'];
                                array_push($tempArr, $permissionId);
                                ?>
                            <tr>
                                <input type="hidden" value="<?php echo $list['obj']['mapping_id']; ?>" id="mapping_<?php echo $permissionId; ?>">
                                <input type="hidden" value="<?php echo $role->id; ?>" id="role_<?php echo $permissionId; ?>">
                                <input type="hidden" value="<?php echo $permissionId; ?>" id="permission_<?php echo $permissionId; ?>">
                                <td style=""><?php echo \Yii::t('app', $list['value']) ;?></td>
                                <td style="">
                                    <input <?php if($list['obj']['default'] == 1){ echo 'checked'; }; ?> type="checkbox" id="default_<?php echo $permissionId; ?>" value=""/>
                                </td>
                            </tr>
                                <?php } ?>
                        </tbody>
                        
                        </table>
                    <?php } ?>
                </div>
                <?php } ?>
                
               <a style="margin-top: 30px; float: right;" onclick="submitForm();" class="btn btn-primary"><?php echo \Yii::t('app', 'Save') ;?></a>
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->



<script>
    function submitForm(){
        var url = '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/add"]); ?>';
        var finalArray = [];
        var js_array = [<?php echo '"'.implode('","', $tempArr).'"' ?>];
        
        
        for(var i = 0; i < js_array.length; i++){
            var add = 0;
            var edit = 0; 
            var delete1 = 0; 
            var view = 0; 
            var list = 0; 
            var change_status = 0;
            var default1 = 0;
            
            var mappingId  = $('#mapping_'+js_array[i]).val();
            var roleId  = $('#role_'+js_array[i]).val();
            var permissionId = $('#permission_'+js_array[i]).val();
            
            if ($('#add_'+js_array[i]).is(':checked')) {
                add = 1;
            }
            
            if ($('#edit_'+js_array[i]).is(':checked')) {
                edit = 1;
            } 
            
            if ($('#delete_'+js_array[i]).is(':checked')) {
                delete1 = 1;
            }
            
            if ($('#view_'+js_array[i]).is(':checked')) {
                view = 1;
            }
            
            if ($('#list_'+js_array[i]).is(':checked')) {
                list = 1;
            }
            
            if ($('#change_status_'+js_array[i]).is(':checked')) {
                change_status = 1;
            }
            
            if ($('#default_'+js_array[i]).is(':checked')) {
                default1 = 1;
            }
            

            var data = [mappingId, roleId, permissionId, add, edit, delete1, view, list, change_status, default1];
            finalArray.push(data);
        }
        
        if(finalArray.length > 0){
            $.ajax({
                type:'post',
                url : url,
                data:{
                   finalArray: finalArray,
                },
                success:function(status) {
                    var parsedData = JSON.parse(status);
                    if(parsedData.CODE == 200){
                        window.location.href = "<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/list"]); ?>"; 
                    } else if(parsedData.CODE == 100) {
                        alert(parsedData.MESSAGE);
                    }
                    
                }
            });
            
        }   
    }
    
    
    $('.addEditClass').on('change', function(){
        var add = $(this).attr('id');
        var id = add.substr(add.indexOf("_") + 1);
        
        if ($('#'+add).is(':checked')) {
            $('#view_'+id).prop('checked', true);
        }
    });
    
    $('.checkClass').on('click', function(){
       $('.checkClass').closest('div').next("table").find(':checkbox').prop('checked', true); 
    });
    
    $('.uncheckClass').on('click', function(){
       $('.checkClass').closest('div').next("table").find(':checkbox').prop('checked', false); 
    });
    
    $('.defaultClass').on('change', function(){
        var default1 = $(this).attr('id');
        var id = default1.substr(default1.indexOf("_") + 1);
        
        if($('#'+default1).prop('checked') == false){
            $('#add_'+id).prop('checked', false);
            $('#edit_'+id).prop('checked', false);
            $('#delete_'+id).prop('checked', false);
            $('#view_'+id).prop('checked', false);
            $('#list_'+id).prop('checked', false);
            $('#change_status_'+id).prop('checked', false);
        }

        //if ($('#'+default1).is(':checked')) {
          //  $('#view_'+id).prop('checked', false);
        //}
    });
    
    
    $('.commonClass').on('change', function(){
        var common = $(this).attr('id');
        var id = common.substr(common.lastIndexOf("_") + 1);
        
        if($('#default_'+id).prop('checked') == false){
            
            $('.parent_'+id).each(function(e) { 
                $('.parent_'+id).find(':checkbox').prop('checked', false);
                var child1 = $(this).attr('id');
                if(child1){
                    var subChild = child1.substr(child1.lastIndexOf("_") + 1);
                    if(subChild){
                        $('.parent_'+subChild).each(function() { 
                            $('.parent_'+subChild).find(':checkbox').prop('checked', false);
                        });
                    }
                }
            });
        }
        
        if ($('#'+common).is(':checked')) {
            
            //$('#add_'+id).prop('checked', true);
            //$('#edit_'+id).prop('checked', true);
            //$('#delete_'+id).prop('checked', true);
            //$('#view_'+id).prop('checked', true);
            
            $('#default_'+id).prop('checked', true);
            $('#list_'+id).prop('checked', true);
            
            
            var cls = $(this).closest('tr').attr('class');
            var parent2 = cls.substr(cls.lastIndexOf("_") + 1);
            $('#default_'+parent2).prop('checked', true);
            
            var superParent2 = $('#tr_'+parent2).attr('class');
            if(superParent2){
                var superParentId2 = superParent2.substr(superParent2.lastIndexOf("_") + 1);
                if(superParentId2){
                    $('#default_'+superParentId2).prop('checked', true);
                }
            }
        }
        
        
        
        
    });
    
    </script>
