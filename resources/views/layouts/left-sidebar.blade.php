<div class="nav-left-sidebar sidebar-dark">
        <div class="menu-list">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-divider">
                            Menu
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="/dashboard"><i class="fa fa-fw fa-tachometer-alt"></i>Dashboard</a>
                            <a class="nav-link" href="/queues"><i class="fa fa-fw fa-clock"></i>Content QUEUE</a>
                            <a class="nav-link" href="/contentbank"><i class="fa fa-fw fa-piggy-bank"></i>Content Bank</a>
                            <a class="nav-link" href="{{ url('/socialaccounts') }}"><i class="fas fa-share-square"></i>Social Accounts</a>
                            <a class="nav-link" href="/messages"><i class="fa fa-fw fa-comment"></i>Messages</a>
                            <a class="nav-link" href="/profile"><i class="fa fa-fw fa-user-circle"></i>My Profile</a>
                            <!--<a class="nav-link" href="/archive"><i class="fa fa-fw fa-archive"></i>Archive</a>-->
                            @if (is_admin())
                                <a class="nav-link" href="/manageusers"><i class="fa fa-fw fa-users"></i>Manage Users</a>
                            @endif
                            @if (is_admin() || is_accountManager())
                                <a class="nav-link" href="/clients"><i class="fa fa-fw fa-users"></i>My Clients</a>
                            @endif
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>