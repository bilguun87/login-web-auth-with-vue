@extends('layouts.maba')

@section('content')
<div class="container maba-center">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="margin-left: -15px; margin-right: -15px;">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/home') }}">
                <button class="btn" style="padding: 0; font-size: 20px;"><i class="fa fa-home"></i></button>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item {{ (request()->is('backups')) ? 'active' : '' }}">
                        <a class="nav-link" href="/backups" class="sidelink" style="font-size: 15px;">Lists</a>
                    </li>
                    <li class="nav-item {{ (request()->is('backups/types')) ? 'active' : '' }}">
                        <a class="nav-link" href="/backups/types" class="sidelink" style="font-size: 15px;">Types</a>
                    </li>
                    <li class="nav-item {{ (request()->is('backups/places')) ? 'active' : '' }}">
                        <a class="nav-link" href="/backups/places" class="sidelink" style="font-size: 15px;">Places</a>
                    </li>
                    <li class="nav-item {{ (request()->is('backups/moves')) ? 'active' : '' }}">
                        <a class="nav-link" href="/backups/moves" class="sidelink" style="font-size: 15px;">Moves</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
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
    @if (isset($subpage))
        <backup-{!! $subpage !!}>
    @else
        <backup-index></backup-index>
    @endif
       
</div>
@endsection