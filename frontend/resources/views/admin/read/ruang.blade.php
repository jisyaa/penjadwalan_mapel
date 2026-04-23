@extends('admin')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Ruang</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('ruang.create') }}">Tambah Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data Ruang
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
                        <h4 class="card-title">Data Ruang</h4>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Ruang</th>
                                    <th>Tipe</th>
                                    <th>Kapasitas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_ruang as $ruang)
                                    <tr>
                                        <td>{{ $ruang->nama_ruang }}</td>
                                        <td>{{ $ruang->tipe }}</td>
                                        <td>{{ $ruang->kapasitas }}</td>
                                        <td>
                                            <div class="row d-flex">
                                                <div class="col-auto mb-1">
                                                    <a href="{{ route('ruang.edit', ['id' => $ruang->id_ruang]) }}"
                                                        class="btn btn-info btn-sm">
                                                        <span class="mdi mdi-file-edit me-1"></span>Edit
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <a data-bs-toggle="modal"
                                                        data-bs-target="#staticBackdrop{{ $ruang->id_ruang }}"
                                                        class="btn btn-danger btn-sm">
                                                        <span class="mdi mdi-delete-outline me-1"></span>Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="staticBackdrop{{ $ruang->id_ruang }}"
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
                                                        <b>{{ $ruang->nama_ruang }}</b>
                                                    </p>
                                                </div>
                                                <div class="modal-footer justify-content-between">

                                                    <form action="{{ route('ruang.delete', ['id' => $ruang->id_ruang]) }}"
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
