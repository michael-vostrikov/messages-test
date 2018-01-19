<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \dektrium\user\models\Profile $profile
 */

$this->title = (empty($profile->name) ? $profile->user->username : $profile->name);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul style="padding: 0; list-style: none outside none;">
                    <?php if (!empty($profile->status)): ?>
                        <li>
                            <?= Yii::t('user', 'Status:') ?>
                            <?= Html::encode($profile->status) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <br>
                        <?= Yii::t('user', 'Email:') ?>
                        <?= Html::a(Html::encode($profile->user->email), 'mailto:' . Html::encode($profile->user->email)) ?>
                    </li>
                    <li>
                        <?= Yii::t('user', 'Joined on {0, date}', $profile->user->created_at) ?>
                    </li>
                    <?php if ($profile->user_id == Yii::$app->user->getId()): ?>
                        <li>
                            <br>
                            <?= Html::a(Yii::t('user', 'Edit'), ['/user/settings'], ['class' => 'btn btn-primary']) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
