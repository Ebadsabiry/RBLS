<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="@yield('description', 'Afghan-owned procurement, transportation, vehicle rental and field logistics. Roman Bahram Logistic Services, Kabul, Afghanistan.')">
<meta name="theme-color" content="#071c32">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="@yield('title', 'Roman Bahram Logistic Services')">
<meta property="og:description" content="@yield('description', 'Procurement, transportation, vehicle rental and field logistics across Afghanistan.')">
<meta property="og:image" content="{{ asset('images/rbls-hero.png') }}">
<meta property="og:url" content="{{ url()->current() }}">
<title>@yield('title', 'Roman Bahram Logistic Services | Moving Afghanistan Forward')</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/rbls-logo with no txt.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@if(request()->routeIs('home'))
<link rel="preload" href="{{ asset('images/rbls-hero.png') }}" as="image">
<link rel="stylesheet" href="{{ asset('css/home.css') }}?v=20261009-3">
@endif
<link rel="stylesheet" href="{{ asset('css/site.css') }}?v=20261009">
<link rel="stylesheet" href="{{ asset('css/restored-design.css') }}?v=20261009-5">
<script src="{{ asset('js/site.js') }}?v=20261009" defer></script>
<noscript><style>@media(max-width:800px){.site-nav{flex-wrap:wrap}.site-links,.is-home .site-links{display:flex;position:static;flex-basis:100%;margin-top:10px}.menu-toggle{display:none}}</style></noscript>
</head>
<body id="top" class="{{ request()->routeIs('home') ? 'is-home' : '' }}" data-page="{{ request()->route()->getName() }}">
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="scroll-progress" aria-hidden="true"></div>
@include('partials.header')
<main id="main-content" tabindex="-1">@yield('content')</main>
@include('partials.footer')
</body>
</html>
