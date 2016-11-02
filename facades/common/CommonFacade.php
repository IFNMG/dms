<?php

namespace app\facades\common;

use Yii;
use \app\models\Lookups;
use \app\models\EmailTemplates;
use app\web\util\Codes\LookupCodes;
use \app\models\PackageQuestionMapping;
use \app\models\TaxPayingReason;
/**
 * @AUTHOR : Prachi
 * @DATE : 29-02-2016
 * @DESCRIPTION: For common functions
 */
class CommonFacade {
    
    /*
     * @AUTHOR: Waseem Khan
     * @DATE: 23-MAR-2016
     * @DESCRIPTION: Will check whether the user has permission to hit the requested controller.
     */

    
    
    public static function authorize($request) {
        if (Yii::$app->admin->isGuestAdmin) {
            return false;
        }         
        $id = Yii::$app->user->getId();
        
        if($id){
            $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1])->one();
            
            $url = Yii::$app->urlManager->parseRequest($request)[0];
            $permission = \app\models\Permissions::find()->where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->andWhere('url LIKE :query')->addParams([':query'=>"%$url%"])->one();
            
            //echo $permission->id;
            if($permission){
                $mapping = \app\models\RolePermissions::find()->where(["default"=>1, 'role_id'=>$user->role, 'is_delete'=>1, 'permission_id'=>$permission->id])->one();
                
              //  print_r($mapping);
                if($mapping){
                    return true;
                } else {
                    return false;
                }
            } else { 
                $arr = explode("/", $url, 3);
                $updatedUrl = $arr[0].'/'.$arr[1].'/list';                
                $action = Yii::$app->controller->action->id;
                
                if($action == "index") { $action = "list";}
                if($action == "create"){ $action = "add";}
                if($action == "update"){ $action = "edit";}
                if($action == 'activatedeactivate'){ $action = 'change_status';}
                
                $permission = \app\models\Permissions::find()->where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->andWhere('url LIKE :query')->addParams([':query'=>"%$updatedUrl%"])->one();
                if($permission){
                    if($action == 'list' || $action == 'add' || $action == 'edit' || $action == 'view' || $action == 'delete' || $action == 'change_status'){
                        $mapping = \app\models\RolePermissions::find()->where(["$action"=>1,'role_id'=>$user->role, 'is_delete'=>1, 'permission_id'=>$permission->id])->exists();
                        if($mapping){
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return true;
                        //return false;
                    }
                } else {
                    return true;
                    //return false;
                }
            }
        } else {
            false;
        }
    }

    /*
     * @AUTHOR: Waseem Khan
     * @DATE: 23-MAR-2016
     * @DESCRIPTION: Will check whether the user has permission to hit the requested controller.
     */

    public static function authorize_($request) {
        $id = Yii::$app->user->getId();
        if($id){
            $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1])->one();
            
            $url = Yii::$app->urlManager->parseRequest($request)[0];
            
            
            $arr = explode("/", $url, 3);
            //$updatedUrl = $arr[0].'/'.$arr[1].'/list';
            $updatedUrl = $arr[0].'/'.$arr[1];
            
            
            if(isset($arr[2])){
                $controller = $arr[2]; // for getting controller 
                if($controller == 'activatedeactivate'){
                    $controller = 'change_status';
                }
            } else {
                $controller = '';
            }
            
            $permission = \app\models\Permissions::find()->where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->andWhere('url LIKE :query')
                            ->addParams([':query'=>"%$updatedUrl%"])->one();
            if($permission){
                //get action code by prachi
                $action= Yii::$app->controller->action->id;
                if($action=="index"){$controller="list";}
                if($action=="create"){$controller="add";}
                if($action=="update"){$controller="edit";}
                if($action=="changestatus"){$controller="change_status";}
                  //EOF get action 
                
                if($controller == 'list' || $controller == 'add' || $controller == 'edit' || $controller == 'view' || $controller == 'delete' || $controller == 'change_status'){
                    $mapping = \app\models\RolePermissions::find()->where(["$controller"=>1,'role_id'=>$user->role, 'is_delete'=>1, 'permission_id'=>$permission->id])->exists();
                    if($mapping){
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            false;
        }
    }
    
    
    /*
     * @AUTHOR: Waseem Khan
     * @DATE: 23-MAR-2016
     * @DESCRIPTION: Will return permission granted to a controller
     */
    public static function getPermissions($request) {
        $id = Yii::$app->user->getId();
        if($id){
            $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1])->one();
            
            $url = Yii::$app->urlManager->parseRequest($request)[0];
            $arr = explode("/", $url, 3);
            //$updatedUrl = $arr[0].'/'.$arr[1].'/list';
            $updatedUrl = $arr[0].'/'.$arr[1];
            $permission = \app\models\Permissions::find()->where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->andWhere('url LIKE :query')->addParams([':query'=>"%$updatedUrl%"])->one();
            if($permission){
                $mapping = \app\models\RolePermissions::find()->where(['role_id'=>$user->role, 'is_delete'=>1, 'permission_id'=>$permission->id])->one();
                if($mapping){
                    return $mapping;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public static function getEmailXml($module) {
        $xmlFile = Yii::$app->params['PATH_XML_EMAIL'];
        $xml = simplexml_load_file($xmlFile);
        $myDataObjects = $xml->xpath('//module[@name="' . $module . '"]');
        foreach ($myDataObjects[0]->NodeId as $key => $node) {
            $val = (string) $node['id'];
            $myArr[$val] = (string) $node;
        }
        return $myArr;
    }

    public static function getMessagesXml($code) {
        $xmlFile = Yii::$app->params['PATH_XML_MESSAGES'];
        $xml = simplexml_load_file($xmlFile);
        return $xml->$code;
    }

    /*  Will return the whole messages XML
     *  Reduces execution time to many folds
     *  @author: Anjan
     *  @date:05/03/2015
     */

    public static function getMessages() {
        $xmlFile = Yii::$app->params['PATH_XML_MESSAGES'];
        $xml = (object) (array) simplexml_load_file($xmlFile);
        return $xml;
    }

    /*  Get user object returned from session
     *  @author: Anjan
     *  @date:05/03/2015
     */

    public static function getUser() {
        return Yii::$app->session->get('user');
    }

    /**
     * @AUTHOR:PRACHI
     * @DATE:08-MAR-2016
     * @DESCRIPTION: return current token for device
     * 
     */
    public static function setDeviceCurrentToken($deviceId, $userId = NULL) {
        $currTime = self::getCurrentDateTime();

        $token['deviceId'] = $deviceId;
        $token['currentTime'] = $currTime;


        if ($userId != "") {
            $token['userId'] = $userId;
        }
        
        $encryptedToken = self::encryptToken($token);
        return $encryptedToken;
    }

    /**
     * @AUTHOR:PRACHI
     * @DATE:08-MAR-2016
     * @DESCRIPTION: return current datetime
     * 
     */
    public static function getCurrentDateTime() {       
        date_default_timezone_set('UTC');
        return date("Y-m-d H:i:s");
    }

    /**
     * @AUTHOR:PRACHI
     * @DATE:08-MAR-2016
     * @DESCRIPTION: to encrypt/decrypt common function
     * 
     */
    public static function encryptToken($data = array()) {
        $salt = Yii::$app->params['MCRYPT_SALT'];
        if (!$data)
            return 0;
        $text = json_encode($data);
        
        //$token = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    public static function decryptToken($text = null) {
        $salt = Yii::$app->params['MCRYPT_SALT'];
        if (!$text)
            return 0;
        $data = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        return json_decode($data);
    }
    
    public static function decryptToken1($text = null) {
        
        $salt = Yii::$app->params['MCRYPT_SALT'];
        if (!$text)
            return 0;
        
        $data = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        
        $abc = json_decode($data);
        print_r($abc);die;
    }

    /**
     * @AUTHOR:PRACHI
     * @DATE:09-MAR-2016
     * @DESCRIPTION: set currrent token as previous and generate a new
     * 
     */
    public static function swapDeviceAuthorizedTokens($device_id, $user_id = NULL) {        
        $modelDevice = \app\models\Devices::find()->where(['device_id' => $device_id])->one();
        if ($user_id != "") {
            $currToken = self::setDeviceCurrentToken($device_id, $user_id);
        } else {
            $currToken = self::setDeviceCurrentToken($device_id);
        }
        Yii::error('TokeSwap0'.$modelDevice->current_token);
        
        Yii::error('TokeSwap1'.$currToken);
        
        $modelDevice->previous_token = $modelDevice->current_token;
        $modelDevice->current_token = $currToken;
        $modelDevice->modified_on = self::getCurrentDateTime();        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($modelDevice->save()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
                Yii::error($modelDevice->getErrors());                    
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());                    
        }
        return $currToken;
    }

    /**
     * @AUTHOR: PRACHI 
     * @DATE: 10-MAR-2016
     * @DESCRIPTION: get userid from Authorized Token
     */
    public function getUserIdFromAuthorizedToken($token) {
        $decryptToken = $this->decryptToken($token);
        $userId = $decryptToken->userId;
        return $userId;
    }

    /*
     * for getting lookup data by lookup id
     * @author: Waseem
     */

    public static function getLookupDataById($id) {
        $value = '';
        $lookup = Lookups::find()->where(['id' => $id, 'is_delete' => 1])->one();
        if ($lookup) {
            $value = $lookup->value;
        }
        return $value;
    }

    /**
     * @author Prachi
     * to get device id from headers 
     */
    public static function getDeviceIdFromHeaders() {
        $deviceId = "";
        $headers = apache_request_headers();
        if (isset($headers['DeviceId'])) {
            $deviceId = $headers['DeviceId'];
        }
        return $deviceId;
    }

    /*
     * @AUTHOR: Anjan
     * @DATE: 11-MAR-2016
     * @DESCRIPTION: Get access level of an user type based on it's own type.
     * Would always refer to lower level.
     */

    public function getOrderwiseUserTypes($data) {
        if (count($data) > 0 && $data['id']) {
            $userId = $data['id'];
            $user = \app\models\Users::find()->where(['id' => $userId])->one();
            if ($user) {
                $userType = $user->user_type;
                $userRole = $user->role;
                
                //For future use;
                //$alltypes = \app\models\Lookups::find()->where(['!=','id',$userType])->andWhere(['type'=>1,'is_delete'=>1])->orderBy(['info1'=>SORT_ASC])->all();
                
                if($userType == 150003){
                    $alltypes = \app\models\Lookups::find()->where(['is_delete' => 1, 'type'=>1])->orderBy(['info1' => SORT_ASC])->all();
                } else {
                    $alltypes = \app\models\Lookups::find()->where(['parent_id' =>$userType, 'is_delete' => 1])->andWhere(['>=', 'id', $userRole])->orderBy(['info1' => SORT_ASC])->all();
                }
                
                
                if ($alltypes) {
                    $response[''] = '--Select role--';
                    foreach ($alltypes AS $k => $v) {
                        $response[$v['id']] = $v['value'];
                    }
                }
                return $response;
            }
        } else {
            return false;
        }
        
        
    }
    
    /*
     * @AUTHOR: Anjan
     * @DATE: 11-MAR-2016
     * @DESCRIPTION: Get parent lookup id of any lookup id passed
     */
    
    
    public function getParentLookup($lookupId){
        $response = \app\models\Lookups::find()->where(['id'=>$lookupId])->one();
        if($response){
            return $response->parent_id;
        }else{
            return false;
        }
    }
    
    
    /*
     * @AUTHOR: Waseem Khan
     * @DATE: 29-MAR-2016
     * @DESCRIPTION: Get email template
     */
    
    
    public static function getEmailTemplate($eventId, $languageId){
        $template = EmailTemplates::find()->where(['event_id'=>$eventId, 'language'=>$languageId, 'is_delete'=>1])->one();
        if($template){
            return $template;
        } else {
            return false;
        }
    }
    
    
    /**
     * @AUTHOR:PRACHI
     * @DATE:04-APR-2016
     * @DESCRIPTION: return current date in defined Format
     * 
     */
    public static function getDisplayDateFormat() {
        $date_format="";
        $configuration=Yii::$app->params['configurations']['DATE_FORMAT'];
        if($configuration!=""){            
            $lookupVal=\app\models\Lookups::find()->select('value')->where(['id'=>$configuration])->one();          
            $date_format=$lookupVal->value;
        }
        
        if($date_format!=""){
            return $date_format;
        }
        return 'Y-m-d';
    }
    
    /**
     * @AUTHOR:PRACHI
     * @DATE:05-APR-2016
     * @DESCRIPTION: get current token for device
     * 
     */
    public static function getDeviceCurrentToken($deviceId) {
        $result=\app\models\Devices::find()->select('current_token')->where(['device_id'=>$deviceId])->one();
        if($result){
            return $result->current_token;
        }
    }
    
    /**
     * @AUTHOR:Waseem Khan
     * @DATE:06-APR-2016
     * @DESCRIPTION: get current active language
     * 
     */
    public static function getLanguage() {
        $lang = 'en';
        $config = \app\models\Configurations::find()->select('value')->where(['short_code'=>'LANGUAGE'])->one();
        if($config){
            $language = \app\models\Lookups::find()->select('description')->where(['id'=>$config->value])->one();
            if($language){
                $lang = $language->description;
            }
        } 
        return $lang;
    }
    
    /**
     * @AUTHOR:PRACHI
     * @DATE:05-APR-2016
     * @DESCRIPTION: get current token for device
     * 
     */
    public static function getLookupValueFromConfig($configuration) {
        $value="";        
        if($configuration!=""){            
            $lookupVal=\app\models\Lookups::find()->select('value')->where(['id'=>$configuration])->one();          
            $value=$lookupVal->value;
        }        
        return $value;

    }
    
    /**
     * @AUTHOR:PRACHI
     * @DATE:13-APR-2016
     * @DESCRIPTION: generate System Token
     * 
     */
    
    public function generateSystemToken($expiryTimeShortcode,$userId,$operation){
        
            $mins=Yii::$app->params['configurations'][$expiryTimeShortcode];
            if($mins){
                $mins = $mins;
            } else {
                $mins = 15;
            }

            $data['id'] = $userId;
            $data['validTill'] = strtotime("+$mins minutes", strtotime(date('Y-m-d H:i:s')));

            $forgotKey = self::encryptToken($data);
            $token = urlencode($forgotKey);

            $systemToken = new \app\models\SystemTokens();
            $systemToken->type = $operation;
            $systemToken->value = $token;
            $systemToken->creation_date_time = self::getCurrentDateTime();
            $systemToken->expiration_date_time = date('Y-m-d H:i:s', strtotime(self::getCurrentDateTime(). " + $mins minutes"));
            $systemToken->user_id = $userId;  
            $systemToken->status = 550001;  
            $systemToken->is_delete = 1;      
            $systemToken->created_on = self::getCurrentDateTime();
            $systemToken->created_by = $userId;      
            $systemToken->modified_on = self::getCurrentDateTime();
            $systemToken->modified_by = $userId;
            
            if($systemToken->save()){
                $STATUS=1;
                $CODE= \app\web\util\Codes\Codes::SUCCESS;
                $MSG="";
                $token=$token;
            } else {
                $STATUS = 0;
                $CODE = \app\web\util\Codes\Codes::ERROR;
                $MSG = $systemToken->getErrors();
                $token="";
            }
        return array('STATUS'=>$STATUS,'CODE'=>$CODE,'MESSAGE'=>$MSG,'TOKEN'=>$token);
        
    }
    
    public $parent = array();
    public function getParent($id){
        $lookuptype = \app\models\LookupTypes::find()->where(['parent_id' =>$id, 'is_delete'=>1])->all();
        if($lookuptype){
            foreach($lookuptype as $lk){
                array_push($this->parent, $lk->id);
                    $this->getParent($lk->id);
            }
        }
        return $this->parent;
    }
    
    public function getLookupDropDown($id) {
        
        $tempArr = $this->getParent($id);
        array_push($tempArr, $id);
        
        $permissionTypeList = Lookups::find()->where(['is_delete'=>1, 'type'=> $tempArr])->orderBy('value ASC')->all();
        
        $myarr = array();
        foreach($permissionTypeList as $type){
            array_push($myarr,array('id'=>$type->id, 'value'=>$type->value, 'parent_id'=>$type->parent_id));
            $tree = $this->buildTree($myarr);
        }
        $this->branch[''] = '--Select--';
        $tree1 = $this->showTree($tree);
        return $tree1;
    }
    
    
    public function getPermissionParent() {
        $parentList = \app\models\Permissions::find()->orderBy(['value' => SORT_ASC])->where(['is_delete'=>1, 'url'=>''])->all();
        $myarr = array();
        foreach($parentList as $type){
            array_push($myarr, array('id'=>$type->id, 'value'=>$type->value, 'parent_id'=>$type->parent_id));
            $tree = $this->buildTree($myarr);
        }
         $this->branch[''] = '--Select--';
        $parentTree = $this->showTree($tree);
        return $parentTree;
    }

    public $branch = array();
    function showTree($catList) {
        foreach ($catList as $row) {
            $id = $row['id'];
            $a = html_entity_decode(str_repeat('&nbsp;', $row['level']*5));
            $val = $a.Yii::t('app', $row['value']);
            
            $this->branch[$id] = $val;
            if (isset($row['children'])) {
                $this->showTree($row['children']);
            }
        }
        return $this->branch;
    } 
    
    
    
    public function buildTree(array $elements, $parentId = 0, $counter=0) {
        
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $element['level']=$counter;
                $children = $this->buildTree($elements, $element['id'], $counter+1);
            
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
    
    
    
    public function createMenu($permissionList) {
        $menuArr = array();
        foreach($permissionList as $perm){
            $permission = \app\models\Permissions::find()->where(['status'=>  LookupCodes::L_COMMON_STATUS_ENABLED,'id'=>$perm->permission_id, 'is_delete'=>1])->one();
            if($permission){
                if($permission->parent_id == null){
                    $parent = 0;
                } else {
                    $parent = $permission->parent_id;
                }
                 array_push($menuArr, array('new_window'=>$permission->is_new_window,
                     'id'=>$permission->id, 'value'=>$permission->value, 'url'=>$permission->url, 
                     'parent_id'=>$parent, 'sort_order'=>$permission->sort_order, 'image'=>$permission->image, 'display_option'=>$permission->display_option));
                 
              
                  usort($menuArr, function($a, $b) {
                        return $a['sort_order'] - $b['sort_order'];
                    });
                 
                 $tree = $this->buildTree($menuArr);
            }
        }
        
        
        $parentTree = $this->createMenuTree($tree);
        //print_r($parentTree);die;
        return $parentTree;
    }

    

    function createMenuTree($catList, $parentId=NULL, $level=NULL) {
        
        
        
        if($parentId  == ''){
            echo "<div class='collapse navbar-collapse pull-left' id='navbar-collapse'><ul class='nav navbar-nav'>";
        } else {
            if($level == 0){
                echo "<ul class='dropdown-menu' role='menu'>";
            } else {
                echo "<ul class='dropdown-menu-two' role='menu'>";
            }
        }
        
        foreach ($catList as $row) {
            $url = $row['url'];
            $value = \yii::t('app', $row['value']);
            
            if($row['new_window'] != 0){
                $childcss = 'target="_blank"';
            } else {
                $childcss = '';
            }
            
            if($row['display_option'] == LookupCodes::L_PERMISSION_DISPLAY_OPTIONS_NAME_ONLY){
                $name = $value;
            } else if($row['display_option'] == LookupCodes::L_PERMISSION_DISPLAY_OPTIONS_ICON_ONLY){
                $image = Yii::$app->params['UPLOAD_URL'].$row['image'];
                $name = '<img width="20" height=20" src='.Yii::$app->urlManager->createUrl($image).'>';
            } else if($row['display_option'] == LookupCodes::L_PERMISSION_DISPLAY_OPTIONS_NAME__ICON){
                $image = Yii::$app->params['UPLOAD_URL'].$row['image'];
                $name = $value.'<img width="20" height=20" src='.Yii::$app->urlManager->createUrl($image).'>';
            } else if($row['display_option'] == LookupCodes::L_PERMISSION_DISPLAY_OPTIONS_ICON__NAME){
                $image = Yii::$app->params['UPLOAD_URL'].$row['image'];
                $name = '<img width="20" height=20" src='.Yii::$app->urlManager->createUrl($image).'>'.$value;
            } else {
                $name =$value;
            }
            
            if($url  != ''){
                echo "<li class='dropdown'>". "<a href=".Yii::$app->getUrlManager()->createUrl([$url])." $childcss>".$name.'</a>';
            } else {
                if($row['level']  == 0){
                    echo "<li class='dropdown'><a class='dropdown-toggle' href='javascript:void(0);' data-toggle='dropdown'>".$name.'<span class="caret"></span></a>';
                } else {
                    echo "<li class='dropdown'><a class='dropdown-toggle' href='javascript:void(0);' data-toggle='dropdown'>".$name.'</a>';
                }
            }
        
        
            
            
            
            
            if (isset($row['children'])) {
                
                $this->createMenuTree($row['children'], $row['id'], $row['level']);
            }
            echo "</li>";
        }
        echo "</ul>";
        
    } 
    
     /**
     * @AUTHOR:PRACHI
     * @DATE:29-APR-2016
     * @DESCRIPTION: return UTC datetime
     * 
     */
    public static function getUTCDateTime($date_time,$format) {       
        date_default_timezone_set('UTC');
        return date($format,strtotime($date_time));    
    }
     
    
     /**
     * @AUTHOR:WASEEM KHAN
     * @DATE:17-JUNE-2016
     * @DESCRIPTION: Payment tier calculation logic on the basis of selected questions.
     * 
     */
    public function getPayment($assessmentRequest, $reasonArray) {       
        $lateFee = 0;
        $isFastTrack = 0;
        $propCount = 0;
        $packageArray = array();
        
        foreach ($reasonArray as $key=>$item){
            
            if(isset($key) && $key == 1650007){
                $property = TaxPayingReason::find()->select(['description'])->where(['request_id'=>$assessmentRequest->id, 'reason_id'=>$key])->one();
                $propCount = $property->description;
            }
            
            $packArr = PackageQuestionMapping::find()->select(['fee_package_id'])->where(['question_id'=>$key, 'is_selected'=>1])->all();
            if($packArr){
                $single = array();
                foreach($packArr as $pack){
                    array_push($single, $pack->feePackage->fee);
                }
                if($key == 1650007){
                    if($propCount <= 2){
                        $amount = min($single);
                    } else if($propCount == 3){
                        $amount = max($single);
                    } else {
                        $amount = min($single);
                    }
                } else {
                    $amount = min($single);
                }
                
                array_push($packageArray, $amount);
            }
            
        }   
        $finalPackageAmount = max($packageArray);
        
        if($assessmentRequest->is_fast_track == 1){
            $isFastTrack = 49;
        }
        
        $range = $this->check_in_range($assessmentRequest->created_on);
        
        if($range == 1){
            $lateFee = 29;
        } else if($range == 2){
            $lateFee = 49;
        } else if($range == 3){
            $lateFee = 79;
        }
        
        
        $lateFee = 0; // just to remove late fee from total as it was removed from SRS.
        
        $total = $lateFee+$finalPackageAmount+$isFastTrack;
        
        $finalArray = array('AssessmentId'=>$assessmentRequest->id, 'PackageAmount'=>$finalPackageAmount, 'LateFee'=>$lateFee, 'Total'=>$total, 'PropertyCount'=>$propCount);
        
        return $finalArray;
    }
    
    
    function check_in_range($date_from_user){
        $flag = 0;
        $year = date("Y");
        
        $user_ts = strtotime($date_from_user);
        if((($user_ts >= strtotime("$year-11-01")) && ($user_ts <= strtotime("$year-11-30")))){
            $flag = 1;
        } else if((($user_ts >= strtotime("$year-12-01")) && ($user_ts <= strtotime("$year-12-31")))){
            $flag = 2;
        } else if((($user_ts >= strtotime("$year-01-01")) && ($user_ts <= strtotime("$year-01-15")))){
            $flag = 3;
        }
        return $flag;
    }
    
     /*
     * function for generating random string
     * @author: Waseem
     */

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        $randomString .= time();
        return $randomString;
    }
    
     public static function formatBytes($bytes, $precision = 2) { 
        $format = ($format === NULL) ? '%01.2f %s' : (string) $format;

    // IEC prefixes (binary)
    if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $mod   = 1024;
    }
    // SI prefixes (decimal)
    else
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $mod   = 1000;
    }

    // Determine unit to use
    if (($power = array_search((string) $force_unit, $units)) === FALSE)
    {
        $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
    }

    return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
    } 
}

    
    /*
function showTree_pp($catList) {
        foreach ($catList as $row) {
            $id = $row['id'];
            echo '<option id='.$id.'>';
            if($row['parent_id'] == ''){
                echo $row['value'];
            } else {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row['value'];
            }
            
            if (isset($row['children'])) {
                $this->showTree($row['children']);
            }
            echo "</option>";
        }
        
    } 
     * 
     */


