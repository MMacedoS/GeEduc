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
                    <a href="/estudantes/" class="text-decoration-none">Estudantes</a>
                </li>
                <li class="breadcrumb-item">Atualizar</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a href="/estudantes/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <form action="/estudante/<?=$estudante->uuid?>" method="post" enctype="multipart/form-data">   
        <div class="row gx-3">
            <? include_once('_forms.php');?>
        </div>
    </form>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
<script>
   $(document).ready(function () {
        $('#responsible_search').on('input', function () {
            var searchTerm = $(this).val();

            if (searchTerm.length < 3) {
                $('#responsible_suggestions').hide();
                $('#redirect_button').hide();
                return;
            }

            $.ajax({
                url: '/pessoas-lista',
                method: 'GET',
                data: { search: searchTerm },
                dataType: 'JSON',
                success: function (response) {
                    console.log(Array.isArray(response)); 
                    var suggestions = '';

                    if (response && Array.isArray(response) && response.length > 0) {
                        response.forEach(function(item) {
                            var pessoaFisica = JSON.parse(item.pessoa_fisica);

                            suggestions += '<li class="list-group-item" data-id="' + item.id + '" data-pessoa-fisica-id="' + item.pessoa_fisica_id + '">';
                            suggestions += pessoaFisica.nome + ' (' + pessoaFisica.email + ')';
                            suggestions += '</li>';
                        });

                        $('#responsible_suggestions').html(suggestions).show();
                        $('#redirect_button').hide();
                    }

                    if (response.length === 0 || !Array.isArray(response)) {
                        $('#responsible_suggestions').html('<li class="list-group-item">Sem resultados, <a href="/pessoa" target="_blank">Cadastrar ?</a></li>').show();
                        $('#redirect_button').show();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro na requisição:', error);
                }
            });
        });

        $('#responsible_suggestions').on('click', 'li', function () {
            var selectedResponsibleName = $(this).text(); 
            var selectedResponsibleId = $(this).data('id');

            if (!selectedResponsibleId) {
                $('#responsible_search').val('');
                return;
            }

            $('#responsible_search').val(selectedResponsibleName);

            $('#responsible_id').val(selectedResponsibleId); 

            $('#responsible_suggestions').hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#responsible_search').length) {
                $('#responsible_suggestions').hide();
            }
        });
    });
</script>