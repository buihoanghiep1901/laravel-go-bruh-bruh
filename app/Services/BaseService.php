<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract protected function setModel();

    /**
     * @param $data
     * @param $message
     * @return JsonResponse
     */
    public function responseSuccess($data, $message = null): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data, 'message' => $message], JsonResponse::HTTP_OK);
    }

    /**
     * @param string $api
     * @param $message
     * @return JsonResponse
     */
    public function responseError($message, string $api = ''): JsonResponse
    {
        \Log::error('call ' . $api . ' error: ' . json_encode($message));
        return response()->json(['success' => false, 'message' => $message], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param $query
     * @param Request $request
     * @return array
     */
    public function getPaginateByQuery($query, Request $request): array
    {
        $without = ['limit', 'offset'];
        $total = DB::query()->fromSub($query->cloneWithout($without), 'sub_table')->count();
        $currentPage = $request->current_page ? (int)$request->current_page : 1;
        $perPage = $request->per_page ? (int)$request->per_page : 20;
        if ($total == 0) {
            $lastPage = 1;
            $offSet = 0;
            $from = 0;
            $to = 0;
        } else {
            $lastPage = ceil($total / $perPage);
            $offSet = (($currentPage - 1) * $perPage);
            $from = $total ? ($offSet + 1) : 0;
            $to = $currentPage == $lastPage ? $total : ((($currentPage - 1) * $perPage) + $perPage);
        }
        $data = $query->offset($offSet ?: 0)->limit($perPage)->get();
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $currentPage,
                'from' => $from,
                'to' => $to,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total
            ]
        ];
    }
}
