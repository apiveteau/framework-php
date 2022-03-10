<?php
/**
 * This file is part of the Framework project
 * Copyright 2019 - Core team
 * Authors :
 *  - PIVETEAU Anatole<anatole.piveteau@gmail.com>
 *  - GAZAUBE François<>
 */

/**
 * Base environment vars
 */
define("PATH_ROOT", __DIR__ . DIRECTORY_SEPARATOR);
define("PATH_MODULE", PATH_ROOT . "Modules" . DIRECTORY_SEPARATOR);
define("PATH_PUBLIC", PATH_ROOT . "Public" . DIRECTORY_SEPARATOR);
define("PATH_CORE", PATH_ROOT . "Core" . DIRECTORY_SEPARATOR);
define("PATH_SITE", PATH_ROOT . "Site" . DIRECTORY_SEPARATOR);
define("PATH_LOG", PATH_ROOT . "Log" . DIRECTORY_SEPARATOR);
define("PATH_CACHE", PATH_ROOT . "Cache" . DIRECTORY_SEPARATOR);

/**
 * Including loader
 */
include_once __DIR__ . "/Core/Loader/Loader.php";

use Core\Environment;
use Core\Kernel;
use Core\Loader;

/**
 * Including classes sorting by constraints
 */
Loader::explore(PATH_CORE, "Interface");
Loader::explore(PATH_CORE, "", "Interface");
Loader::explore(PATH_MODULE, "Interface", "Tests");
Loader::explore(PATH_MODULE, "", "Tests");
Loader::explore(PATH_SITE, "Interface");
Loader::explore(PATH_SITE, "", "Interface");

/**
 * Base environment vars
 */
Environment::read(PATH_ROOT . ".env");
/**
 * Default routing file
 */
if (file_exists(PATH_ROOT . ".routing"))
    Loader::$ROUTING[] = PATH_ROOT . ".routing";

/**
 * Initialize kernel
 */
try {
    session_start();
    Kernel::boot();
} catch (Exception $exception) {
    echo "<h1>" . $exception->getMessage() . "</h1><p><span>" . $exception->getCode() . "</span> on line <span>" . $exception->getLine() . "</span></p>";
}
