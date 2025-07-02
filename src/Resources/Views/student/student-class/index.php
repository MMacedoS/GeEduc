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
            <li class="breadcrumb-item">Estudante & Turma</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('vincular turmas e estudantes')) {?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#linkClass"> + </a>
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
                                    <th>Nome</th>
                                    <th>Ano Letivo</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Situação</th>
                                    <? if (hasPermission('editar professores') || hasPermission('deletar professores')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($estudante_turma as $turma_estudante) { 
                                ?>
                                    <tr>
                                        <td><?=$turma_estudante->id?></td>
                                        <td class="fw-bold"> <?=getJsonToObject($turma_estudante->turma)->nome ?? 'não identificado'?>
                                        </td>
                                        <td>
                                        <?=$turma_estudante->ano_letivo?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">    
                                            <div class="d-flex align-items-center">
                                                <? if($turma_estudante->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($turma_estudante->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('inativar vinculos')) {?>
                                            <td class="d-flex">                                                 
                                                <? if (hasPermission('inativar vinculos')) {?>                                                                           
                                                    <button class="btn btn-outline btn-sm" type="button" onclick="inactivateLink('<?=$turma_estudante->uuid?>')">                                                     
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-power_settings_new"></span>
                                                        </div>
                                                    </button>
                                                <? }?>                                                                                        
                                            </td>
                                        <? }?>
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($estudante_turma)?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="linkClass" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Vincular Turma ao <?=getJsonToObject($estudante->pessoa_fisica)->nome ?? 'não identificado'?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 col-sm-12 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="m-0">
                                <label class="form-label">Turmas</label>
                                <select class="form-select" name="class_id" id="class_id">
                                    <option value="">Selecione uma turma</option>
                                    <?php foreach ($turmas as $key => $value) {?>
                                        <option value="<?=$value->uuid?>"><?=$value->nome?></option>
                                   <? } ?>
                                </select>
                                <div class="invalid-feedback" id="type_doc_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" disabled id="linkBtn" onclick="linkClass('<?=$estudante->uuid?>')" class="btn btn-danger">vincular turma</button>
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

<script>
    $('#class_id').on('change', function() {
        $(this).val() ?  $('#linkBtn').attr('disabled', false) :  $('#linkBtn').attr('disabled', true);;
    });

    function linkClass(student) {
        const classId = $('#class_id').val();

        if (!classId) {
            showSuccessMessage('Selecione uma turma antes de prosseguir.');
            return;
        }

        if (!student) {
            showSuccessMessage('Estudante não especificado.');
            return;
        }

        const url = '/estudantes/' + student + '/turma/' + classId;
        createData(url, '').then((res) => {
            if (res.status === 422) {
                showErrorMessage(res.message);
                return;
            }
            showSuccessMessage(res.message);
            return;
        });
    }

    function inactivateLink(studentClass) {
        const url = '/estudantes-class/' + studentClass;
        sendRequestWithMethod(url,'', 'PUT').then((res) => {
            if (res.status === 422) {
                showErrorMessage(res.message);
                return;
            }
            showSuccessMessage(res.message);
            return;
        });
    }
</script>
