<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = $request->string('filter')->toString();
        $search = trim($request->string('search')->toString());

        $inquiries = Inquiry::query()
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries' => $inquiries,
            'filters' => ['filter' => $filter ?: 'all', 'search' => $search],
        ]);
    }

    public function show(Inquiry $inquiry): Response
    {
        $inquiry->markAsRead();

        return Inertia::render('Admin/Inquiries/Show', ['inquiry' => $inquiry]);
    }

    public function unread(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->forceFill(['read_at' => null])->save();

        return redirect()->route('admin.inquiries.index')->with('success', 'Marked as unread.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
