<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;

class HomeController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        if (!Yii::$app->admin->isGuestAdmin) {
            return $this->render('Landing');
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/login"));
        }
    }

    
}
