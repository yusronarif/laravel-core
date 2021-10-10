<?php

namespace Yusronarif\Core\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

/**
 * Controller base class.
 *
 * @author      Yusron Arif <yusron.arif4@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * Authenticated User
     */
    protected Request $request;

    /**
     * Controller data.
     */
    private array $controllerData = [];

    /**
     * Active menu indicator.
     */
    private array $activeMenu = [];
    private array $activeMenuPack = [];

    /**
     * Page title.
     */
    private string $pageTitle;

    /**
     * Page Meta.
     */
    private array $pageMeta = [
        'description' => null,
        'keywords' => null,
    ];

    /**
     * Reserved variable for the controller.
     */
    private array $reservedVariables = ['activeMenu', 'activeMenuPack', 'pageTitle', 'pageMeta'];

    /**
     * Prefix View Path
     */
    protected string $prefixView = '';
    protected string $viewPath = '';

    /**
     * type of crud form
     */
    protected string $crudType = '';

    /**
     * main route name
     */
    protected string $route = '';

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        auth()->setDefaultDriver('web');
        $this->request = request();
    }

    /**
     * Serve blade template.
     *
     * @param string $view
     *
     * @return View
     */
    protected function view(string $view): View
    {
        $this->share();

        if ($this->prefixView)
            $view = preg_replace('/(\.)+$/i', '', $this->prefixView) . '.' . $view;

        return view($view, $this->controllerData);
    }

    private function share(): void
    {
        if (false === array_key_exists('pageTitle', $this->controllerData)) {
            $this->setPageTitle('Untitled');
        }

        $this->setPageMeta('csrf_token', csrf_token());

        $this->controllerData['activeUser'] = auth()->user();

        $this->controllerData['crudType'] = $this->crudType;
        $this->controllerData['viewPath'] = ($this->viewPath ?: $this->prefixView) . '.';
        $this->controllerData['route'] = $this->route;
    }

    /**
     * Set Default Value for Request Input
     *
     * @param string|array $name
     * @param mixed $value
     *
     * @return void
     */
    protected function setDefault(string|array $name, mixed $value = null): void
    {
        if (!$this->request->input()) {
            setDefaultRequest($name, $value);
        }
    }

    /**
     * Set controller data.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function setData(string $name, mixed $value): void
    {
        if (in_array($name, $this->reservedVariables)) {
            throw new \Exception("Variable [$name] is reserved by this controller");
        }
        $this->controllerData[$name] = $value;
    }

    /**
     * Set page meta.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function setPageMeta(string $key, mixed $value): void
    {
        $this->pageMeta[$key] = $value;
    }

    /**
     * Set Page title.
     *
     * @param string $title
     *
     * @return void
     */
    protected function setPageTitle(string $title): void
    {
        $this->controllerData['pageTitle'] = $title;
    }

    /**
     * Set Active Menu.
     *
     * @param string|array  $menu
     * @return void
     */
    protected function setActiveMenu(string|array $menu): void
    {
        $this->activeMenu = (array) $menu;
    }

    /**
     * Add Active Menu.
     *
     * @param string|array  $menu
     * @return void
     */
    protected function addActiveMenu(string|array $menu): void
    {
        $this->activeMenu = array_merge($this->activeMenu, (array) $menu);
    }
}
