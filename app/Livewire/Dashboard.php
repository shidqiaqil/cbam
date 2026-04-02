<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }
}
