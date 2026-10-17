<?php

/*
|--------------------------------------------------------------------------
| Portfolio content schema
|--------------------------------------------------------------------------
|
| Every editable piece of copy on the public site is declared here. The
| admin "Site Settings" screen is generated from this schema, so adding a
| new field is as simple as adding an entry below and reading it in Vue.
|
| Supported field types: text, textarea, email, url, image
|
*/

return [

    'gallery_categories' => [
        'editorial' => 'Editorial',
        'music-video' => 'Music Video Stills',
        'portrait' => 'Portraits',
        'bts' => 'Behind the Scenes',
    ],

    'project_types' => [
        'Music Video',
        'Brand Campaign',
        'Editorial Shoot',
        'Runway / Fashion Show',
        'Digital Content',
        'Other',
    ],

    'settings' => [

        'brand' => [
            'label' => 'Brand & SEO',
            'description' => 'Identity, logo and how the site appears on Google and social media.',
            'fields' => [
                'site_name' => ['label' => 'Model name', 'type' => 'text', 'default' => 'Sujita Khakural'],
                'tagline' => ['label' => 'Tagline', 'type' => 'text', 'default' => 'Model · Music Video Muse · Digital Creator'],
                'logo' => ['label' => 'Custom logo (optional)', 'type' => 'image', 'help' => 'Leave empty to use the built-in gold SK monogram.'],
                'meta_title' => ['label' => 'SEO title', 'type' => 'text', 'default' => 'Sujita Khakural — Model & Music Video Artist'],
                'meta_description' => ['label' => 'SEO description', 'type' => 'textarea', 'default' => 'Official portfolio of Sujita Khakural — Virginia-based model and digital creator featured in music videos, editorial and runway. View work and book for your next project.'],
                'og_image' => ['label' => 'Social share image', 'type' => 'image', 'help' => 'Shown when the site is shared on Facebook, WhatsApp, X, etc. 1200×630 recommended.'],
            ],
        ],

        'hero' => [
            'label' => 'Hero Banner',
            'description' => 'Full-screen hero presentation displayed on the homepage.',
            'fields' => [
                'hero_first_name' => ['label' => 'First name (large)', 'type' => 'text', 'default' => 'Sujita'],
                'hero_last_name' => ['label' => 'Last name (large)', 'type' => 'text', 'default' => 'Khakural'],
                'hero_image' => ['label' => 'Hero Image', 'type' => 'image', 'default' => 'seed/sujita-1.jpg', 'help' => 'Main background portrait.'],
                'hero_eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'default' => 'Model — Music Videos — Editorial'],
                'hero_subtitle' => ['label' => 'Subtitle', 'type' => 'textarea', 'default' => 'Bringing confidence, elegance and personality to every frame — from the runway to the music video set.'],
                'showreel_url' => ['label' => 'Showreel video URL', 'type' => 'url', 'help' => 'YouTube or Vimeo link. Enables the "Watch Showreel" button.'],
            ],
        ],

        'about' => [
            'label' => 'About',
            'description' => 'Biography and portrait.',
            'fields' => [
                'about_heading' => ['label' => 'Heading', 'type' => 'text', 'default' => 'Elegance, in motion.'],
                'about_text' => ['label' => 'Biography', 'type' => 'textarea', 'default' => "I am a Virginia-based model and digital creator with a passion for fashion, runway, beauty, and visual storytelling.\n\nWhether it's a cinematic music video, an editorial shoot or the runway, I love bringing confidence, elegance and personality to every frame — and collaborating with artists and directors who want their story to be felt, not just seen."],
                'about_image' => ['label' => 'Portrait image', 'type' => 'image', 'help' => 'Vertical portrait works best (3:4).'],
                'location' => ['label' => 'Location', 'type' => 'text', 'default' => 'Manassas, Virginia, USA'],
                'availability' => ['label' => 'Availability note', 'type' => 'text', 'default' => 'Available for travel worldwide'],
            ],
        ],

        'measurements' => [
            'label' => 'Comp Card / Measurements',
            'description' => 'Leave any field empty to hide it on the site.',
            'fields' => [
                'm_height' => ['label' => 'Height', 'type' => 'text', 'default' => "5'3\" / 160 cm"],
                'm_bust' => ['label' => 'Bust', 'type' => 'text', 'default' => '32" / 81 cm'],
                'm_waist' => ['label' => 'Waist', 'type' => 'text', 'default' => '24" / 61 cm'],
                'm_hips' => ['label' => 'Hips', 'type' => 'text', 'default' => ''],
                'm_shoe' => ['label' => 'Shoe', 'type' => 'text', 'default' => 'EU 40 / US 7'],
                'm_dress' => ['label' => 'Dress', 'type' => 'text', 'default' => ''],
                'm_hair' => ['label' => 'Hair', 'type' => 'text', 'default' => 'Black'],
                'm_eyes' => ['label' => 'Eyes', 'type' => 'text', 'default' => 'Brown'],
            ],
        ],

        'contact' => [
            'label' => 'Booking & Contact',
            'description' => 'Booking section copy and contact details. Empty fields are hidden.',
            'fields' => [
                'booking_heading' => ['label' => 'Booking heading', 'type' => 'text', 'default' => "Let's create something unforgettable."],
                'booking_text' => ['label' => 'Booking text', 'type' => 'textarea', 'default' => 'Casting for a music video, campaign or editorial? Share a few details about your project and I will get back to you within 48 hours.'],
                'contact_email' => ['label' => 'Public email', 'type' => 'email', 'default' => ''],
                'contact_phone' => ['label' => 'Public phone / WhatsApp', 'type' => 'text', 'default' => ''],
                'agency' => ['label' => 'Agency / Management', 'type' => 'text', 'default' => ''],
            ],
        ],

        'social' => [
            'label' => 'Social Media',
            'description' => 'Full profile URLs. Empty links are hidden.',
            'fields' => [
                'instagram' => ['label' => 'Instagram', 'type' => 'url', 'default' => 'https://www.instagram.com/sujita_k11'],
                'facebook' => ['label' => 'Facebook', 'type' => 'url', 'default' => 'https://www.facebook.com/sujita.khakurel?mibextid=wwXIfr&rdid=FoV2bwgDkdEHxvFs&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1JiQDYJxvB%2F%3Fmibextid%3DwwXIfr#'],
                'linkedin' => ['label' => 'LinkedIn', 'type' => 'url', 'default' => 'https://www.linkedin.com'],
                'tiktok' => ['label' => 'TikTok', 'type' => 'url', 'default' => ''],
                'youtube' => ['label' => 'YouTube', 'type' => 'url', 'default' => ''],
            ],
        ],
    ],
];
