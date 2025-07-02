<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" step="0" min="1" class="form-control" id="name" name="name" required placeholder="Ex.: José Silva" value="<?=$usuario->nome ?? ''?>" />
        <div class="invalid-feedback" id="name_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email de acesso</label>
        <input type="email" step="0" min="1" class="form-control" required id="email" name="email" placeholder="Ex.: jose.silva@email.com" value="<?=$usuario->email ?? ''?>" />
        <div class="invalid-feedback" id="email_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Senha de acesso</label>
        <input type="password" step="0" min="1" required class="form-control" name="password" placeholder="Ex.: @josesenha" value="<?=$usuario->senha ?? ''?>" />
      </div>
    </div>
  </div>
</div>
<div class="col-lg-2 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="status" class="form-control" id="">
            <option value="0" <?php if(isset($usuario->ativo) && $usuario->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" <?php if(isset($usuario->ativo) && $usuario->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Painel</label>
        <select name="sector" class="form-control" id="">
              <option value="estudante" selected <?php if(isset($usuario->painel) && $usuario->painel == 'estudante') { echo 'selected'; } ?>>Estudante</option>
              <option value="administrativo" <?php if(isset($usuario->painel) && $usuario->painel == 'administrativo') { echo 'selected'; } ?>>Administrativo</option>
              <option value="coordenador" <?php if(isset($usuario->painel) && $usuario->painel == 'coordenador') { echo 'selected'; } ?>>Coordenador</option>
              <option value="professor" <?php if(isset($usuario->painel) && $usuario->painel == 'professor') { echo 'selected'; } ?>>Professor</option>
              <option value="responsavel_legal" <?php if(isset($usuario->painel) && $usuario->painel == 'responsavel_legal') { echo 'selected'; } ?>>Responsavel Legal</option>
              <option value="secretaria" <?php if(isset($usuario->painel) && $usuario->painel == 'secretaria') { echo 'selected'; } ?>>Secretaria</option>
          </select>
        </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Endereço</label>
        <input type="text" step="0" min="1" class="form-control" name="address"  placeholder="Ex.: Rua Antônio Cornélio, 123" value="<?=$usuario->address ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\usuarios" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

