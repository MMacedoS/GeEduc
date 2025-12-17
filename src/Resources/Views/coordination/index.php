<?php require_once __DIR__ . '/../layout/top.php'; ?>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Coordenadores</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar_coordenador')) { ?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="\coordenador" class="btn btn-outline-primary"> + </a>
            </div>
        </div>
    <? } ?>
</div>
<!-- Row end -->

<!-- Alertas -->
<? if (isset($success)) { ?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <i class="icon-check_circle me-2"></i>
        <b>Sucesso!</b> Operação realizada com sucesso.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>
<? if (isset($danger)) { ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <i class="icon-cancel me-2"></i>
        <b>Erro!</b> Ocorreu um problema na operação.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>

<!-- Filtros -->
<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-filter_list me-2"></i>Filtros de Pesquisa
                </h5>
            </div>
            <div class="card-body">
                <form id="coordinators-form" action="/coordenadores" method="GET">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-6 col-12">
                            <label class="form-label">Busca por nome ou email</label>
                            <input
                                class="form-control"
                                type="text"
                                name="name_email"
                                id="name_email"
                                value="<?= isset($searchFilter) ? $searchFilter : '' ?>"
                                placeholder="Digite nome ou email">
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label">Situação</label>
                            <select class="form-select" name="situation" id="situation">
                                <option <?= (isset($situation) && $situation == '') ? 'selected' : '' ?> value="">Todas</option>
                                <option value="1" <?= (isset($situation) && $situation == 1) ? 'selected' : '' ?>>Disponível</option>
                                <option value="0" <?= (isset($situation) && $situation == 0) ? 'selected' : '' ?>>Impedido</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="icon-search me-1"></i>Buscar
                                </button>
                                <a href="\coordenadores" class="btn btn-secondary flex-fill <?= isset($situation) || isset($searchFilter) ? '' : 'd-none' ?>">
                                    <i class="icon-refresh me-1"></i>Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Coordenadores -->
<div class="row gx-3">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-people me-2"></i>Lista de Coordenadores
            </h5>
            <span class="badge bg-primary">Total: <?= count($data['coordenadores']) ?></span>
        </div>
    </div>

    <? foreach ($data['coordenadores'] as $coordenador) { ?>
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card mb-3 h-100 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar rounded-circle bg-primary text-white me-3" style="width: 56px; height: 56px; min-width: 56px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                            <?= strtoupper(substr(getJsonToObject($coordenador->pessoa_fisica)->nome ?? 'N', 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-1 fw-bold text-truncate" title="<?= getJsonToObject($coordenador->pessoa_fisica)->nome ?? 'Não identificado' ?>">
                                <?= getJsonToObject($coordenador->pessoa_fisica)->nome ?? 'Não identificado' ?>
                            </h6>
                            <small class="text-muted">#<?= $coordenador->id ?></small>
                        </div>
                        <? if ($coordenador->ativo == 1) { ?>
                            <span class="badge bg-success ms-2">
                                <i class="icon-check_circle"></i>
                            </span>
                        <? } else { ?>
                            <span class="badge bg-danger ms-2">
                                <i class="icon-block"></i>
                            </span>
                        <? } ?>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-flex align-items-center">
                            <i class="icon-email me-2 fs-6"></i>
                            <span class="text-truncate" title="<?= getJsonToObject($coordenador->pessoa_fisica)->email ?? 'Não informado' ?>">
                                <?= getJsonToObject($coordenador->pessoa_fisica)->email ?? 'Não informado' ?>
                            </span>
                        </small>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted">
                            <i class="icon-circle1 me-1 <?= $coordenador->ativo == 1 ? 'text-success' : 'text-danger' ?>"></i>
                            <?= $coordenador->ativo == 1 ? 'Disponível' : 'Impedido' ?>
                        </small>

                        <? if (hasPermission('editar_coordenador') || hasPermission('deletar_coordenador')) { ?>
                            <div class="d-flex gap-2">
                                <? if (hasPermission('editar_coordenador')) { ?>
                                    <a href="/coordenador/<?= $coordenador->uuid ?>" class="text-decoration-none" title="Editar">
                                        <div class="border p-2 rounded-3 hover-bg-primary" style="transition: all 0.2s;">
                                            <i class="icon-edit fs-6"></i>
                                        </div>
                                    </a>
                                <? } ?>
                                <? if (hasPermission('deletar_coordenador')) { ?>
                                    <a href="#" class="text-decoration-none text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_<?= $coordenador->uuid ?>" title="Excluir">
                                        <div class="border border-danger p-2 rounded-3 hover-bg-danger" style="transition: all 0.2s;">
                                            <i class="icon-delete1 fs-6"></i>
                                        </div>
                                    </a>
                                <? } ?>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Exclusão -->
        <div class="modal fade" id="deleteModal_<?= $coordenador->uuid ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="icon-warning me-2"></i>Confirmação de Exclusão
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Tem certeza que deseja excluir este coordenador?</p>
                        <div class="alert alert-warning">
                            <strong><?= getJsonToObject($coordenador->pessoa_fisica)->nome ?? 'Não identificado' ?></strong><br>
                            <small><?= getJsonToObject($coordenador->pessoa_fisica)->email ?? 'Não informado' ?></small>
                        </div>
                        <p class="text-danger mb-0"><small><i class="icon-info me-1"></i>Esta ação não pode ser desfeita.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="icon-close me-1"></i>Cancelar
                        </button>
                        <button type="button" onclick="deleteData('/coordenador/<?= $coordenador->uuid ?>')" class="btn btn-danger">
                            <i class="icon-delete1 me-1"></i>Confirmar Exclusão
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</div>

<div class="row">
    <div class="float-end">
        <?= $data['links'] ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>