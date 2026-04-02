<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app', ['slot' => view('welcome')]);
});

Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
Route::get('/uploadfile', \App\Livewire\UploadFile::class)->name('uploadfile');
Route::get('/configurationdata', \App\Livewire\ConfigurationData::class)->name('configurationdata');
Route::get('/pcomaster', \App\Livewire\PcoMaster::class)->name('pcomaster');
Route::get('/energymaster', \App\Livewire\EnergyMaster::class)->name('energymaster');
Route::get('/sintermaster', \App\Livewire\SinterMaster::class)->name('sintermaster');
Route::get('/steelmakingmaster', \App\Livewire\SteelMakingMaster::class)->name('steelmakingmaster');
