<?php

namespace App\HttpFoundation;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApiSuccessfulResponse extends JsonResponse
{
    public function __construct(mixed $response = null, int $status = 200, array $headers = [])
    {
        $data = [
            'success' => true
        ];

        if ($response !== null) {
            $data['response'] = $response;
        }

        parent::__construct($data, $status, $headers);
    }
}
