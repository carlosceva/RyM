<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;

class SeguimientoSolicitud extends Component
{
    public $solicitudes;

    public function mount()
    {
        $this->solicitudes = Solicitud::latest()->get();
    }

    public function render()
    {
        $this->solicitudes = Solicitud::latest()->get();
        return view('livewire.seguimiento-solicitud');
    }
}
