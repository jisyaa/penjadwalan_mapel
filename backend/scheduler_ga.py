import random
import mysql.connector
from collections import defaultdict
import json

# =========================
# PARAMETER GA
# =========================

POPULATION_SIZE =60
GENERATIONS = 100
MUTATION_RATE = 0.1
ELITISM = 3
TOURNAMENT_SIZE = 3

# =========================
# SESSION RULE (SOFT CONSTRAINT)
# =========================

SESSION_RULE = {
    1: [1],
    3: [3],
    4: [2,2],
    5: [3,2],
    6: [2,2,2]
}


# =========================
# KONEKSI DATABASE
# =========================

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_penjadwalan"
)

cursor = conn.cursor(dictionary=True)


# =========================
# LOAD DATA
# =========================

def load_data():

    # waktu valid
    cursor.execute("""
    SELECT id_waktu, hari, jam_ke
    FROM waktu
    WHERE keterangan='' OR keterangan IS NULL
    ORDER BY hari, jam_ke
    """)
    waktu = cursor.fetchall()

    # kelas
    cursor.execute("SELECT id_kelas FROM kelas")
    kelas = cursor.fetchall()

    # mapel
    cursor.execute("SELECT id_mapel, jam_per_minggu FROM mapel")
    mapel = cursor.fetchall()

    # guru_mapel
    cursor.execute("""
    SELECT id_guru_mapel,id_guru, id_mapel, id_kelas
    FROM guru_mapel
    WHERE aktif='aktif'
    """)
    guru_mapel = cursor.fetchall()

    return waktu, kelas, mapel, guru_mapel


waktu_list, kelas_list, mapel_list, guru_mapel_list = load_data()


# =========================
# BUAT DICTIONARY GURU_MAPEL
# =========================

guru_mapel_dict = {}

for gm in guru_mapel_list:
    guru_mapel_dict[(gm['id_kelas'], gm['id_mapel'])] = {
        "id_guru": gm["id_guru"],
        "id_guru_mapel": gm["id_guru_mapel"]
    }

# =========================
# SESSION MAPEL
# =========================

def generate_mapel_sessions():

    sessions = {}

    for kelas in kelas_list:

        id_kelas = kelas['id_kelas']
        sessions[id_kelas] = []

        for mapel in mapel_list:

            id_mapel = mapel['id_mapel']
            jam = mapel['jam_per_minggu']

            pattern = SESSION_RULE.get(jam, [1]*jam)

            for durasi in pattern:

                sessions[id_kelas].append({
                    "mapel": id_mapel,
                    "durasi": durasi
                })

    return sessions


mapel_sessions = generate_mapel_sessions()


# =========================
# GENERATE INITIAL SCHEDULE
# =========================

def generate_individual():

    schedule = []

    for kelas in kelas_list:

        id_kelas = kelas['id_kelas']
        slots = waktu_list.copy()
        random.shuffle(slots)

        idx = 0

        for sesi in mapel_sessions[id_kelas]:

            mapel = sesi["mapel"]
            durasi = sesi["durasi"]

            gm = guru_mapel_dict.get((id_kelas, mapel))

            for d in range(durasi):

                waktu = slots[idx]

                gene = {
                    "kelas": id_kelas,
                    "waktu": waktu["id_waktu"],
                    "hari": waktu["hari"],
                    "jam": waktu["jam_ke"],
                    "mapel": mapel,
                    "guru": gm["id_guru"],
                    "guru_mapel": gm["id_guru_mapel"]
                }

                schedule.append(gene)

                idx += 1

    return schedule


# =========================
# FITNESS FUNCTION
# =========================

# def fitness(schedule):

#     penalty = 0

#     guru_time = {}
#     kelas_time = {}

#     for gene in schedule:

#         guru = gene["guru"]
#         kelas = gene["kelas"]
#         waktu = gene["waktu"]

#         if (guru, waktu) in guru_time:
#             penalty += 1000
#         else:
#             guru_time[(guru, waktu)] = True

#         if (kelas, waktu) in kelas_time:
#             penalty += 1000
#         else:
#             kelas_time[(kelas, waktu)] = True

#         if not check_kelas_full(schedule):
#             penalty += 5000

#     return 1 / (1 + penalty)

