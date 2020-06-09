<?php

namespace Yusronarif\Core\Routing;

use Illuminate\Routing\Controller as BaseController;
use phpDocumentor\Reflection\Types\This;

/**
 * Controller base class.
 *
 * @author      Yusron Arif <yusron.arif4@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * Authenticated User
     *
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * Controller data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Active menu indicator.
     *
     * @var array
     */
    private $activeMenu = [];
    private $activeMenuPack = [];

    /**
     * Page title.
     *
     * @var string
     */
    private $pageTitle;

    /**
     * Page Meta.
     *
     * @var array
     */
    private $pageMeta = [
        'description' => null,
        'keywords' => null,
    ];

    /**
     * Reserved variable for the controller.
     *
     * @var array
     */
    private $reservedVariables = ['activeMenu', 'activeMenuPack', 'pageTitle', 'pageMeta'];

    /**
     * Prefix View Path
     *
     * @var string
     */
    protected $prefixView = '';
    protected $viewPath = '';

    /**
     * type of crud form
     *
     * @var string
     */
    protected $crudType = '';

    /**
     * main route name
     *
     * @var string
     */
    protected $route = '';

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
     * @return \Illuminate\View\View
     */
    protected function view($view)
    {
        $this->share();

        if ($this->prefixView)
            $view = preg_replace('/(\.)+$/i', '', $this->prefixView) . '.' . $view;

        return view($view, $this->data);
    }

    private function share()
    {
        if (false === array_key_exists('pageTitle', $this->data)) {
            $this->setPageTitle('Untitled');
        }

        $this->setPageMeta('csrf_token', csrf_token());

        $this->data['activeUser'] = auth()->user();

        $this->data['crudType'] = $this->crudType;
        $this->data['viewPath'] = ($this->viewPath ?: $this->prefixView) . '.';
        $this->data['route'] = $this->route;

        return $this;
    }

    /**
     * Set Default Value for Request Input
     *
     * @param string|array $name
     * @param null $value
     *
     * @return Illuminate\Http\Request
     */
    protected function setDefault($name, $value = null)
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
     * @return Controller
     *
     * @throws \Exception
     */
    protected function setData($name, $value)
    {
        if (in_array($name, $this->reservedVariables)) {
            throw new \Exception("Variable [$name] is reserved by this controller");
        }
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Set page meta.
     *
     * @param string $metaKey
     * @param mixed  $metaValue
     *
     * @return Controller
     */
    protected function setPageMeta($metaKey, $metaValue)
    {
        $this->pageMeta[$metaKey] = $metaValue;

        return $this;
    }

    /**
     * Set Page title.
     *
     * @param string $title
     *
     * @return Controller
     */
    protected function setPageTitle($title)
    {
        $this->data['pageTitle'] = $title;

        return $this;
    }

    /**
     * Set Active Menu.
     *
     * @param $menu
     * @return $this
     */
    protected function setActiveMenu($menu)
    {
        if (is_array($menu)) {
            $this->activeMenu = $menu;
        } else {
            $this->activeMenu = array($menu);
        }

        return $this;
    }

    /**
     * Add Active Menu.
     *
     * @param $menu
     * @return $this
     */
    protected function addActiveMenu($menu)
    {
        if (is_array($menu)) {
            $this->activeMenu = array_merge($this->activeMenu, $menu);
        } else {
            array_push($this->activeMenu, $menu);
        }

        return $this;
    }
}
