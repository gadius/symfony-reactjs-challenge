<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SumController
{    
    #[Route('/api/sum', methods: ['POST'])]
    public function sum(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $errors = $this->validate($data, $validator);
        
        if (!empty($errors)) {
            return new JsonResponse(
                ['error' => implode(' ', $errors)],
                400
            );
        }        

        $sum = $data['a'] + $data['b'];
        
        return new JsonResponse(['sum' => $sum]);
    }

    private function validate($data, ValidatorInterface $validator): array
    {
        if (!is_array($data)) {
            return ['Invalid JSON payload.'];
        }
        
        $constraints = new Assert\Collection([
            'a' => [
                new Assert\NotBlank(message: 'The field "a" is required.'),
                new Assert\Type(['type' => 'numeric', 'message' => 'The field "a" must be numeric.']),
            ],
            'b' => [
                new Assert\NotBlank(message: 'The field "b" is required.'),
                new Assert\Type(['type' => 'numeric', 'message' => 'The field "b" must be numeric.']),
            ],
        ]);
        
        $violations = $validator->validate($data, $constraints);
        
        $errors = [];
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }
        
        return $errors;
    }
}