<?php

namespace App\Tests\Security;

use App\Security\UserInterface;
use App\Tests\AbstractSecurityTest;

/**
 * Class UserTest
 * @package App\Tests\Security
 */
class UserTest extends AbstractSecurityTest
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testExpectedAuthenticatedUserObjectProperties(): void
    {
        $this->runCommand('doctrine:fixtures:load --append --group=UserFixtures');
        $this->output->writeln("\r\n<info>Test the properties of a logged in User</info>");
        $this->client->catchExceptions(false);

        $this->simulateLogin('ROLE_ADMIN');
        $user = $this->getUser();
        $this->assertEquals(true, $user instanceof UserInterface);
        $this->assertEquals('dextermorgan@cvlt.dev', $user->getUsername());
        $this->assertEquals(2, count($user->getRoles()));
        $this->assertEquals(1, $user->getId());

        $this->simulateLogin();
        $user = $this->getUser();
        $this->assertEquals(true, $user instanceof UserInterface);
        $this->assertEquals('eleven@cvlt.dev', $user->getUsername());
        $this->assertEquals(1, count($user->getRoles()));
        $this->assertEquals(2, $user->getId());

        $this->output->writeln("\r\n<info>Wait for token expiration ...</info>");
        sleep(6);
        $this->assertEquals(null, $this->getUser());
    }
}