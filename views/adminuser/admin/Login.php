<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>

<div class="login-box">    
      <div class="login-logo">
        <a href="javascript:void(0);"><?= Html::encode($this->title) ?></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><?php echo \yii::t('app', 'Sign in to start your session')?></p>
        <?php
        $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'form-horizontal',],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]);
        ?> 
          
            <?= $form->field($model, 'username', [
    'template' => '<div class="col-sm-12">{input}{error}{hint}</div>'])->textInput(['placeholder'=>\yii::t('app', 'Email Address'),'autofocus'=>TRUE]) ?>
          
          
            <?= $form->field($model, 'password', [
    'template' => '<div class="col-sm-12">{input}{error}{hint}</div>'])->passwordInput(['placeholder'=>\yii::t('app', 'Password')]) ?>  
          
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                
              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <?= Html::submitButton(\yii::t('app', 'Sign In'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div><!-- /.col -->
          </div>
        <?php ActiveForm::end(); ?>

        <!--div class="social-auth-links text-center">
          <p>- OR -</p>
          <a class="btn btn-block btn-social btn-facebook btn-flat" href="#"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
          <a class="btn btn-block btn-social btn-google btn-flat" href="#"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
        </div--><!-- /.social-auth-links -->

        <?php echo Html::a(\yii::t('app', 'Forgot password').'?', 'forgotpassword'); ?><br>
        <!--a class="text-center" href="javascript:void(0);">Register a new membership</a-->

      </div><!-- /.login-box-body -->
    </div>