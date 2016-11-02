<?php

namespace app\facades\adminuser;

/*
 * @author: Prachi
 * @date: 02-March-2016
 * @description: AdminFacade interacts with models for all basic user related activities Ex:Login,Register
 */

use Yii;

use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Pages;
use \app\web\util\Codes\LookupCodes;

class CmsFacade {

    public $messages;           //stores an instance of the messages XML file.
    public $isGuestAdmin;        //decides user is logged in(=0) or not(=1)
    public $adminId;             //returns looged in user's id. 

    public function __construct() {

        $session = Yii::$app->session;
        $this->messages = CommonFacade::getMessages();

        if ($session->get('user')) {

            $user = $session->get('user');
            $this->isGuestAdmin = 0;
            $this->adminId = $user->id;
        } else {

            $this->isGuestAdmin = 1;
            $this->adminId = null;
        }
    }

    public function createPage($data, $image=array()){
        $CODES = new Codes;
        $commonFacade= new CommonFacade();
                
        $path = "";
        $save_path = "";
        
        if(!empty($image)){
            $ext = end((explode(".", $image->name)));
            $avatar = Yii::$app->security->generateRandomString().".{$ext}";
            $path = Yii::$app->params['UPLOAD_PATH'].'pages/' . $avatar;
            $save_path="pages/".$avatar;
        }
        
        
        if($data['Pages']['id'] != ''){
            if($save_path != ""){
                $temp = $save_path;                  
            } else {
                unset($temp);
            }
              
            $id = $data['Pages']['id'];
            $model = Pages::find()->where(['id'=>$id])->one();
            if($save_path == ""){
                $temp = $model->image;
            }
            
            $model->modified_by = Yii::$app->admin->adminId;
            $model->modified_on = date('Y-m-d H:i:s');
        } else {
            if($save_path != ""){
                $temp = $save_path;                  
            } else {
                unset($temp);
            }
            $model = new Pages();
            $model->created_by = Yii::$app->admin->adminId;
            $model->created_on = date('Y-m-d H:i:s');
        }
        $model->status = LookupCodes::L_COMMON_STATUS_ENABLED;
        $model->is_delete = 1;
        $model->attributes = $data['Pages'];
        
        if(isset($temp)){
            $model->image = $temp;
        }
        if($data['Pages']['id'] != ''){
            $id = $data['Pages']['id'];
        } else {
            $id = '';
        }
        
        if($model->url != '' && $model->category != ''){
            $isExists = Pages::find()->where(['url'=>$model->url, 'category'=>$model->category])->one();
            if($isExists){
                if($isExists->id != $id){
                    $MSG = $this->messages->M126;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                }
            }
        } else if($model->url != '' && $model->category == ''){
            $isExists = Pages::find()->where(['url'=>$model->url])->one();
            if($isExists){
                if($isExists->id != $id){
                    $MSG = $this->messages->M126;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                }
            }
        }
        
        
        if ($model->save()) {
            
            if($path!=""){$image->saveAs($path);}
            $MSG = $this->messages->M115;
            $CODE = $CODES::SUCCESS;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        } else {
            
            $MSG = $this->messages->M116;
            $CODE = $CODES::ERROR;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        }
    }
    
    
     public function editPage($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Pages::find()->where(['id'=>$id, 'is_delete'=>1])->one();
            if($model){
                $MSG = $this->messages->M117;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
            } else {
                $MSG = $this->messages->M103;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
    public function deletePage($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Pages::find()->where(['id'=>$id])->one();
            
            if($model){
                
                if($model->category != ''){
                    $url = 'index.php/adminuser/core/pages/'.$model->category.'/'.$model->url;
                } else {
                    $url = 'index.php/adminuser/core/pages/'.$model->url;
                }
                
                $model->is_delete = 0;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = date('Y-m-d H:i:s');

                if($model->save()){
                    
                    if($url != ''){
                        $permission = \app\models\Permissions::find()->where(['url'=>$url])->one();
                        if($permission){
                            $permission->is_delete = 0;
                            $permission->save();
                        }
                    }
                    
                    $MSG = $this->messages->M120;
                    $CODE = $CODES::SUCCESS;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                } else {
                    $MSG = $this->messages->M103;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                }
            }
            
        }
    }
    
    
}
