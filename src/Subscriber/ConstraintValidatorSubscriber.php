<?php

namespace App\Subscriber;

use App\Exception\ConstraintViolationException;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ConstraintValidatorSubscriber
 * @package App\Subscriber
 */
class ConstraintValidatorSubscriber implements EventSubscriber
{
    /**
     * @var ValidatorInterface $validator
     */
    private ValidatorInterface $validator;

    /**
     * ConstraintValidatorSubscriber constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate the entity before the respective EntityManager persist operation for that entity is executed.
     *
     * @param LifecycleEventArgs $args
     * @return void
     * @throws ConstraintViolationException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if (0 < count($violations = $this->validator->validate($args->getObject()))) {
            throw new ConstraintViolationException($violations->get(0)->getMessage());   
        }
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [Events::prePersist];
    }
}