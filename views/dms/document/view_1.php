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
                                if($model->document_type == 'application/vnd.oasis.opendocument.text' || $model->document_type == 'application/msword' || $model->document_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                                    $icon1 = '/dms/web/images/word.png';
                                } else if($model->document_type == 'application/pdf'){
                                    $icon1 = '/dms/web/images/pdf.png';
                                } else if($model->document_type == 'image/png'){
                                    $icon1 = '/dms/web/images/png.png';
                                } else if($model->document_type == 'image/jpeg' || $model->document_type == 'image/jpg'){
                                    $icon1 = '/dms/web/images/jpeg.png';
                                } else if($model->document_type == 'application/vnd.ms-excel'|| $model->document_type == 'application/vnd.oasis.opendocument.spreadsheet' || $model->document_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                                    $icon1 = '/dms/web/images/excel.png';
                                } else if($model->document_type == 'application/vnd.ms-powerpoint' || $model->document_type == 'application/vnd.oasis.opendocument.presentation'){
                                    $icon1 = '/dms/web/images/ppt.png';
                                }

                                //$path1 = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                                $path1 = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id]);
                                $download1 = '<a href="'.$path1.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon1.' style="margin-left: 0px;" /></a>';
                                echo $download1;
                            }
                        }
                        ?>
                    </div>
                         </div>
                         <div class="col-lg-12">
                        <?php if($userObj->user->role == 100001 || $userObj->user->role == 100005){ ?>
                            <?php if($model->status == 2600001){ ?>
                                <?php if($userObj->department_id == 2300001){ ?>
                                <a onclick="return confirm('Are you sure you want to approve it?')" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600002]); ?>" class="btn my-btn  pull-left">Approve</a> 
                                <a onclick="return confirm('Are you sure you want to reject it?')" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600003]); ?>" class="btn my-btn pull-right">Reject</a> 
                                <?php } else { ?>
                                    <?php if($model->department_id == $userObj->department_id){ ?>
                                    <a onclick="return confirm('Are you sure you want to approve it?')" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600002]); ?>" class="btn btn-primary btn-flat pull-left">Approve</a> 
                                    <a onclick="return confirm('Are you sure you want to reject it?')" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600003]); ?>" class="btn btn-primary btn-flat pull-right">Reject</a> 
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
                        
                        
                        
                        
                        
                        
                        <?php if($model->document_type_id == 2500001){ ?>
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
                        
                        
                        <?php if($model->document_type_id == 2500001 || $model->document_type_id == 2500002){ ?>
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
                        <?php } ?>
                        

			<?php if($model->document_type_id == 2500001){ ?>
                      

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
                        
                        <?php if($model->version >= 1){ ?>
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
                        
                       </div>
                        <?php if($userObj->user->role == 100001 || $userObj->user->role == 100004 || $userObj->user->role == 100005){?>
                        
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

                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Uploaded On</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Uploaded By</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Status</th>
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
                                                <?php 
                                                $icon = '';
                                                if($version->id != ''){
                                                    if($version->document_path != ''){
                                                        if($version->document_type == 'application/vnd.oasis.opendocument.text' || $version->document_type == 'application/msword' || $version->document_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                                                            $icon = '/dms/web/images/word.png';
                                                        } else if($version->document_type == 'application/pdf'){
                                                            $icon = '/dms/web/images/pdf.png';
                                                        } else if($version->document_type == 'image/png'){
                                                            $icon = '/dms/web/images/png.png';
                                                        } else if($version->document_type == 'image/jpeg' || $version->document_type == 'image/jpg'){
                                                            $icon = '/dms/web/images/jpeg.png';
                                                        } else if($model->document_type == 'application/vnd.ms-excel' || $version->document_type == 'application/vnd.oasis.opendocument.spreadsheet' || $version->document_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                                                            $icon = '/dms/web/images/excel.png';
                                                        } else if($model->document_type == 'application/vnd.ms-powerpoint' || $model->document_type == 'application/vnd.oasis.opendocument.presentation'){
                                    $icon = '/dms/web/images/ppt.png';
                                }

                                                        //$path = \Yii::getAlias('@web') . '/uploads/times/'.$version->department->value.'/'.$version->document_path;
                                                        $path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$version->id]);
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
                        
                        <?php } } ?>
                        
                        
                    </div>
                   
                </div>
            </div><!-- /.box-body -->
            
            
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>" name="go-back" class="back-button"></a>         
               
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
