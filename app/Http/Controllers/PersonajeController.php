<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarPersonajeRequest;
use App\Models\Personaje;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PersonajeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Personajes/Index', [
            'personajes' => Personaje::query()->orderBy('id')->paginate(10)->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Personajes/Crear');
    }

    public function store(GuardarPersonajeRequest $request): RedirectResponse
    {
        Personaje::create([
            ...$request->validated(),
            'origen_swapi' => false,
            'swapi_id' => null,
        ]);

        return to_route('personajes.index')->with('success', 'Personaje creado correctamente.');
    }

    public function edit(Personaje $personaje): Response
    {
        return Inertia::render('Personajes/Editar', ['personaje' => $personaje]);
    }

    public function update(GuardarPersonajeRequest $request, Personaje $personaje): RedirectResponse
    {
        $personaje->update($request->validated());

        return to_route('personajes.index')->with('success', 'Personaje actualizado correctamente.');
    }

    public function destroy(Personaje $personaje): RedirectResponse
    {
        $personaje->delete();

        return to_route('personajes.index')->with('success', 'Personaje eliminado correctamente.');
    }
}
