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
            <?php echo \Yii::t('app', 'Document Management');?>
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
                                }

                                $path1 = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                                $download1 = '<a target="_blank" href="'.$path1.'" style="cursor: pointer;" title="Click to download"><img width="70" height="80" alt="" src='.$icon1.' style="margin-left: 0px;" /></a>';
                                echo $download1;
                            }
                        }
                        ?>
                    </div>
                    
                    
                    <div class="col-lg-8">
                        <h3 class="profile-username text-center"><?php echo \Yii::t('app', $model->name); ?></h3><br/>
                        
                        
                                                
                        
                                                
                                                
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Department'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->department->value); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Uploaded By'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->created_by_name); ?>
                            <strong>
                                <?php echo \Yii::t('app', 'Department'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->createdBy->adminPersonals->department->value); ?>
                            <strong>
                                <?php echo \Yii::t('app', 'Role'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->createdBy->role0->value); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Uploaded On'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', date("j M Y, h:i:s", strtotime($model->created_on))); ?>
                        </p><br/>
                        
                        
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Document Type'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->documentType->value); ?>
                        </p><br/>
                        
                        
                        <?php if($model->document_type_id == 2500001){ ?>
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Vendor Name'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->vendor->name); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Vendor Code'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->vendor->code); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Fee'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->fee); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Scope of Work'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->scope_of_work); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Payment Term'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->payment_terms); ?>
                        </p><br/>
                        <?php } ?>
                        
                        <?php if($model->document_type_id == 2500003){ ?>
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Process name'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->process_name); ?>
                        </p><br/>
                        <?php } ?>
                        
                        
                        <?php if($model->document_type_id == 2500001 || $model->document_type_id == 2500002){ ?>
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Valid From'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', date("j M Y, h:i:s", strtotime($model->valid_from))); ?>
                        </p><br/>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Valid Till'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', date("j M Y, h:i:s", strtotime($model->valid_till))); ?>
                        </p><br/>
                        <?php } ?>
                        
                        
                        
                        
                        
                        <?php if($model->document_type_id == 2500002){ ?>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Policy Header'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->policy_header); ?>
                        </p><br/>
                        
                        <?php } ?>
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Version'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->version); ?><span>.0</span>
                        </p><br/>
                        
                        
                        <p class="text-muted text-center">
                            <strong>
                                <?php echo \Yii::t('app', 'Status'); ?>:
                            </strong> 
                            <?php echo \Yii::t('app', $model->status0->value); ?>
                        </p><br/>
                        
                        
                        
                        <?php 
                        
                        if(!empty($versionList)){ 
                         ?>
                        
                        <h3 class="profile-username text-center">
                            <?php echo \yii::t('app', 'Version History');?>
                        </h3><br/>
                        
                        
                        <div class="row">
                            <div class="col-lg-12 col-md-10">
                                <table width="100%" cellspacing="0" style="border: 1px solid #D8D8D8;" class="display" id="listing">
                                    <thead>
                                        <tr>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Id</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Version</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8;">Created By</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Download</th>
                                            <th style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($versionList as $version){ ?>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->id); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $version->version); ?></td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8;"><?php echo \Yii::t('app', $model->createdBy->adminPersonals->first_name.' '.$model->createdBy->adminPersonals->last_name); ?></td>
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
                                                        }

                                                        $path = \Yii::getAlias('@web') . '/uploads/times/'.$version->department->value.'/'.$version->document_path;
                                                        $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 0px;" /></a>';
                                                        echo $download;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td style="padding: 8px; border: 1px solid #D8D8D8; text-align: center;">
                                                <a target="_blank" href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/view", 'Id'=>$version->id]); ?>">
                                                    <?php echo \Yii::t('app', 'View');?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                        
                        <?php } ?>
                        
                        
                    </div>
                    <div class="col-lg-2">
                        <?php if($userObj->user->role == 100001 || $userObj->user->role == 100005){ ?>
                            <?php if($model->status == 2600001){ ?>
                                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600002]); ?>" class="btn btn-primary btn-flat pull-left">Approve</a> 
                                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/update", 'Id'=>$model->id, 'Status'=>2600003]); ?>" class="btn btn-primary btn-flat pull-right">Reject</a> 
                            <?php }  ?>
                        <?php } ?>
                    </div>
                </div>
            </div><!-- /.box-body -->
            
            <div class="box-footer">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/list"]); ?>" name="go-back" class="btn btn-success btn-flat pull-left">Go Back</a>         
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/edit", 'Id'=>$model->id]); ?>" name="edit-permission" class="btn btn-primary btn-flat pull-right">Edit</a>                            
            </div>
        </div><!-- /.box -->
    </section><!-- /.content -->
<!-- </div> -->
