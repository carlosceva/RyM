<?php

namespace App\Services\Contracts;

interface WhatsAppServiceInterface
{
    //public function sendWhatsAppMessage($to, $message);

    public function sendWhatsAppTemplateMessage($to, string $template, array $params);

}