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

        // Categorías destacadas para la home
        $categories = $categoryModel->getFeatured();

        // Mejora 3 — home personalizada:
        // Usuario autenticado con historial → productos recomendados según intereses
        // Usuario no autenticado o sin historial → productos más recientes
        if ($this->isLoggedIn()) {
            $userId           = (int) $_SESSION['user_id'];
            $featuredProducts = $productModel->getRecommended($userId, 8);
            $isPersonalized   = true;
        } else {
            $featuredProducts = $productModel->getFeatured(8);
            $isPersonalized   = false;
        }

        $this->view('home.index', [
            'pageTitle'        => 'Inicio | PrimeLux SmartShop',
            'categories'       => $categories,
            'featuredProducts' => $featuredProducts,
            'isPersonalized'   => $isPersonalized,
        ]);
    }

    public function about(array $params): void
    {
        $categoryModel = new CategoryModel();

        $this->view('about.index', [
            'pageTitle'  => 'Sobre nosotros | PrimeLux SmartShop',
            'categories' => $categoryModel->getAll(),
        ]);
    }

    public function faq(array $params): void
    {
        $this->view('static.faq', [
            'pageTitle' => 'Preguntas frecuentes | PrimeLux SmartShop',
        ]);
    }

    public function envios(array $params): void
    {
        $this->view('static.envios', [
            'pageTitle' => 'Envíos y devoluciones | PrimeLux SmartShop',
        ]);
    }

    public function privacidad(array $params): void
    {
        $this->view('static.privacidad', [
            'pageTitle' => 'Política de privacidad | PrimeLux SmartShop',
        ]);
    }

    public function cookies(array $params): void
    {
        $this->view('static.cookies', [
            'pageTitle' => 'Política de cookies | PrimeLux SmartShop',
        ]);
    }

    public function terminos(array $params): void
    {
        $this->view('static.terminos', [
            'pageTitle' => 'Términos y condiciones | PrimeLux SmartShop',
        ]);
    }
}
