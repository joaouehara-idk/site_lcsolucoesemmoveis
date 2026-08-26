<?php

namespace App\Services;

class SeoService {
    public static function getMeta($title, $description, $slug = '') {
        $baseUrl = url();
        $currentUrl = $baseUrl . '/' . ltrim($slug, '/');
        
        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $currentUrl,
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $currentUrl,
                'type' => 'website',
                'image' => $baseUrl . '/assets/img/og-image.jpg'
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $baseUrl . '/assets/img/og-image.jpg'
            ]
        ];
    }
}
