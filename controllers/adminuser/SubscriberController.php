<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 29-March-2016
 * @description: Subscriber interacts with facade for all subscriber related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\adminuser\SubscriberFacade;
use app\models\Users;
use app\facades\adminuser\AdminFacade;
use app\models\SubscriberForm;
use app\facades\common\CommonFacade;
use app\modules\user\facades\UserFacade;
use \app\modules\user\models\UserPersonal;
use \app\web\util\Codes\LookupCodes;
use yii\web\UploadedFile;


class SubscriberController extends \yii\web\Controller {
    
    public $enableCsrfValidation = false;
    
     public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','changestatus','create','update','view','delete'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','changestatus','create','update','view','delete'
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
    
    public function actionIndex() {
        $lang = \app\facades\common\CommonFacade::getLanguage();
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        $facade = new SubscriberFacade();
        $response = $facade->listUser(LookupCodes::L_USER_TYPE_SUBSCRIBER);
        $userData = $response['DATA']['SUBDATA'];
        $dateDisplayFormat = CommonFacade::getDisplayDateFormat();
        return $this->render('Index', ['data' => $userData, 'permission'=>$permission,'date_format'=>$dateDisplayFormat, 'lang'=>$lang]);
       
    }
    
    public function actionCreate(){                
            $userFormData = new SubscriberForm();   
            $commonFacade = new CommonFacade();
            $userFacade = new UserFacade();
            if(Yii::$app->request->post()){
              
              $currentTime=$commonFacade->getCurrentDateTime();  
              
              $request['User']['role']=$_POST['SubscriberForm']['role'];
              $request['User']['user_type']=  LookupCodes::L_USER_TYPE_SUBSCRIBER;
            
              $request['UserPersonal']['first_name']=$_POST['SubscriberForm']['first_name'];
              $request['UserPersonal']['last_name']=$_POST['SubscriberForm']['last_name'];
              $request['UserPersonal']['email']=$_POST['SubscriberForm']['email'];
              if($_POST['SubscriberForm']['phone']!=""){
                $request['UserPersonal']['phone']=$_POST['SubscriberForm']['phone'];
              }
              $request['UserPersonal']['gender']=$_POST['SubscriberForm']['gender'];
              $request['UserPersonal']['marital_status']=$_POST['SubscriberForm']['marital_status'];
              
              $request['User']['status']=$_POST['SubscriberForm']['status'];
              $request['User']['is_delete']=1;
              $request['User']['created_on']=$request['User']['modified_on']=$request['UserPersonal']['created_on']=$request['UserPersonal']['modified_on']=$currentTime;
              
              $image = UploadedFile::getInstance($userFormData, 'image_path');   
         
              
              $response=$userFacade->Register($request,$image);  
              
              $msgType="error";
              if($response['STATUS']==1){$msgType="success";
              
                //send mail
               Yii::$app->getSession()->setFlash($msgType, $response['MESSAGE']);             
               return  $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/subscriber/index"));
              
              }
              
              Yii::$app->getSession()->setFlash($msgType, $response['MESSAGE']);             
              return $this->render('Create', ['model' => $userFormData]);    
            }
            else{                       
                $userFormData->status=  LookupCodes::L_USER_STATUS_NON_VERIFIED;
                return $this->render('Create', ['model' => $userFormData]);
    
            }             
               
    }
    
    public function actionUpdate(){                            
            $commonFacade = new CommonFacade();
            $subscriberFacade = new SubscriberFacade();
            $getRequest = Yii::$app->request->get();
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/subscriber"));                     
            } 
            $model = Users::find()->where(['id'=>$getRequest['id']])->one();  
            $modelPersonal = UserPersonal::find()->where(['user_id'=>$getRequest['id']])->one();  
            $modelForm = new SubscriberForm();            
            
            $modelForm->id=$model['id'];
            $modelForm->role=$model['role'];
            $modelForm->status=$model['status'];
            $modelForm->first_name=$modelPersonal['first_name'];            
            $modelForm->last_name=$modelPersonal['last_name'];
            $modelForm->email=$modelPersonal['email'];
            $modelForm->phone=$modelPersonal['phone'];
            $modelForm->gender=$modelPersonal['gender'];
            $modelForm->marital_status=$modelPersonal['marital_status'];
            $modelForm->image_path=$modelPersonal['image_path'];
            
