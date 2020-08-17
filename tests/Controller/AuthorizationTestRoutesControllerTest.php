<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AuthorizationTestRoutesControllerTest
 * @package App\Tests\Controller
 */
class AuthorizationTestRoutesControllerTest extends AbstractControllerTest
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testUserRoleRequired()
    {
        $this->output->writeln("\r\n<info>Test a route where user role (ROLE_USER) is required:</info>");
        $this->client->catchExceptions(false);

        // Invalid request without JWT token
        $this->output->writeln("<info>Invalid request without JWT token ...</info>");
        $exceptionThrown = false;
        try {
            $this->client->request('GET', '/authorization-tests/user-role');
        } catch (AccessDeniedException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);

        // Simulate the Authentication: 
        $this->output->writeln("\n<info>Simulate the Authentication with a Token</info>");
        $this->simulateLogin();

        // Valid request with ROLE_USER
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

        // Invalid request without JWT token
        $this->output->writeln("<info>Invalid request without JWT token ...</info>");
        $exceptionThrown = false;
        try {
            $this->client->request('GET', '/authorization-tests/admin-role');
        } catch (AccessDeniedException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);

        // Simulate the Authentication: 
        $this->output->writeln("\n<info>Simulate the Authentication with ROLE_USER</info>");
        $this->simulateLogin();

        // Invalid request with ROLE_USER
        $this->output->writeln("<info>Invalid request with ROLE_USER ...</info>");
        $exceptionThrown = false;
        try {
            $this->client->request('GET', '/authorization-tests/admin-role');
        } catch (AccessDeniedException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);

        $this->output->writeln("\n<info>Simulate the Authentication with ROLE_ADMIN</info>");
        $this->simulateLogin('ROLE_ADMIN');

        $this->output->writeln("<info>Valid request with ROLE_ADMIN ...</info>");
        $this->client->request('GET', '/authorization-tests/admin-role');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}