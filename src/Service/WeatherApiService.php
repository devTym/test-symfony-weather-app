<?php

namespace App\Service;

use App\Exception\WeatherApiException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class WeatherApiService implements WeatherApiClientInterface
{
    private const WEATHER_API_URL = 'https://api.weatherapi.com/v1/current.json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey
    ) {}

    /**
     * Fetches current weather data by location.
     *
     * Location examples:
     *  - city name
     *  - ZIP/postcode
     *  - coordinates
     *  - IP address
     */
    public function fetchWeatherByLocation(string $location): array
    {
        try {
            $response = $this->httpClient->request('GET', self::WEATHER_API_URL, [
                'query' => [
                    'key' => $this->apiKey,
                    'q' => $location,
                ],
            ]);

            $data = $response->toArray(false);

            if (isset($data['error'])) {
                $this->logger->error('Weather API error: ' . $data['error']['message']);
                throw new WeatherApiException('Weather API error: ' . $data['error']['message']);
            }

            return $this->mapWeatherData($data);

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Weather API transport exception: ' . $e->getMessage());
            throw new WeatherApiException('Unable to fetch weather data.', 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('Weather API error: ' . $e->getMessage());
            throw new WeatherApiException('Unexpected error from Weather API.', 0, $e);
        }
    }

    private function mapWeatherData(array $data): array
    {
        return [
            'city' => $data['location']['name'],
            'country' => $data['location']['country'],
            'temperature' => $data['current']['temp_c'],
            'condition' => $data['current']['condition']['text'],
            'humidity' => $data['current']['humidity'],
            'wind_speed' => $data['current']['wind_kph'],
            'last_updated' => $data['current']['last_updated'],
        ];
    }
}
