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

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Role-Permission Mapping');?>
            <small><?php echo \yii::t('app', 'View');?></small>
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
                        <?php if($permission->edit == 1){ ?>
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/edit", 'Id'=>$role->id]); ?>" style="float: right;" class="btn btn-primary"><?php echo \yii::t('app', 'Edit');?></a>
                        <?php } ?>
                        
                    </div>
                </div>
                
                
                
                <?php foreach($model as $type){ ?>
                    <h2><?php echo \Yii::t('app', $type['value']) ;?></h2>
                    <?php if($type['id'] == LookupCodes::L_PERMISSION_TYPES_MENU_LEVEL){ ?>
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
                            ?>
                            
                            <tr style="<?php echo $bgcolor; ?><?php echo $border;?>" id="tr_<?php echo $permissionId;?>">
                                <td><?php echo \Yii::t('app', $list['value']) ;?></td>
                                <td style="">
                                    <?php if($list['obj']['default'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <input type="hidden" value="<?php echo $list['obj']['mapping_id']; ?>" id="mapping_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $role->id; ?>" id="role_<?php echo $permissionId; ?>">
                                    <input type="hidden" value="<?php echo $permissionId; ?>" id="permission_<?php echo $permissionId; ?>">
                                    <?php if($list['obj']['add'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <?php if($list['obj']['edit'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <?php if($list['obj']['delete'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <?php if($list['obj']['view'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <?php if($list['obj']['list'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                                <td style="">
                                    <?php if($list['obj']['change_status'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px; background: #2EFE64;"></button>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    <?php }  else { ?>
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
                                <td style=""><?php echo \Yii::t('app', $list['value']) ;?></td>
                                <td style="">
                                    <?php if($list['obj']['view'] == 1){ ?> 
                                        <button type="button" class="btn btn-success" style="padding: 6px; border-radius: 10px;"></button>
                                    <?php } ?>
                                </td>
                            </tr>
                                <?php } ?>
                        </tbody>
                        
                        </table>
                    <?php } ?>
                <?php } ?>
                
               
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->


