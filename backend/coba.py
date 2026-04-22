import random
import numpy as np
from collections import defaultdict
import pandas as pd
import mysql.connector
from mysql.connector import Error

# ============================================
# KONEKSI DATABASE
# ============================================

def get_db_connection():
    """Membuat koneksi ke database MySQL"""
    try:
        connection = mysql.connector.connect(
            host='localhost',      # Ganti dengan host Anda
            database='db_penjadwalan',  # Ganti dengan nama database Anda
            user='root',       # Ganti dengan username MySQL Anda
            password=''    # Ganti dengan password MySQL Anda
        )
        return connection
    except Error as e:
        print(f"Error koneksi database: {e}")
        return None

def load_data_from_db():
    """Mengambil semua data yang diperlukan dari database"""
    
    conn = get_db_connection()
    if conn is None:
        print("Gagal koneksi ke database. Menggunakan data contoh...")
        return load_sample_data()
    
    cursor = conn.cursor(dictionary=True)
    
    # 1. Ambil data mapel
    cursor.execute("SELECT * FROM mapel")
    mapel_data = cursor.fetchall()
    
    # 2. Ambil data guru
    cursor.execute("SELECT * FROM guru")
    guru_data = cursor.fetchall()
    
    # 3. Ambil data kelas (19 kelas)
    cursor.execute("SELECT * FROM kelas LIMIT 19")
    kelas_data = cursor.fetchall()
    
    # 4. Ambil data guru_mapel yang aktif
    cursor.execute("""
        SELECT gm.*, m.nama_mapel, m.jam_per_minggu, g.nama_guru, k.nama_kelas
        FROM guru_mapel gm
        JOIN mapel m ON gm.id_mapel = m.id_mapel
        JOIN guru g ON gm.id_guru = g.id_guru
        JOIN kelas k ON gm.id_kelas = k.id_kelas
        WHERE gm.aktif = 'aktif'
    """)
    guru_mapel_data = cursor.fetchall()
    
    conn.close()
    
    return {
        'mapel': mapel_data,
        'guru': guru_data,
        'kelas': kelas_data,
        'guru_mapel': guru_mapel_data
    }

def load_sample_data():
    """Data contoh jika database tidak tersedia"""
    mapel_data = [
        {'id_mapel': 1, 'nama_mapel': 'Matematika', 'jam_per_minggu': 5, 'kategori': 'Teori'},
        {'id_mapel': 2, 'nama_mapel': 'Bahasa Indonesia', 'jam_per_minggu': 6, 'kategori': 'Teori'},
        {'id_mapel': 3, 'nama_mapel': 'IPA', 'jam_per_minggu': 5, 'kategori': 'Teori'},
        {'id_mapel': 4, 'nama_mapel': 'IPS', 'jam_per_minggu': 4, 'kategori': 'Teori'},
        {'id_mapel': 5, 'nama_mapel': 'Bahasa Inggris', 'jam_per_minggu': 4, 'kategori': 'Teori'},
        {'id_mapel': 6, 'nama_mapel': 'PAI', 'jam_per_minggu': 3, 'kategori': 'Teori'},
        {'id_mapel': 7, 'nama_mapel': 'Pendidikan Pancasila', 'jam_per_minggu': 3, 'kategori': 'Teori'},
        {'id_mapel': 8, 'nama_mapel': 'PJOK', 'jam_per_minggu': 3, 'kategori': 'Praktek'},
        {'id_mapel': 9, 'nama_mapel': 'Seni Budaya dan Prakarya', 'jam_per_minggu': 3, 'kategori': 'Teori'},
        {'id_mapel': 10, 'nama_mapel': 'Informatika', 'jam_per_minggu': 3, 'kategori': 'Teori'},
        {'id_mapel': 11, 'nama_mapel': 'BK', 'jam_per_minggu': 1, 'kategori': 'Teori'},
    ]
    
    guru_data = [
        {'id_guru': 31, 'nama_guru': 'Bu Ani', 'nip': '12345'},
        {'id_guru': 30, 'nama_guru': 'Pak Budi', 'nip': '12346'},
        {'id_guru': 25, 'nama_guru': 'Bu Citra', 'nip': '12347'},
        {'id_guru': 33, 'nama_guru': 'Pak Dedi', 'nip': '12348'},
    ]
    
    kelas_data = [
        {'id_kelas': 14, 'nama_kelas': '10A'},
        {'id_kelas': 15, 'nama_kelas': '10B'},
        {'id_kelas': 16, 'nama_kelas': '10C'},
        {'id_kelas': 17, 'nama_kelas': '11A'},
        {'id_kelas': 18, 'nama_kelas': '11B'},
        {'id_kelas': 19, 'nama_kelas': '11C'},
        {'id_kelas': 20, 'nama_kelas': '12A'},
        {'id_kelas': 21, 'nama_kelas': '12B'},
        {'id_kelas': 22, 'nama_kelas': '12C'},
    ]
    
    # Data contoh guru_mapel
    guru_mapel_data = []
    for id_kelas in [14, 15, 16, 17, 18, 19, 20, 21, 22]:
        for id_mapel in range(1, 12):
            if id_mapel <= 4:
                id_guru = 31 if id_mapel == 1 else (30 if id_mapel == 2 else (25 if id_mapel == 3 else 33))
            else:
                id_guru = 31 + (id_mapel % 4)
            guru_mapel_data.append({
                'id_guru_mapel': len(guru_mapel_data) + 1,
                'id_guru': id_guru,
                'id_mapel': id_mapel,
                'id_kelas': id_kelas,
                'aktif': 'aktif',
                'nama_mapel': mapel_data[id_mapel-1]['nama_mapel'],
                'jam_per_minggu': mapel_data[id_mapel-1]['jam_per_minggu'],
                'nama_guru': f"Guru {id_guru}",
                'nama_kelas': f"Kelas {id_kelas}"
            })
    
    return {
        'mapel': mapel_data,
        'guru': guru_data,
        'kelas': kelas_data[:19],  # Ambil 19 kelas
        'guru_mapel': guru_mapel_data
    }

