<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Accueil', [
    'version' => config('app.version'),
]))->name('accueil');
