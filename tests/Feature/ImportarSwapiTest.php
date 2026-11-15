<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use App\Services\SwapiService;
use Tests\TestCase;

class ImportarSwapiTest extends TestCase
{
    use RefreshDatabase;

    private function remote(array $changes = []): array
    {
        return array_merge([
            'name' => 'Luke Skywalker', 'height' => '172', 'mass' => '77',
            'hair_color' => 'blond', 'eye_color' => 'blue', 'gender' => 'male',
            'homeworld' => null,
        ], $changes);
    }

    public function test_import_maps_fields_and_prevents_duplicate_without_repeating_http_requests(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://swapi.py4e.com/api/people/1/' => Http::response($this->remote([
                'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
            ])),
            'https://swapi.py4e.com/api/planets/1/' => Http::response(['name' => 'Tatooine']),
        ]);
        $this->post('/personajes/importar', ['id' => 1])->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker', 'altura' => 172, 'masa' => 77,
            'color_cabello' => 'blond', 'color_ojos' => 'blue', 'genero' => 'masculino',
            'planeta_origen' => 'Tatooine', 'origen_swapi' => true, 'swapi_id' => 1,
        ]);
        $this->post('/personajes/importar', ['id' => 1])->assertSessionHasErrors('id');
        $this->assertDatabaseCount('personajes', 1);
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request->url() === 'https://swapi.py4e.com/api/people/1/');
        Http::assertSent(fn ($request) => $request->url() === 'https://swapi.py4e.com/api/planets/1/');
    }

    public function test_id_validation_never_calls_swapi(): void
    {
        Http::preventStrayRequests();
        Http::fake();
        foreach ([0, 84, '', '1.5', 'abc'] as $id) {
            $this->post('/personajes/importar', ['id' => $id])->assertSessionHasErrors('id');
        }
        $this->post('/personajes/importar', [])->assertSessionHasErrors('id');
        Http::assertNothingSent();
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_gender_and_measure_policy_including_upper_bound_id(): void
    {
        Http::preventStrayRequests();
        foreach ([
            ['female', 'femenino'], ['n/a', 'n/a'], ['UNKNOWN', 'n/a'], ['none', 'n/a'],
        ] as [$remoteGender, $localGender]) {
            $id = ['female' => 2, 'n/a' => 3, 'UNKNOWN' => 83, 'none' => 23][$remoteGender];
            Http::fake(["https://swapi.py4e.com/api/people/{$id}/" => Http::response($this->remote([
                'name' => "Personaje {$id}", 'gender' => $remoteGender,
                'height' => '301', 'mass' => '77.5',
            ]))]);
            $this->post('/personajes/importar', ['id' => $id])->assertSessionHasNoErrors();
            $this->assertDatabaseHas('personajes', ['swapi_id' => $id, 'genero' => $localGender, 'altura' => null, 'masa' => null]);
        }
    }

    public function test_unknown_values_and_out_of_range_measures_become_null(): void
    {
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response($this->remote(['height' => 'unknown', 'mass' => '501']))]);
        $this->post('/personajes/importar', ['id' => 4])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 4, 'altura' => null, 'masa' => null]);
    }

    public function test_http_and_connection_failures_do_not_write(): void
    {
        Http::preventStrayRequests();
        foreach ([404, 500, 503] as $status) {
            Http::fake(['*' => Http::response([], $status)]);
            $this->post('/personajes/importar', ['id' => 5])->assertSessionHasErrors('id');
        }
        Http::fake(function () { throw new ConnectionException('Connection timed out'); });
        $this->post('/personajes/importar', ['id' => 5])->assertSessionHasErrors('id');
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_missing_ca_bundle_is_reported_as_a_local_tls_configuration_error(): void
    {
        Http::preventStrayRequests();
        Http::fake(function () {
            throw new ConnectionException('cURL error 60: unable to get local issuer certificate');
        });

        $this->post('/personajes/importar', ['id' => 1])->assertSessionHasErrors([
            'id' => 'El servidor no puede verificar el certificado TLS de SWAPI. Revisa la configuración CA de PHP.',
        ]);
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_malformed_or_invalid_external_data_never_persists(): void
    {
        Http::preventStrayRequests();
        $badResponses = [
            ['invalid' => true],
            $this->remote(['name' => '']),
            $this->remote(['name' => str_repeat('A', 101)]),
            $this->remote(['hair_color' => str_repeat('B', 101)]),
            $this->remote(['eye_color' => ['blue']]),
            $this->remote(['gender' => 'other']),
        ];
        foreach ($badResponses as $body) {
            Http::fake(['*' => Http::response($body)]);
            $this->post('/personajes/importar', ['id' => 6])->assertSessionHasErrors('id');
        }
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_id_23_ig88_with_gender_none_is_imported(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://swapi.py4e.com/api/people/23/' => Http::response($this->remote([
                'name' => 'IG-88', 'height' => '200', 'mass' => '140',
                'gender' => 'none', 'homeworld' => 'https://swapi.py4e.com/api/planets/28/',
            ])),
            'https://swapi.py4e.com/api/planets/28/' => Http::response(['name' => 'unknown']),
        ]);

        $this->post('/personajes/importar', ['id' => 23])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', [
            'swapi_id' => 23, 'nombre' => 'IG-88', 'genero' => 'n/a',
            'planeta_origen' => null,
        ]);
        Http::assertSentCount(2);
    }

    public function test_untrusted_homeworld_url_is_ignored_without_external_request(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://swapi.py4e.com/api/people/22/' => Http::response($this->remote([
                'name' => 'Boba Fett', 'homeworld' => 'https://example.org/api/planets/1/',
            ])),
        ]);

        $this->post('/personajes/importar', ['id' => 22])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 22, 'planeta_origen' => null]);
        Http::assertSentCount(1);
    }

    public function test_planet_http_failure_does_not_prevent_character_import(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://swapi.py4e.com/api/people/6/' => Http::response($this->remote([
                'name' => 'Owen Lars', 'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
            ])),
            'https://swapi.py4e.com/api/planets/1/' => Http::response([], 503),
        ]);

        $this->post('/personajes/importar', ['id' => 6])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 6, 'planeta_origen' => null]);
        Http::assertSentCount(2);
    }

    public function test_planet_timeout_does_not_prevent_character_import(): void
    {
        Http::preventStrayRequests();
        $planetCalls = 0;
        Http::fake(function ($request) use (&$planetCalls) {
            if (str_contains($request->url(), '/api/planets/')) {
                $planetCalls++;
                throw new ConnectionException('Connection timed out');
            }

            return Http::response($this->remote([
                'name' => 'Personaje remoto',
                'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
            ]));
        });

        $this->post('/personajes/importar', ['id' => 30])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 30, 'planeta_origen' => null]);
        $this->assertSame(1, $planetCalls);
        Http::assertSentCount(1); // Laravel no registra la solicitud simulada que lanza la excepción.
    }

    public function test_database_enforces_unique_swapi_id(): void
    {
        $fields = [
            'nombre' => 'Luke Skywalker', 'genero' => 'masculino',
            'origen_swapi' => true, 'swapi_id' => 1,
        ];
        Personaje::create($fields);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Personaje::create([...$fields, 'nombre' => 'Duplicado']);
    }

    public function test_concurrent_unique_collision_returns_a_friendly_error(): void
    {
        $service = $this->mock(SwapiService::class);
        $service->shouldReceive('obtenerPersonaje')->once()->with(7)->andReturnUsing(function () {
            Personaje::create([
                'nombre' => 'Primera importación', 'genero' => 'n/a',
                'origen_swapi' => true, 'swapi_id' => 7,
            ]);

            return [
                'nombre' => 'Segunda importación', 'genero' => 'n/a',
                'altura' => null, 'masa' => null, 'color_cabello' => null,
                'color_ojos' => null, 'planeta_origen' => null,
            ];
        });

        $this->post('/personajes/importar', ['id' => 7])->assertSessionHasErrors('id');
        $this->assertDatabaseCount('personajes', 1);
        $this->assertDatabaseHas('personajes', ['swapi_id' => 7, 'nombre' => 'Primera importación']);
    }
}
