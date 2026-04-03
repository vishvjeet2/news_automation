<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>News Automation — AI-Powered News Platform</title>
    <meta name="description" content="Generate, manage, and publish news articles instantly using AI-powered automation." />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    {{-- Tailwind CDN (replace with Vite build in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '#f8f8f8',
                            100: '#f0f0f0',
                            200: '#e4e4e4',
                            300: '#d1d1d1',
                            400: '#a8a8a8',
                            500: '#737373',
                            600: '#525252',
                            700: '#3d3d3d',
                            800: '#262626',
                            900: '#171717',
                            950: '#0a0a0a',
                        }
                    },
                    animation: {
                        'fade-up':   'fadeUp 0.7s ease forwards',
                        'fade-in':   'fadeIn 0.6s ease forwards',
                        'float':     'float 6s ease-in-out infinite',
                        'float-slow':'float 9s ease-in-out infinite',
                        'pulse-slow':'pulse 4s cubic-bezier(0.4,0,0.6,1) infinite',
                        'scroll':    'scrollDown 2s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeUp:     { '0%': { opacity: 0, transform: 'translateY(30px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        fadeIn:     { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        float:      { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-18px)' } },
                        scrollDown: { '0%,100%': { transform: 'translateY(0)', opacity: 1 }, '50%': { transform: 'translateY(8px)', opacity: 0.4 } },
                    }
                }
            }
        }
    </script>

    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html { font-family: 'Inter', sans-serif; }
        body { margin: 0; background: #ffffff; color: #171717; }

        /* ── Delay utilities ── */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
        .delay-800 { animation-delay: 0.8s; }

        /* ── Fill mode ── */
        .anim-fill { animation-fill-mode: both; opacity: 0; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d1d1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

        /* ── Noise texture overlay ── */
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        /* ── Grid pattern ── */
        .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 56px 56px;
        }

        /* ── Glassmorphism navbar ── */
        .glass-nav {
            background: rgba(10,10,10,0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .glass-nav.scrolled {
            background: rgba(10,10,10,0.92);
            box-shadow: 0 1px 40px rgba(0,0,0,0.4);
        }

        /* ── Card hover lift ── */
        .card-lift { transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease; }
        .card-lift:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }

        /* ── Step connector ── */
        .step-line::before {
            content: '';
            position: absolute;
            top: 28px;
            left: calc(50% + 28px);
            width: calc(100% - 56px);
            height: 1px;
            background: linear-gradient(90deg, #d1d1d1, #e4e4e4);
        }

        /* ── Status badges ── */
        .badge-published { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-generated { background: #f8f8f8; color: #3d3d3d; border: 1px solid #d1d1d1; }
        .badge-pending   { background: #fefce8; color: #854d0e; border: 1px solid #fde68a; }

        /* ── Mobile menu ── */
        #mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        #mobile-menu.open { max-height: 400px; }

        /* ── Smooth reveal on scroll ── */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.65s ease, transform 0.65s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }

        /* ── Orb blobs ── */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        /* ── Feature icon ring ── */
        .icon-ring {
            background: linear-gradient(135deg, #262626, #3d3d3d);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 4px 16px rgba(0,0,0,0.3);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-lift:hover .icon-ring {
            transform: scale(1.08);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.15), 0 8px 24px rgba(0,0,0,0.4);
        }

        /* ── CTA gradient border ── */
        .gradient-border {
            position: relative;
            background: #0a0a0a;
            border-radius: 20px;
        }
        .gradient-border::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 21px;
            background: linear-gradient(135deg, #525252, #262626, #525252);
            z-index: -1;
        }

        /* ── Testimonial quote mark ── */
        .quote-mark { font-size: 5rem; line-height: 1; color: #e4e4e4; font-family: Georgia, serif; }

        /* ── Stat counter animation ── */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-val { animation: countUp 0.8s ease forwards; }
    </style>
</head>

<body class="antialiased">

{{-- ═══════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════ --}}
<header id="navbar" class="glass-nav fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="#" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0 group-hover:shadow-md transition-shadow">
                    {{-- Lightning bolt SVG --}}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#171717" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                </div>
                <span class="font-bold text-white text-[17px] tracking-tight">
                    News<span class="text-brand-400">Auto</span>
                </span>
            </a>

            {{-- Desktop Nav Links --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="#features"  class="px-4 py-2 text-sm font-medium text-brand-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">Features</a>
                <a href="#preview"   class="px-4 py-2 text-sm font-medium text-brand-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">Preview</a>
                <a href="#how"       class="px-4 py-2 text-sm font-medium text-brand-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">How It Works</a>
                <a href="#pricing"   class="px-4 py-2 text-sm font-medium text-brand-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">Pricing</a>
            </nav>

            {{-- CTA Buttons --}}
            <div class="hidden md:flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 text-sm font-semibold text-white bg-white/10 hover:bg-white/15 border border-white/10 rounded-lg transition-all">
                        Dashboard →
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-brand-300 hover:text-white transition-colors">
                        Login
                    </a>
                    <a href="{{ route('edit') }}"
                       class="px-5 py-2 text-sm font-semibold text-black bg-white hover:bg-brand-100 rounded-lg transition-all hover:shadow-lg hover:shadow-white/10 hover:-translate-y-0.5">
                        Get Started
                    </a>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button id="hamburger" aria-label="Toggle menu"
                class="md:hidden flex flex-col gap-1.5 p-2 rounded-lg hover:bg-white/5 transition-colors">
                <span class="ham-line block w-5 h-0.5 bg-white transition-all duration-300"></span>
                <span class="ham-line block w-5 h-0.5 bg-white transition-all duration-300"></span>
                <span class="ham-line block w-3.5 h-0.5 bg-white transition-all duration-300"></span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="md:hidden border-t border-white/5 bg-brand-950/95 backdrop-blur-xl">
        <div class="px-4 py-4 flex flex-col gap-1">
            <a href="#features"  class="px-4 py-3 text-sm font-medium text-brand-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">Features</a>
            <a href="#preview"   class="px-4 py-3 text-sm font-medium text-brand-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">Preview</a>
            <a href="#how"       class="px-4 py-3 text-sm font-medium text-brand-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">How It Works</a>
            <a href="#pricing"   class="px-4 py-3 text-sm font-medium text-brand-300 hover:text-white hover:bg-white/5 rounded-xl transition-all">Pricing</a>
            <div class="flex gap-2 pt-3 mt-1 border-t border-white/5">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-semibold text-white bg-white/10 border border-white/10 rounded-xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"    class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-white border border-white/10 rounded-xl">Login</a>
                    <a href="{{ route('edit') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-semibold text-black bg-white rounded-xl">Get Started</a>
                @endauth
            </div>
        </div>
    </div>
</header>


{{-- ═══════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════ --}}
<section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden noise"
         style="background: linear-gradient(160deg, #0a0a0a 0%, #171717 50%, #0f0f0f 100%);">

    {{-- Grid background --}}
    <div class="absolute inset-0 grid-bg opacity-100"></div>

    {{-- Ambient orbs --}}
    <div class="orb w-[500px] h-[500px] top-[-100px] left-[-150px] opacity-[0.07]"
         style="background: radial-gradient(circle, #ffffff, transparent); animation: float 10s ease-in-out infinite;"></div>
    <div class="orb w-[400px] h-[400px] bottom-[-80px] right-[-100px] opacity-[0.05]"
         style="background: radial-gradient(circle, #ffffff, transparent); animation: float 13s ease-in-out infinite reverse;"></div>
    <div class="orb w-[300px] h-[300px] top-[40%] left-[60%] opacity-[0.04]"
         style="background: radial-gradient(circle, #a8a8a8, transparent); animation: float 8s ease-in-out infinite;"></div>

    {{-- Radial vignette --}}
    <div class="absolute inset-0 pointer-events-none"
         style="background: radial-gradient(ellipse 80% 60% at 50% 40%, transparent 0%, rgba(10,10,10,0.6) 100%);"></div>

    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 text-center pt-24 pb-16">

        {{-- Badge --}}
        <div class="anim-fill animate-fade-up delay-100 inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-8
                    bg-white/5 border border-white/10 text-brand-400 text-sm font-medium">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
            </svg>
            <span>AI-Powered News Automation Platform</span>
        </div>

        {{-- Headline --}}
        <h1 class="anim-fill animate-fade-up delay-200 text-5xl sm:text-6xl lg:text-[72px] font-black text-white mb-6 leading-[1.04] tracking-tight">
            Automate Your News
            <span class="block text-transparent" style="
                background: linear-gradient(135deg, #ffffff 0%, #a8a8a8 50%, #525252 100%);
                -webkit-background-clip: text;
                background-clip: text;">
                Creation with AI
            </span>
        </h1>

        {{-- Subheading --}}
        <p class="anim-fill animate-fade-up delay-300 text-lg sm:text-xl text-brand-400 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
            Generate, manage, and publish news articles instantly using AI-powered automation.
            <br class="hidden sm:block" />
            Save <strong class="text-white font-semibold">90% of your editorial time</strong> while maintaining quality.
        </p>

        {{-- CTA Buttons --}}
        <div class="anim-fill animate-fade-up delay-400 flex flex-col sm:flex-row gap-4 justify-center mb-16">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-black font-semibold rounded-xl
                          hover:bg-brand-100 hover:shadow-2xl hover:shadow-white/10 transition-all hover:-translate-y-1 text-base">
                    Go to Dashboard
                    <svg class="group-hover:translate-x-1 transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            @else
                <a href="{{ route('edit') }}"
                   class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-black font-semibold rounded-xl
                          hover:bg-brand-100 hover:shadow-2xl hover:shadow-white/10 transition-all hover:-translate-y-1 text-base">
                    Get Started Free
                    <svg class="group-hover:translate-x-1 transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 text-white font-medium rounded-xl
                          border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-base">
                    Login to Dashboard
                </a>
            @endauth
        </div>

        {{-- Stats Row --}}
        <div class="anim-fill animate-fade-up delay-500 grid grid-cols-3 gap-6 max-w-md mx-auto mb-16">
            @foreach([['10K+','Articles Generated'],['98%','Accuracy Rate'],['< 5s','Generation Time']] as [$val,$label])
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-black text-white stat-val">{{ $val }}</div>
                <div class="text-xs text-brand-500 mt-1 font-medium tracking-wide">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- Hero Mock Preview Card --}}
        <div class="anim-fill animate-fade-up delay-600 relative mx-auto max-w-3xl">
            {{-- Glow --}}
            <div class="absolute -inset-px rounded-2xl opacity-30 pointer-events-none"
                 style="background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent, rgba(255,255,255,0.05)); filter: blur(1px);"></div>

            {{-- Browser chrome --}}
            <div class="rounded-2xl overflow-hidden border border-white/10 shadow-2xl shadow-black/60">
                <div class="flex items-center gap-2 px-4 py-3 bg-brand-900 border-b border-white/5">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                        <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                        <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                    </div>
                    <div class="flex-1 mx-3 bg-brand-800 rounded-md px-3 py-1 text-xs text-brand-500 text-center border border-white/5">
                        newsauto.app/dashboard
                    </div>
                </div>

                {{-- Mini dashboard inside hero --}}
                <div class="bg-brand-950 flex" style="min-height: 240px;">
                    {{-- Sidebar --}}
                    <div class="w-36 bg-brand-900 border-r border-white/5 p-2 flex flex-col gap-0.5">
                        @foreach(['Dashboard','News','Generate','Images','Settings'] as $i => $item)
                        <div class="px-3 py-2 rounded-lg text-xs font-medium {{ $i === 0 ? 'bg-white text-black' : 'text-brand-500 hover:text-white' }}">
                            {{ $item }}
                        </div>
                        @endforeach
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 p-4">
                        {{-- Stat cards --}}
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            @foreach([['Total News','1,284'],['Generated Today','47'],['API Usage','89%']] as [$lbl,$val])
                            <div class="bg-brand-900 border border-white/5 rounded-xl p-3">
                                <div class="text-xs text-brand-500 mb-1">{{ $lbl }}</div>
                                <div class="text-lg font-black text-white">{{ $val }}</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Table preview --}}
                        <div class="bg-brand-900 border border-white/5 rounded-xl overflow-hidden">
                            <div class="px-3 py-2 border-b border-white/5 flex justify-between items-center">
                                <span class="text-xs font-semibold text-white">Recent Articles</span>
                                <span class="text-xs text-brand-500">View all →</span>
                            </div>
                            @foreach([
                                ['AI Breakthrough Changes Healthcare','Technology','published'],
                                ['Global Markets Hit Record Highs','Business','generated'],
                                ['Scientists Discover Exoplanet','Science','published'],
                            ] as [$title,$cat,$status])
                            <div class="flex items-center gap-2 px-3 py-2 border-b border-white/[0.03] last:border-0">
                                <div class="w-7 h-7 rounded-md bg-brand-800 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-brand-200 truncate">{{ $title }}</div>
                                    <div class="text-xs text-brand-600">{{ $cat }}</div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $status === 'published' ? 'badge-published' : 'badge-generated' }}">
                                    {{ $status }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-brand-600">
        <span class="text-xs tracking-widest uppercase font-medium">Scroll</span>
        <div class="w-5 h-8 border border-brand-700 rounded-full flex justify-center pt-1.5">
            <div class="w-1 h-1.5 bg-brand-500 rounded-full animate-scroll"></div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     TRUSTED BY SECTION
═══════════════════════════════════════════════ --}}
<section class="py-14 bg-brand-50 border-y border-brand-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <p class="text-center text-xs font-semibold text-brand-400 uppercase tracking-widest mb-8">Trusted by leading publishers worldwide</p>
        <div class="flex flex-wrap items-center justify-center gap-10 opacity-40">
            @foreach(['TechCrunch','Reuters','Bloomberg','The Guardian','Axios','Wired'] as $brand)
            <span class="text-brand-700 font-bold text-lg tracking-tight">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FEATURES SECTION
═══════════════════════════════════════════════ --}}
<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Section header --}}
        <div class="reveal text-center mb-16">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-brand-600 bg-brand-100 rounded-full uppercase tracking-widest mb-4">
                Features
            </span>
            <h2 class="text-4xl sm:text-5xl font-black text-brand-950 mb-4 leading-tight">
                Everything You Need
            </h2>
            <p class="text-lg text-brand-500 max-w-2xl mx-auto font-light">
                A complete suite of AI tools to transform your news production workflow from idea to published article.
            </p>
        </div>

        {{-- Feature cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            @php
            $features = [
                [
                    'icon' => '<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>',
                    'title' => 'AI News Generation',
                    'desc'  => 'Generate full, accurate news articles in seconds using state-of-the-art language models trained on millions of verified news sources.',
                    'tag'   => 'Core Feature',
                ],
                [
                    'icon' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
                    'title' => 'Auto Image Creation',
                    'desc'  => 'Automatically source and optimize relevant images for every article, saving hours of manual curation and licensing work.',
                    'tag'   => 'Automation',
                ],
                [
                    'icon' => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
                    'title' => 'Fast API Integration',
                    'desc'  => 'RESTful API with comprehensive documentation. Integrate with your existing CMS, WordPress, or custom platform in minutes.',
                    'tag'   => 'Developer Ready',
                ],
                [
                    'icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
                    'title' => 'SEO Optimized Output',
                    'desc'  => 'Every article ships with auto-generated meta tags, structured data markup, keyword density analysis, and readability scores.',
                    'tag'   => 'Growth',
                ],
                [
                    'icon' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
                    'title' => 'Real-time Processing',
                    'desc'  => 'Watch articles generate live with real-time progress streams, instant previews, and WebSocket-powered status updates.',
                    'tag'   => 'Performance',
                ],
                [
                    'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
                    'title' => 'Admin Dashboard',
                    'desc'  => 'Manage all content from a beautiful, intuitive dashboard with advanced filtering, bulk actions, and detailed analytics.',
                    'tag'   => 'Management',
                ],
            ];
            @endphp

            @foreach($features as $i => $feat)
            <div class="reveal reveal-delay-{{ min($i + 1, 5) }} card-lift group bg-white rounded-2xl p-7 border border-brand-200 hover:border-brand-400 cursor-default">
                {{-- Icon --}}
                <div class="icon-ring w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        {!! $feat['icon'] !!}
                    </svg>
                </div>

                {{-- Tag --}}
                <span class="inline-block text-xs font-semibold text-brand-400 bg-brand-50 border border-brand-200 px-2 py-0.5 rounded-md mb-3">
                    {{ $feat['tag'] }}
                </span>

                <h3 class="font-bold text-brand-900 text-lg mb-2">{{ $feat['title'] }}</h3>
                <p class="text-brand-500 text-sm leading-relaxed">{{ $feat['desc'] }}</p>

                {{-- Arrow --}}
                <div class="mt-5 flex items-center gap-1.5 text-brand-800 text-sm font-semibold opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all duration-200">
                    Learn more
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     DASHBOARD PREVIEW SECTION
═══════════════════════════════════════════════ --}}
<section id="preview" class="py-24 bg-brand-950 overflow-hidden noise relative"
         style="background: linear-gradient(180deg, #0a0a0a 0%, #111111 100%);">

    <div class="absolute inset-0 grid-bg opacity-100 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="reveal text-center mb-14">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-brand-400 bg-white/5 border border-white/10 rounded-full uppercase tracking-widest mb-4">
                Dashboard Preview
            </span>
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-4 leading-tight">
                Powerful Management Interface
            </h2>
            <p class="text-lg text-brand-400 max-w-2xl mx-auto font-light">
                Everything at your fingertips — clean, fast, and built for editorial teams.
            </p>
        </div>

        {{-- Full Dashboard Mock --}}
        <div class="reveal rounded-2xl overflow-hidden border border-white/8 shadow-2xl shadow-black/80">

            {{-- Browser chrome --}}
            <div class="bg-brand-900 px-5 py-3 flex items-center gap-3 border-b border-white/5">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                    <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                    <div class="w-3 h-3 rounded-full bg-brand-700"></div>
                </div>
                <div class="flex-1 mx-4 bg-brand-800 border border-white/5 rounded-lg px-4 py-1.5 text-xs text-brand-500 text-center">
                    newsauto.app/dashboard
                </div>
                <div class="flex gap-2">
                    <div class="w-6 h-4 bg-brand-800 rounded"></div>
                    <div class="w-6 h-4 bg-brand-800 rounded"></div>
                </div>
            </div>

            {{-- Dashboard body --}}
            <div class="flex bg-brand-950" style="min-height: 500px;">

                {{-- Sidebar --}}
                <div class="w-52 bg-brand-900 border-r border-white/5 flex flex-col p-3 gap-0.5 flex-shrink-0">
                    {{-- Logo --}}
                    <div class="flex items-center gap-2 px-3 py-3 mb-2">
                        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0a0a0a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                        </div>
                        <span class="font-bold text-white text-sm">News<span class="text-brand-400">Auto</span></span>
                    </div>

                    @foreach([
                        ['M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z','Dashboard', true],
                        ['M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l6 6v12a2 2 0 0 1-2 2z','News Articles', false],
                        ['M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z','Generate', false],
                        ['M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z','Images', false],
                        ['M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z','Settings', false],
                    ] as [$path, $label, $active])
                    <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-medium {{ $active ? 'bg-white text-black' : 'text-brand-500 hover:text-white hover:bg-white/5' }} cursor-default">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $path }}"/>
                        </svg>
                        {{ $label }}
                    </div>
                    @endforeach

                    <div class="flex-1"></div>

                    {{-- User avatar --}}
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-white/5 mt-2">
                        <div class="w-6 h-6 rounded-full bg-brand-700 flex-shrink-0"></div>
                        <div>
                            <div class="text-xs font-medium text-white">Admin</div>
                            <div class="text-xs text-brand-600">admin@newsauto.app</div>
                        </div>
                    </div>
                </div>

                {{-- Main panel --}}
                <div class="flex-1 flex flex-col overflow-hidden">

                    {{-- Top bar --}}
                    <div class="flex items-center justify-between px-6 py-3 border-b border-white/5 bg-brand-950">
                        <div>
                            <div class="text-sm font-bold text-white">Dashboard</div>
                            <div class="text-xs text-brand-600">Welcome back! Here's your overview.</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-brand-900 border border-white/5 rounded-lg text-xs text-brand-400">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                Search articles...
                            </div>
                            <div class="px-3 py-1.5 bg-white text-black text-xs font-semibold rounded-lg cursor-default">
                                + Generate
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 p-5 overflow-auto">

                        {{-- Stats --}}
                        <div class="grid grid-cols-4 gap-3 mb-5">
                            @foreach([
                                ['Total Articles','1,284','M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l6 6v12a2 2 0 0 1-2 2z'],
                                ['Generated Today','47','M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z'],
                                ['Published','891','M22 11.08V12a10 10 0 1 1-5.93-9.14'],
                                ['API Tokens','48.2K','M13 2L3 14h9l-1 8 10-12h-9l1-8z'],
                            ] as [$lbl,$val,$path])
                            <div class="bg-brand-900 border border-white/5 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-brand-500">{{ $lbl }}</span>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#525252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                                </div>
                                <div class="text-xl font-black text-white">{{ $val }}</div>
                                <div class="text-xs text-brand-600 mt-0.5">↑ 12% this week</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Table --}}
                        <div class="bg-brand-900 border border-white/5 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                                <span class="text-sm font-semibold text-white">Recent Articles</span>
                                <div class="flex gap-2">
                                    <div class="px-2 py-1 bg-brand-800 border border-white/5 rounded-lg text-xs text-brand-400">All Status ▾</div>
                                    <div class="px-2 py-1 bg-brand-800 border border-white/5 rounded-lg text-xs text-brand-400">Export</div>
                                </div>
                            </div>

                            {{-- Table header --}}
                            <div class="grid grid-cols-12 gap-2 px-4 py-2 bg-brand-950 border-b border-white/5">
                                <div class="col-span-6 text-xs font-semibold text-brand-600 uppercase tracking-wider">Article</div>
                                <div class="col-span-2 text-xs font-semibold text-brand-600 uppercase tracking-wider">Category</div>
                                <div class="col-span-2 text-xs font-semibold text-brand-600 uppercase tracking-wider">Status</div>
                                <div class="col-span-2 text-xs font-semibold text-brand-600 uppercase tracking-wider">Date</div>
                            </div>

                            @foreach([
                                ['AI Breakthrough Changes Healthcare Forever','Technology','published','Jan 15, 2025'],
                                ['Global Markets Hit Record Highs Amid Tech Rally','Business','generated','Jan 15, 2025'],
                                ['Scientists Discover New Earth-Like Exoplanet','Science','published','Jan 14, 2025'],
                                ['New Quantum Computer Solves Unsolvable Problems','Technology','pending','Jan 14, 2025'],
                                ['Tesla Announces Revolutionary Solid-State Battery','Business','published','Jan 13, 2025'],
                            ] as [$title,$cat,$status,$date])
                            <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/[0.03] last:border-0 items-center hover:bg-white/[0.02] cursor-default">
                                <div class="col-span-6 flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-brand-800 flex-shrink-0"></div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold text-brand-200 truncate">{{ $title }}</div>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-xs text-brand-500 bg-brand-800 px-2 py-0.5 rounded-md">{{ $cat }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $status === 'published' ? 'badge-published' : ($status === 'generated' ? 'badge-generated' : 'badge-pending') }}">
                                        {{ $status }}
                                    </span>
                                </div>
                                <div class="col-span-2 text-xs text-brand-600">{{ $date }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════ --}}
