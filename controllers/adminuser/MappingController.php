<?php

namespace app\controllers\adminuser;

use Yii;
use yii\web\Controller;
use app\facades\adminuser\AdminFacade;

use \app\models\Permissions;
use \app\models\RolePermissions;
use \app\models\Lookups;
use \app\facades\common\CommonFacade;
use \app\web\util\Codes\LookupCodes;

class MappingController extends \yii\web\Controller {

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
    

      /*
     * function for role-permission mapping
     * @author: Waseem
     */
    public function actionAdd(){
        if(Yii::$app->request->post()){
            $request = Yii::$app->request->post();
            $facade = new AdminFacade();
            $response = $facade->createMapping($request);
            return json_encode($response);
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
        }
    }
    
   /*
     * function for getting list of roles
     * @author: Waseem
     */
    public function actionList() {
        $lang = CommonFacade::getLanguage();
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $id = Yii::$app->user->getId();
        if($id){
            $user = \app\models\Users::find()->select(['role', 'user_type'])->where(['id'=>$id, 'is_delete'=>1])->one();
            $list = Lookups::find()
                    ->where(['type' => 1, 'is_delete'=>1])
                    ->andWhere(['<=', 'parent_id', $user->user_type])
                    ->orderBy('id DESC')->all();
        }    
        
        
        
        
        return $this->render('mapping', array('model'=>$list, 'permission'=>$permission, 'lang'=>$lang));
    }
    
