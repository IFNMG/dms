<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use app\facades\adminuser\AdminFacade;
use \app\models\Permissions;
use \app\models\EmailTemplates;
use \app\models\Lookups;
use \app\facades\common\CommonFacade;
use yii\web\UploadedFile;


class TemplateController extends \yii\web\Controller {

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
        $language = CommonFacade::getLanguage();
        
        //$permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        //$model = EmailTemplates::find()->where(['is_delete'=>1])->all();
        //return $this->render('list', array('model'=>$model, 'permission'=>$permission));
        
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $eventList = Lookups::find()->andWhere(['type'=>18, 'is_delete'=>1])->all();
        $langList = Lookups::find()->andWhere(['type'=>20, 'is_delete'=>1])->all();
                
        if($eventList){
            $finalArr = array();
            foreach ($eventList as $event){
                $langArr = array();
                foreach($langList as $lang){
                    $model = EmailTemplates::find()->where(['is_delete'=>1, 'event_id'=>$event->id, 'language'=>$lang->id])->one();
                    if($model){
                        array_push($langArr, array('LanguageId'=>$lang->id, 'LanguageValue'=>$lang->value, 'Status'=>$model->status));
                    } else {
                        array_push($langArr, array('LanguageId'=>$lang->id, 'LanguageValue'=>$lang->value, 'Status'=>''));
                    }
                }
                
                if($event->parent_id != ''){
                    $sender = $event->parent->value;
                } else {
                    $sender = '';
                }
                array_push($finalArr, array('EventId'=>$event->id, 'EventValue'=>$event->value, 'LangArr'=>$langArr, 'Sender'=>$sender));
            }
        }
        
        return $this->render('list', array('model'=>$finalArr, 'permission'=>$permission, 'lang'=>$language));
    }
     
    
    public function actionAdd() {
         $permission = CommonFacade::getPermissions(Yii::$app->request);
        $model = new EmailTemplates();
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            $facade = new AdminFacade();
            $attachment = UploadedFile::getInstance($model, 'attachment');
            $response = $facade->createTemplate($request, $attachment);
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];
            
            if ($code == 200){
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            } else if($code == 100){
                $model = $response['DATA'];
                Yii::$app->getSession()->setFlash('error', $MSG);
            }
            
        }
     	return $this->render('add', array('model'=>$model, 'permission'=>$permission));
    }

    
     /*
     * function for opening a template in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['event']) && isset($_REQUEST['lang'])){
            if($_REQUEST['event'] != '' && $_REQUEST['lang'] != ''){
                $facade = new AdminFacade();
                $response = $facade->editTemplate($_REQUEST['event'], $_REQUEST['lang']);

                $code = $response['CODE'];
                $model = $response['DATA'];
                return $this->render('add', array('model'=>$model, 'permission'=>$permission));
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            }
        } else {
             $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
        }
    }
    
     /*
     * function for viewing template
     * @author: Waseem
     */
    public function actionView(){
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['event']) && isset($_REQUEST['lang'])){
            if($_REQUEST['event'] != '' && $_REQUEST['lang'] != ''){
                $facade = new AdminFacade();
                $response = $facade->editTemplate($_REQUEST['event'], $_REQUEST['lang']);

                $code = $response['CODE'];
                $model = $response['DATA'];
                return $this->render('view', array('model'=>$model, 'permission'=>$permission));
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            }
        } else {
             $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
        }
    }
    
     /*
     * function for viewing template
     * @author: Waseem
     */
    public function actionView_old(){
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new AdminFacade();
                $response = $facade->editTemplate($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('view', array('model'=>$model));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            }
        }   else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
        }
    }
    
     /*
     * function for opening a template in edit mode
     * @author: Waseem
     */
    public function actionEdit_old() {
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new AdminFacade();
                $response = $facade->editTemplate($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('add', array('model'=>$model));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
        }
    }
    
    
     
 
    
      /*
     * function for activating deactivating a template
     * @author: Waseem
     */
    public function actionActivatedeactivate(){
        $request = Yii::$app->request;
        $permission = CommonFacade::getPermissions($request);
        $id = $request->get('id');
        $status = $request->get('status');
        
        if($id){
            $facade = new AdminFacade();
            $model = EmailTemplates::find()->where(['id'=>$id])->one();
            $response = $facade->activateDeactivate($model, $status);
            if($response['CODE'] == 200){
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/template/list"));
            } else {
                return $this->render('add', array('model'=>$model, 'permission'=>$permission));
            }
            //return json_encode($response);
        }
    }
    
      
      /*
     * function for deleteing template
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $event = $request->post('event');
        $lang = $request->post('lang');
        $facade = new AdminFacade();
        $response = $facade->removeAttachment($event, $lang);
        return json_encode($response);
    }
}
