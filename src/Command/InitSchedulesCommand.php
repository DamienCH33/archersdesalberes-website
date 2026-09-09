<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-schedules',
    description: 'Crée les créneaux d\'entraînement initiaux s\'ils n\'existent pas.',
)]
class InitSchedulesCommand extends Command
{
    private const SCHEDULES = [
        ['category' => 'Jeunes débutants', 'day' => 'Mercredi', 'season' => 'Toute l\'année', 'start' => '14:30', 'end' => '16:00', 'winter' => 'En salle', 'summer' => 'Sur le terrain', 'order' => 1],
        ['category' => 'Jeunes confirmés', 'day' => 'Mercredi', 'season' => 'Toute l\'année', 'start' => '16:30', 'end' => '18:00', 'winter' => 'En salle', 'summer' => 'Sur le terrain', 'order' => 2],
        ['category' => 'Adultes', 'day' => 'Mercredi', 'season' => 'Toute l\'année', 'start' => '18:30', 'end' => '20:00', 'winter' => 'En salle', 'summer' => 'Sur le terrain', 'order' => 3],
        ['category' => 'Adultes', 'day' => 'Vendredi', 'season' => 'Hiver', 'start' => '20:30', 'end' => '22:00', 'winter' => 'En salle', 'summer' => null, 'order' => 4],
        ['category' => 'Adultes', 'day' => 'Vendredi', 'season' => 'Été', 'start' => '17:30', 'end' => '19:00', 'winter' => null, 'summer' => 'Sur le terrain', 'order' => 5],
        ['category' => 'Section Banyuls-sur-Mer', 'day' => 'Jeudi', 'season' => 'Toute l\'année', 'start' => '21:00', 'end' => '23:00', 'winter' => 'En salle', 'summer' => 'En salle', 'order' => 6],
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ScheduleRepository $scheduleRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->scheduleRepository->count([]) > 0) {
            $io->warning('Des créneaux existent déjà. Commande ignorée.');

            return Command::SUCCESS;
        }

        foreach (self::SCHEDULES as $data) {
            $schedule = (new Schedule())
                ->setCategory($data['category'])
                ->setDay($data['day'])
                ->setSeason($data['season'])
                ->setStartTime(new \DateTimeImmutable($data['start']))
                ->setEndTime(new \DateTimeImmutable($data['end']))
                ->setWinterLocation($data['winter'])
                ->setSummerLocation($data['summer'])
                ->setDisplayOrder($data['order']);

            $this->em->persist($schedule);
            $io->text(sprintf('✓ %s · %s · %s', $data['category'], $data['day'], $data['season']));
        }

        $this->em->flush();
        $io->success(sprintf('%d créneaux créés.', count(self::SCHEDULES)));

        return Command::SUCCESS;
    }
}
