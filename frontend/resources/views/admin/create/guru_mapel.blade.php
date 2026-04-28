@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Guru Mapel</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('guru_mapel') }}">Data Guru Mapel</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Guru Mapel
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tambah Data Guru Mapel</h4>
                    <form class="forms-sample" method="post" action="{{ route('guru_mapel.store') }}"
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
                            <label for="id_guru">Guru</label>
                            <select class="form-control" name="id_guru" id="id_guru">
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($data_guru as $guru)
                                    <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_mapel">Mata Pelajaran</label>
                            <select class="form-control" name="id_mapel" id="id_mapel">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach ($data_mapel as $mapel)
                                    <option value="{{ $mapel->id_mapel }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_kelas">Kelas</label>
                            <select class="form-control" name="id_kelas" id="id_kelas">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($data_kelas as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="aktif">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="aktif" id="aktif">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('guru_mapel') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
