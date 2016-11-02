<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'Push Notifications');
$this->params['breadcrumbs'][] = $this->title;
//$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/core.js');
?>
<script>
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = 'language' + '=' + '<?php echo $lang; ?>' + ';expires=' + expires.toUTCString();
</script>
<?php
    $countryList = yii\helpers\ArrayHelper::map(\app\models\Countries::find()->where(['is_delete'=>1])->all(), 'id', 'value');
   // $stateList = yii\helpers\ArrayHelper::map(\app\models\States::find()->where(['is_delete'=>1])->all(), 'id', 'value');
   // $cityList = yii\helpers\ArrayHelper::map(\app\models\Cities::find()->where(['is_delete'=>1])->all(), 'id', 'value');
?>
<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $this->title?>
            <small><?php echo \yii::t('app', 'Send'); ?></small>
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
            <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
            <form method="post" action="<?=Yii::$app->urlManager->createUrl(["index.php/adminuser/device"])?>">
            <div class="form-group" style="padding: 15px 0px 0px 20px;">
                <label style="margin-right: 10px;">Operating System</label>
                <label class="checkbox-inline">
                    <input name="devicetype" <?php if($_POST && isset($_POST['devicetype'])){if(in_array('300001',$_POST['devicetype'])){echo 'checked';}}?> value="300001" type="checkbox"/>  
                    Android 
                </label>
                <label class="checkbox-inline">
                    <input name="devicetype"  <?php if($_POST && isset($_POST['devicetype'])){if(in_array('300002',$_POST['devicetype'])){echo 'checked';}}?> value="300002" type="checkbox"/>   
                    IOS
                </label>
                <label class="checkbox-inline">
                    <input name="devicetype" value="300003" <?php if($_POST && isset($_POST['devicetype'])){if(in_array('300003',$_POST['devicetype'])){echo 'checked';}}?> type="checkbox"/>     
                    Windows
                </label>
            </div>
            
            
            <div class="form-group" style="padding: 15px 0px 0px 20px;">
                <label style="margin-right: 10px;">Mapping</label>
                <label class="radio-inline">
                    <input name="generalize" checked  value="1" type="radio"/>  
                    Mapped        
                </label>
                <label class="radio-inline">
                    <input name="generalize"  value="2" type="radio"/>    
                    Unmapped      
                </label>
                <label class="radio-inline">
                    <input name="generalize"  value="3" type="radio"/>   
                    All       
                </label>
            </div>
            <?php
                $obj = new \app\facades\common\CommonFacade();
                $screenList = $obj->getLookupDropDown(30);
            ?>
            <div class="row" style="padding: 15px 0px 0px 20px;">
                <div class="col-lg-4 form-group">
                    <label style="margin-right: 10px;">Notification Screen</label>
                    <select class="form-control" id="push_screen">
                    <?php 
                    if($screenList) {
                        //echo "<option value=''>-- Select Screen --</option>";
                        foreach($screenList as $key=>$post){
                            echo "<option value='".$key."'>".$post."</option>";
                        }
                    } else {
                         echo "<option>-</option>";
                    }
                    ?>
                    </select>
                    <p class="help-block help-block-error"></p>
                </div>
                <div class="col-lg-3 form-group">
                    <label style="margin-right: 10px;">Image</label>
                    <input type="file" accept="image/*" placeholder="Icon" name="" id="notification-image">
                    <p class="help-block help-block-error"></p>
                </div>
                
                <div class="col-lg-2" id="previewimgDiv" style="display: none;">
                    <img width="50" height="50" src="" id="previewimg" >
                    <span style="cursor: pointer; vertical-align: top;" title="Remove" class="glyphicon glyphicon-remove" id="remove_image"></span>
                </div>
                
            </div>    
                
                
            <div class="col-lg-4 unmappedClass" style="padding: 15px 15px 0px 20px;">
                <label style="margin-right: 10px;">Country</label>
                <select class="form-control" name="countryList" id="countryList" multiple>
        
                <?php 
                if($countryList) {
                    echo "<option value=''>-- Select Country --</option>";
                    echo "<option value='0'>All</option>";
                    foreach($countryList as $key=>$post){
                        echo "<option value='".$key."'>".$post."</option>";
                    }
                } else {
                     echo "<option>-</option>";
                }
                ?>
                </select>   
                
                
            </div>    
                
            <div class="col-lg-4 unmappedClass" style="padding: 15px 15px 0px 20px;">
                <label style="margin-right: 10px;">State</label>
                <select class="form-control" name="stateList" id="stateList" multiple></select>   
            </div>    
                
                
            <div class="col-lg-4 unmappedClass" style="padding: 15px 15px 0px 20px;">
                <label style="margin-right: 10px;">City</label>
                <select class="form-control" name="cityList" id="cityList" multiple></select>   
            </div>        
            
                
            <div class="form-group messageDiv" style="padding: 15px 15px 0px 20px;">
                <label for="" class="control-label">Message</label>
                <textarea  maxlength="250" placeholder="Message" name="" class="form-control" id="my_message"></textarea>
                <p class="help-block help-block-error"></p>
                <label for="" class="control-label">Maximum length is 250 characters.</label>
            </div>    
            
                
            <div class="form-group tableDiv" style="padding: 15px 15px 0px 20px; display:none;">
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Device Id</th>
                            <th>User Name</th>
                        </tr>
                    </thead>
                    
                    
                </table>
            </div>        
                
                
                
            <input id="sendNotification" type="button" class="btn btn-info btn-sm" style="padding:3px 12px; margin: 20px;" value="Send"/>
            <input id="viewList" type="button" class="btn btn-info btn-sm" style="padding:3px 12px; margin: 20px;" value="View List"/>
            <input onclick="location.reload();" type="button" class="btn btn-info btn-sm" style="padding:3px 12px; margin: 20px;" value="Reset Filter"/>
            </form>   
            
            
            
            
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->

