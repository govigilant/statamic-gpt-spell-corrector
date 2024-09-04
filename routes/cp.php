<?php

use Illuminate\Support\Facades\Route;
use Vigilant\GptSpellCorrector\Http\Controllers\SpellCorrectorController;

Route::prefix('vigilant')->group(function (): void {
    Route::post('correct-spelling', [SpellCorrectorController::class, 'correct']);
});
