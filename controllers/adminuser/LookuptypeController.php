<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 16-March-2016
 * @description: LookupTypeController interacts with facade for all lookuptype related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\LookuptypeFacade;
use app\models\LookupTypes;



class LookuptypeController extends \yii\web\Controller {
    
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
            $facade = new LookuptypeFacade();
            $response = $facade->listLookupType();
            $userData = $response['DATA']['SUBDATA'];
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
            return $this->render('Index', ['data' => $userData,'permission'=>$permission, 'lang'=>$lang]);
    }
    
    public function actionCreate(){        
         
            $facade = new LookuptypeFacade();
            $model = new LookupTypes();
           
            if(Yii::$app->request->post()){
              $request=$_POST['LookupTypes'];
              $response=$facade->add_modify($request);
              
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);             
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookuptype/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{
                $model->type_of_lookup_type=0;
                $model->sync_to_mobile=0;
                $model->status=1;
                return $this->render('Create', ['model' => $model]);
            }             
            
    }
    
    
    public function actionUpdate(){
      
            $facade = new LookuptypeFacade();            
            $getRequest = Yii::$app->request->get();   
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookuptype"));                     
            } 
            $model = LookupTypes::find()->where(['id'=>$getRequest['id']])->one();           
            
            if(Yii::$app->request->post()){                
              $request=$_POST['LookupTypes'];
              $response=$facade->add_modify($request);
              $model = LookupTypes::find()->where(['id'=>$getRequest['id']])->one();           
              Yii::$app->getSession()->setFlash($response['DATA']['STATUS'], $response['MESSAGE']);      
              if($response['CODE']==200){
                   return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookuptype/index"));
              } 
              return $this->render('Create', ['model' => $model]);    
            }
            else{                
                return $this->render('Create', ['model' => $model]);
            }             
           
    }
    
    
    public function actionView(){
      
            $facade = new LookuptypeFacade();            
            $getRequest = Yii::$app->request->get();
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/lookuptype"));                     
            } 
            $response=$facade->viewType($getRequest['id']);
            $userData = $response['DATA']['SUBDATA'];
            
            //$model = LookupTypes::find()->where(['id'=>$getRequest['id']])->one();  
            return $this->render('View', ['model' => $userData[0]]);    
        
    }
    
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new LookuptypeFacade();
            $response = $facade->delete($id);
            return json_encode($response);
        }        
    }
    
    public function actionChangestatus(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){
            $facade = new LookuptypeFacade();
            $response = $facade->changeStatus($id, $status);
            return json_encode($response);
        }

    }
    
    public function findModel($id)
    {
        if (($model = LookupTypes::find()->where(['id'=>$id])->one()) !== null) {
            return true;
        } else { 
           return false; 
        }
    }
    
    public function actionCheckshortcodeexist(){     
        $request= Yii::$app->request;
        $shortCode=$request->post('slug');
        $count=LookupTypes::find()->where(['short_code'=>$shortCode])->count();
        if($count>0){
            $shortCode=$shortCode.'_'.rand(1,10);
            return $shortCode;
        }        
    }
    
}
