<?php

/**
 * 应用程序的公共控制器基础类
 *
 * 可以在这个类中添加方法来完成应用程序控制器共享的功能。
 */
abstract class Controller_System_Abstract extends Controller_Abstract
{
	protected function _before_execute()
    {
    	parent::_before_execute();
    }
}

