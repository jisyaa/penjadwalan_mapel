<script>
    let guruMapelOptions = [];
    let optionsCache = {};
    let pendingChanges = new Map();
    let deletedItems = new Map();

    async function loadGuruMapelOptions() {
        try {
            const response = await fetch('{{ route('get.guru.mapel') }}');
            const result = await response.json();
            if (result.success) {
                guruMapelOptions = result.data;
                guruMapelOptions.forEach(option => {
                    if (!optionsCache[option.nama_kelas]) {
                        optionsCache[option.nama_kelas] = [];
                    }
                    optionsCache[option.nama_kelas].push({
                        id: option.id_guru_mapel,
                        text: `${option.nama_guru} - ${option.nama_mapel}`,
                        guru: option.nama_guru,
                        mapel: option.nama_mapel
                    });
                });
                for (let kelas in optionsCache) {
                    optionsCache[kelas].sort((a, b) => a.text.localeCompare(b.text));
                }
            }
        } catch (error) {
            console.error('Error loading options:', error);
        }
    }

    function getCurrentValue(cell) {
        const key = cell.dataset.key;
        if (pendingChanges.has(key)) return pendingChanges.get(key);
        if (deletedItems.has(key)) return null;
        return cell.dataset.originalId;
    }

    function updateCellDisplay(cell, guruMapelId, isDeleted = false) {
        const displayElement = cell.querySelector('.cell-display');

        if (isDeleted || !guruMapelId || guruMapelId === '') {
            displayElement.innerHTML = '<span class="text-muted">- Klik untuk isi -</span>';
            displayElement.classList.remove('bentrok');
            return;
        }

        const kelas = cell.dataset.kelas;
        const option = optionsCache[kelas]?.find(opt => opt.id == guruMapelId);

        if (option) {
            displayElement.innerHTML = `<strong>${option.guru}</strong><br><small>${option.mapel}</small>`;
        }
        checkBentrok();
        renderBentrokTab();
    }

    function showDropdownHistory(displayElement) {
        @if ($isAktif)
            const cell = displayElement.closest('.editable-cell-history');
            if (!cell) return;

            const select = cell.querySelector('.dropdown-select-history');
            const kelas = cell.dataset.kelas;
            const currentId = getCurrentValue(cell);

            if (!optionsCache[kelas]) return;

            select.innerHTML = '<option value="">-- Kosongkan --</option>';
            optionsCache[kelas].forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.text;
                if (option.id == currentId) optionElement.selected = true;
                select.appendChild(optionElement);
            });

            displayElement.style.display = 'none';
            select.style.display = 'block';
            select.focus();

            select.onblur = function() {
                select.style.display = 'none';
                displayElement.style.display = 'block';
            };
        @endif
    }

    function trackChange(selectElement) {
        const cell = selectElement.closest('.editable-cell-history');
        if (!cell) return;

        const selectedId = selectElement.value;
        const key = cell.dataset.key;
        const oldId = cell.dataset.originalId;

        selectElement.style.display = 'none';
        const displayElement = cell.querySelector('.cell-display');
        displayElement.style.display = 'block';

        if ((!selectedId && !oldId) || (selectedId == oldId)) {
            pendingChanges.delete(key);
            deletedItems.delete(key);
            cell.classList.remove('cell-changed');
            updateCellDisplay(cell, oldId, false);
            updateSaveButton();
            return;
        }

        if (!selectedId || selectedId === '') {
            deletedItems.set(key, oldId);
            pendingChanges.delete(key);
            updateCellDisplay(cell, null, true);
            cell.classList.add('cell-changed');
        } else {
            pendingChanges.set(key, selectedId);
            deletedItems.delete(key);
            updateCellDisplay(cell, selectedId, false);
            cell.classList.add('cell-changed');
        }

        updateSaveButton();
    }

    function updateSaveButton() {
        const btnSave = document.getElementById('btnSaveAllChanges');
        const btnCancel = document.getElementById('btnCancelAllChanges');
        const changeCountSpan = document.getElementById('changeCount');
        const totalChanges = pendingChanges.size + deletedItems.size;

        if (btnSave && btnCancel) {
            if (totalChanges > 0) {
                btnSave.style.display = 'inline-block';
                btnCancel.style.display = 'inline-block';
                if (changeCountSpan) changeCountSpan.textContent = totalChanges;
            } else {
                btnSave.style.display = 'none';
                btnCancel.style.display = 'none';
            }
        }
    }

    function checkBentrok() {
        const guruPerSlot = new Map();

        document.querySelectorAll('.editable-cell-history').forEach(cell => {
            let guruMapelId = getCurrentValue(cell);
            if (!guruMapelId) return;

            const option = optionsCache[cell.dataset.kelas]?.find(opt => opt.id == guruMapelId);
            if (option) {
                const slotKey = `${cell.dataset.hari}-${cell.dataset.jam}-${option.guru}`;
                if (!guruPerSlot.has(slotKey)) guruPerSlot.set(slotKey, []);
                guruPerSlot.get(slotKey).push(cell);
            }
        });

        for (let [slotKey, cells] of guruPerSlot) {
            const isBentrok = cells.length > 1;
            cells.forEach(cell => {
                const displayDiv = cell.querySelector('.cell-display');
                if (isBentrok) {
                    displayDiv.classList.add('bentrok');
                    const existingBadge = displayDiv.querySelector('.badge.bg-danger');
                    if (!existingBadge) {
                        displayDiv.innerHTML += '<br><span class="badge bg-danger">Bentrok!</span>';
                    }
                } else {
                    displayDiv.classList.remove('bentrok');
                    const badge = displayDiv.querySelector('.badge.bg-danger');
                    if (badge) badge.remove();
                }
            });
        }
    }

    function renderBentrokTab() {
        const guruPerSlot = new Map();

        document.querySelectorAll('.editable-cell-history').forEach(cell => {
            let guruMapelId = getCurrentValue(cell);
            if (!guruMapelId) return;

            const kelas = cell.dataset.kelas;
            const option = optionsCache[cell.dataset.kelas]?.find(opt => opt.id == guruMapelId);
            if (option) {
                const slotKey = `${cell.dataset.hari}-${cell.dataset.jam}-${option.guru}`;
                if (!guruPerSlot.has(slotKey)) {
                    guruPerSlot.set(slotKey, {
                        guru: option.guru,
                        hari: cell.dataset.hari,
                        jam: cell.dataset.jam,
                        kelas: []
                    });
                }
                guruPerSlot.get(slotKey).kelas.push(kelas);
            }
        });

        const bentrokOnly = [];
        for (let [key, value] of guruPerSlot) {
            if (value.kelas.length > 1) bentrokOnly.push(value);
        }

        const container = document.getElementById('bentrok-content');
        if (!container) return;

        if (bentrokOnly.length > 0) {
            let html = `
                <div class="alert alert-danger">
                    <h5><i class="mdi mdi-alert-circle"></i> Ditemukan ${bentrokOnly.length} Bentrok Guru!</h5>
                    <p>Berikut adalah detail bentrok yang terjadi:</p>
                </div>
                <table class="table table-bordered analysis-table">
                    <thead>
                        <tr><th>No</th><th>Hari</th><th>Jam Ke</th><th>Guru</th><th>Jumlah Kelas</th><th>Kelas yang Bentrok</th></tr>
                    </thead>
                    <tbody>
            `;
            bentrokOnly.forEach((bentrok, index) => {
                html += `<tr class="table-danger">
                    <td>${index + 1}</td>
                    <td><strong>${bentrok.hari}</strong></td>
                    <td><strong>${bentrok.jam}</strong></td>
                    <td><strong>${bentrok.guru}</strong></td>
                    <td><span class="badge bg-danger">${bentrok.kelas.length}</span></td>
                    <td>${bentrok.kelas.join(', ')}</td>
                </tr>`;
            });
            html +=
                `</tbody></table><div class="alert alert-warning mt-2"><strong>💡 Solusi:</strong> Bentrok terjadi karena guru mengajar di beberapa kelas pada waktu yang sama.</div>`;
            container.innerHTML = html;
        } else {
            container.innerHTML =
                `<div class="alert alert-success"><h5><i class="mdi mdi-check-circle"></i> ✅ Tidak Ada Bentrok Guru!</h5><p>Semua guru mengajar di waktu yang berbeda untuk setiap kelas.</p></div>`;
        }
    }

    window.saveAllChanges = async function() {
        const totalChanges = pendingChanges.size + deletedItems.size;
        if (totalChanges === 0) {
            showNotification('info', 'Tidak ada perubahan yang perlu disimpan');
            return;
        }
        if (!confirm(`Simpan ${totalChanges} perubahan?`)) return;

        const changes = [],
            deleted = [];
        for (let [key, newId] of pendingChanges) {
            const cell = document.querySelector(`.editable-cell-history[data-key="${key}"]`);
            if (cell) {
                changes.push({
                    id_waktu: cell.dataset.idWaktu,
                    kelas: cell.dataset.kelas,
                    id_guru_mapel: newId,
                    old_id_guru_mapel: cell.dataset.originalId
                });
            }
        }
        for (let [key, oldId] of deletedItems) {
            const cell = document.querySelector(`.editable-cell-history[data-key="${key}"]`);
            if (cell) {
                deleted.push({
                    id_waktu: cell.dataset.idWaktu,
                    kelas: cell.dataset.kelas,
                    old_id_guru_mapel: oldId
                });
            }
        }

        const btnSave = document.getElementById('btnSaveAllChanges');
        const originalText = btnSave?.innerHTML;
        if (btnSave) {
            btnSave.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...';
            btnSave.disabled = true;
        }

        try {
            const response = await fetch(
                '{{ url('history-jadwal') }}/{{ $master->id_master }}/save-changes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        changes,
                        deleted
                    })
                });
            const result = await response.json();
            if (result.success) {
                showNotification('success', 'Perubahan berhasil disimpan! Halaman akan di-refresh...');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification('error', 'Gagal menyimpan: ' + (result.message || 'Terjadi kesalahan'));
                if (btnSave) {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                }
            }
        } catch (error) {
            showNotification('error', 'Terjadi kesalahan pada server: ' + error.message);
            if (btnSave) {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
            }
        }
    };

    function cancelAllChanges() {
        if (pendingChanges.size === 0 && deletedItems.size === 0) return;
        if (!confirm('Batalkan semua perubahan?')) return;
        window.location.reload();
    }

    function showNotification(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.querySelector('.page-header').after(alertDiv);
        setTimeout(() => alertDiv.remove(), 3000);
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        if (event?.currentTarget) event.currentTarget.classList.add('active');
    }

    // Toggle edit mode
    function toggleMasterEdit() {
        document.getElementById('master-view-mode').style.display = 'none';
        document.getElementById('master-edit-mode').style.display = 'block';
    }

    function cancelMasterEdit() {
        document.getElementById('master-view-mode').style.display = 'block';
        document.getElementById('master-edit-mode').style.display = 'none';
    }

    // Simpan perubahan master
    async function saveMasterChanges(event) {
        event.preventDefault();

        const form = document.getElementById('formEditMaster');
        const formData = new FormData(form);
        const data = {
            tahun_ajaran: formData.get('tahun_ajaran'),
            semester: formData.get('semester'),
            aktif: formData.get('aktif'),
            keterangan: formData.get('keterangan')
        };

        if (!confirm('Simpan perubahan data master?')) return;

        const btnSubmit = form.querySelector('button[type="submit"]');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...';
        btnSubmit.disabled = true;

        try {
            const response = await fetch('{{ route('history.jadwal.update-master', $master->id_master) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', 'Data master berhasil diupdate');

                // Update tampilan view mode
                document.getElementById('view-tahun-ajaran').innerText = data.tahun_ajaran;
                document.getElementById('view-semester').innerText = data.semester === 'ganjil' ? 'Ganjil' :
                    'Genap';
                document.getElementById('view-keterangan').innerText = data.keterangan || '-';

                // Kembali ke view mode
                cancelMasterEdit();
            } else {
                showNotification('error', result.message || 'Gagal update data master');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan pada server');
        } finally {
            btnSubmit.innerHTML = originalText;
            btnSubmit.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadGuruMapelOptions().then(() => {
            setTimeout(() => {
                checkBentrok();
                renderBentrokTab();
            }, 100);
        });
        updateSaveButton();

        const btnSave = document.getElementById('btnSaveAllChanges');
        const btnCancel = document.getElementById('btnCancelAllChanges');
        if (btnSave) btnSave.onclick = saveAllChanges;
        if (btnCancel) btnCancel.onclick = cancelAllChanges;
    });
</script>