def fitness_improved(schedule):
    """Fitness function dengan penalti untuk slot kosong"""
    penalty = 0
    
    guru_time = {}
    kelas_time = {}
    
    # Periksa setiap gene
    for gene in schedule:
        guru = gene["guru"]
        kelas = gene["kelas"]
        waktu = gene["waktu"]
        
        # Bentrok guru
        if (guru, waktu) in guru_time:
            penalty += 1000
        else:
            guru_time[(guru, waktu)] = True
        
        # Bentrok kelas
        if (kelas, waktu) in kelas_time:
            penalty += 1000
        else:
            kelas_time[(kelas, waktu)] = True
    
    # Periksa apakah semua slot terisi untuk setiap kelas
    if not check_all_slots_filled(schedule):
        penalty += 10000  # Penalti besar untuk slot kosong
    
    # Periksa apakah jadwal memenuhi hard constraint
    if not check_hard_constraints(schedule):
        penalty += 50000  # Penalti sangat besar untuk hard constraint yang dilanggar
    
    return 1 / (1 + penalty)

# =========================
# TOURNAMENT SELECTION
# =========================

def tournament_selection(population):

    selected = random.sample(population, TOURNAMENT_SIZE)
    selected.sort(key=lambda x: fitness_improved(x), reverse=True)

    return selected[0]
    

# =========================
# CROSSOVER
# =========================

def crossover(parent1, parent2):

    point = random.randint(0, len(parent1)-1)

    child = parent1[:point] + parent2[point:]

    return child


# =========================
# MUTATION
# =========================

def mutation(schedule):

    if random.random() < MUTATION_RATE:

        kelas = random.choice(kelas_list)["id_kelas"]

        genes = [g for g in schedule if g["kelas"] == kelas]

        if len(genes) >= 2:

            g1, g2 = random.sample(genes, 2)

            g1["waktu"], g2["waktu"] = g2["waktu"], g1["waktu"]

    return schedule

# def repair_schedule(schedule):

#     guru_time = {}
#     kelas_time = {}

#     for gene in schedule:

#         guru = gene["guru"]
#         kelas = gene["kelas"]
#         waktu = gene["waktu"]

#         # perbaiki bentrok guru
#         if (guru, waktu) in guru_time:

#             new_time = random.choice(waktu_list)["id_waktu"]
#             gene["waktu"] = new_time

#         else:
#             guru_time[(guru, waktu)] = True

#         # perbaiki bentrok kelas
#         if (kelas, waktu) in kelas_time:

#             new_time = random.choice(waktu_list)["id_waktu"]
#             gene["waktu"] = new_time

#         else:
#             kelas_time[(kelas, waktu)] = True

#     return schedule

def check_kelas_full(schedule):

    kelas_slots = defaultdict(set)

    for gene in schedule:
        kelas_slots[gene["kelas"]].add(gene["waktu"])

    for kelas in kelas_list:

        if len(kelas_slots[kelas["id_kelas"]]) != len(waktu_list):
            return False

    return True

# =========================
# GENETIC ALGORITHM
# =========================

# def genetic_algorithm():

#     population = [generate_individual() for _ in range(POPULATION_SIZE)]

#     best = None
#     fitness_history = []

#     for gen in range(GENERATIONS):

#         population.sort(key=lambda x: fitness(x), reverse=True)

#         best_fitness = fitness(population[0])
#         fitness_history.append(best_fitness)

#         if best is None or best_fitness > fitness(best):
#             best = population[0]

#         print(f"Generasi {gen} Fitness: {fitness(population[0])}")

#         new_population = population[:ELITISM]

#         while len(new_population) < POPULATION_SIZE:

#             p1 = tournament_selection(population)
#             p2 = tournament_selection(population)

#             child = crossover(p1, p2)
#             child = mutation(child)
#             child = repair_schedule(child)

#             new_population.append(child)

#         population = new_population

#     return best, fitness_history

def convert_full_output(best_schedule, fitness_history):

    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="db_penjadwalan"
    )

    cursor = conn.cursor(dictionary=True)

    schedule_result = []

    for item in best_schedule:

        cursor.execute("""
        SELECT 
            g.nama_guru,
            m.nama_mapel,
            k.nama_kelas,
            w.hari,
            w.jam_ke
        FROM guru_mapel gm
        JOIN guru g ON g.id_guru = gm.id_guru
        JOIN mapel m ON m.id_mapel = gm.id_mapel
        JOIN kelas k ON k.id_kelas = gm.id_kelas
        JOIN waktu w ON w.id_waktu = %s
        WHERE gm.id_guru_mapel = %s
        """, (item["waktu"], item["guru_mapel"]))

        data = cursor.fetchone()

        schedule_result.append({
            "id_guru_mapel": item["guru_mapel"],
            "id_waktu": item["waktu"],
            "guru": data["nama_guru"],
            "mapel": data["nama_mapel"],
            "kelas": data["nama_kelas"],
            "hari": data["hari"],
            "jam": data["jam_ke"]
        })

    conn.close()

    return {
        "fitness_per_generation": fitness_history,
        "schedule": schedule_result
    }

def save_to_json(data, filename="jadwal_terbaik.json"):
    with open(filename, "w") as f:
        json.dump(data, f, indent=2)