            if(Yii::$app->request->post()){
              
              $currentTime=$commonFacade->getCurrentDateTime();  
              
              $request['User']['id']=$getRequest['id'];
              $request['User']['role']=$_POST['SubscriberForm']['role'];              
              
              $request['UserPersonal']['user_id']=$getRequest['id'];
              $request['UserPersonal']['first_name']=$_POST['SubscriberForm']['first_name'];
              $request['UserPersonal']['last_name']=$_POST['SubscriberForm']['last_name'];                            
              $request['UserPersonal']['gender']=$_POST['SubscriberForm']['gender'];
              $request['UserPersonal']['marital_status']=$_POST['SubscriberForm']['marital_status'];
              
              $request['User']['status']=$_POST['SubscriberForm']['status'];              
              $request['User']['modified_on']=$request['UserPersonal']['modified_on']=$currentTime;
              
              $image = UploadedFile::getInstance($modelForm, 'image_path');               
              
              $response=$subscriberFacade->setProfile($request,$image);  
            
              
              $msgType="error";
              if($response['STATUS']==1){
                  
              $msgType="success";
              $userData= $response['DATA'];
              //$modelForm->status=$userData['status'];
              //$modelForm->marital_status=$userData['marital_status'];
              //$modelForm->setAttributes($userData); 
              Yii::$app->getSession()->setFlash($msgType, $response['MESSAGE']);
              return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/subscriber/index"));
              }              
              
              Yii::$app->getSession()->setFlash($msgType, $response['MESSAGE']);
              
              return $this->render('Create', ['model' => $modelForm]);    
            }
            else{                              
                return $this->render('Create', ['model' => $modelForm]);
    
            }             
               
    }
    
    public function actionView(){
        
            $userFacade = new UserFacade();          
            $getRequest = Yii::$app->request->get();     
            if($this->findModel($getRequest['id'])===false){
                return $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/subscriber"));                     
            }  
            
            $request['userId']=$getRequest['id'];            
            $response=$userFacade->getProfile($request);
            if($response['STATUS']==0){
                 Yii::$app->getSession()->setFlash('error', $response['MESSAGE']);                  
            }
            $result=Users::find()->where(['id'=>$request['userId']])->one();           
            $response['DATA']['id']=$request['userId'];
            $response['DATA']['role']=$response['DATA']['status']="";
            if($result->role!=null){$response['DATA']['role']=$result->role0->value;}
            if($result->status!=null){$response['DATA']['status']=$result->status0->value;}            
            $userData=(object)$response['DATA'];            
            return $this->render('View', ['model' => $userData]);    
       
    }
    
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new SubscriberFacade();
            $response = $facade->delete($id);
            return json_encode($response);
        }        
    }
    
    public function actionChangestatus(){
        $request = Yii::$app->request;       
        $id = $request->post('id');
        $status = $request->post('status');
        if($id){ 
            $facade = new SubscriberFacade();
            $response = $facade->changeStatus($id, $status);
            return json_encode($response);
        }

    }
    
    public function findModel($id)
    {
        if (($model = UserPersonal::find()->where(['user_id'=>$id])->one()) !== null) {
            return true;
        } else { 
           return false; 
        }
    }
    
    
    public function actionExport() {
        
        $vendorDir =   \Yii::getAlias('@app').'/vendor/yiisoft/PHPExcel_1.8.0_doc/Classes';
        require_once $vendorDir.'/PHPExcel.php';
        
        
        $subList = \app\models\UserPersonalDetails::find()->orderBy(['id'=>SORT_DESC])->where([])->all();
        
        
        $objPHPExcel = new \PHPExcel();
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Email')
                    ->setCellValue('B1', 'Country')
                    ->setCellValue('C1', 'Status')
                    ->setCellValue('D1', 'Registered On');
        
        

        $celArr = array('A', 'B', 'C', 'D');
        
        foreach($celArr as $key=>$cell){
            $objPHPExcel->getActiveSheet()->getStyle($cell.'1')->getFill()->applyFromArray(array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                     'rgb' => 'FF5733  
'
                )
            ));
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
            
        }
        
        $i = 1;
        foreach($subList as $sub){
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A$i", $sub->email)
                    ->setCellValue("B$i", $sub->country0->value)
                    ->setCellValue("C$i", $sub->user->status0->value)
                    ->setCellValue("D$i", date("j M Y, h:i:s", strtotime($sub->created_on) ));
        }
        

        $objPHPExcel->getActiveSheet()->setTitle('Subscriber List');

        $objPHPExcel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Subscribers.xls"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');

        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        exit;
    }
    
    
    
}
?>
