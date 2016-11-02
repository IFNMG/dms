<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use app\facades\adminuser\AdminFacade;
use \app\models\Permissions;
use \app\facades\common\CommonFacade;
use yii\web\UploadedFile;

class PermissionController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate'
                            
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
    
    
    public function beforeAction($e){
        
        $status = CommonFacade::authorize(Yii::$app->request);
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            return parent::beforeAction($e);
        }
    }  
    

   
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function actionAdd() {
        $model = new Permissions();
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            $facade = new AdminFacade();
            $image = UploadedFile::getInstance($model, 'image');
            $response = $facade->createPermission($request, $image);
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];
            
            if ($code == 200){
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
            } else if($code == 100){
                $model = $response['DATA'];
                Yii::$app->getSession()->setFlash('error', $MSG);
            }
            
        }
     	return $this->render('Permissionadd', array('model'=>$model));
    }
    
   
    public function actionList() {
        if($userType == \app\web\util\Codes\LookupCodes::L_USER_TYPE_DEVELOPERS){
            $developer_admin_only = array(0, 1);
        } else {
            $developer_admin_only = 0;
        }
        
        $lang = CommonFacade::getLanguage();
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $model = Permissions::find()->where(['is_delete'=>1, 'developer_admin_only'=>$developer_admin_only])->all();
        return $this->render('Permissionlist', array('model'=>$model, 'permission'=>$permission, 'lang'=>$lang));
    }
    
    
     /*
     * function for opening a permission in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new AdminFacade();
                $response = $facade->editPermission($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('Permissionadd', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
        }
        
    }
    
   
    
      /*
     * function for viewing permission data
     * @author: Waseem
     */
    public function actionView(){
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new AdminFacade();
                $response = $facade->editPermission($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('Viewpermission', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
            }
        }   else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/permission/list"));
        }
    }
    
      /*
     * function for viewing permission data
     * @author: Waseem
     */
    public function actionActivatedeactivate(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new AdminFacade();
            $model = Permissions::find()->where(['id'=>$id])->one();
            //$response = $facade->activateDeactivate($id, $status);
            $response = $facade->activateDeactivate($model, $status);
            return json_encode($response);
        }
    }
    
     
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new AdminFacade();
            $response = $facade->deletePermission($id);
            return json_encode($response);
        }
    }
    
    
}