<script>
    
    $('body').on('change', '#notification-image', function(){
        if (this.files && this.files[0]) {
            var avatar = $(this).val();
            var extension = avatar.split('.').pop().toUpperCase();
            if (extension === "PNG" || extension === "JPG" || extension === "JPEG"){
                if(this.files[0].size <= 250000){
                    $('#notification-image').next('.help-block-error').text('');
                    var reader = new FileReader();
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                } else {
                    this.value = null;
                    $('#previewimg').attr('src', '');
                    $('#previewimgDiv').hide();
                    $('#notification-image').next('.help-block-error').text('Maximum allowed image size is 250KB.');
                    $('#notification-image').parent('.form-group').addClass('has-error');
                }
            } else {
                this.value = null;
                $('#previewimgDiv').hide();
                $('#previewimg').attr('src', '');
                $('#notification-image').next('.help-block-error').text('Only PNG, JPG and JPEG formats are allowed.');
                $('#notification-image').parent('.form-group').addClass('has-error');
             }
         }
     });

     function imageIsLoaded(e) {
         $('#previewimg').attr('src', e.target.result);
         $('#previewimgDiv').show();
     };
     
     
    $('#push_screen').blur(function(e){
        if(this.val != ''){
            $(this).next('.help-block-error').text('');
            $(this).parent('.form-group').removeClass('has-error');    
        } //else {
            //$(this).next('.help-block-error').text('Please select the notification landing screen.');
            //$(this).parent('.form-group').addClass('has-error');
        //}
    });    
    
    
    $('#my_message').blur(function(e){
        if(this.val != ''){
            $(this).next('.help-block-error').text('');
            $(this).parent('.form-group').removeClass('has-error');    
        } //else {
            //$(this).next('.help-block-error').text('Please select the notification landing screen.');
            //$(this).parent('.form-group').addClass('has-error');
        //}
    });
    
    
    $('#countryList').change(function(){
        var length = $("#countryList option:selected").length;
        if(length == 1){
            if(this.value == 0){
                $('#stateList').html('<option value="0" selected>All</option>');
                $('#cityList').html('<option value="0" selected>All</option>');
            } else if(this.value != ''){ 
                $.ajax({
                    type:'post',
                    data:{
                    },
                    url : '<?php echo Yii::$app->urlManager->createUrl('index.php/adminuser/device/loadstate?id='); ?>'+this.value,
                    success:function(status) {
                        $('#stateList').html(status);
                        $('#cityList').html('<option>--Select--</option>');
                    }
                });
            }
        } else {
            $('#stateList').html('<option value="0" selected>All</option>');
            $('#cityList').html('<option value="0" selected>All</option>');
        }
            
    });

    
    $('#stateList').change(function(){
        var length = $("#stateList option:selected").length;
        if(length == 1){
            if(this.value == 0){
                $('#cityList').html('<option value="0" selected>All</option>');
            } else if(this.value != '') {
                $.ajax({
                    type:'post',
                    data:{
                    },
                    url : '<?php echo Yii::$app->urlManager->createUrl('index.php/adminuser/device/loadcity?id='); ?>'+this.value,
                    success:function(status) {
                         $('#cityList').html(status);
                    }
                });
            }    
        } else {
            $('#cityList').html('<option value="0" selected>All</option>');
        }    
    });
    
     
    $('input:radio[name=generalize]').change(function () {
        var generalize = $("input[name='generalize']:checked").val();
        if (generalize == '2' || generalize == '3') {
            $('#countryList').val('');
            $('#stateList').val('');
            $('#cityList').val('');
            $('.unmappedClass').hide();
        } else {
            $('.unmappedClass').show();
        }
        $('#viewList').trigger('click');
    });
     
     
    $('#sendNotification').click(function () {
        var flag = 1;
        var countryList = [];
        var stateList = [];
        var cityList = [];
        var deviceList = [];
        var generalize = 3;
        var image = $('#previewimg').attr('src');
        
        
        $('input[name="generalize"]:checked').each(function(){
            generalize = $(this).val();
        });
        
        $('input[name="devicetype"]:checked').each(function(){
            deviceList.push($(this).val());
        });
        
        if(generalize != '2'){
            if($('#countryList').val() != 0){
                $('#countryList option:selected').each(function() {
                    countryList.push($(this).val());
                });
            }
            if($('#stateList').val() != 0){
                $('#stateList option:selected').each(function() {
                    stateList.push($(this).val());
                });
            }    
            if($('#cityList').val() != 0){
                $('#cityList option:selected').each(function() {
                    cityList.push($(this).val());
                });
            }    
        }
        
        var screen = $('#push_screen').val();
        if(screen == ''){
            flag = 0;
            $('#push_screen').next('.help-block-error').text('Please select the notification landing screen.');
            $('#push_screen').parent('.form-group').addClass('has-error');
        }
        
        //var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
        var message = $('#my_message').val();
        if(message == ''){
            flag = 0;
            $('#my_message').next('.help-block-error').text('Please enter a message.');
            $('#my_message').parent('.form-group').addClass('has-error');
            
        } //else if(message.indexOf(iChars) == -1) {
          //  alert ("Message has special characters. \nThese are not allowed.\n");
          //  flag = 0;
        //}
     
        if(flag == 1){
            $.ajax({
                type:'post',
                data:{
                    countryList :countryList,
                    stateList :stateList,
                    cityList :cityList,
                    deviceList :deviceList,
                    generalize :generalize,
                    message: message,
                    screen: screen,
                    image: image
                },
                url : '<?=Yii::$app->urlManager->createUrl(["index.php/adminuser/device/savenotification"])?>',
                success:function(status) {
                    //if(status){
                    //    alert('Successfully sent.');
                    //}
                    
                    var parsedData = JSON.parse(status);
                    if(parsedData.CODE == 200){
                        alert(parsedData.MESSAGE);
                        location.reload();
                    } else {
                        alert(parsedData.MESSAGE);
                    }
                    
                }
            });
        }
    }); 
    
    $('#remove_image').click(function () {
        $('#notification-image').val('')
        $('#previewimg').attr('src', '');
        $('#previewimgDiv').hide();
    });  
     
    $('#viewList').click(function () {
        
        var countryList = [];
        var stateList = [];
        var cityList = [];
        var deviceList = [];
        var generalize = 3;
        
        $('input[name="generalize"]:checked').each(function(){
            generalize = $(this).val();
        });
        
        $('input[name="devicetype"]:checked').each(function(){
            deviceList.push($(this).val());
        });
        
        if(generalize != '2'){
            if($('#countryList').val() != 0){
                $('#countryList option:selected').each(function() {
                    countryList.push($(this).val());
                });
            }
            if($('#stateList').val() != 0){
                $('#stateList option:selected').each(function() {
                    stateList.push($(this).val());
                });
            }    
            if($('#cityList').val() != 0){
                $('#cityList option:selected').each(function() {
                    cityList.push($(this).val());
                });
            }    
        }
        
        
        var table = $('#example').DataTable();
        table.destroy();
        
        
        $(document).ready(function() {
            $('#example').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "../adminuser/device/list",
                    "data": function ( d) {
                        d.countryList = countryList;
                        d.stateList = stateList;
                        d.cityList = cityList;
                        d.deviceList = deviceList;
                        d.generalize = generalize;
                    
                    }
                }
            });
        });
        $('.tableDiv').show();
        /*
        $.ajax({
            type:'post',
            data:{
                countryList :countryList,
                stateList :stateList,
                cityList :cityList,
                deviceList :deviceList,
                generalize :generalize
            },
            url : '<?= Yii::$app->urlManager->createUrl(["index.php/adminuser/device/list"])?>',
            success:function(status) {
                
                var parsedData = JSON.parse(status);
                if(parsedData.CODE == 200){
                    
                    var t = $('#example').DataTable();
                    t.clear().draw();
                    for(var i = 0; i<parsedData.DATA.length; i++){
                        t.row.add( [
                            parsedData.DATA[i].id,
                            parsedData.DATA[i].device_id,
                            parsedData.DATA[i].user_name,
                        ] ).draw( false );
                    }
                    //$('.messageDiv').hide();
                    $('.tableDiv').show();
                }
                
            }
        });
        */
    }); 
    
    
</script>
