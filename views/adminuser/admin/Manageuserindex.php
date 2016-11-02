<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupTypeCodes;
use app\web\util\Codes\LookupCodes;


$this->title = \yii::t('app','Manage Users');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');
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
            <?php echo \yii::t('app','Manage Users');?>
            <small><?php echo \yii::t('app','List');?></small>
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
                <?php if($permission->add == 1){ 
                echo Html::a(\yii::t('app', 'Add'), Yii::$app->urlManager->createUrl(["index.php/adminuser/admin/add"]), ['class' => 'btn btn-success btn-flat pull-left col-lg-1', 'name' => 'create']); }?>
                
            </div>
            <div class="box-body">
                
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                
               <table id="listing-table" class="table table-hover">
                <thead><tr>
                  
                  <th><?php echo \yii::t('app','Name');?></th>
                  <th><?php echo \yii::t('app','Email');?></th>
                  <th><?php echo \yii::t('app','Department');?></th>
                  <th><?php echo \yii::t('app','Sub Department');?></th>
                  <th><?php echo \yii::t('app','Role');?></th>
                  <th><?php echo \yii::t('app','Registered Since');?></th>
                  <th><?php echo \yii::t('app','Status');?></th>
                 
                  <th><?php echo \yii::t('app','Action');?></th>
                </tr>
                </thead><tbody>
                    
                <?php
                    $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS])->all(), 'id', 'value');
                    $countStatusList=  count($statusList);
                ?>
                    
                    
                <?php foreach($data AS $k=>$v){?>
                <tr id="tr_<?php echo $v['id'];?>">
                  
                  <td><?=$v->adminPersonals['first_name'].' '.$v->adminPersonals['last_name'];?></td>
                  <td><?=$v->adminPersonals['email'];?></td>
                  <td><?=$v->adminPersonals->department->value;?></td>
                  <td><?=$v->adminPersonals->subDepartment->value;?></td>
                  <td><span class=""><?=$v->role0['value'];?></span></td>
                  <td><span class=""><?=date($date_format,  strtotime($v['created_on']));?></span></td>
                
                        
                 <?php if($permission->change_status == 1 && $countStatusList>2){ ?>
                    <td id="activity_<?php echo $v['id'];?>">
                        <select class="form-control" onchange="activateDeactivate(<?php echo $v['id']; ?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/activatedeactivate"]); ?>', this)">
                        <?php 
                        if($statusList) {
                            echo "<option value=''>".\Yii::t('app', '-- Select Status --')."</option>";
                             foreach($statusList as $key=>$post){ ?>
                            <option value='<?=$key?>' <?php if($key==$v['status']){echo 'selected';}?>><?=\Yii::t('app', $post)?></option>
                             <?php }
                        } else {
                             echo "<option>-</option>";
                        }
                        ?>
                        </select>    
                    </td>
                    <?php }
                    elseif($permission->change_status == 1 && $countStatusList==2){ 
                      $changeTo= LookupCodes::L_COMMON_STATUS_DISABLED;
                      if($v['status']==LookupCodes::L_COMMON_STATUS_DISABLED){$changeTo=LookupCodes::L_COMMON_STATUS_ENABLED;}
                      ?>
                    <td><input id="toggle-event_<?= $v['id'];?>" type="checkbox" <?=($v['status']==LookupCodes::L_COMMON_STATUS_ENABLED)?'checked':''?> id="toggle-event" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-style="ios" onchange="activateDeactivate(<?php echo $v['id'];?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/subscriber/changestatus"]); ?>', <?=$changeTo?>,'')"></td>
                  <?php }else{?>
                          <td id="status_<?php echo $v['id'];?>">
                        <?php if($v->status){ echo  \yii::t('app', $v->status0->value);} ?>
                    </td>
                        <?php } ?>
                  
                  <td>
                    
                      
                    <?php if($permission->view == 1){ ?>
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/view", 'id'=>$v['id']]); ?>"><?php echo \yii::t('app','View');?></a>
                        <!--a href="<?php //echo Yii::$app->urlManager->createUrl("index.php/adminuser/admin/view").'/'.$v['id'];?>">view</a-->
                    <?php } ?>

                    <?php if($permission->edit == 1){ ?>
                    &nbsp;|&nbsp;
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/edit", 'id'=>$v['id']]); ?>"><?php echo \yii::t('app','Edit');?></a>
                      <!--a href="<?php //echo Yii::$app->urlManager->createUrl("index.php/adminuser/admin/edit").'/'.$v['id'];?>">edit</a-->
                    <?php } ?>

                    <?php if($permission->delete == 1){ ?>
                    &nbsp;|&nbsp;
                    <a style="cursor: pointer;" onclick="permanentDelete(<?php echo $v['id']; ?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/delete"]); ?>', this);">
                        <?php echo \yii::t('app','Delete');?>
                    </a>
                    <?php } ?>
                      

                      
                  </td>
                </tr>
                <?php } ?>
              </tbody></table>
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
