<?php require_once __DIR__ . '/../layout/top.php'; ?>


<style>
  .is-valid {
    border-color: #198754; /* Verde */
  }
  .is-invalid {
    border-color: #dc3545; /* Vermelho */
  }

</style>
<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Perfil</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
</div>
    <!-- Row end -->
<? 
if(isset($success)){?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
<? if(isset($danger)){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
    <!-- Row start -->

<div class="row gx-3">
    <div class="col-xxl-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="custom-tabs-container">
                  <ul class="nav nav-tabs" id="customTab2" role="tablist">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                        aria-controls="oneA" aria-selected="true">General</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="tab-oneB" data-bs-toggle="tab" href="#oneB" role="tab"
                      aria-controls="oneA" aria-selected="true">Alterar senha</a>
                    </li>
                  </ul>
                  <div class="tab-content h-350">
                    <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                          <!-- Row start -->
                          <div class="row gx-3">
                            <div class="col-sm-4 col-12">
                              <div id="update-profile" class="mb-3">
                                <form action="/upload" class="dropzone sm needsclick dz-clickable"
                                  id="update-profile-pic">
                                  <div class="dz-message needsclick">
                                    <button type="button" class="dz-button">
                                      Inserir foto.
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                            
                            <div class="col-sm-8 col-12">
                              <form id="formUser" method="post">
                                <div class="row gx-3">
                                  <div class="col-6">
                                    <!-- Form Field Start -->
                                    <div class="mb-3">
                                      <label for="fullName" class="form-label">Nome Completo</label>
                                      <input type="text" class="form-control" id="fullName" name="name" value="<?=$pessoa->nome?>" placeholder="Nome completo" />
                                    </div>
                                    <!-- Form Field Start -->
                                    <div class="mb-3">
                                      <label for="contactNumber" class="form-label">Contato</label>
                                      <input type="text" class="form-control" name="phone" value="<?=$pessoa->telefone?>" id="contactNumber" placeholder="Contato" />
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <!-- Form Field Start -->
                                    <div class="mb-3">
                                      <label for="emailId" class="form-label">Email</label>
                                      <input type="email" class="form-control"  name="email" value="<?=$pessoa->email?>" id="emailId" placeholder="Email" />
                                    </div>
                                    <!-- Form Field Start -->
                                    <div class="mb-3">
                                      <label for="birthDay" class="form-label">Data Nascimento</label>
                                      <div class="input-group">
                                        <input type="date" class="form-control" id="birthDay"
                                          placeholder="DD/MM/YYYY"
                                          name="birthday" value="<?=$pessoa->data_nascimento ?? date('Y-m-d')?>"
                                          />
                                        <span class="input-group-text">
                                          <i class="icon-calendar"></i>
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-12">                                      
                                  </div>
                                </div>
                              </form>
                            </div>
                        </div>
                        <!-- Row end -->
                      <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary">
                          Cancel
                        </button>
                        <button type="button" id="btn-user" class="btn btn-primary">
                          Atualizar
                        </button>
                      </div>
                    </div>

                    <div class="tab-pane fade show" id="oneB" role="tabpanel">
                      <form action="/perfil-senha" method="post">
                        <div class="row gx-3">
                          <div class="col-sm-4">                          
                            <div class="mb-3">
                              <label for="passwordOld" class="form-label">Senha Antiga</label>
                              <input type="password" class="form-control" id="passwordOld" name="password_old" />
                            </div>
                          </div>  
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label for="passwordNew" class="form-label">Nova senha</label>
                              <input type="password" class="form-control" id="passwordNew" name="password"/>
                            </div>
                          </div>              
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label for="passwordConfirm" class="form-label">Confirme nova senha</label>
                              <input type="password" class="form-control" id="passwordConfirm" name="password_confirm"/>
                              <small id="passwordError" class="text-danger" style="display: none;">As senhas não coincidem.</small>
                            </div>
                          </div>                            
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                          <button type="submit" id="btn-user" class="btn btn-primary">
                            Alterar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>

<script>
  $('#btn-user').on('click', function () {
    sendRequestWithMethod(
      '/perfil',
      new FormData(document.getElementById("formUser")), 
      'POST'
    ).then((res) => {
       if (res.status === 422) {
          showErrorMessage(res.message);
          return;
        }
        showSuccessMessage(res.message);
        return;
      });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const passwordNew = document.getElementById('passwordNew');
    const passwordConfirm = document.getElementById('passwordConfirm');
    const passwordError = document.getElementById('passwordError');

    const validatePasswords = () => {
      if (passwordNew.value && passwordConfirm.value) {
        if (passwordNew.value === passwordConfirm.value) {
          passwordError.style.display = 'none';
          passwordNew.classList.remove('is-invalid');
          passwordConfirm.classList.remove('is-invalid');
          passwordNew.classList.add('is-valid');
          passwordConfirm.classList.add('is-valid');
        } else {
          passwordError.style.display = 'block';
          passwordNew.classList.add('is-invalid');
          passwordConfirm.classList.add('is-invalid');
          passwordNew.classList.remove('is-valid');
          passwordConfirm.classList.remove('is-valid');
        }
      } else {
        passwordError.style.display = 'none';
        passwordNew.classList.remove('is-valid', 'is-invalid');
        passwordConfirm.classList.remove('is-valid', 'is-invalid');
      }
    };

    passwordNew.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);

    passwordNew.addEventListener('blur', validatePasswords);
    passwordConfirm.addEventListener('blur', validatePasswords);
  });

</script>