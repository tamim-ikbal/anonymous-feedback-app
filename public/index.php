<?php

use Core\Router;
use Core\Session;

//Entry Point
define('ABSPATH',__DIR__.'/../');

//Config
define('SITE_URL','http://feedback-app.test');

//Vendor autoload
require ABSPATH.'autoload.php';

//Require functions.php
require_once ABSPATH.'functions.php';

//Require bootstrap.php
require_once ABSPATH.'bootstrap/bootstrap.php';

