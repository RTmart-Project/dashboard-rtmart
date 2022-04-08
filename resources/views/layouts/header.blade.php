  <!-- Left navbar links -->
  <ul class="navbar-nav">
      <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
          <a class="nav-link"><b>@yield('header-menu')</b></a>
      </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
      <li class="nav-item text-right">
          <a class="nav-link"><b>{{ Auth::user()->Name }}</b></a>
      </li>
      <li class="nav-item text-right">
          <a href="/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </li>
  </ul>