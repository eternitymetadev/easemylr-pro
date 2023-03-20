<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.includes.header')
    <style>
    .dropdown-toggle svg.feather[class*="feather-chevron-"] {
        width: 15px;
        height: auto;
        vertical-align: middle;
    }

    .fade {
        transition: opacity .15s linear;
        zoom: 110%;
    }
    </style>
</head>

<body class="zoomer alt-menu sidebar-noneoverflow" style="zoom: 90%;">

    <div class="main-container d-flex flex-wrap" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <div class="sidebarBox" id="sidebarBox" data-sidebarOpen='1' onmouseenter="openSidebarWidth()"
            onmouseleave="closeSidebarWidth()">
            <nav id="sidebar">
                <button id="sidebarToggleButton" onclick="toggleSidebarWidth()">
                    <svg id="togglerSvg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-right">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
                @include('layouts.includes.sidebar')
            </nav>
        </div>

        <!--  BEGIN CONTENT AREA  -->
        <?php $authuser = Auth::user(); ?>
        <div id="content" class="main-content" style="min-height: calc(100% - 64px)">
            <header class="header-container header navbar navbar-expand-sm">
                <h1>@yield('page-heading','Agri Wings')</h1>
                <div class="newUserMenu">
                    <img src="{{asset('newasset/assets/img/90x90.jpg')}}" class="img-fluid" alt="admin-profile" />
                    <ul>
                        <li class="userDisplayName">{{ucfirst($authuser->name ?? 'User Name')}}</li>
                        <li>
                            <a class="" href="{{route('logout')}}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf
                            </form>
                        </li>
                    </ul>
                </div>

                {{--            @include('layouts.includes.navbar')--}}
            </header>
            @yield('content')
            @include('layouts.includes.footer')

        </div>
        @yield('js')
</body>

</html>