# =========================
# JALANKAN ALGORITMA
# =========================

# if __name__ == "__main__":

#     best_schedule, fitness_history = genetic_algorithm()
#     output_data = convert_full_output(best_schedule, fitness_history)
#     save_to_json(output_data)

#     # =========================
#     # TAMPILKAN HASIL
#     # =========================

#     print("\nJADWAL TERBAIK\n")

#     for gene in best_schedule[:50]:
#         print(gene)


# =========================
# FUNGSI UNTUK MENGISI SEMUA SLOT WAKTU
# =========================

def get_available_waktu():
    """Mendapatkan semua slot waktu yang tersedia"""
    cursor.execute("""
    SELECT id_waktu, hari, jam_ke
    FROM waktu
    WHERE keterangan='' OR keterangan IS NULL
    ORDER BY hari, jam_ke
    """)
    return cursor.fetchall()

def generate_individual_fixed():
    """Generate individual dengan memastikan semua slot terisi"""
    schedule = []
    
    for kelas in kelas_list:
        id_kelas = kelas['id_kelas']
        
        # Ambil semua slot waktu yang tersedia
        available_slots = get_available_waktu()
        
        # Buat daftar sesi untuk kelas ini
        class_sessions = []
        for sesi in mapel_sessions[id_kelas]:
            mapel = sesi["mapel"]
            durasi = sesi["durasi"]
            
            gm = guru_mapel_dict.get((id_kelas, mapel))
            if gm is None:
                print(f"Warning: Tidak ada guru untuk kelas {id_kelas} mapel {mapel}")
                continue
            
            # Tambahkan sesi dengan durasi
            for d in range(durasi):
                class_sessions.append({
                    "mapel": mapel,
                    "durasi": 1,
                    "guru": gm["id_guru"],
                    "guru_mapel": gm["id_guru_mapel"]
                })
        
        # Jika jumlah sesi tidak sama dengan jumlah slot, tambahkan dummy session
        if len(class_sessions) != len(available_slots):
            print(f"Warning: Kelas {id_kelas} memiliki {len(class_sessions)} sesi tetapi {len(available_slots)} slot")
            
            # Tambahkan sesi kosong atau duplikat mapel tertentu
            while len(class_sessions) < len(available_slots):
                # Bisa tambahkan mapel yang sama atau sesi kosong
                # Pilih mapel pertama sebagai pengisi
                if class_sessions:
                    dummy = class_sessions[0].copy()
                    class_sessions.append(dummy)
                else:
                    break
        
        # Acak sesi
        random.shuffle(class_sessions)
        
        # Assign sesi ke slot waktu
        for idx, slot in enumerate(available_slots):
            if idx < len(class_sessions):
                gene = {
                    "kelas": id_kelas,
                    "waktu": slot["id_waktu"],
                    "hari": slot["hari"],
                    "jam": slot["jam_ke"],
                    "mapel": class_sessions[idx]["mapel"],
                    "guru": class_sessions[idx]["guru"],
                    "guru_mapel": class_sessions[idx]["guru_mapel"]
                }
                schedule.append(gene)
    
    return schedule

# =========================
# PERBAIKI FUNGSI REPAIR_SCHEDULE
# =========================

def repair_schedule_fixed(schedule):
    """Repair schedule dengan memastikan semua slot terisi dan tidak bentrok"""
    
    # Kelompokkan berdasarkan kelas
    schedule_by_class = defaultdict(list)
    for gene in schedule:
        schedule_by_class[gene["kelas"]].append(gene)
    
    repaired_schedule = []
    available_slots = get_available_waktu()
    
    for kelas in kelas_list:
        id_kelas = kelas['id_kelas']
        class_genes = schedule_by_class[id_kelas]
        
        # Buat mapping slot yang sudah digunakan
        used_slots = {gene["waktu"]: gene for gene in class_genes}
        
        # Cek apakah semua slot terisi
        all_slots = [slot["id_waktu"] for slot in available_slots]
        
        # Untuk slot yang kosong, isi dengan mapel yang sama seperti sebelumnya
        for slot_id in all_slots:
            if slot_id not in used_slots:
                # Cari mapel yang ada di kelas ini
                if class_genes:
                    # Ambil mapel dari gene pertama
                    template_gene = class_genes[0].copy()
                    # Update dengan slot yang kosong
                    template_gene["waktu"] = slot_id
                    
                    # Update hari dan jam
                    for slot in available_slots:
                        if slot["id_waktu"] == slot_id:
                            template_gene["hari"] = slot["hari"]
                            template_gene["jam"] = slot["jam_ke"]
                            break
                    
                    repaired_schedule.append(template_gene)
                else:
                    print(f"Error: Kelas {id_kelas} tidak memiliki jadwal sama sekali")
            else:
                repaired_schedule.append(used_slots[slot_id])
    
    # Perbaiki bentrok guru dan kelas
    guru_time = {}
    kelas_time = {}
    final_schedule = []
    
    for gene in repaired_schedule:
        guru = gene["guru"]
        kelas = gene["kelas"]
        waktu = gene["waktu"]
        
        # Cek bentrok guru
        if (guru, waktu) in guru_time:
            # Cari slot alternatif
            available_slots = get_available_waktu()
            for alt_slot in available_slots:
                alt_id = alt_slot["id_waktu"]
                if (guru, alt_id) not in guru_time and (kelas, alt_id) not in kelas_time:
                    gene["waktu"] = alt_id
                    gene["hari"] = alt_slot["hari"]
                    gene["jam"] = alt_slot["jam_ke"]
                    guru_time[(guru, alt_id)] = True
                    kelas_time[(kelas, alt_id)] = True
                    break
        else:
            guru_time[(guru, waktu)] = True
        
        # Cek bentrok kelas
        if (kelas, waktu) in kelas_time and (guru, waktu) in guru_time:
            # Sudah ditangani di atas
            pass
        else:
            kelas_time[(kelas, waktu)] = True
        
        final_schedule.append(gene)
    
    return final_schedule

