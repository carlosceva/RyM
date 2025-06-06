<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;

class SeguimientoSolicitud extends Component
{
    public $solicitudId;

    public function mount($solicitudId)
    {
        $this->solicitudId = $solicitudId;
    }

    public function render()
    {
        $solicitud = Solicitud::find($this->solicitudId);  // Buscar la solicitud por ID

        return view('livewire.seguimiento-solicitud-individual', [
            'solicitud' => $solicitud
        ]);
    }
}
