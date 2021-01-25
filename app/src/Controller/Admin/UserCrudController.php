<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            yield IntegerField::new('id');
            yield TextField::new('username');
            yield TextField::new('email');
            yield BooleanField::new('isActive', 'active')->setFormTypeOption('disabled', 'disabled');
            yield DateTimeField::new('createdAt');
        } else {
            yield FormField::addPanel('Account Information');
            yield TextField::new('username');
            yield TextField::new('email');
            yield TextField::new('plainPassword')->setRequired(False)->onlyOnForms()->setFormType(PasswordType::class);

            if ($this->isGranted('ROLE_ADMIN')) {
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
                yield BooleanField::new('isActive');
                yield DateTimeField::new('deletedAt')->setFormat('full');
                yield DateTimeField::new('updatedAt')->setFormTypeOption('disabled', 'disabled');
                yield DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled');
            }
        }
    }
}
