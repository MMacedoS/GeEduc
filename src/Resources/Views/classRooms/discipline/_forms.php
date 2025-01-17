<div class="col-lg-12 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Componente Curricular</label>
        <select class="js-example-basic-multiple form-control form-select" multiple="multiple" name="teacher_discipline_id[]" id="teacher_discipline_id">
            <option value="">Selecione um componente</option>
            <?php foreach ($disciplinas as $key => $value) { ?>
                <option 
                    value="<?=$value->id ?>" 
                    <?= isset($turma_disciplina->professor_disciplina_id) && $turma_disciplina->professor_disciplina_id == $value->id ? 'selected' : '' ?>>
                    <?="Componente: " . getParamsToJson($value->disciplina)->nome . " | Professor: " . getParamsToJson($value->professor)->nome?>
                </option>
            <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Carga Horária</label>
        <select class="form-select" name="work_load_id" id="work_load_id">
            <option value="">Selecione uma Carga</option>
            <?php foreach ($carga_horaria as $key => $value) { ?>
                <option 
                    value="<?= $value->id ?>" 
                    <?= isset($turma_disciplina->carga_horaria_id) && $turma_disciplina->carga_horaria_id == $value->id ? 'selected' : '' ?>>
                    <?=$value->carga?> Horas
                </option>
            <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-3 col-12 d-none">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Ano Letivo</label>
        <input type="number" step="0000" min="2019" readonly name="academic_year" class="form-control" value="<?=$turma_disciplina->ano_letivo ?? DATE('Y')?>">
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
            <option value="0" <?php if(isset($turma_disciplina->ativo)  && $turma_disciplina->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($turma_disciplina->ativo)  && $turma_disciplina->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
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