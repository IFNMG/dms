<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \Yii::t('app', 'View');
$this->params['breadcrumbs'][] = $this->title;
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Manage Permissions');?>
            <small><?php echo \Yii::t('app', 'View');?></small>
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
                        <h3 class="profile-username text-center"><?php echo \Yii::t('app', $model->value); ?></h3><br/>

                        <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Permission Type'); ?>:</strong> <?php echo \Yii::t('app', $model->permissionType->value); ?></p><br/>
                        <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Description'); ?>:</strong> <?php echo $model->description; ?></p><br/>
                        
                        <?php if($model->permission_type == LookupCodes::L_PERMISSION_TYPES_MENU_LEVEL){?>
                            <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Sort Order'); ?>:</strong> <?php echo $model->sort_order; ?></p><br/>
                            <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Path'); ?>:</strong> <?php echo $model->url; ?></p><br/>
                            <?php if($model->parent_id){ ?>
                            <p class="text-muted text-center"><strong><?php echo \Yii::t('app', 'Parent'); ?>:</strong> <?php echo \Yii::t('app', $model->parent->value); ?></p><br/>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="col-lg-2">

                    </div>
                </div>
            </div><!-- /.box-body -->
            
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/permission/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/permission/edit", 'Id'=>$model->id]); ?>" name="edit-permission" class="btn btn-primary btn-flat pull-right">Edit</a>                            
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
