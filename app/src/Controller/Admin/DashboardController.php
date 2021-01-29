<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\Log;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class DashboardController extends AbstractDashboardController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
            yield MenuItem::linkToCrud('User Roles', 'fas fa-user-tag', UserRole::class);
            yield MenuItem::linkToCrud('Logs', 'fas fa-list', Log::class);
            yield MenuItem::section();
        }
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            
            //Icon
            ->displayUserAvatar(true)
            ->setGravatarEmail($this->security->getUser()->getEmail())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToCrud('My Profile', 'fa fa-id-card', User::class)
                    ->setAction('edit')
                    ->setEntityId($this->security->getUser()->getId()),
                MenuItem::section(),
            ]);
    }

    //Default Crud Settings
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(30)
            ->setTimezone('Europe/Zurich')
            ->setDateTimeFormat('yyyy-MM-dd HH:mm:ss')
            ->setDateFormat('yyyy-MM-dd')
            ->setTimeFormat('HH:mm:ss')
        ;
    }


    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }
}