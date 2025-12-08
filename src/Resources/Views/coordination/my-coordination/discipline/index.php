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
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Disciplina</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Professor</th>
                                    <th class="text-center">Ano Letivo</th>
                                    <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                                        <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <? foreach ($disciplinas as $disciplina) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $disciplina->code ?></td>
                                        <td class="fw-bold text-center">
                                            <?= $disciplina->subject_name ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?= $disciplina->teacher_name ?? 'não identificado' ?>
                                        </td>
                                        <td class="text-center"> <?= $disciplina->school_year ?? 'não identificado' ?>
                                        </td>
                                        <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                                            <td class="d-flex">
                                                <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                    <? if (hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= $disciplina->id ?>/notas">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Notas">
                                                                <i class="icon-edit fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('realizar chamadas') || hasPermission('professor')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= $disciplina->id ?>/frequencia">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Frequência">
                                                                <i class="icon-calendar fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/relatorios/<?= $disciplina->id ?>/gerar-grade" target="_blank" rel="noopener noreferrer ">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Gerar grade">
                                                                <i class="icon-file fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('professor')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/atividades">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="atividades">
                                                                <i class="icon-link fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/recuperacoes">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Recuperações">
                                                                <i class="icon-sync_problem fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                </div>
                                                <div class="d-block d-xl-none d-lg-none d-md-none dropdown ms-3">
                                                    <a class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-menu"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <div class="header-action-links float-end">
                                                            <? if (hasPermission('inserir notas') || hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= $disciplina->id ?>/notas">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Notas">
                                                                        <i class="icon-edit fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('realizar chamadas') || hasPermission('professor')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= $disciplina->id ?>/frequencia">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Frequência">
                                                                        <i class="icon-calendar fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/relatorios/<?= $disciplina->id ?>/gerar-grade" target="_blank" rel="noopener noreferrer ">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Gerar grade">
                                                                        <i class="icon-file fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('professor')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/atividades">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="atividades">
                                                                        <i class="icon-link fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('professor') || hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/minha-coordenacao/turma/<?= $disciplina->class_uuid ?>/disciplina/<?= $disciplina->id ?>/recuperacoes">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="atividades">
                                                                        <i class="icon-sync_problem fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        <? } ?>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?= count($disciplinas) ?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?= $links ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>