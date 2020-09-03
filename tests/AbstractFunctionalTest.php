<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class AbstractFunctionalTest
 * @package App\Tests
 */
abstract class AbstractFunctionalTest extends WebTestCase
{
    /**
     * @var EntityManager $manager
     */
    private EntityManager $manager;

    /**
     * @var ORMExecutor $executor
     */
    private ORMExecutor $executor;

    /**
     * @var KernelBrowser $client
     */
    protected KernelBrowser $client;

    /**
     * @var ConsoleOutput $output
     */
    protected ConsoleOutput $output;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // FIX: Calling "Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient()"
        // while a kernel has been booted is deprecated since Symfony 4.4 and will throw in 5.0,
        // ensure the kernel is shut down before calling the method.
        // https://github.com/symfony/symfony-docs/issues/12961#issuecomment-576021219
        self::ensureKernelShutdown();

        // Configure variables
        $this->client = static::createClient();
        $this->manager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->executor = new ORMExecutor($this->manager, new ORMPurger());
        $this->output = new ConsoleOutput();

        // Run the schema update tool using our entity metadata
        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->updateSchema($this->manager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @param $fixture
     * @return void
     */
    protected function loadFixture($fixture): void
    {
        $loader = new Loader();

        $fixtures = is_array($fixture) ? $fixture : [$fixture];

        foreach ($fixtures as $item) {
            $loader->addFixture($item);
        }

        $this->executor->execute($loader->getFixtures());
    }

    /**
     * @param string $input
     * @throws \Exception
     */
    protected function runCommand(string $input): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $application->run(new StringInput($input), new NullOutput());
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getJsonResponseContentValue(string $key)
    {
        if (empty($responseContent = json_decode($this->client->getResponse()->getContent(), true))) {
            return null;
        }

        if (!empty($responseContent[$key])) {
            return $responseContent[$key];
        }

        if (!is_array($responseContent)) {
            return null;
        }

        $responseContent = reset($responseContent);

        if (is_array($responseContent) && !empty($responseContent[$key])) {
            return $responseContent[$key];
        }

        return null;
    }
    
    /**
     * @param string $method
     * @param string $uri
     * @return void
     */
    protected function methodNotAllowedOnRoute(string $method, string $uri): void
    {
        $exceptionThrown = false;
        try {
            $this->client->request($method, $uri);
        } catch (MethodNotAllowedHttpException $e) {
            $exceptionThrown = true;
        }
        $this->assertSame(true, $exceptionThrown);
    }

    /**
     * Assert the values of two arrays, so the order of elements not important
     * 
     * @param [type] $array1
     * @param [type] $array2
     * @return void
     */
    protected function arrayValuesAsSame($array1, $array2): void
    {
        sort($array1);
        sort($array2);
        $this->assertSame($array1, $array2);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        (new SchemaTool($this->manager))->dropDatabase();
    }
}