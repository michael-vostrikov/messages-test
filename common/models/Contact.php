<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%contact}}".
 *
 * @property int $id
 * @property int $owner_id
 * @property int $user_id
 *
 * @property User $owner
 * @property User $user
 * @property MessageHistoryRecords[] $messageHistoryRecords
 * @property int $unreadCount
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contact}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_id', 'user_id'], 'required'],
            [['owner_id', 'user_id'], 'integer'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'owner_id' => Yii::t('app', 'Owner ID'),
            'user_id' => Yii::t('app', 'User ID'),

            'user.name' => Yii::t('app', 'User'),
            'user.profile.status' => Yii::t('app', 'Status'),
            'unreadCount' => Yii::t('app', 'Unread Count'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageHistoryRecords()
    {
        return $this->hasMany(MessageHistoryRecord::className(), ['contact_id' => 'id'])
            ->joinWith('message')
            ->orderBy(['{{message_history}}.id' => SORT_DESC])
        ;
    }

    /**
     * @return int
     */
    public function getUnreadCount()
    {
        return $this->getMessageHistoryRecords()->andWhere(['is_unread' => 1])->count();
    }
}
