<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document".
 *
 * @property integer $id
 * @property integer $old_id
 * @property string $name
 * @property integer $department_id
 * @property integer $document_type_id
 * @property integer $agreement_type_id
 * @property string $comments
 
 * @property integer $vendor_id
 * @property string $process_name
 * @property string $valid_from
 * @property string $valid_till
 * @property string $scope_of_work
 * @property integer $payment_terms
 * @property double $fee
 * @property string $policy_header
 * @property string $document_path
 * @property string $scanned_document_path
 * @property string $document_type
 * @property string $scanned_document_type
 * @property string $document_text
 * @property integer $document_size
 * @property string $scanned_document_size
 * @property resource $document
 * @property resource $scanned_document
 * @property string $version
 * @property integer $status
 * @property integer $is_delete
 * @property integer $created_by
 * @property string $created_by_name
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 * @property integer $approved_by
 * @property string $approved_on
 * @property integer $is_locked
 * @property integer $is_published
 * @property integer $owner_id
 *
 * @property Lookups $department
 * @property Vendor $vendor
 * @property Lookups $paymentTerms
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Users $owner
 * @property Lookups $documentType
 * @property Lookups $agreementType
 * @property Users $approvedBy
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_id', 'department_id', 'agreement_type_id','document_type_id', 'vendor_id', 'payment_terms', 'document_size', 'status', 'is_delete', 'created_by', 'modified_by', 'approved_by', 'is_locked', 'is_published', 'owner_id'], 'integer'],
            [['name', 'department_id', 'document_type_id', 'document_path', 'document_size', 'document', 'status', 'created_by', 'created_on'], 'required'],
            [['valid_from', 'valid_till', 'created_on', 'modified_on', 'approved_on'], 'safe'],
            [['fee'], 'number'],
            [['document_text', 'document'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['comments'], 'string', 'max' => 500],
            //[['reason'], 'string', 'max' => 1000],
            [['process_name', 'document_path'], 'string', 'max' => 250],
            [['policy_header'], 'string', 'max' => 50],
            [['scope_of_work'], 'string', 'min' => 25],
            [['document_type'], 'string', 'max' => 300],
            [['version'], 'string', 'max' => 100],
            
            [['document_path'], 'file', 'skipOnEmpty' => true, 
                'maxSize' => 5e+7, 
                'tooBig' => 'Maximum document upload size is 1 MB.'
            ],
            
            [['scanned_document_path'], 'file', 'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg', 
                //'maxSize' => 1000000, 
                'maxSize' => 5e+7, 
                'tooBig' => 'Maximum document upload size is 1 MB.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_id' => 'Old ID',
            'name' => 'Name',
            'department_id' => 'Department ID',
            'document_type_id' => 'Document Type ID',
            'agreement_type_id'=>'Document Type',
            'comments' => 'Comments',
            //'reason' => 'Reason',
            'vendor_id' => 'V ID',
            'process_name' => 'Process Name',
            'valid_from' => 'Valid From',
            'valid_till' => 'Valid Till',
            'scope_of_work' => 'Scope Of Work',
            'payment_terms' => 'Payment Terms',
            'fee' => 'Fee',
            'policy_header' => 'Header',
            'document_path' => 'Document Path',
            'document_type' => 'Document Type',
            'document_text' => 'Document Text',
            'document_size' => 'Document Size',
            'scanned_document_path' => 'Scanned Document',
            'scanned_document_type' => 'Scanned Document Type',
            'scanned_document_size' => 'Scanned Document Size',
            'document' => 'Document',
            'version' => 'Version',
            'status' => 'Status',
            'is_delete' => 'Is Delete',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'approved_by' => 'Approved By',
            'approved_on' => 'Approved On',
            'is_locked' => 'Is Locked',
            'is_published' => 'Is Published',
            'owner_id' => 'Owner ID',
            'created_by_name' => 'Created By Name'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(Vendor::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentTerms()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'payment_terms']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Users::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'document_type_id']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreementType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'agreement_type_id']);
    }
    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'approved_by']);
    }
}
