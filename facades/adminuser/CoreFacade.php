<?php

namespace app\facades\adminuser;

/*
 * @author: Prachi
 * @date: 02-March-2016
 * @description: AdminFacade interacts with models for all basic user related activities Ex:Login,Register
 */

use Yii;
use app\models\Users;
use app\models\AdminPersonal;
use app\models\UserPersonalDetails;
use app\models\ChangePasswordForm;
use app\models\LoginForm;
use app\models\PasswordHistory;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Permissions;
use \app\models\RolePermissions;
use \app\models\EmailTemplates;
use \app\web\util\Codes\LookupCodes;

class CoreFacade {

    public $messages;           //stores an instance of the messages XML file.
    public $isGuestAdmin;        //decides user is logged in(=0) or not(=1)
    public $adminId;             //returns looged in user's id. 

    public function __construct() {

        $session = Yii::$app->session;
        $this->messages = CommonFacade::getMessages();

    }

    public function getPage($request) {
        
        $CODES = new Codes;
        
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
        
        $page = \app\models\Pages::find();
        $page->where(['status' => LookupCodes::L_COMMON_STATUS_ENABLED]);

        if ($cat != '') {
            $page->andWhere(['=', 'category', $cat]);
        }
        if($url != ''){
            $page->andWhere(['=', 'url', $url]);
        }
        
        $model = $page->one();
        if(isset($model)){
            $MSG = $this->messages->M108;
            $CODE = $CODES::SUCCESS;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        } else {
            $MSG = $this->messages->M104;
            $CODE = $CODES::ERROR;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
        }
        
        
    }

}
