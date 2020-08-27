<?php

namespace App\Tests;

use App\Security\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AbstractSecurityTest
 * @package App\Tests
 */
abstract class AbstractSecurityTest extends AbstractFunctionalTest
{
    /**
     * Get the logged in User from the JWTTokenAuthenticator.
     * 
     * @return UserInterface|null
     * @throws \InvalidArgumentException If preAuthToken is not of the good type
     * @throws InvalidPayloadException   If the user identity field is not a key of the payload
     * @throws UserNotFoundException     If no user can be loaded from the given token
     */
    protected function getUser(): ?UserInterface
    {
        $rawToken = $this->getToken();
        if (!$rawToken) {
            return null;
        }

        $tokenDecoder = parent::$container->get('lexik_jwt_authentication.encoder.lcobucci');
        $JWTTokenAuthenticator = parent::$container->get('lexik_jwt_authentication.jwt_token_authenticator');

        $payload = $tokenDecoder->decode($rawToken);
        $token = new PreAuthenticationJWTUserToken($rawToken);
        $token->setPayload($payload);

        $user = $JWTTokenAuthenticator->getUser(
            $token,
            parent::$container->get('security.user.provider.concrete.jwt')
        );
        
        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user;
    }
    
    /**
     * @return boolean
     * @throws ParseException
     */
    protected function authorizationHeaderTypeTokenExtractorIsEnabled(): bool
    {
        $lexikJwtConfig = Yaml::parseFile(parent::$kernel->getContainer()
            ->getParameter('kernel.project_dir').'/config/packages/lexik_jwt_authentication.yaml');
            
        return !empty($lexikJwtConfig['lexik_jwt_authentication']['token_extractors']
            ['authorization_header']['enabled']);
    }

    /**
     * @param bool $refresh
     * @return string|null
     * @throws ParameterNotFoundException
     */
    protected function getToken(): ?string
    {
        $cookie = $this->client->getCookieJar()->get(parent::$kernel->getContainer()
            ->getParameter('app.jwt_cookie_name'));

        if (!$cookie instanceof Cookie) {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * @param string $role
     * @return array
     */
    protected function getPayload(string $role = 'ROLE_USER'): array
    {
        $roles = [];
        switch ($role) {
            case 'ROLE_ADMIN': $roles[] = 'ROLE_ADMIN';
            default: $roles[] = 'ROLE_USER';
        }

        $payload = [
            'iat' => time(),
            'exp' => time()+5,
            'roles' => $roles,
        ];

        if (in_array('ROLE_ADMIN', $roles)) {
            return array_merge($payload, ['id' => 1, 'username' => 'dextermorgan@cvlt.dev']);
        }
        
        return array_merge($payload, ['id' => 2, 'username' => 'eleven@cvlt.dev']);
    }

    /**
     * Simulate a JWT Authentication: Set an http-only cookie with the
     * Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder encoded token
     *
     * @param string $role
     * @return void
     */
    protected function simulateLogin(string $role = 'ROLE_USER'): void
    {
        $this->output->writeln("\n<info>Simulate a login with $role ...</info>");
        
        $this->client->getCookieJar()->set(new Cookie(
            self::$kernel->getContainer()->getParameter('app.jwt_cookie_name'),
            // gets the special container that allows fetching private services
            self::$container->get('lexik_jwt_authentication.encoder.lcobucci')
            ->encode($this->getPayload($role)),
            time()+5));
    }

    /**
     * @param string $token
     * @return array
     */
    protected function getAuthHeaders(?string $token = null): array
    {
        if (!$token) {
            $token = $this->getToken();
        }

        return [
            'HTTP_AUTHORIZATION' => "bearer {$token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ];
    }

    /**
     * @param string $uri
     * @param string $method
     * @return void
     */
    protected function accessDeniedWithoutLoginTest(string $uri, string $method = 'GET'): void
    {
        $this->output->writeln("<info>Simulate an invalid request without JWT token ...</info>");
        $exceptionThrown = false;
        try {
            $this->client->request($method, $uri);
        } catch (AccessDeniedException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);
    }

    /**
     * @param string $uri
     * @param string $role
     * @param string $method
     * @return void
     */
    protected function accessDeniedForRoleTest(string $uri, string $role = 'ROLE_USER', string $method = 'GET'): void
    {
        $this->simulateLogin($role);

        $this->output->writeln("<info>Simulate an invalid request with $role ...</info>");
        $exceptionThrown = false;
        try {
            if ($this->authorizationHeaderTypeTokenExtractorIsEnabled()) {
                $this->client->request($method, $uri, [], [], $this->getAuthHeaders());
            } else {
                $this->client->request($method, $uri);
            }
        } catch (AccessDeniedException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);
    }
}