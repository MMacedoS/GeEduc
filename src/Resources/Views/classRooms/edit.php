<?php require_once __DIR__ . '/../layout/top.php'; ?>


<!-- Row start -->
<div class="row gx-3">
        <div class="col-8 col-xl-6">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <i class="icon-house_siding lh-1"></i>
                    <a href="/" class="text-decoration-none">Início</a>
                </li>
                <li class="breadcrumb-item">
                    <i class="fs-3 icon-archive lh-1"></i>
                    <a href="/turmas/" class="text-decoration-none">Turmas</a>
                </li>
                <li class="breadcrumb-item">Atualizar</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="/turmas/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <form action="/turma/<?=$turma->uuid?>" method="post" enctype="multipart/form-data">   
        <div class="row gx-3">
            <? include_once('_forms.php');?>
        </div>
    </form>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
<script>
    $(document).ready(function() {
   
    // Atualiza o campo oculto com os IDs selecionados
    $('#coordinator_id').on('change', function() {
        var selectedIds = $(this).val(); // Pega os IDs selecionados
        $('#coordinator_ids').val(selectedIds.join(',')); // Atualiza o campo oculto
    });

    // Pré-seleciona os valores no Select2 (se houver)
    var initialIds = $('#coordinator_ids').val().split(','); // Pega os IDs do campo oculto
    if (initialIds.length > 0 && initialIds[0] !== '') {
        $('#coordinator_id').val(initialIds).trigger('change'); // Pré-seleciona os valores
    }
});
</script>
<script>
$(document).ready(function() {
    $('#multiple_form').select2({
        placeholder: "Selecione um ou mais componentes",
        width: '100%'
    });
});
</script>