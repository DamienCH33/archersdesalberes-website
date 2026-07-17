<?php

namespace App\Twig;

use App\Repository\PartnerRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly PartnerRepository $partnerRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('footer_partners', $this->getFooterPartners(...)),
        ];
    }

    /**
     * @return \App\Entity\Partner[]
     */
    public function getFooterPartners(): array
    {
        return $this->partnerRepository->findAllForDisplay();
    }
}
