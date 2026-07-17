<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\PartnerRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension
{
    public function __construct(
        private readonly PartnerRepository $partnerRepository,
    ) {
    }

    /**
     * @return \App\Entity\Partner[]
     */
    #[\Twig\Attribute\AsTwigFunction(name: 'footer_partners')]
    public function getFooterPartners(): array
    {
        return $this->partnerRepository->findAllForDisplay();
    }
}
