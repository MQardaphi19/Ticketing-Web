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
                                    <span class="hide-menu">Log Chatbot</span>
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
                            <li class="sidebar-item mt-5 {{ request()->routeIs('users.index') ? 'selected' : '' }}">
                                <a class="sidebar-link primary-hover-bg" aria-expanded="false"
                                    onclick="document.getElementById('logout-form').submit(); return false;"
                                    href="javascript:void(0);">
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
                style="position: fixed; top: 0; left: 250px; right: 0; z-index: 999; background: #fff; height: 70px; border-bottom: 1px solid #dee2e6;">
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
                                <a href="javascript:void(0)" class="nav-link position-relative"
                                    id="dropdownNotification" data-bs-toggle="dropdown">
                                    <iconify-icon icon="mdi:bell" class="fs-6"></iconify-icon>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        id="notification-count">3</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up py-0 overflow-hidden"
                                    aria-labelledby="dropdownNotification">
                                    <div class="p-3 border-bottom bg-primary-subtle">
                                        <h6 class="mb-0">Notifikasi</h6>
                                    </div>
                                    <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="bg-primary-subtle rounded-circle p-2">
                                                <iconify-icon icon="mdi:ticket"
                                                    class="text-primary fs-5"></iconify-icon>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 text-dark small">Tiket TIX-202602-001 telah diselesaikan
                                                </p>
                                                <small class="text-muted">5 menit yang lalu</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="bg-warning-subtle rounded-circle p-2">
                                                <iconify-icon icon="mdi:chat"
                                                    class="text-warning fs-5"></iconify-icon>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 text-dark small">Pesan baru di tiket TIX-202602-002</p>
                                                <small class="text-muted">15 menit yang lalu</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="#" class="dropdown-item text-center py-2 border-top">Lihat
                                        Semua</a>
                                </div>
                            </div>

                            <div class="dropdown">
                                <a href="javascript:void(0)" class="nav-link d-flex align-items-center gap-2"
                                    id="dropdownUser" data-bs-toggle="dropdown">
                                    <img src="{{ asset('assets/images/profile/user1.jpg') }}" alt="User"
                                        class="rounded-circle" width="35" height="35">
                                    <div class="d-none d-lg-block">
                                        <h6 class="mb-0 fw-semibold fs-6">
                                            {{ auth()->check() ? auth()->user()->name : 'Demo User' }}</h6>
                                        <small class="text-muted">Super Admin</small>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="dropdownUser">
                                    <li><a class="dropdown-item" href="#"><iconify-icon icon="mdi:account"
                                                class="me-2"></iconify-icon>Profil Saya</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <iconify-icon icon="mdi:logout" class="me-2"></iconify-icon>Keluar
                                            </button>
                                        </form>
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
    </script>
    @stack('scripts')
    @vite(['resources/js/app.js'])
</body>

</html>
