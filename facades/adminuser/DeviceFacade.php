<?php

namespace app\facades\adminuser;

/**
 * @author: Prachi
 * @date: 17-March-2016
 * @description: LookupFacade interacts with models for all lookup related activities
 */

use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use app\models\Devices;
use \app\models\Notifications;
use app\web\util\Codes\LookupCodes;

class DeviceFacade {

    public $messages;           //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function listDevice($where=NULL){
            $CODES = new Codes();
            $data= Devices::find()->select(['id','user_id','device_id','device_type','status','created_on'])
                    ->where(['is_delete'=>1])->orderBy(['created_on'=>'SORT_DESC'])
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
    
    
    public function listSearchDevice($where=NULL){        
        
        $CODES = new Codes();
        $result = Devices::find();
        $result->select(['id','user_id','device_id','device_type','status','created_on']);
        $result->where(['is_delete'=>1]);
        if(isset($where['devicetype'])){
        $result->andWhere(['device_type'=>$where['devicetype']]);}
        
        if(isset($where['generalize']) && $where['generalize']=='-1'){//mapped i.e. having userid
        $result->andWhere(['IS NOT', 'user_id', NULL]);}
        elseif(isset($where['generalize']) &&  $where['generalize']=='-2'){ //unmapped i.e. not having userid
        $result->andWhere(['IS', 'user_id', NULL]);}
        
        if(isset($where['country'])){
            $result->andWhere([]);
        }
        
        $result->orderBy(['created_on'=>'SORT_DESC']);
        $data=$result->all();
        
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
   
    public function saveNotifications($data){        
        $CODES = new Codes();
        $screenId =  $data['screen'];
        $message = $data['message'];
        $generalize = $data['generalize'];
        $image = $data['image'];
        
        
        $result = Devices::find();
        $result->where(['is_delete'=>1]);
        
        if($generalize == 1){
            $result->andWhere(['IS NOT', 'cc_devices.user_id', NULL]);
        } else if($generalize == 2){
            $result->andWhere(['user_id'=>NULL]);
        }
        
        if(isset($data['deviceList'])){
            $result->andWhere(['device_type'=>$data['deviceList']]);
        }
        
        if($generalize == 1 || $generalize == 3){
            if(isset($data['countryList']) && !empty($data['countryList'])){
                $result->leftJoin('cc_user_personal_details', 'cc_user_personal_details.user_id = cc_devices.user_id');
                $result->andWhere(['IN', 'cc_user_personal_details.country', $data['countryList']]);
                if(isset($data['stateList']) && !empty($data['stateList'])){
                    $result->andWhere(['IN', 'cc_user_personal_details.state', $data['stateList']]);
                    if(isset($data['cityList']) && !empty($data['cityList'])){
                        $result->andWhere(['IN', 'cc_user_personal_details.city', $data['cityList']]);
                    }
                }
            }
        }
        
        $deviceList = $result->all();
        if($deviceList){
            $androidIDs = array(); // device ids array
            $iosIDs = array(); // device ids array
            
            $save_path = "";
            if($image != ''){
                $extension = substr($image, 5, strpos($image, ';') - 5);
                if($extension == 'image/jpeg') {
                    $avatar = Yii::$app->security->generateRandomString().".jpg";
                } else if ($extension == 'image/png') {
                    $avatar = Yii::$app->security->generateRandomString().".png";
                } 
                $name = Yii::$app->params['UPLOAD_PATH'].'notification/' . $avatar;
                $save_path = "notification/".$avatar;
                if($extension == 'image/jpeg'){
                    $image = str_replace("data:image/jpeg;base64,", "", $image);
                } else if ($extension == 'image/png') {
                    $image = str_replace("data:image/png;base64,", "", $image);
                }
                if($extension == 'image/jpeg' || $extension == 'image/png'){
                    $data = base64_decode($image);
                    $im = @imagecreatefromstring($data);
                    if ($im !== false) {
                        header("Content-Type: $extension");
                        if($extension == 'image/jpeg'){
                            imagejpeg($im, $name);
                        } else if($extension == 'image/png'){
                            imagepng($im, $name);
                        }
                    }
                } 
            }
            
            foreach($deviceList as $device){
                $notification = new Notifications();
                $notification->device_id = $device->id;
                $notification->message = $message;
                $notification->screen_id = $screenId;
                if($save_path != ""){
                    $notification->image = $save_path;
                }
                $notification->status = LookupCodes::L_EMAIL_STATUS_PENDING;
                $notification->created_by = Yii::$app->admin->adminId;
                $notification->created_on = date('Y-m-d h:i:s');
                if($notification->save()){
                    $MSG = \yii::t('app', $this->messages->M132);
                    $CODE = $CODES::SUCCESS;
                    $data1 = array('STATUS' => 'success', 'SUBDATA' => array());
                } else {
                    $MSG = $notification->getErrors();
                    $CODE = $CODES::VALIDATION_ERROR;
                    $data1 = array('STATUS' => 'error', 'SUBDATA' => array());
                }
            }
        } else {
            $MSG = \yii::t('app', $this->messages->M131);
            $CODE = $CODES::ERROR;
            $data1 = array('STATUS' => 'error', 'SUBDATA' => array());
            
        }    
        
        return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data1); 
    }
    
    
    
