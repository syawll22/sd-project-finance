<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'S&D Finance')</title>
    {{-- <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"> --}}
 <!-- PWA Settings -->
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0F172A">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => console.log('Service Worker Registered!', reg))
            .catch((err) => console.log('Service Worker Error!', err));
    }
</script>
    <!-- Tailwind CSS CDN & Custom Font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        sidebar: '#2A1E17',      
                        sidebarHover: '#374b46', 
                        activeTab: '#e8ece9',    
                        goldAccent: '#d4a338',   
                        bgMain: '#e8ece9',       
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #e8ece9;
        }
       /* style untuk menu aktif */
        @media (min-width: 1024px) {
            .nav-active {
                background-color: #e8ece9;
                color: #2c3e3a !important;
                font-weight: 700;
                border-top-left-radius: 1.5rem;
                border-bottom-left-radius: 1.5rem;
                position: relative;
            }
            .nav-active::before {
                content: '';
                position: absolute;
                top: -20px;
                right: 0;
                width: 20px;
                height: 20px;
                border-bottom-right-radius: 20px;
                box-shadow: 5px 5px 0 5px #e8ece9;
            }
            .nav-active::after {
                content: '';
                position: absolute;
                bottom: -20px;
                right: 0;
                width: 20px;
                height: 20px;
                border-top-right-radius: 20px;
                box-shadow: 5px -5px 0 5px #e8ece9;
            }
        }
        /* Style active menu versi Mobile */
        @media (max-width: 1023px) {
            .nav-active {
                background-color: #d4a338;
                color: #2A1E17 !important;
                font-weight: 700;
                border-radius: 0.75rem;
            }
        }
    </style>
</head>
<body class="min-h-screen p-3 md:p-6 flex flex-col items-center justify-start lg:justify-center" x-data="{ mobileMenuOpen: false }">

    <div class="w-full max-w-[1440px] space-y-3">

        <!-- TOPBAR: HAMBURGER + LOGO (Sesuai Sketsa) -->
        <header class="flex items-center justify-between px-2 py-1">
            <div class="flex items-center gap-3">
                <!-- Tombol Hamburger (Hanya muncul di Mobile/Tablet) -->
                <button @click="mobileMenuOpen = true" class="lg:hidden p-2 rounded-xl bg-white shadow-sm border border-slate-200 text-slate-700 hover:bg-slate-50 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- BRAND LOGO -->
                <div>
                    <h1 class="font-extrabold text-lg sm:text-xl tracking-tight text-[#2A1E17]">S&D <span class="text-[#D8A749]">FINANCE</span></h1>
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 tracking-widest uppercase">Internal Financial System</p>
                </div>
            </div>

            <!-- TANGGAL OTOMATIS -->
            <div class="hidden sm:flex items-center gap-2 bg-white/70 backdrop-blur px-4 py-2 rounded-2xl border border-slate-200/60 shadow-sm text-xs font-bold text-slate-600">
                <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            </div>
        </header>

        <!-- CONTAINER UTAMA -->
        <div class="w-full bg-bgMain rounded-2xl lg:rounded-[2.5rem] lg:shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[85vh]">

            <!-- SIDEBAR DESKTOP (Tampil normal di laptop/komputer) -->
            <aside class="hidden lg:flex w-64 bg-sidebar flex-col justify-between p-6 pr-0 rounded-l-[2.5rem] text-white shrink-0">
                <div>
                    <nav class="space-y-2 mt-4 text-xs font-semibold tracking-wider text-slate-300">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>DASHBOARD</span>
                        </a>

                        <a href="{{ route('rekening.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('rekening.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>REKENING & KAS</span>
                        </a>

                        <a href="{{ route('kategori.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('kategori.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>KATEGORI COA</span>
                        </a>

                        <a href="{{ route('mutasi.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('mutasi.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>MUTASI TRANSAKSI</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-3 sm:p-6 lg:p-8 bg-bgMain overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- MOBILE DRAWER MENU (Samping Slide-over) -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         style="display: none;">

        <div @click.away="mobileMenuOpen = false" 
             class="w-72 bg-sidebar h-full p-6 text-white flex flex-col justify-between shadow-2xl">
            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-6 border-b border-white/10">
                    <div>
                        <h2 class="font-extrabold text-lg text-white">S&D <span class="text-[#D8A749]">FINANCE</span></h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Menu Navigasi</p>
                    </div>
                    <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Drawer Links -->
                <nav class="space-y-3 mt-6 text-xs font-semibold tracking-wider">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-3 transition-all {{ request()->routeIs('dashboard') ? 'nav-active' : 'text-slate-300 hover:bg-white/5 rounded-xl' }}">
                        <span>DASHBOARD</span>
                    </a>

                    <a href="{{ route('rekening.index') }}" 
                       class="flex items-center px-4 py-3 transition-all {{ request()->routeIs('rekening.*') ? 'nav-active' : 'text-slate-300 hover:bg-white/5 rounded-xl' }}">
                        <span>REKENING & KAS</span>
                    </a>

                    <a href="{{ route('kategori.index') }}" 
                       class="flex items-center px-4 py-3 transition-all {{ request()->routeIs('kategori.*') ? 'nav-active' : 'text-slate-300 hover:bg-white/5 rounded-xl' }}">
                        <span>KATEGORI COA</span>
                    </a>

                    <a href="{{ route('mutasi.index') }}" 
                       class="flex items-center px-4 py-3 transition-all {{ request()->routeIs('mutasi.*') ? 'nav-active' : 'text-slate-300 hover:bg-white/5 rounded-xl' }}">
                        <span>MUTASI TRANSAKSI</span>
                    </a>
                </nav>
            </div>

            <div class="text-[11px] text-slate-500 font-medium">
                © {{ date('Y') }} S&D Finance System
            </div>
        </div>
    </div>

</body>
</html>