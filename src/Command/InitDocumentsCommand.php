<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-documents',
    description: 'Crée les documents PDF fixes du site (formulaire, questionnaire, règlement, statuts) s\'ils n\'existent pas.',
)]
class InitDocumentsCommand extends Command
{
    /**
     * Liste des documents fixes : clé => nom affiché.
     */
    private const DOCUMENTS = [
        'formulaire_inscription' => 'Formulaire d\'inscription',
        'questionnaire_sante' => 'Questionnaire de santé',
        'reglement_interieur' => 'Règlement intérieur',
        'statuts' => 'Statuts de l\'association',
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DocumentRepository $documentRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $created = 0;

        foreach (self::DOCUMENTS as $key => $label) {
            $existing = $this->documentRepository->findOneBy(['documentKey' => $key]);
            if ($existing !== null) {
                $io->text(sprintf('• %s : déjà présent', $label));
                continue;
            }

            $document = (new Document())
                ->setDocumentKey($key)
                ->setLabel($label);

            $this->em->persist($document);
            ++$created;
            $io->text(sprintf('✓ %s : créé', $label));
        }

        $this->em->flush();

        $io->success(sprintf('%d document(s) créé(s). Rends-toi dans l\'admin pour uploader les PDF.', $created));

        return Command::SUCCESS;
    }
}
