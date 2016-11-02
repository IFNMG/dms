<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\web\util\Codes\LookupCodes;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ClaritusCORE</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        
        <?php $this->head() ?>
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
        ul.dropdown-menu > li {
            position: relative;
        }
        ul.dropdown-menu-two {
            position: absolute;
            top: 0;
            left:160px;
            display: none;
            padding: 0;
            margin: 0;
            list-style: none;
            font-size: 14px;
            background-color: #fff;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            border: 1px solid #eee;
            border-radius: 4px;
            min-width: 160px;
            z-index: 1000;
        }
        ul.dropdown-menu > li:hover ul.dropdown-menu-two {
            display: block;
        }
        li.dropdown:last-child ul.dropdown-menu > li:hover ul.dropdown-menu-two {
            right:160px;
            left:auto;
        }
        .dropdown-menu-two>li>a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 400;
            line-height: 1.42857143;
            color: #777;
            white-space: nowrap;
        }
        .dropdown-menu-two>li>a:hover {
            background-color: #e1e3e9;
            color: #333;
        }
        .dropdown.user.user-menu {
            position: absolute;
            right: 0;
            top: 0;
        }
        .dropdown.user.user-menu .dropdown-menu {
            left:auto;
            right:0;
        }
        .navbar-header {
            margin-right: 40px!important;
        }
        </style>
        
 <style>
  .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
  .toggle.ios .toggle-handle { border-radius: 20px; }
</style>

        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    </head>
    <!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
    <?php 
    //get controller and action name
    $this->registerJsFile('@web/js/bootstrap-toggle.min.js');
    $controller= Yii::$app->controller->id;
    $action= Yii::$app->controller->action->id;
    $class_fix="";
    if($controller=='adminuser/configuration' && $action=="index"){
        $class_fix="manage-config";
    }
    
    ?>
    <body class="hold-transition skin-blue layout-top-nav <?=$class_fix;?>">
        <div class="wrapper">

            <?php
            
                $nameRes=app\models\AdminPersonal::find()->select('first_name,last_name,created_on')->where(['user_id'=>Yii::$app->user->identity->id])->one();
                $roleRes=app\models\Lookups::find()->select('value')->where(['id'=>Yii::$app->user->identity->role])->one();
                $dateDisplayFormat = app\facades\common\CommonFacade::getDisplayDateFormat();
            
            ?>

            <header class="main-header">
                
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            
                          <?php 
                                $homeUrl="";
                                if(!Yii::$app->admin->isGuestAdmin){
                                    $homeUrl=Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing");
                                }
                            ?>
                            <a href="<?=$homeUrl;?>" class="navbar-brand"><b>Claritus</b>CORE</a>
                            <?php
                            if(!Yii::$app->admin->isGuestAdmin){
                            ?>
                            <a href="<?=$homeUrl;?>" class="navbar-brand"><span class="glyphicon glyphicon-home"></span></a>
                            <?php } ?>
                            
                            
                             <?php if(!Yii::$app->admin->isGuestAdmin){
                            $id = Yii::$app->user->getId();
                            $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1])->one();
                            $nameRes=app\models\AdminPersonal::find()->select('first_name,last_name,created_on,image_path')->where(['user_id'=>$id])->one();
                            $roleRes=app\models\Lookups::find()->select('value')->where(['id'=>$user->role])->one();
                            $dateDisplayFormat = app\facades\common\CommonFacade::getDisplayDateFormat();
                            
                            $permissionList = \app\models\RolePermissions::find()->where(['role_id'=>$user->role, 'is_delete'=>1, 'default'=>1])->all();
                            $menuObj = new \app\facades\common\CommonFacade();
                            
                            $userImg=Yii::$app->request->baseUrl.'/images/user2-160x160.jpg';
                            if($nameRes->image_path!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$nameRes->image_path)){
                               $userImg=Yii::$app->urlManager->createUrl(Yii::$app->params['UPLOAD_URL'].$nameRes->image_path);
                            }
                             
                        ?>
                            
                            
                            <li class="dropdown user user-menu user-my">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                  <!-- The user image in the navbar-->
                                  <img alt="User Image" class="img-circle" src="<?=$userImg;?>">
                                  <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                  <span class="hidden-xs"><?=$nameRes->first_name.' '.$nameRes->last_name;?></span>
                                </a>
                                <ul class="dropdown-menu">
                                  <!-- The user image in the menu -->
                                  <li class="user-header">
                                          <img alt="User Image" class="img-circle" src="<?=$userImg;?>">

                                         
                                          <div class="name-pop"><?=$nameRes->first_name.' '.$nameRes->last_name.' - '.$roleRes->value;?></div>
                                             
                                              <div class="from"><small>Member Since : <?=date($dateDisplayFormat,  strtotime($nameRes->created_on))?></small>
                                              </div>
                                        </li>
                                        <!-- Menu Body -->
                                        <li class="user-body">
                                            <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item">
                                                <a href="<?=Yii::$app->urlManager->createUrl("index.php/adminuser/admin/edit");?>"><b>Edit Profile</b></a>
                                            </li>
                                            <li class="list-group-item">
                                                <a href="<?=Yii::$app->urlManager->createUrl("index.php/adminuser/admin/changepassword");?>"><b>Change Password</b></a> 
                                            </li>
                                          </ul>
                                            
                                        </li>  
                                  <!-- Menu Footer-->
                                  <li class="user-footer">
                                    <div class="pull-left">
                                      
                                    </div>
                                    <div class="pull-right">
                                      <a href="<?=Yii::$app->urlManager->createUrl("index.php/adminuser/admin/logout");?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                  </li>
                                </ul>
                              </li>                                     

                              <?php } ?> 
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                                <i class="fa fa-bars"></i>
                            </button>
                             
                                    
                               
                        </div>
                       
                    <?php
                      if(!Yii::$app->admin->isGuestAdmin){
                        $menuDropDown = $menuObj->createMenu($permissionList);
                      }
                    ?>


    <ul class="dropdown dropdown-menu">
                                        <!-- User image -->
                                                                            
                                        
                                    </ul>

                             
                          