# ============================================
# PROSES DATA
# ============================================

def process_data(data):
    """Memproses data dari database ke struktur yang diperlukan"""
    
    # Mapping
    mapel_info = {m['id_mapel']: m for m in data['mapel']}
    guru_info = {g['id_guru']: g for g in data['guru']}
    kelas_info = {k['id_kelas']: k for k in data['kelas']}
    
    # Daftar kelas
    kelas_list = [k['id_kelas'] for k in data['kelas']]
    
    # Parameter jam per hari
    jam_per_hari = {
        "Senin": 9,
        "Selasa": 9,
        "Rabu": 8,
        "Kamis": 9,
        "Jumat": 5
    }
    
    hari_list = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"]
    TOTAL_JAM = sum(jam_per_hari.values())
    
    # Daftar slot
    slots = []
    for h, hari in enumerate(hari_list):
        for j in range(jam_per_hari[hari]):
            slots.append((h, j))
    
    # Mapping untuk akses cepat
    mapel_to_guru = {}  # id_mapel -> id_guru (asumsi 1 guru per mapel per kelas)
    guru_to_mapel = defaultdict(list)  # id_guru -> list of id_mapel
    kelas_to_mapel_guru = defaultdict(list)  # id_kelas -> list of (id_mapel, id_guru)
    
    # Beban mengajar guru (jam/minggu) dari database mapel
    guru_beban_dari_mapel = defaultdict(int)
    mapel_beban = {m['id_mapel']: m['jam_per_minggu'] for m in data['mapel']}
    
    for gm in data['guru_mapel']:
        id_kelas = gm['id_kelas']
        id_mapel = gm['id_mapel']
        id_guru = gm['id_guru']
        
        if id_kelas in kelas_list:  # Hanya untuk 19 kelas
            mapel_to_guru[id_mapel] = id_guru
            guru_to_mapel[id_guru].append(id_mapel)
            kelas_to_mapel_guru[id_kelas].append((id_mapel, id_guru))
            
            # Beban guru dihitung dari jam_per_minggu mapel
            # karena satu guru bisa mengajar mapel yang sama di banyak kelas
            guru_beban_dari_mapel[id_guru] += mapel_beban.get(id_mapel, 0)
    
    # Buat mapping nama untuk output
    id_mapel_to_nama = {m['id_mapel']: m['nama_mapel'] for m in data['mapel']}
    id_guru_to_nama = {g['id_guru']: g['nama_guru'] for g in data['guru']}
    id_kelas_to_nama = {k['id_kelas']: k['nama_kelas'] for k in data['kelas']}
    
    return {
        'kelas_list': kelas_list,
        'slots': slots,
        'hari_list': hari_list,
        'jam_per_hari': jam_per_hari,
        'total_jam': TOTAL_JAM,
        'mapel_to_guru': mapel_to_guru,
        'guru_to_mapel': dict(guru_to_mapel),
        'kelas_to_mapel_guru': dict(kelas_to_mapel_guru),
        'guru_beban': dict(guru_beban_dari_mapel),
        'mapel_beban': mapel_beban,
        'id_mapel_to_nama': id_mapel_to_nama,
        'id_guru_to_nama': id_guru_to_nama,
        'id_kelas_to_nama': id_kelas_to_nama,
    }

