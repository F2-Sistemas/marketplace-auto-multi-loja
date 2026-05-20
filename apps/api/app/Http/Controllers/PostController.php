<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::published();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category') && $request->input('category') !== 'Tudo') {
            $query->where('category', $request->input('category'));
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(12);

        return response()->json($posts);
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->first();

        if ($post === null) {
            return response()->json([
                'message' => 'Post não encontrado.',
            ], 404);
        }

        // Increment views count safely
        $post->increment('views_count');

        return response()->json($post);
    }

    /**
     * Administrative endpoints: List all posts (including drafts).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($posts);
    }

    /**
     * Administrative endpoints: Store a new post.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|url|max:255',
            'category' => 'required|string|max:255',
            'related_links' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $slug = $request->input('slug');
        if (empty($slug)) {
            $slug = Str::slug($request->input('title'));
            // Ensure unique
            $slugCount = Post::where('slug', 'like', "{$slug}%")->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . time();
            }
        }

        $publishedAt = $request->input('published_at');
        if ($request->input('status') === 'published' && empty($publishedAt)) {
            $publishedAt = now();
        }

        $post = Post::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
            'category' => $request->input('category'),
            'related_links' => $request->input('related_links'),
            'status' => $request->input('status'),
            'published_at' => $publishedAt,
        ]);

        return response()->json([
            'message' => 'Post criado com sucesso!',
            'post' => $post,
        ], 201);
    }

    /**
     * Administrative endpoints: Update a post.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if ($post === null) {
            return response()->json([
                'message' => 'Post não encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:posts,slug,{$id}",
            'summary' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|url|max:255',
            'category' => 'required|string|max:255',
            'related_links' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $slug = $request->input('slug');
        if (empty($slug)) {
            $slug = Str::slug($request->input('title'));
        }

        $publishedAt = $request->input('published_at');
        if ($request->input('status') === 'published' && empty($publishedAt)) {
            $publishedAt = $post->published_at ?? now();
        }

        $post->update([
            'title' => $request->input('title'),
            'slug' => $slug,
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
            'category' => $request->input('category'),
            'related_links' => $request->input('related_links'),
            'status' => $request->input('status'),
            'published_at' => $publishedAt,
        ]);

        return response()->json([
            'message' => 'Post atualizado com sucesso!',
            'post' => $post,
        ]);
    }

    /**
     * Administrative endpoints: Delete a post.
     */
    public function destroy(int $id): JsonResponse
    {
        $post = Post::find($id);

        if ($post === null) {
            return response()->json([
                'message' => 'Post não encontrado.',
            ], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post excluído com sucesso!',
        ]);
    }
}
