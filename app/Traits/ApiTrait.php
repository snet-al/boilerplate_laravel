<?php

namespace App\Traits;

use App\Helpers\ApiCodes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiTrait
{
    /**
     * @param $responseMessage
     * @param $statusId
     * @param array $data
     */
    public function responseToJson($responseMessage, $statusId, $data = [])
    {
        $meta = [];
        if ($data instanceof AnonymousResourceCollection && request('page-size')) {
            $meta = [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'path' => $data->resolveCurrentPath(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ];
        }

        return response()
            ->json([
                'message' => $responseMessage,
                'data' => $data,
                'meta' => $meta,
            ], $statusId);
    }

    public function successResponseWithData($data)
    {
        return [
            'message' => ApiCodes::getSuccessMessage(),
            'status' => ApiCodes::SUCCESS,
            'data' => $data,
        ];
    }

    /**
     * Empty success response
     *
     * @param null $message
     * @param null $status
     */
    public function successResponse($message = null, $status = null)
    {
        $message = $message ?: ApiCodes::getSuccessMessage();
        $status = $status ?: ApiCodes::SUCCESS;

        return $this->responseToJson($message, $status);
    }

    /**
     * @param string|null $message
     * @param int|null $status
     */
    public function resourceNotFound($message = null, $status = null)
    {
        $message = $message ?: ApiCodes::getResourceNotFoundMessage();
        $status = $status ?: ApiCodes::RESOURCE_NOT_FOUND;

        return $this->responseToJson($message, $status, []);
    }

    /**
     * @param string|null $message
     * @param int|null $status
     */
    public function resourceCantBeModified($message = null, $status = null)
    {
        $message = $message ?: ApiCodes::FORBIDDEN;
        $status = $status ?: ApiCodes::FORBIDDEN;

        return $this->responseToJson($message, $status, []);
    }

    /**
     * @param string|null $message
     * @param int|null $status
     */
    public function validationFailed($message = null, $status = null)
    {
        $message = $this->transformValidationMessages($message);
        $status = $status ?: ApiCodes::UNAUTHENTICATED;

        return $this->responseToJson($message, $status, []);
    }

    /**
     * @param string|null $message
     * @param int|null $status
     */
    public function generalError($message = null, $status = null)
    {
        $message = $message ?: ApiCodes::getGeneralErrorMessage();
        $status = $status ?: ApiCodes::SERVICE_UNAVAILABLE;

        return $this->responseToJson($message, $status, []);
    }

    /**
     * @param $errors
     *
     * @return string
     */
    public function transformValidationMessages($errors): string
    {
        $errorMessages = '';
        foreach ($errors->all() as $error) {
            $errorMessages .= $error . "\n";
        }

        return $errorMessages;
    }

    public function getJsonResponse($resourceItems, $resourceClass, $asModel = false, $message = null)
    {
        if ($resourceItems instanceof Model || $asModel) {
            $data = $resourceClass::make($resourceItems);
        } else {
            $data = $resourceClass::collection($resourceItems);
        }

        $message = $message ?: ApiCodes::getSuccessMessage();

        return $this->responseToJson($message, ApiCodes::SUCCESS, $data);
    }

    public function fetchResults($query)
    {
        $page = request()->get('page') ?? 1;
        $pageSize = request()->get('page-size') ?? 25;

        if (request()->filled('no-pagination') && (bool)request()->get('no-pagination')) {
            $results = $query->get();
        } else {
            $query = $this->prepareSortingQuery($query);
            $results = $query->paginate($pageSize);
        }
        return $results;
    }

    public function prepareSortingQuery($query)
    {
        if (request()->filled('sort-by') && request()->get('sort-by') !== 'null' && request()->get('sort-by') !== '') {
            $sortBy = request()->get('sort-by');
            $descending = request()->filled('descending') && request()->get('descending') === 'true'
                ? 'desc'
                : 'asc';
            $query->orderBy($sortBy, $descending);
        }

        return $query;
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponseWithPaginatedData($data)
    {
        return response()->json([
            'message' => ApiCodes::getSuccessMessage(),
            'status' => ApiCodes::SUCCESS,
            'data' => $data,
            'links' => [
                'first' => $data->resolveCurrentPath() . '?page=1',
                'last' => $data->resolveCurrentPath() . '?page=' . $data->lastPage(),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'path' => $data->resolveCurrentPath(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
        ]);
    }

    /**
     * @param array $data
     * @param null $message
     * @param null $status
     * @param array $errors
     * @param int $httpStatusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restApiResponse($data = [], $message = null, $status = null, $errors = [], $httpStatusCode = 200)
    {
        $json = $this->formatResponse(
            $errors,
            $message ?? ApiCodes::getSuccessMessage(),
            $status ?? ApiCodes::SUCCESS,
            $data
        );

        return response()->json($json, $httpStatusCode);
    }

    /**
     * @param $errors
     * @param $message
     * @param $status
     * @param $data
     *
     * @return array
     */
    private function formatResponse($errors, $message, $status, $data)
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'errors' => $errors,
        ];
    }
}
