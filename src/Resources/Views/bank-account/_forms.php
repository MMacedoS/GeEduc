<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Código do Banco</label>
        <select name="bank_code" id="bankCode" class="form-control" required>
          <option value="">Selecione o código</option>
          <option value="001" <?php if (isset($conta->codigo_banco) && $conta->codigo_banco == '001') { echo 'selected'; } ?>>001 - Banco do Brasil</option>
          <option value="104" <?php if (isset($conta->codigo_banco) && $conta->codigo_banco == '104') { echo 'selected'; } ?>>104 - Caixa Econômica Federal</option>
          <option value="237" <?php if (isset($conta->codigo_banco) && $conta->codigo_banco == '237') { echo 'selected'; } ?>>237 - Bradesco</option>
          <option value="341" <?php if (isset($conta->codigo_banco) && $conta->codigo_banco == '341') { echo 'selected'; } ?>>341 - Itaú</option>
          <option value="033" <?php if (isset($conta->codigo_banco) && $conta->codigo_banco == '033') { echo 'selected'; } ?>>033 - Santander</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Banco</label>
        <select name="bank" class="form-control" id="bankName" required>
          <option value="">Selecione o Banco</option>
          <option value="Banco do Brasil" <?php if (isset($conta->banco) && $conta->banco == 'Banco do Brasil') { echo 'selected'; } ?>>Banco do Brasil</option>
          <option value="Caixa Econômica Federal" <?php if (isset($conta->banco) && $conta->banco == 'Caixa Econômica Federal') { echo 'selected'; } ?>>Caixa Econômica Federal</option>
          <option value="Bradesco" <?php if (isset($conta->banco) && $conta->banco == 'Bradesco') { echo 'selected'; } ?>>Bradesco</option>
          <option value="Itaú" <?php if (isset($conta->banco) && $conta->banco == 'Itaú') { echo 'selected'; } ?>>Itaú</option>
          <option value="Santander" <?php if (isset($conta->banco) && $conta->banco == 'Santander') { echo 'selected'; } ?>>Santander</option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Agência</label>
        <input type="text" class="form-control" name="branch" required minlength="1" maxlength="10" placeholder="Ex.: 1234" value="<?=$conta->agencia ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Conta</label>
        <input type="text" class="form-control" name="account" placeholder="Ex.: 56789-0" minlength="1" maxlength="20" required value="<?=$conta->conta ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Convênio</label>
        <input type="text" class="form-control" name="agreement" required  minlength="1" maxlength="45" placeholder="Digite o convênio" value="<?=$conta->convenio ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="active" class="form-control">
            <option value="0" <?php if(isset($conta->ativo) && $conta->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($conta->ativo) && $conta->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
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
                <a href="\bancos\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>
