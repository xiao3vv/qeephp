<?php
// $Id$

$realaddr = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP']: $_SERVER['REMOTE_ADDR'];
define("REMOTE_ADDR_REAL", $realaddr);


/**
 * MyApp 封装了应用程序的基本启动流程和初始化操作，并为应用程序提供一些公共服务。
 *
 * 主要完成下列任务：
 * - 初始化运行环境
 * - 提供应用程序入口
 * - 为应用程序提供公共服务
 * - 处理访问控制和用户信息在 session 中的存储
 */
class MyApp
{
    /**
     * 应用程序的基本设置
     *
     * @var array
     */
    protected $_app_config;
    protected $_flash_message_key='surak_falshmessage', $_flash_message;

    /**
     * 构造函数
     *
     * @param array $app_config
     *
     * 构造应用程序对象
     */
    protected function __construct(array $app_config)
    {
        /**
         * 初始化运行环境
         */
        if (!PHP53) set_magic_quotes_runtime(0);

        // 设置异常处理函数
        set_exception_handler(array($this, 'exception_handler'));

        // 初始化应用程序设置
        $this->_app_config = $app_config;
        $this->_initConfig();
        Q::replaceIni('app_config', $app_config);

        // 设置默认的时区
        //'Asia/Shanghai'
        date_default_timezone_set(Q::ini('l10n_default_timezone'));

        // 设置 session 服务
        if (Q::ini('runtime_session_provider'))
        {
            Q::loadClass(Q::ini('runtime_session_provider'));
        }

        // 打开 session
        if (Q::ini('runtime_session_start'))
        {
            (!PHP_CLI) && session_start();
        }

        // 导入类搜索路径
        Q::import($app_config['APP_DIR']);
        Q::import($app_config['APP_DIR'] . '/model');

        // 注册应用程序对象
        Q::register($this, 'app');
    }

    /**
     * 析构函数
     */
    function __destruct()
    {

    }

    /**
     * 返回应用程序类的唯一实例
     *
     * @param array $app_config
     *
     * @return MyApp
     */
    static function instance(array $app_config = null)
    {
        static $instance;
        if (is_null($instance))
        {
            if (empty($app_config))
            {
                die('INVALID CONSTRUCT APP');
            }
            $instance = new MyApp($app_config);
        }
        return $instance;
    }

    /**
     * 返回应用程序基础配置的内容
     *
     * 如果没有提供 $item 参数，则返回所有配置的内容
     *
     * @param string $item
     *
     * @return mixed
     */
    function config($item = null)
    {
        if ($item)
        {
            return isset($this->_app_config[$item]) ? $this->_app_config[$item] : null;
        }
        return $this->_app_config;
    }

    /**
     * 根据运行时上下文对象，调用相应的控制器动作方法
     *
     * @param array $args
     *
     * @return mixed
     */
    function dispatching(array $args = array())
    {
        (!PHP_CLI) && header("Content-type: text/html; charset=utf-8");

        // 从 session 中提取 flash message
        if (isset($_SESSION))
        {
            $message = $_SESSION[$this->_flash_message_key] ?? NULL;
            $this->_flash_message = $message;
            unset($_SESSION[$this->_flash_message_key]);
        }

        // 构造运行时上下文对象
        $context = QContext::instance();

        Q::replaceIni('currentUDI',$context->requestUDI(false));

        // 获得请求对应的 UDI（统一目的地信息）
        $udi = $context->requestUDI('array');

        $module_name     = ($udi[QContext::UDI_MODULE]);
        $controller_name = ($udi[QContext::UDI_CONTROLLER]);
        $dir = "{$this->_app_config['APP_DIR']}/controller/{$module_name}";
        $class_name = sprintf("Controller_%s_%s",ucwords($module_name),ucwords($controller_name));
        $filename = "{$controller_name}_controller.php";

        do
        {
            if (!class_exists($class_name, false))
            {
                Q::loadClassFile($filename, array($dir), $class_name);
            }

            // 构造控制器对象
            $controller = new $class_name($this);
            $action_name = $udi[QContext::UDI_ACTION];
            if ($controller->existsAction($action_name))
            {
                // 如果指定动作存在，则调用
                $response = $controller->execute($action_name, $args);
                // 更新 flash message
            }
            else
            {
                // 如果指定动作不存在，则尝试调用控制器的 _on_action_not_defined() 函数处理错误
                $response = $controller->_on_action_not_defined($action_name);
                if (is_null($response))
                {
                    // 如果控制器的 _on_action_not_defined() 函数没有返回处理结果
                    // 则由应用程序对象的 _on_action_not_defined() 函数处理
                    $response = $this->_on_action_not_defined();
                }
            }
        } while (false);

        if (is_object($response) && method_exists($response, 'execute'))
        {
            // 如果返回结果是一个对象，并且该对象有 execute() 方法，则调用
            $response = $response->execute();
        }
        elseif ($response instanceof QController_Forward)
        {
            // 更新 flash message
            if (isset($_SESSION))
            {
                unset($_SESSION[$this->_flash_message_key]);
            }

            // 如果是一个 QController_Forward 对象，则将请求进行转发
            $response = $this->dispatching($response->args);
        }

        if (!PHP_CLI)
        {
            // 其他情况则返回执行结果
            return $response;
        }
    }

