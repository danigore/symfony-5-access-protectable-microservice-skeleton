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
     * Simulate an invalid DB flush: expected ConstraintViolationException
     *
     * @param [type] $entity
     * @param string|null $message
     * @return void
     */
    protected function expectedConstraintViolationException($entity, ?string $message = null): void
    {
        $this->output->writeln("<info>Simulate an invalid DB flush: expected ConstraintViolationException ...</info>");
        $exceptionThrown = false;
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (ConstraintViolationException $e) {
            $exceptionThrown = true;
        }
        $this->assertEquals(true, $exceptionThrown);
    }
}