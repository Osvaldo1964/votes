<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png"
            alt="User Image">
        <div>
            <p class="app-sidebar__user-name" style="text-transform: capitalize; font-size: 10px;">
                <?= $_SESSION['userData']['nombre_usuario']; ?>
            </p>
            <p class="app-sidebar__user-designation" style="text-transform: capitalize; font-size: 10px;">
                <?= $_SESSION['userData']['nombre_rol']; ?>
            </p>
        </div>
    </div>
    <ul class="app-menu">
        <?php if (!empty($_SESSION['permisos'][1]['r_permiso'])) { ?>
            <li>
                <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
                    <i class="app-menu__icon fa fa-dashboard"></i>
                    <span class="app-menu__label">Dashboard</span>
                </a>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][2]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-id-card" aria-hidden="true"></i>
                    <span class="app-menu__label">Usuarios</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>usuarios"><i class="icon fa fa-circle-o"></i>
                            Usuarios</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>roles"><i class="icon fa fa-circle-o"></i>
                            Roles</a></li>
                </ul>
            </li>
        <?php } ?>
        <!-- Aqui debo colocar los permisos de control Administrativo de todos los modulos -->
        <?php if (!empty($_SESSION['permisos'][14]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-briefcase" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Administrativo</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>agenda"><i class="icon fa fa-circle-o"></i>
                            Agenda</a></li>
                </ul>
            </li>
        <?php } ?>
        <!-- Aqui debo colocar los permisos de control electoral de todos los modulos -->
        <?php if (
            !empty($_SESSION['permisos'][4]['r_permiso']) || !empty($_SESSION['permisos'][5]['r_permiso'])
            || !empty($_SESSION['permisos'][6]['r_permiso']) || !empty($_SESSION['permisos'][15]['r_permiso'])
            || !empty($_SESSION['permisos'][16]['r_permiso']) || !empty($_SESSION['permisos'][17]['r_permiso'])
            || !empty($_SESSION['permisos'][18]['r_permiso'])
        ) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-check-square-o" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Electoral</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (!empty($_SESSION['permisos'][4]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>candidatos"><i class="icon fa fa-circle-o"></i>
                                Candidatos</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][5]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>lideres"><i class="icon fa fa-circle-o"></i>
                                Lideres</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][6]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>electores"><i class="icon fa fa-circle-o"></i>
                                Electores</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][18]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>testigos"><i class="icon fa fa-circle-o"></i>
                                Testigos Electorales</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][15]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>votacion"><i class="icon fa fa-circle-o"></i>
                                Votacion</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][16]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>resultados"><i class="icon fa fa-circle-o"></i>
                                Resultados por Mesa</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][17]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>monitor"><i class="icon fa fa-circle-o"></i>
                                Monitor dia D</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][17]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>analisis"><i class="icon fa fa-circle-o"></i>
                                Analisis E-14</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <!-- Aqui debo colocar los permisos de control financiero de todos los modulos -->
        <?php if (
            !empty($_SESSION['permisos'][7]['r_permiso']) || !empty($_SESSION['permisos'][8]['r_permiso'])
            || !empty($_SESSION['permisos'][9]['r_permiso']) || !empty($_SESSION['permisos'][10]['r_permiso'])
            || !empty($_SESSION['permisos'][11]['r_permiso']) || !empty($_SESSION['permisos'][12]['r_permiso'])
        ) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-money" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Financiero</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (!empty($_SESSION['permisos'][7]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>terceros"><i class="icon fa fa-circle-o"></i>
                                Terceros</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][8]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>conceptos"><i class="icon fa fa-circle-o"></i>
                                Conceptos Ing. y Gastos</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][9]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>elementos"><i class="icon fa fa-circle-o"></i>
                                Elementos de Campaña</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][10]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>movimientos"><i class="icon fa fa-circle-o"></i>
                                Movimientos</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][11]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>entradas"><i class="icon fa fa-circle-o"></i>
                                Entradas</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][12]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>salidas"><i class="icon fa fa-circle-o"></i>
                                Salidas</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][13]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-bar-chart" aria-hidden="true"></i>
                    <span class="app-menu__label">Informes</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <!-- Nivel 2: Administrativos -->
                    <li class="treeview">
                        <a class="treeview-item" href="#" data-toggle="treeview"
                            style="display: flex; justify-content: space-between; align-items: center; padding-right: 15px;">
                            <span><i class="icon fa fa-folder-open"></i> Reportes Administrativos</span>
                            <i class="treeview-indicator fa fa-angle-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <!-- Nivel 3 -->
                            <li><a class="treeview-item" href="<?= base_url(); ?>infelectores"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Informe ...</a></li>
                            <li><a class="treeview-item" href="<?= base_url(); ?>ReporteElectoralCenso"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Informe ...</a></li>
                        </ul>
                    </li>
                    <!-- Nivel 2: Electorales -->
                    <li class="treeview">
                        <a class="treeview-item" href="#" data-toggle="treeview"
                            style="display: flex; justify-content: space-between; align-items: center; padding-right: 15px;">
                            <span><i class="icon fa fa-address-book"></i> Reportes Electorales</span>
                            <i class="treeview-indicator fa fa-angle-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <!-- Nivel 3 -->
                            <li><a class="treeview-item" href="<?= base_url(); ?>infelectores"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Informe Electores</a>
                            </li>
                            <li><a class="treeview-item" href="<?= base_url(); ?>ReporteElectoralCenso"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Reporte Censo vs
                                    Real</a></li>
                            <li><a class="treeview-item" href="<?= base_url(); ?>ReporteTestigos"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Reporte de Testigos</a>
                            </li>
                            <li><a class="treeview-item" href="<?= base_url(); ?>ReporteImpugnaciones"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Reporte
                                    Impugnaciones</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Nivel 2: Financieros -->
                    <li class="treeview">
                        <a class="treeview-item" href="#" data-toggle="treeview"
                            style="display: flex; justify-content: space-between; align-items: center; padding-right: 15px;">
                            <span><i class="icon fa fa-area-chart"></i> Reportes Financieros</span>
                            <i class="treeview-indicator fa fa-angle-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <!-- Nivel 3 -->
                            <li><a class="treeview-item" href="<?= base_url(); ?>infsaldos" style="padding-left: 40px;"><i
                                        class="icon fa fa-circle-o"></i> Informe Elementos</a></li>
                            <li><a class="treeview-item" href="<?= base_url(); ?>infmovimientos"
                                    style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Informe Ingresos y
                                    Gastos</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        <?php } ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/logout">
                <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
                <span class="app-menu__label">Logout</span>
            </a>
        </li>
    </ul>
</aside>