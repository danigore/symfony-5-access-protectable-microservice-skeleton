<?php

namespace App\Tests\Entity;

use App\Entity\Pokemon;
use App\Tests\AbstractEntityTest;

/**
 * Class PokemonTest
 * @package App\Tests\Entity
 */
class PokemonTest extends AbstractEntityTest
{
    /**
     * @return void
     */
    public function testConstraintViolations(): void
    {
        $this->output->writeln("\r\n<info>Test the entity constraint validator subscriber:</info>");

        $pokemon = new Pokemon();
        $this->assertSame('normal', $pokemon->getType());
        $this->expectedConstraintViolationExceptionOnPersist($pokemon, 'Every pokemon has a name!');

        $pokemon->setName('Pikachu');
        $this->entityIsValidSoPersistIsPossible($pokemon);

        $pokemon->setType('whatever');
        $this->expectedConstraintViolationExceptionOnPersist($pokemon, '"whatever" is not a valid type of Pokemon!');

        $pokemon->setType('electric');
        $this->entityIsValidSoPersistIsPossible($pokemon);
    }
}