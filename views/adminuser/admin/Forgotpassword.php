<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChangepasswordForm */
/* @var $form ActiveForm */
?>
<section class="content-header">
    <h1>
        <?php echo \yii::t('app', 'Forgot Password')?>
        <!--small>Preview</small-->
    </h1>
    <!--ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Forms</a></li>
      <li class="active">General Elements</li>
    </ol-->
</section>
<section class="content">


    <div class="box box-success">
        <div class="box-header with-border">
            <!--h3 class="box-title">Change Password</h3-->
        </div>
        <?php
        $form = ActiveForm::begin([
                    'id' => 'forgotpassword-form',
                    'options' => ['class' => 'form-horizontal',],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-3'],
                    ],
        ]);
        ?>
        <div class="box-body">
            <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
            <div id="row">
                <div class="col-lg-3">

                </div>
                <div class="col-lg-6">
                <?=
                $form->field($model, 'email', [
                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder'=>\yii::t('app', 'Email Address')])->label(\yii::t('app', 'Email Address'))
                ?>

<?= Html::submitButton(\yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-flat pull-right']) ?>
<?= Html::a(\yii::t('app', 'Go to Login'), 'login', ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'back-profile-button']); ?>
                </div>
                <div class="col-lg-3">

                </div>
            </div>
        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
    </div>
</section>
