<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'State');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
           <?=$this->title ;?>
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
        <div class="box box-default  box-my">
            <div class="box-header with-border">
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div class="row">
                   
                    <div class="col-lg-12">
                        <h3 class="profile-username text-center"><?php echo \yii::t('app', $model['value'])?></h3><br/>
                        <p class="text-muted my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Country')?>:</strong> <?= \yii::t('app', $model->country->value); ?></p><br/>
                        <p class="text-muted my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Status')?>:</strong> <?= \yii::t('app', $model->status0->value); ?></p><br/>
                        
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <?php if($model['id']){?>
<?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/state/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
                <?php echo Html::a(\yii::t('app', 'Edit State'), Yii::$app->urlManager->createUrl(["index.php/adminuser/state/update/",'id'=>$model['id']]), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-state-button']); ?>
                <?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
