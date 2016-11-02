<?php
namespace app\controllers\generators;

/**
 * @author: Prachi
 * @date: 16-March-2016
 * @description: LookupTypeController interacts with facade for all lookuptype related activities
 */

use Yii;
use yii\web\Controller;
use app\models\Lookups;
use app\web\util\Codes\LookupCodes;



class LookupGeneratorController extends \yii\web\Controller {
    
    public $enableCsrfValidation = false;
    
     public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index','create'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index','create'
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
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            if(Yii::$app->user->identity->user_type!=LookupCodes::L_USER_TYPE_DEVELOPERS){
                 $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
            }
            return parent::beforeAction($e);
        }
    }
    
    
   
    public function  actionRegenerate(){
        
        $class="LookupCodes";
        $file=  Yii::$app->basePath .'/web/util/Codes/'.$class.'.php';      
        //flush the file
        
        $f = @fopen($file, "r+");
        if ($f !== false) {
            ftruncate($f, 0);
            fclose($f);
        }
        
        
        $php_head ="<?php ";
        file_put_contents($file, $php_head.PHP_EOL , FILE_APPEND|LOCK_EX);
        
        $ns_head ="namespace app\web\util\Codes;
        /**
         *@AUTHOR: Prachi         
         *@REASON: use code instead of id  
         */";
        file_put_contents($file, $ns_head.PHP_EOL , FILE_APPEND|LOCK_EX);
         
        $head = "class ".$class."{";
        file_put_contents($file, $head.PHP_EOL , FILE_APPEND|LOCK_EX);
        
        
        $result=Lookups::find()->select(['id','value','type','short_code'])->all();        
        if(!empty($result)){
            $arr=array();$errCodes=array();
            foreach ($result as $key=>$value){
             $content=$short_code="";
             
             if($value->short_code==""){
                    $name=$value->type0->value;
                    $name.="_".$value->value;
                    $short_code=  self::getShortCode($name); 
             }else{
                 $short_code=$value->short_code;
                 if (preg_match('/[^A-Za-z0-9_ \-]/', $short_code))//   /^[A-Za-z0-9_]+$/
                    {                        
                        $errCodes[]=$value->short_code;
                        continue;
                    }       
             }
             
          
             
             if(in_array($short_code, $arr)){
                 $short_code=$short_code.'_'.$key;
             }
             $content="CONST ".$short_code ." = ".$value->id.';';
             file_put_contents($file, $content.PHP_EOL , FILE_APPEND|LOCK_EX);             
             $arr[]=$short_code;
             //chk if in table short_code is empty then update shortcode
             if($val->short_code==""){
             \app\models\Lookups::updateAll(['modified_on'=> \app\facades\common\CommonFacade::getCurrentDateTime(),'short_code'=>$short_code], "id = $value->id");                      
             }
             
            }
        }
        
        $foot="}";
       file_put_contents($file, $foot.PHP_EOL , FILE_APPEND|LOCK_EX);
       
       $php_foot ="?>";
       file_put_contents($file, $php_foot.PHP_EOL , FILE_APPEND|LOCK_EX);
       
       if(!empty($errCodes)){
            echo 'You have errors in below short codes: <br/>';       
            foreach($errCodes as $val){           
                echo $val.'<br/>';
            }
       }
       
    }
    
    public function getShortCode($value){
        //replace space with underscore
        $value=trim($value);
        $value=str_replace(" ", "_", $value);
        $value=str_replace("-", "_", $value);
        $value= preg_replace('/[^A-Za-z0-9_\-]/', '', $value); // Removes special chars.
        $value=strtoupper($value);
        return 'L_'.$value;
    }
}