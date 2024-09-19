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
                'offerer_id' => 'required|integer',
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

            $offerer = User::find($request->offerer_id);
            $offeredBook = Book::find($request->offeredBook_id);
            $wishedBook = Book::find($request->wishedBook_id);

            if (!$offerer || !$offeredBook || !$wishedBook) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $bookPost = BookPost::create([
                'offerer_id' => $request->offerer_id,
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

    // Get all book posts
    public function index()
    {
        try {
            $bookPosts = BookPost::all();
            
            return response()->json([
                'status' => true,
                'message' => 'All book posts',
                'bookPosts' => $bookPosts
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get a single book post
    public function show($id)
    {
        try {
            $bookPost = BookPost::find($id);

            return response()->json([
                'status' => true,
                'message' => 'Book post',
                'bookPost' => $bookPost
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a book post
    public function update(Request $request, $id)
    {
        try {
            $bookPost = BookPost::find($id);

            if (!$bookPost) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book post not found'
                ], 404);
            }

            $validateBookPost = Validator::make($request->all(), [
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

            $offeredBook = Book::find($request->offeredBook_id);
            $wishedBook = Book::find($request->wishedBook_id);

            if (!$offeredBook || !$wishedBook) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $bookPost->update([
                'offeredBook_id' => $request->offeredBook_id,
                'wishedBook_id' => $request->wishedBook_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book post updated successfully',
                'bookPost' => $bookPost
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a book post
    public function delete($id)
    {
        try {
            $bookPost = BookPost::find($id);

            if (!$bookPost) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book post not found'
                ], 404);
            }

            $bookPost->delete();
            return response()->json([
                'status' => true,
                'message' => 'Book post deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
