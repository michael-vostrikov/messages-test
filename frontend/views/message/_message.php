<?php

use yii\helpers\Html;

/* @var $record common\models\MessageHistoryRecord */
/* @var $this yii\web\View */
$isOwn = ($record->contact->owner_id == $record->message->sender_id);
?>
<div class="message<?= ($isOwn ? ' own-message' : '') ?>">
    <div class="message-header">
        <div class="message-sender"><?= Html::encode($record->message->sender->name) ?></div>
        <div class="message-time"><?= Html::encode(Yii::$app->formatter->asDatetime($record->message->created_at)) ?></div>
    </div>
    <div class="message-body">
        <?= Html::encode($record->message->text) ?>
    </div>
</div>
