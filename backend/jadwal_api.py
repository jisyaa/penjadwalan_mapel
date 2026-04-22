from flask import Flask, jsonify, request
from flask_cors import CORS
import json
import random
import numpy as np
from collections import defaultdict
import mysql.connector
from mysql.connector import Error
from datetime import datetime

app = Flask(__name__)
CORS(app)

# ============================================
# KONFIGURASI DATABASE
# ============================================

DB_CONFIG = {
    'host': 'localhost',
    'database': 'db_penjadwalan',
    'user': 'root',
    'password': ''
}

# ============================================
# KELAS PENJADWALAN
# ============================================

class PenjadwalanGenetika:
    def __init__(self, db_config):
        self.db_config = db_config
        self.data = None
        self.best_jadwal = None
        self.best_fitness = None
        self.fitness_history = []
        
    def get_db_connection(self):
        try:
            connection = mysql.connector.connect(**self.db_config)
            return connection
        except Error as e:
            print(f"Error koneksi database: {e}")
            return None
    
    def load_data_from_db(self):
        """Mengambil semua data dari database"""
        conn = self.get_db_connection()
        if conn is None:
            return None
        
        cursor = conn.cursor(dictionary=True)
        
        try:
            # Ambil data mapel
            cursor.execute("SELECT * FROM mapel")
            mapel_data = cursor.fetchall()
            
            # Ambil data guru
            cursor.execute("SELECT * FROM guru")
            guru_data = cursor.fetchall()
            
            # Ambil data kelas (19 kelas)
            cursor.execute("SELECT * FROM kelas LIMIT 19")
            kelas_data = cursor.fetchall()
            
            # Ambil data waktu - PASTIKAN mengambil kolom keterangan
            cursor.execute("""
                SELECT id_waktu, hari, jam_ke, keterangan 
                FROM waktu 
                WHERE keterangan IS NULL OR keterangan = ''
                ORDER BY 
                    FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'),
                    jam_ke
            """)
            waktu_data = cursor.fetchall()
            
            # Jika tidak ada data dengan filter, ambil semua data waktu
            if not waktu_data:
                print("PERINGATAN: Tidak ada data dengan keterangan NULL, mengambil semua data waktu")
                cursor.execute("""
                    SELECT id_waktu, hari, jam_ke, keterangan 
                    FROM waktu 
                    ORDER BY 
                        FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'),
                        jam_ke
                """)
                waktu_data = cursor.fetchall()
            
            # Jika masih tidak ada data, buat error
            if not waktu_data:
                print("ERROR: Tidak ada data waktu sama sekali di tabel waktu")
                return None
            
            # Ambil data guru_mapel yang aktif
            cursor.execute("""
                SELECT gm.*, m.nama_mapel, m.jam_per_minggu, g.nama_guru, k.nama_kelas
                FROM guru_mapel gm
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN kelas k ON gm.id_kelas = k.id_kelas
                WHERE gm.aktif = 'aktif'
            """)
            guru_mapel_data = cursor.fetchall()
            
            print(f"Data waktu yang valid: {len(waktu_data)} jam")
            
            return {
                'mapel': mapel_data,
                'guru': guru_data,
                'kelas': kelas_data,
                'waktu': waktu_data,
                'guru_mapel': guru_mapel_data
            }
        except Error as e:
            print(f"Error query: {e}")
            return None
        finally:
            conn.close()
    
    def process_data(self, raw_data):
        """Memproses data dari database - TANPA normalisasi jam"""
        if not raw_data:
            return None
        
        waktu_list = raw_data['waktu']
        
        # Urutkan waktu berdasarkan hari dan jam_ke asli
        urutan_hari = {'Senin': 1, 'Selasa': 2, 'Rabu': 3, 'Kamis': 4, 'Jumat': 5}
        
        waktu_list.sort(key=lambda x: (urutan_hari.get(x['hari'], 99), x['jam_ke']))
        
        # JANGAN normalisasi jam_ke, gunakan jam_ke asli
        hari_list = []
        jam_per_hari = {}
        
        for w in waktu_list:
            hari = w['hari']
            if hari not in jam_per_hari:
                jam_per_hari[hari] = []
                hari_list.append(hari)
            jam_per_hari[hari].append(w['jam_ke'])
        
        # Buat mapping slot dengan jam_ke asli
        slot_ke_id_waktu = {}
        slot_ke_waktu_info = {}
        slot_ke_keterangan = {}
        id_waktu_ke_slot = {}
        
        for slot_index, w in enumerate(waktu_list):
            slot_ke_id_waktu[slot_index] = w['id_waktu']
            slot_ke_waktu_info[slot_index] = {
                'hari': w['hari'],
                'jam': w['jam_ke'],  # Gunakan jam_ke asli
                'keterangan': w.get('keterangan', '') or ''
            }
            slot_ke_keterangan[slot_index] = w.get('keterangan', '') or ''
            id_waktu_ke_slot[w['id_waktu']] = slot_index
        
        total_jam = len(waktu_list)
        
        # Mapping data lainnya
        mapel_to_guru = {}
        kelas_to_guru_mapel = defaultdict(list)
        guru_mapel_info = {}
        
        # Beban guru dari mapel
        mapel_beban = {m['id_mapel']: m['jam_per_minggu'] for m in raw_data['mapel']}
        guru_beban = defaultdict(int)
        
        for gm in raw_data['guru_mapel']:
            id_guru_mapel = gm['id_guru_mapel']
            id_kelas = gm['id_kelas']
            id_mapel = gm['id_mapel']
            id_guru = gm['id_guru']
            
            mapel_to_guru[id_mapel] = id_guru
            kelas_to_guru_mapel[id_kelas].append((id_mapel, id_guru, id_guru_mapel))
            guru_mapel_info[id_guru_mapel] = (id_mapel, id_guru, id_kelas)
            
            # Hitung beban guru berdasarkan jam mapel
            guru_beban[id_guru] += mapel_beban.get(id_mapel, 0)
        
        # Daftar kelas
        kelas_list = [k['id_kelas'] for k in raw_data['kelas']]
        
        # Mapping nama
        id_mapel_to_nama = {m['id_mapel']: m['nama_mapel'] for m in raw_data['mapel']}
        id_guru_to_nama = {g['id_guru']: g['nama_guru'] for g in raw_data['guru']}
        id_kelas_to_nama = {k['id_kelas']: k['nama_kelas'] for k in raw_data['kelas']}
        
        print(f"Proses data selesai:")
        print(f"  - Kelas: {len(kelas_list)}")
        print(f"  - Total jam: {total_jam}")
        print(f"  - Jam per hari: { {h: sorted(j) for h, j in jam_per_hari.items()} }")
        
        return {
            'kelas_list': kelas_list,
            'waktu_list': waktu_list,
            'total_jam': total_jam,
            'jam_per_hari': jam_per_hari,  # List of jam_ke per hari
            'hari_list': hari_list,
            'id_waktu_to_slot': id_waktu_ke_slot,
            'slot_to_id_waktu': slot_ke_id_waktu,
            'slot_to_waktu_info': slot_ke_waktu_info,
            'slot_to_keterangan': slot_ke_keterangan,
            'kelas_to_guru_mapel': dict(kelas_to_guru_mapel),
            'guru_mapel_info': guru_mapel_info,
            'guru_beban': dict(guru_beban),
            'mapel_beban': mapel_beban,
            'id_mapel_to_nama': id_mapel_to_nama,
            'id_guru_to_nama': id_guru_to_nama,
            'id_kelas_to_nama': id_kelas_to_nama,
            'mapel_to_guru': mapel_to_guru,
        }
    
    def hitung_jam_mapel_per_kelas(self, jadwal):
        """Menghitung jam per mapel per kelas"""
        jam_mapel = defaultdict(lambda: defaultdict(int))
        for id_kelas in self.data['kelas_list']:
            for slot in range(self.data['total_jam']):
                id_guru_mapel = jadwal[id_kelas][slot]
                if id_guru_mapel and id_guru_mapel in self.data['guru_mapel_info']:
                    id_mapel = self.data['guru_mapel_info'][id_guru_mapel][0]
                    jam_mapel[id_kelas][id_mapel] += 1
        return jam_mapel
    
    def hitung_fitness(self, jadwal):
        """Menghitung fitness"""
        penalty = 0
        
        # 1. Cek bentrok guru
        for slot in range(self.data['total_jam']):
            guru_di_slot = {}
            for id_kelas in self.data['kelas_list']:
                id_guru_mapel = jadwal[id_kelas][slot]
                if id_guru_mapel and id_guru_mapel in self.data['guru_mapel_info']:
                    id_guru = self.data['guru_mapel_info'][id_guru_mapel][1]
                    if id_guru in guru_di_slot:
                        penalty += 1000
                    else:
                        guru_di_slot[id_guru] = id_kelas
        
        # 2. Cek jam mapel per kelas
        jam_mapel_aktual = self.hitung_jam_mapel_per_kelas(jadwal)
        for id_kelas in self.data['kelas_list']:
            for id_mapel, target_jam in self.data['mapel_beban'].items():
                aktual_jam = jam_mapel_aktual[id_kelas].get(id_mapel, 0)
                selisih = abs(aktual_jam - target_jam)
                if selisih > 0:
                    penalty += (selisih ** 2) * 50
        
        # 3. Cek beban guru
        beban_guru_aktual = defaultdict(int)
        for id_kelas in self.data['kelas_list']:
            for slot in range(self.data['total_jam']):
                id_guru_mapel = jadwal[id_kelas][slot]
                if id_guru_mapel and id_guru_mapel in self.data['guru_mapel_info']:
                    id_guru = self.data['guru_mapel_info'][id_guru_mapel][1]
                    beban_guru_aktual[id_guru] += 1
        
        for id_guru, target_beban in self.data['guru_beban'].items():
            aktual_beban = beban_guru_aktual.get(id_guru, 0)
            selisih = abs(aktual_beban - target_beban)
            if selisih > 0:
                penalty += selisih * 10
        
        return penalty
    
    def generate_initial_population(self, populasi_size):
        """Membuat populasi awal"""
        populasi = []
        
        for _ in range(populasi_size):
            jadwal = {}
            for id_kelas in self.data['kelas_list']:
                jadwal_kelas = [None] * self.data['total_jam']
                
                # Dapatkan daftar guru_mapel untuk kelas ini
                guru_mapel_list = self.data['kelas_to_guru_mapel'].get(id_kelas, [])
                if not guru_mapel_list:
                    continue
                
                # Buat daftar berdasarkan target jam mapel
                daftar_guru_mapel = []
                for id_mapel, id_guru, id_guru_mapel in guru_mapel_list:
                    target_jam = self.data['mapel_beban'].get(id_mapel, 0)
                    daftar_guru_mapel.extend([id_guru_mapel] * target_jam)
                
                # Sesuaikan jumlah dengan total jam yang tersedia
                while len(daftar_guru_mapel) < self.data['total_jam']:
                    daftar_guru_mapel.append(random.choice([gm for _, _, gm in guru_mapel_list]))
                while len(daftar_guru_mapel) > self.data['total_jam']:
                    daftar_guru_mapel.pop()
                
                random.shuffle(daftar_guru_mapel)
                
                for i in range(self.data['total_jam']):
                    jadwal_kelas[i] = daftar_guru_mapel[i]
                
                jadwal[id_kelas] = jadwal_kelas
            
            # Encode jadwal
            encoded = []
            for id_kelas in self.data['kelas_list']:
                encoded.extend(jadwal[id_kelas])
            populasi.append(encoded)
        
        return populasi
    
    def crossover(self, parent1, parent2):
        """Two-point crossover"""
        size = len(parent1)
        point1 = random.randint(1, size // 3)
        point2 = random.randint(point1 + 1, size - 1)
        
        anak1 = parent1[:point1] + parent2[point1:point2] + parent1[point2:]
        anak2 = parent2[:point1] + parent1[point1:point2] + parent2[point2:]
        return anak1, anak2
    
    def mutate(self, individual, mutation_rate=0.1):
        """Mutasi dengan swap"""
        mutated = individual.copy()
        for i in range(len(mutated)):
            if random.random() < mutation_rate:
                j = random.randint(0, len(mutated) - 1)
                mutated[i], mutated[j] = mutated[j], mutated[i]
        return mutated
    
    def seleksi_turnamen(self, populasi, fitness_scores, tournament_size=3):
        """Seleksi turnamen"""
        best_idx = None
        best_fitness = float('inf')
        
        for _ in range(tournament_size):
            idx = random.randint(0, len(populasi) - 1)
            if fitness_scores[idx] < best_fitness:
                best_fitness = fitness_scores[idx]
                best_idx = idx
        
        return populasi[best_idx]
    
    def decode_jadwal(self, encoded):
        """Decode dari list ke dict"""
        jadwal = {}
        idx = 0
        for id_kelas in self.data['kelas_list']:
            jadwal[id_kelas] = encoded[idx:idx + self.data['total_jam']]
            idx += self.data['total_jam']
        return jadwal

    def konversi_ke_output(self, jadwal_dict):
        """Konversi ke format yang sesuai dengan PHP - termasuk data target"""
        output = []
        
        # Buat data target mapel untuk setiap kelas
        target_mapel = {}
        for id_mapel, target in self.data['mapel_beban'].items():
            nama_mapel = self.data['id_mapel_to_nama'].get(id_mapel, str(id_mapel))
            target_mapel[nama_mapel] = target
        
        # Buat data target beban guru
        target_beban_guru = {}
        for id_guru, target in self.data['guru_beban'].items():
            nama_guru = self.data['id_guru_to_nama'].get(id_guru, str(id_guru))
            target_beban_guru[nama_guru] = target
        
        for id_kelas in self.data['kelas_list']:
            for slot, id_guru_mapel in enumerate(jadwal_dict[id_kelas]):
                # Ambil informasi waktu (dengan jam_ke asli)
                waktu_info = self.data['slot_to_waktu_info'].get(slot, {})
                hari = waktu_info.get('hari', '')
                jam_ke_asli = waktu_info.get('jam', 0)
                keterangan = waktu_info.get('keterangan', '')
                
                # Jika ada keterangan (upacara, istirahat, dll)
                if keterangan and keterangan.strip():
                    output.append({
                        'id_guru_mapel': None,
                        'id_waktu': self.data['slot_to_id_waktu'].get(slot),
                        'id_kelas': id_kelas,
                        'kelas': self.data['id_kelas_to_nama'].get(id_kelas, str(id_kelas)),
                        'guru': '',
                        'mapel': '',
                        'hari': hari,
                        'jam': jam_ke_asli,
                        'keterangan': keterangan,
                        'is_keterangan': True
                    })
                elif id_guru_mapel and id_guru_mapel in self.data['guru_mapel_info']:
                    id_mapel, id_guru, _ = self.data['guru_mapel_info'][id_guru_mapel]
                    
                    output.append({
                        'id_guru_mapel': id_guru_mapel,
                        'id_waktu': self.data['slot_to_id_waktu'].get(slot),
                        'id_kelas': id_kelas,
                        'kelas': self.data['id_kelas_to_nama'].get(id_kelas, str(id_kelas)),
                        'guru': self.data['id_guru_to_nama'].get(id_guru, str(id_guru)),
                        'mapel': self.data['id_mapel_to_nama'].get(id_mapel, str(id_mapel)),
                        'hari': hari,
                        'jam': jam_ke_asli,
                        'keterangan': '',
                        'is_keterangan': False
                    })
        
        # Tambahkan data target ke output
        return {
            'jadwal': output,
            'target_mapel': target_mapel,
            'target_beban_guru': target_beban_guru
        }
    
    def jalankan(self, populasi_size=150, generasi=1000):
        """Menjalankan algoritma genetika"""
        print("=" * 50)
        print("Memuat data dari database...")
        
        raw_data = self.load_data_from_db()
        if not raw_data:
            print("ERROR: Gagal memuat data dari database")
            return None, None, None
        
        # Validasi data waktu
        if not raw_data['waktu']:
            print("ERROR: Tidak ada data waktu yang valid (keterangan NULL atau kosong)")
            print("Pastikan tabel waktu memiliki data dengan keterangan = NULL atau ''")
            return None, None, None
        
        print(f"Data waktu yang valid: {len(raw_data['waktu'])} jam")
        
        print("Memproses data...")
        self.data = self.process_data(raw_data)
        if not self.data:
            return None, None, None
        
        print(f"Data loaded: {len(self.data['kelas_list'])} kelas, {self.data['total_jam']} jam")
        
        # Validasi total jam
        if self.data['total_jam'] != 40:
            print(f"PERINGATAN: Total jam adalah {self.data['total_jam']}, seharusnya 40 jam")
            print("Jadwal mungkin tidak optimal karena jumlah jam tidak sesuai")
        
        # Inisialisasi populasi
        print("Membuat populasi awal...")
        populasi = self.generate_initial_population(populasi_size)
        
        self.fitness_history = []
        best_individual = None
        self.best_fitness = float('inf')
        
        print("Menjalankan algoritma genetika...")
        
        for gen in range(generasi):
            # Evaluasi fitness
            fitness_scores = []
            for ind in populasi:
                jadwal = self.decode_jadwal(ind)
                fit = self.hitung_fitness(jadwal)
                fitness_scores.append(fit)
                
                if fit < self.best_fitness:
                    self.best_fitness = fit
                    best_individual = ind.copy()
            
            self.fitness_history.append(self.best_fitness)
            
            if gen % 100 == 0:
                print(f"Generasi {gen}: Best Fitness = {self.best_fitness}")
            
            if self.best_fitness == 0:
                print(f"✅ Solusi optimal ditemukan di generasi {gen}")
                break
            
            # Seleksi & reproduksi
            new_populasi = []
            
            # Elitisme (4 terbaik)
            sorted_indices = np.argsort(fitness_scores)
            for idx in sorted_indices[:4]:
                new_populasi.append(populasi[idx].copy())
            
            while len(new_populasi) < populasi_size:
                parent1 = self.seleksi_turnamen(populasi, fitness_scores)
                parent2 = self.seleksi_turnamen(populasi, fitness_scores)
                
                if random.random() < 0.85:
                    anak1, anak2 = self.crossover(parent1, parent2)
                else:
                    anak1, anak2 = parent1.copy(), parent2.copy()
                
                anak1 = self.mutate(anak1, 0.15)
                anak2 = self.mutate(anak2, 0.15)
                
                new_populasi.append(anak1)
                if len(new_populasi) < populasi_size:
                    new_populasi.append(anak2)
            
            populasi = new_populasi
        
        # Konversi jadwal terbaik
        best_jadwal_dict = self.decode_jadwal(best_individual)
        self.best_jadwal = self.konversi_ke_output(best_jadwal_dict)
        
        print(f"Selesai! Fitness terakhir: {self.best_fitness}")
        
        return self.best_jadwal, self.best_fitness, self.fitness_history


# ============================================
# INISIALISASI
# ============================================

penjadwal = PenjadwalanGenetika(DB_CONFIG)

# ============================================
# API ENDPOINTS
# ============================================

@app.route('/generate-jadwal', methods=['GET'])
def generate_jadwal():
    """Endpoint untuk generate jadwal"""
    try:
        populasi_size = request.args.get('populasi_size', 300, type=int)
        generasi = request.args.get('generasi', 3000, type=int)
        
        print(f"\n{'='*50}")
        print(f"REQUEST GENERATE JADWAL")
        print(f"Populasi size: {populasi_size}, Generasi: {generasi}")
        print(f"{'='*50}")
        
        # Jalankan algoritma
        jadwal, fitness, history = penjadwal.jalankan(
            populasi_size=populasi_size,
            generasi=generasi
        )
        
        if jadwal is None:
            return jsonify({
                'status': 'error',
                'message': 'Gagal memuat data dari database'
            }), 500
        
        # Hitung statistik bentrok
        bentrok_count = 0
        guru_bentrok = {}
        
        for j in jadwal['jadwal']:  # Ambil dari key 'jadwal'
            if not j.get('guru'):
                continue
            key = f"{j['hari']}-{j['jam']}-{j['guru']}"
            if key in guru_bentrok:
                guru_bentrok[key] += 1
            else:
                guru_bentrok[key] = 1
        
        for key, count in guru_bentrok.items():
            if count > 1:
                bentrok_count += 1
        
        return jsonify({
            'status': 'success',
            'jadwal': jadwal['jadwal'],
            'target_mapel': jadwal['target_mapel'],
            'target_beban_guru': jadwal['target_beban_guru'],
            'fitness_best': fitness,
            'fitness_history': history,
            'generasi': len(history),
            'total_data': len(jadwal['jadwal']),
            'bentrok_count': bentrok_count
        })
    
    except Exception as e:
        print(f"ERROR: {str(e)}")
        import traceback
        traceback.print_exc()
        
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/status', methods=['GET'])
def status():
    """Cek status API"""
    return jsonify({
        'status': 'running',
        'timestamp': datetime.now().isoformat(),
        'endpoints': {
            'generate': '/generate-jadwal',
            'status': '/status',
            'test-waktu': '/test-waktu'
        }
    })


@app.route('/test-waktu', methods=['GET'])
def test_waktu():
    """Endpoint untuk test query waktu"""
    try:
        conn = penjadwal.get_db_connection()
        if conn is None:
            return jsonify({'status': 'error', 'message': 'Gagal koneksi database'}), 500
        
        cursor = conn.cursor(dictionary=True)
        
        # Test query waktu dengan filter
        cursor.execute("""
            SELECT id_waktu, hari, jam_ke, keterangan 
            FROM waktu 
            WHERE keterangan IS NULL OR keterangan = ''
            ORDER BY 
                FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'),
                jam_ke
        """)
        waktu_data = cursor.fetchall()
        
        conn.close()
        
        # Hitung statistik per hari
        stat_per_hari = {}
        for w in waktu_data:
            hari = w['hari']
            if hari not in stat_per_hari:
                stat_per_hari[hari] = 0
            stat_per_hari[hari] += 1
        
        return jsonify({
            'status': 'success',
            'total_jam': len(waktu_data),
            'stat_per_hari': stat_per_hari,
            'data': waktu_data
        })
        
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500


@app.route('/test-data', methods=['GET'])
def test_data():
    """Endpoint untuk test data tanpa menjalankan GA"""
    try:
        raw_data = penjadwal.load_data_from_db()
        if not raw_data:
            return jsonify({'status': 'error', 'message': 'Gagal load data'}), 500
        
        processed = penjadwal.process_data(raw_data)
        
        return jsonify({
            'status': 'success',
            'summary': {
                'kelas': len(processed['kelas_list']),
                'total_jam': processed['total_jam'],
                'jam_per_hari': processed['jam_per_hari'],
                'guru': len(processed['guru_beban']),
                'mapel': len(processed['mapel_beban'])
            },
            'jam_per_hari_detail': processed['jam_per_hari'],
            'waktu_list': [
                {'id_waktu': w['id_waktu'], 'hari': w['hari'], 'jam_ke': w['jam_ke']} 
                for w in raw_data['waktu']
            ]
        })
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500


if __name__ == '__main__':
    print("=" * 60)
    print("API PENJADWALAN MATA PELAJARAN")
    print("Menggunakan Algoritma Genetika")
    print("=" * 60)
    print(f"Filter waktu: keterangan IS NULL OR keterangan = ''")
    print(f"Endpoint: http://127.0.0.1:8001")
    print(f"Generate: http://127.0.0.1:8001/generate-jadwal")
    print(f"Status:   http://127.0.0.1:8001/status")
    print(f"Test Waktu: http://127.0.0.1:8001/test-waktu")
    print(f"Test Data: http://127.0.0.1:8001/test-data")
    print("=" * 60)
    
    app.run(host='127.0.0.1', port=8001, debug=True)