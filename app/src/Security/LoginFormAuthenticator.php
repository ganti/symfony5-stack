<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\LogUserService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{

    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private LogUserService $log;
    
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, LogUserService $log)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->log = $log;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'email' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user_username_exists = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);
        $user_email_exists = null;
        $activeParams = null;

        if (!$user_username_exists) {
            $user_email_exists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

            if (!$user_email_exists) {
                $this->log->login('User could not be found.', false);
                throw new CustomUserMessageAuthenticationException('User could not be found.');
            }else{
                $user_username_exists = null;
                $activeParams['email'] = $credentials['email'];
            }
        }else{
            $activeParams['username'] = $credentials['username'];
        }

        if($activeParams != null){
            $activeParams['deletedAt'] = null;
            $activeParams['isActive'] = True;

            $user_active = $this->entityManager->getRepository(User::class)->findOneBy($activeParams);
            
            if (!$user_active) {
                $this->log->login('User is not active', False);
                throw new CustomUserMessageAuthenticationException('User not active.');
            }else{
                $this->log->login('', True);
            }
        }
        
        return $user_active;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $return = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        if($return == False){
            $this->log->login('wrong credentials', False);
        }
        return $return;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        $this->log->logout('',True);
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

}
