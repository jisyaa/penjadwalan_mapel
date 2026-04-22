@extends('admin')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Waktu</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('waktu.create') }}">Tambah Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data Waktu
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
                        <h4 class="card-title">Data Waktu</h4>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam Ke</th>
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Selesai</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_waktu as $waktu)
                                    <tr>
                                        <td>{{ $waktu->hari }}</td>
                                        <td>{{ $waktu->jam_ke }}</td>
                                        <td>{{ $waktu->waktu_mulai }}</td>
                                        <td>{{ $waktu->waktu_selesai }}</td>
                                        <td>{{ $waktu->keterangan }}</td>
                                        <td>
                                            <div class="row d-flex">
                                                <div class="col-auto mb-1">
                                                    <a href="{{ route('waktu.edit', ['id' => $waktu->id_waktu]) }}"
                                                        class="btn btn-primary mb-2 d-flex align-items-center">
                                                        <span class="mdi mdi-file-edit me-1"></span>Edit
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <a data-bs-toggle="modal"
                                                        data-bs-target="#staticBackdrop{{ $waktu->id_waktu }}"
                                                        class="btn btn-danger d-flex align-items-center mb-2">
                                                        <span class="mdi mdi-delete-outline me-1"></span>Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="staticBackdrop{{ $waktu->id_waktu }}"
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
                                                        <b>{{ $waktu->hari }}</b>
                                                    </p>
                                                </div>
                                                <div class="modal-footer justify-content-between">

                                                    <form action="{{ route('waktu.delete', ['id' => $waktu->id_waktu]) }}"
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
