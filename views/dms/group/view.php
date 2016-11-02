<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \Yii::t('app', 'Group Management');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/listing.js');
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
            <?php echo \Yii::t('app', 'Group Management');?>
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
                <?php //if($permission->add == 1){ ?>
                <!--a class="btn btn-success btn-flat pull-left col-lg-1" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/group/add", 'Id'=>$list->id]); ?>" class="btn btn-primary btn-flat pull-right">
                    <?php //echo \Yii::t('app', 'Add');?>
                </a-->
                <?php //} ?>
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
               <table class="table table-hover" id="listing-table">
                <thead>
                    <tr>
                        <th><?php echo \Yii::t('app', 'ID');?></th>
                        <th><?php echo \Yii::t('app', 'Email');?></th>                       
                        <th><?php echo \Yii::t('app', 'Status');?></th>                       
                        <?php if($permission->view == 1 || $permission->edit == 1 || $permission->delete == 1){ ?>
                        <th><?php echo \Yii::t('app', 'Action');?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach($model as $list){?>
                    <tr id="tr_<?php echo $list->id;?>">
                        <td><?php echo $list->id;?></td>
                        <td><?php echo \Yii::t('app', $list->user->adminPersonals->email);?></td>
                        <td><?php echo \Yii::t('app', $list->status0->value);?></td>
                        
                        
                        <td>
                            <?php if($permission->delete == 1){ ?>
                            
                            <a style="cursor: pointer;" onclick="permanentDelete(<?php echo $list->id?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/group/delete"]); ?>', this);">
                                <?php echo \Yii::t('app', 'Delete');?>
                            </a>
                            <?php } ?>
                        </td>
                        
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
