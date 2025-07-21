<?php

namespace App\Services;

use App\Services\Contracts\WhatsAppServiceInterface;
use Illuminate\Support\Facades\Http;

class GupshupWhatsAppService implements WhatsAppServiceInterface
{
    public function sendWhatsAppTemplate(string $to, string $template, array $params): void
    {
        $response = Http::withHeaders([
            'apikey' => config('services.gupshup.api_key'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://api.gupshup.io/sm/api/v1/msg', [
            'channel' => 'whatsapp',
            'source' => config('services.gupshup.source'),
            'destination' => $to,
            'src.name' => config('services.gupshup.app_name'),
            'template' => json_encode([
                'id' => $template,
                'params' => $params
            ]),
            'message' => '',
            'isHSM' => true,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Error al enviar plantilla Gupshup: " . $response->body());
        }
    }

}
