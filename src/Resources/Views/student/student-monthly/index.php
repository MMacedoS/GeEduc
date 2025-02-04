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
                <i class="icon-person lh-1"></i>
                <a href="\estudantes" class="text-decoration-none">Estudantes</a>
            </li>
            <li class="breadcrumb-item">Estudante & Mensalidade</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar mensalidades')) {?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a href="\estudantes\<?=$estudante->uuid?>\mensalidade" class="btn btn-outline-primary" > + </a>
            </div>
        </div>
    <? }?>

    <? if(isset($_GET['error'])){?>
        <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
            <b>Sem permissão!</b>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <? }?>
</div>
    <!-- Row end -->
<? if(isset($success)){?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
<? if(isset($danger)){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
    <!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Data Vencimento</th>
                                    <th>Valor</th>
                                    <th>Situação</th>
                                    <? if (hasPermission('editar mensalidade') || hasPermission('cancelar mensalidades')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                            <? 
                                if (!is_null($estudante_mensalidades) && !empty($estudante_mensalidades)) {
                                foreach (getJsonToObject($estudante_mensalidades[0]->mensalidades) as $estudante_mensalidade) {
                                ?>
                                    <tr>
                                        <td><?=$estudante_mensalidade->id?></td>
                                        <td class="fw-bold"> <?=brDate($estudante_mensalidade->data_vencimento)?>
                                        </td>
                                        <td>
                                        <?=brCurrency($estudante_mensalidade->valor)?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <? if($estudante_mensalidade->situacao == 'atrasado') { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Vencido
                                                <? } ?>
                                                <? if($estudante_mensalidade->situacao == 'pendente') { ?>
                                                    <i class="icon-circle1 me-2 text-warning fs-5"></i>
                                                    Aberto
                                                <? } ?>
                                                <? if($estudante_mensalidade->situacao == 'cancelado') { ?>
                                                    <i class="icon-circle1 me-2 text-secondary fs-5"></i>
                                                    Cancelado
                                                <? } ?>
                                                <? if($estudante_mensalidade->situacao == 'pago') { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Efetivado
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar mensalidade') && hasPermission('cancelar mensalidades')) {?>
                                            <td class="d-flex">
                                                <? if (
                                                    hasPermission('cancelar mensalidades') &&
                                                    (
                                                        $estudante_mensalidade->situacao !== 'cancelado' &&
                                                        $estudante_mensalidade->situacao !== 'pago'
                                                    )
                                                ) {?>
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$estudante_mensalidade->uuid?>">
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-delete1"></span>
                                                        </div>
                                                    </button>
                                                <? }?>
                                                <div class="modal fade" id="exampleModal_<?=$estudante_mensalidade->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir este registro?
                                                                <p>mensalidade: <?=brDate($estudante_mensalidade->data_vencimento)?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                                <button type="button" onclick="deleteData('/estudantes/<?=$estudante->uuid?>/mensalidade/<?=$estudante_mensalidade->uuid?>')" class="btn btn-danger">Confirmar Cancelamento</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <? if (hasPermission('editar mensalidade')) {?>
                                                    <a class="mb-1 me-2 mt-1" href="/estudantes/<?=$estudante->uuid?>/mensalidade/<?=$estudante_mensalidade->uuid?>/">
                                                        <div class="border p-2 rounded-3">
                                                            <i class="icon-edit fs-5"></i>
                                                        </div>
                                                    </a>
                                                <? }?>
                                            </td>
                                        <? }?>
                                    </tr>
                            <? }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        <?  if (!is_null($estudante_mensalidades) && !empty($estudante_mensalidades)) { ?>
                        Total <b><?@count(getJsonToObject($estudante_mensalidades[0]->mensalidades))?></b> registros
                        <? }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?=$links?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>
