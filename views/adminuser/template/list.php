<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;

$this->title = \yii::t('app', 'Manage Templates');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/listing.js');
$this->registerJsFile('@web/js/common.js');
?>
<script>
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = 'language' + '=' + '<?php echo $lang; ?>' + ';expires=' + expires.toUTCString();
</script>

<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \yii::t('app', 'Manage Templates'); ?>
            <small><?php echo \yii::t('app', 'List'); ?></small>
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
                
                
               <table class="table table-hover" id="listing-table">
                <thead>
                    <tr>
                        <th><?php echo \yii::t('app', 'Type'); ?></th>
                        <th><?php echo \yii::t('app', 'Languages'); ?></th>
                        <th><?php echo \yii::t('app', 'Sender'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>10])->all(), 'id', 'value');
                    ?>
                    <?php foreach($model as $list){ ?>
                    <tr>
                        <td><?php echo \yii::t('app', $list['EventValue']); ?></td>
                        <td>
                            <?php 
                            foreach($list['LangArr'] as $lang){
                                if($lang['Status'] == LookupCodes::L_COMMON_STATUS_DISABLED){
                                    $css = '#c23321';
                                } else {
                                    $css = '#3c8dbc';
                                }
                            ?>
                                
                                <?php if($permission->add == 1){ ?>
                                    <!--input type="checkbox" value="<?php //echo $lang['LanguageId'];?>" id="<?php //echo $list['EventId'];?>" class="template_checkbox"--> 
                                    <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/template/edit", 'event'=>$list['EventId'], 'lang'=>$lang['LanguageId']]); ?>" style="color: <?php echo $css;?>; cursor: pointer;">
                                       <?php echo \yii::t('app', $lang['LanguageValue']); ?>
                                    </a>&nbsp;
                                <?php } else { ?>
                                    <label>
                                        <?php echo \yii::t('app', $lang['LanguageValue']); ?>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </td>
                       <td><?php echo \yii::t('app', $list['Sender']); ?></td>
                       
                    </tr>
                    <?php } ?>
                </tbody>
               </table>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <div class="col-lg-10">
                    <span style="padding : 4px 8px; background: #c23321;">
                    </span>
                    <label style="margin-left: 10px;">Inactive Templates</label>  
                    <br>
                    <br>
                    
                    <span style="padding : 4px 8px; background: #3c8dbc;">
                    </span>
                    <label style="margin-left: 10px;">Active Templates</label>  
                    <br>
                    <br>
                    
                    
                    <div style="padding : 5px;" class="col-lg-10 callout btn-info">
                        <h5 style="margin: 0px;">
                            !!! To change the sender email address go to lookups module and update the value of parent of that event.
                        </h5>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
