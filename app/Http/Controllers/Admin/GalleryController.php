<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\Reorderable;
use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    use Reorderable;

    protected function reorderModel(): string
    {
        return GalleryItem::class;
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Gallery/Index', [
            'items' => GalleryItem::ordered()->get(),
            'categories' => config('portfolio.gallery_categories'),
        ]);
    }

    /**
     * Bulk upload: many images at once into one category.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'max:30'],
            'images.*' => ['image', 'max:8192'],
            'category' => ['required', Rule::in(array_keys(config('portfolio.gallery_categories')))],
        ]);

        // New uploads go to the top of the gallery.
        GalleryItem::query()->increment('sort_order', count($request->file('images')));

        foreach (array_values($request->file('images')) as $i => $file) {
            GalleryItem::create([
                'image' => Media::store($file, 'gallery'),
                'category' => $request->input('category'),
                'sort_order' => $i + 1,
            ]);
        }

        $count = count($request->file('images'));

        return back()->with('success', $count.' '.str('image')->plural($count).' uploaded.');
    }

    public function update(Request $request, GalleryItem $item): RedirectResponse
    {
        $item->update($request->validate([
            'caption' => ['nullable', 'string', 'max:200'],
            'category' => ['required', Rule::in(array_keys(config('portfolio.gallery_categories')))],
            'is_published' => ['boolean'],
        ]));

        return back()->with('success', 'Image updated.');
    }

    public function destroy(GalleryItem $item): RedirectResponse
    {
        Media::delete($item->image);
        $item->delete();

        return back()->with('success', 'Image deleted.');
    }
}
