<?php

define("BASE_URL", "http://localhost/HHDM_Fashion/");
define("BASE_URL_ADMIN", BASE_URL . "index.php?page=admin");

define("PATH_ROOT", __DIR__ . "/../");

define("PATH_VIEW_CLIENT", PATH_ROOT . "views/client/");
define("PATH_VIEW_ADMIN", PATH_ROOT . "views/admin/");

define("PATH_VIEW_MAIN_CLIENT", PATH_ROOT . "views/client/main.php");
define("PATH_VIEW_MAIN_ADMIN", PATH_ROOT . "views/admin/main.php");

define("BASE_ASSETS_UPLOADS", BASE_URL . "assets/uploads/");
define("PATH_ASSETS_UPLOADS", PATH_ROOT . "assets/uploads/");

define("PATH_CONTROLLER_CLIENT", PATH_ROOT . "controllers/client/");
define("PATH_CONTROLLER_ADMIN", PATH_ROOT . "controllers/admin/");

define("PATH_MODEL", PATH_ROOT . "models/");

define("DB_HOST", "localhost");
define("DB_PORT", "3306");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "website_ban_hang_thoi_trang");
$localPaymentConfigPath = __DIR__ . '/payments.local.php';
$localPaymentConfig = is_file($localPaymentConfigPath) ? require $localPaymentConfigPath : [];
define("PAYOS_CLIENT_ID", getenv('PAYOS_CLIENT_ID') ?: (string) ($localPaymentConfig['client_id'] ?? ''));
define("PAYOS_API_KEY", getenv('PAYOS_API_KEY') ?: (string) ($localPaymentConfig['api_key'] ?? ''));
define("PAYOS_CHECKSUM_KEY", getenv('PAYOS_CHECKSUM_KEY') ?: (string) ($localPaymentConfig['checksum_key'] ?? ''));
define("APP_PUBLIC_URL", rtrim(getenv('APP_PUBLIC_URL') ?: (string) ($localPaymentConfig['app_public_url'] ?? BASE_URL), '/') . '/');
define("DB_OPTIONS", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