# ============================================
# FUNGSI FITNESS (dengan constraint jam_per_minggu mapel)
# ============================================

def hitung_jam_mapel_per_kelas(jadwal, data):
    """Menghitung jumlah jam per mapel untuk setiap kelas"""
    jam_mapel = defaultdict(lambda: defaultdict(int))
    for id_kelas in data['kelas_list']:
        for slot in data['slots']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel:
                jam_mapel[id_kelas][id_mapel] += 1
    return jam_mapel

def hitung_fitness(jadwal, data):
    """
    Fitness: semakin rendah semakin baik
    Penalty:
    - Bentrok guru: +100 per bentrok
    - Jam mapel tidak sesuai target: +10 per jam kelebihan/kekurangan
    - Beban guru tidak sesuai: +5 per jam kelebihan/kekurangan
    - Kelas tidak penuh: +1000 per slot kosong
    - Guru mengajar di kelas tidak diizinkan: +50 per kejadian
    """
    penalty = 0
    
    # 1. Cek bentrok guru (sangat berat)
    for slot in data['slots']:
        guru_di_slot = {}
        for id_kelas in data['kelas_list']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel and id_mapel in data['mapel_to_guru']:
                id_guru = data['mapel_to_guru'][id_mapel]
                if id_guru in guru_di_slot:
                    penalty += 1000  # Diperbesar dari 100
                else:
                    guru_di_slot[id_guru] = id_kelas
    
    # 2. Cek jam mapel per kelas (PALING PENTING - diperbesar)
    jam_mapel_aktual = hitung_jam_mapel_per_kelas(jadwal, data)
    for id_kelas in data['kelas_list']:
        for id_mapel, target_jam in data['mapel_beban'].items():
            aktual_jam = jam_mapel_aktual[id_kelas].get(id_mapel, 0)
            selisih = abs(aktual_jam - target_jam)
            if selisih > 0:
                # Penalty kuadratik agar lebih agresif
                penalty += (selisih ** 2) * 50  # Diperbesar dan kuadratik
    
    # 3. Pastikan total jam per kelas = 40
    for id_kelas in data['kelas_list']:
        total_jam_terisi = sum(1 for slot in data['slots'] 
                               if jadwal[id_kelas][slot] is not None)
        if total_jam_terisi != data['total_jam']:
            penalty += abs(total_jam_terisi - data['total_jam']) * 1000
    
    # 4. Cek beban mengajar guru
    beban_guru_aktual = defaultdict(int)
    for id_kelas in data['kelas_list']:
        for slot in data['slots']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel and id_mapel in data['mapel_to_guru']:
                id_guru = data['mapel_to_guru'][id_mapel]
                beban_guru_aktual[id_guru] += 1
    
    for id_guru, target_beban in data['guru_beban'].items():
        aktual_beban = beban_guru_aktual.get(id_guru, 0)
        selisih = abs(aktual_beban - target_beban)
        if selisih > 0:
            penalty += selisih * 10
    
    # 5. Cek guru tidak diizinkan
    for id_kelas in data['kelas_list']:
        mapel_guru_diizinkan = data['kelas_to_mapel_guru'].get(id_kelas, [])
        for slot in data['slots']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel:
                id_guru = data['mapel_to_guru'].get(id_mapel)
                if id_guru and (id_mapel, id_guru) not in mapel_guru_diizinkan:
                    penalty += 500  # Diperbesar
    
    return penalty

