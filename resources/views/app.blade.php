@php
    $s = \App\Models\Setting::publicPayload();
    $isAdmin = request()->is('admin*');
    $title = $s['meta_title'] ?: $s['site_name'];
    $description = $s['meta_description'] ?? '';
    $ogImage = $s['og_image'] ?? $s['hero_image'] ?? null;
    $sameAs = array_values(array_filter([$s['instagram'] ?? null, $s['facebook'] ?? null, $s['linkedin'] ?? null, $s['tiktok'] ?? null, $s['youtube'] ?? null]));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0a09">

    <title inertia>{{ $isAdmin ? 'Admin · '.$s['site_name'] : $title }}</title>

    @unless ($isAdmin)
        <meta name="description" content="{{ $description }}">
        <link rel="canonical" href="{{ url('/') }}">
        <meta property="og:type" content="profile">
        <meta property="og:site_name" content="{{ $s['site_name'] }}">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:url" content="{{ url('/') }}">
        @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $description }}">
        @if ($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $s['site_name'],
            'jobTitle' => 'Model',
            'description' => $description,
            'image' => $ogImage,
            'url' => url('/'),
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $s['location'] ?? null],
            'sameAs' => $sameAs,
        ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @else
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/images/logo-full.jpg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Manrope:wght@300;400;500;600;700&family=Pinyon+Script&display=swap" rel="stylesheet">

    @if (!$isAdmin && !empty($s['hero_image']))
        <link rel="preload" as="image" href="{{ $s['hero_image'] }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="{{ $isAdmin ? 'is-admin' : 'is-site' }}">
    @inertia
</body>
</html>
