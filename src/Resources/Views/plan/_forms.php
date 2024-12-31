
<div class="col-lg-8 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Plano</label>
        <input type="text" class="form-control" name="name" placeholder="digite aqui" value="<?=$plano->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Valor</label>
        <input type="number" step="0.01" min="0" class="form-control" name="amount" placeholder="digite aqui" value="<?=$plano->valor ?? '0.0'?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-10 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text"  class="form-control" name="description" placeholder="digite aqui" value="<?=$plano->descricao ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="active" class="form-control" id="">
            <option value="0" <?php if(isset($plano->ativo) && $plano->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($plano->ativo) && $plano->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\planos\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