def repair_jadwal(jadwal, data):
    """
    Memperbaiki jadwal agar jumlah jam setiap mapel sesuai target
    """
    for id_kelas in data['kelas_list']:
        # Hitung jam mapel saat ini
        jam_mapel_saat_ini = defaultdict(int)
        for slot in data['slots']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel:
                jam_mapel_saat_ini[id_mapel] += 1
        
        # Identifikasi kelebihan dan kekurangan
        kelebihan = []
        kekurangan = []
        
        for id_mapel, target in data['mapel_beban'].items():
            saat_ini = jam_mapel_saat_ini.get(id_mapel, 0)
            selisih = saat_ini - target
            if selisih > 0:
                kelebihan.extend([id_mapel] * selisih)
            elif selisih < 0:
                kekurangan.extend([id_mapel] * (-selisih))
        
        # Jika ada kelebihan dan kekurangan, lakukan perbaikan
        if kelebihan and kekurangan:
            # Cari slot yang berisi mapel kelebihan
            for id_mapel_lebih in kelebihan:
                # Cari slot dengan mapel ini
                for slot in data['slots']:
                    if jadwal[id_kelas][slot] == id_mapel_lebih:
                        # Ganti dengan mapel yang kurang
                        if kekurangan:
                            id_mapel_kurang = kekurangan.pop(0)
                            # Pastikan mapel pengganti diizinkan di kelas ini
                            mapel_guru_list = data['kelas_to_mapel_guru'].get(id_kelas, [])
                            if any(m == id_mapel_kurang for m, _ in mapel_guru_list):
                                jadwal[id_kelas][slot] = id_mapel_kurang
                                break
    
    return jadwal

def generate_seimbang_jadwal(data):
    """
    Membuat jadwal awal dengan distribusi jam mapel yang seimbang
    """
    jadwal = {}
    
    for id_kelas in data['kelas_list']:
        jadwal_kelas = {}
        
        # Buat daftar mapel yang harus diisi (repeat sesuai target jam)
        mapel_yang_harus_diajar = []
        for id_mapel, target_jam in data['mapel_beban'].items():
            # Cek apakah mapel ini boleh diajar di kelas ini
            mapel_guru_list = data['kelas_to_mapel_guru'].get(id_kelas, [])
            if any(m == id_mapel for m, _ in mapel_guru_list):
                mapel_yang_harus_diajar.extend([id_mapel] * target_jam)
        
        # Jika total tidak 40, tambahkan mapel yang paling sering muncul
        while len(mapel_yang_harus_diajar) < data['total_jam']:
            # Tambahkan mapel yang paling banyak jamnya
            if mapel_yang_harus_diajar:
                mapel_yang_harus_diajar.append(random.choice(mapel_yang_harus_diajar))
        
        # Acak urutan
        random.shuffle(mapel_yang_harus_diajar)
        
        # Assign ke slot
        for i, slot in enumerate(data['slots']):
            if i < len(mapel_yang_harus_diajar):
                jadwal_kelas[slot] = mapel_yang_harus_diajar[i]
            else:
                # Fallback: pilih mapel random
                if mapel_yang_harus_diajar:
                    jadwal_kelas[slot] = random.choice(mapel_yang_harus_diajar)
        
        jadwal[id_kelas] = jadwal_kelas
    
    return jadwal

# ============================================
# REPRESENTASI & OPERATOR GENETIKA
# ============================================

def generate_random_jadwal(data):
    """Membuat jadwal acak dengan distribusi seimbang"""
    return generate_seimbang_jadwal(data)

def mutate_dengan_prioritas(individual, data, mutation_rate=0.1):
    """
    Mutasi dengan prioritas menjaga keseimbangan jam mapel
    """
    jadwal = decode_jadwal(individual, data)
    
    for id_kelas in data['kelas_list']:
        if random.random() < mutation_rate:
            # Pilih 2 slot berbeda untuk ditukar (swap mutation)
            slot1 = random.choice(data['slots'])
            slot2 = random.choice(data['slots'])
            
            # Tukar mapel
            mapel1 = jadwal[id_kelas][slot1]
            mapel2 = jadwal[id_kelas][slot2]
            
            # Pastikan pertukaran valid (mapel boleh di kelas ini)
            mapel_guru_list = data['kelas_to_mapel_guru'].get(id_kelas, [])
            mapel_yg_diizinkan = [m for m, _ in mapel_guru_list]
            
            if mapel2 in mapel_yg_diizinkan and mapel1 in mapel_yg_diizinkan:
                jadwal[id_kelas][slot1] = mapel2
                jadwal[id_kelas][slot2] = mapel1
    
    # Repair setelah mutasi
    jadwal = repair_jadwal(jadwal, data)
    
    return encode_jadwal(jadwal, data)

def encode_jadwal(jadwal, data):
    """Encode jadwal ke list of integers"""
    encoded = []
    for id_kelas in data['kelas_list']:
        for slot in data['slots']:
            encoded.append(jadwal[id_kelas][slot])
    return encoded

def decode_jadwal(encoded, data):
    """Decode dari list integer ke jadwal dict"""
    jadwal = {}
    idx = 0
    for id_kelas in data['kelas_list']:
        jadwal_kelas = {}
        for slot in data['slots']:
            jadwal_kelas[slot] = encoded[idx]
            idx += 1
        jadwal[id_kelas] = jadwal_kelas
    return jadwal

