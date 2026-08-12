<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\DocumentRepository;
use App\Repository\PartnerRepository;

class AppExtension
{
    public function __construct(
        private readonly PartnerRepository $partnerRepository,
        private readonly DocumentRepository $documentRepository,
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

    /**
     * Renvoie l'URL publique d'un document PDF par sa clé, ou null si absent.
     */
    #[\Twig\Attribute\AsTwigFunction(name: 'document_url')]
    public function getDocumentUrl(string $key): ?string
    {
        $filename = $this->documentRepository->getFilenameByKey($key);

        return $filename !== null ? '/uploads/documents/'.$filename : null;
    }
}
