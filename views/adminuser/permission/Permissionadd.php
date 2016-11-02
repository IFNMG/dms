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
            <?php echo \Yii::t('app', 'Manage Permissions');?>
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
                    'action'=>Yii::$app->getUrlManager()->createUrl(['index.php/adminuser/permission/add'])]); ?>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <?php if($model->permission_type == LookupCodes::L_PERMISSION_TYPES_MENU_LEVEL){
                    $display = 'block';
                } else {
                    $display = 'none';
                }?>


                <div class="row">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'value')->textInput(['placeholder' => \Yii::t('app', 'Name')])->label(\Yii::t('app', 'Permission Name')); ?>
                            </div>
                            <div class="col-lg-6">
                                <?php
                                $permissionTypeArray = array();
                                $id = Yii::$app->user->getId();
                                if($id){
                                    $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1, 'status'=>550001])->one();
                                    if($user){
                                        $permissionTypes = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>11, 'is_delete'=>1])->all(), 'id', 'value');
                                        foreach ($permissionTypes as $key => $value) {
                                            if($user->user_type != 150003){
                                                if($key == 600001){
                                                    $permissionTypeArray[$key] = Yii::t('app', $value);
                                                }
                                            } else {
                                                $permissionTypeArray[$key] = Yii::t('app', $value);
                                            }
                                        }
                                    } 
                                } else {
                                    $permissionTypes = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->orderBy(['value' => SORT_ASC])->andWhere(['type'=>11, 'is_delete'=>1])->all(), 'id', 'value');
                                    foreach ($permissionTypes as $key => $value) {
                                        $permissionTypes[$key] = Yii::t('app', $value);
                                    }
                                }
                                
                                ?>
                                <?= $form->field($model, 'permission_type')->dropDownList($permissionTypeArray, ['prompt' =>\Yii::t('app', 'Select Type')])->label(\Yii::t('app', 'Permission Type'));?>
                                
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'description')->textInput(['placeholder' =>\Yii::t('app', 'Description')])->label(\Yii::t('app', 'Description')) ; ?>
                            </div>
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?php
                                    $myList = new \app\facades\common\CommonFacade();
                                    $parentChildList = $myList->getPermissionParent();
                                    
                                ?>
                                <?php echo $form->field($model, 'parent_id')
                                        ->dropDownList($parentChildList); ?>
                                
                            </div>
                        </div>
                        
                        
                         <div class="row">
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?= $form->field($model, 'image')->fileInput(['placeholder' => \Yii::t('app', 'Icon'), 'accept' => 'image/*'])->label(\Yii::t('app', 'Icon')); ?>
                            
                                <div class="col-lg-2">
                                    <img width="30" height="30" src="" id="previewimg" style="display: none;">
                                </div>
                            </div>
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?php
                                    $obj = new \app\facades\common\CommonFacade(); 
                                    $displayOptions = $obj->getLookupDropDown(LookupTypeCodes::LT_PERMISSION_DISPLAY_OPTIONS);
                                ?>
                                <?php echo $form->field($model, 'display_option')
                                        ->dropDownList($displayOptions)->label(\yii::t('app', 'Display Options')); ?>
                            </div>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?= $form->field($model, 'url')
                                    ->label(\Yii::t('app', 'Relative Url'))
                                    ->textInput(['placeholder' =>\Yii::t('app', 'Relative Url')]); ?>
                                <div class="callout callout-danger" style="padding : 5px;">
                                    <h5 style="margin: 0px;">
                                        <?php echo \Yii::t('app', 'If relative url field left blank. This menu item will not be clickable.!'); ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?= $form->field($model, 'sort_order')
                                    ->textInput(['placeholder' =>\Yii::t('app', 'Sort Order')])
                                    ->label(\Yii::t('app', 'Sort Order')); ?>
                            </div>
                        </div>
                        
                       
                        
                        <div class="row">
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?= $form->field($model, 'is_new_window')->checkbox()->label( \Yii::t('app', 'Open in new window')); ?> 
                            </div>
                            
                            <?php if(Yii::$app->user->identity->user_type == LookupCodes::L_USER_TYPE_DEVELOPERS) { ?>
                            <div class="col-lg-6 menuClass" style="display: <?php echo $display?>">
                                <?= $form->field($model, 'developer_admin_only')->checkbox()->label( \Yii::t('app', 'For Developer Admin Only')); ?> 
                            </div>
                            <?php } ?>
                        </div>
                        
                        
                                <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                        <div class="box-footer">
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/permission/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                            <a onclick="submitForm();" class="btn btn-success btn-flat pull-right"><?php echo \Yii::t('app', 'Save'); ?></a>         
                            <?php //echo Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'create-user-button']) ?>
                        </div>

                            </div>
                            <div class="col-lg-2">

                            </div>
                        </div><!-- /.box-body -->
<?php ActiveForm::end(); ?>
                        
                    </div><!-- /.box -->
                    </section><!-- /.content -->
                <!-- </div> -->
        
        
        
<script>
    $('#permissions-permission_type').change(function() {
        var id = $('#permissions-permission_type').val();
        if(id != ''){
            if(id == 600001){
                $('.menuClass').show();
            } else {
                $('.menuClass').hide();
            }
        } else {
            $('.menuClass').hide();
        }

    });
            
            
            
            
    function submitForm(){
        var flag = 1;
        var displayOption = $('#permissions-display_option').val();
        var image = $('#permissions-image').val();
        var preview = $('#previewimg').attr('src');

        if(displayOption == 1450002 || displayOption == 1450003 || displayOption == 1450004){
            if(image == '' && preview == ''){
                flag = 0;
                $('#permissions-image').next('.help-block-error').text('Please upload an icon.');
                $('#permissions-image').parent('.form-group').addClass('has-error');
            } else {
                $('#permissions-image').next('.help-block-error').text('');
                $('#permissions-image').parent('.form-group').removeClass('has-error');
            }
        }
        if(flag == 1){
           document.getElementById('permission-form').submit();
       }
   }
           
           
    $('body').on('change', '#permissions-image', function(){
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
    </script>

    <?php
        if($model->image != '' && $model->id != ''){
        $img = Yii::$app->params['UPLOAD_URL'].$model->image;
    ?>
        <script>
            $('#previewimg').attr('src', '<?= Yii::$app->urlManager->createUrl($img); ?>');
            $('#previewimg').show();
        </script>
    <?php } ?>                