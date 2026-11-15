<?php

use App\Http\Controllers\ImportacionSwapiController;
use App\Http\Controllers\PersonajeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/personajes');
Route::get('/personajes', [PersonajeController::class, 'index'])->name('personajes.index');
Route::get('/personajes/crear', [PersonajeController::class, 'create'])->name('personajes.create');
Route::post('/personajes', [PersonajeController::class, 'store'])->name('personajes.store');
Route::post('/personajes/importar', [ImportacionSwapiController::class, 'store'])->name('personajes.importar');
Route::get('/personajes/{personaje}/editar', [PersonajeController::class, 'edit'])->whereNumber('personaje')->name('personajes.edit');
Route::put('/personajes/{personaje}', [PersonajeController::class, 'update'])->whereNumber('personaje')->name('personajes.update');
Route::delete('/personajes/{personaje}', [PersonajeController::class, 'destroy'])->whereNumber('personaje')->name('personajes.destroy');
