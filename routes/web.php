<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app', ['slot' => view('welcome')]);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/uploadfile', \App\Livewire\UploadFile::class)->name('uploadfile');
    Route::get('/configurationdata', \App\Livewire\ConfigurationData::class)->name('configurationdata');
    Route::get('/pcomaster', \App\Livewire\PcoMaster::class)->name('pcomaster');
    Route::get('/energymaster', \App\Livewire\EnergyMaster::class)->name('energymaster');
    Route::get('/sintermaster', \App\Livewire\SinterMaster::class)->name('sintermaster');
    Route::get('/steelmakingmaster', \App\Livewire\SteelMakingMaster::class)->name('steelmakingmaster');
    Route::get('/bfmaster', \App\Livewire\BfMaster::class)->name('bfmaster');
    Route::get('/byproductmaster', \App\Livewire\ByproductMaster::class)->name('byproductmaster');
    Route::get('/configurationdata-chp', \App\Livewire\ConfigurationDataCHP::class)->name('configurationdata-chp');
    Route::get('/datacalculation', \App\Livewire\DataCalculation::class)->name('datacalculation');
    Route::get('/datacalculation/c-emission', \App\Livewire\DataCalculationCEmission::class)->name('datacalculation.c-emission');
    Route::get('/users', \App\Livewire\UserManagement::class)->name('users');
});

// Authentication routes
Route::get('/login', \App\Livewire\Authentication::class)->name('login');
Route::get('/sso', [\App\Http\Controllers\SsoController::class, 'sso'])->name('sso');
Route::post('/logout', [\App\Http\Controllers\SsoController::class, 'logout'])->name('logout');
