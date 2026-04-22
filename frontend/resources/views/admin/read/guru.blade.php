@extends('admin')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Guru</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('guru.create') }}">Tambah Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data Guru
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
                        <h4 class="card-title">Data Guru</h4>
                        {{-- <a href="{{ route('guru.export') }}" class="btn btn-success d-flex align-items-center ms-auto">
                            <span class="mdi mdi-export me-1"></span> Export
                        </a> --}}
                        {{-- <a data-bs-toggle="modal" data-bs-target="#import"
                            class="btn btn-warning d-flex align-items-center ms-3">
                            <span class="mdi mdi-import me-1"></span> Import
                        </a> --}}
                    </div>
                    {{-- <div class="modal fade" id="import" tabindex="-1" aria-labelledby="importLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="importlabel">New message
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('guru.import') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="file" class="col-form-label">Import File</label>
                                            <input type="file" class="form-control" name="file" id="file">
                                            @error('file')
                                                <small>{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Guru</th>
                                    <th>NIP</th>
                                    <th>Jam Mingguan</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_guru as $guru)
                                    <tr>
                                        <td>{{ $guru->nama_guru }}</td>
                                        <td>{{ $guru->nip }}</td>
                                        <td>{{ $guru->jam_mingguan }}</td>
                                        <td>{{ $guru->mapel }}</td>
                                        <td>
                                            <div class="row d-flex">
                                                <div class="col-auto mb-1">
                                                    <a href="{{ route('guru.edit', ['id' => $guru->id_guru]) }}"
                                                        class="btn btn-primary mb-2 d-flex align-items-center">
                                                        <span class="mdi mdi-file-edit me-1"></span>Edit
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <a data-bs-toggle="modal"
                                                        data-bs-target="#staticBackdrop{{ $guru->id_guru }}"
                                                        class="btn btn-danger d-flex align-items-center mb-2">
                                                        <span class="mdi mdi-delete-outline me-1"></span>Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="staticBackdrop{{ $guru->id_guru }}"
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
                                                        <b>{{ $guru->nama }}</b>
                                                    </p>
                                                </div>
                                                <div class="modal-footer justify-content-between">

                                                    <form action="{{ route('guru.delete', ['id' => $guru->id_guru]) }}"
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
