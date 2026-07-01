<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Tiket Layanan Kominfo')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabler-icons@latest/iconfont/tabler-icons.min.css" />
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        iconify-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1em;
            height: 1em;
            line-height: 1;
        }

        iconify-icon[class*="fs-1"] {
            font-size: 3.5rem;
        }

        iconify-icon[class*="fs-2"] {
            font-size: 3rem;
        }

        iconify-icon[class*="fs-3"] {
            font-size: 2.25rem;
        }

        iconify-icon[class*="fs-4"] {
            font-size: 1.5rem;
        }

        iconify-icon[class*="fs-5"] {
            font-size: 1.25rem;
        }

        iconify-icon[class*="fs-6"] {
            font-size: 1rem;
        }

        .btn-sm iconify-icon {
            font-size: 1rem;
        }

        /* ==================================================
   SIDEBAR PREMIUM DISKOMINFO
   ================================================== */

.left-sidebar{
    background: linear-gradient(
        180deg,
        #172554 0%,
        #1e3a8a 45%,
        #2563eb 100%
    ) !important;

    box-shadow: 8px 0 30px rgba(0,0,0,.12);
}

/* container sidebar */
.left-sidebar > div{
    background: transparent !important;
}

/* ==========================================
   LOGO AREA
========================================== */

.brand-logo{
    background: rgba(255,255,255,.06) !important;
    backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(255,255,255,.12) !important;
    min-height: 100px;
}

.brand-logo h5{
    color: #ffffff !important;
    font-weight: 700;
    font-size: 15px;
    line-height: 1.3;
    margin-bottom: 0;
}

.brand-logo img{
    filter: drop-shadow(0 3px 10px rgba(255,255,255,.15));
}

/* ==========================================
   MENU SECTION TITLE
========================================== */

.nav-small-cap{
    padding: 14px 22px 8px !important;
    margin-top: 8px;
}

.nav-small-cap .hide-menu{
    color: rgba(255,255,255,.75) !important;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.nav-small-cap iconify-icon{
    color: rgba(255,255,255,.65) !important;
}

/* ==========================================
   MENU ITEM
========================================== */

.sidebar-item{
    margin-bottom: 6px;
}

.sidebar-link{
    position: relative;

    margin: 0 14px !important;
    padding: 12px 18px !important;

    border-radius: 16px;

    color: rgba(255,255,255,.92) !important;

    transition: all .25s ease;
}

.sidebar-link span{
    font-size: 15px;
    font-weight: 500;
}

.sidebar-link iconify-icon{
    color: rgba(255,255,255,.95) !important;
    font-size: 20px;
    margin-right: 12px;
}

/* hover */

.sidebar-link:hover{
    background: rgba(255,255,255,.10) !important;

    transform: translateX(5px);

    color: #ffffff !important;
}

/* ==========================================
   ACTIVE MENU
========================================== */

.sidebar-item.selected .sidebar-link,
.sidebar-item.active .sidebar-link{
    background: #ffffff !important;

    color: #1e40af !important;

    box-shadow:
        0 10px 30px rgba(0,0,0,.12);

    font-weight: 700;
}

.sidebar-item.selected .sidebar-link iconify-icon,
.sidebar-item.active .sidebar-link iconify-icon{
    color: #2563eb !important;
}

/* garis aktif kiri */

.sidebar-item.selected .sidebar-link::before,
.sidebar-item.active .sidebar-link::before{
    content: "";

    position: absolute;

    left: -14px;
    top: 50%;

    transform: translateY(-50%);

    width: 5px;
    height: 60%;

    background: #ffffff;

    border-radius: 20px;
}

/* ==========================================
   DIVIDER
========================================== */

.sidebar-divider{
    border-top: 1px solid rgba(255,255,255,.12) !important;
    margin: 18px 20px !important;
}

/* ==========================================
   FOOTER
========================================== */

.sidebar-footer{
    background: rgba(255,255,255,.06) !important;

    border-top: 1px solid rgba(255,255,255,.12) !important;

    backdrop-filter: blur(12px);
}

.sidebar-footer p{
    color: #ffffff !important;
}

/* ==========================================
   LOGOUT
========================================== */

#logout-form .sidebar-link{
    color: #ffffff !important;
}

#logout-form .sidebar-link:hover{
    background: rgba(255,255,255,.10) !important;
}

