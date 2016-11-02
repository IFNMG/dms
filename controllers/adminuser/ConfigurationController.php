<?php

namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 22-March-2016
 * @description: ConfigurationController interacts with facade for all configuration settings related activities
 */

use Yii;
use yii\web\Controller;
use app\facades\configuration\ConfigurationFacade;
use app\models\Configurations;
use app\web\util\Codes\LookupCodes;

class ConfigurationController extends \yii\web\Controller {

     public $enableCsrfValidation = false;
          public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                          'index','getchild','generatekey','save'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','getchild','generatekey','save'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
    
     public function beforeAction($e){
        
        $status = \app\facades\common\CommonFacade::authorize(Yii::$app->request);        
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        } else {
            return parent::beforeAction($e);
        }
    }
    
    public function actionIndex(){        
        
            $facade = new ConfigurationFacade();
            $response = $facade->listConfigurations();
            $menu_section_list=$facade->getMenuSectionList();
            $model= new Configurations();
            $data = $response['DATA']['SUBDATA'];                        
            return $this->render('Index', ['data' => $data,'model'=>$model,'menu_section_list'=>$menu_section_list]);
        
    }
    
    
    public function actionGetchild(){
       $getRequest = Yii::$app->request->post(); 
       $childConfigs= \app\models\Configurations::find()->select('id,name,value,short_code,hint')->where(['section'=>$getRequest['section_id']])->orderBy(['sort_order'=>'ASC'])->all();              
       if(!empty($childConfigs)){
           foreach ($childConfigs as $child){
               echo '<div class="clearfix">
                    <label class="pull-left">'.$child->name.': </label>'.
                    '<input rel="'.$child->short_code.'" id="val_'.$child->id.'" type="text" value="'.$child->value.'" class="myinput col-lg-3 col-lg-offset-1" placeholder="'.$child->name.'"/>'.
                     '<span style="color:#F39C12;font-size:12px;margin-left:10px;">'.
                          \yii::t('app', $child->hint).
                      '</span><br/>   
                    </div>';
               
           }
        }
    }
    
    public function actionGeneratekey(){
        $getRequest = Yii::$app->request->post(); 
        $autoId=$getRequest['id'];
        $autoType=$getRequest['type'];
        $facade = new ConfigurationFacade();
        $response = $facade->setApiStaticKey($autoId,$autoType);            
        if($response!=""){                      
            return $response;
        }
        return false;        
    }
    
    public function actionSave(){        
        $getRequest = Yii::$app->request->post();           
        $parent_id=$getRequest['parent_id'];
        
        $chk=Configurations::find()->select('developer_only')->where(['id'=>$parent_id])->all();      
        $chk1=$chk[0]->developer_only;        
        if($chk1==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
            return false; 
            
        }
            $contain=$getRequest['contain'];        
            //save model        
            foreach ($contain as $key=>$val){      
                
                $chk=Configurations::find()->select('developer_only')->where(['id'=>$key])->all();      
                $chk1=$chk[0]->developer_only;        
                if($chk1==1 && Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                }else{                
                $model= Configurations::findOne(['id'=>$key]);
                $result['value']=$val;
                $model->setAttributes($result);
                $model->save(); 
                }
            }       
            return true;
       
    }
    
  public function actionCancel(){        
        $getRequest = Yii::$app->request->post();           
        $parent_id=$getRequest['parent_id'];        
        //return values;
        $result=Configurations::find()->select(['id','value'])->where(['parent_id'=>$parent_id])->orWhere(['id'=>$parent_id])->orderBy(['sort_order'=>'ASC'])->all();        
        foreach($result as $key=>$value){
           // $res['result']['id']=$value->id;
            
            $res[$value->id]=$value->value;
          /*if($value->source_value!=""){
              // get value from lookup
              $lookResult=\app\models\Lookups::find()->select(['value'])->where(['id'=>$value->value])->one();             
              $res[$value->id]=$lookResult->value;              
          }
           * 
           */
            
        }
        $res=  json_encode($res);
        return $res;
    }  
    
}
