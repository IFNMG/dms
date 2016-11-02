<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = 'Manage Role-Permission Mapping';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Role-Permission Mapping
            <small>List</small>
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

                    </div>
                    <div class="col-lg-8">
                        <h3 class="profile-username text-center"><?php echo $role->value; ?></h3><br/>
                        <p class="text-muted text-center"><strong>User Type:</strong> <?php echo $role->parent->value; ?></p><br/>
                        <p class="text-muted text-center"><strong>Description:</strong> <?php echo $role->description; ?></p><br/>
                    </div>
                    <div class="col-lg-2">
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/viewmapping", 'Id'=>$role->id]); ?>" style="float: right;" class="btn btn-primary">View</a>
                    </div>
                </div>
                
                
                
                <?php foreach($model as $type){ ?>
                    <h2><?php echo $type['value']?></h2>
                    <?php if($type['id'] == 600001){ ?>
                        <table class="table table-hover" id="permission-list1">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>Common</th>
                                <th>Add</th>
                                <th>Edit</th>
                                <th>Delete</th>
                                <th>View</th>
                                <th>View List</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $tempArr = array();
                                foreach($type['PermissionList'] as $list){ 
                                $permissionId = $list['id'];
                                array_push($tempArr, $permissionId);
                                
                                ?>
                            
                            
                            <tr id="tr_<?php echo $permissionId;?>">
                                <td><?php echo $list['value'] ;?></td>
                                <td style="">
                                    <input class="commonClass" <?php if($list['obj']['common'] == 1){ echo 'checked'; }; ?> type="checkbox" id="common_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input type="hidden" value="<?php echo $list['obj']['mapping_id']; ?>" id="mapping_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $role->id; ?>" id="role_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $permissionId; ?>" id="permission_<?php echo $permissionId; ?>">
                                    <input class="addEditClass" <?php if($list['obj']['add'] == 1){ echo 'checked'; }; ?> type="checkbox" id="add_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="addEditClass" <?php if($list['obj']['edit'] == 1){ echo 'checked'; }; ?> type="checkbox" id="edit_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input <?php if($list['obj']['delete'] == 1){ echo 'checked'; }; ?> type="checkbox" id="delete_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input class="viewClass" <?php if($list['obj']['view'] == 1){ echo 'checked'; }; ?> type="checkbox" id="view_<?php echo $permissionId; ?>" value=""/>
                                </td>
                                <td style="">
                                    <input <?php if($list['obj']['viewlist'] == 1){ echo 'checked'; }; ?> type="checkbox" id="viewlist_<?php echo $permissionId; ?>" value=""/>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    <?php }  else { ?>
                        <table class="table table-hover" id="permission-list2" style="width: 38.5%;">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>Status</th>
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
                                <td style=""><?php echo $list['value'] ;?></td>
                                <td style="">
                                    <input <?php if($list['obj']['common'] == 1){ echo 'checked'; }; ?> type="checkbox" id="common_<?php echo $permissionId; ?>" value=""/>
                                </td>
                            </tr>
                                <?php } ?>
                        </tbody>
                        
                        </table>
                    <?php } ?>
                <?php } ?>
                
               <a style="margin-top: 30px; float: right;" onclick="submitForm();" class="btn btn-primary">SAVE CHANGES</a>
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->



<script>
    function submitForm(){
        var url = '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/createmapping"]); ?>';
        var finalArray = [];
        var js_array = [<?php echo '"'.implode('","', $tempArr).'"' ?>];
        
        
        for(var i = 0; i < js_array.length; i++){
            var add = 0;
            var edit = 0; 
            var delete1 = 0; 
            var view = 0; 
            var viewlist = 0; 
            var common = 0;
            
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
            
            if ($('#viewlist_'+js_array[i]).is(':checked')) {
                viewlist = 1;
            }
            
            if ($('#common_'+js_array[i]).is(':checked')) {
                common = 1;
            }
            

            var data = [mappingId, roleId, permissionId, add, edit, delete1, view, viewlist, common];
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
                        location.reload(); 
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
    
    
    $('.commonClass').on('change', function(){
        var common = $(this).attr('id');
        var id = common.substr(common.indexOf("_") + 1);
        
        if ($('#'+common).is(':checked')) {
            $('#add_'+id).prop('checked', true);
            $('#edit_'+id).prop('checked', true);
            $('#delete_'+id).prop('checked', true);
            $('#view_'+id).prop('checked', true);
            $('#viewlist_'+id).prop('checked', true);
        }
    });
    
    </script>
