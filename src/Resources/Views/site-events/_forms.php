
<div class="col-lg-12 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome</label>
        <input type="text" class="form-control" name="name" placeholder="digite aqui" value="<?=$site_evento->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Imagem</label>
        <input type="file" class="form-control" name="arquivo" value="<?=$site_evento->archive ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="active" class="form-control" id="">
            <option value="0" <?php if(isset($site_evento->status) && $site_evento->status == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($site_evento->status) && $site_evento->status == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-12 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descricao</label>
        <input type="text" class="form-control" name="description" placeholder="digite aqui" value="<?=$site_evento->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\site-eventos\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

