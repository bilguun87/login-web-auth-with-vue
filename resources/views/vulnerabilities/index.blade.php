@extends('layouts.maba')

@section('content')
<div class="container maba-center">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="margin-left: -15px; margin-right: -15px;">
        <div class="container">
            <div class="homedrop">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <i class="fa fa-home"></i>
                </a>
                <div class="homedrop-content">
                    @canany(['vulner.hosts.view','vulner.hosts.manage','vulner.manage','vulner.report','vulner.view'])
                    <a href="/vulners" class="">Vulnerabilities</a>
                    @endcanany
                    @canany(['backup.view','backup.manage'])
                    <a href="/backups" class="">Backups</a>
                    @endcanany
                    @canany(['oradb.view','oradb.manage'])
                    <a href="/oradb" class="">OraDB Check</a>
                    @endcanany
                    @canany(['addc.view'])
                    <a href="/addc" class="">ADDC Check</a>
                    @endcanany
                    @canany(['link.view','link.manage'])
                    <a href="/links">Links</a>
                    @endcanany
                </div>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    @canany(['user.manage','vulner.hosts.view','vulner.hosts.manage','vulner.manage','vulner.report','vulner.view'])
                    <li class="nav-item {{ (request()->is('vulners')) ? 'active' : '' }}">
                        <a class="nav-link" href="/vulners" class="sidelink" style="font-size: 15px;">Dashboard</a>
                    </li>
                    @endcanany
                    @canany(['user.manage','vulner.manage','vulner.report','vulner.view'])
                    <li class="nav-item {{ (request()->is('vulners/search')) ? 'active' : '' }}">
                        <a class="nav-link" href="/vulners/search" class="sidelink" style="font-size: 15px;">Search</a>
                    </li>
                    @endcanany
                    @can('vulner.manage')
                    <li class="nav-item {{ (request()->is('vulners/upload')) ? 'active' : '' }}">
                        <a class="nav-link" href="/vulners/upload" class="sidelink" style="font-size: 15px;">Upload</a>
                    </li>
                    @endcan
                    @can('vulner.manage')
                    <li class="nav-item {{ (request()->is('vulners/allot')) ? 'active' : '' }}">
                        <a id="grouping" class="nav-link" href="/vulners/allot" class="sidelink" style="font-size: 15px;" data-toggle="tooltip" data-placement="top" title="To allot a vulnerabilities into departments">Grouping</a>
                    </li>
                    @endcan
                    @canany(['user.manage','vulner.manage','vulner.hosts.view','vulner.hosts.manage'])
                    <li class="nav-item {{ (request()->is('vulners/servers')) ? 'active' : '' }}">
                        <a class="nav-link" href="/vulners/servers" class="sidelink" style="font-size: 15px;">Known servers</a>
                    </li>
                    @endcanany
                    @canany(['user.manage','vulner.manage','vulner.report'])
                    <li class="nav-item {{ (request()->is('vulners/report')) ? 'active' : '' }}">
                        <a class="nav-link" href="/vulners/report" class="sidelink" style="font-size: 15px;">Report</a>
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
    @if (isset($subpage))
        <maba-vuln{!! $subpage !!}>
    @else
        <maba-vulnindex></maba-vulnindex>
    @endif
       
</div>
@endsection