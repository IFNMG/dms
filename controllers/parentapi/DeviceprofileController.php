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


class DeviceprofileController extends ApiController{
 
        /**
     *@author:prachi
     *To update Device Token
     */
    public function actionUpdate()
    {      
        $commonFacade = new CommonFacade(); 
        $deviceId=$commonFacade->getDeviceIdFromHeaders();
        
        $params=$_REQUEST;
        
        $facade= new ApiFacade();        
        
        $request['device_id']=$deviceId;
        $request['device_token']=  isset($params['device_token'])?$params['device_token']:'';
        
        $response=$facade->updateDeviceToken($request);        
        $STATUS=$CODE=$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS=$response['STATUS'];
            $CODE=$response['CODE'];
            $MESSAGE=$response['MESSAGE'];            
        }        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);   
        
    }
}