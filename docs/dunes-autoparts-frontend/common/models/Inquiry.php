<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Inquiry Model - การสอบถาม/แชทจากลูกค้า
 * 
 * ใช้สำหรับเก็บข้อมูลการสนทนากับลูกค้าผ่านช่องทางต่างๆ
 * และรองรับการทำงานร่วมกับ AI Chatbot
 *
 * @property int $id
 * @property string $inquiry_number
 * @property int|null $customer_id
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_line_id
 * @property string $channel
 * @property string $status
 * @property string|null $priority
 * @property string|null $subject
 * @property string|null $vehicle_info
 * @property string|null $requested_parts
 * @property float|null $quoted_amount
 * @property int|null $converted_order_id
 * @property int|null $assigned_to
 * @property string|null $notes
 * @property int|null $closed_at
 * @property string|null $closed_reason
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 * @property Order $convertedOrder
 * @property User $assignedTo
 * @property InquiryMessage[] $messages
 */
class Inquiry extends ActiveRecord
{
    // Inquiry Channels
    const CHANNEL_LINE = 'line';
    const CHANNEL_FACEBOOK = 'facebook';
    const CHANNEL_WEBSITE = 'website';
    const CHANNEL_PHONE = 'phone';
    const CHANNEL_WALK_IN = 'walk_in';

    // Inquiry Status
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_QUOTED = 'quoted';
    const STATUS_CONVERTED = 'converted';
    const STATUS_CLOSED = 'closed';

