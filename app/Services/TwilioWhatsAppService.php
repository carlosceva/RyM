<?php

namespace App\Services;

use App\Services\Contracts\WhatsAppServiceInterface;
use GuzzleHttp\Client as GuzzleClient;

class TwilioWhatsAppService implements WhatsAppServiceInterface
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = new GuzzleClient([
            'base_uri' => 'https://api.twilio.com'
        ]);
    }

    public function sendWhatsAppTemplateMessage($to, string $template, array $params)
    {
        //dd($to, $template, $params);
        // Configuración de Twilio
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');

        // URL para enviar el mensaje
        $url = "/2010-04-01/Accounts/{$sid}/Messages.json";

        $contentSid = $this->getContentSidForTemplate($template);

        if (!$contentSid) {
            \Log::error("No se encontró ContentSid para el template: $template");
            return null;
        }

        // Mapear los parámetros como "1", "2", "3", etc.
        $contentVariables = [];
        foreach ($params as $index => $value) {
            $contentVariables[(string)($index + 1)] = strval($value);
        }

        // $contentSid = 'HX2d2bf27d87e37a3ecfecb0748d64c8bc'; // Reemplaza con el SID real de la plantilla

        // Construir el cuerpo de la solicitud
        $body = [
            'To' => "whatsapp:$to",  // Número de destino
            'From' => "whatsapp:$from",  // Tu número Twilio habilitado para WhatsApp
            'ContentSid' => $contentSid,  // SID de la plantilla
            'ContentVariables' => json_encode($contentVariables),
        ];

        try {
            // Realizar la solicitud POST a la API de Twilio
            $response = $this->guzzle->post($url, [
                'auth' => [$sid, $token],  // Autenticación básica
                'form_params' => $body  // Enviar los datos como parámetros de formulario
            ]);

            // Decodificar la respuesta de Twilio
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Manejo de errores
            \Log::error('Error enviando plantilla WhatsApp: ' . $e->getMessage());
            return null;
        }
    }

    private function getContentSidForTemplate(string $template): ?string
    {
        return config("twilio_templates.$template");
    }
}
