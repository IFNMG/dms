<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/bootstrap3-wysihtml5.min.css');
$this->registerCssFile('@web/css/bootstrap-responsive.min.css');
$this->registerJsFile('@web/js/bootstrap3-wysihtml5.all.min.js');
?>
    <!--<link rel="stylesheet" href="../web/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">-->

    <style>
        body {
            padding: 0;
        }

        .container > .navbar-header, .container-fluid > .navbar-header, .container > .navbar-collapse, .container-fluid > .navbar-collapse {
            margin-left: 0;
        }

        .search_outer, .search_outer * {
            margin: 0;
            padding: 0;
        }
        .search_outer #term {
            padding: 5px;
            margin-bottom: 10px;
            width: 100%;
        }

        .search_outer input[type="submit"] {
            padding: 5px 10px;
            text-align: center;
            outline: none;
        }

        #advanceSearch {
            padding: 5px 10px;
        }

        @media (max-width: 979px) and (min-width: 768px) {
            .search_outer, .search_outer * {
                margin: 0;
                padding: 0;
            }
        }

    </style>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="">
                <div class="my-overlay">

                    <div class="search_outer">

                        <form method="post" id="searchForm" action="<?php echo Yii::$app->getUrlManager()->createUrl([" index.php/dms/document/search "]); ?>">
                            <div class="my-center" style="margin-top: 17%;">
                                <input type="text" value="" name="term" id="term" placeholder="SEARCH DOCUMENTS" class="span12">
                                <input type="submit" value="SEARCH" name="searchbutton" class="customButtons">
                                <a id="advanceSearch" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list "]); ?>">ADVANCED SEARCH</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mart2">
                <?php
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        $label = 'PENDING';
        if($userObj->user->role == 100008){
            $label = 'APPROVED';
        }
    ?>



            </div>










            <!---END     -->




    </section>
    <!-- /.content -->
