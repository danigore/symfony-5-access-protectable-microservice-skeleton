<?php

namespace App\Tests\Controller;

use App\Tests\AbstractSecurityTest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AuthorizationTestRoutesControllerTest
 * @package App\Tests\Controller
 */
class AuthorizationTestRoutesControllerTest extends AbstractSecurityTest
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testUserRoleRequired()
    {
        $this->output->writeln("\r\n<info>Test a route where user role (ROLE_USER) is required:</info>");
        $this->client->catchExceptions(false);

        $this->accessDeniedWithoutLoginTest('/authorization-tests/user-role');
        $this->simulateLogin();

        $this->output->writeln("<info>Valid request with ROLE_USER ...</info>");
        $this->client->request('GET', '/authorization-tests/user-role');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testAdminRoleRequired()
    {
        $this->output->writeln("\r\n<info>Test a route where admin role (ROLE_ADMIN) is required:</info>");
        $this->client->catchExceptions(false);

        $this->accessDeniedWithoutLoginTest('/authorization-tests/admin-role');
        $this->accessDeniedForRoleTest('/authorization-tests/admin-role');
        $this->simulateLogin('ROLE_ADMIN');

        $this->output->writeln("<info>Valid request with ROLE_ADMIN ...</info>");
        $this->client->request('GET', '/authorization-tests/admin-role');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}