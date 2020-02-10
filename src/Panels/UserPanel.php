<?php
namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\VarDumper\VarDumper;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Controllers\UserController;
use Yiisoft\Yii\Debug\Panel;
use Yiisoft\Yii\Web\User\User;

/**
 * Debugger panel that collects and displays user data.
 */
class UserPanel extends Panel
{
    /**
     * @var array the rule which defines who allowed to switch user identity.
     * Access Control Filter single rule. Ignore: actions, controllers, verbs.
     * Settable: allow, roles, ips, matchCallback, denyCallback.
     * By default deny for everyone. Recommendation: can allow for administrator
     * or developer (if implement) role: ['allow' => true, 'roles' => ['admin']]
     * @see http://www.yiiframework.com/doc-2.0/guide-security-authorization.html
     */
    public $ruleUserSwitch = [
        'allow' => false,
    ];
    public $userSwitch;
    public $filterModel;
    /**
     * @var array allowed columns for GridView.
     * @see http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html#$columns-detail
     */
    public $filterColumns = [];
    /**
     * @var string|User ID of the user component or a user object
     */
    public $userComponent = 'user';
    private RequestInterface $request;
    private $app;

    public function __construct(RequestInterface $request, Application $app, View $view)
    {
        $this->app = $app;
        $this->request = $request;
        parent::__construct($view);
    }
    public function init(): void
    {
        if (!$this->isEnabled() || $this->getUser()->isGuest) {
            return;
        }

        $this->userSwitch = new UserSwitch(['userComponent' => $this->userComponent]);
        $this->addAccessRules();

        if (!is_object($this->filterModel)
            && class_exists($this->filterModel)
            && in_array(\Yiisoft\Yii\Debug\Models\Search\UserSearchInterface::class, class_implements($this->filterModel), true)
        ) {
            $this->filterModel = new $this->filterModel();
        } elseif ($this->getUser() && $this->getUser()->identityClass) {
            if (is_subclass_of($this->getUser()->identityClass, ActiveRecord::class)) {
                $this->filterModel = new \Yiisoft\Yii\Debug\Models\Search\User();
            }
        }
    }

    public function getUser(): User
    {
        return is_string($this->userComponent) ? $this->app->get($this->userComponent, false) : $this->userComponent;
    }

    /**
     * Add ACF rule. AccessControl attach to debug module.
     * Access rule for main user.
     */
    private function addAccessRules()
    {
        $this->ruleUserSwitch['controllers'] = [$this->module->id . '/user'];

        $this->module->attachBehavior(
            'access_debug',
            [
                '__class' => AccessControl::class,
                'only' => [$this->module->id . '/user', $this->module->id . '/default'],
                'user' => $this->userSwitch->getMainUser(),
                'rules' => [
                    $this->ruleUserSwitch,
                ],
            ]
        );
    }

    /**
     * Get model for GridView -> FilterModel
     * @return Model|UserSearchInterface
     */
    public function getUsersFilterModel()
    {
        return $this->filterModel;
    }

    /**
     * Get model for GridView -> DataProvider
     * @return DataProviderInterface
     */
    public function getUserDataProvider()
    {
        return $this->getUsersFilterModel()->search($this->request->queryParams);
    }

    /**
     * Check is available search of users
     * @return bool
     */
    public function canSearchUsers()
    {
        return (isset($this->filterModel) &&
            $this->filterModel instanceof Model &&
            $this->filterModel->hasMethod('search')
        );
    }

    /**
     * Check can main user switch identity.
     * @return bool
     */
    public function canSwitchUser()
    {
        if ($this->getUser()->isGuest) {
            return false;
        }

        $allowSwitchUser = false;

        $rule = new AccessRule($this->ruleUserSwitch);

        /** @var Controller $userController */
        $userController = null;
        $controller = $this->module->createController('user');
        if (isset($controller[0]) && $controller[0] instanceof UserController) {
            $userController = $controller[0];
        }

        //check by rule
        if ($userController) {
            $action = $userController->createAction('set-identity');
            $user = $this->userSwitch->getMainUser();
            $request = $this->request;

            $allowSwitchUser = $rule->allows($action, $user, $request) ?: false;
        }

        return $allowSwitchUser;
    }
    public function getName(): string
    {
        return 'User';
    }
    public function getSummary(): string
    {
        return $this->render('panels/user/summary', ['panel' => $this]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/user/detail', ['panel' => $this]);
    }
    public function save()
    {
        $identity = $this->app->getUser()->getIdentity(false);

        if (!isset($identity)) {
            return null;
        }

        $rolesProvider = null;
        $permissionsProvider = null;

        try {
            $authManager = $this->app->getAuthManager();

            if ($authManager instanceof \Yiisoft\Rbac\ManagerInterface) {
                $roles = ArrayHelper::toArray($authManager->getRolesByUser($this->getUser()->id));
                foreach ($roles as &$role) {
                    $role['data'] = $this->dataToString($role['data']);
                }
                unset($role);
                $rolesProvider = new ArrayDataProvider([
                    'allModels' => $roles,
                ]);

                $permissions = ArrayHelper::toArray($authManager->getPermissionsByUser($this->getUser()->id));
                foreach ($permissions as &$permission) {
                    $permission['data'] = $this->dataToString($permission['data']);
                }
                unset($permission);

                $permissionsProvider = new ArrayDataProvider([
                    'allModels' => $permissions,
                ]);
            }
        } catch (\Exception $e) {
            // ignore auth manager misconfiguration
        }

        $identityData = $this->identityData($identity);
        foreach ($identityData as $key => $value) {
            $identityData[$key] = VarDumper::dumpAsString($value);
        }

        // If the identity is a model, let it specify the attribute labels
        if ($identity instanceof IdentityInterface) {
            $attributes = [];

            foreach (array_keys($identityData) as $attribute) {
                $attributes[] = [
                    'attribute' => $attribute,
                    'label' => $identity->getId(),
                ];
            }
        } else {
            // Let the DetailView widget figure the labels out
            $attributes = null;
        }

        return [
            'id' => $identity->getId(),
            'identity' => $identityData,
            'attributes' => $attributes,
            'rolesProvider' => $rolesProvider,
            'permissionsProvider' => $permissionsProvider,
        ];
    }
    public function isEnabled(): bool
    {
        try {
            $this->getUser();
        } catch (\Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * Converts mixed data to string
     *
     * @param mixed $data
     * @return string
     */
    protected function dataToString($data)
    {
        if (is_string($data)) {
            return $data;
        }

        return VarDumper::export($data);
    }

    /**
     * Returns the array that should be set on [[\yii\widgets\DetailView::model]]
     *
     * @param IdentityInterface $identity
     * @return array
     */
    protected function identityData(IdentityInterface $identity): array
    {
        return get_object_vars($identity);
    }
}
