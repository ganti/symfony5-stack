<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Log;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin",name="admin_dashboard")
     */
    public function index(): Response
    {
        return $this->render('@EasyAdmin/dashboard/dashboard.html.twig');
        return parent::index(); // orginal

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('Logs', 'fas fa-list', Log::class);
        }
    }

    //Default Crud Settings
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(30)
        ;
    }
}


