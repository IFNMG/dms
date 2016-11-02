<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use app\facades\adminuser\AdminFacade;
use app\models\Users;
use app\models\LoginForm;
use app\models\ChangepasswordForm;
use app\models\ForgotpasswordForm;
use app\models\ResetpasswordForm;
use \app\models\Permissions;
use \app\models\RolePermissions;
use \app\models\Lookups;
use app\facades\common\CommonFacade;
use app\web\util\Codes\LookupCodes;
use yii\web\UploadedFile;




class AdminController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'changepassword', 'view'
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'changepassword', 'view'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
    
    
    public function actionError(){
        
        //$error = Yii::$app->errorHandler->error;
        //die;
        //if( $error ){
         return   $this -> render( 'error');
        //}
    }
    
    
    public function beforeAction($e){
        $action = Yii::$app->controller->action->id;
        
        if($action == 'list' || $action == 'add'  || $action == 'delete' || $action == 'view' || $action == 'activatedeactivate'){
       
            $status = \app\facades\common\CommonFacade::authorize(Yii::$app->request);
             //echo $action;die;    
            if(!$status){
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
            } else {
                return parent::beforeAction($e);
            }
        } else {
            return parent::beforeAction($e);
        }
    }  
    
    
     /*
     * function for activating deactivating user
     * @author: Waseem
     */
    public function actionSubdepartment(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        
        if($id){
            $countPosts = Lookups::find()
                    ->where(['parent_id' => $id, 'is_delete'=>1, 'status'=>550001])
                    ->orderBy('id DESC')
                    ->count();
            $posts = Lookups::find()
                    ->where(['parent_id' => $id, 'is_delete'=>1, 'status'=>550001])
                    ->orderBy('id DESC')
                    ->all();

            if($countPosts>0){
                echo "<option value=''>--Select Sub Department--</option>";
                foreach($posts as $post){
                    echo "<option value='".$post->id."'>".$post->value."</option>";
                }
            } else {
                echo "<option>--Select Sub Department--</option>";
            }
            
        }
    }
    
      /*
     * function for activating deactivating user
     * @author: Waseem
     */
    public function actionActivatedeactivate(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new AdminFacade();
            $model = Users::find()->where(['id'=>$id])->one();
            $response = $facade->activateDeactivate($model, $status);
            return json_encode($response);
        }
    }
    
      /*
     * function for deleteing user
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new AdminFacade();
            $response = $facade->deleteUser($id);
            return json_encode($response);
        }
    }
   
    public function actionIndex() {

        echo Yii::$app->admin->adminId;
    }

    
    public function actionLogin() {
        if (!Yii::$app->admin->isGuestAdmin) {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        }
        $facade = new AdminFacade();
        $model = new LoginForm();
        $request = Yii::$app->request->post();

        if (!empty($request['LoginForm'])) {

            $data = $request['LoginForm'];
            $response = $facade->Login($data);

            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];

            if ($response && $response['DATA']['STATUS'] == 'error')
                Yii::$app->getSession()->setFlash('error', $MSG);
            else if ($response && $response['DATA']['STATUS'] == 'success') {
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
            }

            return $this->render('Login', [
                        'model' => $model,
            ]);
        } else {

            return $this->render('Login', [
                        'model' => $model,
            ]);
        }
    }

    
    public function actionLanding() {                
         if (!Yii::$app->admin->isGuestAdmin) { 
        $lang = \app\facades\common\CommonFacade::getLanguage(); //for jquery datatable language settings
       
        $facade = new AdminFacade();
        $response = $facade->dashboardData();
        $data = $response['DATA'];
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);        
        return $this->render('Landing', ['data' => $data,'permission'=>$permission,'lang'=>$lang]);
         }else{
              $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/logout"));
         }
       
    }

    
    public function actionView() {
       
        if (!Yii::$app->admin->isGuestAdmin) {
            $facade = new AdminFacade();
            $idRequest = Yii::$app->request->get();
        if($idRequest&&$idRequest['id']){
            $response = $facade->getProfile($idRequest['id']);
        }else{
            $response = $facade->getProfile();
        }
            $userData = $response['DATA']['SUBDATA'];
            return $this->render('Profile', ['model' => $userData,'id'=>$idRequest?$idRequest['id']:0]);
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }
    
    
    public function actionList() {        
        $lang = CommonFacade::getLanguage();
        if (!Yii::$app->admin->isGuestAdmin) {
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
            $facade = new AdminFacade();
            
            $id = Yii::$app->user->getId();
            if($id){
                $user = \app\models\Users::find()->where(['id'=>$id, 'is_delete'=>1, 'status'=>550001])->one();
                if($user){
                    $response = $facade->listUser($user);
                    $userData = $response['DATA']['SUBDATA'];
                    $dateDisplayFormat = CommonFacade::getDisplayDateFormat();
                    return $this->render('Manageuserindex', ['data' => $userData, 'permission'=>$permission,'date_format'=>$dateDisplayFormat, 'lang'=>$lang]);
                } else {
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }
    
    
    public function actionAdd() {

        if (!Yii::$app->admin->isGuestAdmin) {
            
            if(Yii::$app->request->post()){
                $request = Yii::$app->request->post();
                $facade = new AdminFacade();
                $userFormData = new \app\models\ManageuseraddForm();
                $image = UploadedFile::getInstance($userFormData, 'image_path'); 
                //$response = $facade->createUser($request['ManageuseraddForm']);
                $response = $facade->createUser($request,$image);
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];
                
                if ($response && $response['DATA']['STATUS'] == 'error'){
                    Yii::$app->getSession()->setFlash('error', $MSG);
                    $userFormData = $response['DATA']['DATA'];
                    
                } else if ($response && $response['DATA']['STATUS'] == 'success'){ 
                    Yii::$app->getSession()->setFlash('success', $MSG);
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/list"));
                }
                
                $data = \app\facades\common\CommonFacade::getOrderwiseUserTypes(['id'=>Yii::$app->admin->adminId]);
                
                return $this->render('Manageuseradd', ['model' => $userFormData,'types'=>$data]);
                
            }else{
                $facade = new AdminFacade();

                $response = $facade->getProfile();
                $userFormData = new \app\models\ManageuseraddForm();
                $data = \app\facades\common\CommonFacade::getOrderwiseUserTypes(['id'=>Yii::$app->admin->adminId]);

                return $this->render('Manageuseradd', ['model' => $userFormData,'types'=>$data]);
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }

    
    public function actionEditprofile() {
       

        if (!Yii::$app->admin->isGuestAdmin) {
            
            $facade = new AdminFacade();
            $request = Yii::$app->request->post();

            if (!empty($request['AdminPersonal'])) {

                $data = $request['AdminPersonal'];
                $response = $facade->updateProfile($data,'');
                //echo "<pre>";
                //print_r($response);die;
                if ($response && $response['DATA']['STATUS'] == 'success') {

                    $code = $response['CODE'];
                    $MSG = $response['MESSAGE'];
                    Yii::$app->getSession()->setFlash('success', $MSG);
                    $userData = $response['DATA']['SUBDATA'];

                    return $this->render('Editprofile', ['model' => $userData,'id'=>0]);
                } else if ($response && $response['DATA']['STATUS'] == 'error') {

                    $code = $response['CODE'];
                    $MSG = $response['MESSAGE'];

                    Yii::$app->getSession()->setFlash('error', $MSG);
                    $userData = $response['DATA']['SUBDATA'];

                    return $this->render('Editprofile', ['model' => $userData,'id'=>0]);
                }
            } else {

                $response = $facade->getProfile();                
                $userData = $response['DATA']['SUBDATA'];

                return $this->render('Editprofile', ['model' => $userData,'id'=>0]);
            }
        } else {

            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }
    
    
    public function actionEdit() {
        $type = 'Other';
        
        if (!Yii::$app->admin->isGuestAdmin) {
            
            $facade = new AdminFacade();
            $request = Yii::$app->request->post();
            $idRequest = Yii::$app->request->get();
            
            if(isset($idRequest['id'])){     
                    $roleId= Users::find()->select('role')->where(['id'=>$idRequest['id']])->one();
                    $roleId=$roleId->role;
                } else {                  
                    $type = 'Self';
                    $roleId= Users::find()->select('role')->where(['id'=>Yii::$app->admin->adminId])->one();
                    $roleId=$roleId->role;
                } 
            
            
            if (!empty($request['AdminPersonal'])) {
                $data = $request['AdminPersonal'];                  
                $data['role'] = $request['role'];
                
                if($data['id']){
                   $response = $facade->updateProfile($data, $data['id']);                   
                } else {
                    $response = $facade->updateProfile($data);                    
                }    
                //echo "<pre>";
                //print_r($response);die;
                if ($response && $response['DATA']['STATUS'] == 'success') {

                    $code = $response['CODE'];
                    $MSG = $response['MESSAGE'];
                    Yii::$app->getSession()->setFlash('success', $MSG);
                    $userData = $response['DATA']['SUBDATA'];
                    if($idRequest['id']!=""){
                        $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/list"));
                    }else{
                        $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/edit"));
                    }
                    
                    //return $this->render('Editprofile', ['role'=>$roleId, 'model' => $userData,'id'=>(isset($data['id']))?$data['id']:'']);
                    
                } else if ($response && $response['DATA']['STATUS'] == 'error') {

                    $code = $response['CODE'];
                    $MSG = $response['MESSAGE'];

                    Yii::$app->getSession()->setFlash('error', $MSG);
                    $userData = $response['DATA']['SUBDATA'];
                    
                    return $this->render('Editprofile', ['role'=>$roleId,'model' => $userData,'id'=>(isset($data['id']))?$data['id']:'']);
                }
            } else {
                if($idRequest){
                    $id = $idRequest['id'];
                    $response = $facade->getProfile($id);
                } else {
                     $id = Yii::$app->admin->adminId;
                    //$response = $facade->getProfile();
                    $response = $facade->getProfile($id);
                    
                }
                $userData = $response['DATA']['SUBDATA'];


                return $this->render('Editprofile', ['model' => $userData,'id'=>$id,'role'=>$roleId, 'type'=>$type]);
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/logout"));
        }
    }

    
    public function actionChangepassword() {
        if (!Yii::$app->admin->isGuestAdmin) {

            $model = new ChangepasswordForm();
            $request = Yii::$app->request->post();

            if (!empty($request['ChangepasswordForm'])) {

                $facade = new AdminFacade();
                $data = $request['ChangepasswordForm'];

                $response = $facade->Changepassword($data);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($response && $response['DATA']['STATUS'] == 'error')
                    Yii::$app->getSession()->setFlash('error', $MSG);
                else if ($response && $response['DATA']['STATUS'] == 'success')
                    Yii::$app->getSession()->setFlash('success', $MSG);

                return $this->render('Changepassword', [
                            'model' => $model,
                ]);
            } else {
                return $this->render('Changepassword', [
                            'model' => $model,
                ]);
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }

    
    public function actionForgotpassword() {
        if (!Yii::$app->admin->isGuestAdmin) {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        }
        $facade = new AdminFacade();
        $model = new ForgotpasswordForm;
        $request = Yii::$app->request->post();

        if (!empty($request['ForgotpasswordForm'])) {

            $data = $request['ForgotpasswordForm'];

            $response = $facade->Forgotpassword($data);

            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];

            if ($response && $response['DATA']['STATUS'] == 'error')
                Yii::$app->getSession()->setFlash('error', $MSG);
            else if ($response && $response['DATA']['STATUS'] == 'success')
                Yii::$app->getSession()->setFlash('success', $MSG);

            return $this->render('Forgotpassword', [
                        'model' => $model,
            ]);
            /* return $this->render('Forgotpassword', [
              'model' => $model,
              ]); */
        } else {
            return $this->render('Forgotpassword', [
                        'model' => $model,
            ]);
        }
    }

    
    public function actionResetpassword() {
        if (!Yii::$app->admin->isGuestAdmin) {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/logout"));
        } 
        
        
        $facade = new AdminFacade();
        $model = new ResetpasswordForm();
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            if ($request && $request['ResetpasswordForm']) {
                
                $response = $facade->resetPassword($request['ResetpasswordForm']);
                
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];
                
                if ($response && $response['DATA']['STATUS'] == 'success') {
                    Yii::$app->getSession()->setFlash('success', $MSG);
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
                } else {
                    Yii::$app->getSession()->setFlash('error', $MSG);
                    return $this->render('Resetpassword', [
                                'model' => $model,
                    ]);
                }
            }
        } else if (Yii::$app->request->get()) {
            $request = Yii::$app->request->get();
            
            if ($request) {
                $id = $request['id'];
                $response = $facade->authorizeResetToken(['secretHash' => $id]);
                $model->setAttributes(['secretHash' => $id]);
                
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];
                
                if ($response && $response['DATA']['STATUS'] == 'success') {
                    Yii::$app->getSession()->setFlash('success', $MSG);
                    return $this->render('Resetpassword', [
                                'model' => $model,
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', $MSG);
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/forgotpassword"));
                }
            } else {
               return false;
            }
        }
    }

    
    public function actionLogout() {
        if (Yii::$app->admin->isGuestAdmin) {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
        $model = new LoginForm();
        $facade = new AdminFacade();
        $response = $facade->Logout();
        Yii::$app->user->logout();
        
        $code = $response['CODE'];
        $MSG = $response['MESSAGE'];

        if ($response && $response['DATA']['STATUS'] == 'success')
            Yii::$app->getSession()->setFlash('success', $MSG);

        $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
    }
    
   
}
