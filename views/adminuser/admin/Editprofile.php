<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title =  \yii::t('app', 'Edit Profile');
$this->params['breadcrumbs'][] = $this->title;
?>


<?php 
    $roleList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['parent_id'=>150001,'type'=>1])->all(), 'id', 'value');
    if($roleList){
        foreach($roleList as $key=>$value){
            $roleList[$key]= \Yii::t('app', $value);
        }
    }
    
    $roleList = \app\facades\common\CommonFacade::getOrderwiseUserTypes(['id'=>Yii::$app->admin->adminId]);
    
?>
<div class="">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
           <?php echo \yii::t('app', 'Edit Profile'); ?>
            <small><?php echo \yii::t('app', 'Update')?></small>
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
                <?php
                $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => [
                                'class' => 'form-horizontal',
                                'enctype' => 'multipart/form-data',  
                                ],
                            'fieldConfig' => [
                                'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
                                'labelOptions' => ['class' => 'col-lg-4'],
                            ],
                ]);
                ?> 
              

                <div class="row">
                    <div class="col-lg-2">

                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-8">
                        <div class="row">
                            <?php
                                if($id){
                                   echo $form->field($model, 'id')->hiddenInput(['value'=>$id])->label(false);
                                }
                                ?>
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'first_name', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'First Name')])->label(\yii::t('app', 'First Name'));
                                ?>
                            </div>
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'last_name', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'Last Name')])->label(\yii::t('app', 'Last Name'));
                                ?>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Role</label>
                                <?php
                                if($type == 'Self'){ 
                                    $disabled = 'disabled';
                                } else {
                                    $disabled = '';
                                }?>
                                <select id="roleDropDown" class="form-control" name="role" <?php echo $disabled;?>                                 
                                   <?php
                                   foreach ($roleList as $key=>$val){?>
                                   <option value="<?=$key?>" <?php if($role==$key){echo 'selected';}?> ><?=$val?></option>
                                   <?php }?>
                                </select>
                                
                            </div>  
                            <div class="col-lg-6">
                                <?=
                                $form->field($model, 'email', [
                                    'template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->textInput(['placeholder' => \yii::t('app', 'Email'), 'disabled' => 'disabled'])->label(\yii::t('app', 'Email'));
                                ?>
                            </div>
                                                  
                        </div>
                        
                        <?php $departmentList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>45, 'is_delete'=>1])->all(), 'id', 'value');?>
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'department_id', ['template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])
                                        ->dropDownList($departmentList, 
                                        ['prompt' =>\Yii::t('app', '--Select Department--'),  'onchange'=>'getSubDepartment();'])
                                        ->label( \yii::t('app','Department'));?> 
                            </div>

                            <div class="col-lg-6">
                                <?= $form->field($model, 'sub_department_id', ['template' => "{label}\n<div class=\"col-sm-12\">{input}{error}{hint}</div>"])->dropDownList(['prompt' =>\Yii::t('app', '--Select Sub Department--')])->label( \yii::t('app','Sub Department'));?> 
                            </div>
                        </div>
                        
                        
                        
                         <div class="box-footer">
                        <?= Html::submitButton(\yii::t('app', 'Update'), ['class' => 'btn btn-primary btn-flat pull-right', 'name' => 'update-profile-button']) ?>
                        
                        <?php                         
                        if($id && $id!=Yii::$app->user->identity->id){                                                        
                            ?>
                        <?= Html::a('Go-Back', 'list', ['class' => 'btn btn-success btn-flat pull-left', 'name' => 'back-profile-button']); ?>
                        <?php }else{
                            echo Html::resetButton(\yii::t('app', 'Cancel'), ['class' => 'btn btn-success btn-flat pull-left', 'id' => 'cancel-profile-button']);
                        }?>
                         </div>    
                    </div><!-- /.col-lg-6 -->

                    <div class="col-lg-2">

                    </div><!-- /.col-lg-6 -->
                </div>

                <?php ActiveForm::end(); ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </section><!-- /.content -->
</div>
<style>
.image-wrapper {
    position:relative;
}
.browse-image {
    position: absolute;
    left: 0;
    right: 0;
    top: 42%;
    color: #ffffff;
    font-size: 24px;
    display:none;
    /*display:block;*/
    /*height:30px;*/
    /*width:30px;*/
    margin: 0 auto;
}
.browse-image:hover {
    cursor:pointer;
}
</style>
<script>
    
    function getSubDepartment(e){
        var id = $('#adminpersonal-department_id').val();
        
        $("option:selected").removeAttr("selected");
        $('#adminpersonal-department_id').val(id);
        
         //$('#adminpersonal-department_id option[value='.id.']').prop('selected', true);
        var url = '<?php echo Yii::$app->urlManager->createUrl('index.php/adminuser/admin/subdepartment?id=')?>'+id
        $.ajax({
        type:'get',
        data:{
        },
        url:url,
        success:function(data) {
            $( "select#adminpersonal-sub_department_id" ).html( data );
        }
    });
        //$.post( "'.Yii::$app->urlManager->createUrl('index.php/adminuser/admin/subdepartment?id=').'"+$(e).val(), function( data ) {
        //  $( "select#adminpersonal-sub_department_id" ).html( data );
        //});
    
    }
    
    function readURL(input) {

    if (input.files && input.files[0]) {        
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#user-img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
 $(document).ready(function() {

    $('label[for="adminpersonal-sub_department_id"]').css('width', 'auto');
    setTimeout(function() { 
        $('#roleDropDown').val('<?php echo $role; ?>');  
        $('#roleDropDown').trigger('change');
        //$('#roleDropDown option[value='.<?php echo $role; ?>.']').prop('selected', true);
        }, 800);
   
    $('#adminpersonal-department_id').trigger('change');
     
    setTimeout(function() {
        $('#adminpersonal-sub_department_id').val('<?php echo $model->sub_department_id; ?>');
        $('#adminpersonal-sub_department_id').trigger('change');
    }, 800);


        
        $('#manageuseraddform-department').trigger('click');
     
    $("#adminpersonal-image_path").change(function(){        
       var ext = $("#adminpersonal-image_path").val().split('.').pop().toLowerCase();
       var arrValues = ["png","jpg", "jpeg"];
       if(arrValues.indexOf(ext) <= -1){  
           $("#image_path_msg").text('Only files with these extensions are allowed: png, jpg, jpeg.');
           $(this).val("");
           return false;
       }
       $("#image_path_msg").text('');
       readURL(this);
        
    });
//    $('.browse-image').css('display','none');
    $( ".image-wrapper img, .browse-image" ).hover(
        function() {
          $('.browse-image').css('display','inline-block');
//          $('.browse-image').addClass('visible')
        }, function() {
          $('.browse-image').css('display','none');
//         $('.browse-image').removeClass('visible')
        }
      );
      
    $('.browse-image').click(function(){
        $('.browse-image').addClass('visible');
        $('#adminpersonal-image_path').click();
    });
    
     $("#cancel-profile-button").click(function(){
       $('#user-img').attr('src', $('#hidden-user-img').val());
    });
 });
</script>  
