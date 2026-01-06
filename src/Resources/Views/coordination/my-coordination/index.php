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
            <li class="breadcrumb-item">Minha Coordenação</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
</div>
<!-- Row end -->

<!-- Filtro de Ano Letivo -->
<div class="row gx-3 mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" action="/minha-coordenacao" class="d-flex align-items-center gap-3">
                    <label for="school_year" class="mb-0 text-nowrap">
                        <i class="icon-calendar me-1"></i>Ano Letivo:
                    </label>
                    <select name="school_year" id="school_year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <?php
                        $startYear = $current_year - 5;
                        $endYear = $current_year + 1;
                        for ($year = $endYear; $year >= $startYear; $year--) : ?>
                            <option value="<?= $year ?>" <?= $school_year == $year ? 'selected' : '' ?>>
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">
                        Mostrando: <strong><?= $school_year ?></strong>
                    </small>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (isset($success)) { ?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<?php if (isset($danger)) { ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b>Danger!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <form id="coordinators-form" action="/coordenadores" method="GET">
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                        <button class=" bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filters-coordinators" aria-expanded="false"
                            aria-controls="collapseSpecialTitleTwo">
                            <h5 class="m-0">Filtros</h5>
                        </button>
                    </h2>
                    <div id="filters-coordinators" class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse' ?>"
                        aria-labelledby="headingSpecialTitleTwo" data-bs-parent="#accordionSpecialTitle">
                        <div class="accordion-body">
                            <div class="row justify-content-start">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Busca por nome ou email</label>
                                                <input
                                                    class="form-input form-control"
                                                    type="text"
                                                    name="name_email"
                                                    id="name_email"
                                                    value="<?= isset($searchFilter) ? $searchFilter : null ?>"
                                                    placeholder="Digite nome ou email">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Situação</label>
                                                <select class="form-select form-control" name="situation" id="situation">
                                                    <option <?= (isset($situation) && $situation == '') ? 'selected' : '' ?> value="">Ambas</option>
                                                    <option value="1" <?= (isset($situation) && $situation == 1) ? 'selected' : '' ?>>Disponível</option>
                                                    <option value="0" <?= (isset($situation) && $situation == 0) ? 'selected' : '' ?>>Impedido</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-12">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                <a href="\coordenadores" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) ? 'd-block' : 'd-none' ?>">Limpar</a>
                                                <button type="submit" class="btn btn-primary">Buscar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row gx-3">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-school me-2"></i>Minhas Turmas
            </h5>
            <span class="badge bg-primary">Total: <?= count($turmas) ?></span>
        </div>
    </div>

    <? foreach ($turmas as $turma) {
        $turmaDetails = getJsonToObject($turma->turma_details);
    ?>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="card h-100 shadow-sm" style="transition: all 0.3s ease; border-left: 4px solid #6c757d;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white;">
                    <h6 class="mb-0 fw-bold">
                        <i class="icon-book me-2"></i><?= $turmaDetails->nome ?? 'Não identificado' ?>
                    </h6>
                    <? if ($turmaDetails->ativo == 1) { ?>
                        <span class="badge bg-success">
                            <i class="icon-check_circle"></i>
                        </span>
                    <? } else { ?>
                        <span class="badge bg-danger">
                            <i class="icon-block"></i>
                        </span>
                    <? } ?>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-calendar me-2 fs-6"></i>
                                    <strong>Turma:</strong>&nbsp;<?= $turmaDetails->nome ?? 'Não identificado' ?>
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-schedule me-2 fs-6"></i>
                                    <strong>Turno:</strong>&nbsp;<?= $turmaDetails->turno ?? 'N/A' ?>
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-tag me-2 fs-6"></i>
                                    <strong>ID:</strong>&nbsp;<?= $turmaDetails->id ?>
                                </small>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-date_range me-2 fs-6"></i>
                                    <strong>Ano Letivo:</strong>&nbsp;<?= $turmaDetails->ano_letivo ?? 'N/A' ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <? if (hasPermission('editar_disciplina')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/disciplinas?school_year=<?= $school_year ?>"
                                class="btn btn-sm btn-outline-primary flex-fill"
                                title="Disciplinas">
                                <i class="icon-subject me-1"></i>Disciplinas
                            </a>
                        <? } ?>
                        <? if (hasPermission('editar_disciplina')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/disciplinas?school_year=<?= $school_year ?>"
                                class="btn btn-sm btn-outline-secondary flex-fill"
                                style="border-color: #86A789; color: #86A789;"
                                title="Notas">
                                <i class="icon-edit me-1"></i>Notas
                            </a>
                        <? } ?>
                        <? if (hasPermission('coordenador')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/estudantes"
                                class="btn btn-sm btn-outline-secondary flex-fill"
                                style="border-color: #86A789; color: #86A789;"
                                title="Frequência">
                                <i class="icon-calendar_today me-1"></i>Frequência
                            </a>
                        <? } ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <? if (hasPermission('coordenador')) { ?>
                            <a href="/relatorios/turma/<?= $turmaDetails->uuid ?>/boletins?school_year=<?= $school_year ?>"
                                class="btn btn-sm btn-outline-primary flex-fill"
                                target="_blank"
                                title="Grade">
                                <i class="icon-grid_on me-1"></i>Grade
                            </a>
                        <? } ?>
                        <? if (hasPermission('coordenador')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/recuperacoes"
                                class="btn btn-sm btn-outline-warning flex-fill"
                                title="Atividades">
                                <i class="icon-assignment me-1"></i>Atividades
                            </a>
                        <? } ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <? if (hasPermission('coordenador')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/recuperacoes"
                                class="btn btn-sm btn-outline-danger flex-fill"
                                title="Recuperação">
                                <i class="icon-sync_problem me-1"></i>Recuperação
                            </a>
                        <? } ?>
                        <? if (hasPermission('coordenador')) { ?>
                            <a href="/minha-coordenacao/turma/<?= $turmaDetails->uuid ?>/visibilidade"
                                class="btn btn-sm btn-outline-info flex-fill"
                                title="Visibilidade">
                                <? if ($turmaDetails->visivel == 0) { ?>
                                    <i class="icon-eye-off me-1"></i>Oculto
                                <? } else { ?>
                                    <i class="icon-eye me-1"></i>Visível
                                <? } ?>
                            </a>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</div>

<div class="row">
    <div class="float-end">
        <?= $links ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>