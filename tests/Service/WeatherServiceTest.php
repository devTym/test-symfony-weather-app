<?php

namespace App\Tests\Service;

use App\Exception\WeatherApiException;
use App\Service\WeatherApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Psr\Log\LoggerInterface;

class WeatherServiceTest extends TestCase
{
    /**
     * @dataProvider getWeatherApiSuccessDataProvider
     */
    public function testGetWeatherDataSuccess(array $responseData): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('toArray')->willReturn($responseData);
        $httpClient->method('request')->willReturn($response);

        $service = new WeatherApiService($httpClient, $logger, 'fake-api-key');

        $weather = $service->fetchWeatherByLocation('fake-location');

        $this->assertEquals($responseData['location']['name'], $weather['city']);
        $this->assertEquals($responseData['location']['country'], $weather['country']);
        $this->assertEquals($responseData['current']['temp_c'], $weather['temperature']);
        $this->assertEquals($responseData['current']['condition']['text'], $weather['condition']);
        $this->assertEquals($responseData['current']['humidity'], $weather['humidity']);
        $this->assertEquals($responseData['current']['wind_kph'], $weather['wind_speed']);
    }

    public static function getWeatherApiSuccessDataProvider(): array
    {
        return [
            'Successful weather response' => [
                [
                    'location' => [
                        'name' => 'Indianapolis',
                        'country' => 'United States of America',
                    ],
                    'current' => [
                        'temp_c' => 22,
                        'condition' => ['text' => 'Moderate or heavy rain with thunder'],
                        'humidity' => 22,
                        'wind_kph' => 22,
                        'last_updated' => '2025-04-29 11:00',
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider getWeatherApiErrorDataProvider
     */
    public function testGetWeatherDataFailed(array $responseData): void
    {
        $this->expectException(WeatherApiException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('toArray')->willReturn($responseData);
        $httpClient->method('request')->willReturn($response);

        $service = new WeatherApiService($httpClient, $logger, 'fake-api-key');

        $service->fetchWeatherByLocation('fake-location');
    }

    public static function getWeatherApiErrorDataProvider(): array
    {
        return [
            'API key failed' => [
                [
                    'error' => [
                        'code' => 2008,
                        'message' => 'API key has been disabled.',
                    ]
                ]
            ],
            'Empty response' => [[]]
        ];
    }
}
