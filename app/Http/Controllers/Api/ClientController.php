<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ClientController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        parent::__construct();
        $this->controller_name = 'API/ClientController';
        $this->success_status = config('custom.status_code_for_success') ?? 200;
        $this->exception_status = config('custom.status_code_for_exception_error') ?? 500;
        $this->backend_error_status = config('custom.status_code_for_backend_error') ?? 400;
        $this->validation_error_status = config('custom.status_code_for_validation_error') ?? 422;
        $this->common_error_message = config('custom.common_error_message') ?? 'Internal Server Error';
    }

    public function getClients(): JsonResponse
    {
        $function_name = 'getClients';
        try {
            $clients = Client::select(
                'id',
                'name',
                'icon',
                'status',
                'created_at',
                'updated_at'
            )
                ->where('status', 1)
                ->get();

            if ($clients->isEmpty()) {
                return $this->sendError('No clients found.', $this->backend_error_status);
            }

            $clients->transform(function ($client) {
                if ($client->icon) {
                    $client->icon = asset('uploads/clients/' . $client->icon);
                }
                return $client;
            });

            return $this->sendResponse(
                $clients,
                'Clients retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
