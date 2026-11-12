<?php

class HomeController
{
    public function index(): void
    {
        require PATH_VIEW_CLIENT . 'home.php';
    }
}
