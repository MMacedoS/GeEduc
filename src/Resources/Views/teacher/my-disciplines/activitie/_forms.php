<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Atividade</label>
        <select class="form-control" name="type" id="type">
            <option value="">Selecione um componente</option>
            <option value="A-1" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-1') { echo 'selected'; } ?>>Atividade 1</option>
            <option value="A-2" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-2') { echo 'selected'; } ?>>Atividade 2</option>
            <option value="A-3" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-3') { echo 'selected'; } ?>>Atividade 3</option>
            <option value="A-4" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-4') { echo 'selected'; } ?>>Atividade 4</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nota Maxima</label>
        <input type="number" class="form-control" name="value" step="0.01" min="0" value="<?=$atividade->valor ?? '0'?>" id="">
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="active" class="form-control" id="">
            <option value="0" <?php if(isset($atividade->ativo)  && $atividade->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($atividade->ativo)  && $atividade->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
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
                <a href="/meus-componentes/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividades" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>