<?php

use Cli\Helper\Cli;

require_once "vendor/autoload.php";

$arqumentos = (count($argv) > 1) ? $argv : false;
$cli = new Cli($arqumentos);

