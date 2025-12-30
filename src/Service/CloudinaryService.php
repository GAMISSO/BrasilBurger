<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        
        // Support both individual env vars and CLOUDINARY_URL format
        if (!empty($_ENV['CLOUDINARY_CLOUD_NAME'])) {
            $this->cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'];
            $this->apiKey = $_ENV['CLOUDINARY_API_KEY'] ?? '';
            $this->apiSecret = $_ENV['CLOUDINARY_API_SECRET'] ?? '';
        } else {
            throw new \RuntimeException('CLOUDINARY_CLOUD_NAME environment variable is not set');
        }
        
        if (empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            throw new \RuntimeException('Cloudinary credentials are incomplete. Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET.');
        }
    }

    public function upload(string $filePath, array $options = []): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";
        
        $timestamp = time();
        $params = array_merge([
            'timestamp' => $timestamp,
        ], $options);

        // Créer la signature (ne pas inclure api_key et file)
        $signature = $this->createSignature($params);

        // Préparer le formulaire multipart avec tous les paramètres
        $formData = [
            'api_key' => $this->apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
        
        // Ajouter les options (folder, etc.)
        foreach ($options as $key => $value) {
            if (!is_array($value)) {
                $formData[$key] = $value;
            }
        }

        try {
            $response = $this->httpClient->request('POST', $url, [
                'body' => array_merge($formData, [
                    'file' => fopen($filePath, 'r'),
                ]),
            ]);

            $result = json_decode($response->getContent(), true);
            
            if (!isset($result['secure_url'])) {
                throw new \Exception('Upload failed: ' . ($result['error']['message'] ?? json_encode($result)));
            }

            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Cloudinary upload error: " . $e->getMessage());
        }
    }

    private function createSignature(array $params): string
    {
        // Supprimer api_key, resource_type et file s'ils sont présents
        $paramsToSign = array_filter($params, function($key) {
            return !in_array($key, ['api_key', 'resource_type', 'file']);
        }, ARRAY_FILTER_USE_KEY);
        
        // Trier les paramètres par clé
        ksort($paramsToSign);
        
        // Construire la chaîne à signer
        $paramsString = '';
        foreach ($paramsToSign as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $paramsString .= $key . '=' . $value . '&';
        }
        $paramsString = rtrim($paramsString, '&');
        
        // Ajouter le secret et générer le hash SHA-1
        return sha1($paramsString . $this->apiSecret);
    }

    public function destroy(string $publicId): array
    {
        $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";
        
        $timestamp = time();
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ];

        $signature = $this->createSignature($params);

        try {
            $response = $this->httpClient->request('POST', $url, [
                'body' => [
                    'public_id' => $publicId,
                    'api_key' => $this->apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ],
            ]);

            return json_decode($response->getContent(), true);
        } catch (\Exception $e) {
            throw new \Exception("Cloudinary destroy error: " . $e->getMessage());
        }
    }
}
