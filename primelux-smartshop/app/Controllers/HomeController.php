<?php
declare(strict_types=1);

require_once APP_PATH . '/Models/CategoryModel.php';
require_once APP_PATH . '/Models/ProductModel.php';

class HomeController extends Controller
{
    public function index(array $params): void
    {
        $categoryModel = new CategoryModel();
        $productModel  = new ProductModel();

        $this->view('home.index', [
            'pageTitle'        => 'Inicio | PrimeLux SmartShop',
            'categories'       => $categoryModel->getAll(),
            'featuredProducts' => $productModel->getFeatured(8),
        ]);
    }

    public function about(array $params): void
    {
        $this->view('about.index', [
            'pageTitle' => 'Sobre nosotros | PrimeLux SmartShop',
        ]);
    }
}
