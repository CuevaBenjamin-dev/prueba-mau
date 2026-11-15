<?php

namespace App\Services;

use DomainException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SwapiService
{
    public function obtenerPersonaje(int $id): array
    {
        try {
            $response = Http::acceptJson()->connectTimeout(3)->timeout(7)
                ->get("https://swapi.py4e.com/api/people/{$id}/");
        } catch (ConnectionException $exception) {
            if (str_contains($exception->getMessage(), 'cURL error 60')) {
                Log::error('No se pudo verificar el certificado TLS de SWAPI.', [
                    'swapi_id' => $id,
                    'error' => $exception->getMessage(),
                ]);

                throw new DomainException('El servidor no puede verificar el certificado TLS de SWAPI. Revisa la configuración CA de PHP.', previous: $exception);
            }

            Log::warning('No se pudo conectar a SWAPI.', [
                'swapi_id' => $id,
                'error' => $exception->getMessage(),
            ]);

            throw new DomainException('SWAPI no está disponible. Intenta de nuevo más tarde.', previous: $exception);
        }

        if ($response->status() === 404) {
            throw new DomainException('No existe un personaje de SWAPI con ese ID.');
        }
        if (! $response->successful()) {
            throw new DomainException('SWAPI no está disponible. Intenta de nuevo más tarde.');
        }

        $remote = $response->json();
        if (! is_array($remote) || array_is_list($remote)) {
            throw new DomainException('SWAPI devolvió datos incompatibles. No se importó el personaje.');
        }

        foreach (['name', 'height', 'mass', 'hair_color', 'eye_color', 'gender'] as $field) {
            if (! array_key_exists($field, $remote)) {
                throw new DomainException('SWAPI devolvió datos incompatibles. No se importó el personaje.');
            }
        }

        $gender = is_string($remote['gender']) ? strtolower(trim($remote['gender'])) : null;
        $gender = match ($gender) {
            'male' => 'masculino',
            'female' => 'femenino',
            'n/a', 'unknown', 'none' => 'n/a',
            default => null,
        };

        if ($gender === null || ! $this->validOptionalText($remote['hair_color']) || ! $this->validOptionalText($remote['eye_color'])
            || ! $this->validMeasureShape($remote['height']) || ! $this->validMeasureShape($remote['mass'])) {
            throw new DomainException('SWAPI devolvió datos incompatibles. No se importó el personaje.');
        }

        $data = [
            'nombre' => $remote['name'],
            'altura' => $this->measure($remote['height'], 300),
            'masa' => $this->measure($remote['mass'], 500),
            'color_cabello' => $remote['hair_color'],
            'color_ojos' => $remote['eye_color'],
            'genero' => $gender,
            'planeta_origen' => null,
        ];

        $validator = Validator::make($data, [
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
            'altura' => ['nullable', 'integer', 'between:1,300'],
            'masa' => ['nullable', 'integer', 'between:1,500'],
            'color_cabello' => ['nullable', 'string', 'max:100'],
            'color_ojos' => ['nullable', 'string', 'max:100'],
            'genero' => ['required', 'in:masculino,femenino,n/a'],
            'planeta_origen' => ['nullable', 'string', 'max:100'],
        ]);
        if ($validator->fails()) {
            throw new DomainException('SWAPI devolvió datos incompatibles. No se importó el personaje.');
        }

        // Solo se enriquece el campo opcional después de validar los datos obligatorios.
        $data['planeta_origen'] = $this->obtenerNombrePlaneta($remote['homeworld'] ?? null);

        return $data;
    }

    /**
     * La referencia homeworld solo se acepta del dominio y ruta canónicos de SWAPI.
     * Un fallo secundario no impide importar el personaje.
     */
    private function obtenerNombrePlaneta(mixed $homeworld): ?string
    {
        if (! is_string($homeworld)
            || ! preg_match('~\\Ahttps://swapi\\.py4e\\.com/api/planets/([1-9][0-9]{0,3})/\\z~', $homeworld, $matches)) {
            return null;
        }

        $planetId = (int) $matches[1];

        try {
            $response = Http::acceptJson()->connectTimeout(3)->timeout(7)->withoutRedirecting()
                ->get("https://swapi.py4e.com/api/planets/{$planetId}/");
        } catch (ConnectionException $exception) {
            Log::warning('No se pudo consultar el planeta de origen en SWAPI.', [
                'planet_id' => $planetId,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('SWAPI no devolvió un planeta disponible.', [
                'planet_id' => $planetId,
                'status' => $response->status(),
            ]);

            return null;
        }

        $planet = $response->json();
        if (! is_array($planet) || array_is_list($planet) || ! is_string($planet['name'] ?? null)) {
            return null;
        }

        $name = trim($planet['name']);

        return $name !== '' && ! in_array(strtolower($name), ['unknown', 'n/a', 'none'], true)
            && mb_strlen($name) <= 100 ? $name : null;
    }

    private function validOptionalText(mixed $value): bool
    {
        return $value === null || is_string($value);
    }

    private function validMeasureShape(mixed $value): bool
    {
        return $value === null || is_string($value) || is_int($value);
    }

    private function measure(mixed $value, int $maximum): ?int
    {
        if (! is_int($value) && ! (is_string($value) && preg_match('/^[0-9]+$/D', $value))) {
            return null;
        }
        $integer = filter_var($value, FILTER_VALIDATE_INT);

        return $integer !== false && $integer >= 1 && $integer <= $maximum ? $integer : null;
    }
}
