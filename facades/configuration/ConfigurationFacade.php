<?php

namespace app\facades\configuration;

use Yii;
use app\models\Configurations;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\web\util\Codes\LookupCodes;

/**
 * @AUTHOR : Prachi
 * @DATE : 29-02-2016
 * @DESCRIPTION: For configuration functions
 */
class ConfigurationFacade {
    
    public $messages;           //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
     public function listConfigurations(){      
            $CODES = new Codes();
            if(Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                $data=  Configurations::find()->where(['is_delete'=>1,'parent_id'=>NULL,'developer_only'=>0])->orderBy(['menu_section'=>'SORT_ASC','sort_order'=>'SORT_ASC'])->all();            
            }
            else{
                $data=  Configurations::find()->where(['is_delete'=>1,'parent_id'=>NULL])->orderBy(['menu_section'=>'SORT_ASC','sort_order'=>'SORT_ASC'])->all();            
            }
            
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
    
    public function getMenuSectionList(){
        $section=$arr=array();
        if(Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
          $section=Configurations::find()->distinct('menu_section')->where(['is_delete'=>1,'parent_id'=>NULL,'developer_only'=>0])->orderBy(['menu_section'=>'SORT_ASC','name'=>'SORT_ASC'])->all();          
        }else{
          $section=Configurations::find()->distinct('menu_section')->where(['is_delete'=>1,'parent_id'=>NULL])->orderBy(['menu_section'=>'SORT_ASC','name'=>'SORT_ASC'])->all();              
        }
        
        foreach ($section as $k1=>$v1){
            $arr[$v1->menu_section]=$v1->menuSection->value;
        }     
        asort($arr);                
        return $arr;      
        
    }

    public static function setApiStaticKey($autoId,$autoType){       
        $value='';
        //get developer_only from auto-id
        $chk=Configurations::find()->select('developer_only')->where(['id'=>$autoId])->all();      
        $chk1=$chk[0]->developer_only; 
        if($chk1==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
            return false; 
            
        }
        if($autoType==LookupCodes::L_AUTO_GENERATE_GUID){
            $value=self::GUID();
        }       
        return $value;
    }
    
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    
}
