<header class="site-header">
<nav class="site-nav" aria-label="Main navigation">
<a class="site-brand" href="{{ route('home') }}" aria-label="Roman Bahram Logistic Services home"><img class="site-brand-logo" src="{{ asset(request()->routeIs('home') ? 'images/rbls-whitelogo with no txt.svg' : 'images/rbls-logo with no txt.svg') }}" alt="" width="54" height="42"><span class="site-brand-name"><strong>ROMAN BAHRAM</strong><small>LOGISTIC SERVICES</small></span></a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu"><span class="sr-only">Open navigation</span><span></span><span></span><span></span></button>
<div class="site-links" id="site-menu">
@foreach(['home' => 'Home', 'about' => 'About', 'services' => 'Services', 'projects' => 'Projects', 'profile' => 'Company Profile'] as $name => $label)
<a href="{{ route($name) }}" @if(request()->routeIs($name)) aria-current="page" @endif>{{ $label }}</a>
@endforeach
<a class="nav-contact" href="{{ route('contact') }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Get in touch <span aria-hidden="true">↗</span></a>
</div></nav></header>
