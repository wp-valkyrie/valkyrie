<?php


require_once 'Core.php';
require_once 'Module.php';
require_once 'RequireHandler.php';

Core::prepare();


require_once 'module/AdminPage.php';
require_once 'module/AdminForm.php';

Core::addModule(new \Core\Module\AdminPage());
Core::addModule(new \Core\Module\AdminForm());