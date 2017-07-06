<?php
// $Id: default_config.php 2340 2009-03-25 17:00:26Z dualface $

return array
(
    // {{{ 运行环境相关

    /**
     * 要使用的 session 服务
     */
    'runtime_session_provider'  => null,

    /**
     * 是否自动打开 session
     */
    'runtime_session_start'     => true,

    /**
     * QeePHP 内部及 cache 系列函数使用的缓存目录
     * 应用程序必须设置该选项才能使用 cache 功能。
     */
    'runtime_cache_dir'         => '/tmp',

    /**
     * 默认使用的缓存服务
     */
    'runtime_cache_backend'     => 'QCache_PHPDataFile',

    // }}}


    // {{{ 调度器相关

    /**
     * url 参数的传递模式，可以是标准、PATHINFO、URL 重写等模式
     */
    'dispatcher_url_mode'       => 'rewrite',

    /**
     * 路由规则的缓存时间
     */
    'routes_cache_lifetime'     => 10,

    // }}}


    // {{{ 数据库相关

    /**
     * 数据库查询是否写入日志
     */
    'db_log_enabled' => true,

    /**
     * 数据表元数据缓存时间（秒），如果 db_meta_cached 设置为 false，则不会缓存数据表元数据
     */
    'db_meta_lifetime' => 10,

    /**
     * 指示是否缓存数据表的元数据
     */
    'db_meta_cached' => false,

    /**
     * 缓存元数据使用的缓存服务
     */
    'db_meta_cache_backend' => 'QCache_PHPDataFile',

    // }}}


    // {{{ 国际化（I18N）和本地化（L10N）相关

    /**
     * 默认的时区设置
     */
    'l10n_default_timezone' => 'Asia/Shanghai',

    // }}}


    // {{{ 日志和错误处理

    /**
     * 指示是否允许记录日志
     */
    'log_enabled' => true,

    /**
     * 指示记录哪些优先级的日志（不符合条件的会直接过滤）
     */
    'log_priorities' => 'EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG',

    /**
     * 日志缓存块大小（单位KB）
     *
     * 更小的缓存块可以节约内存，但写入日志的次数更频繁，性能更低。
     */
    'log_cache_chunk_size' => 64,  // 64KB

    /**
     * 保存日志文件的目录
     */
    'log_writer_dir' => '/tmp',

    /**
     * 日志文件的文件名
     */
    'log_writer_filename' => 'access.log'
);
