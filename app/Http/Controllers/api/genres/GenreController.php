<?php

namespace App\Http\Controllers\api\genres;

use App\Models\Genre;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class GenreController extends Controller
{
    // Create a new Genre
    public function create(Request $request)
    {
        try {
            $validateGenre = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:genres'
            ]);

            if ($validateGenre->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateGenre->errors()
                ], 401);
            }

            $Genre = Genre::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Genre created successfully',
                'Genre' => $Genre
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all genres
    public function index()
    {
        try {
            $genres = Genre::all();
            return response()->json([
                'status' => true,
                'message' => 'All genres',
                'genres' => $genres
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a Genre
    public function update(Request $request, $id)
    {
        try {
            $Genre = Genre::find($id);

            if (!$Genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found'
                ], 404);
            }

            $validateGenre = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:genres,name,' . $id
            ]);

            if ($validateGenre->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateGenre->errors()
                ], 401);
            }

            $Genre->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Genre updated successfully',
                'Genre' => $Genre
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a Genre
    public function delete($id)
    {
        try {
            $Genre = Genre::find($id);

            if (!$Genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found'
                ], 404);
            }

            $Genre->delete();
            return response()->json([
                'status' => true,
                'message' => 'Genre deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}