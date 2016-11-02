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
use app\models\Lookups;
use app\web\util\Codes\LookupCodes;

class LookupFacade {

    public $messages;           //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function listLookup(){        
            $CODES = new Codes();
            $lookupModel = new Lookups();
            
            $connection = Yii::$app->getDb();
            
           if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
            $query="SELECT c.id,c.value,t.value AS lookup_type,IFNULL(l.value,'-') AS parent,IF(c.is_seed_data='0','user managed','system managed') AS seed_data_type,ls.value AS status,c.status as status_id"
                    . " FROM cc_lookups AS c"
                    . " LEFT JOIN cc_lookups AS l ON c.parent_id=l.id"
                    . " LEFT JOIN cc_lookups AS ls ON c.status=ls.id"
                    . " LEFT JOIN cc_lookup_types AS t ON c.type=t.id"
                    . " WHERE c.is_delete='1' AND c.type = 45 OR c.type = 46 OR c.type = 50" ;
           }else{
                $query="SELECT c.id,c.value,t.value AS lookup_type,IFNULL(l.value,'-') AS parent,IF(c.is_seed_data='0','user managed','system managed') AS seed_data_type,ls.value AS status,c.status as status_id"
                    . " FROM cc_lookups AS c"
                    . " LEFT JOIN cc_lookups AS l ON c.parent_id=l.id"
                    . " LEFT JOIN cc_lookups AS ls ON c.status=ls.id"
                    . " LEFT JOIN cc_lookup_types AS t ON c.type=t.id"
                    . " WHERE c.is_delete='1' AND c.is_seed_data='0' AND t.type_of_lookup_type='0' AND c.type = 45 OR c.type = 46 OR c.type = 50" ;
           }
            
            $command = $connection->createCommand($query);
            $data = $command->queryAll();               
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
    
    public function add_modify($request,$image=array()){        
        
          $CODES = new Codes();
          $commonFacade= new CommonFacade();
          $model= new Lookups();
          $path=$save_path="";
          if(!empty($image)){
           // store the source file name            
            $ext = end((explode(".", $image->name)));

            // generate a unique file name
            $avatar = Yii::$app->security->generateRandomString().".{$ext}";

            // the path to save file, you can set an uploadPath           
            $path = Yii::$app->params['UPLOAD_PATH'].'lookup/' . $avatar;
            $save_path="lookup/".$avatar;
          }
          
          $currentTime=$commonFacade->getCurrentDateTime();        
          if($request['id']!=""){ //edit
              if($save_path!=""){
                 $request['image_path']=$save_path;                  
              }else{unset($request['image_path']);}
              $model = Lookups::find()->where(['id'=>$request['id']])->one(); 
              
              if($model->is_seed_data==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                  $MSG=$this->messages->M122;
                  $MSG=  str_replace('{1}','update', $MSG);
                  $MSG=  str_replace('{2}','lookup', $MSG);
                  $CODE=$CODES::ERROR;
                  $data = array('STATUS' => 'error', 'SUBDATA' => array());
                  return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
              }
              
               if($save_path!=""){
                if(file_exists(Yii::$app->params['UPLOAD_PATH'].$model->image_path) && $model->image_path!=""){
                 unlink(Yii::$app->params['UPLOAD_PATH'].$model->image_path);
                 Yii::$app->params['UPLOAD_PATH'].$model->image_path;
                }
               }
              $response="updated";
          }else{    //add
              $auto_id=self::setIdentity($request['type']);              
              $request['id']=$auto_id;
              $request['created_by']=Yii::$app->admin->adminId;
              $request['created_on']=$currentTime;
              $request['is_delete']='1';
              $request['image_path']=$save_path;
              $model->id= $request['id'];
              $response="added";              
          } 
              $request['modified_by'] =  Yii::$app->admin->adminId;
              $request['modified_on'] = $currentTime;              
              $model->setAttributes($request);              
          if($model->validate()){  //To Validate Model
                try{
                    
                    if ($model->save()) {                           
                         if($path!=""){$res=$image->saveAs($path);}
                        $MSG=$this->messages->M113; //To get Message
                        $MSG= str_replace("{1}","Lookup", $MSG);
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
            $connection = Yii::$app->getDb();
            $query="SELECT c.id,c.value,c.description,c.info1,c.info2,c.image_path,t.value AS lookup_type,IFNULL(l.value,'-') AS parent,IF(c.is_seed_data='0','user managed','system managed') AS seed_data_type,ls.value AS status,c.short_code"
                    . " FROM cc_lookups AS c"
                    . " LEFT JOIN cc_lookups AS l ON c.parent_id=l.id"
                    . " LEFT JOIN cc_lookups AS ls ON c.status=ls.id"
                    . " LEFT JOIN cc_lookup_types AS t ON c.type=t.id"
                    . " WHERE c.id='".$id."'";
            $command = $connection->createCommand($query);
            $data = $command->queryAll();                           
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
        $model = Lookups::find()->where(['id'=>$id])->one(); 
        if($model->is_seed_data==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                  $MSG=$this->messages->M122;
                  $MSG=  str_replace('{1}','delete', $MSG);
                  $MSG=  str_replace('{2}','lookup', $MSG);
                  $CODE=$CODES::ERROR;
                  $data = array('STATUS' => 'error', 'SUBDATA' => array());
                  return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        }
        
        $value=$model->value;
        $model->is_delete=0;
        /*if($model->validate(false)){  //To Validate Model
                try{
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
                 * 
                            
                
        } else {              
            $error = array_values($model->firstErrors);
            $MSG="";
            if(!empty($error)){$MSG=$error[0];}       
            $CODE=$CODES::VALIDATION_ERROR;
            $data = array('STATUS' => 'error', 'SUBDATA' => array());
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        } */  
    }
    
    public function changeStatus($id,$status){        
        $CODES = new Codes;   
        $commonFacade= new CommonFacade();
        $currentTime=$commonFacade->getCurrentDateTime();        
        if($id != ''){
            $model = Lookups::find()->where(['id'=>$id])->one();
            if($model){
                if($model->is_seed_data==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                  $MSG=$this->messages->M122;
                  $MSG=  str_replace('{1}','change status of', $MSG);
                  $MSG=  str_replace('{2}','lookup', $MSG);
                  $CODE=$CODES::ERROR;                  
                  return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $status);
                }
                
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


    public function setIdentity($lookupTypeId){       
        $count=Lookups::find()->where(['type'=>$lookupTypeId])->count();
        if($count>0){
            $max_id=Lookups::find()->select('id')->where(['type'=>$lookupTypeId])->max('id');
            $auto_id=$max_id+1;
            return $auto_id;
        }else{
            $startRange=(($lookupTypeId*50000)+50000);
            $endRange=$startRange+50000;
            $auto_id=$startRange+1;
            return $auto_id;
        }        
        return false;
    }

    
}