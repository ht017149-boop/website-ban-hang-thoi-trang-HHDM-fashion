<?php

require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

session_start();

$dispatch = require __DIR__ . '/routes/client.php';
$dispatch();
