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
     * @param array|null $messages
     * @return void
     */
    protected function expectedConstraintViolationExceptionOnPersist($entity, ?array $messages = null): void
    {
        $this->output->writeln("<info>Simulate an invalid entity persist: expected ConstraintViolationException ...</info>");
        $exceptionThrown = false;
        try {
            $this->entityManager->persist($entity);
        } catch (ConstraintViolationException $e) {
            $exceptionThrown = true;

            if ($messages) {
                $this->output->writeln("<info>Exception error messages: ".$e->getMessage()."</info>");
                $exceptionMessages = array_values(json_decode($e->getMessage())['messages']);
                $this->assertSame(array_values($messages), $exceptionMessages);
            }
        }
        $this->assertSame(true, $exceptionThrown);

        $this->entityManager->clear();
    }

    /**
     * @param [type] $entity
     * @return void
     */
    protected function entityIsValidSoPersistIsPossible($entity): void
    {
        $this->output->writeln("<info>Simulate a valid entity persist ...</info>");
        $exceptionThrown = false;
        try {
            $this->entityManager->persist($entity);
        } catch (ConstraintViolationException $e) {
            $exceptionThrown = true;
        }
        $this->assertSame(false, $exceptionThrown);

        $this->entityManager->clear();
    }
}