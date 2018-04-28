<?php


require_once 'Core.php';
require_once 'Module.php';
require_once 'RequireHandler.php';

Core::prepare();


require_once 'module/AdminPage.php';
require_once 'module/AdminForm.php';
require_once 'module/AdminNotice.php';
require_once 'module/AdminPluginCheck.php';


Core::addModule(new \Core\Module\AdminPage());
Core::addModule(new \Core\Module\AdminForm());
Core::addModule(new \Core\Module\AdminNotice());
Core::addModule(new \Core\Module\AdminPluginCheck());