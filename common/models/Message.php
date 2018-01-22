<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property int $id
 * @property int $sender_id
 * @property string $text
 * @property string $created_at
 *
 * @property User $sender
 * @property MessageHistoryRecord[] $messageHistoryRecords
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'text'], 'required'],
            [['sender_id'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender_id' => 'id']],
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
            'id' => Yii::t('app', 'ID'),
            'sender_id' => Yii::t('app', 'Sender ID'),
            'text' => Yii::t('app', 'Text'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageHistoryRecords()
    {
        return $this->hasMany(MessageHistoryRecord::className(), ['message_id' => 'id']);
    }
}
