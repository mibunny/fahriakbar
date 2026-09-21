// ====== PARAM SAW ======
const bobot = { pendapatan: 0.27, pekerjaan: 0.27, tanggungan: 0.25, rumah: 0.22 };
const tipe = { pendapatan: 'cost', pekerjaan: 'cost', tanggungan: 'benefit', rumah: 'cost' };

let nilaiInput = {};
let UID = '', FID = '';
let currentCategory = null;

// ===== SAFE NORMALIZE =====
function normalize(val, type, maxVal = 5, minVal = 1) {
    if (val <= 0) val = minVal; // cegah nol atau negatif
    if (type === 'benefit') {
        return val / maxVal;       // benefit: nilai / max skala
    } else {
        return minVal / val;       // cost: min skala / nilai
    }
}

// ===== HITUNG SAW =====
function hitungSAW(val) {
    let skor = 0, rows = '';

    Object.keys(bobot).forEach(k => {
        const norm = normalize(val[k], tipe[k]);
        const skorK = norm * bobot[k];

        rows += `<tr>
            <td>${k.toUpperCase()}</td>
            <td>${val[k]}</td>
            <td>${norm.toFixed(3)}</td>
            <td>${bobot[k]}</td>
            <td>${skorK.toFixed(3)}</td>
        </tr>`;

        skor += skorK;
    });

    // Pastikan skor 0-1
    skor = Math.max(0, Math.min(1, skor));

    return { skor, rows };
}

// ===== TENTUKAN KATEGORI =====
function determineCategory(score) {
    if (score >= 0.75) return { golongan: 'A', kuota: 1000, class: 'success' };
    if (score >= 0.60) return { golongan: 'B', kuota: 750, class: 'primary' };
    if (score >= 0.45) return { golongan: 'C', kuota: 500, class: 'warning' };
    return { golongan: 'Tidak Layak', kuota: 0, class: 'danger' };
}

// ===== CEK SEMUA FIELD TERISI =====
function allFilled(v) {
    return v.pendapatan && v.pekerjaan && v.tanggungan && v.rumah;
}

// ===== EVENT INPUT KRITERIA =====
document.querySelectorAll('.kriteria-input').forEach(inp => {
    inp.addEventListener('change', function () {
        nilaiInput[this.id] = parseInt(this.value || '0', 10);

        if (allFilled(nilaiInput)) {
            const hasil = hitungSAW(nilaiInput);
            currentCategory = determineCategory(hasil.skor);

            console.log('Hasil perhitungan SAW:', hasil.skor); // Debug

            // Update preview table
            document.getElementById('previewBody').innerHTML = hasil.rows;

            // Update progress bar
            const percent = Math.round(hasil.skor * 100);
            const sb = document.getElementById('scoreBar');
            sb.style.width = percent + '%';
            sb.innerText = hasil.skor.toFixed(3);
            sb.className = 'progress-bar ' + (currentCategory.kuota > 0 ? 'bg-success' : 'bg-danger');

            // Update status & kuota
            document.getElementById('scoreStatus').innerText = `GOLONGAN ${currentCategory.golongan}`;
            document.getElementById('scoreStatus').className = 'score-status text-' + currentCategory.class;
            document.getElementById('kuotaBantuan').innerHTML =
                currentCategory.kuota > 0
                    ? `<span class="badge bg-${currentCategory.class} kuota-badge">Kuota: ${currentCategory.kuota} gram</span>`
                    : `<span class="badge bg-danger kuota-badge">Tidak Menerima Bantuan</span>`;

            // Simpan data ke button dataset
            const btn = document.getElementById('toRFID');
            btn.disabled = (currentCategory.kuota <= 0);
            btn.dataset.skor = hasil.skor.toString();
            btn.dataset.golongan = currentCategory.golongan;
            btn.dataset.kuota = String(currentCategory.kuota);
            btn.dataset.eligible = (currentCategory.golongan !== 'Tidak Layak' ? 'ya' : 'tidak');

            document.getElementById('previewHasil').style.display = 'block';
        } else {
            currentCategory = null;
            document.getElementById('previewHasil').style.display = 'none';
            document.getElementById('toRFID').disabled = true;
        }
    });
});

// ===== STEP KONTROL =====
document.getElementById('toRFID').addEventListener('click', function () {
    if (!this.dataset.golongan) {
        alert('Lengkapi kriteria dan hitung skor terlebih dahulu.');
        return;
    }
    document.getElementById('formStep').style.display = 'none';
    document.getElementById('rfidStep').style.display = '';
    setStep(2);
});

function stepBack(step) {
    if (step === 1) {
        document.getElementById('rfidStep').style.display = 'none';
        document.getElementById('formStep').style.display = '';
        setStep(1);
    } else if (step === 2) {
        document.getElementById('successStep').style.display = 'none';
        document.getElementById('rfidStep').style.display = '';
        setStep(2);
    }
}

