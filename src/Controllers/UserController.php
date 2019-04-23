<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug\Controllers;

use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use Yiisoft\Debug\Models\UserSwitch;

/**
 * User controller
 *
 * @author Semen Dubina <yii2debug@sam002.net>
 * @since 2.0.10
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
