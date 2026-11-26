<?php

require_once PATH_CONTROLLER_CLIENT . "HomeController.php";
require_once PATH_CONTROLLER_CLIENT . "AuthController.php";

return static function (): void {
    $page = (string) ($_GET["page"] ?? "home");

    switch ($page) {
        case "create_order":
            (new HomeController())->createOrder();
            return;
        case "payment_webhook":
            (new HomeController())->paymentWebhook();
            return;
        case "payment_return":
            (new HomeController())->paymentReturn();
            return;
        case "payment_status":
            (new HomeController())->paymentStatus();
            return;
        case "payment_success":
            (new HomeController())->paymentSuccess();
            return;
        case "payment_cancel":
            (new HomeController())->paymentCancel();
            return;
        case "my_orders":
            (new HomeController())->myOrders();
            return;
        case "register":
            (new AuthController())->register();
            return;
        case "login":
            (new AuthController())->login();
            return;
        case "logout":
            (new AuthController())->logout();
            return;
        case "product_detail":
            (new HomeController())->detail();
            return;
        case "home":
        default:
            (new HomeController())->index();
    }
};
