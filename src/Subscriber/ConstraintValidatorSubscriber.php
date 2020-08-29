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
    public function prePersist(LifecycleEventArgs $args): void
    {
        if (0 < count($constraintViolationInterfaces = $this->validator->validate($args->getObject()))) {
            $violations = ['messages' =>[]];
            foreach($constraintViolationInterfaces as $violation) {
                $violations['messages'][] = (string)$violation->getMessage();
            }

            throw new ConstraintViolationException(\json_encode($violations));   
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