<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/**
 * @var \yii\web\View $this
 * @var \dektrium\user\models\Profile $profile
 */

$profileUser = $profile->user;
$currentUser = Yii::$app->user->identity;

$this->title = (empty($profile->name) ? $profileUser->username : $profile->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profiles'), 'url' => ['/profile/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-4">
        <h4><?= Html::encode($this->title) ?></h4>
        <ul style="padding: 0; list-style: none outside none;">
            <?php if (!empty($profile->status)) { ?>
                <li>
                    <?= Yii::t('app', 'Status:') ?>
                    <?= Html::encode($profile->status) ?>
                </li>
            <?php } ?>
            <li>
                <br>
                <?= Yii::t('app', 'Email:') ?>
                <?= Html::a(Html::encode($profileUser->email), 'mailto:' . Html::encode($profileUser->email)) ?>
            </li>
            <li>
                <?= Yii::t('app', 'Joined on {0, date}', $profileUser->created_at) ?>
            </li>
        </ul>
    </div>
    <div class="col-sm-8">

        <?php if ($currentUser !== null) { ?>

            <?php if ($profile->isOwner($currentUser)) { ?>
                <p>
                    <?= Html::a(Yii::t('app', 'Edit profile'), ['/user/settings'], ['class' => 'btn btn-primary']) ?>
                </p>
            <?php } ?>


            <?php if ($currentUser->hasContact($profileUser)) { ?>
                <p>
                    <?= \yii\bootstrap\Alert::widget([
                            'body' => Yii::t('app', 'This user is in your contact list'),
                            'closeButton' => false,
                            'options' => ['class' => 'alert-success'],
                    ]) ?>
                </p>
            <?php } elseif ($profileUser->hasRequestFrom($currentUser)) { ?>
                <p>
                    <?= \yii\bootstrap\Alert::widget([
                            'body' => Yii::t('app', 'You have sent a contact request to this user'),
                            'closeButton' => false,
                            'options' => ['class' => 'alert-info'],
                    ]) ?>
                </p>
            <?php } elseif ($currentUser->hasRequestFrom($profileUser)) { ?>
                <p>
                    <?= \yii\bootstrap\Alert::widget([
                            'body' => Yii::t('app', 'This user has sent a contact request to you'),
                            'closeButton' => false,
                            'options' => ['class' => 'alert-info'],
                    ]) ?>
                </p>
            <?php } elseif ($profileUser->id != $currentUser->id)  { ?>
                <p>
                    <?= Html::a(Yii::t('app', 'Add to contact list'), ['/contact-request/create', 'to_user_id' => $profile->user_id], [
                        'class' => 'btn btn-primary',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'Are you sure you want to add this user to your contact list?'),
                    ]) ?>
                </p>
            <?php } ?>

        <?php } ?>

    </div>
</div>

<?php if ($currentUser !== null) { ?>

    <?php if ($profile->isOwner($currentUser)) { ?>

        <br>

        <h4><?= Yii::t('app', 'Contact list') ?></h4>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $profileUser->getContactUsers(),
            ]),
            'columns' => [
                'name',
                'profile.status',
                ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}', 'urlCreator' => function ($action, $model) {
                    return ['/contact-request/delete-contact', 'user_id' => $model->id];
                }],
            ],
        ]) ?>


        <br>

        <h4><?= Yii::t('app', 'Received contact requests') ?></h4>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $profileUser->getReceivedContactRequests()->orderBy('created_at', SORT_DESC),
            ]),
            'columns' => [
                ['attribute' => 'from_user_id', 'format' => 'raw', 'value' => function ($model) {
                    return Html::a(Html::encode($model->fromUser->name), ['/user/profile/show', 'id' => $model->from_user_id]);
                }],
                ['attribute' => 'state', 'format' =>'raw', 'value' => function ($model) {
                    $name = ($model->getStateList()[$model->state] ?? null);
                    if ($model->isDeclined()) {
                        $html = Html::a(Html::encode($name), null, ['class' => 'label label-danger']);
                    } else {
                        $html = Html::a(Html::encode($name), null, ['class' => 'label label-info']);
                    }
                    return $html;
                }],
                'created_at:datetime',

                ['label' => Yii::t('app', 'Actions'), 'format' => 'raw', 'value' => function ($model) {
                    $html = '';
                    $html .= Html::a(
                        Html::encode(Yii::t('app', 'Accept')),
                        ['/contact-request/accept', 'from_user_id' => $model->from_user_id],
                        ['class' => 'btn btn-success btn-xs', 'data-method' => 'post']
                    );
                    $html .= '&nbsp;';

                    if (!$model->isDeclined()) {
                        $html .= Html::a(
                            Html::encode(Yii::t('app', 'Decline')),
                            ['/contact-request/decline', 'from_user_id' => $model->from_user_id],
                            ['class' => 'btn btn-danger btn-xs', 'data-method' => 'post']
                        );
                    }

                    return $html;
                }],
            ],
        ]) ?>


        <br>

        <h4><?= Yii::t('app', 'Sent contact requests') ?></h4>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $profileUser->getSentContactRequests()->orderBy('created_at', SORT_DESC),
            ]),
            'columns' => [
                ['attribute' => 'to_user_id', 'format' => 'raw', 'value' => function ($model) {
                    return Html::a(Html::encode($model->toUser->name), ['/user/profile/show', 'id' => $model->to_user_id]);
                }],
                // don't show if request was declined or not
                'created_at:datetime',
            ],
        ]) ?>

    <?php } ?>

<?php } ?>