/* ==========================================
   SCROLLBAR
========================================== */

.simplebar-scrollbar:before{
    background: rgba(255,255,255,.35) !important;
}

/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:991px){

    .sidebar-link{
        margin: 0 10px !important;
    }

}

/* ==========================================
   CUSTOM SCROLLBAR SIDEBAR
========================================== */

/* Chrome, Edge, Safari */

.sidebar-nav::-webkit-scrollbar,
.simplebar-content-wrapper::-webkit-scrollbar{
    width: 8px;
}

.sidebar-nav::-webkit-scrollbar-track,
.simplebar-content-wrapper::-webkit-scrollbar-track{
    background: rgba(255,255,255,0.08);
    border-radius: 20px;
}

.sidebar-nav::-webkit-scrollbar-thumb,
.simplebar-content-wrapper::-webkit-scrollbar-thumb{
    background: linear-gradient(
        180deg,
        rgba(96,165,250,.9),
        rgba(37,99,235,.9)
    );

    border-radius: 20px;

    border: 2px solid transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover,
.simplebar-content-wrapper::-webkit-scrollbar-thumb:hover{
    background: linear-gradient(
        180deg,
        #93c5fd,
        #3b82f6
    );
}

/* ==========================================
   SIMPLEBAR STYLE
========================================== */

.simplebar-track.simplebar-vertical{
    width: 10px !important;
    right: 3px !important;
}

.simplebar-track.simplebar-vertical .simplebar-scrollbar:before{
    background: linear-gradient(
        180deg,
        #60a5fa,
        #2563eb
    ) !important;

    border-radius: 20px !important;

    opacity: .9 !important;
}

.simplebar-track.simplebar-vertical .simplebar-scrollbar:hover:before{
    background: linear-gradient(
        180deg,
        #93c5fd,
        #3b82f6
    ) !important;
}

.simplebar-track.simplebar-vertical .simplebar-scrollbar.simplebar-visible:before{
    opacity: 1 !important;
}

/* ==========================================
   SMOOTH SCROLL
========================================== */

.sidebar-nav{
    scroll-behavior: smooth;
}

/* ==========================================
   HIDE HORIZONTAL SCROLL
========================================== */

.sidebar-nav,
.simplebar-content-wrapper{
    overflow-x: hidden !important;
}


/* ==========================================
   HEADER PREMIUM BIRU DISKOMINFO
========================================== */

.app-header{
    background: linear-gradient(
        90deg,
        #172554 0%,
        #1e3a8a 50%,
        #2563eb 100%
    ) !important;

    border-bottom: none !important;

    box-shadow:
        0 4px 20px rgba(0,0,0,.15) !important;
}

/* Judul halaman */

.app-header h5{
    color: #ffffff !important;
    font-weight: 700;
    letter-spacing: .3px;
}

/* ==========================================
   NOTIFICATION BUTTON
========================================== */

#dropdownNotification{
    width: 42px;
    height: 42px;

    display: flex !important;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: rgba(255,255,255,.12);

    transition: .25s ease;
}

#dropdownNotification:hover{
    background: rgba(255,255,255,.20);
}

#dropdownNotification iconify-icon{
    color: #ffffff !important;
    font-size: 22px;
}

/* Badge */

#notification-count{
    border: 2px solid #2563eb;
}

/* ==========================================
   USER PROFILE
========================================== */

#dropdownUser{
    padding: 6px 12px;

    border-radius: 14px;

    transition: .25s ease;
}

#dropdownUser:hover{
    background: rgba(255,255,255,.12);
}

#dropdownUser h6{
    color: #ffffff !important;
}

#dropdownUser small{
    color: rgba(255,255,255,.75) !important;
}

#dropdownUser img{
    border: 2px solid rgba(255,255,255,.35);
}

/* ==========================================
   DROPDOWN
========================================== */

.dropdown-menu{
    border: none !important;

    border-radius: 16px !important;

    box-shadow:
        0 15px 35px rgba(0,0,0,.15);
}

.dropdown-item:hover{
    background: #eff6ff;
}

/* ==========================================
   MOBILE MENU
========================================== */

.sidebartoggler{
    color: #ffffff !important;
}

.sidebartoggler i{
    color: #ffffff !important;
}

/* ==========================================
   ICON HEADER
========================================== */

