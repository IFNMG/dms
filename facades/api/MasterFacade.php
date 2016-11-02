<?php
namespace app\facades\api;


/*
 * @AUTHOR : Prachi
 * @DATE : 07-04-2016
 * @DESCRIPTION: Masters for API
 */

use Yii;

use app\models\LookupTypes;
use app\models\Lookups;
use app\models\Countries;
use app\models\States;
use app\models\Cities;

use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;

class MasterFacade{
    
    private $messages;           //stores an instance of the messages XML file.
    
    public function __construct() {   
         $this->messages = CommonFacade::getMessages();
         //::findall is delete1
    }  
    
    public function getMasters($request){
        $countries= Countries::findAll(['is_delete'=>1]);
        $states=  States::findAll(['is_delete'=>1]);
        $cities=  Cities::findAll(['is_delete'=>1]);
        //return array('Countries'=>$countries,'States'=>$states,'Cities'=>$cities);
        
         $connection = Yii::$app->getDb();
         $query="SELECT id,value,isd_code,exit_code,status,modified_on FROM cc_countries "
                 . "WHERE is_delete='1'" ;
            
        $command = $connection->createCommand($query);
        $countries = $command->queryAll();  
        
        $query1="SELECT id,value,short_name,country_id,modified_on FROM cc_states "
                . "WHERE is_delete='1'";
        $command1 = $connection->createCommand($query1);
        $states = $command1->queryAll();  
        
        $query2="SELECT id,value,state_id,modified_on FROM cc_cities "
                . "WHERE is_delete='1'";
        $command2 = $connection->createCommand($query2);
        $cities = $command2->queryAll();  
        return array('Countries'=>$countries,'States'=>$states,'Cities'=>$cities);

        
    }
    
    public function getLookupsData($request){
        $data=array();
        $query=LookupTypes::find();
        $query->where(['is_delete'=>1,'sync_to_mobile'=>1]);
        if(isset($request['last_sync_on'])){
            $query->andWhere(['>=','modified_on',$request['last_sync_on']]);
        }
        $types=$query->all();              
        for($t=0;$t<count($types);$t++){
            $query2=Lookups::find();
            $query2->where(['is_delete'=>1,'type'=>$types[$t]->id]);
            if(isset($request['last_sync_on'])){
                $query2->andWhere(['>=','modified_on',$request['last_sync_on']]);
            }
            $result=$query2->all();
            for($i=0;$i<count($result);$i++){                
                $data[$types[$t]->value][]=array(
                                            'id'=>$result[$i]->id,
                                            'value'=>$result[$i]->value,
                                            'parent_id'=>$result[$i]->parent_id,
                                            'status'=>$result[$i]->status,
                                            'modified_on'=>$result[$i]->modified_on
                        
                        );
            }
            
        }
        return $data;
    }
    
    public function getCity($request){
        
        $connection = Yii::$app->getDb();
 
        $query="SELECT id,value,state_id,zip_code,status,modified_on FROM cc_cities "
                . "WHERE is_delete='1'";
        if(isset($request['last_sync_on'])){
           $utcTime=  CommonFacade::getUTCDateTime($request['last_sync_on'],"Y-m-d H:i:s");
           $query.=" AND modified_on >='".$utcTime."'";
        }
        
        $command = $connection->createCommand($query);
        $cities = $command->queryAll();  
        if(!empty($cities)){
              $CODE = Codes::SUCCESS;
              $STATUS = 1;
              $MSG = "";
              $DATA = $cities;
        }
        else{
                $CODE = Codes::SUCCESS;
                $STATUS = 1;
                $MSG = $this->messages->M121;
                $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);        
    }
    
    public function getState($request){
        
        $connection = Yii::$app->getDb();
 
        $query="SELECT id,value,short_name,country_id,status,modified_on FROM cc_states "
                . "WHERE is_delete='1'";
        if(isset($request['last_sync_on'])){
           $utcTime=  CommonFacade::getUTCDateTime($request['last_sync_on'],"Y-m-d H:i:s");
           $query.=" AND modified_on >='".$utcTime."'";
        }
        
        $command = $connection->createCommand($query);
        $states = $command->queryAll();  
        if(!empty($states)){
              $CODE = Codes::SUCCESS;
              $STATUS = 1;
              $MSG = "";
              $DATA = $states;
        }
        else{
                $CODE = Codes::SUCCESS;
                $STATUS = 1;
                $MSG = $this->messages->M121;
                $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);        
    }
    
    public function getCountry($request){
        
        $connection = Yii::$app->getDb();
        $base_url=Yii::$app->params['HTTP_UPLOAD_URL'];
        $query="SELECT id,value,iso_code,isd_code,exit_code,status,modified_on,IF(flag_url IS NULL or flag_url = '','',CONCAT('".$base_url."',flag_url)) AS flag_url FROM cc_countries "
                . "WHERE is_delete='1'";
        if(isset($request['last_sync_on'])){
           $utcTime=  CommonFacade::getUTCDateTime($request['last_sync_on'],"Y-m-d H:i:s");
           $query.=" AND modified_on >='".$utcTime."'";
        }
        
        $command = $connection->createCommand($query);
        $country = $command->queryAll();  
        
        if(!empty($country)){
              $CODE = Codes::SUCCESS;
              $STATUS = 1;
              $MSG = "";
              $DATA = $country;
        }
        else{
                $CODE = Codes::SUCCESS;
                $STATUS = 1;
                $MSG = $this->messages->M121;
                $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);        
    }
    
    function  getLookupTypes($request){
        $connection = Yii::$app->getDb();
        $query="SELECT id,value,short_code,parent_id,status FROM cc_lookup_types WHERE is_delete=1 AND sync_to_mobile=1";
         if(isset($request['last_sync_on'])){
           $utcTime=  CommonFacade::getUTCDateTime($request['last_sync_on'],"Y-m-d H:i:s");
           $query.=" AND modified_on >='".$utcTime."'";
        }
        
        $command = $connection->createCommand($query);
        $lookupType = $command->queryAll(); 
        if(!empty($lookupType)){
              $CODE = Codes::SUCCESS;
              $STATUS = 1;
              $MSG = "";
              $DATA = $lookupType;
        }
        else{
                $CODE = Codes::SUCCESS;
                $STATUS = 1;
                $MSG = $this->messages->M121;
                $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);  
        
    }
    
    function  getLookups($request){
        $connection = Yii::$app->getDb();
        $query="SELECT c.id,c.value,c.short_code,c.parent_id,c.type,c.status, c.modified_on FROM cc_lookups  AS c "
                . "JOIN cc_lookup_types AS ct ON c.type=ct.id "
                . "WHERE ct.sync_to_mobile=1 AND c.is_delete=1";
        
        if(isset($request['last_sync_on'])){
           $utcTime=  CommonFacade::getUTCDateTime($request['last_sync_on'],"Y-m-d H:i:s");
           $query.=" AND c.modified_on >='".$utcTime."'";
        }
        
        $command = $connection->createCommand($query);
        $lookup = $command->queryAll(); 
        if(!empty($lookup)){
              $CODE = Codes::SUCCESS;
              $STATUS = 1;
              $MSG = "";
              $DATA = $lookup;
        }
        else{
                $CODE = Codes::SUCCESS;
                $STATUS = 1;
                $MSG = $this->messages->M121;
                $DATA = array();
        }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG, 'DATA'=>$DATA);  
        
    }
}