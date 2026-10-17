<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\Reorderable;
use App\Http\Controllers\Controller;
use App\Models\MusicVideo;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MusicVideoController extends Controller
{
    use Reorderable;

    protected function reorderModel(): string
    {
        return MusicVideo::class;
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Videos/Index', [
            'videos' => MusicVideo::ordered()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Videos/Form', ['video' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) MusicVideo::max('sort_order') + 1;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = Media::store($request->file('thumbnail'), 'videos');
        }

        MusicVideo::create($data);

        return redirect()->route('admin.videos.index')->with('success', 'Music video added.');
    }

    public function edit(MusicVideo $video): Response
    {
        return Inertia::render('Admin/Videos/Form', ['video' => $video]);
    }

    public function update(Request $request, MusicVideo $video): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = Media::replace($video->thumbnail, $request->file('thumbnail'), 'videos');
        } elseif ($request->boolean('remove_thumbnail')) {
            Media::delete($video->thumbnail);
            $data['thumbnail'] = null;
        }

        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', 'Music video updated.');
    }

    public function destroy(MusicVideo $video): RedirectResponse
    {
        Media::delete($video->thumbnail);
        $video->delete();

        return back()->with('success', 'Music video deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'artist' => ['nullable', 'string', 'max:160'],
            'role' => ['nullable', 'string', 'max:120'],
            'director' => ['nullable', 'string', 'max:160'],
            'year' => ['nullable', 'integer', 'between:1990,2100'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'thumbnail' => ['nullable', 'image', 'max:8192'],
        ]);

        unset($data['thumbnail']);

        return $data;
    }
}
