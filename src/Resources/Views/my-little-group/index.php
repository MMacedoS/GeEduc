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

            <li class="breadcrumb-item">Minha Galerinha</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
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
<!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Estudantes</h5>
            </div>
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Estudante</th>
                                    <th>email</th>
                                    <th>Situação</th>
                                    <? if (hasPermission('responsavel_legal')) { ?>
                                        <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <? foreach ($data['estudantes'] as $estudante) {
                                    $estudante = (object)$estudante;
                                ?>
                                    <tr>
                                        <td><?= $estudante->code ?></td>
                                        <td class="fw-bold"> <?= $estudante->student_name ?>
                                        </td>
                                        <td>
                                            <?= $estudante->email ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <? if ($estudante->active == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if ($estudante->active == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('responsavel_legal')) { ?>
                                            <td class="d-flex">
                                                <a class="mb-1 me-2 mt-1" href="minha-galerinha/estudante/<?= $estudante->id ?>">
                                                    <div class="border p-2 rounded-3" data-toggle="tooltip" title="Historico">
                                                        <i class="icon-file-text fs-5"></i>
                                                    </div>
                                                </a>
                                            <? } ?>
                                            </td>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?= count($estudantes) ?></b> registros
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

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>