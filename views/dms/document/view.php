<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\web\util\Codes\LookupCodes;
use app\web\util\Codes\LookupTypeCodes;

$this->title = \Yii::t('app', 'View');
$this->params['breadcrumbs'][] = $this->title;
?>


<style>
    .space-bottom:after {
        content: "";
        display: inline-block;
        clear: both;
    }
    .text-muted {
        display: inline-block;
        float: left;
        width: 49%;
        margin-right: 2%;
        margin-bottom: 10px;
    }
    .text-muted:nth-of-type(even) {
        margin-right: 0;
    }
    .my-title {
        display: inline-block;
        float: left;
        width: 49%;
        margin-right: 2%;
        font-weight: bold;
    }
    .my-info {
        display: inline-block;
        float: left;
        width: 49%;
    }

    @media screen and (max-width:768px) {
        .text-muted {
            float: none;
            width: 100%;
            margin-right: 0;
        }
    }
</style>

<!-- <div class="container"> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo \Yii::t('app', 'Documents');?>
            <small><?php echo \Yii::t('app', 'View');?></small>
        </h1>
        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>
    
    <?php 
        $department = 2300001;
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        if($userObj){
            if($userObj->department_id != ''){
                $department = $userObj->department_id;
            }
        }


    ?>

    <!-- Main content -->
    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <?php if($model->status == 2600002 && $model->document_type_id == 2500001){ ?>
                <a class="btn my-btn1 pull-left col-lg-2" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/addendum", 'id'=>$model->id]); ?>" class="btn btn-primary btn-flat pull-right">
                    <?php echo \Yii::t('app', 'Add Addendum');?>
                </a>
                <?php } ?>
            </div>
            <div class="box-body">
                <?= \Yii::$app->view->renderFile('@app/views/layouts/flash_message.php'); ?>
                <div class="row">
                    <div class="col-lg-2">
                         <div class="space-bottom">
                        <div class=" my-center1">
                        <?php 
                        $icon1 = '';
                        if($model->id != ''){
                            if($model->document_path != ''){
                                echo '<label>Document</label><br>';
                                $facade = new \app\facades\dms\DocumentFacade();
                                $icon1 = $facade->getIcon($model->document_type);
                                $path1 = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                                $download1 = '<a target="_blank" download="'.$model->name.'" href="'.$path1.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon1.' style="margin-left: 18px;" /></a>';

                                //$path1 = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id, 'type'=>'doc']);
                                //$download1 = '<a href="'.$path1.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon1.' style="margin-left: 0px;" /></a>';
                                echo $download1;
                            }
                            
                            if($model->scanned_document_path != ''){
                                echo '<label>Scanned Document</label>';
                                $facade1 = new \app\facades\dms\DocumentFacade();
                                $icon2 = $facade1->getIcon($model->scanned_document_type);
                                //$path2 = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id, 'type'=>'scanned']);
                                //$download2 = '<a href="'.$path2.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon2.' style="margin-left: 0px;" /></a>';
                                
                                $path2 = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                                $download2 = '<a target="_blank" download="'.$model->name.'" href="'.$path2.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon2.' style="margin-left: 18px;" /></a>';

                                
                                echo $download2;
                            }
                        }
                        ?>
                    </div>
                         </div>
                         <div class="col-lg-12">
                         
                             
                                 
                             <!--textarea>
                             </textarea-->
                             
                             
                        <?php if($userObj->user->role == 100001 || $userObj->user->role == 100005){ ?>
                            <?php if($model->status == 2600001){ ?>
                                <input id="rejection_reason" maxlength="1000" name="rejection_reason" />
                                <?php if($userObj->department_id == 2300001){ ?>
                                <a onclick="approveRejectDocument(2600002);" class="btn my-btn  pull-left">Approve</a> 
                                <a onclick="approveRejectDocument(2600003);" class="btn my-btn pull-right">Reject</a> 
                                <?php } else { ?>
                                    <?php if($model->department_id == $userObj->department_id){ ?>
                                    <a onclick="approveRejectDocument(2600002);" class="btn my-btn  pull-left">Approve</a> 
                                    <a onclick="approveRejectDocument(2600003);" class="btn btn-primary btn-flat pull-right">Reject</a> 
                                    <?php } ?>
                                <?php } ?>
                        <?php } } ?>            
                    </div>
                    </div>
                    
                    <div class="col-lg-10">
                       <div class="space-bottom"> <h3 class="profile-username"><?php echo \Yii::t('app', $model->name); ?>  <div class="edit-my"> <?php if($userObj->user->role == 100001 || $userObj->user->role == 100004 || $userObj->user->role == 100005){?>
                    <?php if($userObj->department_id == 2300001){ ?>
                        <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/edit", 'Id'=>$model->id]); ?>" name="" class="edit-icon"></a>                            
                    <?php } else { ?>
                        <?php if($model->department_id == $userObj->department_id){ ?>
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/edit", 'Id'=>$model->id]); ?>" name="" class="edit-icon"></a>                            
                        <?php } ?>
                    <?php } ?>
                <?php } ?>    </div></h3>
                         
                        
                        <?php if($model->document_type_id == 2500004){ ?>
                        <div class="text-muted">
                            <div class="my-title">
                                <?php echo \Yii::t('app', 'Parent'); ?>:
                            </div> 
                             <div class="my-info">
                                <a target="_blank" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$model->old_id]); ?>">
                                <?php echo $model->is_locked; ?>
                                </a>                            
                             </div>
                        </div>
                        <?php } ?>   
                                                
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Applicable To'); ?>:
                            </div> 
                             <div class="my-info">
                                    <?php foreach($selectedDepartmentList as $selected){ ?>
                                        <?php echo \Yii::t('app', $selected->department->value); ?></br>
                                    <?php } ?>
                             </div>
                        </div>
                                                
                                                
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Department'); ?>:
                            </div> 
                             <div class="my-info">
                            <?php echo \Yii::t('app', $model->department->value); ?>
                             </div></div>
                           
                           

			<div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Document Type'); ?>:
                             </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->documentType->value); ?>
                        </div>
                        </div>
                        
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Uploaded By'); ?>:
                            </div> 
                             <div class="my-info">
                            <?php echo \Yii::t('app', $model->created_by_name); ?>
                        </div>
                        </div>

                        
                       <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Uploaded On'); ?>:
                                </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', date("j M Y", strtotime($model->created_on))); ?>
                        </div>
                          </div>
                        
                        
                        
                        
                        
                        
                        <?php if($model->document_type_id == 2500001 || $model->document_type_id == 2500004){ ?>
                      <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Vendor Name'); ?>:
                             </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->vendor->name); ?>
                        </div>
                        </div>
                        
                       <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Vendor Code'); ?>:
                            </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->vendor->code); ?>
                       </div>
                        </div>
                        
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Agreement Type'); ?>:
                           </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->agreementType->value); ?>
                        </div>
                        </div>
                           
                       <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Fee'); ?>:
                           </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->fee); ?>
                        </div>
                        </div>
                        
                       
                        
                       <div class="text-muted">
                           <div class="my-title">
                           
                                <?php echo \Yii::t('app', 'Payment Term'); ?>:
                           </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->paymentTerms->value); ?>
                      </div>
                        </div>


                        <?php } ?>
                        
                        <?php if($model->document_type_id == 2500003){ ?>
                         <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Process name'); ?>:
                            </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->process_name); ?>
                        </div>
                        </div>
                        <?php } ?>
                        
                        
                        
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Valid From'); ?>:
                           </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', date("Y-m-d", strtotime($model->valid_from))); ?>
                         </div>
                        </div>
                        
                         <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Valid Till'); ?>:
                           </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', date("Y-m-d", strtotime($model->valid_till))); ?>
                        </div>
                        </div>
                        
			<?php if($model->document_type_id == 2500001 || $model->document_type_id == 2500004){ ?>
                      

			<div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Scope of Work'); ?>:
                           </div>
                            <div class="my-info">
                            <?php echo \Yii::t('app', $model->scope_of_work); ?>
                       </div>
                        </div>
                        <?php } ?>
                        
                        
                        
                        
                        <?php if($model->document_type_id == 2500002){ ?>
                        
                       <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Policy Header'); ?>:
                            </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->policy_header); ?>
                        </div>
                        </div>
                        
                        <?php } ?>
                        
                        <?php
                        
                        if($model->version >= 1){ ?>
                       <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Version'); ?>:
                            </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->version); ?><span>.0</span>
                        </div>
                        </div>
                        <?php } ?>
                        
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Comments'); ?>:
                           </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->comments); ?>
                        </div>
                        </div>
                        
                        
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Status'); ?>:
                            </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->status0->value); ?>
                        </div>
                        </div>
                        
                        <?php if($model->status == 2600003){ ?>
                        <div class="text-muted">
                           <div class="my-title">
                                <?php echo \Yii::t('app', 'Reason for Rejection'); ?>:
                            </div>
                            <div class="my-info"> 
                            <?php echo \Yii::t('app', $model->reason); ?>
                        </div>
                        </div>
                        <?php } ?>
                           
                       </div>
                        <?php if($userObj->user->role == 100001 || $userObj->user->role == 100004 || $userObj->user->role == 100005){?>
                        
                        
                        
                        
                        <?php 
                        
                        if(!empty($addendumList)){ 
                         ?>
                        
                        <h3 class="profile-username">
                            <?php echo \yii::t('app', 'Addendums');?>
                        </h3><br/>
                        
                        <div class="row">
                            <div class="col-lg-12 col-md-10">
                                <table width="100%" cellspacing="0" style="border: 1px solid #D8D8D8;" class="display my-shadow" id="listing">
                                    <thead>
                                        <tr>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Document Name</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Version</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Created On</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Created By</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Status</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">View</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Download</th>
                                            <?php //if($userObj->department_id == 2300001){ ?>
                                                <!--th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Action</th-->
                                            <?php //} else { ?>
                                                <?php //if($model->department_id == $userObj->department_id){ ?>
                                                    <!--th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Action</th-->
                                                <?php //} ?>
                                            <?php //} ?>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($addendumList as $version){ ?>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->name); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;">
                                                <?php
                                                    if($version->version >= 1){
                                                        echo \Yii::t('app', $version->version).'.0'; 
                                                    }
                                                ?>
                                            </td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo date("Y-m-d", strtotime($version->created_on)); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->createdBy->adminPersonals->first_name.' '.$version->createdBy->adminPersonals->last_name); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->status0->value); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to View">
                                                    View
                                                </a>
                                            </td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                <?php 
                                                $icon = '';
                                                if($version->id != ''){
                                                    if($version->document_path != ''){
                                                        $facade = new \app\facades\dms\DocumentFacade();
                                                        $icon = $facade->getIcon($version->document_type);
                                                        $path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$version->id, 'type'=>'doc']);
                                                        $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 0px;" /></a>';
                                                        echo $download;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <?php if($userObj->department_id == 2300001){ ?>
                                                <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                    <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/rollback", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to rollback">
                                                        Rollback
                                                    </a>
                                                </td>
                                            <?php } else { ?>
                                                <?php if($model->department_id == $userObj->department_id){ ?>
                                                    <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                        <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/rollback", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to rollback">
                                                            Rollback
                                                        </a>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                            
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                        
                        <?php } ?>
                        
                        
                        
                        
                        <?php 
                        
                        if(!empty($versionList)){ 
                         ?>
                        
                        <h3 class="profile-username">
                            <?php echo \yii::t('app', 'Version History');?>
                        </h3><br/>
                        
                        <div class="row">
                            <div class="col-lg-12 col-md-10">
                                <table width="100%" cellspacing="0" style="border: 1px solid #D8D8D8;" class="display my-shadow" id="listing">
                                    <thead>
                                        <tr>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Document Name</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Version</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Created On</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Created By</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Status</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">View</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Download</th>
                                            <?php if($userObj->department_id == 2300001){ ?>
                                                <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Action</th>
                                            <?php } else { ?>
                                                <?php if($model->department_id == $userObj->department_id){ ?>
                                                    <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Action</th>
                                                <?php } ?>
                                            <?php } ?>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($versionList as $version){ ?>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->name); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->version).'.0'; ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo date("Y-m-d", strtotime($version->created_on)); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->createdBy->adminPersonals->first_name.' '.$version->createdBy->adminPersonals->last_name); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->status0->value); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to rollback">
                                                    View
                                                </a>
                                            </td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                <?php 
                                                $icon = '';
                                                if($version->id != ''){
                                                    if($version->document_path != ''){
                                                        $facade = new \app\facades\dms\DocumentFacade();
                                                        $icon = $facade->getIcon($version->document_type);
                                                        $path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$version->id, 'type'=>'doc']);
                                                        $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 0px;" /></a>';
                                                        echo $download;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <?php if($userObj->department_id == 2300001){ ?>
                                                <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                    <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/rollback", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to rollback">
                                                        Rollback
                                                    </a>
                                                </td>
                                            <?php } else { ?>
                                                <?php if($model->department_id == $userObj->department_id){ ?>
                                                    <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                        <a href="<?php echo \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/rollback", 'Id'=>$version->id])?>" style="cursor: pointer;" title="Click to rollback">
                                                            Rollback
                                                        </a>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                            
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                        
                        <?php } ?>
                        
                        <?php } ?>
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            
            
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>" name="go-back" class="back-button"></a>         
               
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->

<script>
    function approveRejectDocument(status){
        var retVal = "";       
        var url = '<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update"]); ?>';
        var reason = $('#rejection_reason').val();
        var id = '<?php echo $model->id;?>'
        
        if(status == 2600002){
            retVal = confirm("Are you sure? You want to approve it.");
            if( retVal == true ){
                $.ajax({
                   type:'post',
                   data:{
                       id: id,
                       status: status,
                       reason: reason,
                   },
                   url : url,
                   success:function(status) {
                        alert('Approved successfully');
                        window.location="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$model->id]); ?>";
                    }
                });
            }
        } else if(status == 2600003){
            
            if(reason != ''){          
                retVal = confirm("Are you sure? You want to reject it.");
                if( retVal == true ){
                    $.ajax({
                       type:'post',
                       data:{
                           id: id,
                           status: status,
                           reason: reason,
                       },
                       url : url,
                        success:function(status) {
                            alert('Rejected successfully');
                           window.location="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$model->id]); ?>";
//                           /var parsedData = JSON.parse(status);

                        }
                    });
                }
            } else {
                alert('Please provide the reason for rejecting.');
            }
        }
        
        
        
    }
    </script>
