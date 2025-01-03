        <!-- App navbar starts -->
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <div class="offcanvas offcanvas-end" id="MobileMenu">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title semibold">Navegação</h5>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="offcanvas">
                            <i class="icon-clear"></i>
                        </button>
                    </div>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown <?=$active === 'dashboard' ? 'active-link': ''?>">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-stacked_line_chart"></i> Dashboards
                            </a>
                            <ul class="dropdown-menu">
                                <!-- <li>
                                    <a class="dropdown-item current-page" href="/dashboard/analytics">
                                        <span>Analytics</span>
                                    </a>
                                </li> -->
                                <li>
                                    <a class="dropdown-item current-page" href="/dashboard/facility">
                                        <span>Facilidades</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php if (hasPermission('visualizar cadastro')) { ?>
                            <li class="nav-item dropdown <?=$active === 'cadastro' ? 'active-link': ''?>">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-add_task"></i> Cadastros
                                </a>
                                <ul class="dropdown-menu">
                                <?php if (hasPermission('visualizar bimestres')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="\apartamento\">
                                            <span>Bimestres</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (hasPermission('visualizar componentes')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="\apartamento\">
                                            <span>Componente Curriculares</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (hasPermission('visualizar estudantes')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="\apartamento\">
                                            <span>Estudantes</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (hasPermission('visualizar professores')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="\professores">
                                            <span>Professores</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (hasPermission('visualizar turmas')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="\turmas">
                                            <span>Turmas</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                </ul>
                            </li>
                        <?php } if (hasPermission('visualizar pedagogico')) { ?>
                            <li class="nav-item dropdown <?=$active === 'cadastro' ? 'active-link': ''?>">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-add_task"></i> Pedagogico
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if (hasPermission('visualizar componentes')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\apartamento\">
                                                <span>Disciplinas & Professor</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (hasPermission('visualizar turma e estudante')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\turma-estudante\">
                                                <span>Turmas & Estudantes</span>
                                            </a>
                                        </li>
                                    <?php } ?>                                
                                    <?php if (hasPermission('visualizar estudantes')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\apartamento\">
                                                <span>Turmas & Disciplinas</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (hasPermission('visualizar estudantes')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\apartamento\">
                                                <span>Conteúdos</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (hasPermission('visualizar estudantes')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\apartamento\">
                                                <span>Frequência</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (hasPermission('visualizar estudantes')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="\apartamento\">
                                                <span>Notas</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } if (hasPermission('visualizar financeiro')) {?>
                            <li class="nav-item dropdown <?=$active === 'financeiro' ? 'active-link': ''?>">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-now_widgets"></i> Financeiro
                                </a>
                                <ul class="dropdown-menu"> 
                                    <?php if (hasPermission('visualizar contas bancarias')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="/bancos/">
                                                <span>Contas Bancárias</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (hasPermission('visualizar boletos')) { ?>                               
                                    <li>
                                        <a class="dropdown-item current-page" href="/consumos/produto">
                                            <span>Boletos</span>
                                        </a>
                                    </li>
                                    <?php } if (hasPermission('visualizar mensalidades')) { ?>
                                    <li>
                                        <a class="dropdown-item current-page" href="/consumos/diaria">
                                            <span>Mensalidade</span>
                                        </a>
                                    </li>
                                    <?php } if (hasPermission('visualizar planos')) { ?>
                                    <li>
                                        <a class="dropdown-item current-page" href="/planos">
                                            <span>Planos</span>
                                        </a>
                                    </li>
                                    <? }?>
                                </ul>
                            </li>
                        <?php } if (hasPermission('visualizar cadastro')) { ?>
                            <li class="nav-item dropdown ">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-add_task"></i>Parametros
                                </a>
                                <ul class="dropdown-menu">
                                    
                                <?php if (hasPermission('criar usuários')) { ?>
                                    <li>
                                        <a class="dropdown-item" href="/usuario/">
                                            <span>Usuário</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/permissao/">
                                            <span>Permissões</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (hasPermission('visualizar afiliados')) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href=""><i class="icon-supervised_user_circle"></i> Afiliados
                                </a>
                            </li>
                        <? } ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning" href="/logout">Sair</a>
                            </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- App Navbar ends -->