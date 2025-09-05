<?php

namespace App\Http\Controllers;

use App\Events\MediaDeleted;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{

    // List media: admins see all, others see only their own
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('viewAny', Media::class)) {
            $media = Media::with('author')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['id', 'title', 'alt', 'path', 'size', 'mime_type', 'created_at', 'user_id']);
        } else {
            $media = $user->media()
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['id', 'title', 'alt', 'path', 'size', 'mime_type', 'created_at']);
        }
        if((bool) $request->input('jsonResponse')) {
            return Response::json(compact('media'));
        }
        return Inertia::render('media/Index', [
            'media' => $media,
        ]);
    }

    // Store upload (no change to policy here; user stores their own)
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'title' => 'string|max:255',
            'alt' => 'string|max:255',
        ]);

        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $mime = $file->getClientMimeType();
        $size = $file->getSize();

        $storedPath = $file->storeAs(
            'uploads/media',
            \Illuminate\Support\Str::random(12) . '_' . preg_replace('/\s+/', '_', $original),
            'public'
        );

        Media::create([
            'user_id' => $request->user()->id,
            'filename' => basename($storedPath),
            'original_name' => $original,
            'mime_type' => $mime,
            'size' => $size,
            'path' => $storedPath,
            'title' => $request->input('title'),
            'alt' => $request->input('alt')
        ]);

        return redirect()->back()->with('success', 'File uploaded.');
    }

/*     // Download/view: authorize via policy
    public function show(Media $media, Request $request)
    {
        Gate::authorize('view', $media);

        if (!Storage::disk('public')->exists($media->path)) {
            abort(404);
        }

        return Storage::download($media->path, $media->original_name);
    } */

    public function update($media, Request $request)
    {
        // Authorize using the appropriate policy ability
        $media = Media::find((int) $media);
        Gate::authorize('update', $media);
        // Validate as nullable so PATCH/PUT that don't include these fields don't fail
        $data = $request->validate([
            'title' => 'string|max:255',
            'alt' => 'string|max:255',
        ]);

        $media->update($data);

        return to_route('media.index')->with('success', 'File metadata updated.');
    }

    // Delete: authorize via policy
    public function destroy($media, Request $request)
    {
        $media = Media::with('post')->find((int) $media);
        Gate::authorize('delete', $media);

        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }
        if($media->post) $media->post->update(['featured_image' => null]);
        $media->delete();
        MediaDeleted::dispatch($media);
        return redirect()->back()->with('success', 'File deleted.');
    }
}
