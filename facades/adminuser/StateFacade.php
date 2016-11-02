<?php

namespace app\facades\adminuser;

/**
 * @author: Prachi
 * @date: 16-March-2016
 * @description: stateFacade interacts with models for all state related activities
 */

use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use app\models\States;


class StateFacade {

    public $messages;           //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function listStates(){
            $CODES = new Codes();   
            $model= new States(); 
            $data=States::find()->select(['id','value','country_id','status'])->where(['is_delete'=>1])
                    ->all();
            if (!empty($data)) {
                $MSG ="";
                $CODE = $CODES::SUCCESS;
                $data = array('STATUS' => 'success', 'SUBDATA' => $data);
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);                
            } else {                
                $MSG = $this->messages->M121;
                $CODE = $CODES::VALIDATION_ERROR;
                $data = array('STATUS' => 'error', 'SUBDATA' => array());
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
            }
          
    }
    
    public function add_modify($request){
          $CODES = new Codes();
          $commonFacade= new CommonFacade();
          $model= new States(); 
          
          $currentTime=$commonFacade->getCurrentDateTime();
        
           if($request['id']!=""){ //edit              
              $model = States::find()->where(['id'=>$request['id']])->one();                
              $response="updated";
          }else{    //add              
              $request['created_by']=Yii::$app->admin->adminId;
              $request['created_on']=$currentTime;
              $request['is_delete']='1';              
              $response="added";
          } 
              $request['modified_by'] =  Yii::$app->admin->adminId;
              $request['modified_on'] = $currentTime; 
              
              $model->setAttributes($request); 
          
          if($model->validate()){  //To Validate Model
                try{
                    if ($model->save()) {                           
                        $MSG=$this->messages->M113; //To get Message
                        $MSG= str_replace("{1}","State", $MSG);
                        $MSG= str_replace("{2}",$response, $MSG);
                        $CODE=$CODES::SUCCESS;  
                        $data = array('STATUS' => 'success', 'SUBDATA' => array());
                        return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
                        
                    } else {                
                        $MSG=$this->messages->M102;
                        $CODE=$CODES::DB_TECH_ERROR;
                        Yii::error($model->getErrors());
                        $data = array('STATUS' => 'error', 'SUBDATA' => array());
                        return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
                    }
                }//EOF try
                catch (\yii\base\Exception $e){
                    $MSG=$this->messages->M103;
                    $CODE=$CODES::EXCEPTION_ERROR;
                    Yii::error($e->getMessage());
                    // use $e->getMessage() to write error in log file.
                    $data = array('STATUS' => 'error', 'SUBDATA' => array());
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
                }//EOF catch                
                
        } else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            $data = array('STATUS' => 'error', 'SUBDATA' => array());
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        }
          
          
    }
    
    public function viewType($id){
            $CODES = new Codes();
            $data=States::find()->select(['id','value','country_id','status'])->where(['id'=>$id])->one();  
            if (!empty($data)) {
                $MSG ="";
                $CODE = $CODES::SUCCESS;
                $data = array('STATUS' => 'success', 'SUBDATA' => $data);
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);                
            } else {                
                $MSG = $this->messages->M121;
                $CODE = $CODES::VALIDATION_ERROR;
                $data = array('STATUS' => 'error', 'SUBDATA' => array());
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
            }
          
    }
    
    public function delete($id){
        $CODES = new Codes();  
        $commonFacade= new CommonFacade();
        $model = States::find()->where(['id'=>$id])->one(); 
        $value=$model->value;
        $model->is_delete=0;
       /* if($model->validate()){  //To Validate Model
            try{
        * 
        */


                if ($model->save(false)) {                                                   
                    $MSG=$this->messages->M113; //To get Message
                    $MSG= str_replace("{1}",$value, $MSG);
                    $MSG= str_replace("{2}","deleted", $MSG);
                    $CODE=$CODES::SUCCESS;  
                    $data = array('STATUS' => 'success', 'SUBDATA' => array());
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);

                } else {                
                    $MSG=$this->messages->M102;
                    $CODE=$CODES::DB_TECH_ERROR;
                    Yii::error($model->getErrors());
                    $data = array('STATUS' => 'error', 'SUBDATA' => array());
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
                }
            /*}//EOF try
            catch (Exception $e){
                $MSG=$this->messages->M103;
                $CODE=$CODES::EXCEPTION_ERROR;
                // use $e->getMessage() to write error in log file.
                $data = array('STATUS' => 'error', 'SUBDATA' => array());
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
            }//EOF catch      
        } else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            $data = array('STATUS' => 'error', 'SUBDATA' => array());
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        }
             * 
             */
    }
    
    
    public function changeStatus($id,$status){        
        $CODES = new Codes;   
        $commonFacade= new CommonFacade();
        $currentTime=$commonFacade->getCurrentDateTime();        
        if($id != ''){
            $model = States::find()->where(['id'=>$id])->one();
            if($model){
                $model->status = $status;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = $currentTime;

                if($model->save(false)){
                    $MSG = $this->messages->M119;
                    $CODE = $CODES::SUCCESS;
                    $DATA = $model->status0->value;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>$DATA);
                } else {
                    $MSG = $this->messages->M103;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $status);
                }
            }
            
        }
    }

    
}