.app-header .nav-link{
    color: #ffffff !important;
}


    </style>
    @stack('styles')
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <aside class="left-sidebar"
            style="height: 100vh; overflow: hidden; position: fixed; top: 0; left: 0; z-index: 1000;">
            <div style="height: 100%; display: flex; flex-direction: column; background: #fff;">
                <div class="brand-logo d-flex align-items-center justify-content-between"
                    style="padding: 15px 20px 15px 20px; margin-bottom: 0; border-bottom: 1px solid #dee2e6;">
                    <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/images/logos/logo-kominfoo.png') }}" alt=""
                                style="height: 50px; width: auto;" />
                            <h5>Sistem Ticketing <br>Diskominfo</h5>
                        </div>
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>

                <div class="modal fade" id="exampleModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Modal Title
                                </h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                Halo Wahid 🚀
                                Ini isi modal Bootstrap di Laravel Blade.
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>

                                <button type="button" class="btn btn-primary">
                                    Save
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <nav class="sidebar-nav scroll-sidebar" data-simplebar=""
                    style="flex-grow: 1; overflow-y: auto; overflow-x: hidden;">
                    <ul id="sidebarnav" style="margin-bottom: 0; padding-bottom: 0;">
                        <li class="nav-small-cap">
                            <iconify-icon icon="mdi:dots-horizontal" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">Menu Utama {{ auth()->user()->role }}</span>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'selected' : '' }}">
                            <a class="sidebar-link primary-hover-bg" href="{{ route('dashboard') }}"
                                aria-expanded="false">
                                <iconify-icon icon="mdi:home"></iconify-icon>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        @can('view my tickets')
                        <li class="sidebar-item {{ request()->routeIs('tickets.my') ? 'selected' : '' }}">
                            <a class="sidebar-link primary-hover-bg" href="{{ route('tickets.my') }}"
                                aria-expanded="false">
                                <iconify-icon icon="mdi:ticket"></iconify-icon>
                                <span class="hide-menu">Tiket Saya</span>
                            </a>
                        </li>
                        @endcan

                        @can('create tickets')
                        <li class="sidebar-item {{ request()->routeIs('tickets.create') ? 'selected' : '' }}">
                            <a class="sidebar-link primary-hover-bg" href="{{ route('tickets.create') }}"
                                aria-expanded="false">
                                <iconify-icon icon="mdi:plus-circle"></iconify-icon>
                                <span class="hide-menu">Buat Tiket</span>
                            </a>
                        </li>
                        @endcan
                        @can('view tickets')
                        <li class="sidebar-item {{ request()->routeIs('tickets.index') ? 'selected' : '' }}">
                            <a class="sidebar-link primary-hover-bg" href="{{ route('tickets.index') }}"
                                aria-expanded="false">
                                <iconify-icon icon="mdi:ticket"></iconify-icon>
                                <span class="hide-menu">Semua Tiket</span>
                            </a>
                        </li>
                        @endcan

                        <li>
                            <span class="sidebar-divider lg"></span>
                        </li>
                        @can('view kategori')
                            <li class="nav-small-cap">
                                <iconify-icon icon="mdi:dots-horizontal" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">Manajemen AI</span>
                            </li>
                            <li class="sidebar-item {{ request()->routeIs('categories.index') ? 'selected' : '' }}">
                                <a class="sidebar-link primary-hover-bg" href="{{ route('categories.index') }}"
                                    aria-expanded="false">
                                    <iconify-icon icon="mdi:folder"></iconify-icon>
                                    <span class="hide-menu">Kategori</span>
                                </a>
                            </li>
                        @endcan

                        @can('view knowledge base')
                            <li class="sidebar-item {{ request()->routeIs('knowledge.index') ? 'selected' : '' }}">
                                <a class="sidebar-link primary-hover-bg" href="{{ route('knowledge.index') }}"
                                    aria-expanded="false">
                                    <iconify-icon icon="mdi:book-open-variant"></iconify-icon>
                                    <span class="hide-menu">Knowledge Base</span>
                                </a>
                            </li>
                        @endcan

                        @can('view log chatbot')
                            <li class="sidebar-item {{ request()->routeIs('chatbot.logs') ? 'selected' : '' }}">
                                <a class="sidebar-link primary-hover-bg" href="{{ route('chatbot.logs') }}"
                                    aria-expanded="false">
                                    <iconify-icon icon="mdi:chat"></iconify-icon>
                                    <span class="hide-menu">Riwayat Chatbot</span>
                                </a>
                            </li>
                        @endcan

                        @can('view pengguna')
                          
                        <li>
                            <span class="sidebar-divider lg"></span>
                        </li>
                        <li class="nav-small-cap">
                            <iconify-icon icon="mdi:dots-horizontal" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">Manajemen</span>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('users.index') ? 'selected' : '' }}">
                            <a class="sidebar-link primary-hover-bg" href="{{ route('users.index') }}"
                                aria-expanded="false">
                                <iconify-icon icon="mdi:account-group"></iconify-icon>
                                <span class="hide-menu">Pengguna</span>
                            </a>
                        </li>
                        @endcan

                        @can('view role permission')
                            <li class="sidebar-item {{ request()->routeIs('roles.index') ? 'selected' : '' }}">
                                <a class="sidebar-link primary-hover-bg" href="{{ route('roles.index') }}"
                                    aria-expanded="false">
                                    <iconify-icon icon="mdi:account-key"></iconify-icon>
                                    <span class="hide-menu">Hak Akses</span>
                                </a>
                            </li>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
    @csrf
    <li class="sidebar-item mt-5">
        <a class="sidebar-link primary-hover-bg"
            href="javascript:void(0);"
            onclick="openLogoutModal()">
            <iconify-icon icon="mdi:logout"></iconify-icon>
            <span class="hide-menu">Logout</span>
        </a>
    </li>
