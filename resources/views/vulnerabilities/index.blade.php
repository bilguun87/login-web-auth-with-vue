@extends('layouts.maba')

@section('content')
<div id="menu" class="container maba-center">
	@if (Auth::user())
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
    @endif
    <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; open</span>
    <div id="mySidenav" class="sidenav">
		<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
		<a href="#">About</a>
		<a href="#">Services</a>
		<a href="#">Clients</a>
		<a href="#">Contact</a>
	</div>
    <div class="row" style="margin-bottom: 50px;">
         
            <!--div class="row"-->
            <maba-vulnindex></maba-vulnindex>
            <!--/div-->
        
    </div>
</div>
<!--script>
/*function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}*/
</script-->
@endsection