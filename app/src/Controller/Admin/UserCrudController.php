<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    private AdminContextProvider $adminContextProvider;
    private Security $security;


    /**
     * UserCrudController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(AdminContextProvider $adminContextProvider, Security $security, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;
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
            ->setPageTitle('edit', 'Edit user: %entity_label_singular%')
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
            if ($this->getIsLoggedInUserEditingUserCrud() OR $this->isGranted('ROLE_ADMIN')) {

                yield FormField::addPanel('Account Information')->setIcon('far fa-address-card');
                yield TextField::new('username', 'Username')->setFormTypeOption('disabled', 'disabled');
                yield TextField::new('email', 'eMail');
                
                yield FormField::addPanel('Change password')->setIcon('fa fa-key');
                yield Field::new('plainPassword', 'New password')
                                            ->onlyOnForms()
                                            ->setFormType(RepeatedType::class)
                                            ->setFormTypeOption('empty_data', '')
                                            ->setFormTypeOptions([
                                                'type' => PasswordType::class,
                                                'first_options' => ['label' => 'New password'],
                                                'second_options' => ['label' => 'Repeat password'],
                                            ]);
            }
            
            if ($this->isGranted('ROLE_ADMIN')) {
                yield FormField::addPanel('Admin Settings')->setIcon('fas fa-users-cog');
                yield ChoiceField::new('roles', 'Role')
                                            ->allowMultipleChoices()
                                            ->autocomplete()
                                            ->setChoices(
                                                [   'User' => 'ROLE_USER',
                                                    'Admin' => 'ROLE_ADMIN',
                                                    'SuperAdmin' => 'ROLE_SUPER_ADMIN'
                                                ]
                                            );
                yield BooleanField::new('active', 'is active');
                yield DateTimeField::new('deletedAt')->setFormat('full');
                yield DateTimeField::new('updatedAt')->setFormTypeOption('disabled', 'disabled');
                yield DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled');
            }
        }
    }


    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        
        // set new password with encoder interface
        if (method_exists($entityInstance, 'setPassword')) {
            $passwords = $this->adminContextProvider->getContext()->getRequest()->request->all()['User']['plainPassword'];
            
            if (isset($passwords['first']) AND empty(trim($passwords['first'])) == False) {
                
                if (trim($passwords['first']) == trim($passwords['second'])) {
                    $plainPassword = trim($passwords['first']);
                }
                if (!empty($plainPassword)) {
                    $encodedPassword = $this->passwordEncoder->encodePassword($this->getUser(), $plainPassword);
                    $entityInstance->setPassword($encodedPassword);
                }else{
                    $entityInstance->eraseCredentials();
                }
            }else{
                $entityInstance->eraseCredentials();
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }


}