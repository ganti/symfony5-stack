<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DatetimeFilter;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;




/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class LogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Log::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //->setEntityLabelInSingular('Log')
            ->setEntityLabelInPlural('Logs')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setDateFormat('full')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(100)
            ->setSearchFields(['type', 'action'])
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {

        if (Crud::PAGE_INDEX === $pageName) {

            yield IntegerField::new('id')->setCssClass('col-auto');
            yield TextField::new('level');
            yield TextField::new('fullcontext', 'Context');
            yield TextareaField::new('message');
            yield DateTimeField::new('createdAt')->setFormat('short', 'short');

        }else if (Crud::PAGE_DETAIL=== $pageName) { 

            yield FormField::addPanel('General');
                yield IdField::new('id');
                yield TextField::new('level');
                yield TextField::new('fullcontext', 'Context');
                yield TextareaField::new('message');
                yield DateTimeField::new('createdAt')->setFormat('short','short')->setFormTypeOption('disabled','disabled');
            
            yield FormField::addPanel('Request Information');
                yield TextField::new('requestMethod', 'Request Method');
                yield TextField::new('requestPath', 'Request Path');
                yield TextField::new('clientIP', 'Client IP');
                yield AssociationField::new('user', 'User');
                yield TextField::new('clientLocale', 'Locale');

        }
        return $this;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('level', 'Level')
            ->add('message', 'Message')
            //->add(TextFilter::new('fullcontext')->mapped(false))
            ->add('user', 'User')
            ->add('createdAt', 'created at')
        ;
    }
    
    public function configureActions(Actions $actions): Actions
    {
        
        return $actions
            ->disable('new')
            ->disable('edit')
            ->disable('delete')
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)     
        ;
    }
}
