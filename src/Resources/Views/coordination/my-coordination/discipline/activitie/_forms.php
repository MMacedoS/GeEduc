<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Atividade</label>
        <select class="form-control" name="type" id="type">
            <option value="">Selecione um componente</option>
            <option value="atividade" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'atividade') { echo 'selected'; } ?>>Atividade</option>
            <option value="participacao" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'participacao') { echo 'selected'; } ?>>Participação</option>
            <option value="teste" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'teste') { echo 'selected'; } ?>>Teste</option>
            <option value="prova" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'prova') { echo 'selected'; } ?>>Prova</option>
            <option value="trabalho" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'trabalho') { echo 'selected'; } ?>>Trabalho</option>
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
                <a href="/minha-coordenacao/turma/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividades" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>