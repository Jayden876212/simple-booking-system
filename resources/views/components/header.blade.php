<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="">Simple Booking System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a
                        class="
                            nav-link
                            @if ($page_title == "Home")
                                active
                            @endif
                        "
                        href="{{ url("home") }}"
                        @if ($page_title == "Home")
                            aria-current="page"
                        @endif
                    >
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="
                            nav-link
                            @if (($page_title == "Login") or ($page_title == "Register"))
                                active
                            @endif
                        "
                        href="{{ url("account") }}"
                        @if (($page_title == "Login") or ($page_title == "Register"))
                            aria-current="page"
                        @endif
                    >
                        Account
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="
                            nav-link
                            @if ($page_title == "Bookings")
                                active
                            @endif
                        "
                        href="{{ url("bookings") }}"
                        @if ($page_title == "Bookings")
                            aria-current="page"
                        @endif
                    >
                        Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="
                            nav-link
                            @if ($page_title == "Orders")
                                active
                            @endif
                        "
                        href="{{ url("orders") }}"
                        @if ($page_title == "Orders")
                            aria-current="page"
                        @endif
                    >
                        Orders
                    </a>
                </li>
            </ul>
            <div class="btn-group dropstart">
                <button
                    type="button"
                    class="btn 
                        @auth
                            btn-success
                        @endauth
                        @guest
                            btn-outline-secondary
                        @endguest
                    dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false"
                >
                    @auth
                        <i class="fa-solid fa-user"></i>
                        {{ Auth::user()["username"] }}
                    @endauth
                    @guest
                        <i class="fa-regular fa-user"></i>
                        Not logged in
                    @endguest
                </button>
                <ul class="dropdown-menu">
                    @auth
                        <li><a class="dropdown-item" href="{{ url("account/logout") }}">Log Out</a></li>
                        <li><a class="dropdown-item" href="{{ url("account/delete") }}">Delete Account</a></li>
                    @endauth
                    @guest
                        <li><a class="dropdown-item" href="{{ url("account/login") }}">Log In</a></li>
                        <li><a class="dropdown-item" href="{{ url("account/register") }}">Sign Up</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @if (\Session::has('success'))
        <div class="alert alert-success m-2" role="alert">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif
</header>