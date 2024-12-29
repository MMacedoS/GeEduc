
<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" step="0" min="1" class="form-control" name="name" placeholder="digite aqui" value="<?=$usuario->nome ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email de acesso</label>
        <input type="email" step="0" min="1" class="form-control" name="email" placeholder="digite aqui" value="<?=$usuario->email ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Senha de primeiro acesso</label>
        <input type="password" step="0" min="1" class="form-control" name="password" placeholder="digite aqui" value="" />
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
            <option value="0" <?php if(isset($usuario->status) && $usuario->status == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($usuario->status) && $usuario->status == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-2 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Telefone de contato</label>
        <input type="tel" step="0" min="1" class="form-control" name="telefone" placeholder="digite aqui" value="<?=$usuario->address ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Endereço</label>
        <input type="text" step="0" min="1" class="form-control" name="address" placeholder="digite aqui" value="<?=$usuario->address ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\usuario\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

