<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%contact_request}}".
 *
 * @property int $from_user_id
 * @property int $to_user_id
 * @property int $state
 * @property string $created_at
 *
 * @property User $fromUser
 * @property User $toUser
 */
class ContactRequest extends \yii\db\ActiveRecord
{
    // there is no accepted state because on accept a user is added to contact list and contact request is deleted
    const STATE_SENT = 1;
    const STATE_DECLINED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contact_request}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'state'], 'required'],
            [['from_user_id', 'to_user_id', 'state'], 'integer'],
            [['from_user_id', 'to_user_id'], 'unique', 'targetAttribute' => ['from_user_id', 'to_user_id']],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [self::EVENT_BEFORE_INSERT => ['created_at']],
                'value' => function() { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'from_user_id' => Yii::t('app', 'From'),
            'to_user_id' => Yii::t('app', 'To'),
            'state' => Yii::t('app', 'State'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::className(), ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }

    /**
     * Get state list as id => name
     * @return array
     */
    public function getStateList()
    {
        return [
            self::STATE_SENT => Yii::t('app', 'Sent'),
            self::STATE_DECLINED => Yii::t('app', 'Declined'),
        ];
    }

    /**
     * @return bool
     */
    public function isDeclined()
    {
        return ($this->state == self::STATE_DECLINED);
    }
}
