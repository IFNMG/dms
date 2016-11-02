<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \Yii::t('app', 'Add New');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Vendor Management');?>
            <?php if($model->id) { ?>
            <small><?php echo \Yii::t('app', 'Update');?></small>
            <?php } else { ?>
            <small><?php echo \Yii::t('app', 'Add New');?></small>
            <?php }  ?>
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
            <?php $form = ActiveForm::begin(['id' => 'vendor-form', 
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/dms/vendor/add'])]); ?>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
               
                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        
                        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'code')->textInput(['placeholder' =>\Yii::t('app', 'Code'), 'maxlength'=>10])->label(\Yii::t('app', 'Code')) ; ?>
                            </div>
                            
                            <div class="col-lg-6">
                                <?= $form->field($model, 'name')->textInput(['maxlength'=>100,'placeholder' =>\Yii::t('app', 'Name')])->label(\Yii::t('app', 'Name')) ; ?>
                            </div>
                            
                        </div>
                        
                        <div class="box-footer">
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/vendor/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                            <a style="margin-left: 20px;" onclick="location.reload()" class="btn btn-success btn-flat pull-left">Reset</a>         
                            <?php echo Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
                        </div>

                            </div>
                            <div class="col-lg-2">

                            </div>
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                        
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                <!-- </div> -->
        
        
           