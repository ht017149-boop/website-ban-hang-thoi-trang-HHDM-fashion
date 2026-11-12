<?php

require_once PATH_CONTROLLER_CLIENT . 'HomeController.php';
require_once PATH_CONTROLLER_CLIENT . 'AuthController.php';

return static function (): void {
    $page = (string) ($_GET['page'] ?? 'home');

    switch ($page) {
        case 'register':
            (new AuthController())->register();
            return;
        case 'login':
            (new AuthController())->login();
            return;
        case 'logout':
            (new AuthController())->logout();
            return;
        case 'home':
        default:
            (new HomeController())->index();
    }
};
