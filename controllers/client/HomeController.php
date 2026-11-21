<?php

require_once PATH_MODEL . "ProductModel.php";

class HomeController
{
    public function index(): void
    {
        $homepageData = (new ProductModel())->getHomepageData();
        require PATH_VIEW_CLIENT . "home.php";
    }

    public function detail(): void
    {
        $productId = (int) ($_GET["id"] ?? 0);
        $model = new ProductModel();
        $product = $productId > 0 ? $model->getClientProduct($productId) : null;
        if (!$product) {
            header("Location: index.php?page=home");
            exit();
        }
        $comments = $model->getProductComments($productId);
        require PATH_VIEW_CLIENT . "product_detail.php";
    }
}
