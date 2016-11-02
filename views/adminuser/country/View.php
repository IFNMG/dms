<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'Country');
$this->params['breadcrumbs'][] = $this->title;
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Country');?>
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
        <div class="box box-default box-my">
            <div class="box-header with-border">
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div class="row">
                    
                    <div class="col-lg-12">
                        <h3 class="profile-username text-center"><?= yii::t('app',$model['value']); ?></h3><br/>                        
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'ISO Code')?>:</strong> <?= \yii::t('app',$model['iso_code']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'ISD Code')?>:</strong> <?= \yii::t('app',$model['isd_code']); ?></p><br/>                       
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Exit Code')?>:</strong> <?=  \yii::t('app',$model['exit_code']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Status')?>:</strong> <?= yii::t('app', $model['status']); ?></p><br/>
                        <?php $img=Yii::$app->params['UPLOAD_URL'].$model['flag_url']; ?>
                        <p class="text-muted text-center my-para""><strong class="light-grey"><?php echo \yii::t('app', 'Flag')?>:</strong> 
                       <span class="my-flag"><?php if($model['flag_url']!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$model['flag_url'])){?> 
                           <img src="<?= Yii::$app->urlManager->createUrl($img);?>" class="image-flag"/>   </span>
                        <?= Html::a('Download', Yii::$app->urlManager->createUrl($img), ['class' => '','target'=>'_blank']) ?></p><br/><?php } ?>
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <?php if($model['id']){?>
<?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/country/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
                <?php echo Html::a(\yii::t('app', 'Edit Country'), Yii::$app->urlManager->createUrl(["index.php/adminuser/country/update/",'id'=>$model['id']]), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-country-button']); ?>
                <?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
