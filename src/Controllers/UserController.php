<?php

namespace Yiisoft\Yii\Debug\Controllers;

/**
 * User controller
 */
class UserController extends Controller
{
    public function beforeAction(Action $action): bool
    {
        $this->app->response->format = Response::FORMAT_JSON;
        if (!$this->app->session->hasSessionId) {
            throw new BadRequestHttpException('Need an active session');
        }
        return parent::beforeAction($action);
    }

    /**
     * Set new identity, switch user
     * @return \yii\web\User
     */
    public function actionSetIdentity()
    {
        $user_id = $this->app->request->post('user_id');

        $userSwitch = new UserSwitch();
        $newIdentity = $this->app->user->identity->findIdentity($user_id);
        $userSwitch->setUserByIdentity($newIdentity);
        return $this->app->user;
    }

    /**
     * Reset identity, switch to main user
     * @return \yii\web\User
     */
    public function actionResetIdentity()
    {
        $userSwitch = new UserSwitch();
        $userSwitch->reset();
        return $this->app->user;
    }
}