</form>
                    </ul>
                </nav>
                <div class="sidebar-footer p-3 text-center"
                    style="background: #f8f9fa; border-top: 1px solid #dee2e6; margin-top: 0;">
                    <div class="text-muted small">
                        <p class="mb-0 fw-semibold">Sistem Tiket Layanan</p>
                        <p class="mb-0" style="opacity: 0.7;">© 2026 Diskominfo</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="body-wrapper" style="margin-left: 270px; padding-top: 70px; min-height: 100vh;">
            <header class="app-header"
                style="position: fixed; top: 0; left: 250px; right: 0; z-index: 999; height: 70px;">
                <nav class="navbar navbar-expand-lg navbar-light" style="margin: 0; padding: 0; height: 100%;">
                    <div class="d-block d-lg-flex align-items-center justify-content-between px-4 w-100"
                        style="height: 100%;">
                        <div class="d-flex align-items-center gap-3">
                            <a class="nav-link sidebartoggler nav-icon-hover d-block d-xl-none" id="headerCollapse"
                                href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                            <h5 class="mb-0 fw-semibold fs-4 d-none d-md-block">@yield('page-title')</h5>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="dropdown">
                                
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="dropdownUser">
                                    <li><a class="dropdown-item" href="#"><iconify-icon icon="mdi:account"
                                                class="me-2"></iconify-icon>Profil Saya</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button type="button"
    class="dropdown-item text-danger"
    onclick="openLogoutModal()">
    <iconify-icon icon="mdi:logout" class="me-2"></iconify-icon>
    Keluar
