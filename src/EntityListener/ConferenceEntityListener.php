<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Conference;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AutoconfigureTag('doctrine.orm.entity_listener')]
final class ConferenceEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger, Uuid::uuid4());
    }

    public function preUpdate(Conference $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger, Uuid::uuid4());
    }
}
