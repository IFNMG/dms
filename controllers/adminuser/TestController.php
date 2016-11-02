<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use app\facades\adminuser\AdminFacade;
use app\models\Users;
use app\models\LoginForm;
use app\models\ChangepasswordForm;
use app\models\ForgotpasswordForm;
use app\models\ResetpasswordForm;
use \app\models\Permissions;
use \app\models\RolePermissions;
use \app\models\Lookups;

class TestController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    /*
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate'
                            
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
     * 
     */
    
    /*
    public function beforeAction($e){
        
        $status = \app\facades\common\CommonFacade::authorize(Yii::$app->request);
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            return parent::beforeAction($e);
        }
    }  
     * 
     */
    

   
     /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionTest2(){
        
        
        $maxThreads = 5;
        echo 'Example of the multi-thread manager with ' . $maxThreads . ' threads' . PHP_EOL . PHP_EOL;
        $exampleTask = new Yii::$app->thread2;
        $multithreadManager = new Yii::$app->thread1;
        $cpt = 0;
        while (++$cpt <= 30)
        {
            $multithreadManager->start($exampleTask);
        }



        die;
        $threads = new Yii::$app->thread;
        $threads->newThread(dirname(__FILE__).'/file.php', array());
        while (false !== ($result = $threads->iteration())) {
            if (!empty($result)) {
                echo $result."\r\n";
            }
        }
        echo (date("H:i:s"));
        $end = microtime(true);
        echo "Execution time ".round($end - $start, 2)."\r\n";
        die;
        
        
        /*
        for ($i=0; $i<10; $i++) {
            // open ten processes
            for ($j=0; $j<10; $j++) {
                //$pipe[$j] = popen('script2.php', 'w');
                echo $i ;
            }
            echo "\n";
            
            //for ($j=0; $j<10; ++$j) {
                //pclose($pipe[$j]);
            //}
        }
        
         * 
         */
        //$model = new \app\data\AsyncOperation($arg);
    }
    
    
}
