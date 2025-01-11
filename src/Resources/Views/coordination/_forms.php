
<div class="col-lg-5 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" class="form-control" name="name" placeholder="digite aqui" value="<?=$pessoa_fisica->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email de acesso</label>
        <input type="email" class="form-control" name="email" placeholder="digite aqui" value="<?=$pessoa_fisica->email ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-3 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Graduação</label>
        <input type="text" class="form-control" name="graduacao" placeholder="digite aqui" value="<?=$coordenador->graduacao ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-2 col-sm-2 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Tipo documento</label>
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
        <label class="form-label">Numero documento</label>
        <input type="text" class="form-control" name="doc" id="doc" placeholder="" value="<?=$pessoa_fisica->doc ?? ''?>" />
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
        <input type="text" step="0" min="1" class="form-control" name="mother" placeholder="digite aqui" value="<?=$pessoa_fisica->nome_mae ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome do Pai</label>
        <input type="text" step="0" min="1" class="form-control" name="father" placeholder="digite aqui" value="<?=$pessoa_fisica->nome_pai ?? ''?>" />
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
        <label class="form-label">Situação</label>
        <select name="active" class="form-control" id="">
            <option value="0" <?php if(isset($pessoa_fisica->ativo) && $pessoa_fisica->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" <?php if(isset($pessoa_fisica->ativo) && $pessoa_fisica->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Telefone de contato</label>
        <input type="phone" class="form-control" name="phone" id="phone" placeholder="digite aqui" value="<?=$pessoa_fisica->telefone ?? ''?>" 
        required pattern="^\(?([0-9]{2})\)?[-. ]?([0-9]{4,5})[-. ]?([0-9]{4})$"/>
        <div class="invalid-feedback">Telefone inválido</div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Endereço</label>
        <input type="text" step="0" min="1" class="form-control" name="address" placeholder="digite aqui" value="<?=$pessoa_fisica->endereco ?? ''?>" />
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

