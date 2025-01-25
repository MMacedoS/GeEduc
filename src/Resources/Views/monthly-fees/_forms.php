
<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Estudantes</label>
        <select class="js-example-basic-multiple form-control form-select" multiple="multiple" name="students_id[]" id="student_id" <?=!is_null($mensalidade) ? 'disabled' : ''?>>
        <?php foreach ($estudantes as $key => $value) { ?>
                <option 
                    value="<?=$value->id ?>" 
                    <?= isset($mensalidade->estudante_mensalidade_id) && $mensalidade->estudante_mensalidade_id == $value->id ? 'selected' : '' ?>>
                    <?=getJsonToObject($value->estudantes)->nome?>
                </option>
            <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Dia de Pagamento</label>
        <input type="number" step="0" min="1" max="30" name="monthly_day" id="<?=is_null($mensalidade) ? 'expiration_day': ''?>" class="form-control" <?=!is_null($mensalidade) ? 'disabled' : ''?> value="<?= $mensalidade->dia_vencimento ?? $estudante_mensalidade->dia_mensalidade ?>">
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Data de Vencimento</label>
        <input type="date" name="expiration_date" id="<?=is_null($mensalidade) ? 'expiration_date': ''?>" class="form-control" value="<?=$mensalidade->data_vencimento ?? Date('Y-m-d')?>">
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12 <?=!is_null($mensalidade) ? 'd-none' : ''?>">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Plano</label>
        <select class="form-select" name="plan_amount" id="plan_id">
            <option value="">Selecione uma plano</option>
            <?php foreach ($planos as $key => $value) { ?>
                <option 
                    value="<?= $value->valor ?>" 
                    <?= isset($estudante_mensalidade->plano_id) && $estudante_mensalidade->plano_id == $value->id ? 'selected' : '' ?>>
                    <?= $value->nome ?>
                </option>
            <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Valor</label>
        <input type="number" readonly step="0.01" min="0" id="amount" class="form-control" value="<?=$mensalidade->valor ?? '0.0'?>">
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Desconto</label>
        <input type="number" step="0.01" min="0" name="discont" id="discont" class="form-control" value="<?=$mensalidade->desconto ?? '0.0'?>">
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Total</label>
        <input type="number" step="0.01" min="0" name="amount" id="total" class="form-control" value="<?=$mensalidade->valor ?? '0.0'?>">
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\mensalidades" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

