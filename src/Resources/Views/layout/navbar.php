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
                            <a class="nav-link dropdown-toggle" href="/dashboard" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-stacked_line_chart"></i> Dashboards
                            </a>
                            <ul class="dropdown-menu">
                                <!-- <li>
                                    <a class="dropdown-item current-page" href="/dashboard/analytics">
                                        <span>Analytics</span>
                                    </a>
                                </li> -->
                                <li>
                                    <a class="dropdown-item current-page" href="/dashboard">
                                        <span>Facilidades</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php if (hasPermission('visualizar pedagogico')) { ?>
                        <li class="nav-item dropdown <?=$active === 'pedagogico' ? 'active-link': ''?>">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-add_task"></i> Pedagógico
                            </a>
                            <ul class="dropdown-menu">                             
                            <?php if (hasPermission('visualizar periodos')) { ?>
                                <li>
                                    <a class="dropdown-item" href="\periodos\">
                                        <span>Trimestres</span>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (hasPermission('visualizar coordenadores')) { ?>
                                <li>
                                    <a class="dropdown-item" href="\coordenadores">
                                        <span>Coordenadores</span>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (hasPermission('visualizar carga_horaria')) { ?>
                                <li>
                                    <a class="dropdown-item" href="\carga-horaria">
                                        <span>Carga Horaria</span>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (hasPermission('visualizar disciplinas')) { ?>
                                <li>
                                    <a class="dropdown-item" href="\disciplinas\">
                                        <span>Disciplinas</span>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if (hasPermission('visualizar estudantes')) { ?>
                                <li>
                                    <a class="dropdown-item" href="\estudantes\">
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
                                        <a class="dropdown-item current-page" href="/mensalidades">
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
                            <?php } if (hasPermission('visualizar site')) {?>
                            <li class="nav-item dropdown <?=$active === 'financeiro' ? 'active-link': ''?>">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-now_widgets"></i> Site
                                </a>
                                <ul class="dropdown-menu"> 
                                    <?php if (hasPermission('visualizar site')) { ?>
                                        <li>
                                            <a class="dropdown-item" href="/site-albuns/">
                                                <span>Albuns</span>
                                            </a>
                                            <a class="dropdown-item" href="/site-carrossel/">
                                                <span>Carrossel</span>
                                            </a>
                                            <a class="dropdown-item" href="/site-eventos/">
                                                <span>Eventos</span>
                                            </a>
                                            

                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } if (hasPermission('criar usuários')) { ?>
                            <li class="nav-item dropdown ">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-add_task"></i>Parâmetros
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
                                    <li>
                                        <a class="dropdown-item" href="/pessoas/">
                                            <span>Pessoa Contato</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (hasPermission('estudante')) { ?>
                            <li class="nav-item <?=$active === 'students' ? 'active-link': ''?>">
                                <a class="nav-link" href="/minhas-turmas"><i class="icon-book-open"></i> Minha Turma
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href=""><i class="icon-supervised_user_circle"></i> Contrato
                                </a>
                            </li>
                        <? } ?>
                        <?php if (hasPermission('professor')) { ?>
                            <li class="nav-item <?=$active === 'teacher' ? 'active-link': ''?>">
                                <a class="nav-link" href="/meus-componentes"><i class="icon-book-open"></i> Minhas Disciplinas
                                </a>
                            </li>
                        <? } ?>
                        <?php if (hasPermission('responsavel_legal')) { ?>
                            <li class="nav-item <?=$active === 'responsible_legal' ? 'active-link': ''?>">
                                <a class="nav-link" href="/minha-galerinha"><i class="icon-book-open"></i> Minha Galerinha
                                </a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="/minha-galerinha/contratos"><i class="icon-supervised_user_circle"></i> Contrato
                                </a>
                            </li> -->
                        <? } ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning" href="/logout">Sair</a>
                            </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- App Navbar ends -->