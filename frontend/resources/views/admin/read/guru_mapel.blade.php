@extends('admin')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Guru Mapel</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('guru_mapel.create') }}">Tambah Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Data Guru Mapel
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
                        <h4 class="card-title">Data Guru Mapel</h4>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between border-bottom mb-3">
                        <ul class="nav nav-tabs" role="tablist">

                            <li class="nav-item">
                                <a class="nav-link {{ $filter == 'guru' ? 'active' : '' }}"
                                    href="{{ route('guru_mapel', ['filter' => 'guru']) }}">
                                    Berdasarkan Guru
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ $filter == 'kelas' ? 'active' : '' }}"
                                    href="{{ route('guru_mapel', ['filter' => 'kelas']) }}">
                                    Berdasarkan Kelas
                                </a>
                            </li>

                        </ul>
                    </div>

                    <div class="mt-4">

                        @foreach ($grouped_data as $group)
                            <div class="card shadow-sm mb-4 border-0">

                                {{-- HEADER CARD --}}
                                <div class="card-header d-flex justify-content-between align-items-center">

                                    <h6 class="mb-0 fw-bold">
                                        @if ($filter == 'guru')
                                            <i class="mdi mdi-account me-2 text-primary"></i>
                                            {{ $group->first()->r_guru->nama_guru ?? 'Tidak Ada' }}
                                        @else
                                            <i class="mdi mdi-google-classroom me-2 text-success"></i>
                                            {{ $group->first()->r_kelas->nama_kelas ?? 'Tidak Ada' }}
                                        @endif
                                    </h6>

                                    <span class="badge text-primary">
                                        {{ $group->count() }} Data
                                    </span>

                                </div>

                                {{-- BODY CARD --}}
                                <div class="card-body p-0">

                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    @if ($filter == 'guru')
                                                        <th>Mata Pelajaran</th>
                                                        <th>Kelas</th>
                                                    @else
                                                        <th>Mata Pelajaran</th>
                                                        <th>Guru</th>
                                                    @endif
                                                    <th>Status</th>
                                                    <th width="180">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($group as $guru_mapel)
                                                    <tr>
                                                        <td>{{ $guru_mapel->r_mapel->nama_mapel }}</td>

                                                        @if ($filter == 'guru')
                                                            <td>{{ $guru_mapel->r_kelas->nama_kelas }}</td>
                                                        @else
                                                            <td>{{ $guru_mapel->r_guru->nama_guru }}</td>
                                                        @endif

                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $guru_mapel->aktif == 'aktif' ? 'success' : 'danger' }}">
                                                                {{ ucfirst($guru_mapel->aktif) }}
                                                            </span>
                                                        </td>

                                                        <td>
                                                            <a href="{{ route('guru_mapel.edit', ['id' => $guru_mapel->id_guru_mapel]) }}"
                                                                class="btn btn-sm btn-info me-2">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>

                                                            <form
                                                                action="{{ route('guru_mapel.delete', ['id' => $guru_mapel->id_guru_mapel]) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Yakin hapus data ini?')">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </form>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
