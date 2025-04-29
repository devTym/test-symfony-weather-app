<?php

namespace App\Service;

interface WeatherApiClientInterface
{
    public function fetchWeatherByLocation(string $location): array;
}
