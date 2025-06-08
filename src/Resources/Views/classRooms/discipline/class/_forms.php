
<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Dias de Aulas/ Horario</label>
        <select class="js-example-basic-multiple form-control form-select" multiple="multiple" name="days_id[]" id="days_id">
            <option value="">Selecione</option>
            <?php foreach ($dias as $key => $value) { 
              ?>
                <option 
                    value="<?=$value->id ?>" 
                    <?= isset($aulas) && in_array($value->id, $aulas) ? 'selected' : ''  ?>>
                    <?="" . $value->dia . " | " . $value->horario . "ª Horário " . $value->turno?>
                </option>
            <?php } ?>
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
                <a href="/turmas/<?=$turma->uuid?>/disciplinas/<?=$turmas_disciplinas[0]->uuid?>/aulas" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>