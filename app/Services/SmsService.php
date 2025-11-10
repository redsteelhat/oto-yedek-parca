<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $gateway;
    protected $config;

    public function __construct()
    {
        $this->gateway = Setting::getValue('sms_gateway', 'netgsm');
        $this->loadConfig();
    }

    protected function loadConfig()
    {
        $this->config = [
            'username' => Setting::getValue('sms_username', ''),
            'password' => Setting::getValue('sms_password', ''),
            'api_key' => Setting::getValue('sms_api_key', ''),
            'sender' => Setting::getValue('sms_sender', ''),
            'url' => Setting::getValue('sms_api_url', ''),
        ];
    }

    /**
     * Send SMS message
     */
    public function send($phone, $message)
    {
        if (empty($phone) || empty($message)) {
            return ['success' => false, 'message' => 'Telefon ve mesaj boş olamaz'];
        }

        // Clean phone number
        $phone = $this->cleanPhoneNumber($phone);

        if (!$this->validatePhoneNumber($phone)) {
            return ['success' => false, 'message' => 'Geçersiz telefon numarası'];
        }

        try {
            switch ($this->gateway) {
                case 'netgsm':
                    return $this->sendViaNetgsm($phone, $message);
                case 'iletimerkezi':
                    return $this->sendViaIletiMerkezi($phone, $message);
                default:
                    return ['success' => false, 'message' => 'Bilinmeyen SMS gateway'];
            }
        } catch (\Exception $e) {
            Log::error('SMS gönderim hatası: ' . $e->getMessage());
            return ['success' => false, 'message' => 'SMS gönderilemedi: ' . $e->getMessage()];
        }
    }

    /**
     * Send SMS via Netgsm
     */
    protected function sendViaNetgsm($phone, $message)
    {
        $url = 'https://api.netgsm.com.tr/sms/send/get';
        
        $params = [
            'usercode' => $this->config['username'],
            'password' => $this->config['password'],
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $this->config['sender'] ?? '',
        ];

        $queryString = http_build_query($params);
        $fullUrl = $url . '?' . $queryString;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && strpos($response, '00') === 0) {
            return ['success' => true, 'message' => 'SMS başarıyla gönderildi', 'response' => $response];
        }

        return ['success' => false, 'message' => 'SMS gönderilemedi', 'response' => $response];
    }

    /**
     * Send SMS via İleti Merkezi
     */
    protected function sendViaIletiMerkezi($phone, $message)
    {
        $url = 'https://api.iletimerkezi.com/v1/send/sms';
        
        $data = [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'messages' => [
                [
                    'msg' => $message,
                    'to' => $phone,
                    'from' => $this->config['sender'] ?? '',
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['response']['status']['code']) && $responseData['response']['status']['code'] == 200) {
            return ['success' => true, 'message' => 'SMS başarıyla gönderildi', 'response' => $responseData];
        }

        return ['success' => false, 'message' => 'SMS gönderilemedi', 'response' => $responseData];
    }

    /**
     * Clean phone number
     */
    protected function cleanPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, remove it
        if (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }
        
        // Add country code if not present
        if (strlen($phone) == 10) {
            $phone = '90' . $phone;
        }
        
        return $phone;
    }

    /**
     * Validate phone number
     */
    protected function validatePhoneNumber($phone)
    {
        // Turkish phone number validation: should start with 90 and be 12 digits
        return preg_match('/^90[0-9]{10}$/', $phone);
    }
}

