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
                    <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
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
        <?php if (!empty($_SESSION['permisos'][4]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Electoral</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>candidatos"><i class="icon fa fa-circle-o"></i>
                            Candidatos</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>lideres"><i class="icon fa fa-circle-o"></i>
                            Lideres</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>electores"><i class="icon fa fa-circle-o"></i>
                            Electores</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>votacion"><i class="icon fa fa-circle-o"></i>
                            Votacion</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][7]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Financiero</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>terceros"><i class="icon fa fa-circle-o"></i>
                            Terceros</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>conceptos"><i class="icon fa fa-circle-o"></i>
                            Conceptos Ing. y Gastos</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>elementos"><i class="icon fa fa-circle-o"></i>
                            Elementos de Campaña</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>movimientos"><i class="icon fa fa-circle-o"></i>
                            Movimientos</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>entradas"><i class="icon fa fa-circle-o"></i>
                            Entradas</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>salidas"><i class="icon fa fa-circle-o"></i>
                            Salidas</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][13]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
                    <span class="app-menu__label">Informes</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>infelectores"><i class="icon fa fa-circle-o"></i>
                            Informe Electores</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][14]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
                    <span class="app-menu__label">Agenda</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>agenda"><i class="icon fa fa-circle-o"></i>
                            Agenda</a></li>
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