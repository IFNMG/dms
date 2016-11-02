<?php

namespace app\modules\navitrex\controllers\Api\v1;

use app\controllers\parentapi\ApiController;
use app\facades\common\CommonFacade;
use \app\modules\navitrex\facades\NavitrexFacade;


class NavitrexController extends ApiController{
    
    
    
    /**
    * @author: Waseem Khan
    * @date: 29-JUNE-2016
    * @description: getting list of GPX files
    */
    
    public function actionGetgpx(){       
        $params = $_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade = new NavitrexFacade();     
        
        $response = $facade->getGPXFiles($request);   
        
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);  
    }
    
    
    /**
    * @author: Waseem Khan
    * @Date: 28 JUNE 2016
    * @description: For adding places
    */
    public function  actionAdd(){
        
        $DATA = array();
        $STATUS = $CODE = $MESSAGE = "";
        $facade = new NavitrexFacade();
        $commonFacade = new CommonFacade();
        $globalAuthorizedToken = ApiController::setAuthorizeToken();   //getting token from header
        $user_id = $commonFacade->getUserIdFromAuthorizedToken($globalAuthorizedToken); //getting user id from authorizedToken
        
        
        if($user_id == ""){
           $STATUS = 0; $CODE = 400 ; $MESSAGE = "Bad Request";
            ApiController::response($STATUS, $CODE, $MESSAGE, $DATA);
        }
        
        $response = $facade->addPlace($_REQUEST, $user_id);        
        
        
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            if(isset($response['DATA'])){
                $DATA=$response['DATA'];
            }
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);
    }
    
    
    /**
    * @author: Waseem Khan
    * @Date: 29 JUNE 2016
    * @description: For viewing complete details of a place
    */
    public function  actionViewplace(){
        
        $DATA = array();
        $STATUS = $CODE = $MESSAGE = "";
        $facade = new \app\facades\backend\PlacesFacade();
        $commonFacade = new CommonFacade();
        $globalAuthorizedToken = ApiController::setAuthorizeToken();   //getting token from header
        $user_id = $commonFacade->getUserIdFromAuthorizedToken($globalAuthorizedToken); //getting user id from authorizedToken
        
        
        if($user_id == ""){
           $STATUS = 0; $CODE = 400 ; $MESSAGE = "Bad Request";
            ApiController::response($STATUS, $CODE, $MESSAGE, $DATA);
        }
        $id = $_REQUEST['id'];
        $response = $facade->reviewPlace($id, $user_id);        
        
        
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            if(isset($response['DATA'])){
                //$DATA=$response['DATA'];
                
                $model = $response['DATA']['model'];
                
                $DATA['PLACE']['ID'] = $model->id;
                $DATA['PLACE']['LATITUDE'] = $model->latitude;
                $DATA['PLACE']['LONGITUDE'] = $model->longitude;
                $DATA['PLACE']['POI_TYPE'] = $model->poi_type;
                $DATA['PLACE']['NAME'] = $model->name;
                $DATA['PLACE']['PHONE'] = $model->phone_number;
                if($model->poi_type == 2000001){
                    if($model->brand_icon != ''){
                        $DATA['PLACE']['ICON'] = \Yii::getAlias('@web') . '/uploads/places/'.$model->brand_icon;
                    } else {
                        $DATA['PLACE']['ICON'] = '';
                    }
                }
                
                $DATA['PLACE']['COMMENTS'] = $model->comments;
                
                $DATA['ATTRIBUTES'] = $response['DATA']['facilityArr'];
                
            }
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);
    }
    
    
    /**
    * @author: Waseem Khan
    * @Date: 29 JUNE 2016
    * @description: For searching places
    */
    public function  actionSearch(){
        $DATA = array();
        $STATUS = $CODE = $MESSAGE = "";
        $facade = new NavitrexFacade();
        $commonFacade = new CommonFacade();
        $globalAuthorizedToken = ApiController::setAuthorizeToken();   //getting token from header
        $user_id = $commonFacade->getUserIdFromAuthorizedToken($globalAuthorizedToken); //getting user id from authorizedToken
        
        
        if($user_id == ""){
           $STATUS = 0; $CODE = 400 ; $MESSAGE = "Bad Request";
            ApiController::response($STATUS, $CODE, $MESSAGE, $DATA);
        }
        
        $response = $facade->searchPlaces($_REQUEST, $user_id);        
        
        
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            if(isset($response['DATA'])){
                $DATA=$response['DATA'];
            }
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);
    }
}


