<!-- BEGIN NAVBAR -->
<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <!-- NAVBAR TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- NAVBAR BRAND -->
        <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="/">CBAM</a>
        </div>
        <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item d-none d-md-flex me-2">
                <!-- Optional theme toggle if needed -->
            </div>
            <div class="nav-item dropdown">
                @auth
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm"
                        style="background-image: url({{ Auth::user()->avatar ?? '/static/avatars/default.jpg' }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-secondary">{{ Auth::user()->role ?? 'User' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="/profile" class="dropdown-item">Profile</a>
                    <a href="/settings" class="dropdown-item">Settings</a>
                    <form method="POST" action="" class="dropdown-item">
                        @csrf
                        <button type="submit" class="dropdown-item btn btn-link p-0">Logout</button>
                    </form>
                </div>
                @else
                <a href="" class="nav-link px-0 hide-theme-dark btn btn-link">Login</a>
                @endauth
            </div>
        </div>
    </div>
</header>
<div class="collapse navbar-collapse" id="navbar-menu">
    <div class="navbar">
        <div class="container-xl">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/upload">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 9l5 -5l5 5" />
                                <path d="M12 4l0 12" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Upload File</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/master">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3" />
                                <path d="M4 6v6c0 1.657 3.582 3 8 3c1.118 0 2.183 -.086 3.15 -.241" />
                                <path d="M20 12v-6" />
                                <path d="M4 12v6c0 1.657 3.582 3 8 3c.157 0 .312 -.002 .466 -.005" />
                                <path d="M16 19h6" />
                                <path d="M19 16l3 3l-3 3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Master Data</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reports">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2" />
                                <path d="M9 17v-5" />
                                <path d="M12 17v-1" />
                                <path d="M15 17v-3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Reports</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- END NAVBAR -->