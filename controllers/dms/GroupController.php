<?php

namespace app\controllers\dms;

use Yii;
use \app\facades\common\CommonFacade;
use \app\facades\dms\GroupFacade;
use \app\models\Vendor;

class GroupController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
                            
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
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
    
     /*
     * function for getting list of all assessment requests submitted by users
     * @author: Waseem
     */ 
    public function actionList(){        
        $lang = CommonFacade::getLanguage();
        $facade = new GroupFacade();
        $response = $facade->getGroupList();             
        $Data = $response['DATA']['SUBDATA'];     
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        return $this->render('list', ['model' => $Data, 'permission'=>$permission, 'lang'=>$lang]);
    }
    
    public function actionViewlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new VendorFacade();
            $response = $facade->viewList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    } 
    
    /*
     * function for viewing assessment requests complete data
     * @author: Waseem
     */
    public function actionView(){
        $lang = CommonFacade::getLanguage();
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new GroupFacade();
                $response = $facade->viewGroup($id);
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    
                    return $this->render('view', ['model' => $model, 'permission'=>$permission, 'lang'=>$lang]);
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    }
    
     public function actionAdd() {
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                
                $userList = \app\models\AdminPersonal::find()->select(['user_id', 'email'])->all();
                
                $userArr = array();
                foreach($userList as $user){
                    $obj = \app\models\Group::find()->where(['user_id'=>$user->user_id, 'group_id'=>$id])->exists();
                    if(!$obj){
                        $userArr[$user->user_id] = $user->email;
                        //$userArr['EMAIL'] = $user->email;
                        //array_push($userArr, array('ID'=>$user->user_id, 'EMAIL'=>$user->email));
                        
                    }
                }
                $model = new \app\models\Group();
                return $this->render('add', array('model'=>$model, 'id'=>$id, 'userArr'=>$userArr));
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/group/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/group/list"));
        }
    }
    
    
    public function actionSave() {
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            $facade = new GroupFacade();
            $response = $facade->addMember($request);
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];
            
            if ($code == 200){
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/group/list"));
            } else if($code == 100){
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/group/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/ttg/register/list"));
        }
    }
    
    
     /*
     * function for deleteing group member
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new GroupFacade();
            $response = $facade->deleteMember($id);
            return json_encode($response);
        }
    }
    
     /*
     * function for opening a vendor's data in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new VendorFacade();
                $response = $facade->editVendor($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('add', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
        
    }
    
    
}
