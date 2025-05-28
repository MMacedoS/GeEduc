
<div class="col-lg-12 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Bimestre</label>
        <input type="number" step="0" min="1" class="form-control" name="period" placeholder="digite aqui" value="<?=$periodo->periodo ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\bimestres\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

