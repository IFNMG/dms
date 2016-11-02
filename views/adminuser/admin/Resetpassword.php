<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChangepasswordForm */
/* @var $form ActiveForm */
?>
<section class="content-header">
    <h1>
        Create Password
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
        <?php $form = ActiveForm::begin(); ?>

        <div class="box-body">
            <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
            <div class="row">
                <div class="col-lg-3">

                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'secretHash')->hiddenInput()->label(false); ?>
                    <?= $form->field($model, 'newPassword')->passwordInput(); ?>
                    <?= $form->field($model, 'repeatNewPassword')->passwordInput(); ?>
                    <div class="form-group pull-right">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-flat']) ?>
                    </div>
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-3">

                </div><!-- /.col-lg-6 -->
            </div>

        </div><!-- /.box-body -->
        <?php ActiveForm::end(); ?>
    </div>
</section>
