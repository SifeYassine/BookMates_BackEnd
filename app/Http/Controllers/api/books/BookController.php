<?php

namespace App\Http\Controllers\api\books;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Validator;

class BookController extends Controller
{
    // Create a new Book
    public function create(Request $request)
    {
        try {
            $validateBook = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'page_count' => 'required|integer',
                'published_year' => 'required|integer',
                'isbn' => 'required|string|max:255|unique:books',
                'user_id' => 'required|integer|exists:users,id',
                'genre_id' => 'required|integer|exists:genres,id',
            ]);

            if ($validateBook->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateBook->errors()
                ], 401);
            }
            
            // Handle image upload
            $imagePath = null;
            $folder = 'public/book_covers';
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $imagePath = $file->storeAs($folder, $imageName);  
                $imagePath = Storage::url($imagePath);
            }


            $book = Book::create([
                'title' => $request->title,
                'author' => $request->author,
                'description' => $request->description,
                'cover_image' => $imagePath,
                'page_count' => $request->page_count,
                'published_year' => $request->published_year,
                'isbn' => $request->isbn,
                'user_id' => $request->user_id,
                'genre_id' => $request->genre_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book created successfully',
                'Book' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all books
    public function index()
    {
        try {
            $books = Book::all();

            return response()->json([
                'status' => true,
                'message' => 'All books',
                'books' => $books
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a Book
    public function update(Request $request, $id)
    {
        try {
            $book = Book::find($id);
            
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }
    
            // Validate only the fields that are being updated
            $validateBook = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'page_count' => 'required|integer',
                'published_year' => 'required|integer',
                'isbn' => 'required|string|max:255'
            ]);
    
            if ($validateBook->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateBook->errors()
                ], 401);
            }
    
            // Initialize imagePath
            $imagePath = $book->cover_image;
            $folder = 'public/book_covers';
    
            // Handle image upload if a new image is provided
            if ($request->hasFile('cover_image')) {
                // Convert the URL to the actual file path
                $oldImagePath = str_replace('/storage', 'public', $book->cover_image);
    
                // Delete old image if exists
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
    
                $file = $request->file('cover_image');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $imagePath = $file->storeAs($folder, $imageName);
                $imagePath = Storage::url($imagePath);
            }
    
            $book->update([
                'title' => $request->title,
                'author' => $request->author,
                'description' => $request->description,
                'cover_image' => $imagePath,
                'page_count' => $request->page_count,
                'published_year' => $request->published_year,
                'isbn' => $request->isbn
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Book updated successfully',
                'Book' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }    
    
    // Delete a Book
    public function delete($id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $book->delete();
            return response()->json([
                'status' => true,
                'message' => 'Book deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}