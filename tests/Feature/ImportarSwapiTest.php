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
            'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
        ], $changes);
    }

    public function test_import_maps_fields_and_prevents_duplicate_without_second_http_request(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://swapi.py4e.com/api/people/1/' => Http::response($this->remote())]);
        $this->post('/personajes/importar', ['id' => 1])->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker', 'altura' => 172, 'masa' => 77,
            'color_cabello' => 'blond', 'color_ojos' => 'blue', 'genero' => 'masculino',
            'planeta_origen' => null, 'origen_swapi' => true, 'swapi_id' => 1,
        ]);
        $this->post('/personajes/importar', ['id' => 1])->assertSessionHasErrors('id');
        $this->assertDatabaseCount('personajes', 1);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === 'https://swapi.py4e.com/api/people/1/');
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
            ['female', 'femenino'], ['n/a', 'n/a'], ['UNKNOWN', 'n/a'],
        ] as [$remoteGender, $localGender]) {
            $id = ['female' => 2, 'n/a' => 3, 'UNKNOWN' => 83][$remoteGender];
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
