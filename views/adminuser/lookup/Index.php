<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \yii::t('app', 'Manage Lookup');
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
           <?php echo $this->title;?>
            <small><?php echo \yii::t('app', 'List'); ?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>
<?php
    $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS,'is_delete'=>1])->all(), 'id', 'value');
      $countStatusList=  count($statusList);
?>
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
              <?php if($permission->add == 1){ 
              echo Html::a(\yii::t('app', 'Add'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/create"]), ['class' => 'btn btn-success btn-flat pull-left col-lg-1', 'name' => 'create']);               
              }?>
            <?php if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
                echo Html::a(
                        \yii::t('app', 'Generate ShortCode'), 
                        Yii::$app->urlManager->createUrl(["index.php/generators/lookup-generator/regenerate"]),
                        ['class'=>"pull-right","target"=>"_blank"]
                        );
            }?>
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
               <table id="listing-table" class="table table-hover">
                <thead><tr>
                  <th><?php echo \yii::t('app', 'ID')?></th>
                  <th><?php echo \yii::t('app', 'Name')?></th>
                  <th><?php echo \yii::t('app', 'Parent')?></th>
                  <th><?php echo \yii::t('app', 'Lookup Type')?></th>
                  <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                  <th><?php echo \yii::t('app', 'Seed Data Type')?></th>
                  <?php } ?>
                  <th><?php echo \yii::t('app', 'Status')?></th>    
                    
                 <?php if($permission->view == 1 || $permission->edit == 1 || $permission->delete == 1){ ?> 
                  <th><?php echo \yii::t('app', 'Action')?></th>
                <?php } ?> 
                </tr>
                </thead><tbody>
                <?php foreach($data AS $k=>$v){
                    
                    ?>
                  <tr id="tr_<?= $v['id'];?>">
                  <td><?=$v['id'];?></td>
                  <td><?php echo \yii::t('app', $v['value']);?></td>                  
                  <td><?php echo \yii::t('app', $v['parent']);?></td>
                  <td><?php echo \yii::t('app', $v['lookup_type']);?></td>
                  <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                  <td><span class=""><?php echo \yii::t('app', $v['seed_data_type']);?></span></td>
                   <?php } ?>
                  
                   <?php if($permission->change_status == 1 && $countStatusList>2){ ?>
                  <td>
                    <select class="form-control" onchange="activateDeactivate(<?php echo $v['id'];?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/lookup/changestatus"]); ?>', this.value,'dd')">
                        <?php 
                        if($statusList) {
                                echo "<option value=''>".\Yii::t('app', '-- Select Status --')."</option>";
                                 foreach($statusList as $key=>$post){?>
                        <option value='<?=$key?>' <?php if($key==$v['status_id']){echo 'selected';}?>><?=\Yii::t('app', $post);?></option>
                                 <?php }
                            } else {
                                 echo "<option>-</option>";
                            }
                        ?>
                    </select>
                  </td>
                  <?php }
                  elseif($permission->change_status == 1 && $countStatusList==2){ 
                      $changeTo=  LookupCodes::L_COMMON_STATUS_DISABLED;
                      if($v['status_id']==LookupCodes::L_COMMON_STATUS_DISABLED){$changeTo=LookupCodes::L_COMMON_STATUS_ENABLED;}
                      ?>
                  <td><input id="toggle-event_<?= $v['id'];?>" type="checkbox" <?=($v['status_id']=='550001')?'checked':''?> id="toggle-event" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-style="ios" onchange="activateDeactivate(<?php echo $v['id'];?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/lookup/changestatus"]); ?>', <?=$changeTo?>,'')"></td>
                  <?php }
                  else{?>                      
                      <td id="status_<?= $v['id'];?>"><span class=""><?php echo \yii::t('app', $v['status']);?></span></td>                  
                 <?php } ?>
                 <?php if($permission->view == 1 || $permission->edit == 1 || $permission->delete == 1){ ?> 
                  <td>
                    <?php if($permission->view == 1){ ?>          
                    <?= Html::a(\yii::t('app', 'View'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/view",'id'=>$v['id']])); ?>                
                    <?php } ?> <?php if($permission->edit == 1){ ?>          &nbsp;|&nbsp;
                    <?= Html::a(\yii::t('app', 'Edit'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/update",'id'=>$v['id']])); ?>                
                    <?php } ?> <?php if($permission->delete == 1){ ?>          &nbsp;|&nbsp;
                        <a style="cursor: pointer;" onclick="permanentDelete(<?php echo $v['id']?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/lookup/delete"]); ?>', this);">  
                          <?php echo \yii::t('app', 'Delete')?>
                         </a>
                   <?php } ?>
                  </td>
                  
                 <?php } ?> 
                </tr>
                <?php } ?>
              </tbody></table>
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
