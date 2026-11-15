<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonajesCrudTest extends TestCase
{
    use RefreshDatabase;

    private function fields(array $changes = []): array
    {
        return array_merge([
            'nombre' => 'Luke Skywalker',
            'altura' => 172,
            'masa' => 77,
            'color_cabello' => 'rubio',
            'color_ojos' => 'azul',
            'genero' => 'masculino',
            'planeta_origen' => 'Tatooine',
        ], $changes);
    }

    public function test_pages_and_crud_routes_work_with_manual_origin(): void
    {
        $this->get('/personajes')->assertOk()->assertInertia(fn ($page) => $page->component('Personajes/Index'));
        $this->get('/personajes/crear')->assertOk()->assertInertia(fn ($page) => $page->component('Personajes/Crear'));
        $this->post('/personajes', [...$this->fields(), 'origen_swapi' => true, 'swapi_id' => 42])
            ->assertRedirect('/personajes')->assertSessionHas('success');

        $personaje = Personaje::firstOrFail();
        $this->assertFalse($personaje->origen_swapi);
        $this->assertNull($personaje->swapi_id);
        $this->get("/personajes/{$personaje->id}/editar")->assertOk()->assertInertia(fn ($page) => $page->component('Personajes/Editar'));
        $this->put("/personajes/{$personaje->id}", [...$this->fields(['nombre' => 'Maestro Luke']), 'swapi_id' => 80])
            ->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseHas('personajes', ['id' => $personaje->id, 'nombre' => 'Maestro Luke', 'swapi_id' => null]);
        $this->delete("/personajes/{$personaje->id}")->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_edit_keeps_imported_origin_metadata(): void
    {
        $personaje = Personaje::create([...$this->fields(), 'origen_swapi' => true, 'swapi_id' => 1]);
        $this->put("/personajes/{$personaje->id}", [...$this->fields(['nombre' => 'Luke editado']), 'origen_swapi' => false, 'swapi_id' => null])->assertRedirect('/personajes');
        $this->assertDatabaseHas('personajes', ['id' => $personaje->id, 'nombre' => 'Luke editado', 'origen_swapi' => true, 'swapi_id' => 1]);
    }

    public function test_pagination_shows_ten_then_one(): void
    {
        foreach (range(1, 11) as $number) {
            Personaje::create($this->fields(['nombre' => "Personaje {$number}"]));
        }
        $first = $this->get('/personajes')->assertOk()->inertiaProps('personajes');
        $second = $this->get('/personajes?page=2')->assertOk()->inertiaProps('personajes');
        $this->assertCount(10, $first['data']);
        $this->assertCount(1, $second['data']);
        $this->assertSame(11, $first['total']);
    }

    public function test_validation_boundaries(): void
    {
        $this->post('/personajes', $this->fields(['nombre' => 'Ab']))->assertSessionHasErrors('nombre');
        $this->post('/personajes', $this->fields(['nombre' => str_repeat('A', 101)]))->assertSessionHasErrors('nombre');
        $this->post('/personajes', $this->fields(['altura' => 0]))->assertSessionHasErrors('altura');
        $this->post('/personajes', $this->fields(['altura' => 301]))->assertSessionHasErrors('altura');
        $this->post('/personajes', $this->fields(['altura' => 1.5]))->assertSessionHasErrors('altura');
        $this->post('/personajes', $this->fields(['masa' => 0]))->assertSessionHasErrors('masa');
        $this->post('/personajes', $this->fields(['masa' => 501]))->assertSessionHasErrors('masa');
        $this->post('/personajes', $this->fields(['genero' => 'otro']))->assertSessionHasErrors('genero');
        foreach (['color_cabello', 'color_ojos', 'planeta_origen'] as $field) {
            $this->post('/personajes', $this->fields([$field => str_repeat('A', 101)]))->assertSessionHasErrors($field);
        }
        $this->assertDatabaseCount('personajes', 0);
        $this->post('/personajes', $this->fields(['nombre' => 'Ana', 'altura' => 1, 'masa' => 500, 'color_cabello' => null]))->assertSessionHasNoErrors();
        $this->post('/personajes', $this->fields(['nombre' => str_repeat('A', 100), 'altura' => 300, 'masa' => 1, 'color_ojos' => str_repeat('A', 100)]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('personajes', 2);
    }

    public function test_missing_personaje_returns_404(): void
    {
        $this->get('/personajes/999/editar')->assertNotFound();
        $this->put('/personajes/999', $this->fields())->assertNotFound();
        $this->delete('/personajes/999')->assertNotFound();
    }
}
