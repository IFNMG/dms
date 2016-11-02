<style>
   /*.panel:hover .hoverme{
    display : block !important;
    color:#367FA9;
    }*/
    /*.panel:not(:hover) .functional{
    display : none !important;
    color:#367FA9;
    }    
    */  
/* make sidebar nav vertical */ 
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
<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \yii::t('app', 'Manage Configurations');
$this->params['breadcrumbs'][] = $this->title;
//$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/core.js');
?>


<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
           <?php echo $this->title;?>
            <small><?php echo \yii::t('app', 'List');?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>
 <?php
    $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>  LookupTypeCodes::LT_COMMON_STATUS])->all(), 'id', 'value');    
    //$sectionList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>15])->orderBy(['value'=>SORT_ASC])->all(), 'id', 'value');    
?>
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">                    
            </div>            
            <div class="box-body">   
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div id="config-success-box" class="alert alert-success" style="display:none;">
                    <button class="close" aria-hidden="true" id="config-success-remove" onclick="hideCustomAlertBox('config-success-box','config-success')" type="button">×</button>
                 <span id="config-success"></span>
                </div>
                <div id="config-error-box" class="alert alert-error" style="display:none;">
                    <button class="close" aria-hidden="true" id="config-error-remove" onclick="hideCustomAlertBox('config-error-box','config-error')" type="button">×</button>
                 <span id="config-error"></span>
                </div>
            <div class="col-lg-12">
              <div class="row">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom nav-tabs-vertical">
                  <div class="col-lg-3">
                    <ul class="nav nav-tabs">
                        <?php                        
                        $i=0;                                               
                        foreach ($menu_section_list as $k2=>$v2){
                            $tabClass="";
                            if($i==0){$tabClass="active";}
                            echo '<li class="'.$tabClass.'"><a href="#'.$k2.'" data-toggle="tab">'.$v2.'</a></li>';
                            $i++;
                        }?>                      
                    </ul>
                  </div>
                  <div class="col-lg-9">
                    <div class="tab-content">
                      <?php   
                      $i=0;
                      foreach ($menu_section_list as $k2=>$v2){
                          $tabClass="";
                            if($i==0){$tabClass="active";}
                          echo '<div class="tab-pane '.$tabClass.'" id="'.$k2.'">';
                          
                          ?>
                        
                        <?php                 
                        foreach($data as $k=>$v){ 
                            $autoid="";
                            $autoid=$v['id'];
                            $value=$v['value'];
                            $menu_section=$v['menu_section'];
                            if($menu_section==$k2){
                        ?>                
                        <div class="panel panel-default" id="panel_<?= $autoid?>" onmouseout="hideEditLink(<?=$autoid;?>)" onmouseover="showEditLink(<?= $autoid?>)" >                  
                   <div class="panel-heading">
                       <a id="edit_<?= $autoid;?>" class="hoverme pull-right" href="javascript:void(0);" onclick="showFunctional(<?=$autoid;?>);" style="display:none;"><?php echo \yii::t('app', 'Edit'); ?></a>
                    <?php
                        //functional buttons   
                      echo '<div class="functional pull-right" id="functional_'.$autoid.'" style="display:none;">';                          
                      if($v['auto_generate']==1){                          
                          $autotype=$v['auto_generate_type'];
                          $url=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/configuration/generatekey"]);
                          // show a button with text box
                         echo Html::a(\yii::t('app', 'Generate a key'),'javascript:void(0);', ['class' => 'btn-flat', 'name' => 'generate','style'=>'text-decoration:underline;','onclick'=>"generateKey($autoid,$autotype,'".$url."');"]);                                           
                         echo '&nbsp; &nbsp;';
                       }                       
                       //cancel button
                       $loadChildUrl=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/configuration/getchild"]); 
                       $cancelUrl=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/configuration/cancel"]);
                       echo Html::a(\yii::t('app', 'Cancel'),'javascript:void(0);' , ['class' => 'cancel','style'=>'text-decoration:underline;','name' => 'cancel','onclick'=>"reloadPanel($autoid,'".$value."','".$cancelUrl."','".$loadChildUrl."');"]);                                                                   
                       echo '&nbsp; &nbsp;';
                       //save button
                       $saveUrl=Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/configuration/save"]);
                       echo Html::a(\yii::t('app', 'Save'),'javascript:void(0);' , ['class' => 'save','style'=>'text-decoration:underline;','name' => 'save','onclick'=>"save($autoid,'".$value."','".$saveUrl."','".$cancelUrl."','".$loadChildUrl."');"]);                                                                   
                       echo '</div>';
                    ?>
                      <h3 class="panel-title"><?php echo \yii::t('app', $v['name']);?></h3>
                      <span style="color:#F39C12;font-size:12px;">
                         <?php echo \yii::t('app', $v['hint']);?>
                      </span>
                      <!--<a href="" class="pull-right">edit</a>-->
                    </div>
                   <div class="panel-body">
                      <?php                      
                       //if($v['entity_type']=='DDL'){
                      if($v['source_value']!=''){
                            $ddList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>$v['source_value']])->all(), 'id', 'info1');    
                           ?>
                        <div class="clearfix" style="margin: 0 0 10px">
                            <select rel="<?=$v['short_code'];?>" disabled="true" id="val_<?=$autoid;?>" class="myinput col-lg-2" onchange="loadConfigChild(this.value,<?php echo $v['id'];?>,'<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/configuration/getchild"]); ?>');">
                            <option><?php echo \yii::t('app', '--Select--')?></option>
                            <?php                             
                            foreach ($ddList as $key=>$value){ 
                                ?>
                                        <option value="<?= $key?>" <?php if($v['value']==$key){echo "selected";}?> ><?php echo \yii::t('app', $value)?></option>;
                            <?php }?>
                        </select> 
                        </div>
                        
                        <div id="child_<?=$autoid;?>" class="clearfix">    
                            
                            <?php 
                            $childList=\app\models\Configurations::find()->select(['id','name','value','short_code','hint'])->where(['parent_id'=>$v['id'],'section'=>$v['value']])->all();
                            
                            // $childList = yii\helpers\ArrayHelper::map(\app\models\Configurations::find()->where(['parent_id'=>$v['id'],'section'=>$v['value']])->all(),'id','name', 'value','short_code');                                                    
                             //print_r($childList);
                             
                             foreach ($childList as $key=>$val){
                                 $childShortCode=$val->short_code;
                                 $childValue=$val->value;
                                 $childName=$val->name;
                                 $childId=$val->id;
                                 $childHint=$val->hint;
                                 /*foreach($val as $k1=>$v1){
                                     $id=$k1;
                                     $name=$v1;
                                     $val[$k1]= \yii::t('app', $v1);
                                 }
                                  * 
                                  */
                                 
                                  echo '<div class="clearfix">
                    <label class="pull-left">'.\yii::t('app', $childName).': </label>'.
                    '<input rel="'.$childShortCode.'" disabled="true" id="val_'.$childId.'" type="text" value="'.$childValue.'" class="myinput col-lg-3 col-lg-offset-1" placeholder="'.$childName.'"/>'.
                        '<span style="color:#F39C12;font-size:12px;margin-left:10px;">'.
                          \yii::t('app', $childHint).
                      '</span><br/>
                    </div>';
                             }
                            ?>
                        </div>
                       
                        
                          <?php }
                        else{
                            echo '<input rel="'.$v['short_code'].'" disabled="true" id="val_'.$autoid.'" type="text" value="'.$v['value'].'" placeholder="'.$v['name'].'" class="myinput col-lg-3 pull-left"/>';
                          }
                     
                       ?> 
                    </div>
                </div>         
                            <?php } //EOF if
                            
                        } //EOF loop ?>
                          
                       <?php echo '</div>';
                       $i++;
                      }?>
                
                     
                      <div class="tab-pane" id="tab_2">
                        content 2
                      </div>
                      <!-- /.tab-pane -->
                      <div class="tab-pane" id="tab_3">
                        content 3
                      </div>
                      <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                  </div>
                </div>
                <!-- nav-tabs-custom -->
              </div>
            </div>
            </div><!-- /.box-body -->            
            <div class="box-footer">

            </div>





        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