<!--                            </ul>
                            
                            
                            
                            
                        </div> /.navbar-collapse -->
                      
                        <!-- Navbar Right Menu -->
                    </div><!-- /.container-fluid -->
                </nav>
                

                
                
            </header>
            <!-- Full Width Column -->
            <div class="content-wrapper" style="min-height:570px;">
                <div class="container">
                    <section class="content-header">
                        <?php if(isset($page)){ 
                            if($page->showTitle == 0){
                                if($page->title){ ?>    
                        <h1><?php echo $page->title; ?></h1> 
                        <?php } } } ?>  
                    </section>
                    
                    
                    <?php if(isset($page)){ 
                        if($page->content){
                    ?>    
                        <?php echo $page->content; ?>
                    <?php }} ?> 
                    
                </div>
            </div>

        <!-- /.content-wrapper -->
        <?php if(isset($page)){ if($page->layout == LookupCodes::L_CMS_LAYOUT_WITH_HEADER_AND_FOOTER || $page->layout == LookupCodes::L_CMS_LAYOUT_ONLY_WITH_FOOTER){ ?>    
            <footer class="main-footer">
                <div class="container">
                    <div class="pull-right hidden-xs">
                        <b><?php echo \Yii::t('app', 'Version 2.3.0')?></b>
                    </div>
                    <strong><?php echo \Yii::t('app', 'Copyright &copy; 2015-2016 Claritus.')?></strong>
                    <?php echo \Yii::t('app', 'All rights reserved.')?>
                </div><!-- /.container -->
            </footer>
        <?php } } ?> 
        
    </div><!-- ./wrapper -->

<?php $this->endBody() ?>  
</body>
</html>
<?php $this->endPage() ?>
