<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'S&D Finance')</title>
    
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
                        sidebar: '#2A1E17',      // Warna dark brown sidebar
                        sidebarHover: '#374b46', // Hover state sidebar
                        activeTab: '#e8ece9',    // Light grey/mint menyatu ke background
                        goldAccent: '#d4a338',   // Warna mustard gold aksen
                        bgMain: '#e8ece9',       // Background luar/body
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
        /* Style melengkung untuk menu aktif */
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
    </style>
</head>
<body class="min-h-screen p-4 md:p-6 flex flex-col items-center justify-center">

    <!-- WRAPPER UTAMA (SUPAYA HEADER ADA DI ATAS DASHBOARD) -->
    <div class="w-full max-w-[1440px] space-y-3">

        <!-- TOPBAR / BRANDING ZONE ATAS -->
        <header class="flex items-center justify-between px-2 py-1">
            <!-- LOGO FOTO & NAMA APP -->
            <div class="flex items-center gap-3">
                
                <div>
                    <h1 class="font-extrabold text-xl tracking-tight text-[#2A1E17]">S&D <span class="text-[#D8A749]">FINANCE</span></h1>
                    <p class="text-[11px] font-bold text-slate-400 tracking-widest uppercase">Internal Financial System</p>
                </div>
            </div>

            <!-- TANGGAL OTOMATIS -->
            <div class="hidden sm:flex items-center gap-2 bg-white/60 backdrop-blur px-4 py-2 rounded-2xl border border-slate-200/60 shadow-sm">
                <span class="text-xs font-bold text-slate-600">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            </div>
        </header>

        <!-- CONTAINER SIDEBAR & KONTEN -->
        <div class="w-full bg-bgMain rounded-[2.5rem] shadow-2xl overflow-hidden flex min-h-[85vh]">

            <!-- SIDEBAR -->
            <aside class="w-64 bg-sidebar flex flex-col justify-between p-6 pr-0 rounded-l-[2.5rem] text-white shrink-0">
                <div>
                    <!-- Navigasi Menu Sidebar -->
                    <nav class="space-y-2 mt-4 text-xs font-semibold tracking-wider text-slate-300">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>DASHBOARD</span>
                        </a>

                        <!-- Rekening / Wallet -->
                        <a href="{{ route('rekening.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('rekening.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>REKENING & KAS</span>
                        </a>

                        <!-- Kategori (COA) -->
                        <a href="{{ route('kategori.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('kategori.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>KATEGORI COA</span>
                        </a>

                        <!-- Mutasi Transaksi -->
                        <a href="{{ route('mutasi.index') }}" 
                           class="flex items-center gap-3 px-6 py-3.5 transition-all duration-200 {{ request()->routeIs('mutasi.*') ? 'nav-active' : 'hover:text-white hover:bg-sidebarHover rounded-l-2xl' }}">
                            <span>MUTASI TRANSAKSI</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-6 md:p-8 bg-bgMain overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

</body>
</html>