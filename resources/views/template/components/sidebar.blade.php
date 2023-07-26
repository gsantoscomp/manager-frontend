<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-solid fa-horse-head"></i></i>
        </div>
        <div class="sidebar-brand-text mx-3">Hor<sup>System</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Admin -->

    <div class="sidebar-heading">
        @if(session('user')->id === 1)
        Admin
        @elseif(session('user')->id === 2)
        Veterinário
        @endif
    </div>

    <!-- Nav Item - Pages Collapse Menu -->

    @if(in_array('userType.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#user-type-routes"
            aria-expanded="true" aria-controls="user-type-routes">
            <i class="fas fa-fw fa-cog"></i>
            <span>Tipos de Usuário</span>
        </a>

        <div id="user-type-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.userType.index') }}">Lista de Tipos de Usuário</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('animals.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#animal-type-routes"
            aria-expanded="true" aria-controls="animal-type-routes">
            <i class="fas fa-fw fa fa-paw"></i>
            <span>Animais</span>
        </a>

        <div id="animal-type-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.animal.index') }}">Lista todos os Animais</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('user.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#user-routes"
            aria-expanded="true" aria-controls="user-routes">
            <i class="fas fa-fw fa-users"></i>
            <span>Usuários</span>
        </a>

        <div id="user-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.user.index') }}">Lista todos os Usuários</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('appointment.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#appointment-routes"
            aria-expanded="true" aria-controls="appointment-routes">
            <i class="fa fa-address-book"></i>
            <span>Consultas</span>
        </a>

        <div id="appointment-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.appointment.index') }}">Lista as Consultas</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('client.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#client-routes"
            aria-expanded="true" aria-controls="client-routes">
            <i class="fas fa-fw fa-address-card"></i>
            <span>Clientes</span>
        </a>

        <div id="client-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.client.index') }}">Lista os Clientes</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('medicine.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#medicine-routes"
            aria-expanded="true" aria-controls="medicine-routes">
            <i class="fas fa-fw fa-medkit"></i>
            <span>Medicamentos</span>
        </a>

        <div id="medicine-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.medicine.index') }}">Lista os Medicamentos</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('permission.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#permission-routes"
            aria-expanded="true" aria-controls="permission-routes">
            <i class="fas fa-fw fa-cog"></i>
            <span>Permissões</span>
        </a>

        <div id="permission-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.permission.index') }}">Lista as Permissões</a>
            </div>
        </div>
    </li>
    @endif

    @if(in_array('procedure.index', session('permissions')))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#procedure-routes"
            aria-expanded="true" aria-controls="procedure-routes">
            <i class="fas fa-fw fa fa-stethoscope"></i>
            <span>Procedimentos</span>
        </a>

        <div id="procedure-routes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.procedure.index') }}">Lista os Procedimentos</a>
            </div>
        </div>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
