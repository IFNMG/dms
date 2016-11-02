<?php

namespace app\facades\adminuser;

/*
 * @author: Prachi
 * @date: 29-March-2016
 * @description: SubscriberFacade interacts with models for all basic user related activities Ex:Register
 */

use Yii;
use app\models\Users;
use app\modules\user\models\UserPersonal;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use app\web\util\Codes\LookupCodes;



class SubscriberFacade {

    public $messages;           //stores an instance of the messages XML file.
    
    public function __construct() {
    $this->messages = CommonFacade::getMessages();
    }


    
    public function listUser($user_type) {
        
        $CODES = new Codes;    //To get Code
        $model = new Users();
        $modelPersonal = new UserPersonal();
        $CODES = new Codes;
        
        
        $userdata = Users::find()->where(['user_type'=>$user_type])->orderBy(['id'=>SORT_DESC])->all();
       
        if ($userdata) {

                $MSG = '';
                $CODE = $CODES::SUCCESS;

                $data = array('STATUS' => 'success', 'SUBDATA' => $userdata);
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
                
            } else {
                $MSG = $this->messages->M121;
                $CODE = $CODES::ERROR;

                $data = array('STATUS' => 'error', 'SUBDATA' => array());
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
            }
        
    }
    
    


    public function changeStatus($id,$status){        
        $CODES = new Codes;   
        $commonFacade= new CommonFacade();
        $currentTime=$commonFacade->getCurrentDateTime();        
        if($id != ''){
            $model = Users::find()->where(['id'=>$id])->one();
            if($model){
                
                if($status==LookupCodes::L_USER_STATUS_NON_VERIFIED && ($model->status==LookupCodes::L_USER_STATUS_VERIFIED || $model->status==LookupCodes::L_USER_STATUS_BLOCKED)){
                    $MSG = $this->messages->M130;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $status);
                }
                
                $model->status = $status;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = $currentTime;               
                if($model->save()){
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
    
    public function viewType($id){
            $CODES = new Codes();
            $data=  Users::findAll(['id'=>$id,'is_delete'=>1]);      
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
    
    
   /**
     * @author: prachi
     * set user profile
     */
    public function setProfile($request,$image=array()){
        
        $facade = new CommonFacade;        
        $CODES = new Codes;    //To get Code           
        $oldImg="";
        $model = Users::find()->where(['id'=>$request['User']['id']])->one(); 
        $modelPersonal = UserPersonal::find()->where(['user_id'=>$request['User']['id']])->one(); 
        $oldImg=$modelPersonal->image_path;
        
        $model->role=$request['User']['role'];
        $model->status=$request['User']['status'];
        $model->modified_on=$request['User']['modified_on'];
        $model->modified_by=Yii::$app->admin->adminId;   
        
        $modelPersonal->first_name=$request['UserPersonal']['first_name'];
        $modelPersonal->last_name=$request['UserPersonal']['last_name'];
        $modelPersonal->gender=$request['UserPersonal']['gender'];
        $modelPersonal->marital_status=$request['UserPersonal']['marital_status'];        
        $modelPersonal->modified_on=$request['UserPersonal']['modified_on'];
        $modelPersonal->modified_by=Yii::$app->admin->adminId;
        
        $path=$save_path="";
        if(!empty($image)){
         // store the source file name            
          $ext = end((explode(".", $image->name)));

          // generate a unique file name
          $avatar = Yii::$app->security->generateRandomString().".{$ext}";

          // the path to save file, you can set an uploadPath           
          $path = Yii::$app->params['UPLOAD_PATH'].'subscriber/profile/' . $avatar;
          $save_path="subscriber/profile/".$avatar;
          $modelPersonal->image_path=$save_path;
        }
        if($save_path!=""){
               $modelPersonal->image_path=$save_path;
        if($oldImg!="" && file_exists(Yii::$app->params['UPLOAD_PATH'].$oldImg)){
            unlink(Yii::$app->params['UPLOAD_PATH'].$oldImg);
        }
        }        
        
        if($model->validate()){  //To Validate Model
            try{
                if ($model->save()) {  
                    $modelPersonal->save();
                    if($path!=""){$image->saveAs($path);}
                    $modelUpdated = Users::find()->where(['id'=>$request['User']['id']])->one(); 
                    $modelPersonalUpdated = UserPersonal::find()->where(['user_id'=>$request['User']['id']])->one(); 
                    $DATA['first_name']=$modelPersonalUpdated->first_name;
                    $DATA['last_name']=$modelPersonalUpdated->last_name;
                    $DATA['gender']=$modelPersonalUpdated->gender;                    
                    $DATA['marital_status']=$modelPersonalUpdated->marital_status;
                    $DATA['email']=$modelPersonalUpdated->email;
                    $DATA['phone']=$modelPersonalUpdated->phone;
                    $DATA['role']=$modelUpdated->role;
                    $DATA['status']=$modelUpdated->status;
                    $DATA['image_path']=$modelPersonalUpdated->image_path;                    
                    $MSG=$this->messages->M113; //To get Message
                    $MSG=  str_replace('{1}', 'Profile', $MSG);
                    $MSG=  str_replace('{2}', 'updated', $MSG);
                    $CODE=$CODES::SUCCESS;                      
                    return array('STATUS'=>1,'CODE'=>$CODE,'MESSAGE'=>$MSG,'DATA'=>$DATA);
              
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
  
    
     public function delete($id){
        $CODES = new Codes();  
        $commonFacade= new CommonFacade();
        $model = Users::find()->where(['id'=>$id])->one(); 
        $modelPersonal = UserPersonal::find()->where(['user_id'=>$id])->one(); 
        $value=$modelPersonal->email;
        $model->is_delete=0;
        $modelPersonal->is_delete=0;
        if($model->validate()){  //To Validate Model
                try{
                    
                    if ($model->save()) {  
                        $modelPersonal->save();
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
}
