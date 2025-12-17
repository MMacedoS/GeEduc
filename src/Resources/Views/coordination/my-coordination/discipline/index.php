<?php require_once __DIR__ . '/../../../layout/top.php'; ?>

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
                <a href="\minha-coordenacao" class="text-decoration-none">Minha Coordenação</a>
            </li>
            <li class="breadcrumb-item">Disciplinas Turma: <?= $turma->nome ?? 'não identificado' ?></li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar_atividade')) { ?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="\minha-coordenacao\turma\<?= $turma->uuid ?>\atividades" class="btn btn-outline-primary"> + </a>
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
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-book me-2"></i>Disciplinas da Turma
            </h5>
            <span class="badge bg-primary">Total: <?= count($disciplinas) ?></span>
        </div>
    </div>

    <? foreach ($disciplinas as $disciplina) { ?>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="card h-100 shadow-sm" style="transition: all 0.3s ease; border-left: 4px solid #86A789;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #86A789 0%, #739975 100%); color: white;">
                    <h6 class="mb-0 fw-bold">
                        <i class="icon-subject me-2"></i><?= $disciplina->subject_name ?? 'Não identificado' ?>
                    </h6>
                    <span class="badge bg-white text-dark">
                        #<?= $disciplina->code ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-person me-2 fs-6"></i>
                                    <strong>Professor:</strong>&nbsp;<?= $disciplina->teacher_name ?? 'Não identificado' ?>
                                </small>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="icon-date_range me-2 fs-6"></i>
                                    <strong>Ano Letivo:</strong>&nbsp;<?= $disciplina->school_year ?? 'Não identificado' ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                        <div class="d-flex flex-wrap gap-2">
                            <? if (hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                                <a href="/minha-coordenacao/turma/<?= $disciplina->id ?>/notas"
                                    class="btn btn-sm btn-outline-secondary flex-fill"
                                    style="border-color: #86A789; color: #86A789;"
                                    title="Notas">
                                    <i class="icon-edit me-1"></i>Notas
                                </a>
                            <? } ?>
                            <? if (hasPermission('realizar chamadas') || hasPermission('professor')) { ?>
                                <a href="/minha-coordenacao/turma/<?= $disciplina->id ?>/frequencia"
                                    class="btn btn-sm btn-outline-secondary flex-fill"
                                    style="border-color: #86A789; color: #86A789;"
                                    title="Frequência">
                                    <i class="icon-calendar_today me-1"></i>Frequência
                                </a>
                            <? } ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                                <a href="/relatorios/<?= $disciplina->id ?>/gerar-grade"
                                    class="btn btn-sm btn-outline-primary flex-fill"
                                    target="_blank"
                                    title="Grade">
                                    <i class="icon-grid_on me-1"></i>Grade
                                </a>
                            <? } ?>
                            <? if (hasPermission('professor')) { ?>
                                <a href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/atividades"
                                    class="btn btn-sm btn-outline-warning flex-fill"
                                    title="Atividades">
                                    <i class="icon-assignment me-1"></i>Atividades
                                </a>
                            <? } ?>
                        </div>

                        <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/recuperacoes"
                                    class="btn btn-sm btn-outline-danger flex-fill"
                                    title="Recuperação">
                                    <i class="icon-sync_problem me-1"></i>Recuperação
                                </a>
                            </div>
                        <? } ?>
                    <? } ?>
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

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>