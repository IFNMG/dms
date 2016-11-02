<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 17-March-2016
 * @description: LookupController interacts with facade for all lookup related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\CityFacade;
use app\models\Cities;
use app\models\CityForm;




class CityController extends \yii\web\Controller {
    
    public $enableCsrfValidation = false;
     public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','create','update','delete','view','changestatus','loadstate'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','create','update','delete','view','changestatus','loadstate'
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
            $lang = \app\facades\common\CommonFacade::getLanguage(); //for jquery datatable language settings
       
            $facade = new CityFacade();
            $response = $facade->listCity();
            $userData = $response['DATA']['SUBDATA'];
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
            return $this->render('Index', ['data' => $userData,'permission'=>$permission, 'lang'=>$lang]);
       
    }
    
    public function actionCreate(){        
     
            $facade = new CityFacade();
            $model = new CityForm();
            if(Yii::$app->request->post()){
              $request=$_POST['CityForm'];              
              $response=$facade->add_modify($request);         
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/city/index"));
              }              
              return $this->render('Create', ['model' => $model]);    
            }
            else{                
                return $this->render('Create', ['model' => $model]);
            }             
           
    }
    
    
    public function actionUpdate(){
       
            $facade = new CityFacade();                   
            $getRequest = Yii::$app->request->get();           
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/city"));                     
            }
            $model = Cities::find()->where(['id'=>$getRequest['id']])->one();  
            $modelCityForm = new CityForm();            
            $stateModel=\app\models\States::find()->where(['id'=>$model['state_id']])->one();
            $country_id=$stateModel->country->id;
            
            $modelCityForm->id=$model['id'];
            $modelCityForm->value=$model['value'];
            $modelCityForm->zip_code=$model['zip_code'];
            $modelCityForm->state_id=$model['state_id'];            
            $modelCityForm->country_id=$country_id;
            $modelCityForm->status=$model['status'];
      
            
            if(Yii::$app->request->post()){                
              $request=$_POST['CityForm'];              
              $response=$facade->add_modify($request);
              $cityData=$response['DATA']['SUBDATA'];  
              
              
                $model = Cities::find()->where(['id'=>$getRequest['id']])->one();  
                $modelCityForm = new CityForm();            
                $stateModel=\app\models\States::find()->where(['id'=>$model['state_id']])->one();
                $country_id=$stateModel->country->id;

                $modelCityForm->id=$model['id'];
                $modelCityForm->value=$model['value'];
                $modelCityForm->zip_code=$model['zip_code'];
                $modelCityForm->state_id=$model['state_id'];            
                $modelCityForm->country_id=$country_id;
                $modelCityForm->status=$model['status'];
                Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
                if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/city/index"));
              }              
              
              return $this->render('Create', ['model' => $modelCityForm]);    
            }
            else{                
                return $this->render('Create', ['model' => $modelCityForm]);
            }             
             
    }
    
    
    public function actionView(){
      
            $facade = new CityFacade();            
            $getRequest = Yii::$app->request->get();       
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/city"));                     
            }
            $response=$facade->viewType($getRequest['id']);            
            $userData = $response['DATA']['SUBDATA'];    
            return $this->render('View', ['model' => $userData]);    
        
    }
    
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new CityFacade();
            $response = $facade->delete($id);
            return json_encode($response);
        }        
    }

    public function actionLoadstate(){
        $getRequest = Yii::$app->request->get();           
        $states=  \app\models\States::find()->select('id,value')->where(['country_id'=>$getRequest['id']])->all();
        echo "<option value=''>---Select--</option>";
        if(!empty($states)){
            foreach($states as $state){
                echo "<option value='".$state->id."'>".$state->value."</option>";             
            }            
        }
    }
    
    public function actionChangestatus(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new CityFacade();
            $response = $facade->changeStatus($id, $status);
            return json_encode($response);
        }

    }
    
    public function actionLoadcity(){
        $getRequest = Yii::$app->request->get();           
        $cities= \app\models\Cities::find()->select('id,value')->where(['state_id'=>$getRequest['id']])->all();
        echo "<option value=''>---Select--</option>";
        if(!empty($cities)){
            foreach($cities as $city){
                echo "<option value='".$city->id."'>".$city->value."</option>";             
            }            
        }
    }
    
    public function findModel($id)
    {
        if (($model = Cities::find()->where(['id'=>$id])->one()) !== null) {
            return true;
        } else { 
           return false; 
        }
    }
    
    
    
}
