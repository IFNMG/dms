<?php


namespace app\controllers\parentapi;
/*
 *@AUTHOR:Prachi
 *@DATE:09-03-2016
 * @DESCRIPTION: common functions for api
 * 
*/
use Yii;
use yii\web\Controller;
use app\facades\api\ApiFacade;
use app\facades\common\CommonFacade;
use app\controllers\parentapi;


class AuthorizeController extends ApiController{
    /**
     *@author:prachi
     *To register a new device or generate a new authorized token
     */
     public function actionIndex(){   
       
        $facade= new ApiFacade();                 
        $commonFacade= new CommonFacade();             
        $deviceId=$commonFacade->getDeviceIdFromHeaders();
        
        $headers=apache_request_headers(); 
     //   print_r($headers);
        $deviceType=$headers['DeviceType'];
        $deviceToken="";
        if(isset($headers['DeviceToken'])){
            $deviceToken=$headers['DeviceToken'];
        }       
       
       
        $deviceCount=$facade->deviceIdExists($deviceId);
        if($deviceCount==0){
            $currentToken=$commonFacade->setDeviceCurrentToken($deviceId);
            $currentTime=$commonFacade->getCurrentDateTime();

            $saveData['device_id']=  $deviceId;
            $saveData['device_type'] = $deviceType;
            $saveData['device_token'] = $deviceToken;
            $saveData['current_token'] =$currentToken;
            $saveData['is_delete']=1;
            $saveData['created_on']=$saveData['modified_on']=$currentTime;

            $regResponse=$facade->registerNewDevice($saveData);
            $STATUS=$CODE=$MESSAGE="";$DATA=array();
            if(!empty($regResponse)){
                $STATUS=$regResponse['STATUS'];
                $CODE=$regResponse['CODE'];
                $MESSAGE=$regResponse['MESSAGE'];
            }
            if($STATUS==1){$DATA['API_CURRENT_TOKEN']=$currentToken;}                  
            ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);             
        }else{
            $currentToken=$commonFacade->swapDeviceAuthorizedTokens($deviceId);
            $STATUS=1;$CODE=200;$MESSAGE="";$DATA['API_CURRENT_TOKEN']=$currentToken;
            ApiController::response($STATUS,$CODE,$MESSAGE,$DATA); 
        }
          
    }
    
}
