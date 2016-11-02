<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'View Template');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Manage Templates'); ?>
            <small><?php echo \yii::t('app', 'View'); ?></small>
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
                        <h3 class="profile-username text-center"><?php echo $model->name; ?></h3><br/>

                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Event Type'); ?>:</strong><?php echo \yii::t('app', $model->event->value); ?></p><br/>
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Subject'); ?> :</strong> <?php echo \yii::t('app', $model->subject); ?></p><br/>
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Content'); ?>:</strong> <?php echo \yii::t('app', $model->content); ?></p><br/>
                        
                    </div>
                    <div class="col-lg-2">

                    </div>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left"><?php echo \yii::t('app', 'Go Back'); ?></a>         
                <?php if($permission->edit == 1){?>
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/edit", 'event'=>$model->event_id, 'lang'=>$model->language]); ?>" name="edit-permission" class="btn btn-primary btn-flat pull-right"><?php echo \yii::t('app', 'Edit'); ?></a>                            
                <?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
</div>