    public function sendNotifications(){        
        $CODES = new Codes();
        
        //$notificationList = Notifications::find()->select(['id', 'device_id', 'message', 'status'])->where(['status'=>400001])->all();
        $notificationList = Notifications::find()->where(['status'=>400001])->all();
        if($notificationList){
            $androidIDs = array(); // device ids array
            $iosIDs = array(); // device ids array
            $i =0;
            foreach($notificationList as $model){
                if($model->device->device_type == 300001){
                    $i++;
                    if($i < 1000){
                        array_push($androidIDs, $model->device->device_token);
                    }
                } else if($model->device->device_type == 300002){
                    array_push($iosIDs, $model);
                }
                $message = $model->message;
                if($model->image != ''){
                    $image = Yii::getAlias('@web').Yii::$app->params['UPLOAD_URL'].$model->image;
                } else {
                    $image = '';
                }
            }
            if(!empty($androidIDs)){
                $android = $this->sendToAndroid($androidIDs, $message, $image);
                if($android){
                    foreach($android as $deviceObj){
                        $deviceObj->status = 400002;
                        if($deviceObj->save()){
                            $MSG = \yii::t('app', $this->messages->M132);
                            $CODE = $CODES::SUCCESS;
                            $data1 = array('STATUS' => 'success', 'SUBDATA' => array());
                        }
                    }
                }
            }
            if(!empty($iosIDs)){
                $ios = $this->sendToIOS($iosIDs, $message);
                if($ios){
                    if($ios['Code'] == 200){
                        foreach($ios['Data'] as $deviceObj){
                            $deviceObj->status = 400002;
                            if($deviceObj->save()){
                                $MSG = \yii::t('app', $this->messages->M132);
                                $CODE = $CODES::SUCCESS;
                                $data1 = array('STATUS' => 'success', 'SUBDATA' => array());
                            }
                        }
                    } else {
                        $MSG = \yii::t('app', $this->messages->M131);
                        $CODE = $CODES::ERROR;
                        $data1 = $ios['Data'];
                    }
                }
            }
        } else {
            $MSG = \yii::t('app', $this->messages->M131);
            $CODE = $CODES::SUCCESS;
            $data1 = array();  
        }   
        
        return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data1); 
    }
    
    
    public function sendToIOS($iosIDs, $notification){
        
        require_once \Yii::getAlias('@app') .'/vendor/yiisoft/ApnsPHP-master/ApnsPHP/Autoload.php';
        
        $pem = Yii::getAlias('@app').'/web/uploads/'.Yii::$app->params['configurations']['APN_PEM'];
        
        $push = new \ApnsPHP_Push(\ApnsPHP_Abstract::ENVIRONMENT_SANDBOX, $pem);
        
        $push->connect();
        
        foreach ($iosIDs as $device) {
            if($device->device_id != ''){
                $deviceId = $device->device->device_token;
                $message = new \ApnsPHP_Message($deviceId);
                $message->setCustomIdentifier("Message-Badge-3");
                $message->setBadge(3);
                $message->setText($notification);
                $message->setSound();
                $message->setCustomProperty('ScreenId', $device->screen_id);
                $message->setExpiry(30);
                $push->add($message);
            }
        }
        
        $push->send();
        $push->disconnect();
        
        $aErrorQueue = $push->getErrors();
        
        if (!empty($aErrorQueue)) {
            return array('Code'=>100, 'Data'=>$aErrorQueue);
        } else {
            return array('Code'=>200, 'Data'=>$iosIDs);
        }
          
    }
    
    
    public function sendToAndroid($registrationIDs, $message, $image){
        $apiKey = Yii::$app->params['configurations']['GCM_KEY'];
        
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => array( 
                    "message" => $message,
                    "largeIcon"	=> $image,
                ),
        );
        
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    
    
    
    
    public function viewList($data){        
        $CODES = new Codes();
        
        $draw = $data['start'];
        $orderArr = $data['order'];
        $sortBy = $orderArr[0]['column'];
        
        if($orderArr[0]['dir'] == 'desc'){
            $order = SORT_DESC;
        } else if($orderArr[0]['dir'] == 'asc'){
            $order = SORT_ASC;
        }
        
        $term = $data['search']['value'];
        
        $generalize = $data['generalize'];
        
        
        $result1 = Devices::find();
        $result1->where(['is_delete'=>1]);
        
        if($generalize == 1){
            $result1->andWhere(['IS NOT', 'cc_devices.user_id', NULL]);
        } else if($generalize == 2){
            $result1->andWhere(['user_id'=>NULL]);
        }
        if(isset($data['deviceList'])){
            $result1->andWhere(['device_type'=>$data['deviceList']]);
        }
        if($generalize == 1 || $generalize == 3){
            if(isset($data['countryList']) && !empty($data['countryList'])){
                $result1->leftJoin('cc_user_personal_details', 'cc_user_personal_details.user_id = cc_devices.user_id');
                $result1->andWhere(['IN', 'cc_user_personal_details.country', $data['countryList']]);
                if(isset($data['stateList']) && !empty($data['stateList'])){
                    $result1->andWhere(['IN', 'cc_user_personal_details.state', $data['stateList']]);
                    if(isset($data['cityList']) && !empty($data['cityList'])){
                        $result1->andWhere(['IN', 'cc_user_personal_details.city', $data['cityList']]);
                    }
                }
            }
        }
        if($term != ''){
            if($generalize == 1 || $generalize == 3){
                $result1->leftJoin('cc_user_personal_details as upd2', 'upd2.user_id = cc_devices.user_id');
                $result1->andFilterWhere(['or', ['like','cc_devices.id', $term], ['like','cc_devices.device_id', $term],['like','upd2.first_name', $term],]);
            } else {
                $result1->andFilterWhere(['or', ['like','cc_devices.id', $term], ['like','cc_devices.device_id', $term],]);
            }
        }
        $total = $result1->count();
        
        
        
        $result = Devices::find();
        $result->where(['is_delete'=>1]);
        
        if($generalize == 1){
            $result->andWhere(['IS NOT', 'cc_devices.user_id', NULL]);
        } else if($generalize == 2){
            $result->andWhere(['user_id'=>NULL]);
        }
        
        if(isset($data['deviceList'])){
            $result->andWhere(['device_type'=>$data['deviceList']]);
        }
        
        if($generalize == 1 || $generalize == 3){
            if(isset($data['countryList']) && !empty($data['countryList'])){
                $result->leftJoin('cc_user_personal_details as upd', 'upd.user_id = cc_devices.user_id');
                $result->andWhere(['IN', 'upd.country', $data['countryList']]);
                if(isset($data['stateList']) && !empty($data['stateList'])){
                    $result->andWhere(['IN', 'upd.state', $data['stateList']]);
                    if(isset($data['cityList']) && !empty($data['cityList'])){
                        $result->andWhere(['IN', 'upd.city', $data['cityList']]);
                    }
                }
            }
        }
        
        if($sortBy == 0){
            $result->orderBy(['Id' =>$order]);
        } else if($sortBy == 1){
            $result->orderBy(['device_id' =>$order]);
        } else if($sortBy == 2 && ($generalize == 1 || $generalize == 3)){
            $result->leftJoin('cc_user_personal_details as upd1', 'upd1.user_id = cc_devices.user_id');
            $result->orderBy(['upd1.Id' =>$order]);
        } 
        
        if($term != ''){
            if($generalize == 1 || $generalize == 3){
                $result->leftJoin('cc_user_personal_details as upd2', 'upd2.user_id = cc_devices.user_id');
                $result->andFilterWhere(['or', ['like','cc_devices.id', $term], ['like','cc_devices.device_id', $term],['like','upd2.first_name', $term],]);
            } else {
                $result->andFilterWhere(['or', ['like','cc_devices.id', $term], ['like','cc_devices.device_id', $term],]);
            }
        }
        $output = $result->offset($draw)->limit(10)->all();
        
        
        
        $finalArr = array();
        if($output){
            foreach($output as $model){
                $userName = '';
                if($model->user_id != ''){
                    $userPersonal = \app\modules\user\models\UserPersonal::find()->where(['user_id'=>$model->user_id])->one();
                    if($userPersonal){
                        $userName = $userPersonal->first_name.' '.$userPersonal->last_name;
                    }
                }
                array_push($finalArr, array($model->id, $model->device_id, $userName));
            }
        }
        $finalArray = array('recordsTotal'=>$total, 'recordsFiltered'=>$total, 'data'=>$finalArr);
        return ($finalArray);
        
        //$CODE = $CODES::SUCCESS;
        //return array('CODE' => $CODE, 'MESSAGE' =>"", 'DATA' => $finalArr);         
        
    }
    
}
