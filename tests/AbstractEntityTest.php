<?php

namespace App\Tests;

use App\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractEntityTest
 * @package App\Tests
 */
abstract class AbstractEntityTest extends AbstractFunctionalTest
{
    /**
     * @var EntityManager $entityManager
     */
    protected EntityManager $entityManager;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Simulate an invalid entity persist: expected ConstraintViolationException
     *
     * @param [type] $entity
     * @param string|null $message
     * @return void
     */
    protected function expectedConstraintViolationExceptionOnPersist($entity, ?string $message = null): void
    {
        $this->output->writeln("<info>Simulate an invalid DB flush: expected ConstraintViolationException ...</info>");
        $exceptionThrown = false;
        try {
            $this->entityManager->persist($entity);
        } catch (ConstraintViolationException $e) {
            $exceptionThrown = true;

            if ($message) {
                $this->output->writeln("<info>Exception message is: $message</info>");
                $this->assertSame($message, $e->getMessage());
            }
        }
        $this->assertSame(true, $exceptionThrown);
    }
}