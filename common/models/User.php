<?php

namespace common\models;

use Yii;
use dektrium\user\models\User as BaseUser;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $confirmed_at
 * @property string $unconfirmed_email
 * @property int $blocked_at
 * @property string $registration_ip
 * @property int $created_at
 * @property int $updated_at
 * @property int $flags
 * @property int $last_login_at
 *
 * @property Contact[] $contacts
 * @property User[] $contactUsers
 * @property ContactRequest[] $sentContactRequests
 * @property ContactRequest[] $receivedContactRequests
 *
 * @property Profile $profile
 * @property SocialAccount[] $socialAccounts
 * @property Token[] $tokens
 */
class User extends BaseUser
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['owner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->via('contacts');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSentContactRequests()
    {
        return $this->hasMany(ContactRequest::className(), ['from_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedContactRequests()
    {
        return $this->hasMany(ContactRequest::className(), ['to_user_id' => 'id']);
    }

    /**
     * If this user has another user in contact list
     *
     * @param User $user
     * @return bool
     */
    public function hasContact(User $user)
    {
        return $this->getContacts()->where(['user_id' => $user->id])->exists();
    }

    /**
     * If this user already has a contact request from user
     *
     * @param User $user
     * @return bool
     */
    public function hasRequestFrom(User $user)
    {
        return $this->getReceivedContactRequests()->where(['from_user_id' => $user->id])->exists();
    }

    /**
     * Get user name to display
     * Returns name from profile or username if empty
     *
     * @return string
     */
    public function getName()
    {
        return ($this->profile && $this->profile->name ? $this->profile->name : $this->username);
    }
}
