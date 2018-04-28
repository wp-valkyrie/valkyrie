<?php


require_once 'Core.php';
require_once 'Module.php';
require_once 'RequireHandler.php';

Core::prepare();


require_once 'module/Admin.php';
require_once 'module/Form.php';

Core::addModule(new \Core\Module\Admin());
Core::addModule(new \Core\Module\Form());