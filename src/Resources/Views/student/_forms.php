<style>
  #responsible_suggestions {
    background-color: #fff;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    z-index: 100;
    width: 100%;
    border: 1px solid #ccc;
    margin-top: 5px;
  }

  #responsible_suggestions .list-group-item {
      cursor: pointer;
  }

  #responsible_suggestions .list-group-item:hover {
      background-color: #f1f1f1;
  }
</style>

<div class="col-lg-3 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" class="form-control" id="name" name="name" required placeholder="Ex.: José dos Santos" value="<?=$pessoa_fisica->nome ?? ''?>" />
        <div class="invalid-feedback" id="name_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-3 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email de acesso</label>
        <input type="email" class="form-control" name="email" required id="email" placeholder="Ex.: jose.santos@escolacesp.com" value="<?=$pessoa_fisica->email ?? ''?>" />
        <div class="invalid-feedback" id="email_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Data de Nascimento</label>
        <input type="date"  class="form-control" name="birthday" value="<?=$pessoa_fisica->data_nascimento ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Responsável Legal</label>
        <input type="hidden" id="responsible_id" name="legal_responsible_id" value="<?=$estudante->pessoa_contato_id ?? ''?>">

        <input type="text" id="responsible_search" class="form-control" placeholder="Digite o nome do responsável" value="<?=$pessoa_fisica_contato->nome ?? ''?>" autocomplete="off" />
        <ul 
          id="responsible_suggestions" 
          class="list-group" 
          style="display: none; position: absolute; z-index: 100; width: 100%; max-height: 200px; overflow-y: auto; border: 1px solid #ccc;">
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-2 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Tipo do Documento</label>
        <select class="form-select" name="type_doc" id="type_doc">
          <option value="CPF" <?php if (isset($pessoa_fisica->type_doc) && $pessoa_fisica->type_doc === 'CPF') { echo 'selected';} ?>>CPF</option>
          <option value="CNH" <?php if (isset($pessoa_fisica->type_doc) && $pessoa_fisica->type_doc === 'CNH') { echo 'selected';} ?>>CNH</option>
          <option value="RG" <?php if (isset($pessoa_fisica->type_doc) && $pessoa_fisica->type_doc === 'RG') { echo 'selected';} ?>>RG</option>
          <option value="PASSAPORTE" <?php if (isset($pessoa_fisica->type_doc) && $pessoa_fisica->type_doc === 'PASSAPORTE') { echo 'selected';} ?>>Passaporte</option>
        </select>
        <div class="invalid-feedback" id="type_doc_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nº do Documento</label>
        <input type="text" class="form-control" name="doc" id="doc"placeholder="Ex.: 999.999.999-99" maxlength="14" value="<?=$pessoa_fisica->doc ?? ''?>" />
        <div class="invalid-feedback" id="doc_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome da Mãe</label>
        <input type="text" step="0"id="mother" min="1" required minlength="1" maxlength="100" class="form-control" name="mother" placeholder="Ex.: Joana dos Santos" value="<?=$pessoa_fisica->nome_mae ?? ''?>" />
        <div class="invalid-feedback" id="mother_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome do Pai</label>
        <input type="text" step="0" id="father" min="1" class="form-control" minlength="1" maxlength="100" name="father" placeholder="Ex.: João dos Santos" value="<?=$pessoa_fisica->nome_pai ?? ''?>" />
        <div class="invalid-feedback" id="father_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Gênero</label>
        <select name="gender" class="form-control" id="">
            <option value="1" <?php if(isset($pessoa_fisica->genero) && $pessoa_fisica->genero == "1") { echo 'selected'; } ?>>Masculino</option>
            <option value="2" <?php if(isset($pessoa_fisica->genero) && $pessoa_fisica->genero == "2") { echo 'selected'; } ?>>Feminino</option>            
            <option value="0" <?php if(!isset($pessoa_fisica->genero) || isset($pessoa_fisica->genero) && $pessoa_fisica->genero == "0") { echo 'selected'; } ?>>Prefiro não dizer</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Gerar Mensalidades</label>
        <select name="procees_monthylees" class="form-control" id="">
            <option value="nao" <?php if(isset($pessoa_fisica->ativo) && $pessoa_fisica->ativo == 'nao') { echo 'selected'; } ?>>Não</option>
            <option value="sim" <?php if(isset($pessoa_fisica->ativo) && $pessoa_fisica->ativo == 'sim') { echo 'selected'; } ?>>Sim</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Telefone</label>
        <input type="phone" class="form-control" name="phone" id="phone" placeholder="Ex.: (99) 99999-9999" minlength="15" maxlength="16" value="<?=$pessoa_fisica->telefone ?? ''?>" />
        <div class="invalid-feedback">Telefone inválido</div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Planos</label>
        <select name="plan_id" class="form-control" id="plan_id" required>
            <?php 
              foreach ($plans as $plan): ?>
                <option value="<?= htmlspecialchars($plan->id) ?>" 
                    <?= isset($monthly->plan_id) && $monthly->plan_id == $plan->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($plan->nome) ?>
                </option>
            <?php endforeach; ?>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Desconto</label>
        <input type="number" class="form-control" step="0.01" min="0" name="discont" id="" value="<?=$monthly->desconto ?? '0'?>">
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Dia Mensalidade</label>
        <select name="monthly_day" class="form-control" id="monthly_day" required>
            <option value="1" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '1') { echo 'selected'; } ?>>01</option>
            <option value="5" selected <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '5') { echo 'selected'; } ?>>05</option>
            <option value="10" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '10') { echo 'selected'; } ?>>10</option>
            <option value="15" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '15') { echo 'selected'; } ?>>15</option>
            <option value="20" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '20') { echo 'selected'; } ?>>20</option>
            <option value="25" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '25') { echo 'selected'; } ?>>25</option>
            <option value="28" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '28') { echo 'selected'; } ?>>28</option>
            <option value="30" <?php if(isset($monthly->dia_mensalidade) && $monthly->dia_mensalidade == '30') { echo 'selected'; } ?>>30</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Endereço</label>
        <input type="text" step="0" min="1" class="form-control" name="address" placeholder="Ex.: Rua Antônio Cornélio, 123" value="<?=$pessoa_fisica->endereco ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\estudantes\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<script>
  document.getElementById('email').addEventListener('input', function() {
    const domain = '@escolacesp.com.br';
    const currentValue = this.value;

    if (currentValue.includes('@') && !currentValue.includes(domain)) {
      this.value = currentValue.split('@')[0] + domain;
      return;
    } 
      this.value = currentValue;
      return;
  });
</script>