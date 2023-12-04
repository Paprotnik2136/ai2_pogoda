<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class WeatherApiController extends AbstractController
{
    public function __construct(private WeatherUtil $weatherUtil)
    {

    }

    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(#[MapQueryParameter] string $city,
                          #[MapQueryParameter] string $country,
                          #[MapQueryParameter] string $format = 'json',
                          #[MapQueryParameter] bool $twig = false): Response
    {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country,$city);
        if($format == "csv")
        {
            if($twig)
            {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                    'columns' => "city,country,date,celsius,fahrenheit"
                ]);
            }
            $csv = "city,country,date,celsius,fahrenheit\n";
            $csv .= implode(
                "\n",
                array_map(fn(Measurement $m) => sprintf(
                    '%s,%s,%s,%s,%s',
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getCelsius(),
                    $m->getFahrenheit(),
                ), $measurements)
            );

            return new Response($csv, 200, [
                   'Content-Type' => 'text/csv'
            ]);
        }
        else 
        {
            if($twig)
            {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            else
            {
                $measurements = array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getCelsius(),
                    'fahrenheit' => $m->getFahrenheit()
                ], $measurements);
    
                return $this->json([
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements
                ]);
            }
            
        }
    }
}
