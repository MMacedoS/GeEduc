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
                    <a href="/usuarios" class="text-decoration-none">Usuarios</a>
                </li>
                <li class="breadcrumb-item">Vincular Permissão</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="/usuarios" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <form action="/usuario/<?=$data['usuario']->uuid?>/permissao" method="post">
        <div class="row gx-3">
            <div class="col-sm-12 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-center py-3">
                        <h2>Permissões</h2>
                    </div>
                    <div class="row">
                        <?php 
                            foreach ($data['permissions'] as $permission) {
                        ?>
                            <div class="col-sm-4 col-sm-6 col-md-6 col-lg-4 py-2 px-4 border form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="inlineCheckbox<?=$permission->id?>" 
                                    <?=isPermissionChecked($permission->id, $data['permissions_user']) ? 'checked' : ''?>
                                    name="permissions[]" 
                                    value="<?=$permission->id?>" />
                                <label 
                                    class="form-check-label" 
                                    for="inlineCheckbox<?=$permission->id?>">
                                    <?=$permission->name?>
                                </label>
                            </div>      
                        <?php } ?>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-xxl-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <a href="\usuarios" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
