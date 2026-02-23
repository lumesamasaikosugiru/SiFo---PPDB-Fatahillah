<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'PPDB Online - Yayasan Fatahillah')</title>
  <link rel="icon" href="{{ asset('assets/images/favicon-yayasan-1.jpeg') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  {{-- Swiper (untuk slider testimoni) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  {{-- Flatpickr (datepicker) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50:  '#f0faf8',
              100: '#d9f2ee',
              200: '#b3e5de',
              300: '#7BBCAF',
              400: '#5aa99a',
              500: '#3d9080',
              600: '#2d7268',
              700: '#225a52',
              800: '#1a4540',
              900: '#122e2b',
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .gradient-hero {
      background: linear-gradient(135deg, #0f4c3a 0%, #1a6b55 30%, #7BBCAF 70%, #a8d8d0 100%);
    }
    .glass-card {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .nav-link { position: relative; }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 0;
      height: 2px;
      background: #7BBCAF;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after { width: 100%; }
    .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(123,188,175,0.25); }
    .animate-float { animation: float 3s ease-in-out infinite; }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #7BBCAF; border-radius: 3px; }
    /* Swiper custom */
    .swiper-pagination-bullet-active { background: #3d9080 !important; }
    .swiper-button-next, .swiper-button-prev { color: #3d9080 !important; }
    /* Flatpickr override */
    .flatpickr-calendar { font-family: 'Inter', sans-serif; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); }
    .flatpickr-day.selected { background: #3d9080; border-color: #3d9080; }
    .flatpickr-day.selected:hover { background: #2d7268; border-color: #2d7268; }
    /* Gender radio buttons */
    .gender-btn input[type="radio"] { display: none; }
    .gender-btn label {
      display: flex; flex-direction: column; align-items: center; gap: 6px;
      border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px 20px;
      cursor: pointer; transition: all 0.2s; font-size: 13px; color: #6b7280;
    }
    .gender-btn input:checked + label {
      border-color: #3d9080; background: #f0faf8; color: #225a52; font-weight: 600;
    }
  </style>
  @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

  <!-- Navbar -->
  <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 py-4">
    <div class="max-w-7xl mx-auto px-6">
      <div class="glass-card rounded-2xl px-6 py-3 flex items-center justify-between shadow-lg border border-white/50">
        
        <!-- Logo Yayasan -->
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md group-hover:scale-110 transition-transform flex-shrink-0">
            <img src="{{ asset('assets/images/logo-Yayasan Fatahillah.jpeg') }}" alt="Logo Yayasan Fatahillah"
                 class="w-full h-full object-contain"
                 onerror="this.onerror=null;this.style.display='none';this.parentElement.innerHTML='<div class=\'w-10 h-10 rounded-xl bg-gradient-to-br from-primary-300 to-primary-600 flex items-center justify-center shadow-md\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'2\'><path d=\'M12 14l9-5-9-5-9 5 9 5z\'/></svg></div>'">
          </div>
          <div>
            <span class="font-bold text-gray-800 text-lg leading-none block">Yayasan Fatahillah</span>
            <span class="text-xs text-primary-500 font-medium">PPDB Online 2026/2027</span>
          </div>
        </a>

        <!-- Desktop Nav -->
        <ul class="hidden md:flex items-center gap-8">
          <li><a href="{{ url('/') }}" class="nav-link text-gray-700 font-medium hover:text-primary-500 transition-colors text-sm">Beranda</a></li>
          <li><a href="{{ url('/#sekolah') }}" class="nav-link text-gray-700 font-medium hover:text-primary-500 transition-colors text-sm">Sekolah</a></li>
          <li><a href="{{ url('/#alur') }}" class="nav-link text-gray-700 font-medium hover:text-primary-500 transition-colors text-sm">Alur Daftar</a></li>
          <li><a href="{{ route('daftar.create') }}" class="nav-link text-gray-700 font-medium hover:text-primary-500 transition-colors text-sm">Pendaftaran</a></li>
          <!-- <li><a href="{{ route('status.index') }}" class="nav-link text-gray-700 font-medium hover:text-primary-500 transition-colors text-sm">Cek Status</a></li> -->
        </ul>

        <!-- CTA Button -->
        <div class="hidden md:flex items-center gap-3">
          <a href="{{ route('status.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors px-4 py-2 rounded-lg hover:bg-primary-50">
            Cek Status
          </a>
          <a href="{{ route('daftar.create') }}" class="text-sm font-semibold bg-gradient-to-r from-primary-400 to-primary-600 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all">
            Daftar Sekarang
          </a>
        </div>

        <!-- Mobile Toggle -->
        <button id="mobile-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
          <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div id="mobile-menu" class="hidden mt-2 glass-card rounded-2xl px-6 py-4 shadow-lg border border-white/50">
        <ul class="flex flex-col gap-3">
          <li><a href="{{ url('/') }}" class="block py-2 text-gray-700 font-medium hover:text-primary-500 transition-colors">Beranda</a></li>
          <li><a href="{{ url('/#sekolah') }}" class="block py-2 text-gray-700 font-medium hover:text-primary-500 transition-colors">Sekolah</a></li>
          <li><a href="{{ url('/#alur') }}" class="block py-2 text-gray-700 font-medium hover:text-primary-500 transition-colors">Alur Daftar</a></li>
          <li><a href="{{ route('daftar.create') }}" class="block py-2 text-gray-700 font-medium hover:text-primary-500 transition-colors">Pendaftaran</a></li>
          <li><a href="{{ route('status.index') }}" class="block py-2 text-gray-700 font-medium hover:text-primary-500 transition-colors">Cek Status</a></li>
          <li class="pt-2 border-t border-gray-100">
            <a href="{{ route('daftar.create') }}" class="block text-center bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold py-2.5 rounded-xl">
              Daftar Sekarang
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main>
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

        <!-- Brand -->
        <div class="md:col-span-2">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl overflow-hidden">
              <img src="{{ asset('assets/images/logo-yayasan.jpeg') }}" alt="Logo Yayasan" class="w-full h-full object-contain"
                   onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'w-10 h-10 rounded-xl bg-gradient-to-br from-primary-300 to-primary-600 flex items-center justify-center\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'2\'><path d=\'M12 14l9-5-9-5-9 5 9 5z\'/></svg></div>'">
            </div>
            <div>
              <span class="font-bold text-white text-lg">Yayasan Fatahillah</span>
              <span class="block text-xs text-primary-400">PPDB Online 2026/2027</span>
            </div>
          </div>
          <p class="text-sm text-gray-400 leading-relaxed max-w-sm">
            Sistem Penerimaan Peserta Didik Baru Online untuk SMK dan SMP di bawah naungan Yayasan Fatahillah. Mendidik generasi unggul untuk masa depan bangsa.
          </p>
          <div class="flex gap-3 mt-5">
            <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-600 flex items-center justify-center transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-primary-600 flex items-center justify-center transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
          </div>
        </div>

        <!-- Links -->
        <div>
          <h4 class="font-semibold text-white mb-4">Navigasi</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="{{ url('/') }}" class="hover:text-primary-400 transition-colors">Beranda</a></li>
            <li><a href="{{ url('/#sekolah') }}" class="hover:text-primary-400 transition-colors">Pilih Sekolah</a></li>
            <li><a href="{{ url('/#alur') }}" class="hover:text-primary-400 transition-colors">Alur Pendaftaran</a></li>
            <li><a href="{{ route('daftar.create') }}" class="hover:text-primary-400 transition-colors">Pendaftaran</a></li>
            <li><a href="{{ route('status.index') }}" class="hover:text-primary-400 transition-colors">Cek Status</a></li>
          </ul>
        </div>

        <!-- Kontak -->
        <div>
          <h4 class="font-semibold text-white mb-4">Kontak Kami</h4>
          <ul class="space-y-3 text-sm">
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-primary-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>Jl. Fatahillah No.1, Cilegon, Banten</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span>info@fatahillah.sch.id</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span>(0254) 123-4567</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Senin–Jumat: 08.00–16.00 WIB</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-gray-500">
        <p>&copy; {{ date('Y') }} Yayasan Fatahillah. Semua hak cipta dilindungi.</p>
        <p>Dibuat dengan ❤️ untuk pendidikan Indonesia</p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobile-toggle');
    const mobileMenu   = document.getElementById('mobile-menu');
    const iconOpen     = document.getElementById('icon-open');
    const iconClose    = document.getElementById('icon-close');

    mobileToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
      iconOpen.classList.toggle('hidden');
      iconClose.classList.toggle('hidden');
    });

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        navbar.classList.add('py-2');
        navbar.classList.remove('py-4');
      } else {
        navbar.classList.add('py-4');
        navbar.classList.remove('py-2');
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
