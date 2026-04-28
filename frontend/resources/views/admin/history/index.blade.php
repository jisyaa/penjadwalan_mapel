@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Riwayat Jadwal</h3>
            <a href="{{ route('generate-jadwal') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Generate Jadwal Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Data Waktu</h4>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal Generate</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwalMasters as $index => $master)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $master->tahun_ajaran }}</td>
                                        <td>{{ ucfirst($master->semester) }}</td>
                                        <td>{{ $master->keterangan ?? '-' }}</td>
                                        <td>{{ $master->tanggal_generate->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if ($master->aktif == 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('history.jadwal.show', $master->id_master) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger ms-2"
                                                onclick="confirmDelete({{ $master->id_master }})">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                            <form id="delete-form-{{ $master->id_master }}"
                                                action="{{ route('history.jadwal.destroy', $master->id_master) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada data jadwal
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
