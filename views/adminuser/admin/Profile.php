<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'Profile');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Profile');?>
            <small><?php echo \yii::t('app', 'Admin');?></small>
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
                        <h3 class="profile-username text-center"><?= \yii::t('app', $model->first_name . ' ' . $model->last_name); ?></h3><br/>

                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Email');?>:</strong> <?= $model->email; ?></p><br/>
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Role');?>:</strong> <?= $model->user->role0->value; ?></p><br/>                        
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Department');?>:</strong> <?= $model->department->value; ?></p><br/>                        
                        <p class="text-muted text-center"><strong><?php echo \yii::t('app', 'Sub Department');?>:</strong> <?= $model->subDepartment->value; ?></p><br/>                        
                    </div>
                    <div class="col-lg-2">

                    </div>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
<?php //= Html::submitButton('Edit Profile', ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-profile-button'])  ?>
<?php if($id){
     echo Html::a('Go-Back', 'list', ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'back-profile-button']);
    ?>
    <a class="btn btn-primary btn-flat pull-right" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/admin/edit", 'id'=>$id]); ?>">Edit Profile</a>    
<?php }
else{?>
    <?php echo Html::a(\yii::t('app', 'Edit Profile'), 'editprofile', ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'edit-profile-button']); ?>
<?php }?>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
</div>