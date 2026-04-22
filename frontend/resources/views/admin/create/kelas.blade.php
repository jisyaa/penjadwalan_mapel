@extends('admin')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Kelas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('kelas') }}">Data Kelas</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Kelas
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tambah Data Kelas</h4>
                    <form class="forms-sample" method="post" action="{{ route('kelas.store') }}"
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
                            <label for="nama_kelas">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas"
                                placeholder="Nama Kelas">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="tingkat">Tingkat</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="tingkat" id="tingkat">
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_siswa">Jumlah Siswa</label>
                            <input type="number" class="form-control" id="jumlah_siswa" name="jumlah_siswa"
                                placeholder ="Jumlah Siswa">
                        </div>
                        <div class="form-group">
                            <label for="wali_kelas">Wali Kelas</label>
                            <select class="js-example-basic-single" style="width:100%" id="wali_kelas" name="wali_kelas">
                                <option selected disabled>Pilih Wali Kelas</option>
                                @foreach ($data_guru as $guru)
                                    <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_ruang">Ruangan</label>
                            <select class="js-example-basic-single" style="width:100%" id="id_ruang" name="id_ruang">
                                <option selected disabled>Pilih Ruangan</option>
                                @foreach ($data_ruang as $ruang)
                                    <option value="{{ $ruang->id_ruang }}">{{ $ruang->nama_ruang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('kelas') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
