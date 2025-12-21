<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * InquiryMessage model - ข้อความในการสอบถาม
 *
 * @property int $id
 * @property int $inquiry_id
 * @property string $message
 * @property string $sender_type
 * @property int|null $sender_id
 * @property string|null $attachments
 * @property int $is_read
 * @property int|null $created_at
 *
 * @property Inquiry $inquiry
 * @property User $sender
 * @property array $attachmentsArray
 */
class InquiryMessage extends ActiveRecord
{
    const SENDER_CUSTOMER = 'customer';
    const SENDER_STAFF = 'staff';
    const SENDER_BOT = 'bot';
    const SENDER_SYSTEM = 'system';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%inquiry_message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inquiry_id', 'message', 'sender_type'], 'required'],
            [['inquiry_id', 'sender_id', 'is_read', 'created_at'], 'integer'],
            [['message', 'attachments'], 'string'],
            [['sender_type'], 'string', 'max' => 20],
            [['sender_type'], 'in', 'range' => [self::SENDER_CUSTOMER, self::SENDER_STAFF, self::SENDER_BOT, self::SENDER_SYSTEM]],
            ['is_read', 'default', 'value' => 0],
            [['inquiry_id'], 'exist', 'skipOnError' => true, 'targetClass' => Inquiry::class, 'targetAttribute' => ['inquiry_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inquiry_id' => 'รหัสสอบถาม',
            'message' => 'ข้อความ',
            'sender_type' => 'ประเภทผู้ส่ง',
            'sender_id' => 'รหัสผู้ส่ง',
            'attachments' => 'ไฟล์แนบ',
            'is_read' => 'อ่านแล้ว',
            'created_at' => 'เวลาส่ง',
        ];
    }

    /**
     * Gets query for [[Inquiry]].
     * @return \yii\db\ActiveQuery
     */
    public function getInquiry()
    {
        return $this->hasOne(Inquiry::class, ['id' => 'inquiry_id']);
    }

    /**
     * Gets query for [[Sender]] (User).
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        if ($this->sender_type === self::SENDER_STAFF) {
            return $this->hasOne(User::class, ['id' => 'sender_id']);
        }
        return null;
    }

    /**
     * Get sender name
     * @return string
     */
    public function getSenderName()
    {
        switch ($this->sender_type) {
            case self::SENDER_CUSTOMER:
                return $this->inquiry->customer ? $this->inquiry->customer->getDisplayName() : ($this->inquiry->contact_name ?: 'ลูกค้า');
            case self::SENDER_STAFF:
                return $this->sender ? $this->sender->getDisplayName() : 'พนักงาน';
            case self::SENDER_BOT:
                return '🤖 AI Bot';
            case self::SENDER_SYSTEM:
                return '⚙️ ระบบ';
            default:
                return 'ไม่ทราบ';
        }
    }

    /**
     * Get sender type list
     * @return array
     */
    public static function getSenderTypeList()
    {
        return [
            self::SENDER_CUSTOMER => 'ลูกค้า',
            self::SENDER_STAFF => 'พนักงาน',
            self::SENDER_BOT => 'AI Bot',
            self::SENDER_SYSTEM => 'ระบบ',
        ];
    }

    /**
     * Get sender type text
     * @return string
     */
    public function getSenderTypeText()
    {
        $list = self::getSenderTypeList();
        return $list[$this->sender_type] ?? 'ไม่ทราบ';
    }

    /**
     * Get sender type badge
     * @return string
     */
    public function getSenderTypeBadge()
    {
        $badges = [
            self::SENDER_CUSTOMER => '<span class="badge bg-info">ลูกค้า</span>',
            self::SENDER_STAFF => '<span class="badge bg-primary">พนักงาน</span>',
            self::SENDER_BOT => '<span class="badge bg-warning">AI Bot</span>',
            self::SENDER_SYSTEM => '<span class="badge bg-secondary">ระบบ</span>',
        ];
        return $badges[$this->sender_type] ?? '<span class="badge bg-light text-dark">ไม่ทราบ</span>';
    }