    /**
     * 设置可以跨请求显示的提示信息
     */
    function setFlashMessage()
    {
        $args = func_get_args();
        $this->_flash_message = array_shift($args);

        if (isset($_SESSION))
        {
            $_SESSION[$this->_flash_message_key] = $this->_flash_message;
        }
    }

    /**
     * 返回用 setFlashMessage() 设置的提示信息
     *
     * @return string
     */
    function getFlashMessage()
    {
        return $this->_flash_message;
    }

    /**
     * 载入配置文件内容
     *
     * @param array $app_config
     *
     * @return array
     */
    static function loadConfigFiles(array $app_config)
    {
        $ext = $app_config['CONFIG_FILE_EXTNAME'] ?? 'yaml';
        $cfg = $app_config['CONFIG_DIR'];

        $files = array
        (
            "{$cfg}/app.{$ext}"                       => 'appini',
            "{$cfg}/routes.{$ext}"                    => 'routes',
        );

        $replace = array();
        foreach ($app_config as $key => $value)
        {
            if (!is_array($value)) $replace["%{$key}%"] = $value;
        }

        $config = require(Q_DIR . '/_config/default_config.php');
        $config['runtime_cache_dir'] = $app_config['ROOT_DIR'] . '/tmp';
        $config['log_writer_dir']    = $app_config['ROOT_DIR'] . '/tmp';
        foreach ($files as $filename => $scope)
        {
            if (!file_exists($filename)) continue;
            $contents = Helper_YAML::load($filename, $replace);
            if ($scope == 'global')
            {
                $config = array_merge($config, $contents);
            }
            else
            {
                if (!isset($config[$scope]))
                {
                    $config[$scope] = array();
                }
                $config[$scope] = array_merge($config[$scope], $contents);
            }
        }

        $config['db_dsn_pool']['default'] = $config['appini']['database'];
        unset($config['appini']['database']);
        return $config;
    }

    /**
     * 初始化应用程序设置
     */
    protected function _initConfig()
    {
        // 载入配置文件
        if ($this->_app_config['CONFIG_CACHED'])
        {
            // 构造缓存服务对象
            $backend = $this->_app_config['CONFIG_CACHE_BACKEND'];
            $settings = isset($this->_app_config['CONFIG_CACHE_SETTINGS'][$backend]) ? $this->_app_config['CONFIG_CACHE_SETTINGS'][$backend] : null;
            $cache = new $backend($settings);

            // 载入缓存内容
            $cache_id = $this->_app_config['APPID'] . '_app_config';
            $config   = $cache->get($cache_id);
            if (!empty($config))
            {
                Q::replaceIni($config);
                return;
            }
        }

        // 没有使用缓存，或缓存数据失效
        $config = self::loadConfigFiles($this->_app_config);
        if ($this->_app_config['CONFIG_CACHED'])
        {
            $cache->set($cache_id, $config);
        }

        Q::replaceIni($config);
    }

    /**
     * 访问被拒绝时的错误处理函数
     */
    protected function _on_access_denied()
    {
        header('HTTP/1.1 403 Forbidden');
        return false;
    }

    /**
     * 视图调用未定义的控制器或动作时的错误处理函数
     */
    protected function _on_action_not_defined()
    {
        header('HTTP/1.1 404 Not Found');
        return false;
    }

    /**
     * 默认的异常处理
     */
    static function exception_handler($e)
    {
        if(PHP_CLI)
        {
            __($e);
            return false;
        }else
        {
            header('HTTP/1.1 500 Internal Server Error');
            if(Q::cookie('authval') == Q::ini('appini/authval/system'))
            {
                dump($e);
            }
            return false;
        }
    }
}

function haveText($string, $params)
{
    if(stripos($string, strval($params)) !== false)
    {
        return true;
    }
    return false;
}