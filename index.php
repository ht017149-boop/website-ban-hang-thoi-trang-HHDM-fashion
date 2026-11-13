<?php

require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

session_start();

$page = (string) ($_GET['page'] ?? 'home');
$routeFile = in_array($page, ['admin', 'admin-accounts'], true)
    ? __DIR__ . '/routes/admin.php'
    : __DIR__ . '/routes/client.php';
$dispatch = require $routeFile;
$dispatch();
