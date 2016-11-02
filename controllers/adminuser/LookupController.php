<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 17-March-2016
 * @description: LookupController interacts with facade for all lookup related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\LookupFacade;
use app\models\Lookups;
use yii\web\UploadedFile;
use app\web\util\Codes\LookupCodes;

class LookupController extends \yii\web\Controller {
     
    public $enableCsrfValidation = false;
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','create','update','delete','view','changestatus','loadparent'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','create','update','delete','view','changestatus','loadparent'
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
            $facade = new LookupFacade();
            $response = $facade->listLookup();
            $userData = $response['DATA']['SUBDATA'];
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
            return $this->render('Index', ['data' => $userData,'permission'=>$permission, 'lang'=>$lang]);        
    }
    
    public function actionCreate(){ 
            $facade = new LookupFacade();
            $model = new Lookups();
            if(Yii::$app->request->post()){
              $request=$_POST['Lookups'];
              $image = UploadedFile::getInstance($model, 'image_path');
              
              $response=$facade->add_modify($request,$image);
              
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookup/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{
                $model->is_seed_data=0;
                $model->status=  LookupCodes::L_COMMON_STATUS_ENABLED;
                return $this->render('Create', ['model' => $model]);
            } 
    }
    
    
    public function actionUpdate(){        
            $facade = new LookupFacade();            
            $getRequest = Yii::$app->request->get();  
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookup"));                     
            }  
            $model = Lookups::find()->where(['id'=>$getRequest['id']])->one();           
            
            if(Yii::$app->request->post()){                
              $request=$_POST['Lookups'];
              $image = UploadedFile::getInstance($model, 'image_path');
              $response=$facade->add_modify($request,$image);              
              $model = Lookups::find()->where(['id'=>$getRequest['id']])->one();           
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookup/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{                
                return $this->render('Create', ['model' => $model]);
            }  
    }
    
    
    public function actionView(){      
            $facade = new LookupFacade();            
            $getRequest = Yii::$app->request->get();  
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookup"));                     
            }
            $response=$facade->viewType($getRequest['id']);
            $userData = $response['DATA']['SUBDATA'];            
            return $this->render('View', ['model' => $userData[0]]);    
    }
    
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new LookupFacade();
            $response = $facade->delete($id);
            return json_encode($response);
        }        
    }

    public function actionLoadparent(){
        $getRequest = Yii::$app->request->get();           
        echo "<option value=''>---Select--</option>";
        $parentType=\app\models\LookupTypes::find()->select('parent_id')->where(['id'=>$getRequest['id']])->one();
        if($parentType->parent_id!=""){
            if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
                $parents=Lookups::find()->select('id,value')->where(['type'=>$parentType->parent_id])->all();            
            }else{
                $parents=Lookups::find()->select('id,value')->where(['type'=>$parentType->parent_id,'is_seed_data'=>0])->all();            
            }
            
            
            if(!empty($parents)){
                foreach($parents as $parent){
                    echo "<option value='".$parent->id."'>".$parent->value."</option>";             
                }            
            }
        }
    }
    
    public function actionChangestatus(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new LookupFacade();
            $response = $facade->changeStatus($id, $status);
            return json_encode($response);
        }

    }
    
   

    public function findModel($id)
    {
        if (($model = Lookups::find()->where(['id'=>$id])->one()) !== null) {
            return true;
        } else { 
           return false; 
        }
    }
    
    
    public function actionCheckshortcodeexist(){     
        $request= Yii::$app->request;
        $shortCode=$request->post('slug');
        $count=Lookups::find()->where(['short_code'=>$shortCode])->count();
        if($count>0){
            $shortCode=$shortCode.'_'.rand(1,10);
            return $shortCode;
        }        
    }
    
}
