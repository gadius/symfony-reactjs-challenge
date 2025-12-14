<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SumController
{    
    #[Route('/api/sum', methods: ['POST'])]
    public function sum(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(
                ['error' => "Both 'a' and 'b' must be numeric."],
                400
            );
        }
        
        if (!isset($data['a']) || !isset($data['b']) || 
            !is_numeric($data['a']) || !is_numeric($data['b'])) {
            return new JsonResponse(
                ['error' => "Both 'a' and 'b' must be numeric."],
                400
            );
        }
        
        $sum = $data['a'] + $data['b'];
        
        return new JsonResponse(['sum' => $sum]);
    }
}