<?php

namespace App\Services;

class AIService {
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct() {
        $this->apiKey = GEMINI_API_KEY;
    }

    public function generateBlogPost($topic) {
        if (empty($this->apiKey)) {
            return ['error' => 'API Key do Gemini não configurada.'];
        }

        $prompt = "Escreva um post de blog profissional em português sobre: {$topic}. 
        O post deve ter um título atraente, uma introdução, corpo do texto dividido em subtítulos e uma conclusão. 
        Formate a resposta em JSON com as chaves: 'title', 'content', 'summary'. 
        O 'summary' deve ser uma breve descrição de SEO.";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Erro na API do Gemini: ' . $response];
        }

        $result = json_decode($response, true);
        $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Tentar extrair JSON da resposta do Gemini (às vezes ele coloca markdown ```json)
        if (preg_match('/\{.*\}/s', $textResponse, $matches)) {
            $jsonContent = json_decode($matches[0], true);
            if ($jsonContent) {
                return $jsonContent;
            }
        }

        return ['error' => 'Não foi possível processar a resposta da IA.'];
    }

    public function generatePortfolioInfo($topic) {
        if (empty($this->apiKey)) {
            return ['error' => 'API Key do Gemini não configurada.'];
        }

        $prompt = "Crie informações profissionais para um projeto de móveis planejados sobre: {$topic}. 
        O objetivo é vender o projeto para clientes de alto padrão.
        Formate a resposta em JSON com as chaves: 'title' (um título elegante) e 'description' (uma descrição técnica e sofisticada de no máximo 3 parágrafos).";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Erro na API do Gemini: ' . $response];
        }

        $result = json_decode($response, true);
        $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (preg_match('/\{.*\}/s', $textResponse, $matches)) {
            $jsonContent = json_decode($matches[0], true);
            if ($jsonContent) {
                return $jsonContent;
            }
        }

        return ['error' => 'Não foi possível processar a resposta da IA.'];
    }
}
