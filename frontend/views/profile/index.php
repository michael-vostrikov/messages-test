<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Profiles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'user_id',
            ['attribute' => 'name', 'format' => 'raw', 'value' => function ($model) {
                return Html::a(Html::encode($model->name ?: $model->user->username), ['/user/profile/show', 'id' => $model->user_id]);
            }],
            'status',
        ],
    ]); ?>
</div>
