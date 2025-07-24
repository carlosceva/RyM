<?php

namespace App\Services;

use App\Models\Configuracion;
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
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');

        $url = "/2010-04-01/Accounts/{$sid}/Messages.json";

        $contentSid = $this->getContentSidForTemplate($template);

        if (!$contentSid) {
            \Log::error("No se encontró ContentSid para el template: $template");
            return null;
        }

        $contentVariables = [];
        foreach ($params as $index => $value) {
            $contentVariables[(string)($index + 1)] = strval($value);
        }

        $body = [
            'To' => "whatsapp:$to",
            'From' => "whatsapp:$from",
            'ContentSid' => $contentSid,
            'ContentVariables' => json_encode($contentVariables),
        ];

        try {
            $response = $this->guzzle->post($url, [
                'auth' => [$sid, $token],
                'form_params' => $body
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error('Error enviando plantilla WhatsApp: ' . $e->getMessage());
            return null;
        }
    }

    private function getContentSidForTemplate(string $template): ?string
    {
        return config("twilio_templates.$template");
    }
}
