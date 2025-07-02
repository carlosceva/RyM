<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendWithAPIKey(array $phoneNumbers, string $message): array
    {
        $responses = [];
    
        // foreach ($phoneNumbers as $user) {
        //     $rawPhone = $user['telefono'] ?? null;
        //     $apikey = $user['api_key'] ?? null;
    
        //     // Validar teléfono (debe tener al menos 8 dígitos diferentes de solo ceros)
        //     if (!$rawPhone || $rawPhone === '+59100000000' || preg_match('/^(\+?591)?0{7,}$/', $rawPhone)) {
        //         Log::warning("Número inválido o nulo omitido: $rawPhone");
        //         continue;
        //     }
    
        //     // Validar API Key
        //     if (!$apikey) {
        //         Log::error("No se encontró API Key para el número: $rawPhone");
        //         continue;
        //     }
    
        //     // URL de la API
        //     $url = 'https://api.callmebot.com/whatsapp.php';
    
        //     // Enviar mensaje
        //     $response = Http::withoutVerifying()->get($url, [
        //         'phone'  => $rawPhone,
        //         'text'   => $message,
        //         'apikey' => $apikey,
        //     ]);
    
        //     // Capturar respuesta
        //     $status = $response->successful();
        //     $responseBody = $response->body();
    
        //     Log::info("Enviando mensaje a: $rawPhone");
        //     Log::info("Respuesta de la API: $responseBody");
    
        //     $responses[] = [
        //         'phone' => $rawPhone,
        //         'status' => $status,
        //         'response' => $responseBody,
        //     ];
        // }
    
        return $responses;
    }
    
}