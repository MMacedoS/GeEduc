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
                    <a href="/estudantes" class="text-decoration-none">Estudantes</a>
                </li>
                <li class="breadcrumb-item">Cadastrar</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="/estudantes/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>

    <a  href="/estudante/excel" style="padding: 10px 10px;  border-radius: 10px; color: white; background: #40AA5C; margin: 10px 0; width: 280px; display: flex; gap: 20px;">Deseja cadastrar via planilha? Clique aqui!</a>
    <!-- Row end -->
    <form action="/estudante" method="POST">
        <div class="row gx-3">
            <? include_once('_forms.php');?>
        </div>
    </form>

    <div class="modal fade modal-xl" id="responsavel-legal-modal" tabindex="-1" role="dialog" aria-labelledby="responsavel-legal-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="/pessoa-responsavel" method="POST" class="modal-content" id="modal-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Cadastrar Responsável Legal</h5>
                </div>

                <div class="modal-body row gx-3">
                    <p id="teste-de-errors"></p>
                    <p class="mt-2 text-muted">Insira as informações para o cadastro</p>
                    <?php include_once __DIR__ . '/../person/_forms.php'; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi-search"></i> Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

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
                        $('#responsible_suggestions').html('<li class="list-group-item" data-bs-toggle="modal" data-bs-target="#responsavel-legal-modal">Sem resultados, <span class="text-decoration-underline text-info">Cadastrar?</span></li>').show();
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

    $("#modal-form").submit(function(event) {
        event.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "/pessoa-responsavel",
            data: formData,
            dataType: "JSON",
            success: function(response) {
                if(response.errors){
                    $.each(response.errors, function(key, value) {
                        $("#teste-de-errors").text(value); 
                    });
                }else{
                    $('#responsible_id').val(response.id); 
                    $('#responsible_search').val(response.pessoa_fisica.nome + ' (' + response.pessoa_fisica.email + ')'); 

                    $('#responsavel-legal-modal').modal('hide');
                }
            },
            error: function(error){
                console.error('Erro na requisição:', error);
            }
        });
    });
</script>