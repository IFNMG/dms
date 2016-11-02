<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \Yii::t('app', 'Manage Pages');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');

?>
<script>
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = 'language' + '=' + '<?php echo $lang; ?>' + ';expires=' + expires.toUTCString();
</script>
<?php if (Yii::$app->session->getFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->getFlash('error')): ?>
    <div class="alert alert-danger">
        <?php echo Yii::$app->session->getFlash('error'); ?>
    </div>

<?php endif; ?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Manage Pages');?>
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
                <a class="btn btn-success btn-flat pull-left col-lg-1" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/add"]); ?>" class="btn btn-primary btn-flat pull-right">
                    <?php echo \Yii::t('app', 'Add');?>
                </a>
                <?php } ?>
            </div>
            <div class="box-body">
               <table class="table table-hover" id="listing-table">
                <thead>
                    <tr>
                        
                        <th><?php echo \Yii::t('app', 'ID');?></th>
                        <th><?php echo \Yii::t('app', 'Title');?></th>
                        <th><?php echo \Yii::t('app', 'Layout');?></th>
                        <th><?php echo \Yii::t('app', 'Category');?></th>
                        <th><?php echo \Yii::t('app', 'Url');?></th>
                        <th><?php echo \Yii::t('app', 'Status');?></th>
                        
                        <?php if($permission->view == 1 || $permission->edit == 1 || $permission->delete == 1){ ?>
                        <th><?php echo \Yii::t('app', 'Action');?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>10])->all(), 'id', 'value');
                          $countStatusList=  count($statusList);
                    ?>
                    <?php foreach($model as $list){ ?>
                    <tr id="tr_<?php echo $list->id;?>">
                        <td><?php echo $list->id;?></td>
                        <td>
                            <?php if($list->image != ""){?>
                            <?php $img = Yii::$app->params['UPLOAD_URL'].$list->image; ?>
                                <img src="<?= Yii::$app->urlManager->createUrl($img); ?>" width="30" height="30" />    
                            <?php } ?>
                            <?php echo \Yii::t('app', $list->title);?>
                        </td>
                        <td>
                            <?php if($list->layout != ''){ echo $list->layout0->value; }?>
                        </td>
                        <td>
                            <?php if($list->category != '') { echo \Yii::t('app', $list->category0->value); } ?>
                        </td>
                        <td><?php echo \Yii::t('app', $list->url);?></td>
                        
                        
                        <?php if($permission->change_status == 1 && $countStatusList>2){ ?>
                        <td id="activity_<?php echo $list->id;?>">
                            <select class="form-control " onchange="activateDeactivate(<?php echo $list->id?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/activatedeactivate"]); ?>',  this.value,'dd')">
                            <?php                            
                            if($statusList) {
                                echo "<option value=''>".\Yii::t('app', '-- Select Status --')."</option>";
                                 foreach($statusList as $key=>$post){?>
                                      <option value='<?=$key?>' <?php if($key==$list->status){echo 'selected';}?>><?=\Yii::t('app', $post);?></option>
                                 <?php }
                            } else {
                                 echo "<option>-</option>";
                            }
                            ?>
                            </select>    
                        </td>
                        <?php }
                        elseif($permission->change_status == 1 && $countStatusList==2){ 
                      $changeTo=550002;
                      if($list->status==550002){$changeTo=550001;}
                      ?>
                  <td><input id="toggle-event_<?= $list->id;?>" type="checkbox" <?=($list->status=='550001')?'checked':''?> id="toggle-event" data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-style="ios" onchange="activateDeactivate(<?php echo $list->id;?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/activatedeactivate"]); ?>', <?=$changeTo?>,'')"></td>
                  <?php }else{?>
                            
                            <td id="status_<?php echo $list->id;?>">
                            <?php echo \Yii::t('app', $list->status0->value) ;?>
                            </td>
                            <?php } ?>
                        
                        <?php if($permission->view == 1 || $permission->edit == 1 || $permission->delete == 1){ ?>
                        <td>
                            <?php if($permission->view == 1){
                                if($list->category != ''){
                                    $cms_url = $list->category0->value.'/'.$list->url;
                                } else {
                                    $cms_url = $list->url;
                                }
                                
                            ?>
                            
                            <a target="_blank" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/core/pages/$cms_url"]); ?>">
                                <?php echo \Yii::t('app', 'View');?>
                            </a>
                            <!--a href="<?php //echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/view", 'Id'=>$list->id]); ?>">
                                <?php //echo \Yii::t('app', 'View');?>
                            </a-->
                            <?php } ?>
                            
                            <?php if($permission->edit == 1){ ?>
                            &nbsp;|&nbsp;
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/edit", 'Id'=>$list->id]); ?>">
                                <?php echo \Yii::t('app', 'Edit');?>
                            </a>
                            <?php } ?>
                            
                            
                            <?php if($permission->delete == 1){ ?>
                            &nbsp;|&nbsp;
                            <a style="cursor: pointer;" onclick="permanentDelete(<?php echo $list->id?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/delete"]); ?>', this);">
                                <?php echo \Yii::t('app', 'Delete');?>
                            </a>
                            <?php } ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
               </table>
            </div><!-- /.box-body -->
            
            <div class="box-footer">
               
            </div>
            
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->

<script>
    /*
    $(document).ready(function() {
        var url = 'http://cdn.datatables.net/plug-ins/1.10.7/i18n/English.json';
        var lang = '<?php echo $lang; ?>';
        if(lang == 'ar'){
            url = "http://cdn.datatables.net/plug-ins/1.10.7/i18n/Arabic.json";
        }
        
        var table = $('#permission-list').DataTable( {
            "order": [[ 1, "desc" ]],
            "language": {
                "url": url
            }
        });
    });
    */
    </script>
