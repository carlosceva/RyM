<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    // public function send(string $message = 'Test'): int|false
    // {
    //     $phone = '+59177813264'; // Tu número real
    //     $apikey = '5185410';      // Tu API key de CallMeBot

    //     $url = 'https://api.callmebot.com/whatsapp.php';

    //     $response = Http::withoutVerifying()->get($url, [
    //         'source' => 'laravel',
    //         'phone'  => $phone,
    //         'text'   => $message,
    //         'apikey' => $apikey,
    //     ]);

    //     return $response->successful() ? $response->status() : false;
    // }
    // Cambiar la función para enviar mensajes a varios números con sus respectivas API Keys
    public function sendWithAPIKey(array $phoneNumbers, string $message): array
    {
        $responses = [];

        foreach ($phoneNumbers as $user) {
            // Obtener teléfono y API Key de cada usuario
            $phone = $user['telefono'];
            $apikey = $user['api_key'];

            if (!$apikey) {
                Log::error("No se encontró API Key para el número: $phone");
                continue;
            }

            // URL de la API de CallMeBot
            $url = 'https://api.callmebot.com/whatsapp.php';

            // Enviar la solicitud GET a la API de CallMeBot con la API Key del usuario
            $response = Http::withoutVerifying()->get($url, [
                'phone'  => $phone,
                'text'   => $message,
                'apikey' => $apikey,
            ]);

            // Capturar la respuesta de la API
            $status = $response->successful();  // Si la respuesta es 200 OK
            $responseBody = $response->body();  // Capturar el cuerpo de la respuesta

            // Agregar logs para cada intento de envío
            Log::info("Enviando mensaje a: $phone");
            Log::info("Respuesta de la API: $responseBody");

            // Almacenar la respuesta en el array
            $responses[] = [
                'phone' => $phone,
                'status' => $status,
                'response' => $responseBody,
            ];
        }

        return $responses; // Retornar las respuestas para verificar en el controlador
    }
}