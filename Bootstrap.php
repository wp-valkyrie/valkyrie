<?php

use Valkyrie\System;

/**
 * Main System Components
 */
require_once 'Valkyrie/System.php';
require_once 'Valkyrie/Module.php';
require_once 'Valkyrie/RequireHandler.php';
require_once 'Valkyrie/Form.php';

/**
 * Module intercommunication
 */
require_once 'Valkyrie/API.php';
require_once 'Valkyrie/Pipeline.php';
require_once 'Valkyrie/Component.php';

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