</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script>
        // ambil element modal
        const modalElement = document.getElementById('exampleModal');

        // ambil tombol
        const openModalBtn = document.getElementById('openModalBtn');

        if (modalElement && openModalBtn) {
            // buat instance bootstrap modal
            const modal = new bootstrap.Modal(modalElement);

            // ketika tombol diklik
            openModalBtn.addEventListener('click', function() {
                modal.show();
            });
        }

        function replaceIconsWithFallback() {
            const icons = document.querySelectorAll('iconify-icon');
            console.log('Replacing ' + icons.length + ' icons with fallback...');
            icons.forEach(function(icon) {
                const iconAttr = icon.getAttribute('icon');
                if (!iconAttr) return;

                let fallbackText = '';
                if (iconAttr.includes('pen') || iconAttr.includes('edit') || iconAttr.includes('pencil'))
                    fallbackText = '✎';
                else if (iconAttr.includes('trash') || iconAttr.includes('delete')) fallbackText = '🗑️';
                else if (iconAttr.includes('home')) fallbackText = '🏠';
                else if (iconAttr.includes('ticket')) fallbackText = '🎫';
                else if (iconAttr.includes('add') || iconAttr.includes('plus') || iconAttr.includes('circle'))
                    fallbackText = '➕';
                else if (iconAttr.includes('folder')) fallbackText = '📁';
                else if (iconAttr.includes('book') || iconAttr.includes('bookmark')) fallbackText = '📖';
                else if (iconAttr.includes('chat') || iconAttr.includes('message') || iconAttr.includes('dots'))
                    fallbackText = '💬';
                else if (iconAttr.includes('users') || iconAttr.includes('group') || iconAttr.includes('rounded'))
                    fallbackText = '👥';
                else if (iconAttr.includes('chart') || iconAttr.includes('pie')) fallbackText = '📊';
                else if (iconAttr.includes('bell') || iconAttr.includes('notification')) fallbackText = '🔔';
                else if (iconAttr.includes('logout') || iconAttr.includes('logout-2')) fallbackText = '🚪';
                else if (iconAttr.includes('user')) fallbackText = '👤';
                else if (iconAttr.includes('menu') || iconAttr.includes('menu-2')) fallbackText = '⋮';
                else if (iconAttr.includes('close') || iconAttr.includes('x') || iconAttr.includes('ti-x'))
                    fallbackText = '✕';
                else if (iconAttr.includes('check')) fallbackText = '✓';
                else if (iconAttr.includes('eye') || iconAttr.includes('view')) fallbackText = '👁️';
                else if (iconAttr.includes('upload') || iconAttr.includes('export')) fallbackText = '⬆️';
                else if (iconAttr.includes('download') || iconAttr.includes('import')) fallbackText = '⬇️';
                else if (iconAttr.includes('refresh') || iconAttr.includes('reload')) fallbackText = '🔄';
                else if (iconAttr.includes('settings') || iconAttr.includes('gear')) fallbackText = '⚙️';
                else if (iconAttr.includes('danger') || iconAttr.includes('triangle')) fallbackText = '⚠️';
                else if (iconAttr.includes('cpu')) fallbackText = '🖥️';
                else fallbackText = '■';

                const fontSize = icon.className.includes('fs-1') ? '3.5rem' :
                    icon.className.includes('fs-2') ? '3rem' :
                    icon.className.includes('fs-3') ? '2.25rem' :
                    icon.className.includes('fs-4') ? '1.5rem' :
                    icon.className.includes('fs-5') ? '1.25rem' :
                    icon.className.includes('fs-6') ? '1rem' : '1.2em';

                icon.style.fontSize = fontSize;
                icon.style.lineHeight = '1';
                icon.style.display = 'inline-flex';
                icon.style.alignItems = 'center';
                icon.style.justifyContent = 'center';
                icon.textContent = fallbackText;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(replaceIconsWithFallback, 1000);
            setTimeout(replaceIconsWithFallback, 2000);
            setTimeout(replaceIconsWithFallback, 3000);
        });


        function openLogoutModal() {
    new bootstrap.Modal(
        document.getElementById('logoutModal')
    ).show();
}

function confirmLogout() {
    document.getElementById('logout-form').submit();
}
    </script>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content logout-modal">

            <div class="modal-body text-center p-5">

                <div class="logout-icon mb-4">
                    <iconify-icon icon="mdi:logout"></iconify-icon>
                </div>

                <h4 class="fw-bold text-dark mb-2">
                    Konfirmasi Logout
                </h4>

                <p class="text-muted mb-4">
                    Apakah Anda yakin ingin keluar dari sistem?
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button"
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="button"
                        class="btn btn-primary px-4"
                        onclick="confirmLogout()">
                        Ya, Logout
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
.logout-modal{
    border:none;
    border-radius:24px;
    overflow:hidden;

    box-shadow:
        0 25px 60px rgba(15,23,42,.25);
}

.logout-icon{
    width:90px;
    height:90px;

    margin:auto;

    border-radius:50%;

    display:flex;
    align-items:center;
    justify-content:center;

    background:linear-gradient(
        135deg,
        #172554,
        #1e3a8a,
        #2563eb
    );

    box-shadow:
        0 15px 35px rgba(37,99,235,.35);
}

.logout-icon iconify-icon{
    color:white;
    font-size:42px;
}

#logoutModal .btn-primary{
    background:linear-gradient(
        135deg,
        #172554,
        #2563eb
    );

    border:none;
    border-radius:12px;
}

#logoutModal .btn-primary:hover{
    transform:translateY(-1px);
}

#logoutModal .btn-light{
    border-radius:12px;
}
</style>

    @stack('scripts')
    @vite(['resources/js/app.js'])
</body>

</html>