    /*
     * function for opening role-permission mapping in edit mode
     * @author: Waseem
     */
    
    
    public function actionEdit() {
        $mappingList = CommonFacade::getPermissions(Yii::$app->request);
        $userType = Yii::$app->user->identity->user_type;
        if($userType == LookupCodes::L_USER_TYPE_DEVELOPERS){
            $developer_admin_only = array(0, 1);
        } else {
            $developer_admin_only = 0;
        }
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $finalArray = array();

                /*
                $permissionTypeList = Lookups::find()->where(['type' => 11, 'is_delete'=>1])->orderBy('id DESC')->all();
                foreach($permissionTypeList as $type){
                    $permissionList = Permissions::find()->where(['permission_type' =>$type->id, 'is_delete'=>1])->all();


                    $tempArr1 = array();
                    foreach($permissionList as $perm){
                        if($perm->parent_id == ''){
                            $perm->parent_id = 0;
                        }
                        array_push($tempArr1, array('id' =>$perm->id, 'parent_id' => $perm->parent_id, 'value'=>$perm->value));
                    }
                    $tree = $this->buildTree($tempArr1);

                    $tempArr =  array('TypeId'=>$type->id, 'Name'=>$type->value, 'PermissionList'=>$tree);
                    array_push($finalArray, $tempArr);
                }
                echo "<pre>";print_r(json_encode($finalArray));die;
                return $this->render('Editmapping', array('model'=>$finalArray));

                */

                $role = Lookups::find()->where(['id' =>$id, 'is_delete'=>1])->one();
                if($role){
                    $permissionTypeList = Lookups::find()->where(['type' => 11, 'is_delete'=>1, 'status'=>  LookupCodes::L_COMMON_STATUS_ENABLED])->all();
                    foreach($permissionTypeList as $type){
                        $permissionList = Permissions::find()->where(['developer_admin_only'=>$developer_admin_only, 'permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>NULL])->all();
                        $finalArray1 = array();
                        foreach($permissionList as $key=>$permission){
                            $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$permission->id])->one();
                            if($obj){
                                $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                            } else {
                                $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                            }
                            if($permission->parent_id != ''){
                                $parent1 = $permission->parent->value;
                                $parentId1 = $permission->parent_id;
                            } else {
                                $parent1 = '';
                                $parentId1 = '';
                            }
                            
                            array_push($finalArray1, 
                                array(
                                'id'=>$permission->id, 
                                'value'=>$permission->value, 
                                'parent'=>$parent1,
                                'parentId'=>$parentId1,
                                'level'=>1,
                                "obj"=>
                                    array('mapping_id'=>$mappingId, 'change_status'=>$change_status,'add'=>$add, 'edit'=>$edit, 'default'=>$default, 'delete'=>$delete, 'view'=>$view, 'list'=>$viewList
                                    )
                                )
                            );
                            
                            $childList = Permissions::find()->where(['developer_admin_only'=>$developer_admin_only, 'permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>$permission->id])->all();
                            if($childList){
                                foreach($childList as $key=>$child){
                                    $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                    if($obj){
                                        $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                    } else {
                                        $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                    }
                                    if($child->parent_id != ''){
                                        $parent2 = $child->parent->value;
                                        $parentId2 = $child->parent_id;
                                    } else {
                                        $parent2 = '';
                                        $parentId2 = '';
                                    }

                                    array_push($finalArray1, 
                                        array(
                                        'id'=>$child->id, 
                                        'value'=>$child->value, 
                                        'parent'=>$parent2,
                                        'parentId'=>$parentId2,
                                        'level'=>2,
                                        "obj"=>
                                            array('mapping_id'=>$mappingId, 
                                                'change_status'=>$change_status,
                                                'add'=>$add, 
                                                'edit'=>$edit, 
                                                'default'=>$default,
                                                'delete'=>$delete, 
                                                'view'=>$view, 
                                                'list'=>$viewList
                                            )
                                        )
                                    );
                                    
                                    $subchildList = Permissions::find()->where(['developer_admin_only'=>$developer_admin_only, 'permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>$child->id])->all();
                                    if($subchildList){
                                        foreach($subchildList as $key=>$child){
                                            $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                            if($obj){
                                                $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                            } else {
                                                $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                            }
                                            if($child->parent_id != ''){
                                                $parent3 = $child->parent->value;
                                                $parentId3 = $child->parent_id;
                                            } else {
                                                $parent3 = '';
                                                $parentId3 = '';
                                            }

                                            array_push($finalArray1, 
                                                array(
                                                'id'=>$child->id, 
                                                'value'=>$child->value, 
                                                'level'=>3,
                                                'parent'=>$parent3,
                                                'parentId'=>$parentId3,
                                                "obj"=>
                                                    array('mapping_id'=>$mappingId, 
                                                        'change_status'=>$change_status,
                                                        'add'=>$add, 
                                                        'edit'=>$edit, 
                                                        'default'=>$default,
                                                        'delete'=>$delete, 
                                                        'view'=>$view, 
                                                        'list'=>$viewList
                                                    )
                                                )
                                            );
                                            
                                            $subchildList1 = Permissions::find()->where(['developer_admin_only'=>$developer_admin_only, 'permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>$child->id])->all();
                                            if($subchildList1){
                                                foreach($subchildList1 as $key=>$child){
                                                    $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                                    if($obj){
                                                        $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                                    } else {
                                                        $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                                    }
                                                    if($child->parent_id != ''){
                                                        $parent = $child->parent->value;
                                                        $parentId = $child->parent_id;
                                                    } else {
                                                        $parent = '';
                                                        $parentId = '';
                                                    }

                                                    array_push($finalArray1, 
                                                        array(
                                                        'id'=>$child->id, 
                                                        'value'=>$child->value, 
                                                        'level'=>3,
                                                        'parent'=>$parent,
                                                        'parentId'=>$parentId,
                                                        "obj"=>
                                                            array('mapping_id'=>$mappingId, 
                                                                'change_status'=>$change_status,
                                                                'add'=>$add, 
                                                                'edit'=>$edit, 
                                                                'default'=>$default,
                                                                'delete'=>$delete, 
                                                                'view'=>$view, 
                                                                'list'=>$viewList
                                                            )
                                                        )
                                                    );
                                                }
                                            }
                                            
                                        }
                                    }
                                    
                                }
                            }
                        }
                        

                        $tempArr =  array('id'=>$type->id, 'value'=>$type->value, 'PermissionList'=>$finalArray1);
                        array_push($finalArray, $tempArr);
                    }
                    return $this->render('Editmapping', array('model'=>$finalArray, 'role'=>$role, 'permission'=>$mappingList));
                } else {
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
                }
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
        }
    }
    
    public function actionView(){
        $mappingList = CommonFacade::getPermissions(Yii::$app->request);
                
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $finalArray = array();
                $role = Lookups::find()->where(['id' =>$id, 'is_delete'=>1])->one();
                if($role){
                    $permissionTypeList = Lookups::find()->where(['type' => 11, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED])->all();
                    foreach($permissionTypeList as $type){
                        
                        //////////////////////////////////////////////////////////////////////////////////////////
                        $permissionList = Permissions::find()->where(['permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>NULL])->all();
                        
                        $finalArray1 = array();
                        foreach($permissionList as $key=>$permission){
                            $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$permission->id])->one();
                            if($obj){
                                $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                            } else {
                                $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                            }
                            if($permission->parent_id != ''){
                                $parent1 = $permission->parent->value;
                                $parentId1 = $permission->parent_id;
                            } else {
                                $parent1 = '';
                                $parentId1 = '';
                            }
                            
                            array_push($finalArray1, 
                                array(
                                'id'=>$permission->id, 
                                'value'=>$permission->value, 
                                'parent'=>$parent1,
                                'parentId'=>$parentId1,
                                'level'=>1,
                                "obj"=>
                                    array('mapping_id'=>$mappingId, 'change_status'=>$change_status,'add'=>$add, 'edit'=>$edit, 'default'=>$default, 'delete'=>$delete, 'view'=>$view, 'list'=>$viewList
                                    )
                                )
                            );
                            
                            $childList = Permissions::find()->where(['permission_type' =>$type->id, 'is_delete'=>1, 'status'=>LookupCodes::L_COMMON_STATUS_ENABLED, 'parent_id'=>$permission->id])->all();
                            if($childList){
                                foreach($childList as $key=>$child){
                                    $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                    if($obj){
                                        $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                    } else {
                                        $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                    }
                                    if($child->parent_id != ''){
                                        $parent2 = $child->parent->value;
                                        $parentId2 = $child->parent_id;
                                    } else {
                                        $parent2 = '';
                                        $parentId2 = '';
                                    }

                                    array_push($finalArray1, 
                                        array(
                                        'id'=>$child->id, 
                                        'value'=>$child->value, 
                                        'parent'=>$parent2,
                                        'parentId'=>$parentId2,
                                        'level'=>2,
                                        "obj"=>
                                            array('mapping_id'=>$mappingId, 
                                                'change_status'=>$change_status,
                                                'add'=>$add, 
                                                'edit'=>$edit, 
                                                'default'=>$default,
                                                'delete'=>$delete, 
                                                'view'=>$view, 
                                                'list'=>$viewList
                                            )
                                        )
                                    );
                                    
                                    $subchildList = Permissions::find()->where(['permission_type' =>$type->id, 'is_delete'=>1, 'status'=>550001, 'parent_id'=>$child->id])->all();
                                    if($subchildList){
                                        foreach($subchildList as $key=>$child){
                                            $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                            if($obj){
                                                $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                            } else {
                                                $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                            }
                                            if($child->parent_id != ''){
                                                $parent3 = $child->parent->value;
                                                $parentId3 = $child->parent_id;
                                            } else {
                                                $parent3 = '';
                                                $parentId3 = '';
                                            }

                                            array_push($finalArray1, 
                                                array(
                                                'id'=>$child->id, 
                                                'value'=>$child->value, 
                                                'level'=>3,
                                                'parent'=>$parent3,
                                                'parentId'=>$parentId3,
                                                "obj"=>
                                                    array('mapping_id'=>$mappingId, 
                                                        'change_status'=>$change_status,
                                                        'add'=>$add, 
                                                        'edit'=>$edit, 
                                                        'default'=>$default,
                                                        'delete'=>$delete, 
                                                        'view'=>$view, 
                                                        'list'=>$viewList
                                                    )
                                                )
                                            );
                                            
                                            $subchildList1 = Permissions::find()->where(['permission_type' =>$type->id, 'is_delete'=>1, 'status'=>550001, 'parent_id'=>$child->id])->all();
                                            if($subchildList1){
                                                foreach($subchildList1 as $key=>$child){
                                                    $obj = RolePermissions::find()->where(['role_id' =>$id, 'is_delete'=>1, 'permission_id'=>$child->id])->one();
                                                    if($obj){
                                                        $default = $obj->default; $change_status = $obj->change_status; $add = $obj->add; $edit = $obj->edit; $delete = $obj->delete; $view = $obj->view ; $viewList = $obj->list; $mappingId = $obj->id;
                                                    } else {
                                                        $default=0; $change_status = 0; $add = 0; $edit = 0; $delete = 0; $view = 0; $viewList = 0; $mappingId = '';
                                                    }
                                                    if($child->parent_id != ''){
                                                        $parent = $child->parent->value;
                                                        $parentId = $child->parent_id;
                                                    } else {
                                                        $parent = '';
                                                        $parentId = '';
                                                    }

                                                    array_push($finalArray1, 
                                                        array(
                                                        'id'=>$child->id, 
                                                        'value'=>$child->value, 
                                                        'level'=>3,
                                                        'parent'=>$parent,
                                                        'parentId'=>$parentId,
                                                        "obj"=>
                                                            array('mapping_id'=>$mappingId, 
                                                                'change_status'=>$change_status,
                                                                'add'=>$add, 
                                                                'edit'=>$edit, 
                                                                'default'=>$default,
                                                                'delete'=>$delete, 
                                                                'view'=>$view, 
                                                                'list'=>$viewList
                                                            )
                                                        )
                                                    );
                                                }
                                            }
                                            
                                        }
                                    }
                                    
                                }
                            }
                        }
                        

                        $tempArr =  array('id'=>$type->id, 'value'=>$type->value, 'PermissionList'=>$finalArray1);
                        array_push($finalArray, $tempArr);
                    }
                    return $this->render('Viewmapping', array('model'=>$finalArray, 'role'=>$role, 'permission'=>$mappingList));
                } else {
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/mapping/list"));
        }
    }
    
     public function buildTree(array $elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
    
    
    
   
    
}
