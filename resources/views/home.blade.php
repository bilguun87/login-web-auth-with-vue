@extends('layouts.maba')

@section('content')
<div id="menu" class="container maba-center">
    <div><a href="/home">Home</a></div>
    <div class="row" style="justify-content: flex-end;">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
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
    </div>

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
