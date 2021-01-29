<?php

namespace App\Controller\Admin;

use App\Entity\UserRole;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;


/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserRole::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('User Roles')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setPageTitle('new', 'New Role')

            ->setDateFormat('full')
            ->setDefaultSort(['id' => 'ASC'])
            ->setSearchFields(['role', 'description'])
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {

        if (Crud::PAGE_INDEX === $pageName) {

            yield TextField::new('role', 'Role');
            yield TextField::new('name', 'Name');
            yield TextField::new('description', 'Description');
            yield ArrayField::new('ParentRoleRecursive', 'Parent Roles');
            yield BooleanField::new('active', 'is active')->setFormTypeOption('disabled','disabled');
            yield DateTimeField::new('createdAt');

        } else {
            yield FormField::addPanel('Role')->setIcon('fa fa-user-tag');
            if (Crud::PAGE_NEW === $pageName) {
                yield TextField::new('role', 'Role');
            } elseif (Crud::PAGE_EDIT === $pageName) {
                yield TextField::new('role', 'Role')->setFormTypeOption('disabled', 'disabled');
            }

            yield TextField::new('name', 'Name');
            yield TextField::new('description', 'Description');
            yield AssociationField::new('parentRole', 'Parent Role');
            yield BooleanField::new('active', 'is active');
            yield BooleanField::new('systemrole', 'is systemrole')->setFormTypeOption('disabled','disabled');
            yield FormField::addPanel('Timestamps')->setIcon('fa fa-clock');
            yield DateTimeField::new('createdAt', 'created')->setFormTypeOption('disabled', 'disabled');
            yield DateTimeField::new('updatedAt', 'updated')->setFormTypeOption('disabled', 'disabled');
            yield DateTimeField::new('deletedAt', 'deleted');

        }
        return $this;
    }

    public function configureActions(Actions $actions): Actions
    {
        
        return $actions
        ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
        ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }

}
