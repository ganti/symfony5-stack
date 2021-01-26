<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    private AdminContextProvider $adminContextProvider;
    private Security $security;

    public function __construct(AdminContextProvider $adminContextProvider, Security $security)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User ')
            ->setEntityLabelInPlural('Users')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setSearchFields(['username', 'email'])
        ;
    }

    /*
     * Checks if logged in user ist the same as in the CRUD request
     */ 
    private function getIsLoggedInUserEditingUserCrud(): bool{
        $requestUserId = $this->adminContextProvider->getContext()->getRequest()->query->get('entityId');
        $loggedInUserId = $this->security->getUser()->getId();
        return ($requestUserId == $loggedInUserId AND $requestUserId != null);
    }

    public function configureFields(string $pageName): iterable
    {

        if (Crud::PAGE_INDEX === $pageName) {
            if ($this->isGranted('ROLE_ADMIN')) {
                yield IntegerField::new('id');
                yield TextField::new('username');
                yield TextField::new('email');
                yield BooleanField::new('isActive', 'active')->setFormTypeOption('disabled', 'disabled');
                yield DateTimeField::new('createdAt');
            }
        } else {

            //User Profile
            if ($this->getIsLoggedInUserEditingUserCrud() AND $this->isGranted('ROLE_ADMIN') == False) {
                yield FormField::addPanel('Account Information');
                yield TextField::new('username');
                yield TextField::new('email');
                yield TextField::new('plainPassword')->setRequired(false)->onlyOnForms()->setFormType(PasswordType::class);
            }
            
            if ($this->isGranted('ROLE_ADMIN')) {
                yield FormField::addPanel('Account Information');
                yield TextField::new('username');
                yield TextField::new('email');
                yield TextField::new('plainPassword')->setRequired(false)->onlyOnForms()->setFormType(PasswordType::class);


                yield FormField::addPanel('Admin Settings');
                yield ChoiceField::new('roles', 'Roles')
                                            ->allowMultipleChoices()
                                            ->autocomplete()
                                            ->setChoices(
                                                [   'User' => 'ROLE_USER',
                                                    'Admin' => 'ROLE_ADMIN',
                                                    'SuperAdmin' => 'ROLE_SUPER_ADMIN'
                                                ]
                                            );
                yield BooleanField::new('Active', 'active');
                yield DateTimeField::new('deletedAt')->setFormat('full');
                yield DateTimeField::new('updatedAt')->setFormTypeOption('disabled', 'disabled');
                yield DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled');
            }
        }
    }
}
