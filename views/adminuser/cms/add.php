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
  @media (min-width: 768px) {
  .sidebar-nav .navbar .navbar-collapse {
    padding: 0;
    max-height: none;
  }
  .sidebar-nav .navbar ul {
    float: none;
  }
  .sidebar-nav .navbar ul {
    display: block;
  }
  .sidebar-nav .navbar li {
    float: none;
    display: block;
  }
  .sidebar-nav .navbar li a {
    padding-top: 12px;
    padding-bottom: 12px;
  }
}
.nav-tabs-vertical .nav-tabs>li {
    float: none;
}
.nav-tabs-vertical .nav-tabs>li.active>a, 
.nav-tabs-vertical .nav-tabs>li.active>a:focus, 
.nav-tabs-vertical .nav-tabs>li.active>a:hover {
    border-bottom-color: #ddd;
    border-right-color: transparent;
}
.nav-tabs-vertical .nav-tabs {
    border-bottom: 0 none;
}
.nav-tabs-vertical .nav-tabs>li>a {
    border-radius: 4px 0 0 4px;
}
.nav-tabs-custom.nav-tabs-vertical .nav-tabs>li.active {
    border-left: 3px solid #3c8dbc;
}
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
            <?php $form = ActiveForm::begin(['id' => 'cms-form', 
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/adminuser/cms/add'])]); ?>
            <div class="box-body">     
                
                <div class="col-lg-12">
                    <div class="row">
                    <div class="nav-tabs-custom nav-tabs-vertical">
                        <div class="col-lg-3">
                        <ul class="nav nav-tabs">
                          <li id="li_1" class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?php echo \Yii::t('app', 'GENERAL');?></a></li>
                          <li id="li_2" class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?php echo \Yii::t('app', 'CONTENT');?></a></li>
                          <li id="li_3" class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><?php echo \Yii::t('app', 'META DATA');?></a></li>
                        </ul>
                        </div>    
                        <div class="col-lg-9">
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                            <div class="row">
                                
                                <div class="col-lg-8">
                                    <?= $form->field($model, 'title')->textInput(['placeholder' => \Yii::t('app', 'Title')])->label(\Yii::t('app', 'Title')); ?>
                                </div>
                                <div class="col-lg-3">
                                    <div style="padding-top: 20px;">
                                        <?= $form->field($model, 'showTitle')->checkbox()->label( \Yii::t('app', 'Hide title on frontend')); ?> 
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                 <div class="col-lg-6">
                                    <?php
                                    $layoutTypeList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()
                                        ->orderBy(['value' => SORT_ASC])
                                        ->andWhere(['type'=>25, 'is_delete'=>1])
                                        ->all(), 'id', 'value');
                                    ?>
                                    <?php echo $form->field($model, 'layout')->dropDownList($layoutTypeList, ['prompt' =>\Yii::t('app', '--Select--')])->label(\Yii::t('app', 'Select Layout'));?>
                                </div>
                                <div class="col-lg-6">
                                    <?php
                                    $obj = new \app\facades\common\CommonFacade();
                                    $list = $obj->getLookupDropDown(23);
                                    ?>
                                    <?php echo $form->field($model, 'category')->dropDownList($list); ?>
                                </div>
                            </div>
                        
                        
                            <div class="row">
                                <div class="col-lg-12">
                                    <?= $form->field($model, 'url')->textInput(['placeholder' => \Yii::t('app', 'Url')])->label(\Yii::t('app', 'Url')); ?>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-lg-3">
                                    <?= $form->field($model, 'image')->fileInput(['placeholder' => \Yii::t('app', 'Image'), 'accept' => 'image/*'])->label(\Yii::t('app', 'Image')); ?>
                                </div>
                                <div class="col-lg-3">
                                    <img width="60" height="60" src="" id="previewimg" style="display: none;">
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-lg-12">
                                    <?= $form->field($model, 'short_description')
                                    ->textarea(['placeholder' => \Yii::t('app', 'Short Description'), 'maxlength'=>"1000"])->label(\Yii::t('app', 'Short Description')); ?>
                                     <span class="help-block" style="padding-top: 0px; margin-top: -15px;">Maximum length is 1000 characters.</span>
                                </div>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                          <div class="tab-pane" id="tab_2">
                            <div class="row" style="">
                                <?= $form->field($model, 'content')->textarea(['placeholder'=>\yii::t('app', 'Content'), 'id'=>'content'])->label('Content'); ?>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                          <div class="tab-pane" id="tab_3">
                             <div class="row" style="">
                                <?= $form->field($model, 'meta_title')->textInput(['placeholder'=>\yii::t('app', 'Meta Title')])->label(\yii::t('app', 'Meta Title')); ?>
                            </div>  
                            <div class="row" style="">
                                <?= $form->field($model, 'meta_description')
                            ->textarea(['placeholder'=>\yii::t('app', 'Meta Description'), 'maxlength'=>"1000"])->label(\yii::t('app', 'Meta Description')); ?>
                             <span class="help-block" style="padding-top: 0px; margin-top: -15px;">Maximum length is 1000 characters.</span>
                            </div>
                        
                            <div class="row" style="">
                                <?= $form->field($model, 'keywords')->textarea(['placeholder'=>\yii::t('app', 'Keywords')])->label(\yii::t('app', 'Keywords')); ?>
                            </div>
                          </div>
                          <!-- /.tab-pane -->
                        </div>
                        </div>    
                        <!-- /.tab-content -->
                    </div>
                    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                    <div class="box-footer">
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/cms/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left"><?php echo \Yii::t('app', 'Go Back'); ?></a>         
                        <a onclick="submitForm();" class="btn btn-success btn-flat pull-right"><?php echo \Yii::t('app', 'Save'); ?></a>         
                        
                        <a onclick="preview();" class="btn btn-success btn-flat pull-right" style="margin-right: 20px;"><?php echo \yii::t('app', 'Preview')?></a>
                        
                        <?php if($model->id != ''){ ?>
                        
                        <?php 
                            if($model->category != ''){
                                $url = $model->category0->value.'/'.$model->url;
                            } else {
                                $url = $model->url;
                            }
                        ?>
                        <textarea id="relative_url" style="width: 520px; margin-left:204px; height: 30px; resize: none;">index/adminuser/core/pages/<?php echo $url; ?></textarea>
                        
                        <a onclick="copyToClipboard();" class="btn btn-success btn-flat pull-right" style="margin-right: 20px;">
                            <?php echo \yii::t('app', 'Copy Url')?>
                        </a>
                        <?php } ?>

                    </div>
                </div>
                
                </div>
                </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                        
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                </div>
        
        <script>
    

    CKEDITOR.replace( 'content', {
            extraPlugins: 'placeholder',
            height: 220
    } );

    CKEDITOR.config.startupMode = 'source';

    function submitForm(){
        document.getElementById('cms-form').submit();
    }

    $( "#pages-title" ).keypress(function() {
        copyText();
    });

    function copyText() {
        src = document.getElementById("pages-title");
        dest = document.getElementById("pages-url");
        dest.value = src.value.toLowerCase().replace(/ /g,"-");
    }

    /*
    var clipboard = new Clipboard('#button1');
    clipboard.on('success', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
        e.clearSelection();
    });
    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });
    */
    
    function copyToClipboard(){
        var copyTextarea = document.querySelector('#relative_url');
        copyTextarea.select();
        try {
          var successful = document.execCommand('copy');
          var msg = successful ? 'successful' : 'unsuccessful';
          console.log('Copying text command was ' + msg);
        } catch (err) {
          console.log('Oops, unable to copy');
        }
    }

    function preview(){
        var layout = 1300001;
        var layout1 = $('#pages-layout').val();
        if(layout1 != ''){
            layout = layout1;
        }
        if($('#pages-showtitle').attr('checked')) {
            var hideTitle = 1;
        } else {
            var hideTitle = 0;
        }
        var url = '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/core/preview"]); ?>';
        var content = CKEDITOR.instances['content'].getData();
        var title = $('#pages-title').val();
        if(content == ''){
            alert('Please put some content to preview.');
        } else {
            $.ajax({
                type: "POST",
                data: {
                    title: title,
                    layout: layout,
                    content: content,
                    hideTitle: hideTitle
                },
                url: url,
                success: function(data) {
                    window.open('test', '').document.write(data);
                }
            });
        }
    }

    var abc = 0;   
    $('body').on('change', '#pages-image', function(){
        if (this.files && this.files[0]) {
            var avatar = $(this).val();
            var extension = avatar.split('.').pop().toUpperCase();
            if (extension === "PNG" || extension === "JPG" || extension === "JPEG"){
                if(this.files[0].size <= 2000000){
                    var reader = new FileReader();
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                } 
            }
        }
    });
    
    function imageIsLoaded(e) {
        $('#previewimg').attr('src', e.target.result);
        $('#previewimg').show();
    };
    
    
    
                
    <?php
        if($model->image != '' && $model->id != ''){
        $img = Yii::$app->params['UPLOAD_URL'].$model->image;
    ?>
        
            $('#previewimg').attr('src', '<?= Yii::$app->urlManager->createUrl($img); ?>');
            $('#previewimg').show();
        
    <?php } ?>  
    </script>

        <?php
        if($model->getErrors()){
            if(sizeof($model->getErrors()) == 1){
                if(($model->firstErrors['content']) == 'Content cannot be blank.'){
                    ?>
                        
        <script>
            $('#li_2').addClass('active');
            $('#tab_2').addClass('active');
            
            $('#li_1').removeClass('active');
            $('#tab_1').removeClass('active');
            
            $('#li_3').removeClass('active');
            $('#tab_3').removeClass('active');
            </script>
        <?php } } } ?>


        
