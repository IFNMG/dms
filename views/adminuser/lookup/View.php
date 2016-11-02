<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title = \yii::t('app', 'Lookup');
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
            <div class="box-header with-border ">
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div class="row">
                   
                    <div class="col-lg-12">
                        <h3 class="profile-username text-center"><?php echo \yii::t('app', $model['value']); ?></h3><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Lookup Type'); ?>:</strong><?php echo \yii::t('app',  $model['lookup_type']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Parent'); ?>:</strong> <?php echo \yii::t('app', $model['parent']); ?></p><br/>
                         <?php  if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){?>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Seed Data Type'); ?>:</strong> <?php echo \yii::t('app', $model['seed_data_type']); ?></p><br/>
                         <?php } ?>
                        <p style="display: block;overflow: auto;" class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Description'); ?>:</strong><?php echo \yii::t('app', $model['description']); ?></p><br/>
                        <p style="display: block;overflow: auto;" class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Info'); ?>1:</strong> <?php echo \yii::t('app', $model['info1']); ?></p><br/>
                        <p style="display: block;overflow: auto;" class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Info'); ?>2:</strong><?php echo \yii::t('app', $model['info2']); ?></p><br/>
                        <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Status'); ?>:</strong><?php echo \yii::t('app', $model['status']); ?></p><br/>
                         <p class="text-muted text-center my-para"><strong class="light-grey"><?php echo \yii::t('app', 'Short Code'); ?>:</strong><?php echo \yii::t('app', $model['short_code']); ?></p><br/>
                        <?php $img=Yii::$app->params['UPLOAD_URL'].$model['image_path']; ?>
                        <p class="text-muted text-center my-para-image"><strong class="light-grey"><?php echo \yii::t('app', 'Image'); ?>:</strong> 
                           <span class="my-flag"> <?php if($model['image_path']!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$model['image_path'])){?>
                               <img src="<?=Yii::$app->urlManager->createUrl($img)?>" class="image-flag"/>    </span>
                        <?= Html::a(\yii::t('app', 'Download'), Yii::$app->urlManager->createUrl($img), ['class' => '','target'=>'_blank']) ?></p><br/>
                            <?php } ?>
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <?php if($model['id']){?>
<?php echo Html::a(\yii::t('app', 'Go Back'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/"]), ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'go-back']); ?>
                <?php echo Html::a(\yii::t('app', 'Edit'), Yii::$app->urlManager->createUrl(["index.php/adminuser/lookup/update/",'id'=>$model['id']]), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-lookup-button']); ?>
                <?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
</div>