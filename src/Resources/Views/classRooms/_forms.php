
<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Turma</label>
        <input type="text" class="form-control" name="name" placeholder="digite aqui" value="<?=$turma->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Coordenador</label>
        <select class="form-control form-select" name="coordinator_id" id="coordinator_id">
            <option value="">Selecione um Coordenador</option>
            <?php foreach ($coordenadores as $key => $value) { ?>
                <option 
                    value="<?=$value->id ?>" 
                    <?= isset($turma->coordenador_id) && $turma->coordenador_id == $value->id ? 'selected' : '' ?>>
                    <?=getParamsToJson($value->pessoa_fisica)->nome?>
                </option>
            <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>


<div class="col-lg-2 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Ordem</label>
        <input type="number" step="0" min="0" class="form-control" name="order" placeholder="digite aqui" value="<?=$turma->ordem ?? '1'?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Turno</label>
          <select name="shift" class="form-control" id="">
              <option value="matutino" selected <?php if(isset($turma->turno) && $turma->turno == 'matutino') { echo 'selected'; } ?>>Matutino</option>
              <option value="vespertino" <?php if(isset($turma->turno) && $turma->turno == 'vespertino') { echo 'selected'; } ?>>Vespertino</option>
              <option value="noturno" <?php if(isset($turma->turno) && $turma->turno == 'noturno') { echo 'selected'; } ?>>Noturno</option>
              <option value="integral" <?php if(isset($turma->turno) && $turma->turno == 'integral') { echo 'selected'; } ?>>Integral</option>
          </select>
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
            <option value="0" <?php if(isset($turma->ativo) && $turma->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($turma->ativo) && $turma->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
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
                <a href="\turmas\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

