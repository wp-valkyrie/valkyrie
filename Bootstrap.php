<?php


use Core\System;

require_once 'Core/System.php';
require_once 'Core/Module.php';
require_once 'Core/RequireHandler.php';
require_once 'Core/Form.php';

require_once 'Modules/Core.php';
require_once 'Modules/CoreAdmin.php';
require_once 'Modules/CoreForm.php';

System::prepare();

//require_once 'Core.php';
//require_once 'Module.php';
//require_once 'RequireHandler.php';
//
//Core::prepare();
//
//require_once 'module/AdminPage.php';
//require_once 'module/AdminForm.php';
//require_once 'module/AdminNotice.php';
//require_once 'module/AdminPluginCheck.php';
//require_once 'module/AdminWidget.php';
//
//Core::addModule(new \Core\Module\AdminPage());
//Core::addModule(new \Core\Module\AdminForm());
//Core::addModule(new \Core\Module\AdminNotice());
//Core::addModule(new \Core\Module\AdminPluginCheck());
//Core::addModule(new \Core\Module\AdminWidget());