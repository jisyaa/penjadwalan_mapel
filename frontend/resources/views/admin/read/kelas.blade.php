@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Kelas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('kelas.create') }}">Tambah Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data Kelas
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Data Kelas</h4>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jumlah Siswa</th>
                                    <th>Wali Kelas</th>
                                    <th>Nama Ruang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_kelas as $kelas)
                                    <tr>
                                        <td>{{ $kelas->nama_kelas }}</td>
                                        <td>{{ $kelas->tingkat }}</td>
                                        <td>{{ $kelas->jumlah_siswa }}</td>
                                        <td>{{ $kelas->wali_kelas }}</td>
                                        <td>{{ $kelas->r_ruang->nama_ruang ?? 'Tidak Ada' }}</td>
                                        <td>
                                            <div class="row d-flex">
                                                <div class="col-auto mb-1">
                                                    <a href="{{ route('kelas.edit', ['id' => $kelas->id_kelas]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <span class="mdi mdi-file-edit me-1"></span>Edit
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <a data-bs-toggle="modal"
                                                        data-bs-target="#staticBackdrop{{ $kelas->id_kelas }}"
                                                        class="btn btn-sm btn-danger">
                                                        <span class="mdi mdi-delete-outline me-1"></span>Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="staticBackdrop{{ $kelas->id_kelas }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="staticBackdropLabel" aria-hidden="true">>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi
                                                        Hapus Data</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah kamu yakin ingin menghapus data
                                                        <b>{{ $kelas->nama_kelas }}</b>
                                                    </p>
                                                </div>
                                                <div class="modal-footer justify-content-between">

                                                    <form action="{{ route('kelas.delete', ['id' => $kelas->id_kelas]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-default"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Ya,
                                                            Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
