<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = 'View';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$this->title;?>
            <small>View</small>
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
                <div class="row  text-center">
                    <div class="col-lg-12">
                        <img alt="User Image" class="img-circle" src="<?=Yii::$app->request->baseUrl.'/images/user2-160x160.jpg';?>">
                    </div>
                </div>
                     
                <div class="row">
                    <div class="col-lg-12">                        
                        <h3 class="profile-username text-center"><?= ($model->first_name!="" || $model->last_name!="")?$model->first_name . ' ' . $model->last_name:'Name'; ?></h3><br/>
                        <div>
                            <div class="col-lg-6">
                                <div class="text-muted clearfix"><strong class="col-lg-3">Role</strong><div class="col-lg-9"><?= $model->role; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Email</strong><div class="col-lg-9"><?= $model->email; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Phone</strong><div class="col-lg-9"><?= $model->phone; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Gender</strong><div class="col-lg-9"><?= $model->gender; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Marital Status</strong><div class="col-lg-9"><?= $model->marital_status; ?></div></div>                             
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Image'); ?>:</strong> 
                            <?php if($model->image_path!=""){?>
                        <img src="<?=Yii::$app->urlManager->createUrl($model->image_path)?>" width="50" height="50" />    
                        <?= Html::a(\yii::t('app', 'Download'), Yii::$app->urlManager->createUrl($model->image_path), ['class' => '','target'=>'_blank']) ?></p><br/>
                            <?php } ?>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-muted clearfix"><strong class="col-lg-3">Address</strong><div class="col-lg-9"><?= $model->address; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Country</strong><div class="col-lg-9"><?= $model->country; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">State</strong><div class="col-lg-9"><?= $model->state; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">City</strong><div class="col-lg-9"><?= $model->city; ?></div></div>
                                <div class="text-muted clearfix"><strong class="col-lg-3">Status</strong><div class="col-lg-9"><?= $model->status; ?></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
 <?php if($model->id){?>
<?php echo Html::a('Go Back', Yii::$app->urlManager->createUrl(["index.php/adminuser/subscriber/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
                <?php echo Html::a('Edit Subscriber', Yii::$app->urlManager->createUrl(["index.php/adminuser/subscriber/update/",'id'=>$model->id]), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-user-button']); ?>
                <?php }?>
              
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
