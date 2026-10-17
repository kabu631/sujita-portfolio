<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\MusicVideo;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'videos' => MusicVideo::count(),
                'gallery' => GalleryItem::count(),
                'testimonials' => Testimonial::count(),
                'inquiries' => Inquiry::count(),
                'unread' => Inquiry::unread()->count(),
            ],
            'recentInquiries' => Inquiry::latest()->take(6)->get(['id', 'name', 'email', 'project_type', 'read_at', 'created_at']),
            'latestVideos' => MusicVideo::ordered()->take(4)->get(),
        ]);
    }
}
