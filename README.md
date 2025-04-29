# Weather API Symfony Application

## Description

This Symfony application fetches current weather data from an external Weather API.

---

## Installation

1. Clone the repository
2. Build and run the Docker container:
```bash
docker-compose up --build
```
This will automatically start the PHP built-in server on http://localhost:8020.

3. Composer Install
```bash
composer i
```
4. Environment Configuration
```bash
WEATHER_API_KEY=your_api_key_here
```
5. Running Tests
```bash
make test
```