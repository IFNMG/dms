<?php

namespace app\facades\adminuser;

/**
 * @author: Prachi
 * @date: 16-March-2016
 * @description: LookuptypeFacade interacts with models for all lookuptype related activities
 */

use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use app\models\LookupTypes;
use app\web\util\Codes\LookupCodes;


class LookuptypeFacade {

    public $messages;           //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function listLookupType(){
            $CODES = new Codes();
            $lookupTypeModel = new LookupTypes();
            
            $connection = Yii::$app->getDb();
            
            if(Yii::$app->user->identity->user_type==LookupCodes::L_USER_TYPE_DEVELOPERS){
                $query="SELECT c.id,c.value,IFNULL(l.value,'-') AS parent,IF(c.type_of_lookup_type='0','user managed','system managed') AS seed_data_type,IF(c.status='0','disabled','enabled') AS status,c.status AS status_id FROM cc_lookup_types AS c LEFT JOIN cc_lookup_types AS l ON c.parent_id=l.id WHERE c.is_delete='1'";
            }else{
                $query="SELECT c.id,c.value,IFNULL(l.value,'-') AS parent,IF(c.type_of_lookup_type='0','user managed','system managed') AS seed_data_type,IF(c.status='0','disabled','enabled') AS status,c.status AS status_id FROM cc_lookup_types AS c LEFT JOIN cc_lookup_types AS l ON c.parent_id=l.id WHERE c.is_delete='1'  AND c.type_of_lookup_type='0'";
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
    
    public function add_modify($request){
          $CODES = new Codes();
          $commonFacade= new CommonFacade();
          $model= new LookupTypes();
          
          $currentTime=$commonFacade->getCurrentDateTime();
        
          if($request['id']!=""){ //edit
              $model = LookupTypes::find()->where(['id'=>$request['id']])->one();  
              $response="updated";
              unset($request['short_code']);
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
                        $MSG= str_replace("{1}","Lookup Types", $MSG);
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
            $query="SELECT c.id,c.short_code,c.value,IFNULL(l.value,'-') AS parent,IF(c.type_of_lookup_type='0','user managed','system managed') AS seed_data_type,IF(c.status='0','disabled','enabled') AS status,IF(c.sync_to_mobile='0','no','yes') AS sync_to_mobile FROM cc_lookup_types AS c LEFT JOIN cc_lookup_types AS l ON c.parent_id=l.id WHERE c.id='".$id."'";
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
        $model = LookupTypes::find()->where(['id'=>$id])->one(); 
        $value=$model->value;
        $model->is_delete=0;
      /*  if($model->validate()){  //To Validate Model
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
             /*   }//EOF try
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
            $model = LookupTypes::find()->where(['id'=>$id])->one();
            if($model){
                $model->status = $status;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = $currentTime;

                if($model->save(false)){
                    $MSG = $this->messages->M119;
                    $CODE = $CODES::SUCCESS;
                    $DATA="";
                    if($model->status==1){$DATA='enabled';}
                    elseif($model->status==0){$DATA='disabled';}                    
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
