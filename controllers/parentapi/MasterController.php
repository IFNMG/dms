<?php


namespace app\controllers\parentapi;
/*
 *@AUTHOR:Prachi
 *@DATE:07-04-2016
 * @DESCRIPTION: common functions for masters and lookups data
 */

use Yii;
use yii\web\Controller;
use app\facades\api\ApiFacade;
use app\facades\common\CommonFacade;
use app\controllers\parentapi;


class MasterController extends ApiController{
 
     /**
     *@author:prachi
     *get Master Data (like country state city)
     */
    public function actionIndex()
    {      
                
        $params=$_REQUEST;        
        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getMasters($request);
                
        ApiController::response($STATUS=1,$CODE=200,$MESSAGE="",$response);   
        
    }
    
     /**
     *@author:prachi
     *get lookup and lookuptypes in a single badge
     */
    
    public function actionLookups(){
        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getLookups($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);   
        
    }
    
    
    // 5 different apis fro 5 different tables for masters and lookups
    
    public function actionCity(){
        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getCity($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);   
        
    }
    
    public function actionState(){
        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getState($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);   
        
    }
    
    public function actionCountry(){
        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getCountry($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);   
        
    }
    
    public function actionLookup(){        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getLookups($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);  
    }
    
    public function actionLookuptype(){        
        $params=$_REQUEST;        
        $request['last_sync_on']=  isset($params['last_sync_on'])?$params['last_sync_on']:'';
        $facade= new \app\facades\api\MasterFacade();        
        $response=$facade->getLookupTypes($request);
        $STATUS=0;$CODE=100;$MESSAGE="";$DATA=array();
        if(!empty($response)){
            $STATUS = $response['STATUS'];
            $CODE = $response['CODE'];
            $MESSAGE = $response['MESSAGE'];
            $DATA = $response['DATA'];
        }
        ApiController::response($STATUS,$CODE,$MESSAGE,$DATA);  
    }
 
    
    
    
}