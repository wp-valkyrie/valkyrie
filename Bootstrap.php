<?php

use Core\System;

/**
 * Main System Components
 */
require_once 'Core/System.php';
require_once 'Core/Module.php';
require_once 'Core/RequireHandler.php';
require_once 'Core/Form.php';

/**
 * Module intercommunication
 */
require_once 'Core/API.php';
require_once 'Core/Pipeline.php';

/**
 * Main Core-Modules
 */
require_once 'Modules/Core.php';
require_once 'Modules/CoreAdmin.php';
require_once 'Modules/CoreForm.php';

/**
 * Load the System
 */
System::prepare();