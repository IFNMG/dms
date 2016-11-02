<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 17-March-2016
 * @description: CountryController interacts with facade for all lookuptype related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\CountryFacade;
use app\models\Countries;
use yii\web\UploadedFile;



class CountryController extends \yii\web\Controller {
    
    public $enableCsrfValidation = false;
    
     public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','create','update','delete','view','changestatus'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','create','update','delete','view','changestatus'
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
            $facade = new CountryFacade();
            $response = $facade->listCountries();            
            $Data = $response['DATA']['SUBDATA'];
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);            
            
            return $this->render('Index', ['data' => $Data,'permission'=>$permission, 'lang'=>$lang]);
        
    }
    
    public function actionCreate(){        
      
            $facade = new CountryFacade();
            $model = new Countries();
            if(Yii::$app->request->post()){
              $request=$_POST['Countries'];
              $image = UploadedFile::getInstance($model, 'flag_url');
              $response=$facade->add_modify($request,$image);
              
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);   
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/country/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{                
                return $this->render('Create', ['model' => $model]);
            }             
               
    }
    
    
    public function actionUpdate(){
        
            $facade = new CountryFacade();            
            $getRequest = Yii::$app->request->get();  
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/country"));                     
            } 
            $model = Countries::find()->where(['id'=>$getRequest['id']])->one();                       
            if(Yii::$app->request->post()){                
              $request=$_POST['Countries'];
              $image = UploadedFile::getInstance($model, 'flag_url');              
              $response=$facade->add_modify($request,$image);
              $model = Countries::find()->where(['id'=>$getRequest['id']])->one();           
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/country/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{                
                return $this->render('Create', ['model' => $model]);
            }             
             
    }
    
    
    public function actionView(){
        
            $facade = new CountryFacade();            
            $getRequest = Yii::$app->request->get();     
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/country"));                     
            } 
            $response=$facade->viewType($getRequest['id']);
            $userData = $response['DATA']['SUBDATA'];
            
            //$model = Countries::find()->where(['id'=>$getRequest['id']])->one();  
            return $this->render('View', ['model' => $userData[0]]);    
       
    }
    
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new CountryFacade();
            $response = $facade->delete($id);
            return json_encode($response);
        }        
    }
    
    public function actionChangestatus(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new CountryFacade();
            $response = $facade->changeStatus($id, $status);
            return json_encode($response);
        }

    }
    
    public function findModel($id)
    {
        if (($model = Countries::find()->where(['id'=>$id])->one()) !== null) {
            return true;
        } else { 
           return false; 
        }
    }
    
}
