<div id="rfidStep" style="display:none;" class="slide-in">
    <h4 class="mb-3">Step 2: Daftar RFID & Sidik Jari</h4>
    <div class="row">
        <div class="col-md-6 text-center mb-3">
            <i class="bi bi-credit-card fs-1 text-primary"></i><br>
            <button type="button" class="btn btn-outline-primary" id="scanRFIDBtn">Scan RFID</button>
            <div id="rfidResult" class="mt-2"></div>
        </div>
        <div class="col-md-6 text-center mb-3">
            <i class="bi bi-fingerprint fs-1 text-primary"></i><br>
            <button type="button" class="btn btn-outline-primary" id="scanFingerBtn">Scan Sidik Jari</button>
            <div id="fingerResult" class="mt-2"></div>
        </div>
    </div>
    <div class="step-btns">
        <button class="btn btn-secondary" onclick="stepBack(1)">Sebelumnya</button>
        <button class="btn btn-success btn-lg" id="submitReg" disabled>Selesai & Simpan</button>
    </div>
</div>
