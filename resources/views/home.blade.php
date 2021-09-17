@extends('layouts.maba')

@section('content')
<div id="menu" class="container maba-center">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="margin-left: -15px; margin-right: -15px; margin-bottom: 20px;">
        <div class="container">
            <!--a class="navbar-brand" href="{{ url('/home') }}">
                <button class="btn" style="padding: 0; font-size: 20px;"><i class="fa fa-home"></i></button>
            </a-->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    @canany(['link.view','link.manage'])
                    <li class="nav-item {{ (request()->is('links')) ? 'active' : '' }}">
                        <a class="nav-link" href="/links" class="sidelink" style="font-size: 15px;">Links</a>
                    </li>
                    @endcanany
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    @can('user.manage')
                    <li class="nav-item dropdown">
                        <i class="bi bi-gear-fill nav-link" style="font-size: 2em;" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/users">User Management</a>
                            <a class="dropdown-item" href="/roles">Role Management</a>
                        </div>
                    </li>
                    @endcan
                    <!-- Authentication Links -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="font-size: 15px;">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row" style="margin-bottom: 50px;">
         
            <!--div class="row"-->
            <maba-menu></maba-menu>
            <!--/div-->
    </div>
    <!--div class="row" style="margin-bottom: 50px;">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="block-menu" @mouseover = "menuHovering = true"
                                      @mouseout = "menuHovering = false"
                                      :class = "{shadow: menuHovering}">
                        <a href="/vulners">
                        <img class="menu-image" src="/img/addc-logo.png" alt="Active Directory Domain Controller">
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="block-menu" @mouseover = "menuHovering = true"
                                      @mouseout = "menuHovering = false"
                                      :class = "{shadow: menuHovering}">
                        <a href="/vulners">
                        <img class="menu-image" src="/img/user-management-icon2.png" alt="User management">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div-->
    
</div>
@endsection
