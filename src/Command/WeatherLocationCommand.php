<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherService;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand extends Command
{
    public function __construct(private WeatherUtil $weatherUtil,
                                private LocationRepository $locationRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('location_id', InputArgument::REQUIRED, 'location id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('location_id');

        if ($id) {
            $location = $this->locationRepository->findOneById($id);
            $measurements = $this->weatherUtil->getWeatherForLocation($location);
            $io->writeln(sprintf('Location: %s', $location->getCity()));
            foreach($measurements as $measurement)
            {
                $io->writeln(sprintf("\t%s: %s",
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius()
                ));
            }
        }

        $io->success('DONE');

        return Command::SUCCESS;
    }
}
