@extends('admin')

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
                    <h4 class="card-title">Form Edit Data Guru Mapel</h4>
                    <form class="forms-sample" method="post"
                        action="{{ route('guru_mapel.update', ['id' => $data_guru_mapel->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                            <select class="js-example-basic-single" style="width:100%" id="id_guru" name="id_guru">
                                <option selected disabled>Pilih Guru</option>
                                @foreach ($data_guru as $guru)
                                    <option value="{{ $guru->id_guru }}"
                                        {{ $guru->id_guru == $data_guru_mapel->id_guru ? 'selected' : '' }}>
                                        {{ $guru->nama_guru }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_mapel">Mata Pelajaran</label>
                            <select class="js-example-basic-single" style="width:100%" id="id_mapel" name="id_mapel">
                                <option selected disabled>Pilih Mata Pelajaran</option>
                                @foreach ($data_mapel as $mapel)
                                    <option value="{{ $mapel->id_mapel }}"
                                        {{ $mapel->id_mapel == $data_guru_mapel->id_mapel ? 'selected' : '' }}>
                                        {{ $mapel->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_kelas">Kelas</label>
                            <select class="js-example-basic-single" style="width:100%" id="id_kelas" name="id_kelas">
                                <option selected disabled>Pilih Kelas</option>
                                @foreach ($data_kelas as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ $kelas->id_kelas == $data_guru_mapel->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="aktif">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="aktif" id="aktif">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="aktif" {{ $data_guru_mapel->aktif == 'aktif' ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="tidak" {{ $data_guru_mapel->aktif == 'tidak' ? 'selected' : '' }}>
                                        Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('guru_mapel') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    @endsection