    /**
     * Get attachments as array
     * @return array
     */
    public function getAttachmentsArray()
    {
        return $this->attachments ? json_decode($this->attachments, true) : [];
    }

    /**
     * Set attachments from array
     * @param array $files
     */
    public function setAttachmentsArray($files)
    {
        $this->attachments = !empty($files) ? json_encode($files, JSON_UNESCAPED_UNICODE) : null;
    }

    /**
     * Add attachment
     * @param array $file ['name' => string, 'path' => string, 'type' => string, 'size' => int]
     */
    public function addAttachment($file)
    {
        $attachments = $this->getAttachmentsArray();
        $attachments[] = $file;
        $this->setAttachmentsArray($attachments);
    }

    /**
     * Check if message is from staff
     * @return bool
     */
    public function isFromStaff()
    {
        return $this->sender_type === self::SENDER_STAFF;
    }

    /**
     * Check if message is from customer
     * @return bool
     */
    public function isFromCustomer()
    {
        return $this->sender_type === self::SENDER_CUSTOMER;
    }

    /**
     * Check if message is from bot
     * @return bool
     */
    public function isFromBot()
    {
        return $this->sender_type === self::SENDER_BOT;
    }

    /**
     * Mark as read
     * @return bool
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->is_read = 1;
            return $this->save(false, ['is_read']);
        }
        return true;
    }

    /**
     * Create a message
     * 
     * @param int $inquiryId
     * @param string $message
     * @param string $senderType
     * @param int|null $senderId
     * @param array $attachments
     * @return InquiryMessage|null
     */
    public static function createMessage($inquiryId, $message, $senderType, $senderId = null, $attachments = [])
    {
        $model = new self();
        $model->inquiry_id = $inquiryId;
        $model->message = $message;
        $model->sender_type = $senderType;
        $model->sender_id = $senderId;
        
        if (!empty($attachments)) {
            $model->setAttachmentsArray($attachments);
        }
        
        if ($model->save()) {
            // Update inquiry last_message_at
            $inquiry = Inquiry::findOne($inquiryId);
            if ($inquiry) {
                $inquiry->last_message_at = date('Y-m-d H:i:s');
                
                // If customer sends message, set status to waiting if closed
                if ($senderType === self::SENDER_CUSTOMER && $inquiry->status === 'closed') {
                    $inquiry->status = 'open';
                }
                
                $inquiry->save(false);
            }
            
            return $model;
        }
        
        return null;
    }

    /**
     * Create system message
     * 
     * @param int $inquiryId
     * @param string $message
     * @return InquiryMessage|null
     */
    public static function createSystemMessage($inquiryId, $message)
    {
        return self::createMessage($inquiryId, $message, self::SENDER_SYSTEM);
    }

    /**
     * Create bot message
     * 
     * @param int $inquiryId
     * @param string $message
     * @return InquiryMessage|null
     */
    public static function createBotMessage($inquiryId, $message)
    {
        return self::createMessage($inquiryId, $message, self::SENDER_BOT);
    }

    /**
     * Get formatted time
     * @return string
     */
    public function getFormattedTime()
    {
        $timestamp = $this->created_at;
        $now = time();
        $diff = $now - $timestamp;
        
        if ($diff < 60) {
            return 'เมื่อสักครู่';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return "{$mins} นาทีที่แล้ว";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "{$hours} ชั่วโมงที่แล้ว";
        } elseif (date('Y-m-d', $timestamp) === date('Y-m-d', strtotime('-1 day'))) {
            return 'เมื่อวาน ' . date('H:i', $timestamp);
        } else {
            return date('d/m/Y H:i', $timestamp);
        }
    }

    /**
     * After save - update inquiry unread count
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            // If message is from customer or bot, increment staff unread count
            // This can be extended based on business logic
        }
    }
}
