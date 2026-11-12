<?php

require_once PATH_MODEL . 'ProductModel.php';

class HomeController
{
    public function index(): void
    {
        $homepageData = (new ProductModel())->getHomepageData();
        require PATH_VIEW_CLIENT . 'home.php';
    }
}
