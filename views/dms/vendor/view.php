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
            <?php echo \Yii::t('app', 'Vendor Management');?>
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
                        <h3 class="profile-username text-center"><?php echo \Yii::t('app', $model->name); ?></h3><br/>
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Code'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->code); ?>
                        </p><br/>
                        
                      
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Status'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->status0->value); ?>
                        </p><br/>
                        
                        
                    </div>
                    <div class="col-lg-2">

                    </div>
                </div>
            </div><!-- /.box-body -->
            
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/vendor/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/vendor/edit", 'Id'=>$model->id]); ?>" name="edit-permission" class="btn btn-primary btn-flat pull-right">Edit</a>                            
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