def crossover(parent1, parent2):
    """Two-point crossover"""
    size = len(parent1)
    point1 = random.randint(1, size // 3)
    point2 = random.randint(point1 + 1, size - 1)
    
    anak1 = parent1[:point1] + parent2[point1:point2] + parent1[point2:]
    anak2 = parent2[:point1] + parent1[point1:point2] + parent2[point2:]
    return anak1, anak2

def mutate(individual, data, mutation_rate=0.05):
    """Mutasi dengan mempertimbangkan mapel yang diizinkan per kelas"""
    jadwal = decode_jadwal(individual, data)
    
    for id_kelas in data['kelas_list']:
        if random.random() < mutation_rate:
            jumlah_mutasi = random.randint(1, 2)
            mapel_guru_list = data['kelas_to_mapel_guru'].get(id_kelas, [])
            if mapel_guru_list:
                for _ in range(jumlah_mutasi):
                    slot = random.choice(data['slots'])
                    id_mapel_baru, _ = random.choice(mapel_guru_list)
                    jadwal[id_kelas][slot] = id_mapel_baru
    
    return encode_jadwal(jadwal, data)

def seleksi_turnamen(populasi, fitness_scores, tournament_size=3):
    """Seleksi turnamen"""
    terbaik = None
    fitness_terbaik = float('inf')
    
    for _ in range(tournament_size):
        idx = random.randint(0, len(populasi) - 1)
        if fitness_scores[idx] < fitness_terbaik:
            fitness_terbaik = fitness_scores[idx]
            terbaik = populasi[idx]
    
    return terbaik

# ============================================
# ALGORITMA GENETIKA UTAMA
# ============================================

def algoritma_genetika(data, populasi_size=100, generasi=500, 
                       mutation_rate=0.1, crossover_rate=0.8):
    
    # Inisialisasi populasi
    populasi = [encode_jadwal(generate_random_jadwal(data), data) 
                for _ in range(populasi_size)]
    
    best_fitness_history = []
    best_individual = None
    best_fitness = float('inf')
    
    for gen in range(generasi):
        fitness_scores = []
        for ind in populasi:
            jadwal = decode_jadwal(ind, data)
            fit = hitung_fitness(jadwal, data)
            fitness_scores.append(fit)
            
            if fit < best_fitness:
                best_fitness = fit
                best_individual = ind.copy()
        
        best_fitness_history.append(best_fitness)
        
        if gen % 50 == 0:
            print(f"Generasi {gen}: Best Fitness = {best_fitness}")
        
        if best_fitness == 0:
            print(f"✅ Solusi optimal ditemukan di generasi {gen}")
            break
        
        new_populasi = []
        
        # Elitisme: 2 individu terbaik
        sorted_indices = np.argsort(fitness_scores)
        for idx in sorted_indices[:2]:
            new_populasi.append(populasi[idx].copy())
        
        while len(new_populasi) < populasi_size:
            parent1 = seleksi_turnamen(populasi, fitness_scores)
            parent2 = seleksi_turnamen(populasi, fitness_scores)
            
            if random.random() < crossover_rate:
                anak1, anak2 = crossover(parent1, parent2)
            else:
                anak1, anak2 = parent1.copy(), parent2.copy()
            
            anak1 = mutate(anak1, data, mutation_rate)
            anak2 = mutate(anak2, data, mutation_rate)
            
            new_populasi.append(anak1)
            if len(new_populasi) < populasi_size:
                new_populasi.append(anak2)
        
        populasi = new_populasi
    
    return best_individual, best_fitness, best_fitness_history

def algoritma_genetika_diperbaiki(data, 
                                  populasi_size=200,
                                  generasi=2000,
                                  mutation_rate=0.15,
                                  crossover_rate=0.85):
    
    # Inisialisasi populasi dengan jadwal seimbang
    print("Membuat populasi awal dengan distribusi jam seimbang...")
    populasi = []
    for i in range(populasi_size):
        jadwal = generate_seimbang_jadwal(data)
        populasi.append(encode_jadwal(jadwal, data))
        if (i + 1) % 50 == 0:
            print(f"  Populasi {i+1}/{populasi_size}")
    
    best_fitness_history = []
    best_individual = None
    best_fitness = float('inf')
    fitness_tanpa_perbaikan = 0
    
    for gen in range(generasi):
        fitness_scores = []
        for ind in populasi:
            jadwal = decode_jadwal(ind, data)
            fit = hitung_fitness(jadwal, data)
            fitness_scores.append(fit)
            
            if fit < best_fitness:
                best_fitness = fit
                best_individual = ind.copy()
        
        best_fitness_history.append(best_fitness)
        
        if gen % 100 == 0:
            print(f"Generasi {gen}: Best Fitness = {best_fitness}")
            
            # Cek apakah ada perbaikan
            if best_fitness == fitness_tanpa_perbaikan:
                print(f"  ⚠️  Fitness stagnan, meningkatkan mutation rate...")
                mutation_rate = min(0.3, mutation_rate * 1.1)
            else:
                fitness_tanpa_perbaikan = best_fitness
        
        if best_fitness == 0:
            print(f"✅ Solusi optimal ditemukan di generasi {gen}")
            break
        
        # Seleksi & reproduksi
        new_populasi = []
        
        # Elitisme: 4 individu terbaik
        sorted_indices = np.argsort(fitness_scores)
        for idx in sorted_indices[:4]:
            new_populasi.append(populasi[idx].copy())
        
        while len(new_populasi) < populasi_size:
            parent1 = seleksi_turnamen(populasi, fitness_scores, tournament_size=5)
            parent2 = seleksi_turnamen(populasi, fitness_scores, tournament_size=5)
            
            if random.random() < crossover_rate:
                anak1, anak2 = crossover(parent1, parent2)
            else:
                anak1, anak2 = parent1.copy(), parent2.copy()
            
            anak1 = mutate_dengan_prioritas(anak1, data, mutation_rate)
            anak2 = mutate_dengan_prioritas(anak2, data, mutation_rate)
            
            new_populasi.append(anak1)
            if len(new_populasi) < populasi_size:
                new_populasi.append(anak2)
        
        populasi = new_populasi
        
        # Adaptive mutation rate
        if gen > 0 and gen % 200 == 0:
            mutation_rate = max(0.05, mutation_rate * 0.95)
    
    return best_individual, best_fitness, best_fitness_history

# ============================================
# FUNGSI VALIDASI JAM MAPEL
# ============================================

def validasi_jam_mapel(jadwal, data):
    """Memeriksa apakah semua jam mapel sesuai target"""
    semua_sesuai = True
    jam_mapel = hitung_jam_mapel_per_kelas(jadwal, data)
    
    for id_kelas in data['kelas_list']:
        nama_kelas = data['id_kelas_to_nama'].get(id_kelas, f"Kelas {id_kelas}")
        for id_mapel, target in data['mapel_beban'].items():
            aktual = jam_mapel[id_kelas].get(id_mapel, 0)
            if aktual != target:
                semua_sesuai = False
                nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
                print(f"  ❌ {nama_kelas} - {nama_mapel}: target {target}, aktual {aktual}")
    
    return semua_sesuai

# ============================================
# FUNGSI OUTPUT
# ============================================

def tampilkan_jadwal(jadwal, data):
    """Menampilkan jadwal dalam format tabel per kelas"""
    for id_kelas in data['kelas_list']:
        nama_kelas = data['id_kelas_to_nama'].get(id_kelas, f"Kelas {id_kelas}")
        print(f"\n{'='*90}")
        print(f"📚 JADWAL KELAS {nama_kelas}")
        print(f"{'='*90}")
        
        print(f"{'Hari':<12}", end="")
        for j in range(max(data['jam_per_hari'].values())):
            print(f"| Jam {j+1:<3}", end="")
        print("|")
        print("-" * (14 + 8 * max(data['jam_per_hari'].values())))
        
        for h, hari in enumerate(data['hari_list']):
            print(f"{hari:<12}", end="")
            jam_max = data['jam_per_hari'][hari]
            for j in range(jam_max):
                id_mapel = jadwal[id_kelas][(h, j)]
                nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
                print(f"| {nama_mapel[:10]:<5}", end="")
            print("|")
        print()

def tampilkan_analisis_jam_mapel(jadwal, data):
    """Menampilkan analisis pemenuhan jam mapel per kelas"""
    print("\n" + "=" * 80)
    print("📊 ANALISIS PEMENUHAN JAM MATA PELAJARAN PER KELAS")
    print("=" * 80)
    
    jam_mapel_aktual = hitung_jam_mapel_per_kelas(jadwal, data)
    
    for id_kelas in data['kelas_list']:
        nama_kelas = data['id_kelas_to_nama'].get(id_kelas, f"Kelas {id_kelas}")
        print(f"\n{'-'*40}")
        print(f"Kelas {nama_kelas}:")
        print(f"{'Mapel':<30} | {'Target':<8} | {'Aktual':<8} | {'Status':<10}")
        print("-" * 60)
        
        for id_mapel, target in data['mapel_beban'].items():
            nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
            aktual = jam_mapel_aktual[id_kelas].get(id_mapel, 0)
            status = "✅" if aktual == target else f"⚠️ ({aktual - target:+d})"
            print(f"{nama_mapel:<30} | {target:<8} | {aktual:<8} | {status:<10}")
        print()

def tampilkan_beban_guru(jadwal, data):
    """Menampilkan beban mengajar guru"""
    print("\n" + "=" * 60)
    print("📊 BEBAN MENGAJAR GURU")
    print("=" * 60)
    
    beban_aktual = defaultdict(int)
    for id_kelas in data['kelas_list']:
        for slot in data['slots']:
            id_mapel = jadwal[id_kelas][slot]
            if id_mapel and id_mapel in data['mapel_to_guru']:
                id_guru = data['mapel_to_guru'][id_mapel]
                beban_aktual[id_guru] += 1
    
    data_list = []
    for id_guru, target in data['guru_beban'].items():
        nama_guru = data['id_guru_to_nama'].get(id_guru, f"Guru {id_guru}")
        aktual = beban_aktual.get(id_guru, 0)
        selisih = aktual - target
        status = "✅" if aktual == target else f"⚠️ ({'+' if selisih > 0 else ''}{selisih})"
        data_list.append([nama_guru, target, aktual, status])
    
    df = pd.DataFrame(data_list, columns=["Guru", "Target Jam", "Aktual Jam", "Status"])
    print(df.to_string(index=False))

def simpan_ke_excel(jadwal, data, filename="jadwal_hasil.xlsx"):
    """Menyimpan jadwal ke file Excel"""
    with pd.ExcelWriter(filename, engine='openpyxl') as writer:
        # Sheet untuk setiap kelas
        for id_kelas in data['kelas_list']:
            nama_kelas = data['id_kelas_to_nama'].get(id_kelas, f"Kelas_{id_kelas}")
            rows = []
            for h, hari in enumerate(data['hari_list']):
                row = {"Hari": hari}
                for j in range(data['jam_per_hari'][hari]):
                    id_mapel = jadwal[id_kelas][(h, j)]
                    nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
                    id_guru = data['mapel_to_guru'].get(id_mapel)
                    nama_guru = data['id_guru_to_nama'].get(id_guru, f"G{id_guru}") if id_guru else "-"
                    row[f"Jam {j+1}"] = f"{nama_mapel}\n({nama_guru})"
                rows.append(row)
            df = pd.DataFrame(rows)
            df.to_excel(writer, sheet_name=nama_kelas, index=False)
        
        # Sheet analisis jam mapel
        jam_mapel_aktual = hitung_jam_mapel_per_kelas(jadwal, data)
        analisis_rows = []
        for id_kelas in data['kelas_list']:
            nama_kelas = data['id_kelas_to_nama'].get(id_kelas, f"Kelas {id_kelas}")
            for id_mapel, target in data['mapel_beban'].items():
                nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
                aktual = jam_mapel_aktual[id_kelas].get(id_mapel, 0)
                analisis_rows.append({
                    'Kelas': nama_kelas,
                    'Mata Pelajaran': nama_mapel,
                    'Target Jam': target,
                    'Aktual Jam': aktual,
                    'Selisih': aktual - target,
                    'Status': 'OK' if aktual == target else 'MISMATCH'
                })
        df_analisis = pd.DataFrame(analisis_rows)
        df_analisis.to_excel(writer, sheet_name="Analisis_Jam_Mapel", index=False)
    
    print(f"\n✅ Jadwal disimpan ke {filename}")

# ============================================
# MAIN PROGRAM
# ============================================

# def main():
#     print("=" * 60)
#     print("SISTEM PENJADWALAN MATA PELAJARAN")
#     print("DENGAN ALGORITMA GENETIKA")
#     print("=" * 60)
    
#     # Load data dari database
#     print("\n📂 Mengambil data dari database...")
#     raw_data = load_data_from_db()
    
#     # Proses data
#     print("📊 Memproses data...")
#     data = process_data(raw_data)
    
#     # Tampilkan konfigurasi
#     print(f"\n📅 Konfigurasi Jam:")
#     for hari, jam in data['jam_per_hari'].items():
#         print(f"   {hari}: {jam} jam")
#     print(f"   Total: {data['total_jam']} jam/minggu")
    
#     print(f"\n🏫 Jumlah kelas: {len(data['kelas_list'])}")
#     print(f"📚 Jumlah mata pelajaran: {len(data['mapel_beban'])}")
#     print(f"👩‍🏫 Jumlah guru: {len(data['guru_beban'])}")
#     print(f"⏰ Total slot: {len(data['kelas_list']) * data['total_jam']}")
#     print("-" * 60)
    
#     # Jalankan algoritma genetika
#     print("\n🔄 Menjalankan algoritma genetika...")
#     best_encoded, best_fit, history = algoritma_genetika(
#         data,
#         populasi_size=150,
#         generasi=1000,
#         mutation_rate=0.15,
#         crossover_rate=0.85
#     )
    
#     # Hasil
#     print("\n" + "=" * 60)
#     print(f"🏆 HASIL AKHIR")
#     print(f"   Fitness terbaik: {best_fit}")
#     print("=" * 60)
    
#     if best_fit == 0:
#         print("\n✅ SELAMAT! Semua constraint terpenuhi dengan sempurna!")
#     else:
#         print(f"\n⚠️  Jadwal terbaik masih memiliki penalty {best_fit}")
#         print("   (semakin mendekati 0, semakin baik)")
    
#     best_jadwal = decode_jadwal(best_encoded, data)
    
#     # Tampilkan hasil
#     tampilkan_jadwal(best_jadwal, data)
#     tampilkan_analisis_jam_mapel(best_jadwal, data)
#     tampilkan_beban_guru(best_jadwal, data)
    
#     # Simpan ke file
#     simpan_ke_excel(best_jadwal, data, "jadwal_sekolah.xlsx")

# if __name__ == "__main__":
#     main()

def main():
    print("=" * 60)
    print("SISTEM PENJADWALAN MATA PELAJARAN")
    print("DENGAN ALGORITMA GENETIKA (VERSI DIPERBAIKI)")
    print("=" * 60)
    
    # Load data dari database
    print("\n📂 Mengambil data dari database...")
    raw_data = load_data_from_db()
    
    # Proses data
    print("📊 Memproses data...")
    data = process_data(raw_data)
    
    # Tampilkan konfigurasi
    print(f"\n📅 Konfigurasi Jam:")
    for hari, jam in data['jam_per_hari'].items():
        print(f"   {hari}: {jam} jam")
    print(f"   Total: {data['total_jam']} jam/minggu")
    
    print(f"\n🏫 Jumlah kelas: {len(data['kelas_list'])}")
    print(f"📚 Jumlah mata pelajaran: {len(data['mapel_beban'])}")
    print(f"👩‍🏫 Jumlah guru: {len(data['guru_beban'])}")
    print(f"⏰ Total slot: {len(data['kelas_list']) * data['total_jam']}")
    
    # Tampilkan target jam per mapel
    print("\n🎯 TARGET JAM PER MATA PELAJARAN PER KELAS:")
    for id_mapel, target in data['mapel_beban'].items():
        nama_mapel = data['id_mapel_to_nama'].get(id_mapel, f"M{id_mapel}")
        print(f"   {nama_mapel}: {target} jam/minggu")
    
    print("-" * 60)
    
    # Jalankan algoritma genetika yang diperbaiki
    print("\n🔄 Menjalankan algoritma genetika (dengan repair mechanism)...")
    best_encoded, best_fit, history = algoritma_genetika_diperbaiki(
        data,
        populasi_size=200,
        generasi=2000,
        mutation_rate=0.15,
        crossover_rate=0.85
    )
    
    # Hasil
    print("\n" + "=" * 60)
    print(f"🏆 HASIL AKHIR")
    print(f"   Fitness terbaik: {best_fit}")
    print("=" * 60)
    
    best_jadwal = decode_jadwal(best_encoded, data)
    
    # Validasi jam mapel
    print("\n🔍 VALIDASI JAM MATA PELAJARAN:")
    if validasi_jam_mapel(best_jadwal, data):
        print("   ✅ SEMUA jam mapel sudah sesuai target!")
    else:
        print("   ⚠️ Masih ada ketidaksesuaian jam mapel")
    
    # Tampilkan analisis lengkap
    tampilkan_analisis_jam_mapel(best_jadwal, data)
    tampilkan_beban_guru(best_jadwal, data)
    
    # Simpan ke file
    simpan_ke_excel(best_jadwal, data, "jadwal_sekolah_fixed.xlsx")

if __name__ == "__main__":
    main()