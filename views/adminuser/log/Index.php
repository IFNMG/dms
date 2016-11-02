<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = \yii::t('app', 'Manage Logs');
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
           <?php echo $this->title;?>
            <small><?php echo \yii::t('app', 'List'); ?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>
<?php
    $statusList = yii\helpers\ArrayHelper::map(\app\models\Lookups::find()->where(['type'=>10])->all(), 'id', 'value');
?>
    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">              
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
               <table id="listing-table" class="table table-hover">
                <thead><tr>
                  <th><?php echo \yii::t('app', 'S.No.')?></th>
                  <th><?php echo \yii::t('app', 'Name')?></th>                 
                </tr>
                </thead><tbody>
                <?php   
                $i=0;
                foreach($data AS $k=>$v){                      
                    $i++;
                    ?>
               <tr id="tr_<?= $i;?>">
                   <td><?= $i;?></td>
                   <td><?php echo Html::a($v['Name'], $v['Url'], ['class' => '', 'name' => 'log','target'=>'_blank']); ?></td>                  
                </tr>
                <?php } ?>
              </tbody></table>
            </div><!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
