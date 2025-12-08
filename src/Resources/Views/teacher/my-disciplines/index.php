<?php require_once __DIR__ . '/../../layout/top.php'; ?>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\meus-componentes" class="text-decoration-none">Meus Componentes</a>
            </li>
            <li class="breadcrumb-item">Meus Componentes Curriculares</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar_turmas_estudantes')) { ?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#linkClass"> + </a>
            </div>
        </div>
    <? } ?>

    <? if (isset($_GET['error'])) { ?>
        <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
            <b>Sem permissão!</b>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <? } ?>
</div>
<!-- Row end -->
<? if (isset($success)) { ?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>
<? if (isset($danger)) { ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b>Danger!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>

<div class="row gx-3">
    <? if (empty($disciplinas)) { ?>
        <div class="col-12">
            <div class="card mb-3 text-center">
                <div class="card-body py-5">
                    <i class="icon-book fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma disciplina encontrada</h5>
                    <p class="text-muted">Você ainda não possui componentes curriculares cadastrados.</p>
                </div>
            </div>
        </div>
    <? } else { ?>
        <? foreach ($disciplinas as $disciplina) { ?>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="card mb-3 h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="icon-book me-2"></i>
                            <?= getJsonToObject($disciplina->professor_disciplina)->disciplina->nome ?? 'Não identificado' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                <i class="icon-people me-1"></i>Turma
                            </small>
                            <p class="mb-0 fw-semibold">
                                <?= getJsonToObject($disciplina->turma)->nome ?? 'Não identificado' ?>
                            </p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                <i class="icon-calendar me-1"></i>Ano Letivo
                            </small>
                            <p class="mb-0 fw-semibold">
                                <?= $disciplina->ano_letivo ?? 'Não identificado' ?>
                            </p>
                        </div>
                        <hr class="my-3">
                        <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor')) { ?>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <? if (hasPermission('inserir notas') || hasPermission('professor')) { ?>
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="/meus-componentes/<?= $disciplina->uuid ?>/notas"
                                        data-bs-toggle="tooltip"
                                        title="Gerenciar Notas">
                                        <i class="icon-edit"></i>
                                        <span class="d-none d-sm-inline ms-1">Notas</span>
                                    </a>
                                <? } ?>
                                <? if (hasPermission('realizar chamadas') || hasPermission('professor')) { ?>
                                    <a class="btn btn-sm btn-outline-success"
                                        href="/meus-componentes/<?= $disciplina->uuid ?>/frequencia"
                                        data-bs-toggle="tooltip"
                                        title="Gerenciar Frequência">
                                        <i class="icon-calendar"></i>
                                        <span class="d-none d-sm-inline ms-1">Frequência</span>
                                    </a>
                                <? } ?>
                                <? if (hasPermission('professor')) { ?>
                                    <a class="btn btn-sm btn-outline-info"
                                        href="/relatorios/<?= $disciplina->uuid ?>/gerar-grade"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        data-bs-toggle="tooltip"
                                        title="Gerar Grade">
                                        <i class="icon-file"></i>
                                        <span class="d-none d-sm-inline ms-1">Grade</span>
                                    </a>
                                    <a class="btn btn-sm btn-outline-warning"
                                        href="/meus-componentes/<?= getJsonToObject($disciplina->turma)->uuid ?>/disciplina/<?= $disciplina->uuid ?>/atividades"
                                        data-bs-toggle="tooltip"
                                        title="Gerenciar Atividades">
                                        <i class="icon-link"></i>
                                        <span class="d-none d-sm-inline ms-1">Atividades</span>
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger"
                                        href="/meus-componentes/turma/<?= getJsonToObject($disciplina->turma)->uuid ?>/disciplina/<?= $disciplina->uuid ?>/recuperacoes"
                                        data-bs-toggle="tooltip"
                                        title="Gerenciar Recuperação">
                                        <i class="icon-sync_problem"></i>
                                        <span class="d-none d-sm-inline ms-1">Recuperação</span>
                                    </a>
                                <? } ?>
                            </div>
                        <? } ?>
                    </div>
                    <div class="card-footer bg-light text-muted small">
                        <i class="icon-tag me-1"></i>ID: <?= $disciplina->id ?>
                    </div>
                </div>
            </div>
        <? } ?>
    <? } ?>
</div>

<? if (!empty($disciplinas)) { ?>
    <div class="row gx-3 mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Total de <strong><?= count($disciplinas) ?></strong>
                                <?= count($disciplinas) === 1 ? 'disciplina' : 'disciplinas' ?>
                            </small>
                        </div>
                        <div>
                            <?= $links ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? } ?>

<style>
    .hover-shadow-lg {
        transition: all 0.3s ease;
    }

    .hover-shadow-lg:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }

    .card-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.375rem 0.5rem;
    }

    @media (max-width: 576px) {
        .btn-sm span {
            display: none !important;
        }
    }
</style>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>