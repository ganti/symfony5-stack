<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LogUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private LogUserService $log;

    public function __construct(LogUserService $log)
    {
        $this->log = $log;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        if( isset($_ENV['EASYADMIN_REGISTRATION_ACTIVE']) ){
            $EASYADMIN_REGISTRATION_ACTIVE = (bool) $_ENV['EASYADMIN_REGISTRATION_ACTIVE'];
        }else{
            $EASYADMIN_REGISTRATION_ACTIVE = False;
        }

        if( isset($_ENV['EASYADMIN_PASSWORD_RESET_ACTIVE']) ){
            $EASYADMIN_PASSWORD_RESET_ACTIVE = (bool) $_ENV['EASYADMIN_PASSWORD_RESET_ACTIVE'];
        }else{
            $EASYADMIN_PASSWORD_RESET_ACTIVE = False;
        }

        return $this->render('@EasyAdmin/login/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            'translation_domain' => 'admin',

            'page_title' => 'ACME login',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('admin_dashboard'),

            // the 'name' HTML attribute of the <input> used for the username field (default: '_username', default: '_password')
            'username_parameter' => 'username',
            'password_parameter' => 'password',

            'registration_active' => $EASYADMIN_REGISTRATION_ACTIVE,
            'passwordreset_active' => $EASYADMIN_PASSWORD_RESET_ACTIVE,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->log->logout(True);
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
