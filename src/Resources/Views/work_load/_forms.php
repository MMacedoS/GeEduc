
<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Carga</label>
        <input type="text" class="form-control" name="load" placeholder="digite aqui" value="<?=$carga_horaria->carga ?? ''?>" />
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
            <option value="0" <?php if(isset($carga_horaria->ativo)  && $carga_horaria->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($carga_horaria->ativo)  && $carga_horaria->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
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
                <a href="\carga-horaria\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

