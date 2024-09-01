<?php

namespace App\Http\Controllers\api\bookmarks;

use App\Models\Bookmark;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class BookmarkController extends Controller
{
    // Create a new Bookmark
    public function create(Request $request)
    {
        try {
            $validateBookmark = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'bookPost_id' => 'required|integer',
            ]);

            if ($validateBookmark->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validateBookmark->errors()
                ], 401);
            }

            $bookmark = Bookmark::create([
                'user_id' => $request->user_id,
                'bookPost_id' => $request->bookPost_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bookmark created successfully',
                'data' => $bookmark
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }             
    }

    // Get all bookmarks
    public function index()
    {
        try {
            $bookmarks = Bookmark::all();

            return response()->json([
                'success' => true,
                'message' => 'All bookmarks',
                'data' => $bookmarks
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a bookmark
    public function delete($id)
    {
        try {
            $bookmark = Bookmark::find($id);

            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bookmark not found'
                ], 404);
            }

            $bookmark->delete();
            return response()->json([
                'success' => true,
                'message' => 'Bookmark deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
