<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\Reorderable;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    use Reorderable;

    protected function reorderModel(): string
    {
        return Testimonial::class;
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => Testimonial::ordered()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Testimonials/Form', ['testimonial' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($this->validated($request) + [
            'sort_order' => (int) Testimonial::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('Admin/Testimonials/Form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request));

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:160'],
            'quote' => ['required', 'string', 'max:1500'],
            'is_published' => ['boolean'],
        ]);
    }
}
