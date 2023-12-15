<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }


    public function store(Request $request)
    {
        // Validate and store post
        // Implement your validation logic based on your requirements
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            'category_id' => 'required|exists:categories,id',
            // Add other fields as needed
        ]);

        // Assuming the 'image' field is a file, handle file upload
        $imagePath = $request->file('image')->store('post_images', 'public');

        // Create a new Post instance with the validated data
        $post = new Post([
            'title' => $request->input('name'),
            'image' => $imagePath,
            'category_id' => $request->input('category_id'),
            'is_visible' => $request->has('is_visible'),
        ]);

        // Save the post to the database
        $post->save();

        // Redirect back to the main page with a success message
        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update post
        $request->validate([
            'title' => 'nullable|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:4096',  // Allow image to be nullable
            'is_visible' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            // Add other fields as needed
        ]);

        $post = Post::findOrFail($id);

        // Check if the 'image' file has been provided
        if ($request->hasFile('image')) {
            // Handle file upload and update image path
            $imagePath = $request->file('image')->store('post_images', 'public');
            $post->image = $imagePath;
        }

        // Update other fields
        $post->title = $request->input('title');
        $post->category_id = $request->input('category_id');
        $post->is_visible = $request->has('is_visible');

        // Save the changes
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post updated successfully');
    }



    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }

    public function showAllPosts()
    {
        $posts = Post::all();
        return view('main', compact('posts'));
    }

}


