<div id="page-content-wrapper">

    <nav class="navbar navbar-dark text-light bg-dark border-bottom">

        <button class="sidebar-toggler" id="menu-toggle"><span class="navbar-toggler-icon"></span></button>

        <div class="dropdown">
            <button class="btn btn-transparent text-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Profile
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">@lang('Change password')</a>
            <a class="dropdown-item" href="#">@lang('Logout')</a>
            </div>
        </div>

    </nav>

    <div class="container-fluid">
        {{ $slot }}
    </div>
</div>
