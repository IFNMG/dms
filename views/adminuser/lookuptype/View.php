<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title = \yii::t('app', 'Lookup Type');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$this->title ;?>
            <small><?php echo \yii::t('app', 'View')?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-default box-my ">
             
<!--            <div class="box-header with-border">
            </div>-->
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div class="row">
                    
                    <div class="col-lg-12">
                        <h3 class="profile-username text-center profile-username-my"><?php echo \yii::t('app', $model['value']); ?></h3>
                     <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Parent'); ?>:</strong><?php echo \yii::t('app', $model['parent']); ?></p><br/>
                       <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Seed Data Type'); ?>:</strong><?php echo \yii::t('app', $model['seed_data_type']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Sync To Mobile'); ?>:</strong><?php echo \yii::t('app', $model['sync_to_mobile']); ?></p><br/>
                       <?php } ?>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Status'); ?>:</strong><?php echo \yii::t('app', $model['status']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Short Code'); ?>:</strong><?php echo \yii::t('app', $model['short_code']); ?></p><br/>
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <?php if($model['id']){?>
<?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookuptype/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
                <?php echo Html::a(\yii::t('app', 'Edit'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookuptype/update/",'id'=>$model['id']]), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-lookuptype-button']); ?>
                <?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
</div>