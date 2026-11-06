<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController())->index(),
    'products'  => (new ProductController())->index(),
    'product'   => (new ProductController())->show(),
    default     => (new ProductController())->notFound(),
};
