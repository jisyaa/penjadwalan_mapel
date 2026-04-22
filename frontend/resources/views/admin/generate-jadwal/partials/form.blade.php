<div class="alert alert-info mt-2">
    <b>Hasil Genetic Algorithm</b><br>
    Fitness Terbaik : <b>{{ $fitness ?? 'N/A' }}</b><br>
    Jumlah Generasi : <b>{{ $generasi ?? 'N/A' }}</b><br>
    Total Data : <b>{{ count($jadwal) }}</b>
</div>

@if (isset($fitness_history) && count($fitness_history) > 0)
    <div style="width:100%;max-width:800px;margin:auto">
        <canvas id="fitnessChart"></canvas>
    </div>
@endif

<div class="row mb-3 mt-3">
    <div class="col-md-3">
        <label>Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" required>
    </div>
    <div class="col-md-3">
        <label>Semester</label>
        <select name="semester" class="form-control" required>
            <option value="">Pilih</option>
            <option value="ganjil">Ganjil</option>
            <option value="genap">Genap</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control">
    </div>
</div>
