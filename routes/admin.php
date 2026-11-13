<?php

require_once PATH_CONTROLLER_ADMIN . 'AdminController.php';

return static function (): void {
    (new AdminController())->handle();
};