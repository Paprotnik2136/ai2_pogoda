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
    name: 'weather:city',
    description: 'Add a short description for your command',
)]
class WeatherCityCommand extends Command
{
    public function __construct(private WeatherUtil $weatherUtil,
                                private LocationRepository $locationRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('country', InputArgument::REQUIRED, 'country name')
            ->addArgument('city', InputArgument::REQUIRED, 'city name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $city = $input->getArgument('city');
        $country = $input->getArgument('country');
        if ($city && $country) {
            $location = $this->locationRepository->findOneBy([
                'country' => $country,
                'city' => $city,
            ]);
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
