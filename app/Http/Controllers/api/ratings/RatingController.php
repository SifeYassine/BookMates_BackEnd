<?php

namespace App\Http\Controllers\api\ratings;

use App\Models\Rating;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class RatingController extends Controller
{
    // Create a new rating
    public function create(Request $request)
    {
        try {
            $validateRating = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
                'rater_id' => 'required|integer|exists:users,id',
                'ratee_id' => 'required|integer|exists:users,id',
                'exchangeRequest_id' => 'required|integer|exists:exchange_requests,id',
            ]);

            if ($validateRating->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validateRating->errors()
                ], 401);
            }

            $rating = Rating::create([
                'rating' => $request->rating,
                'rater_id' => $request->rater_id,
                'ratee_id' => $request->ratee_id,
                'exchangeRequest_id' => $request->exchangeRequest_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Rating created successfully',
                'data' => $rating
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all ratings
    public function index()
    {
        try {
            $ratings = Rating::all();
            return response()->json([
                'status' => true,
                'message' => 'Ratings retrieved successfully',
                'data' => $ratings
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a rating
    public function update(Request $request, $id)
    {
        try {
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            $validateRating = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
            ]);

            if ($validateRating->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateRating->errors()
                ], 401);
            }

            $rating->update([
                'rating' => $request->rating
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Rating updated successfully',
                'data' => $rating
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a rating
    public function delete($id)
    {
        try {
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            $rating->delete();
            return response()->json([
                'status' => true,
                'message' => 'Rating deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
