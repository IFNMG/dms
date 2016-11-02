<?php
namespace app\controllers\adminuser;

/**
 * @author: Prachi
 * @date: 21-March-2016
 * @description: LogController for logs listing
 */

use Yii;
use yii\web\Controller;

class LogController extends \yii\web\Controller{
    
        public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'index'
                        ],
                'rules' => [
                    [
                        'actions' => [
                             'index'
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
            $lang = \app\facades\common\CommonFacade::getLanguage(); //for jquery datatable language settings
            $path=realpath(Yii::$app->basePath).'/runtime/logs';
            $pathUrl=Yii::$app->request->baseUrl.'/../runtime/logs';
            $files=\yii\helpers\FileHelper::findFiles($path);              
            $count=count($files);            
            //foreach ($files as $file){
            for($i=$count-1;$i>=0;$i--){
                $file=  str_replace($path, $pathUrl, $files[$i]);
                $filesData[$i]['Url']=$file;                
                $fileName=  str_replace($path,'', $files[$i]);
                $fileName=  str_replace('/','', $fileName);
                $filesData[$i]['Name']=$fileName;                
            }            
             return $this->render('Index', ['data' => $filesData, 'lang'=>$lang]);   
    }
    
}
