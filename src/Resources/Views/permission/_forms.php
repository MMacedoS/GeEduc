
<div class="col-lg-4 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Permissão</label>
        <input type="text" step="0" min="1" class="form-control" id="name" name="name" required placeholder="Ex.: cadastrar notas" value="<?=$permissao->name ?? ''?>" />
        <div class="invalid-feedback" id="name_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text" step="0" min="1" class="form-control" name="description" required placeholder="Ex.: Essa permissão permite..." value="<?=$permissao->description ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\permissao\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

