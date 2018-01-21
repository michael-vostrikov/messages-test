<?php

namespace common\components;

use yii\base\Component;
use common\models\User;
use Yii;

/**
 * Component for managing information about online and offline users
 */
class OnlineManager extends Component
{
    const LAST_USER_ACTION_TIME_KEY = 'last_user_action_time';
    const IS_ONLINE_TIME_DIFF = 10*60;

    /**
     * Calculate key for cache
     * @param User $user
     * @return string
     */
    public function calcUserKey(User $user)
    {
        return static::LAST_USER_ACTION_TIME_KEY . $user->id;
    }

    /**
     * Set user online
     * @param User $user
     */
    public function setOnline(User $user)
    {
        $key = $this->calcUserKey($user);
        Yii::$app->cache->set($key, time());
    }

    /**
     * Set user offline
     * @param User $user
     */
    public function setOffline(User $user)
    {
        $key = $this->calcUserKey($user);
        Yii::$app->cache->delete($key);
    }

    /**
     * Get online status
     * @return bool
     */
    public function isOnline(User $user)
    {
        $key = $this->calcUserKey($user);
        $time = Yii::$app->cache->get($key);
        if ($time > 0) {
            if (time() - $time < static::IS_ONLINE_TIME_DIFF) {
                return true;
            }

            Yii::$app->cache->delete($key);
        }

        return false;
    }

    /**
     * Handler for EVENT_BEFORE_LOGOUT event, can be attached somewhere in bootstrap code
     * @param \yii\web\UserEvent $event
     */
    public function onLogout($event)
    {
        $user = $event->sender->identity;
        $this->setOffline($user);
    }
}
