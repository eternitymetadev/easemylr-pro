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
<!-- BEGIN LOADER -->
<div id="load_screen">
    <div class="loader">
        <div class="loader-content">
            <div class="spinner-grow align-self-center"></div>
        </div>
    </div>
</div>
<!--  END LOADER -->

<!--  BEGIN NAVBAR  -->
<div class="header-container fixed-top">
    <header class="header navbar navbar-expand-sm">
        @include('layouts.includes.navbar')
    </header>
</div>
<!--  END NAVBAR  -->

<!--  BEGIN MAIN CONTAINER  -->
<div class="main-container" id="container">

    <div class="overlay"></div>
    <div class="search-overlay"></div>

    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content" style="min-height: calc(100% - 64px)">
        @yield('content')
    </div>
@include('layouts.includes.footer')
@yield('js')
</body>
</html>
