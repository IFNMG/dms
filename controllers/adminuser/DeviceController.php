<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 16-March-2016
 * @description: LookupTypeController interacts with facade for all lookuptype related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\DeviceFacade;
use app\models\Devices;
use app\facades\common\CommonFacade;


class DeviceController extends \yii\web\Controller {
    
    public $enableCsrfValidation = false;
    
     public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','Savenotification', 'Sendnotifications', 'List', 'Loadstate', 'Loadcity' 
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','Savenotification', 'Sendnotifications', 'List', 'Loadstate', 'Loadcity'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }   
    
    public function beforeAction($e){
        
        $status = \app\facades\common\CommonFacade::authorize(Yii::$app->request);        
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        } else {           
            return parent::beforeAction($e);
        }
    }
    
    
    public function actionIndex(){
        
        $lang = CommonFacade::getLanguage(); //for jquery datatable language settings
        $facade = new DeviceFacade();        
        if(Yii::$app->request->post()){
              $response = $facade->listSearchDevice($_POST);        
        }else{
            $response = $facade->listDevice();
        }
        $deviceData = $response['DATA']['SUBDATA'];           
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $dateDisplayFormat = CommonFacade::getDisplayDateFormat();
        return $this->render('Index', ['data' => $deviceData,'permission'=>$permission,'date_format'=>$dateDisplayFormat, 'lang'=>$lang]);
    }    
    
    public function actionSavenotification(){
        if(Yii::$app->request->post()){
            $request = Yii::$app->request->post();
            $facade = new DeviceFacade();
            $response = $facade->saveNotifications($request);
            return json_encode($response);
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
        }
    }    
    
    public function actionSendnotifications(){
        $request = Yii::$app->request->post();
        $facade = new DeviceFacade();
        $response = $facade->sendNotifications();
        echo "<pre>";print_r($response);
        //return json_encode($response);
    }    
    
    
    public function actionList(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new DeviceFacade();
            $response = $facade->viewList($request);
            return json_encode($response);
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
        }
    } 
    
    public function actionLoadstate(){
        $getRequest = Yii::$app->request->get();           
        $states=  \app\models\States::find()->select('id,value')->where(['country_id'=>$getRequest['id']])->all();
        echo "<option value=''>---Select--</option>";
        echo "<option value='0'>All</option>";
        if(!empty($states)){
            foreach($states as $state){
                echo "<option value='".$state->id."'>".$state->value."</option>";             
            }            
        }
    }
    
    public function actionLoadcity(){
        $getRequest = Yii::$app->request->get();           
        $cities= \app\models\Cities::find()->select('id, value')->where(['state_id'=>$getRequest['id']])->all();
        echo "<option value=''>---Select--</option>";
        echo "<option value='0'>All</option>";
        if(!empty($cities)){
            foreach($cities as $city){
                echo "<option value='".$city->id."'>".$city->value."</option>";             
            }            
        }
    }
  
}
