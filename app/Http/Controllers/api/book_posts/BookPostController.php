<?php

namespace App\Http\Controllers\api\book_posts;

use App\Models\BookPost;
use App\Models\Book;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Validator;

class BookPostController extends Controller
{
    // Add books to a post
    public function create(Request $request)
    {   
        try {
            $validateBookPost = Validator::make($request->all(), [
                'offer_id' => 'required|integer',
                'offeredBook_id' => 'required|integer',
                'wishedBook_id' => 'required|integer',
            ]);

            if ($validateBookPost->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateBookPost->errors()
                ], 401);
            }

            $offerer = User::find($request->offer_id);
            $offeredBook = Book::find($request->offeredBook_id);
            $wishedBook = Book::find($request->wishedBook_id);

            if (!$offerer || !$offeredBook || !$wishedBook) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $bookPost = BookPost::create([
                'offerer_id' => $request->offer_id,
                'offeredBook_id' => $request->offeredBook_id,
                'wishedBook_id' => $request->wishedBook_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book post created successfully',
                'bookPost' => $bookPost
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
