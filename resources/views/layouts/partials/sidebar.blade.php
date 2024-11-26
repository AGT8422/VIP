@inject('request', 'Illuminate\Http\Request')

 

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar" style="width:16%; border-right:2px solid #ee680e !important; background-color:black!important; ">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar"  >

    <a href="{{route('home')}}" class="logo">
      <span class="logo-lg">{{ Session::get('business.name') }}</span>
    </a>

      <!-- Sidebar Menu -->
      {!! Menu::render('admin-sidebar-menu', 'adminltecustom'); !!}

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>
 