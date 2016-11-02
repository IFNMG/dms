<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \yii::t('app', 'Role-Permission Mapping');
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
            <?php echo \yii::t('app', 'Department wise document libraries');?>
            <small><?php echo \yii::t('app', 'List')?></small>
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
               <table class="table table-hover" id="listing-table">
                <thead>
                    <tr>
                        <th style="display: none;"><input name="select_all" value="1" id="permission-list-select-all" type="checkbox"></th>
                        <th><?php echo \yii::t('app', 'Role')?></th>
                        <th><?php echo \yii::t('app', 'User Type')?></th>
                        <th><?php echo \yii::t('app', 'Description')?></th>
                        <th><?php echo \yii::t('app', 'Action')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS])->all(), 'id', 'value');
                    ?>
                    <?php foreach($model as $list){ ?>
                    <tr id="tr_<?php echo $list->id;?>">
                        <td style="display: none;"><?php echo $list->id;?></td>
                        <td> <?php echo \yii::t('app', $list->value); ?></td>
                        <td><?php if($list->parent) { echo \yii::t('app', $list->parent->value );} ?></td>
                        <td><?php echo \yii::t('app', $list->description); ?></td>
                        
                        
                        <td>
                            <?php if($permission->view == 1){ ?>
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/view", 'Id'=>$list->id]); ?>">
                                <?php echo \yii::t('app', 'View')?>
                            </a>
                            <?php } ?>
                            
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            
                            <?php if($permission->view == 1){ ?>
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/mapping/edit", 'Id'=>$list->id]); ?>">
                                <?php echo \yii::t('app', 'Edit')?>
                            </a>
                            <?php } ?>
                            <!--a style="cursor: pointer;" onclick="permanentDelete(<?php echo $list->id?>, '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/deletemapping"]); ?>');">
                                Delete
                            </a-->
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
