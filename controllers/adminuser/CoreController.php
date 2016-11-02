<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use \app\models\Permissions;
use \app\facades\common\CommonFacade;
use \app\models\Pages;
use \app\facades\adminuser\CmsFacade;
use yii\web\UploadedFile;

class CoreController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
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
    
    public function beforeAction($e){
        $status = CommonFacade::authorize(Yii::$app->request);
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            return parent::beforeAction($e);
        }
        
    }  
    
    public function actionPages() {
        $request = Yii::$app->request->get();
        
        if($request){
            $facade = new \app\facades\adminuser\CoreFacade();
            $response = $facade->getPage($request);
            
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];

            if ($code == 200){
                $model = $response['DATA'];
                //if($model->layout == 1){
                //    return $this->render('main', array('page'=>$model));
                //} else {
                    return $this->renderPartial('main', array('page'=>$model));
                //}
            } else if($code == 100){
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        }
        
        
        /*
        $cat = '';
        if(isset($request['cat'])){
            $category = \app\models\Lookups::find()->select(['id'])->where(['value'=>$request['cat']])->one();
            if($category){
                $cat = $category->id;
            }
        }
        
        $url = '';
        if(isset($request['url'])){
            $url = $request['url'];
        }
        
        $page = Pages::find();
        $page->where(['status' => 550001]);

        if ($cat != '') {
            $page->andWhere(['=', 'category', $cat]);
        }
        if($url != ''){
            $page->andWhere(['=', 'url', $url]);
        }
        
        $model = $page->one();
        if(isset($model)){
            return $this->render('main', array('page'=>$model));
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        }
        
        
         * 
         */
    }
    
    
    public function actionPreview() {
        $request = Yii::$app->request;
        $content = $request->post('content');
        
        $title = $request->post('title');
        $layout = $request->post('layout');
        $hideTitle = $request->post('hideTitle');
        
        if($content){
            return $this->renderPartial('preview', array('layout'=>$layout, 'title'=>$title, 'content'=>$content, 'hideTitle'=>$hideTitle));
        }
    }
     
    
}
