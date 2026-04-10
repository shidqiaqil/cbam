<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SteelMakingImport;
use App\Imports\EnergyImport;
use App\Imports\PcoImport;
use App\Imports\SinterImport;
use App\Imports\BfImport;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    private array $importMap = [
        'steel making'  => SteelMakingImport::class,
        'energy'        => EnergyImport::class,
        'pco'           => PcoImport::class,
        'sinter'        => SinterImport::class,
        'blast furnace' => BfImport::class,
    ];

    public function submit()
    {
        try {
            $this->validate([
                'plant' => 'required',
                'month' => 'required',
                'year'  => 'required|integer',
                'file'  => 'required|file|mimes:xlsx,xls|max:2048',
            ]);

            $this->isUploading = true;
            $this->progress = 0;

            if (!isset($this->importMap[$this->plant])) {
                Log::warning('Import attempted for unsupported plant', [
                    'plant'   => $this->plant,
                    'month'   => $this->month,
                    'year'    => $this->year,
                    // 'user_id' => auth()->id(),
                ]);

                session()->flash('error', 'Upload supported only for Steel Making, Energy, PCO, Sinter, and BF plants currently.');
                return;
            }

            $importClass = $this->importMap[$this->plant];
            $realPath = str_replace('/', DIRECTORY_SEPARATOR, $this->file->getRealPath());

            Log::info('Import started', [
                'plant'     => $this->plant,
                'month'     => $this->month,
                'year'      => $this->year,
                // 'user_id'   => auth()->id(),
                'filename'  => $this->file->getClientOriginalName(),
                'filesize'  => $this->file->getSize(),
            ]);

            Excel::import(
                new $importClass($this->plant, $this->month, (int) $this->year),
                $realPath
            );

            $this->progress = 100;

            Log::info('Import success', [
                'plant'     => $this->plant,
                'month'     => $this->month,
                'year'      => $this->year,
                // 'user_id'   => auth()->id(),
                'filename'  => $this->file->getClientOriginalName(),
            ]);

            session()->flash('message', 'File successfully uploaded!');
        } catch (ValidationException $e) {
            $this->isUploading = false;
            throw $e;
        } catch (Throwable $e) {
            $this->progress = 0;

            Log::error('Import failed', [
                'plant'   => $this->plant,
                'month'   => $this->month,
                'year'    => $this->year,
                // 'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
            $this->resetForm();
        }
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
