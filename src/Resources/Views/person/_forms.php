
<div class="col-lg-5 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" class="form-control" required name="name" id="name" placeholder="Ex.: jose.santos@email.com" value="<?=$pessoa_fisica->nome ?? ''?>" />
        <div class="invalid-feedback" id="name_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email de acesso</label>
        <input type="email" class="form-control" required name="email" id="email" placeholder="Ex.: jose.santos@email.com" value="<?=$pessoa_fisica->email ?? ''?>" />
        <div class="invalid-feedback" id="email_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-3 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Graduação</label>
        <input type="text" class="form-control" name="graduacao" placeholder="Ex.: Ciências Biológicas" value="<?=$coordenador->graduacao ?? ''?>" />
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
        <label class="form-label">Nº do Documento</label>
        <input type="text" required class="form-control" name="doc" id="doc" maxlength="14" placeholder="999.999.999-99" value="<?=$pessoa_fisica->doc ?? ''?>" />
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
        <input type="text" step="0" min="1" class="form-control" id="mother" required name="mother" placeholder="Ex.: Joana dos Santos" value="<?=$pessoa_fisica->nome_mae ?? ''?>" />
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
        <input type="text" step="0" min="1" class="form-control"id="father" name="father" placeholder="Ex.: João dos Santos" value="<?=$pessoa_fisica->nome_pai ?? ''?>" />
        <div class="invalid-feedback" id="father_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
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

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Telefone</label>
        <input type="phone" class="form-control" name="phone" id="phone" maxlength="16" maxlength="15" placeholder="(99) 99999-9999" value="<?=$pessoa_fisica->telefone ?? ''?>" 
        required/>
        <div class="invalid-feedback">Telefone inválido</div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Responsável Legal</label>
        <select name="legal_responsive" class="form-control" id="">
          <?php
            if(!isset($responsavelForm)){
          ?>
            <option value="0" <?php if(isset($pessoa_contato->responsavel_legal) && $pessoa_contato->responsavel_legal == 0) { echo 'selected'; } ?>>Não</option>
          <?php
            }
          ?>
          <option value="1" <?php if(isset($pessoa_contato->responsavel_legal) && $pessoa_contato->responsavel_legal == 1) { echo 'selected'; } ?>>Sim</option>
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

<?php
  if(!isset($responsavelForm)){
?>
  <div class="col-xxl-12">
      <div class="card mb-3">
          <div class="card-body">
              <div class="d-flex flex-wrap gap-2 justify-content-end">
                  <button type="submit" class="btn btn-primary">Salvar</button>
                  <a href="\pessoas\" class="btn btn-secondary">Cancelar</a>
              </div>
          </div>
      </div>
  </div>
<?php
  }
?>

