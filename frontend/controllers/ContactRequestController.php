<?php

namespace frontend\controllers;

use Yii;
use common\models\ContactRequest;
use common\models\Contact;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ContactRequestController implements the actions for ContactRequest models.
 */
class ContactRequestController extends Controller
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
                    'create' => ['POST'],
                    'accept' => ['POST'],
                    'decline' => ['POST'],
                    'delete-contact' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Adds request from current user to add another user to contact list.
     * @return mixed
     */
    public function actionCreate($to_user_id)
    {
        $from_user_id = Yii::$app->user->identity->id;
        $user = $this->findUserModel($to_user_id);

        $model = ContactRequest::findOne(['from_user_id' => $from_user_id, 'to_user_id' => $to_user_id]);
        if ($model === null) {
            // prevent each-other requests
            $model = ContactRequest::findOne(['from_user_id' => $to_user_id, 'to_user_id' => $from_user_id]);
        }

        if ($model !== null) {
            return $this->redirect(['user/profile/show', 'id' => $user->id]);
        }


        $contactRequest = new ContactRequest();
        $contactRequest->from_user_id = $from_user_id;
        $contactRequest->to_user_id = $user->id;
        $contactRequest->state = ContactRequest::STATE_SENT;
        $contactRequest->save();

        return $this->redirect(['user/profile/show', 'id' => $user->id]);
    }

    /**
     * Accept request
     * @return mixed
     */
    public function actionAccept($from_user_id)
    {
        $to_user_id = Yii::$app->user->identity->id;
        $user = $this->findUserModel($from_user_id);
        $contactRequest = $this->findModel($from_user_id, $to_user_id);

        $transaction = Yii::$app->db->beginTransaction();

        $contact = new Contact();
        $contact->owner_id = $to_user_id;
        $contact->user_id = $from_user_id;
        $contact->save();

        $contact = new Contact();
        $contact->owner_id = $from_user_id;
        $contact->user_id = $to_user_id;
        $contact->save();

        $contactRequest->delete();

        $transaction->commit();

        return $this->redirect(['user/profile/show', 'id' => $to_user_id]);
    }

    /**
     * Decline request
     * @return mixed
     */
    public function actionDecline($from_user_id)
    {
        $to_user_id = Yii::$app->user->identity->id;
        $user = $this->findUserModel($from_user_id);
        $contactRequest = $this->findModel($from_user_id, $to_user_id);

        $contactRequest->state = ContactRequest::STATE_DECLINED;
        $contactRequest->save();

        return $this->redirect(['user/profile/show', 'id' => $to_user_id]);
    }

    /**
     * Delete contact
     * @return mixed
     */
    public function actionDeleteContact($user_id)
    {
        $owner_id = Yii::$app->user->identity->id;

        $transaction = Yii::$app->db->beginTransaction();

        $contact = Contact::findOne(['owner_id' => $owner_id, 'user_id' => $user_id]);
        if ($contact) {
            $contact->delete();
        }

        $contact = Contact::findOne(['owner_id' => $user_id, 'user_id' => $owner_id]);
        if ($contact) {
            $contact->delete();
        }

        $transaction->commit();

        return $this->redirect(['user/profile/show', 'id' => $owner_id]);
    }

    /**
     * Finds the ContactRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $from_user_id
     * @param integer $to_user_id
     * @return ContactRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($from_user_id, $to_user_id)
    {
        if (($model = ContactRequest::findOne(['from_user_id' => $from_user_id, 'to_user_id' => $to_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
