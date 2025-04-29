<?php

namespace App\Controller;

use App\Exception\WeatherApiException;
use App\Service\WeatherApiClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherApiClientInterface $weatherService
    ) {}

    /**
     * Redirects to default location weather page.
     *
     * This is a placeholder for test purposes.
     */
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->redirectToRoute('weather', ['location' => 'London']);
    }

    /**
     * Displays weather information for the given location.
     *
     * Location examples:
     * - city name
     * - ZIP/postcode
     * - coordinates
     * - IP address
     */
    #[Route('/weather/{location}', name: 'show')]
    public function show(string $location): Response
    {
        try {
            $weatherData = $this->weatherService->fetchWeatherByLocation($location);
        } catch (WeatherApiException $e) {
            return $this->render('weather/error.html.twig', [
                'errorMessage' => $e->getMessage(),
            ]);
        }

        return $this->render('weather/show.html.twig', [
            'weather' => $weatherData,
        ]);
    }
}
