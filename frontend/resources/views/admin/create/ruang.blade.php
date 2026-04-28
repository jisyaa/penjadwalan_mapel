@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Ruang</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('ruang') }}">Data Ruang</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Ruang
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tambah Data Ruang</h4>
                    <form class="forms-sample" method="post" action="{{ route('ruang.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="nama_ruang">Nama Ruang</label>
                            <input type="text" class="form-control" id="nama_ruang" name="nama_ruang"
                                placeholder="Nama Ruang">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="tipe">Tipe</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="tipe" id="tipe">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="kelas">Kelas</option>
                                    <option value="laboratorium">Laboratorium</option>
                                    <option value="ruangan">Ruangan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kapasitas">Kapasitas</label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                                placeholder ="Kapasitas">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('ruang') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
