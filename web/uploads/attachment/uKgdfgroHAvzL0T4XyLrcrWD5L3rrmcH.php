<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('app', 'Add New');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    
    </style>

<?php if (Yii::$app->session->getFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->getFlash('error')): ?>
    <div class="alert alert-danger">
        <?php echo Yii::$app->session->getFlash('error'); ?>
    </div>

<?php endif; ?>

<div class="container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Manage Pages');?>
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
            <?php $form = ActiveForm::begin(['id' => 'permission-form', 
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/adminuser/cms/add'])]); ?>
            <div class="box-body">     

                <div class="col-lg-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?php echo \Yii::t('app', 'GENERAL');?></a></li>
                          <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?php echo \Yii::t('app', 'CONTENT');?></a></li>
                          <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><?php echo \Yii::t('app', 'META DATA');?></a></li>
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                            <div class="row">
                                <div class="col-lg-8">
                                    <?= $form->field($model, 'title')->textInput(['placeholder' => \Yii::t('app', 'Title')])->label(\Yii::t('app', 'Title')); ?>
                                </div>
                                <div class="col-lg-2">
                                    <div style="padding-top: 20px;">
                                        <?= $form->field($model, 'showTitle')->checkbox()->label( \Yii::t('app', 'Show Title')); ?> 
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php
                                    $catList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->andWhere(['type'=>23, 'is_active'=>1])->all(), 'id', 'value');
                                        foreach ($catList as $key => $value) {
                                            $catList[$key] = Yii::t('app', $value);
                                        }   
                                    ?>
                                    <?= $form->field($model, 'category')->dropDownList($catList, ['prompt' =>\Yii::t('app', '--Select--')])->label(\Yii::t('app', 'Category'));?>
                                </div>
                            </div>
                        
                        
                            <div class="row">
                                <div class="col-lg-12">
                                    <?= $form->field($model, 'url')->textInput(['placeholder' => \Yii::t('app', 'Url')])->label(\Yii::t('app', 'Url')); ?>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-lg-6">
                                    <?= $form->field($model, 'image')->fileInput(['placeholder' => \Yii::t('app', 'Image'), 'accept' => 'image/*'])->label(\Yii::t('app', 'Image')); ?>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-lg-12">
                                    <?= $form->field($model, 'short_description')->textarea(['placeholder' => \Yii::t('app', 'Short Description')])->label(\Yii::t('app', 'Short Description')); ?>
                                </div>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                          <div class="tab-pane" id="tab_2">
                            <div class="row" style="padding: 15px;">
                                <?= $form->field($model, 'content')->textarea(['placeholder'=>\yii::t('app', 'Content')])->label(false); ?>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                          <div class="tab-pane" id="tab_3">
                            <div class="row" style="padding: 15px;">
                                <?= $form->field($model, 'meta_description')->textarea(['placeholder'=>\yii::t('app', 'Meta Description')])->label(\yii::t('app', 'Meta Description')); ?>
                            </div>
                        
                            <div class="row" style="padding: 15px;">
                                <?= $form->field($model, 'keywords')->textarea(['placeholder'=>\yii::t('app', 'Keywords')])->label(\yii::t('app', 'Keywords')); ?>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                    <div class="box-footer">
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left"><?php echo \Yii::t('app', 'Go Back'); ?></a>         
                        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
                    </div>
                </div>

                </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                        
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                </div>
        
        <script>
		CKEDITOR.replace( 'Pages[content]', {
			extraPlugins: 'placeholder',
                        height: 220
		} );
                
                CKEDITOR.config.startupMode = 'source';
	</script>