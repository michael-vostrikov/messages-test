<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%message_history}}".
 *
 * @property int $id
 * @property int $contact_id
 * @property int $message_id
 * @property int $is_unread
 *
 * @property Contact $contact
 * @property Message $message
 */
class MessageHistoryRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_unread'], 'default', 'value' => 1],
            [['contact_id', 'message_id', 'is_unread'], 'required'],
            [['contact_id', 'message_id', 'is_unread'], 'integer'],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'contact_id' => Yii::t('app', 'Contact ID'),
            'message_id' => Yii::t('app', 'Message ID'),
            'is_unread' => Yii::t('app', 'Is Unread'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }
}
