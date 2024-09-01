<?php

namespace App\Http\Controllers\api\exchangerequests;

use App\Models\ExchangeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ExchangeRequestController extends Controller
{
    // Create a new ExchnageRequest
    public function create(Request $request)
    {
        try {
            $validatorExchangeRequest = Validator::make($request->all(), [
                'status' => 'nullable|in:accepted,pending,declined',
                'notification_sent' => 'nullable|boolean',
                'requester_id' => 'required|integer',
                'bookPost_id' => 'required|integer',
            ]);

            if ($validatorExchangeRequest->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validatorExchangeRequest->errors()
                ], 401);
            }

            $exchangeRequest = ExchangeRequest::create([
                'status' => $request->status ?? 'pending',
                'notification_sent' => $request->notification_sent ?? 0,
                'requester_id' => $request->requester_id,
                'bookPost_id' => $request->bookPost_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exchange request created successfully',
                'data' => $exchangeRequest
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all exchange requests
    public function index()
    {
        try {
            $exchangeRequests = ExchangeRequest::all();

            return response()->json([
                'success' => true,
                'data' => $exchangeRequests
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update an exchange request
    public function update(Request $request, $id)
    {
        try {
            $exchangeRequest = ExchangeRequest::find($id);

            if (!$exchangeRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exchange request not found'
                ], 404);
            }

            $validatorExchangeRequest = Validator::make($request->all(), [
                'status' => 'nullable|in:accepted,pending,declined',
            ]);

            if ($validatorExchangeRequest->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validatorExchangeRequest->errors()
                ], 401);
            }

            $status = $request->status;
            $notification = $exchangeRequest->notification_sent;

            if ($status == 'accepted' || $status == 'declined') {
                $notification = 1;
            }

            $exchangeRequest->update([
                'status' => $status,
                'notification_sent' => $notification,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exchange request updated successfully',
                'data' => $exchangeRequest
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete an exchange request
    public function delete($id)
    {
        try {
            $exchangeRequest = ExchangeRequest::find($id);

            if (!$exchangeRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exchange request not found'
                ], 404);
            }

            $exchangeRequest->delete();
            return response()->json([
                'success' => true,
                'message' => 'Exchange request deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
