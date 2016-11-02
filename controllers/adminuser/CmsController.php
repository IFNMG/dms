<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use \app\models\Permissions;
use \app\facades\common\CommonFacade;
use \app\models\Pages;
use \app\facades\adminuser\CmsFacade;
use yii\web\UploadedFile;

class CmsController extends \yii\web\Controller {

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
    
    public function actionList() {
        $lang = CommonFacade::getLanguage();
        
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $pagesList = Pages::find()->where(['is_delete'=>1])->all();
        return $this->render('list', array('model'=>$pagesList, 'permission'=>$permission, 'lang'=>$lang));
    }
     
    
    public function actionAdd() {
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $model = new Pages();
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            $facade = new CmsFacade();
            $image = UploadedFile::getInstance($model, 'image');
            
            $response = $facade->createPage($request, $image);
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];
            
            if ($code == 200){
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
            } else if($code == 100){
                $model = $response['DATA'];
                Yii::$app->getSession()->setFlash('error', $MSG);
            }
            
        }
     	return $this->render('add', array('model'=>$model, 'permission'=>$permission));
    }

    
      /*
     * function for opening a page in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new CmsFacade();
                $response = $facade->editPage($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('add', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
        }
        
    }
    
      /*
     * function for viewing page data
     * @author: Waseem
     */
    public function actionView(){
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new CmsFacade();
                $response = $facade->editPage($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('view', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
            }
        }   else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/cms/list"));
        }
    }
    
     
      /*
     * function for changing page status
     * @author: Waseem
     */
    public function actionActivatedeactivate(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new \app\facades\adminuser\AdminFacade();
            $model = Pages::find()->where(['id'=>$id])->one();
            $response = $facade->activateDeactivate($model, $status);
            return json_encode($response);
        }
    }
    
      
      /*
     * function for deleteing template
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new CmsFacade();
            $response = $facade->deletePage($id);
            return json_encode($response);
        }
    }
}
