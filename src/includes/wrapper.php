            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="header"></li>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador" || $_SESSION['access'][2] == "Usuario" || $_SESSION['access'][2] == "Super Usuario") {
                            echo '<li id="mEscritorio">
                                    <a href="/dashboard">
                                        <i class="fa fa-tasks"></i> <span>Dashboard</span>
                                    </a>
                                </li>';
                        }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador") {
                            echo '<li id="mMaestras" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-laptop"></i>
                                        <span>Maestras</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lClientes"><a href="/clientes"><i class="fa fa-circle-o"></i> Clientes</a></li>
                                        <li id="lProducto"><a href="/productos"><i class="fa fa-circle-o"></i> Productos</a></li>
                                        <li id="lRuote"><a href="/rutas"><i class="fa fa-circle-o"></i> Rutas</a></li>
                                        <li id="lPrecio"><a href="/precios"><i class="fa fa-circle-o"></i> Lista de Precios</a></li>
                                        <li id="lCategorias"><a href="/categorias"><i class="fa fa-circle-o"></i> Categorías</a></li>
                                        <li id="lUnidadMedida"><a href="/unidadmedidas"><i class="fa fa-circle-o"></i> Unidad Medida</a></li>
                                        <li id="lBank"><a href="/bancos"><i class="fa fa-circle-o"></i> Bancos</a></li>
                                    </ul>
                                </li>';
                        }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador") {
                            /*echo '<li id="mVentas" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Ventas</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lVentas"><a href="ventas.php"><i class="fa fa-circle-o"></i> Generar Documento</a></li>
                                    </ul>
                                </li>';*/
                            }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador" || $_SESSION['access'][2] == "Super Usuario") {
                            echo '<li id="mNotaPedido" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-file-text"></i>
                                        <span>Pedidos</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lNotaPedido"><a href="/notapedidos"><i class="fa fa-circle-o"></i> Notas De Pedidos</a></li>
                                        <li id="lNotaPedidoTwo"><a href="/imprimirlotes"><i class="fa fa-circle-o"></i> Imprimir por Lotes</a></li>
                                    </ul>
                                </li>';
                            }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador" || $_SESSION['access'][2] == "Super Usuario") {
                            echo '<li id="mNotaPedido" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-truck"></i>
                                        <span>Rutas</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lRutasPorHacer"><a href="/rutasporhacer"><i class="fa fa-circle-o"></i> Rutas Por Asignar</a></li>
                                    </ul>
                                </li>';
                            }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador" || $_SESSION['access'][2] == "Super Usuario") {
                            echo '<li id="mPayment" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-money"></i> <span>Pagos</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lPago1"><a href="/pagos"><i class="fa fa-circle-o"></i> Pago Clientes</a></li>
                                        <li id="lPago2"><a href="/docpendientes"><i class="fa fa-circle-o"></i> Documentos Pendientes</a></li>
                                        <li id="lPago3"><a href="/doccancelados"><i class="fa fa-circle-o"></i> Documentos Pagados</a></li>
                                        <li id="lPago4"><a href="/resumenpagos"><i class="fa fa-circle-o"></i> Resumen Pago Cliente</a></li>
                                    </ul>
                                </li>';
                        }
                        ?>
                        <?php
                        if ($_SESSION['access'][2] == "Administrador" || $_SESSION['access'][2] == "Super Usuario") {
                            echo '<li id="mAcceso" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-user"></i> <span>Acceso</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lUsuarios"><a href="/usuarios"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                                    </ul>
                                </li>';
                        }
                        ?>
                        <?php 
                        if ($_SESSION["access"][2] == "Administrador" || $_SESSION["access"][2] == "Super Usuario") {
                            echo '<li id="mConsultaV" class="treeview">
                                    <a href="#">
                                        <i class="fa fa-bar-chart"></i> <span>Consulta Ventas</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="lConsulasD"><a href="/ventasdiarias"><i class="fa fa-circle-o"></i> Ventas Diarias</a></li>
                                        <li id="lConsulasC"><a href="/ventascategorias"><i class="fa fa-circle-o"></i> Ventas Categoria Producto</a></li>
                                        <li id="lConsulasP"><a href="/resumenproducto"><i class="fa fa-circle-o"></i> Resumen Por Productos</a></li>
                                    </ul>
                                </li>';
                        }
                        ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-desktop"></i> <span>Contadora</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="person"><a href="contadora.php"><i class="fa fa-circle-o"></i> Bajar Documento</a></li>
                            </ul>
                        </li>
                        <!--
                        <li>
                            <a href="ayuda.php">
                                <i class="fa fa-plus-square"></i> <span>Ayuda</span>
                                    <small class="label pull-right bg-red">PDF</small>
                            </a>
                        </li>
                        <li>
                            <a href="acerca.php">
                                <i class="fa fa-info-circle"></i> <span>Acerca De...</span>
                                <small class="label pull-right bg-yellow">IT</small>
                            </a>
                        </li>-->
                    </ul>
                </section>
            <!-- /.sidebar -->
            </aside>