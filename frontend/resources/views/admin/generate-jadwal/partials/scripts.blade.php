<script>
    let guruMapelOptions = [];
    let optionsCache = {};

    async function loadGuruMapelOptions() {
        try {
            const response = await fetch('{{ route("get.guru.mapel") }}');
            const result = await response.json();

            if (result.success) {
                guruMapelOptions = result.data;
                guruMapelOptions.forEach(option => {
                    if (!optionsCache[option.nama_kelas]) {
                        optionsCache[option.nama_kelas] = [];
                    }
                    optionsCache[option.nama_kelas].push({
                        id: option.id_guru_mapel,
                        text: `${option.nama_guru} - ${option.nama_mapel}`
                    });
                });
                for (let kelas in optionsCache) {
                    optionsCache[kelas].sort((a, b) => a.text.localeCompare(b.text));
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function showDropdown(displayElement) {
        const cell = displayElement.closest('td');
        const select = cell.querySelector('.dropdown-select');
        const kelas = cell.dataset.kelas;
        const currentId = cell.dataset.currentId;

        select.innerHTML = '<option value="">-- Kosongkan --</option>';

        if (optionsCache[kelas]) {
            optionsCache[kelas].forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.text;
                if (option.id == currentId) optionElement.selected = true;
                select.appendChild(optionElement);
            });
        }

        displayElement.style.display = 'none';
        select.style.display = 'block';
        select.focus();

        select.onblur = function() {
            select.style.display = 'none';
            displayElement.style.display = 'block';
        };
    }

    async function updateCell(selectElement) {
        const cell = selectElement.closest('td');
        const kelas = cell.dataset.kelas;
        const hari = cell.dataset.hari;
        const jam = cell.dataset.jam;
        const idWaktu = cell.dataset.idWaktu;
        const selectedId = selectElement.value;

        if (selectedId == cell.dataset.currentId) {
            selectElement.style.display = 'none';
            cell.querySelector('.cell-display').style.display = 'block';
            return;
        }

        if (!confirm('Ubah jadwal? Halaman akan di-refresh setelah perubahan.')) {
            selectElement.style.display = 'none';
            cell.querySelector('.cell-display').style.display = 'block';
            return;
        }

        try {
            const response = await fetch('{{ route("generate-jadwal.update-cell") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kelas: kelas,
                    hari: hari,
                    jam: jam,
                    id_waktu: idWaktu,
                    id_guru_mapel: selectedId,
                    old_id_guru_mapel: cell.dataset.currentId
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', 'Data berhasil diupdate. Halaman akan di-refresh...');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert('Gagal update: ' + (result.message || 'Terjadi kesalahan'));
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server');
            window.location.reload();
        }
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    function showNotification(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.querySelector('.page-header').after(alertDiv);
        setTimeout(() => alertDiv.remove(), 3000);
    }

    document.addEventListener('DOMContentLoaded', () => loadGuruMapelOptions());
</script>
