<div id="page-content-wrapper">

    <nav class="navbar navbar-dark text-light bg-dark border-bottom">

        <button class="sidebar-toggler" id="menu-toggle"><span class="navbar-toggler-icon"></span></button>

        <div class="dropdown">
            <button class="btn btn-transparent text-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->name }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">@lang('Change password')</a>
            <form action="/logout" method="POST">
                @csrf
                <input class="dropdown-item" type="submit" value="@lang('Logout')">
            </form>
            </div>
        </div>

    </nav>

    <div>
        {{ $slot }}
    </div>
</div>
