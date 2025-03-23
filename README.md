# TruTrip Emission API

This Laravel-based backend application integrates with [Squake](https://squake.earth) to calculate carbon emissions for various travel types including **flight**, **train**, and **hotel**.

---

## Features

- Supports multiple methodologies per travel type (TIM, ADEME, DEFRA, etc.)
- Clean service architecture with Factory + DTO
- Centralized Logging Service
- Automatic caching of request/response (10 minutes)
- Retry & timeout mechanism on HTTP requests
- Fully tested with Unit & Integration tests

---

## Installation

```bash
git clone https://github.com/akurnia/trutrip-emission-api.git
cd trutrip-emission-api
composer install
cp .env.example .env
php artisan key:generate
```

Set your Squake token in `.env`:

```
SQUAKE_TOKEN=your_squake_token
```

---

## Configuration

Set cache to file (in `.env`):

```
CACHE_DRIVER=file
```

---

## Running the Application

Start the local server with:

```bash
php artisan serve
```

Default URL: `http://localhost:8000`

---

## API Endpoint

**POST** `/api/emissions/calculate`

### Request Body
```json
{
    "type": "flight",
    "parameters": {
        "methodology": "DEFRA",
        "origin": "BER",
        "destination": "LAX",
        "number_of_travelers": 2,
        "booking_class": "first",
        "aircraft_type": "737",
        "radiative_forcing_index": true
    }
}
```

### Response Body
```json
{
    "success": true,
    "data": {
        "carbon_quantity": 10916516,
        "carbon_unit": "gram",
        "items": [
            {
                "carbon_quantity": 10916516,
                "carbon_unit": "gram",
                "external_reference": "flight_67df517f041a0",
                "type": "flight",
                "methodology": "defra",
                "distance": 9352,
                "distance_unit": "kilometer"
            }
        ]
    }
}
```

## Testing

Run all tests:

```bash
php artisan test
```

---

## Notes

- Cache duration: 10 minutes
- Retry: 3x with 100ms delay
- Timeout: 10 seconds
- Uses Laravel file cache

---

## Security & Performance (Future Improvement)

- **Authentication**: This version uses a basic API token for simplicity. Recommended: switch to **JWT** or **Laravel Sanctum** for production.
- **Caching**: Uses file-based cache. For higher performance and scalability, consider using **Redis/Memcached**.
