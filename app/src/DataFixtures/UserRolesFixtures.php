<?php

namespace App\DataFixtures;
use App\Entity\User;
use App\Entity\Userrole;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManager; 

class UserRolesFixtures extends Fixture
{
    private $encoder;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {

        
        $roles = [  'Super Admin' => 'ROLE_SUPER_ADMIN',
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER'];
        foreach ($roles as $k => $v) {
            $role = new Userrole();
            $role->setRole($v);
            $role->setName($k);
            $role->setDescription($k.' (System Role)');
            $role->setSystemrole(true);
            $manager->persist($role);
        }

        $role_test = new Userrole();
        $role_test->setRole('ROLE_TEST');
        $role_test->setName('ROLE_TEST');
        $role_test->setDescription('Testrole');
        $role_test->setSystemrole(false);
        $role_test->setParentRole($role);
        $manager->persist($role_test);

        $role_test2 = new Userrole();
        $role_test2->setRole('ROLE_TEST2');
        $role_test2->setName('ROLE_TEST2');
        $role_test2->setDescription('Testrole 2');
        $role_test2->setSystemrole(false);
        $role_test2->setParentRole($role_test);
        $manager->persist($role_test2);

        $manager->flush();
    }
}
