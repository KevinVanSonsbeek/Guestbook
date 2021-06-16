<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\ConferenceRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private ConferenceRepository $conferenceRepository,
    ) {
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
//            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('conferences', [$this, 'getConferences']),
        ];
    }

    public function getConferences()
    {
        return $this->conferenceRepository->findAll();
    }
}
