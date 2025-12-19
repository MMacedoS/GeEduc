</div>
</div>
<!-- App footer start -->
<div class="app-footer">
    <div class="container">
        <span>© <?= APP_NAME ?> <?= date('Y') ?></span>
        <span class="float-center">Version: 1.0.0</span>
        <span class="float-end">Powered by <a href="https://github.com/MMacedoS" target="_blank">Mauricio Macedo</a></span>
    </div>
</div>
<!-- App footer end -->
<div class="modal fade" id="modalBalance" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalBalanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBalanceLabel">
                    Abertura Caixa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container mt-2">
                    <form action="" id="form_inserir_caixa" method="post">
                        <div class="row gx-3">
                            <div class="col-12">
                                <label for="">Saldo Abertura</label>
                                <input type="number" name="starting_balance" id="starting_balance" step="0.01" value="0" min="0" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_caixa" class="btn btn-primary">
                    Inserir Caixa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- *************
    ************ JavaScript Files *************
    ************* -->

<!-- Required jQuery first, then Bootstrap Bundle JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/jquery.min.js"></script>

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/bootstrap.bundle.min.js"></script>

<!-- *************
    ************ Vendor Js Files *************
    ************* -->

<!-- Overlay Scroll JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>
<!-- <script src="public/assets/vendor/quill/quill.min.js"></script>
    <script src="public/assets/vendor/quill/custom.js"></script> -->

<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/custom.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/validations.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/morris/raphael-min.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/morris/morris.min.js"></script>

<!-- Dropzone JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/dropzone/dropzone.min.js"></script> <!-- Moment JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/moment.min.js"></script>

<!-- Date Range JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/daterange/daterange.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/daterange/custom-daterange.js"></script>

<!-- Calendar JS -->
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/calendar/js/main.min.js"></script>

<script>
    var diasLetivos = <?= json_encode(array_map(function ($d) {
                            return [
                                'title' => $d->evento,
                                'start' => $d->data,
                                'id' => $d->uuid,
                                'allDay' => true
                            ];
                        }, $dias ?? [])) ?>;
</script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/calendar/custom/selectable-calendar.js"></script>

<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/apex/apexcharts.min.js"></script>

</body>

</html>