<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $contact common\models\Contact */
/* @var $messageHistory common\models\MessageHistoryRecord[] */
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

/* @var $newMessage common\models\Message */
$newMessage = new \common\models\Message();

$messageHistory = array_reverse($messageHistory);
$last_record_id = (count($messageHistory) > 0 ? $messageHistory[0]->id : 0);

$status = $contact->user->profile->status;
$this->title = $contact->user->name . ($status ? ' | ' . $status : '');

$this->params['breadcrumbs'][] = $contact->user->name;
?>
<div class="message-history">

    <h4 class="dialog-title"><?= Html::encode($this->title) ?></h4>

    <div class="message-container autoscroll-to-bottom">
        <div class="text-center load-more-container">
            <?= Html::a(
                Html::encode(Yii::t('app', 'load more')),
                ['/message/history', 'user_id' => $contact->user_id],
                ['class' => 'history-load-more-btn', 'role' => 'button', 'data-last_record_id' => $last_record_id]
            ) ?>
        </div>

        <?php foreach ($messageHistory as $record) { ?>
            <?= $this->render('_message', ['record' => $record]) ?>
        <?php } ?>
    </div>


    <div class="message-form">

        <?php $form = ActiveForm::begin(['action' => ['create', 'user_id' => $contact->user_id]]); ?>

        <?php
            $btnTemplate = '<span class="input-group-btn">'.Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-success']).'</span>';
            $inputTemplate = '<div class="input-group">{input}'.$btnTemplate.'</div>';
            $template = "{label}\n".$inputTemplate."\n{hint}\n{error}";
        ?>
        <?= $form->field($newMessage, 'text', ['template' => $template])->label(false)->textInput() ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
