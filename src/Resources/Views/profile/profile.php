<?php require_once __DIR__ . '/../layout/top.php'; ?>

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
                              <div class="row gx-3">
                                <div class="col-6">
                                  <!-- Form Field Start -->
                                  <div class="mb-3">
                                    <label for="fullName" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="fullName" name="name" value="<?=$pessoa->nome?>" placeholder="Full Name" />
                                  </div>

                                  <!-- Form Field Start -->
                                  <div class="mb-3">
                                    <label for="contactNumber" class="form-label">Contato</label>
                                    <input type="text" class="form-control" name="telefone" value="<?=$pessoa->telefone?>" id="contactNumber" placeholder="Contact" />
                                  </div>
                                </div>
                                <div class="col-6">
                                  <!-- Form Field Start -->
                                  <div class="mb-3">
                                    <label for="emailId" class="form-label">Email</label>
                                    <input type="email" class="form-control"  name="email" value="<?=$pessoa->email?>" id="emailId" placeholder="Email ID" />
                                  </div>

                                  <!-- Form Field Start -->
                                  <div class="mb-3">
                                    <label for="birthDay" class="form-label">Data Nascimento</label>
                                    <div class="input-group">
                                      <input type="text" class="form-control datepicker-opens-left" id="birthDay"
                                        placeholder="DD/MM/YYYY"
                                        name="birthday" value="<?=$pessoa->data_nascimento?>"
                                        />
                                      <span class="input-group-text">
                                        <i class="icon-calendar"></i>
                                      </span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-12">
                                  <!-- Form Field Start -->
                                  <div class="mb-3">
                                    <label class="form-label">Sobre</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Row end -->
                        </div>
                      </div>
                      <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary">
                          Cancel
                        </button>
                        <button type="button" class="btn btn-primary">
                          Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
