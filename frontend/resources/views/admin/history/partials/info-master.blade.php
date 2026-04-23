<div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Informasi Jadwal</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="btnEditMaster"
                                    onclick="toggleMasterEdit()">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </button>
                            </div>

                            <!-- Mode Tampilan -->
                            <div id="master-view-mode">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Tahun Ajaran:</strong></label>
                                            <p id="view-tahun-ajaran">{{ $master->tahun_ajaran }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Semester:</strong></label>
                                            <p id="view-semester">{{ ucfirst($master->semester) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Status:</strong></label>
                                            <p id="view-aktif">
                                                {{ $master->aktif == 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Keterangan:</strong></label>
                                            <p id="view-keterangan">{{ $master->keterangan ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Tanggal Generate:</strong></label>
                                            <p>{{ $master->tanggal_generate->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mode Edit -->
                            <div id="master-edit-mode" style="display: none;">
                                <form id="formEditMaster" onsubmit="saveMasterChanges(event)">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>Tahun Ajaran:</strong></label>
                                                <input type="text" name="tahun_ajaran" class="form-control"
                                                    value="{{ $master->tahun_ajaran }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>Semester:</strong></label>
                                                <select name="semester" class="form-control" required>
                                                    <option value="ganjil"
                                                        {{ $master->semester == 'ganjil' ? 'selected' : '' }}>Ganjil
                                                    </option>
                                                    <option value="genap"
                                                        {{ $master->semester == 'genap' ? 'selected' : '' }}>Genap</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><strong>Status:</strong></label>
                                                <select name="aktif" class="form-control">
                                                    <option value="aktif"
                                                        {{ $master->aktif == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="tidak"
                                                        {{ $master->aktif == 'tidak' ? 'selected' : '' }}>Tidak Aktif
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><strong>Keterangan:</strong></label>
                                                <textarea name="keterangan" class="form-control" rows="2">{{ $master->keterangan }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="mdi mdi-content-save"></i> Simpan
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="cancelMasterEdit()">
                                                <i class="mdi mdi-close"></i> Batal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
