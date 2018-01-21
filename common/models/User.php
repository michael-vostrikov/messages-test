<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use dektrium\user\models\User as BaseUser;

/**
 * User model
 *
 * @inheritdoc
 */
class User extends BaseUser
{
}