def check_all_slots_filled(schedule):
    """Memeriksa apakah semua slot waktu terisi untuk setiap kelas"""
    available_slots = get_available_waktu()
    total_slots = len(available_slots)
    
    schedule_by_class = defaultdict(list)
    for gene in schedule:
        schedule_by_class[gene["kelas"]].append(gene)
    
    for kelas in kelas_list:
        id_kelas = kelas['id_kelas']
        if len(schedule_by_class[id_kelas]) != total_slots:
            print(f"Kelas {id_kelas}: {len(schedule_by_class[id_kelas])} sesi, seharusnya {total_slots}")
            return False
    
    return True

# =========================
# MODIFIKASI FITNESS FUNCTION
# =========================

def check_hard_constraints(schedule):
    """Memeriksa semua hard constraint"""
    
    # Hard constraint 1: Tidak ada slot kosong
    if not check_all_slots_filled(schedule):
        return False
    
    # Hard constraint 2: Tidak ada bentrok guru
    guru_time = set()
    for gene in schedule:
        guru = gene["guru"]
        waktu = gene["waktu"]
        if (guru, waktu) in guru_time:
            return False
        guru_time.add((guru, waktu))
    
    # Hard constraint 3: Tidak ada bentrok kelas
    kelas_time = set()
    for gene in schedule:
        kelas = gene["kelas"]
        waktu = gene["waktu"]
        if (kelas, waktu) in kelas_time:
            return False
        kelas_time.add((kelas, waktu))
    
    return True

# =========================
# UPDATE GENETIC ALGORITHM
# =========================

def genetic_algorithm_improved():
    """Genetic algorithm dengan perbaikan"""
    population = [generate_individual_fixed() for _ in range(POPULATION_SIZE)]
    
    best = None
    fitness_history = []
    
    for gen in range(GENERATIONS):
        # Evaluasi fitness
        population_with_fitness = [(ind, fitness_improved(ind)) for ind in population]
        population_with_fitness.sort(key=lambda x: x[1], reverse=True)
        
        population = [ind for ind, _ in population_with_fitness]
        best_fitness = population_with_fitness[0][1]
        fitness_history.append(best_fitness)
        
        if best is None or best_fitness > fitness_improved(best):
            best = population[0].copy()
        
        print(f"Generasi {gen} Fitness: {best_fitness} | Best so far: {fitness_improved(best)}")
        
        # Seleksi dan reproduksi
        new_population = population[:ELITISM]
        
        while len(new_population) < POPULATION_SIZE:
            p1 = tournament_selection(population)
            p2 = tournament_selection(population)
            
            child = crossover(p1, p2)
            child = mutation(child)
            child = repair_schedule_fixed(child)
            
            new_population.append(child)
        
        population = new_population
    
    return best, fitness_history

# Jalankan algoritma yang diperbaiki
if __name__ == "__main__":
    best_schedule, fitness_history = genetic_algorithm_improved()
    
    # Verifikasi akhir
    print("\n=== VERIFIKASI AKHIR ===")
    if check_hard_constraints(best_schedule):
        print("✓ Semua hard constraint terpenuhi!")
    else:
        print("✗ Masih ada hard constraint yang dilanggar")
    
    output_data = convert_full_output(best_schedule, fitness_history)
    save_to_json(output_data)
    
    print("\nJADWAL TERBAIK\n")
    for gene in best_schedule[:50]:
        print(gene)