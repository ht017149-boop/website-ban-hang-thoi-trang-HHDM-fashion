<?php

class HomeController
{
    public function index(): void
    {
        $productModel = new ProductModel();
        $products = $productModel->featured();
        $officeProducts = $productModel->byCollection('Đồ công sở');
        $streetwearProducts = $productModel->byCollection('Streetwear');
        $title = 'HHDM Studio | Thời trang nam hiện đại';

        require PATH_VIEW . 'main.php';
    }
}
