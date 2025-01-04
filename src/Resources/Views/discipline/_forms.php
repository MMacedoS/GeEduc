
<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Disciplina</label>
        <input type="text" step="0" min="1" class="form-control" name="name" placeholder="digite aqui" value="<?=$disciplina->nome ?? ''?>" />
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
            <option value="0" <?php if(isset($disciplina->status) && $disciplina->status == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($disciplina->status) && $disciplina->status == '1') { echo 'selected'; } ?>>Disponivel</option>
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
                <a href="\disciplinas\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

