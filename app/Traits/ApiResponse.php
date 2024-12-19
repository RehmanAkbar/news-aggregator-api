<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Success response format
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Handle different types of data
        if ($data instanceof JsonResource) {
            $response['data'] = $data->response()->getData();
        } elseif ($data instanceof ResourceCollection) {
            $resourceData = $data->response()->getData();
            $response['data'] = $resourceData->data ?? null;

            // Add pagination data if it exists
            if (isset($resourceData->meta)) {
                $response['meta'] = $resourceData->meta;
            }
            if (isset($resourceData->links)) {
                $response['links'] = $resourceData->links;
            }
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response format
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, mixed $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Response with just a message
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function messageResponse(string $message, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => $code < 400,
            'message' => $message,
        ], $code);
    }
}
