<?php require_once __DIR__ . '/../layout/top.php'; ?>


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
  <div class="col-2 col-xl-6">
      <div class="float-end">
          <a href="/estudantes/" class="btn btn-outline-primary" > Voltar </a>
      </div>
  </div>
  </div>
  <!-- Row end -->
  <form action="/estudante/excel" method="POST" enctype="multipart/form-data">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="m-0">
            <label for="arq-excel" class="form-label">Anexar arquivo</label>
            <input type="file" name="arq-excel" id="arq-excel" class="form-control" />
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="\estudantes\" class="btn btn-secondary">Cancelar</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../layout/bottom.php'; ?>