    // Priority
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%inquiry}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel'], 'required'],
            [['customer_id', 'converted_order_id', 'assigned_to', 'closed_at'], 'integer'],
            [['vehicle_info', 'requested_parts', 'notes'], 'string'],
            [['quoted_amount'], 'number'],
            [['inquiry_number'], 'string', 'max' => 20],
            [['customer_name', 'subject', 'closed_reason'], 'string', 'max' => 255],
            [['customer_phone'], 'string', 'max' => 20],
            [['customer_line_id'], 'string', 'max' => 100],
            [['channel', 'status'], 'string', 'max' => 20],
            [['priority'], 'string', 'max' => 10],
            [['inquiry_number'], 'unique'],
            [['channel'], 'in', 'range' => array_keys(self::getChannels())],
            [['status'], 'default', 'value' => self::STATUS_OPEN],
            [['priority'], 'default', 'value' => self::PRIORITY_NORMAL],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inquiry_number' => 'เลขที่การสอบถาม',
            'customer_id' => 'ลูกค้า',
            'customer_name' => 'ชื่อลูกค้า',
            'customer_phone' => 'เบอร์โทร',
            'customer_line_id' => 'Line ID',
            'channel' => 'ช่องทาง',
            'status' => 'สถานะ',
            'priority' => 'ความสำคัญ',
            'subject' => 'หัวข้อ',
            'vehicle_info' => 'ข้อมูลรถ',
            'requested_parts' => 'อะไหล่ที่ต้องการ',
            'quoted_amount' => 'ยอดเสนอราคา',
            'converted_order_id' => 'ออเดอร์ที่สร้าง',
            'assigned_to' => 'ผู้รับผิดชอบ',
            'notes' => 'หมายเหตุ',
            'closed_at' => 'ปิดเมื่อ',
            'closed_reason' => 'เหตุผลปิด',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->inquiry_number)) {
                $this->inquiry_number = $this->generateInquiryNumber();
            }
            return true;
        }
        return false;
    }

    // =====================================================
    // Relations
    // =====================================================

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getConvertedOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'converted_order_id']);
    }

    public function getAssignedTo()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_to']);
    }

    public function getMessages()
    {
        return $this->hasMany(InquiryMessage::class, ['inquiry_id' => 'id'])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    // =====================================================
    // Static Lists
    // =====================================================

    public static function getChannels()
    {
        return [
            self::CHANNEL_LINE => 'Line',
            self::CHANNEL_FACEBOOK => 'Facebook',
            self::CHANNEL_WEBSITE => 'Website',
            self::CHANNEL_PHONE => 'โทรศัพท์',
            self::CHANNEL_WALK_IN => 'Walk-in',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'เปิด',
            self::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
            self::STATUS_QUOTED => 'เสนอราคาแล้ว',
            self::STATUS_CONVERTED => 'สั่งซื้อแล้ว',
            self::STATUS_CLOSED => 'ปิด',
        ];
    }

    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'ต่ำ',
            self::PRIORITY_NORMAL => 'ปกติ',
            self::PRIORITY_HIGH => 'สูง',
        ];
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    /**
     * Get channel label
     */
    public function getChannelLabel()
    {
        return self::getChannels()[$this->channel] ?? $this->channel;
    }

    /**
     * Get channel badge
     */
    public function getChannelBadge()
    {
        $badges = [
            self::CHANNEL_LINE => '<span class="badge bg-success"><i class="fab fa-line"></i> Line</span>',
            self::CHANNEL_FACEBOOK => '<span class="badge bg-primary"><i class="fab fa-facebook"></i> Facebook</span>',
            self::CHANNEL_WEBSITE => '<span class="badge bg-info"><i class="fas fa-globe"></i> Website</span>',
            self::CHANNEL_PHONE => '<span class="badge bg-warning"><i class="fas fa-phone"></i> โทรศัพท์</span>',
            self::CHANNEL_WALK_IN => '<span class="badge bg-secondary"><i class="fas fa-store"></i> Walk-in</span>',
        ];
        return $badges[$this->channel] ?? '<span class="badge bg-secondary">' . $this->channel . '</span>';
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_OPEN => '<span class="badge bg-info">เปิด</span>',
            self::STATUS_IN_PROGRESS => '<span class="badge bg-primary">กำลังดำเนินการ</span>',
            self::STATUS_QUOTED => '<span class="badge bg-warning">เสนอราคาแล้ว</span>',
            self::STATUS_CONVERTED => '<span class="badge bg-success">สั่งซื้อแล้ว</span>',
            self::STATUS_CLOSED => '<span class="badge bg-secondary">ปิด</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel()
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * Get priority badge
     */
    public function getPriorityBadge()
    {
        $badges = [
            self::PRIORITY_LOW => '<span class="badge bg-secondary">ต่ำ</span>',
            self::PRIORITY_NORMAL => '<span class="badge bg-info">ปกติ</span>',
            self::PRIORITY_HIGH => '<span class="badge bg-danger">สูง</span>',
        ];
        return $badges[$this->priority] ?? '<span class="badge bg-secondary">' . $this->priority . '</span>';
    }

    /**
     * Generate inquiry number
     */
    protected function generateInquiryNumber()
    {
        $prefix = 'INQ';
        $date = date('ymd');

        $lastInquiry = self::find()
            ->where(['like', 'inquiry_number', $prefix . $date])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastInquiry) {
            preg_match('/(\d+)$/', $lastInquiry->inquiry_number, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get vehicle info array
     */
    public function getVehicleInfoArray()
    {
        if (empty($this->vehicle_info)) {
            return [];
        }
        try {
            return Json::decode($this->vehicle_info);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set vehicle info from array
     */
    public function setVehicleInfoArray($info)
    {
        $this->vehicle_info = Json::encode($info);
    }

    /**
     * Get requested parts array
     */
    public function getRequestedPartsArray()
    {
        if (empty($this->requested_parts)) {
            return [];
        }
        try {
            return Json::decode($this->requested_parts);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set requested parts from array
     */
    public function setRequestedPartsArray($parts)
    {
        $this->requested_parts = Json::encode($parts);
    }

    /**
     * Get customer display name
     */
    public function getCustomerDisplayName()
    {
        if ($this->customer) {
            return $this->customer->full_name;
        }
        return $this->customer_name ?: 'ลูกค้าไม่ระบุชื่อ';
    }

    /**
     * Get customer contact info
     */
    public function getCustomerContact()
    {
        $contacts = [];
        if ($this->customer_phone) {
            $contacts[] = $this->customer_phone;
        }
        if ($this->customer_line_id) {
            $contacts[] = 'Line: ' . $this->customer_line_id;
        }
        return implode(' | ', $contacts);
    }

    /**
     * Add message
     */
    public function addMessage($message, $senderType = 'customer', $senderId = null, $isAutoReply = false, $attachments = null)
    {
        $inquiryMessage = new InquiryMessage([
            'inquiry_id' => $this->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $message,
            'is_auto_reply' => $isAutoReply,
            'attachments' => $attachments ? Json::encode($attachments) : null,
        ]);

        if ($inquiryMessage->save()) {
            // Update inquiry status if still open
            if ($this->status === self::STATUS_OPEN && $senderType === 'staff') {
                $this->status = self::STATUS_IN_PROGRESS;
                $this->save(false, ['status', 'updated_at']);
            }
            return $inquiryMessage;
        }
        return null;
    }

    /**
     * Get last message
     */
    public function getLastMessage()
    {
        return InquiryMessage::find()
            ->where(['inquiry_id' => $this->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }

    /**
     * Mark as quoted
     */
    public function markAsQuoted($amount)
    {
        $this->status = self::STATUS_QUOTED;
        $this->quoted_amount = $amount;
        return $this->save(false, ['status', 'quoted_amount', 'updated_at']);
    }

    /**
     * Convert to order
     */
    public function convertToOrder($orderId)
    {
        $this->status = self::STATUS_CONVERTED;
        $this->converted_order_id = $orderId;
        return $this->save(false, ['status', 'converted_order_id', 'updated_at']);
    }

    /**
     * Close inquiry
     */
    public function close($reason = null)
    {
        $this->status = self::STATUS_CLOSED;
        $this->closed_at = time();
        $this->closed_reason = $reason;
        return $this->save(false, ['status', 'closed_at', 'closed_reason', 'updated_at']);
    }

    /**
     * Assign to staff
     */
    public function assignTo($userId)
    {
        $this->assigned_to = $userId;
        return $this->save(false, ['assigned_to', 'updated_at']);
    }

    /**
     * Link to customer
     */
    public function linkToCustomer($customerId)
    {
        $this->customer_id = $customerId;
        return $this->save(false, ['customer_id', 'updated_at']);
    }

    /**
     * Create or find customer from inquiry info
     */
    public function findOrCreateCustomer()
    {
        if ($this->customer_id) {
            return $this->customer;
        }

        // Try to find by phone
        if ($this->customer_phone) {
            $customer = Customer::find()
                ->where(['phone' => $this->customer_phone])
                ->one();
            
            if ($customer) {
                $this->linkToCustomer($customer->id);
                return $customer;
            }
        }

        // Try to find by Line ID
        if ($this->customer_line_id) {
            $customer = Customer::find()
                ->where(['line_id' => $this->customer_line_id])
                ->one();
            
            if ($customer) {
                $this->linkToCustomer($customer->id);
                return $customer;
            }
        }

        // Create new customer if we have enough info
        if ($this->customer_name && $this->customer_phone) {
            $customer = new Customer([
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
                'line_id' => $this->customer_line_id,
            ]);

            if ($customer->save()) {
                $this->linkToCustomer($customer->id);
                return $customer;
            }
        }

        return null;
    }

    // =====================================================
    // Scopes
    // =====================================================

    public static function find()
    {
        return new InquiryQuery(get_called_class());
    }
}

/**
 * Inquiry Query Class
 */
class InquiryQuery extends \yii\db\ActiveQuery
{
    public function open()
    {
        return $this->andWhere(['status' => Inquiry::STATUS_OPEN]);
    }

    public function inProgress()
    {
        return $this->andWhere(['status' => Inquiry::STATUS_IN_PROGRESS]);
    }

    public function quoted()
    {
        return $this->andWhere(['status' => Inquiry::STATUS_QUOTED]);
    }

    public function converted()
    {
        return $this->andWhere(['status' => Inquiry::STATUS_CONVERTED]);
    }

    public function closed()
    {
        return $this->andWhere(['status' => Inquiry::STATUS_CLOSED]);
    }

    public function active()
    {
        return $this->andWhere(['not in', 'status', [Inquiry::STATUS_CONVERTED, Inquiry::STATUS_CLOSED]]);
    }

    public function byChannel($channel)
    {
        return $this->andWhere(['channel' => $channel]);
    }

    public function byAssignee($userId)
    {
        return $this->andWhere(['assigned_to' => $userId]);
    }

    public function unassigned()
    {
        return $this->andWhere(['assigned_to' => null]);
    }

    public function highPriority()
    {
        return $this->andWhere(['priority' => Inquiry::PRIORITY_HIGH]);
    }

    public function today()
    {
        $start = strtotime('today');
        $end = strtotime('tomorrow') - 1;
        return $this->andWhere(['between', 'created_at', $start, $end]);
    }

    public function search($keyword)
    {
        return $this->andWhere([
            'or',
            ['like', 'inquiry_number', $keyword],
            ['like', 'customer_name', $keyword],
            ['like', 'customer_phone', $keyword],
            ['like', 'customer_line_id', $keyword],
            ['like', 'subject', $keyword],
        ]);
    }
}

/**
 * InquiryMessage Model - ข้อความในการสอบถาม
 */
class InquiryMessage extends ActiveRecord
{
    const SENDER_CUSTOMER = 'customer';
    const SENDER_STAFF = 'staff';
    const SENDER_BOT = 'bot';

    public static function tableName()
    {
        return '{{%inquiry_message}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['inquiry_id', 'sender_type', 'message'], 'required'],
            [['inquiry_id', 'sender_id'], 'integer'],
            [['message', 'attachments'], 'string'],
            [['is_auto_reply'], 'boolean'],
            [['sender_type'], 'string', 'max' => 20],
            [['sender_type'], 'in', 'range' => [self::SENDER_CUSTOMER, self::SENDER_STAFF, self::SENDER_BOT]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inquiry_id' => 'การสอบถาม',
            'sender_type' => 'ประเภทผู้ส่ง',
            'sender_id' => 'ผู้ส่ง',
            'message' => 'ข้อความ',
            'attachments' => 'ไฟล์แนบ',
            'is_auto_reply' => 'ตอบอัตโนมัติ',
            'created_at' => 'เวลา',
        ];
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::class, ['id' => 'inquiry_id']);
    }

    public function getSender()
    {
        if ($this->sender_type === self::SENDER_STAFF && $this->sender_id) {
            return $this->hasOne(User::class, ['id' => 'sender_id']);
        }
        return null;
    }

    public function getSenderName()
    {
        switch ($this->sender_type) {
            case self::SENDER_CUSTOMER:
                return $this->inquiry->getCustomerDisplayName();
            case self::SENDER_STAFF:
                return $this->sender ? $this->sender->full_name : 'พนักงาน';
            case self::SENDER_BOT:
                return 'AI Assistant';
            default:
                return 'Unknown';
        }
    }

    public function getSenderTypeBadge()
    {
        $badges = [
            self::SENDER_CUSTOMER => '<span class="badge bg-primary">ลูกค้า</span>',
            self::SENDER_STAFF => '<span class="badge bg-success">พนักงาน</span>',
            self::SENDER_BOT => '<span class="badge bg-info">AI</span>',
        ];
        return $badges[$this->sender_type] ?? '<span class="badge bg-secondary">' . $this->sender_type . '</span>';
    }

    public function getAttachmentsArray()
    {
        if (empty($this->attachments)) {
            return [];
        }
        try {
            return Json::decode($this->attachments);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function isFromCustomer()
    {
        return $this->sender_type === self::SENDER_CUSTOMER;
    }

    public function isFromStaff()
    {
        return $this->sender_type === self::SENDER_STAFF;
    }

    public function isFromBot()
    {
        return $this->sender_type === self::SENDER_BOT;
    }
}
