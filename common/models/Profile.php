<?php

namespace common\models;

use dektrium\user\models\Profile as BaseProfile;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $status
 * @property string  $timezone
 * @property User    $user
 */
class Profile extends BaseProfile
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * Returns avatar url or null if avatar is not set.
     * @param  int $size
     * @return string|null
     */
    public function getAvatarUrl($size = 200)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'timeZoneValidation'   => ['timezone', 'validateTimeZone'],
            'nameLength'           => ['name', 'string', 'max' => 255],
            'publicEmailLength'    => ['status', 'string', 'max' => 4096],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'           => \Yii::t('user', 'Name'),
            'status'         => \Yii::t('user', 'Status'),
            'timezone'       => \Yii::t('user', 'Time zone'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return ActiveRecord::beforeSave($insert);
    }

    /**
     * If user is owner of this profile
     *
     * @param User|null $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return ($this->user_id == $user->id);
    }
}
