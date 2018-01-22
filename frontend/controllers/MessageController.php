<?php

namespace frontend\controllers;

use Yii;
use common\models\Message;
use common\models\MessageHistoryRecord;
use common\models\Contact;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MessageController implements the actions for messages.
 */
class MessageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Shows message history with user starting from last_record_id
     * @param int $user_id
     * @param int $last_record_id
     * @return mixed
     */
    public function actionHistory($user_id, $last_record_id = null)
    {
        $owner = Yii::$app->user->identity;
        $contact = $this->findContactModel($owner->id, $user_id);
        $pageSize = 20;

        $query = $contact->getMessageHistoryRecords()->limit($pageSize);
        if ($last_record_id !== null) {
            $query->andWhere(['<', '{{%message_history}}.id', $last_record_id]);
        }
        $messageHistory = $query->all();

        if (Yii::$app->request->isAjax) {
            ob_start();
            $messageHistory = array_reverse($messageHistory);
            foreach ($messageHistory as $record) {
                echo $this->renderPartial('_message', ['record' => $record]);
            }
            $last_record_id = 0;
            if (count($messageHistory) > 0) {
                $last_record_id = $messageHistory[0]->id;
            }
            $html = ob_get_clean();

            return $this->asJson(['last_record_id' => $last_record_id, 'html' => $html]);
        } else {
            return $this->render('history', [
                'contact' => $contact,
                'messageHistory' => $messageHistory,
            ]);
        }
    }

    /**
     * Creates a new MessageHistoryRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($user_id)
    {
        $owner = Yii::$app->user->identity;
        $ownerContactModel = $this->findContactModel($owner->id, $user_id);
        $userContactModel = $this->findContactModel($user_id, $owner->id);

        $transaction = Yii::$app->db->beginTransaction();

        $message = new Message();
        $message->load(Yii::$app->request->post());
        $message->sender_id = $owner->id;
        $message->save();

        $ownerHistoryRecord = new MessageHistoryRecord();
        $ownerHistoryRecord->contact_id = $ownerContactModel->id;
        $ownerHistoryRecord->message_id = $message->id;
        $ownerHistoryRecord->save();

        $userHistoryRecord = new MessageHistoryRecord();
        $userHistoryRecord->contact_id = $userContactModel->id;
        $userHistoryRecord->message_id = $message->id;
        $userHistoryRecord->save();

        $transaction->commit();

        return $this->redirect(['history', 'user_id' => $ownerContactModel->user_id]);
    }

    /**
     * Deletes an existing MessageHistoryRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $record_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($record_id)
    {
        $model = $this->findModel($record_id);
        $owner = Yii::$app->user->identity;

        if ($model->contact->owner_id != $owner->id) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $transaction = Yii::$app->db->beginTransaction();

        $message = $model->message;
        $model->delete();
        if ($message->getMessageHistoryRecords()->count() == 0) {
            $message->delete();
        }

        $transaction->commit();

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['success' => true]);
        }

        return $this->redirect(['history', 'user_id' => $model->contact->user_id]);
    }

    /**
     * Finds the MessageHistoryRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MessageHistoryRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MessageHistoryRecord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findContactModel($owner_id, $user_id)
    {
        if (($model = Contact::findOne(['owner_id' => $owner_id, 'user_id' => $user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
