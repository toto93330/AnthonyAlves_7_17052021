<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\ApiKey;
use App\Entity\Product;
use App\Entity\Customer;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProductCrudController;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdminController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel Bilmo Api');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Users Utility', 'fas fa-user');
        yield MenuItem::linkToCrud('Users', '', User::class);
        yield MenuItem::section('Products Utility', 'fas fa-shopping-cart');
        yield MenuItem::linkToCrud('Products', '', Product::class);
        yield MenuItem::section('Customers Utility', 'fas fa-users');
        yield MenuItem::linkToCrud('Customers', '', Customer::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
