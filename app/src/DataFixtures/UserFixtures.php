<?php

namespace App\DataFixtures;
use App\Entity\User;
use App\Entity\Userrole;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManager; 

class UserFixtures extends Fixture
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
        
        //Create the following Userroles
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'];
        foreach ($roles as $r) {
            $role = new Userrole();
            $role->setRole($r);
            $role->setName($r);
            $role->setDescription('System Role');
            $role->setSystemrole(True);
            $manager->persist($role);
        }

        $role_test = new Userrole();
        $role_test->setRole('ROLE_TEST');
        $role_test->setName('ROLE_TEST');
        $role_test->setDescription('Testrole');
        $role_test->setSystemrole(False);
        $role_test->setParentRole($role);
        $manager->persist($role_test);

        $role_test2 = new Userrole();
        $role_test2->setRole('ROLE_TEST2');
        $role_test2->setName('ROLE_TEST2');
        $role_test2->setDescription('Testrole 2');
        $role_test2->setSystemrole(False);
        $role_test2->setParentRole($role_test);
        $manager->persist($role_test2);

        
        //Create the following users
         $items = array(
            ['user' => 'superadmin', 'pass' => 'admin', 'role' => 'ROLE_SUPER_ADMIN'],
            ['user' => 'admin',      'pass' => 'admin', 'role' => 'ROLE_ADMIN'],
            ['user' => 'user',       'pass' => 'admin', 'role' => 'ROLE_USER']
        );
        foreach ($items as $i) {
            $user = new User();
            $user->setUsername($i['user']);
            $user->setEmail($i['user'].'@foo.dev');
        
            $password = $this->encoder->encodePassword($user, $i['pass']);
            $user->setPassword($password);
            $user->setRoles([$i['role']]);

            $manager->persist($user);
        }
        $manager->flush();
    
        // set Actvive = False
        foreach (['user', 'superadmin'] as $usr) {
            $user =  $this->entityManager->getRepository(User::class)
                        ->findOneBy(['username' => $usr])
                            ->setActive(false);
            $manager->persist($user);
            $manager->flush();
        }
        
    }
}
