<?php

/*
 * @author: Prachi
 * @date: 08-March-2016
 * @description: UserFacade interacts with models for all basic subscriber related activities Ex:Login,Register
 */

namespace app\modules\user\facades;

use Yii;
use app\models\Users;
use app\modules\user\models\UserPersonal;

use app\facades\common\CommonFacade;
use \app\web\util\Codes\Codes;
use \app\models\LoginForm;
use \app\models\ForgotpasswordForm;
use \app\facades\request\Status;
use \app\facades\common\MailFacade;
use \app\models\SystemTokens;
use \app\models\PasswordHistory;
use \app\models\ChangepasswordForm;
use \yii\base\Exception;
use \app\models\Lookups;
use app\web\util\Codes\LookupCodes;


Class UserFacade {
    /*
     * Register function
     * Handles registration function
     */
    private $messages="";
    private $globalUserId="";
    //public  $apiUserId="";
   // public  $isGuestApiUser="";


    public function __construct() {        
        $this->messages = CommonFacade::getMessages();
       
        /*
        $this->apiUserId="";
        if($this->apiUserId!=""){
             $this->isGuestApiUser=0;
        }else{
         $this->isGuestApiUser=1;
        }        
        */
    }
   

    /**
     * @author: prachi
     * @set user registration
     */
      
    public function Register($request,$image=array()) {
        $auto_id="";
        $model = new Users();
        $modelPersonal = new \app\models\UserPersonalDetails(); 
        
        $facade = new CommonFacade;        
        $CODES = new Codes;  
      
        
        $model->attributes = $request['User'];
        $modelPersonal->attributes=$request['UserPersonal'];   
        if($modelPersonal->phone==""){ 
            $modelPersonal->phone=NULL;
        }
        
        $path = $save_path = "";
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
        
        $transaction = Yii::$app->db->beginTransaction();
        try  {
            if ($model->save()) {                
                $user_id=$model->getPrimaryKey();
                $modelPersonal->user_id=$user_id;
                $modelPersonal->created_by=$user_id;
                $modelPersonal->modified_by=$user_id;
                if($modelPersonal->save()){
                    //update User table for create_by and modified_by field
                    $model->created_by=$user_id;
                    $model->modified_by=$user_id;
                    $model->save();                   
                    $transaction->commit();
                    
                    
                    $pass = md5(md5($request['User']['password']));
                    Users::updateAll(['password' =>$pass], "id = $model->id");
                    
                    
                    if($path!=""){$image->saveAs($path);}
                    $STATUS=1;
                    $CODE=$CODES::SUCCESS;
                    $MSG=  $this->messages->M101;   
                    $MSG=str_replace("{1}", $modelPersonal->email, $MSG);
                    $auto_id=$user_id;
                    
                    
                    
                    $token = Yii::$app->security->generatePasswordHash(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?"), 0, 10));
                    $token.= $user_id;
                    
                    //////////// FOR USER VERIFICATION ///////////////////
                    $systemToken = new SystemTokens();
                    $systemToken->type = LookupCodes::L_SYSTEM_OPERATIONS_REGISTRATION_EMAIL;
                    $systemToken->value = $token;
                    $systemToken->creation_date_time = date('Y-m-d H:i:s');
                    //$systemToken->expiration_date_time = date('Y-m-d h:i:s', strtotime(date('Y-m-d H:i:s'). " + $mins minutes"));
                    $systemToken->expiration_date_time = date('Y-m-d H:i:s');
                    $systemToken->user_id = $user_id; 
                    $systemToken->status = LookupCodes::L_COMMON_STATUS_ENABLED;        
                    $systemToken->is_delete = 1;      
                    $systemToken->created_on = date('Y-m-d H:i:s');
                    $systemToken->created_by = $user_id;      
                    $systemToken->modified_on = date('Y-m-d H:i:s');
                    $systemToken->modified_by = $user_id;
                    
                    $link = Yii::$app->urlManager->getHostInfo().Yii::$app->urlManager->createUrl("index.php/backend/backend/verify?id=".$token);
                    if($systemToken->save()){
                        $emailObj = array('LINK'=>$link, 'USER.FIRSTNAME'=>$modelPersonal->first_name, 'USER.LASTNAME'=>$modelPersonal->last_name, 'SUBSCRIBER.FIRSTNAME'=>$modelPersonal->first_name, 'SUBSCRIBER.LASTNAME'=>$modelPersonal->last_name);
                        $eventId = LookupCodes::L_EMAIL_TEMPLATES_REGISTRATION_MAIL;
                        $langId = LookupCodes::L_LANGUGAGE_ENGLISH;
                        $recepient = $modelPersonal->email;
                        $mailfacade = new MailFacade();
                        $mail = $mailfacade->sendEmail($emailObj, $recepient, $eventId, $langId);
                        if($mail){
                            $STATUS = 1;
                            $CODE = $CODES::SUCCESS;
                            $MSG = $this->messages->M101;   
                            $MSG=str_replace("{1}", $modelPersonal->email, $MSG);
                        }
                    } else {
                        $STATUS = 0;
                        $CODE = $CODES::ERROR;
                        $MSG = $systemToken->getErrors();
                    }
                    //////////// FOR USER VERIFICATION ///////////////////
                }else {
                    $transaction->rollBack();
                    $errorAll=array_merge(array_values($model->firstErrors),array_values($modelPersonal->firstErrors));
                    $error = $errorAll;  
                    Yii::error($error);
                    $MSG=$error[0];
                    $CODE=$CODES::VALIDATION_ERROR;
                    $STATUS=0;
                }
            }else {
                    $transaction->rollBack();
                    $errorAll=array_merge(array_values($model->firstErrors),array_values($modelPersonal->firstErrors));
                    $error = $errorAll;
                    Yii::error($error);
                    $MSG=$error[0];
                    $CODE=$CODES::VALIDATION_ERROR;
                    $STATUS=0;
            }
        } catch (Exception $e) {
            
            $transaction->rollBack();            
            Yii::error($e->getMessage());
            $errorAll=array_merge(array_values($model->firstErrors),array_values($modelPersonal->firstErrors));            
            $error = $errorAll;            
            $MSG=$error[0];
            $CODE=$CODES::VALIDATION_ERROR;
            $STATUS=0;
        }        
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG,'AUTO_ID'=>$auto_id);    
        
    }
    
    
    /**
    *  function for login
    * @author Waseem
    **/
    public function login($request){
        $CODES = new Codes;    //To get Code
        $DATA = array();
        
        $username = $request['username'];
        $password = $request['password'];
        $deviceId = $request['deviceId'];
        
        $login = new LoginForm();
        $login->username = $username;
        $login->password = $password;
        
        if($login->validate()){
            $user = UserPersonal::find()->where(['email'=>$username])->orWhere(['phone' =>$username])->one();
            if($user){
                $User = Users::find()->select(['password','id','is_delete', 'status'])->where(['id'=>$user->user_id,'user_type'=>  LookupCodes::L_USER_TYPE_SUBSCRIBER])->one();
                
                if($User->status == LookupCodes::L_USER_STATUS_NON_VERIFIED){
                    $STATUS = 0;
                    $CODE = $CODES::VALIDATION_ERROR;
                    $MSG = $this->messages->M136;
                } else if ($User->is_delete == 0) {
                    $STATUS = 0;
                    $CODE = $CODES::VALIDATION_ERROR;
                    $MSG = $this->messages->M111;
                } else {
                    if(empty($User)){
                         $STATUS = 0;
                         $CODE = $CODES::ERROR;
                         $MSG = $this->messages->M104;
                        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA); 
                    }                   
                    
                    if(md5(md5($password)) != $User->password){
                        $STATUS = 0;
                        $CODE = $CODES::VALIDATION_ERROR;
                        $MSG = $this->messages->M104;
                    } else {
                        
                        $user_id=$User->id;
                        
                        $this->embedUserToDevice($deviceId,$user_id);
                        $token = CommonFacade::swapDeviceAuthorizedTokens($deviceId,$user_id);
                        
                        
                        
                        $STATUS = 1;
                        $CODE = $CODES::SUCCESS;
                        $MSG = $this->messages->M108;
                        $DATA['API_CURRENT_TOKEN'] = $token;         
                    }
                }
            } else {
                $STATUS = 0;
                $CODE=$CODES::VALIDATION_ERROR;
                $MSG = $this->messages->M110;
            }
        } else {
            $STATUS = 0;
            $CODE=$CODES::VALIDATION_ERROR;
            //$MSG = $login->getErrors();
            $errorAll = array_merge(array_values($login->firstErrors));            
            $error = $errorAll;            
            $MSG=$error[0];
        }
        
        
        
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA); 
        
    }
    
    
    /*
     * function for forgot password
     * @author: Waseem
     */
    public function forgotPassword($request){
        
        $CODES = new Codes;    //To get Code
        
        $model = new ForgotpasswordForm();
        $model->email = $request['email'];
        $model->messages = $this->messages;
        
            if($model->validate()){
            $user = UserPersonal::find()->where(['email'=>$model->email])->one();
            if($user){
                    $userId = $user->user_id;
                    $config = \app\models\Configurations::find()->select(['value'])->where(['short_code'=>'FORGOT_PASSWORD_EXPIRY_MINUTES'])->one();
                    if($config){
                        $mins = $config->value;
                    } else {
                        $mins = 15;
                    }
                    
                    $data['id'] = $userId;
                    $data['validTill'] = strtotime("+$mins minutes", strtotime(date('Y-m-d H:i:s')));

                    $facade = new CommonFacade();
                    $forgotKey = $facade->encryptToken($data);
                    $token = urlencode($forgotKey);
                    
                    $token1 = ($forgotKey);
                    
                    $systemToken = new SystemTokens();
                    $systemToken->type = LookupCodes::L_SYSTEM_OPERATIONS_FORGOT_PASSWORD;
                    $systemToken->value = $token1;
                    $systemToken->creation_date_time = date('Y-m-d H:i:s');
                    $systemToken->expiration_date_time = date('Y-m-d h:i:s', strtotime(date('Y-m-d H:i:s'). " + $mins minutes"));
                    $systemToken->user_id = $userId; 
                    $systemToken->status = LookupCodes::L_COMMON_STATUS_ENABLED;        
                    $systemToken->is_delete = 1;      
                    $systemToken->created_on = date('Y-m-d H:i:s');
                    $systemToken->created_by = $userId;      
                    $systemToken->modified_on = date('Y-m-d H:i:s');
                    $systemToken->modified_by = $userId;
                    
                    $link = Yii::$app->urlManager->getHostInfo().Yii::$app->urlManager->createUrl("index.php/adminuser/admin/resetpassword?id=".$token);

                    if($systemToken->save()){
                        $emailObj = array('LINK'=>$link, 'USER.FIRSTNAME'=>$user->first_name, 'USER.LASTNAME'=>$user->last_name, 'SUBSCRIBER.FIRSTNAME'=>$user->first_name, 'SUBSCRIBER.LASTNAME'=>$user->last_name);
                        $eventId = LookupCodes::L_EMAIL_TEMPLATES_FORGOT_PASSWORD_MAIL;
                        $langId = LookupCodes::L_LANGUGAGE_ENGLISH;
                        
                        $recepient = $model->email;
                        $mailfacade = new MailFacade();
                        $mail = $mailfacade->sendEmail($emailObj, $recepient, $eventId, $langId);
                        if($mail){
                            $STATUS = 1;
                            $CODE = $CODES::SUCCESS;
                            $MSG = $this->messages->M112;
                        }
                    } else {
                        $STATUS = 0;
                        $CODE = $CODES::ERROR;
                        $MSG = $systemToken->getErrors();
                    }
            } else {
                $STATUS = 0;
                $CODE = $CODES::ERROR;
                $MSG = $this->messages->M110;
            }
        } else {
            $STATUS = 0;
            $CODE = $CODES::ERROR;
            //$MSG = $model->getErrors();
            
            $errorAll = array_merge(array_values($model->firstErrors));            
            $error = $errorAll;            
            $MSG=$error[0];
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG);
    }
    
    /**
     * @author: prachi
     * set user profile
     */
    public function setProfile($request){
        $facade = new CommonFacade;        
        $CODES = new Codes;    //To get Code           
        
        $model = $this->findModel($request['UserPersonal']['user_id']);   
        if($model===false){
            return array('STATUS'=>0,'CODE'=>400,'MESSAGE'=>'Bad Request'); 
        }
        
        $model->first_name=$request['UserPersonal']['first_name'];
        $model->last_name=$request['UserPersonal']['last_name'];
        $model->gender=$request['UserPersonal']['gender'];
        $model->marital_status=$request['UserPersonal']['marital_status'];
        $model->modified_on=$request['UserPersonal']['modified_on'];
        $model->modified_by=$request['UserPersonal']['modified_by'];        
        
        if($model->validate()){  //To Validate Model
            try{
                if ($model->save()) {                                     
                    $modelUpdated = $this->findModel($request['UserPersonal']['user_id']); 
                    $DATA['first_name']=$modelUpdated->first_name;
                    $DATA['last_name']=$modelUpdated->last_name;
                    $DATA['gender']=$modelUpdated->gender;
                    $DATA['gender_value']=$facade->getLookupDataById($modelUpdated->gender);
                    $DATA['marital_status']=$modelUpdated->marital_status;                
                    $DATA['marital_status_value']=$facade->getLookupDataById($modelUpdated->marital_status);
                    $MSG=$this->messages->M113; //To get Message
                    $MSG=  str_replace('{1}', 'Your profile', $MSG);
                    $MSG=  str_replace('{2}', 'updated', $MSG);
                    $CODE=$CODES::SUCCESS;                      
                    return array('STATUS'=>1,'CODE'=>$CODE,'MESSAGE'=>$MSG,'DATA'=>$DATA);
              
                } else {  
                    $MSG=$this->messages->M102;
                    $CODE=$CODES::DB_TECH_ERROR;
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
                
        }else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            return array('STATUS'=>0,'CODE'=>$CODE,'MESSAGE'=>$MSG);            
        }
    }
    
    /**
     * @author :prachi
     * Finds the  model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     */
    protected function findModel($id)
    {
        if (($model = UserPersonal::find()->where(['user_id'=>$id])->one()) !== null) {
            return $model;
        } else { 
            return false;           
        }
    }
     /*
     * function for change password
     * @author: Waseem
     */
    public function Changepassword($data) {
        $CODES = new Codes; 
        $changePassword = new ChangepasswordForm();
        $changePassword->setAttributes($data);
        if ($changePassword->validate()) {
            $user = Users::find()->Where(['id' =>$data['userId'], 'password' => md5(md5($data['oldPassword']))])->one();
            if($user){
                $history = new PasswordHistory();
                $history->user_id = $user->id;
                $history->value = $user->password;
                $history->type_of_operation = LookupCodes::L_SYSTEM_OPERATIONS_CHANGE_PASSWORD;
                $history->is_delete = 1;
                $history->created_on = date('Y-m-d h:i:s');
                $history->created_by = $user->id;
                $history->modified_on = date('Y-m-d h:i:s');
                $history->modified_by = $user->id;

                if($history->save()) {
                    $newPassword = md5(md5($data['newPassword']));
                    $user->setAttribute('password', $newPassword);
                    if ($user->save(false)) {
                        $STATUS = 1;
                        $MSG = $this->messages->M106;
                        $CODE = $CODES::SUCCESS;
                    } else {
                        $error = array_values($user->firstErrors);
                        $MSG = $error[0];
                        $CODE = $CODES::DB_TECH_ERROR;
                    }
                } else {
                    $error = array_values($history->firstErrors);
                    $MSG = $error[0];
                    $CODE = $CODES::DB_TECH_ERROR;
                    $STATUS = 0;
                }
            } else {
                $STATUS = 0;
                $CODE = $CODES::VALIDATION_ERROR;
                $MSG = $this->messages->M105;
            }
        } else {
            $STATUS = 0;
            $CODE = $CODES::VALIDATION_ERROR;
            //$MSG = $changePassword->getErrors();
            /**
             * @change : by prachi throw one error at a time
             */
            $error = array_values($changePassword->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}                   
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG);
    }
    
    
     /*
     * function for getting user profile info
     * @author: Waseem
     */
    public function getProfile($data) {
        $CODES = new Codes; 
        
        $userId  = $data['userId'];
        $user = UserPersonal::find()->Where(['user_id' =>$userId])->one();
        if($user) {
            $gender = '';
            if($user->gender){
                $gender = CommonFacade::getLookupDataById($user->gender);
            }
            
            $maritalStatus = '';
            if($user->marital_status){
                $maritalStatus = CommonFacade::getLookupDataById($user->marital_status);
            }
            
            //add 4 cols by prachi
            $country = '';
            if($user->country){
                $countryModel = \app\models\Countries::find('value')->where(['id'=>$user->country,'is_delete'=>1])->one();
                if ($countryModel) {
                $country = $countryModel->value;
                }
            }
            
            $state = '';
            if($user->state){
                $stateModel = \app\models\States::find('value')->where(['id'=>$user->state,'is_delete'=>1])->one();
                if ($stateModel) {
                $state = $stateModel->value;
                }
            }
            
            $city = '';
            if($user->city){
                $cityModel = \app\models\Cities::find('value')->where(['id'=>$user->city,'is_delete'=>1])->one();
                if ($cityModel) {
                $city = $cityModel->value;
                }
            }
            
            $image_path = '';
            if($user->image_path){
               $image_path=Yii::$app->params['UPLOAD_URL'].$user->image_path;                
            }
            
            $DATA = array(
                'first_name'=>$user->first_name,
                'last_name'=>$user->last_name,
                'email'=>$user->email,
                'phone'=>$user->phone,
                'gender'=>$gender,
                'marital_status'=>$maritalStatus,
                'address'=>$user->address,
                'country'=>$country,
                'state'=>$state,
                'city'=>$city,
                'image_path'=>$image_path
            );
            
            $STATUS = 1;
            $CODE = $CODES::SUCCESS;
            /**
             * @change prev $MSG = $this->messages->M113;
             */
            $MSG = "";
        } else {
            $STATUS = 0;
            $CODE = $CODES::ERROR;
            $MSG = $this->messages->M111;
            $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);
    }
    
    public function logout($device_id) {
        $modelDevice = \app\models\Devices::find()->where(['device_id' => $device_id])->one();
        
        $modelDevice->user_id =NULL;
        $modelDevice->previous_token  = NULL;
        $modelDevice->current_token = NULL;
        $modelDevice->modified_on = CommonFacade::getCurrentDateTime();        
        $modelDevice->current_token= CommonFacade::setDeviceCurrentToken($device_id);
        Yii::error('TokenA1A'.$modelDevice->current_token);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($modelDevice->save()) {
                Yii::error('TokenB1B'.$modelDevice->current_token);
                $transaction->commit();
                $MSG = $this->messages->M124;
                return array('STATUS'=>1,'CODE'=>  Codes::SUCCESS,'MESSAGE'=>$MSG);                
            } else {
                $transaction->rollBack();                   
                Yii::error($modelDevice->getErrors());                    
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());                    
        }
        $MSG = $this->messages->M125;
        return array('STATUS'=>0,'CODE'=>  Codes::ERROR,'MESSAGE'=>$MSG);
    }
    
    /**
     *@AUTHOR:Prachi
     * @DATE:06-04-2016
     * @REASON:..Embed User to device
     * allow user to loggedin on multiple devices or not
     */
    public function embedUserToDevice($deviceId,$user_id){        
        $value=CommonFacade::getLookupValueFromConfig(Yii::$app->params['configurations']['MULTI_DEVICE_LOGIN']);
        
        //if($value=='FALSE'){
        if(Yii::$app->params['configurations']['MULTI_DEVICE_LOGIN']==LookupCodes::L_MULTIPLE_DEVICE_OPTIONS_FALSE){
            //unembed user from all devices           
            \app\models\Devices::updateAll(['modified_by'=>$user_id,'modified_on'=>  CommonFacade::getCurrentDateTime(),'user_id' => NULL, 'current_token' => CommonFacade::setDeviceCurrentToken($deviceId), 'previous_token' => NULL], "user_id = $user_id");                      
        }
        
        
        $modelDevice = \app\models\Devices::find()->where(['device_id' => $deviceId])->one();
        $modelDevice->user_id = $user_id;
        $modelDevice->modified_by = $user_id;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($modelDevice->save()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
                Yii::error($modelDevice->getErrors());                    
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($modelDevice->getErrors());               
        }
       
        return true;
    }
    
      /*
     * function for getting CMS pages list
     * @author: Waseem
     */
    public function getCmsPagesList() {
        $CODES = new Codes; 
        $list = \app\models\Pages::find()->Where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->all();
        if($list) {
            $STATUS = 1;
            $CODE = $CODES::SUCCESS;
            $MSG = "";
            $DATA = $list;
        } else {
            $STATUS = 0;
            $CODE = $CODES::ERROR;
            $MSG = $this->messages->M111;
            $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);
    }
    
    
      /*
     * function for getting CMS pages list
     * @author: Waseem
     */
    public function getCmsPage($data) {
        $CODES = new Codes; 
        
        $id = $data['id'];
        $list = \app\models\Pages::find()->Where(['id'=>$id, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED])->one();
        if($list) {
            $STATUS = 1;
            $CODE = $CODES::SUCCESS;
            $MSG = "";
            $DATA = $list;
        } else {
            $STATUS = 0;
            $CODE = $CODES::ERROR;
            $MSG = $this->messages->M111;
            $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);
    }
   
}
