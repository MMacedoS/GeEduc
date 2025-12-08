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

    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Turno</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Situação</th>
                                    <? if (hasPermission('editar_coordenador') || hasPermission('deletar_coordenador')) { ?>
                                        <th class="float-end me-5">Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?
                                foreach ($turmas as $turma) {
                                ?>
                                    <tr>
                                        <td><?= getJsonToObject($turma->turma_details)->id ?></td>
                                        <td class="fw-bold"> <?= getJsonToObject($turma->turma_details)->nome ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell"> <?= getJsonToObject($turma->turma_details)->turno ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <div class="d-flex align-items-center">
                                                <? if (getJsonToObject($turma->turma_details)->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if (getJsonToObject($turma->turma_details)->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar_disciplina')) { ?>
                                            <td>
                                                <div class="d-none d-xl-flex d-lg-flex d-md-flex float-end">
                                                    <? if (hasPermission('editar_disciplina')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/disciplinas">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-link fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/relatorios/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/boletins" target="_blank">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Boletim">
                                                                <i class="icon-archive fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/visibilidade">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="Boletim">
                                                                <? if (getJsonToObject($turma->turma_details)->visivel == 0) { ?>
                                                                    <i class="icon-eye-off fs-5"></i>
                                                                <? } ?>
                                                                <? if (getJsonToObject($turma->turma_details)->visivel == 1) { ?>
                                                                    <i class="icon-eye fs-5"></i>
                                                                <? } ?>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/estudantes">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="estudantes">
                                                                <i class="icon-list fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('coordenador')) { ?>
                                                        <a class="mb-1 me-2 mt-1"
                                                            href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/recuperacoes">
                                                            <div class="border p-2 rounded-3" data-toggle="tooltip" title="atividades">
                                                                <i class="icon-sync_problem fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                </div>

                                                <div class="d-block d-xl-none d-lg-none d-md-none dropdown ms-3 float-end me-5">
                                                    <a class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-menu"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <div class="header-action-links float-end">
                                                            <? if (hasPermission('editar_disciplina')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/disciplinas">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-link fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/relatorios/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/boletins" target="_blank">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Boletim">
                                                                        <i class="icon-archive fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/visibilidade">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Boletim">
                                                                        <? if (getJsonToObject($turma->turma_details)->visivel == 0) { ?>
                                                                            <i class="icon-eye-off fs-5"></i>
                                                                        <? } ?>
                                                                        <? if (getJsonToObject($turma->turma_details)->visivel == 1) { ?>
                                                                            <i class="icon-eye fs-5"></i>
                                                                        <? } ?>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/estudantes">
                                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="estudantes">
                                                                        <i class="icon-list fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>

                                                            <? if (hasPermission('coordenador')) { ?>
                                                                <a class="mb-1 me-2 mt-1"
                                                                    href="/minha-coordenacao/turma/<?= getJsonToObject($turma->turma_details)->uuid ?>/recuperacoes">
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
                        Total <b><?= count($turmas) ?></b> registros
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

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>