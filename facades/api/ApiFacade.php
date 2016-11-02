<?php
namespace app\facades\api;


/*
 * @AUTHOR : Prachi
 * @DATE : 29-02-2016
 * @DESCRIPTION: For API
 */

use Yii;
use app\models\Configurations;
use app\models\Devices;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;

class ApiFacade{
    
    private $messages;           //stores an instance of the messages XML file.
    
    public function __construct() {   
         $this->messages = CommonFacade::getMessages();
    }  
    
    /*
     *@author :prachi
     * to get api static key
     */
    public static function getApiKey(){
        $modelConfig=new Configurations();
        $result=  Configurations::find()->where(['short_code'=>'API_STATIC_KEY'])->one();
        return $result;
        
    }
    
    /*
     *@author :prachi
     * to check device id exist
     * need to pass device id
     */
    
    public static function deviceIdExists($deviceId){        
        $result= Devices::find()->where(['device_id'=>$deviceId])->count();
        return $result;
        
    }
    
    public function registerNewDevice($request){
        
        $facade = new CommonFacade;
        $model = new Devices();
        $CODES = new Codes;    //To get Code    
        $model->setAttributes($request);        
        if($model->validate()){  //To Validate Model
                try{
                    if ($model->save()) {               
                        $MSG=$this->messages->M1001; //To get Message
                        $CODE=$CODES::SUCCESS;  
                        return array('STATUS'=>1,'CODE'=>$CODE,'MESSAGE'=>$MSG);
                    } else {                
                        $MSG=$this->messages->M102;
                        $CODE=$CODES::DB_TECH_ERROR;
                        Yii::error($model->getErrors());
                        return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);
                    }
                }//EOF try
                catch (\Exception $e){
                    $MSG=$this->messages->M103;
                    $CODE=$CODES::EXCEPTION_ERROR;
                    Yii::error($e->getMessage());
                    // use $e->getMessage() to write error in log file.
                    return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);
                }//EOF catch                
                
        } else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);            
        }
        
    }
    
    public function updateDeviceToken($request){
        $facade = new CommonFacade;
        $model = new Devices();
        $CODES = new Codes;    //To get Code                
        $model=  $this->findModel($request['device_id']);
        if($model===false){
            return array('STATUS'=>0,'CODE'=>400,'MESSAGE'=>'Bad Request'); 
        }
        
        $model->device_token=$request['device_token'];
        if($model->validate()){  //To Validate Model
                try{
                    if ($model->save()) {               
                        $MSG=$this->messages->M1002; //To get Message
                        $CODE=$CODES::SUCCESS;  
                        $DATA=array();
                        return array('STATUS'=>1,'CODE'=>$CODE,'MESSAGE'=>$MSG,$DATA);
                    } else {                
                        $MSG=$this->messages->M102;
                        $CODE=$CODES::DB_TECH_ERROR;
                        Yii::error($model->getErrors());
                        return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);
}
                }//EOF try
                catch (\yii\base\Exception $e){
                    $MSG=$this->messages->M103;
                    $CODE=$CODES::EXCEPTION_ERROR;
                    Yii::error($e->getMessage());
                    // use $e->getMessage() to write error in log file.
                    return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);
                }//EOF catch                
                
        }else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);            
        }
    }
    
    /**
     * @author: Prachi
     * Finds the  model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     */
    protected function findModel($id)
    {
        if (($model = Devices::find()->where(['device_id'=>$id])->one()) !== null) {
            return $model;
        } else { 
            return false;           
        }
    }
    
    /**
     *@author :prachi
     *to get device data on basis of device id      
     **/
    
    public static function getDeviceData($deviceId){        
        $result= Devices::find()->where(['device_id'=>$deviceId])->one();
        return $result;
        
    }
    
}