<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SteelMakingImport;
use App\Imports\EnergyImport;
use App\Imports\PcoImport;
use App\Imports\SinterImport;


#[Layout('layouts.app')]
#[Title('Upload File')]
class UploadFile extends Component
{
    use WithFileUploads;

    public $plant = '';
    public $month = '';
    public $year = '';
    public $file;
    public $isUploading = false;
    public $progress = 0;

    public function submit()
    {
        $this->validate([
            'plant' => 'required',
            'month' => 'required',
            'year'  => 'required|integer',
            'file'  => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        $this->isUploading = true;
        $this->progress = 0;

        $realPath = str_replace('/', DIRECTORY_SEPARATOR, $this->file->getRealPath());

        if ($this->plant === 'steel making') {
            Excel::import(
                new SteelMakingImport($this->plant, $this->month, (int) $this->year),
                $realPath
            );

            $this->progress = 100;
            session()->flash('message', 'File succesfully Uploaded!');
        } elseif ($this->plant === 'energy') {
            Excel::import(
                new EnergyImport($this->plant, $this->month, (int) $this->year),
                $realPath
            );

            $this->progress = 100;
            session()->flash('message', 'File succesfully Uploaded!');
        } elseif ($this->plant === 'pco') {
            Excel::import(
                new PcoImport($this->plant, $this->month, (int) $this->year),
                $realPath
            );

            $this->progress = 100;
            session()->flash('message', 'File succesfully Uploaded!');
        } elseif ($this->plant === 'sinter') {
            Excel::import(
                new SinterImport($this->plant, $this->month, (int) $this->year),
                $realPath
            );

            $this->progress = 100;
            session()->flash('message', 'File succesfully Uploaded!');
        } else {
            $this->progress = 100;
            session()->flash('message', 'Upload supported only for Steel Making, Energy, PCO, and Sinter plants currently.');
        }

        $this->isUploading = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->plant = '';
        $this->month = '';
        $this->year  = '';
        $this->file  = null;
    }

    public function render()
    {
        return view('livewire.upload-file');
    }
}