<section id="how" class="py-24 bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="reveal text-center mb-16">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-brand-600 bg-brand-100 border border-brand-200 rounded-full uppercase tracking-widest mb-4">
                Process
            </span>
            <h2 class="text-4xl sm:text-5xl font-black text-brand-950 mb-4">How It Works</h2>
            <p class="text-lg text-brand-500 max-w-xl mx-auto font-light">Four simple steps from idea to published article.</p>
        </div>

        {{-- Steps --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $steps = [
                ['01','Enter Topic',         'Provide a keyword, topic, or let AI automatically identify trending subjects in your niche.',         'M12 20h9','M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'],
                ['02','AI Generates News',   'Our AI writes a complete, accurate, and engaging news article with proper structure in under 10 seconds.','M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z',''],
                ['03','Auto Image Creation', 'Relevant images are automatically sourced, licensed, and optimized for web performance.',               'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z',''],
                ['04','Publish or Export',   'One-click publish to your CMS, or export as JSON, HTML, Markdown, or WordPress XML.',                  'M22 11.08V12a10 10 0 1 1-5.93-9.14','M9 11l3 3L22 4'],
            ];
            @endphp

            @foreach($steps as $i => [$num, $title, $desc, $path1, $path2])
            <div class="reveal reveal-delay-{{ $i + 1 }} relative">
                {{-- Connector (desktop) --}}
                @if($i < 3)
                <div class="hidden lg:block absolute top-8 left-[calc(50%+32px)] right-[-50%] h-px bg-gradient-to-r from-brand-300 to-brand-200 z-0"></div>
                @endif

                <div class="relative z-10 flex flex-col items-center text-center group">
                    {{-- Step number circle --}}
                    <div class="w-16 h-16 rounded-2xl bg-brand-900 flex items-center justify-center mb-5 shadow-lg
                                group-hover:bg-brand-800 transition-colors duration-200 border border-brand-800">
                        <span class="text-white font-black text-xl">{{ $num }}</span>
                    </div>

                    {{-- Check --}}
                    <div class="flex items-center gap-1.5 mb-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <h3 class="font-bold text-brand-900 text-base">{{ $title }}</h3>
                    </div>
                    <p class="text-sm text-brand-500 leading-relaxed">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     PRICING SECTION
═══════════════════════════════════════════════ --}}
<section id="pricing" class="py-24 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="reveal text-center mb-14">
            <span class="inline-block px-3 py-1 text-xs font-semibold text-brand-600 bg-brand-100 rounded-full uppercase tracking-widest mb-4">Pricing</span>
            <h2 class="text-4xl sm:text-5xl font-black text-brand-950 mb-4">Simple, Transparent Pricing</h2>
            <p class="text-lg text-brand-500 max-w-xl mx-auto font-light">Start free. Scale as you grow. No hidden fees.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $plans = [
                ['Starter','Free','0','For individuals getting started',['50 articles/month','Basic AI generation','API access (100 req/day)','Email support'],false],
                ['Professional','$49','/mo','For growing editorial teams',['Unlimited articles','Advanced AI models','Priority API access','Image auto-creation','SEO optimization','Chat support'],true],
                ['Enterprise','Custom','','For large publishers & agencies',['Everything in Pro','Dedicated infrastructure','Custom AI fine-tuning','SLA guarantee','Dedicated account manager'],false],
            ];
            @endphp

            @foreach($plans as [$name,$price,$period,$tagline,$feats,$popular])
            <div class="reveal reveal-delay-{{ $loop->index + 1 }} card-lift relative rounded-2xl border
                        {{ $popular ? 'bg-brand-950 border-brand-700 shadow-2xl shadow-black/20' : 'bg-white border-brand-200' }} p-7 flex flex-col">

                @if($popular)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-white text-brand-900 text-xs font-bold rounded-full border border-brand-200 shadow-sm">
                    Most Popular
                </div>
                @endif

                <div class="mb-6">
                    <div class="text-xs font-semibold {{ $popular ? 'text-brand-400' : 'text-brand-500' }} uppercase tracking-widest mb-2">{{ $name }}</div>
                    <div class="flex items-end gap-1 mb-1">
                        <span class="text-4xl font-black {{ $popular ? 'text-white' : 'text-brand-950' }}">{{ $price }}</span>
                        @if($period)
                        <span class="{{ $popular ? 'text-brand-400' : 'text-brand-500' }} text-sm mb-1">{{ $period }}</span>
                        @endif
                    </div>
                    <p class="text-sm {{ $popular ? 'text-brand-400' : 'text-brand-500' }}">{{ $tagline }}</p>
                </div>

                <ul class="flex flex-col gap-3 mb-8 flex-1">
                    @foreach($feats as $feat)
                    <li class="flex items-center gap-2.5 text-sm {{ $popular ? 'text-brand-300' : 'text-brand-600' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $popular ? '#a8a8a8' : '#525252' }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>

                @auth
                <a href="{{ route('dashboard') }}"
                   class="block text-center px-5 py-3 rounded-xl text-sm font-semibold transition-all
                          {{ $popular ? 'bg-white text-black hover:bg-brand-100' : 'bg-brand-950 text-white hover:bg-brand-800 border border-brand-800' }}">
                    Go to Dashboard
                </a>
                @else
                <a href="{{ route('edit') }}"
                   class="block text-center px-5 py-3 rounded-xl text-sm font-semibold transition-all
                          {{ $popular ? 'bg-white text-black hover:bg-brand-100' : 'bg-brand-950 text-white hover:bg-brand-800 border border-brand-800' }}">
                    Get Started
                </a>
                @endauth
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     CTA BANNER
═══════════════════════════════════════════════ --}}
<section class="py-20 bg-brand-950 relative overflow-hidden noise">
    <div class="absolute inset-0 grid-bg opacity-100 pointer-events-none"></div>
    <div class="orb w-96 h-96 top-[-80px] left-[-80px] opacity-[0.06]"
         style="background: radial-gradient(circle, #ffffff, transparent);"></div>
    <div class="orb w-64 h-64 bottom-[-40px] right-[-40px] opacity-[0.05]"
         style="background: radial-gradient(circle, #ffffff, transparent);"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="reveal">
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-5 leading-tight">
                Ready to Automate<br/>Your Newsroom?
            </h2>
            <p class="text-lg text-brand-400 mb-10 max-w-xl mx-auto font-light">
                Join thousands of publishers using NewsAuto to produce high-quality content at scale — faster than ever before.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                <a href="{{ route('dashboard') }}"
                   class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-brand-100 hover:shadow-2xl hover:shadow-white/10 transition-all hover:-translate-y-1 text-base">
                    Open Dashboard
                    <svg class="group-hover:translate-x-1 transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                @else
                <a href="{{ route('edit') }}"
                   class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-brand-100 hover:shadow-2xl hover:shadow-white/10 transition-all hover:-translate-y-1 text-base">
                    Start Free Today
                    <svg class="group-hover:translate-x-1 transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 text-white font-medium rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-base">
                    Login
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════ --}}
<footer class="bg-brand-950 border-t border-white/5 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Top row --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mb-12">

            {{-- Brand --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0a0a0a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-lg">News<span class="text-brand-400">Auto</span></span>
                </div>
                <p class="text-brand-500 text-sm leading-relaxed max-w-xs mb-6">
                    AI-powered news automation for modern publishers. Generate, manage, and publish at scale.
                </p>
                {{-- Social --}}
                <div class="flex gap-3">
                    @foreach([
                        ['Twitter','M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z'],
                        ['GitHub','M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22'],
                        ['LinkedIn','M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z'],
                    ] as [$name, $path])
                    <a href="#" aria-label="{{ $name }}"
                       class="w-8 h-8 bg-brand-800 hover:bg-brand-700 border border-white/5 text-brand-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $path }}"/>
                            @if($name === 'LinkedIn')
                            <rect x="2" y="2" width="4" height="4" rx="1"/>
                            @endif
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Links --}}
            @foreach([
                ['Product',  ['Features','API Docs','Changelog','Roadmap','Status']],
                ['Company',  ['About','Blog','Careers','Press','Contact']],
                ['Legal',    ['Privacy Policy','Terms of Service','Cookie Policy','GDPR']],
            ] as [$heading, $links])
            <div>
                <div class="text-xs font-semibold text-brand-500 uppercase tracking-widest mb-4">{{ $heading }}</div>
                <ul class="flex flex-col gap-2.5">
                    @foreach($links as $link)
                    <li>
                        <a href="#" class="text-sm text-brand-400 hover:text-white transition-colors">{{ $link }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="border-t border-white/5 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-brand-600 text-sm">
                &copy; {{ date('Y') }} NewsAuto. All rights reserved.
            </p>
            <div class="flex items-center gap-2 text-brand-600 text-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse-slow inline-block"></span>
                All systems operational
            </div>
        </div>
    </div>
</footer>


{{-- ═══════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════ --}}
<script>
    // ── Navbar scroll effect ──
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    }, { passive: true });

    // ── Mobile menu toggle ──
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    let menuOpen = false;

    hamburger.addEventListener('click', () => {
        menuOpen = !menuOpen;
        mobileMenu.classList.toggle('open', menuOpen);

        // Animate hamburger lines
        const lines = hamburger.querySelectorAll('.ham-line');
        if (menuOpen) {
            lines[0].style.transform = 'translateY(8px) rotate(45deg)';
            lines[1].style.opacity  = '0';
            lines[2].style.transform = 'translateY(-8px) rotate(-45deg)';
            lines[2].style.width = '20px';
        } else {
            lines[0].style.transform = '';
            lines[1].style.opacity  = '1';
            lines[2].style.transform = '';
            lines[2].style.width = '14px';
        }
    });

    // Close menu on link click
    mobileMenu.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            menuOpen = false;
            mobileMenu.classList.remove('open');
            const lines = hamburger.querySelectorAll('.ham-line');
            lines.forEach(l => { l.style.transform = ''; l.style.opacity = '1'; });
            lines[2].style.width = '14px';
        });
    });

    // ── Scroll reveal ──
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── Active nav link highlight on scroll ──
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('header nav a[href^="#"]');

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.remove('text-white');
                    link.classList.add('text-brand-400');
                    if (link.getAttribute('href') === '#' + entry.target.id) {
                        link.classList.add('text-white');
                        link.classList.remove('text-brand-400');
                    }
                });
            }
        });
    }, { threshold: 0.4 });

    sections.forEach(s => sectionObserver.observe(s));
</script>

</body>
</html>
