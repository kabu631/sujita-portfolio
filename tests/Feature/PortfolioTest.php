<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_portfolio_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sujita Khakural');
    }

    public function test_booking_inquiry_can_be_submitted_via_form(): void
    {
        $payload = [
            'name' => 'Director Alex',
            'email' => 'alex@cinema.com',
            'phone' => '+1 555 123 4567',
            'company' => 'Apex Visuals',
            'project_type' => 'Music Video',
            'shoot_date' => now()->addDays(14)->format('Y-m-d'),
            'message' => 'We are casting Sujita for the main lead in a high-concept music video in Los Angeles.',
            'website' => '', // honeypot left blank
        ];

        $response = $this->post('/inquiries', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'email' => 'alex@cinema.com',
            'company' => 'Apex Visuals',
        ]);
    }

    public function test_booking_inquiry_rejects_past_shoot_date(): void
    {
        $payload = [
            'name' => 'Director Past',
            'email' => 'past@cinema.com',
            'shoot_date' => now()->subDay()->format('Y-m-d'), // past date
            'message' => 'Shoot date is in the past, which must be rejected.',
        ];

        $response = $this->post('/inquiries', $payload);

        $response->assertSessionHasErrors(['shoot_date']);
        $this->assertDatabaseMissing('inquiries', [
            'email' => 'past@cinema.com',
        ]);
    }

    public function test_booking_inquiry_honeypot_silently_discards_bot_submissions(): void
    {
        $payload = [
            'name' => 'Spam Bot',
            'email' => 'bot@spam.com',
            'message' => 'Spam message offering services...',
            'website' => 'http://spam-link.com', // filled honeypot
        ];

        $response = $this->post('/inquiries', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('inquiries', [
            'email' => 'bot@spam.com',
        ]);
    }

    public function test_guest_is_redirected_to_admin_login_when_visiting_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/admin-login', [
            'email' => 'admin@sujita.com',
            'password' => 'Sujita@2026',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $dashboardResponse = $this->get('/admin');
        $dashboardResponse->assertStatus(200);
    }

    public function test_authenticated_admin_can_update_settings(): void
    {
        $admin = User::where('email', 'admin@sujita.com')->first();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Sujita Khakural',
            'tagline' => 'International Fashion & Music Video Model',
            'about_heading' => 'New Editorial Heading',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('International Fashion & Music Video Model', Setting::get('tagline'));
    }

    public function test_authenticated_admin_can_create_music_video(): void
    {
        $admin = User::where('email', 'admin@sujita.com')->first();

        $response = $this->actingAs($admin)->post('/admin/videos', [
            'title' => 'Maya Ko Dhun',
            'artist' => 'Sushant KC',
            'role' => 'Lead Model',
            'year' => 2026,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'Romantic music video',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.videos.index'));
        $this->assertDatabaseHas('music_videos', [
            'title' => 'Maya Ko Dhun',
            'artist' => 'Sushant KC',
        ]);
    }
}
