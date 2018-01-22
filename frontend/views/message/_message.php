<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $record common\models\MessageHistoryRecord */
/* @var $this yii\web\View */
$isOwn = ($record->contact->owner_id == $record->message->sender_id);
$isUnread = $record->is_unread;
?>
<div class="message<?= ($isOwn ? ' own-message' : '') ?><?= ($isUnread ? ' is-unread' : '') ?>">
    <div class="message-header">
        <div class="message-sender"><?= Html::encode($record->message->sender->name) ?></div>
        <div class="message-time"><?= Html::encode(Yii::$app->formatter->asDatetime($record->message->created_at)) ?></div>
        &nbsp;
        <?= Html::tag('div', '&times;', [
            'class' => 'close',
            'title' => Yii::t('app', 'Delete message'),
            'data-url' => Url::to(['/message/delete', 'record_id' => $record->id]),
            'data-confirm' => Yii::t('app', 'Are you sure you want to delete this message?'),
        ]) ?>
    </div>
    <div class="message-body">
        <?= Html::encode($record->message->text) ?>
    </div>
</div>