// ===== SCAN RFID (pakai file rfid.json) =====
document.getElementById('scanRFIDBtn').addEventListener('click', function () {
    let btn = this;
    btn.disabled = true;
    btn.textContent = "Mendeteksi...";

    fetch('rfid.json?_=' + Date.now())
        .then(res => res.json())
        .then(data => {
            if (data.uid) {
                UID = data.uid;
                document.getElementById('rfidResult').innerHTML = `<span class="text-success">Terdaftar: ${UID}</span>`;
                btn.textContent = "Terdaftar";
                btn.classList.add('btn-success');

                fetch('clear_json.php?file=rfid.json');
                checkRFIDFinger();
            } else {
                btn.disabled = false;
                btn.textContent = "Scan RFID Lagi";
                alert("Belum ada UID diterima dari ESP32");
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.textContent = "Scan RFID";
            alert("Gagal membaca rfid.json");
        });
});

// ===== SCAN FINGER (pakai file finger.json) =====
document.getElementById('scanFingerBtn').addEventListener('click', function () {
    let btn = this;
    btn.disabled = true;
    btn.textContent = "Mendeteksi...";

    fetch('finger.json?_=' + Date.now())
        .then(res => res.json())
        .then(data => {
            if (data.fid) {
                FID = data.fid;
                document.getElementById('fingerResult').innerHTML = `<span class="text-success">ID #${FID}</span>`;
                btn.textContent = "Terdaftar";
                btn.classList.add('btn-success');

                fetch('clear_json.php?file=finger.json');
                checkRFIDFinger();
            } else {
                btn.disabled = false;
                btn.textContent = "Scan Finger Lagi";
                alert("Belum ada Finger ID dari ESP32");
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.textContent = "Scan Finger";
            alert("Gagal membaca finger.json");
        });
});

function checkRFIDFinger() {
    if (UID && FID) document.getElementById('submitReg').disabled = false;
}

// ===== SUBMIT REGISTRATION =====
document.getElementById('submitReg').addEventListener('click', function (e) {
    e.preventDefault();

    const btn = document.getElementById('toRFID');
    const golFromDS = btn.dataset.golongan || 'Tidak Layak';
    const eligible = btn.dataset.eligible || (golFromDS !== 'Tidak Layak' ? 'ya' : 'tidak');
    const kuota = parseInt(btn.dataset.kuota || '0', 10);
    const skor = parseFloat(btn.dataset.skor || '0');

    if (!currentCategory && !btn.dataset.golongan) {
        alert('Skor belum dihitung. Kembali ke langkah sebelumnya.');
        return;
    }

    const nama = document.getElementById('nama').value.trim();
    const nik = document.getElementById('nik').value.trim();
    const hp = document.getElementById('hp').value.trim();
    const alamat = document.getElementById('alamat').value.trim();

    if (!nama || !nik || !hp || !alamat) {
        alert('Semua field wajib harus diisi');
        return;
    }

    if (!UID || !FID) {
        alert('RFID dan Fingerprint harus sudah terdaftar');
        return;
    }

    const data = {
        nama: nama,
        nik: nik,
        hp: hp,
        alamat: alamat,
        pendapatan: parseInt(nilaiInput['pendapatan'] || '0', 10),
        pekerjaan: parseInt(nilaiInput['pekerjaan'] || '0', 10),
        tanggungan: parseInt(nilaiInput['tanggungan'] || '0', 10),
        rumah: parseInt(nilaiInput['rumah'] || '0', 10),
        saw_score: skor,
        golongan: golFromDS,
        koutaBantuan: kuota,
        uid_rfid: UID,
        finger_id: FID.toString(),
        eligible: eligible
    };

    console.log('Final data:', data);

    this.disabled = true;
    this.textContent = 'Menyimpan...';

    fetch('save_registration.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(data)
    })
        .then(async response => {
            const responseText = await response.text();
            console.log('Server response:', responseText);

            try {
                return JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                throw new Error('Response bukan JSON: ' + responseText.substring(0, 200));
            }
        })
        .then(res => {
            console.log('Parsed response:', res);
            if (res.status === 'ok') {
                document.getElementById('rfidStep').style.display = 'none';
                document.getElementById('successStep').style.display = '';
                setStep(3);
                if (res.pin && document.getElementById('pinCode')) {
                    document.getElementById('pinCode').textContent = res.pin;
                }
                // Copy button handler
                const copyBtn = document.getElementById('copyPinBtn');
                if (copyBtn) {
                    copyBtn.onclick = function () {
                        const val = (document.getElementById('pinCode')?.textContent || '').trim();
                        if (val) {
                            navigator.clipboard.writeText(val).then(() => {
                                copyBtn.textContent = 'Tersalin';
                                setTimeout(() => copyBtn.textContent = 'Salin', 1500);
                            });
                        }
                    };
                }
            } else {
                alert("Error: " + res.msg);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Daftar';
        });
});

function setStep(x) {
    for (let i = 1; i <= 3; i++) {
        const n = document.getElementById('st-' + i);
        n.className = (i < x) ? "step completed" : (i === x ? "step active" : "step");
    }
}
