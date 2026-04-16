<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ClientController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ClientController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.clients.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.clients.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $client = Client::where('id', decryptId($id))->first();
            if ($client) {
                return view('admin.clients.edit', [
                    'client' => $client
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataClients(Request $request)
    {
        $function_name = 'getDataClients';
        try {
            if ($request->ajax()) {
                $clients = Client::query();

                if ($request->status !== null && $request->status !== '') {
                    $clients->where('status', $request->status);
                }

                if ($request->created_date) {
                    $clients->whereDate('created_at', $request->created_date);
                }

                return DataTables::of($clients)
                    ->addColumn('status', function ($client) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $client->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($client) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.clients.edit', encryptId($client->id)),
                            'delete_id' => $client->id,
                            'current_status' => $client->status,
                            'hidden_id' => $client->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($client) {
                        if ($client->icon && file_exists(public_path('uploads/clients/' . $client->icon))) {
                            $imageUrl = asset('uploads/clients/' . $client->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:80px; border-radius: 8px;" alt="Client Icon" />';
                        }
                        return '';
                    })
                    ->rawColumns(['action', 'icon', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'name' => 'required',
                'icon' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg,webp' : 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::clientImageUpload($request->icon);
                }

                Client::create([
                    'name' => $request->name,
                    'icon' => $icon ?? null,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Client added successfully"
                ]);
            } else {
                $client = Client::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/clients/' . $client->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $icon = ImageUploadHelper::clientImageUpload($request->icon);
                } else {
                    $icon = $client->icon;
                }

                $client->update([
                    'name' => $request->name,
                    'icon' => $icon,
                    'status' => (int) $request->status,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Client updated successfully"
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            Client::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';
        try {
            $client = Client::where('id', $id)->first();
            if ($client) {
                $filePath = public_path('uploads/clients/' . $client->icon);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $client->delete();
                return response()->json(['message' => "Client deleted successfully"]);
            }
            return response()->json(['error' => 'Client not found.'], 404);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
