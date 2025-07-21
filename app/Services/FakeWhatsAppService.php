<?php

namespace App\Services;

use App\Services\Contracts\WhatsAppServiceInterface;
use Illuminate\Support\Facades\Log;

class FakeWhatsAppService implements WhatsAppServiceInterface
{
    public function sendWhatsAppMessage($to, $message)
    {
        Log::info("[FAKE WHATSAPP] Mensaje simulado para {$to}: {$message}");
        // También podrías guardar esto en la base de datos si lo deseas.
        return true;
    }
}
