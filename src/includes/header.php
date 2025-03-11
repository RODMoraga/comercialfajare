            <header class="main-header">
                <!-- Logo -->
                <a href="/dashboard" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini">CM</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg">Comercial Fajare</span>
                </a>

                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Navegación</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->
              
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="src/files/usernames/admin.jpg" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?php echo $_SESSION['access'][1]; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="src/files/usernames/admin.jpg" class="img-circle" alt="User Image">
                                        <p>
                                            <small><i class="fa fa-wrench"></i>&nbsp;Soporte técnico: Rodrigo Moraga Garrido</small>
                                            <small><i class="fa fa-phone-square"></i>&nbsp;+56 9 8478 1159</small>
                                        </p>
                                    </li>
                  
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-right">
                                            <a href="/login/close" class="btn btn-danger btn-flat">Cerrar</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>