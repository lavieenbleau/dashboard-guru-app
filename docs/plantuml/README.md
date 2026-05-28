# PlantUML Diagrams - DashboardGuru

Folder ini berisi diagram alur sistem DashboardGuru dalam format PlantUML.

## Daftar Diagram

### Fitur Inti Sistem
1. **[01-sistem-utama.puml](01-sistem-utama.puml)** - Flowchart Sistem Utama
2. **[02-login-autentikasi.puml](02-login-autentikasi.puml)** - Alur Login dan Autentikasi
3. **[03-manajemen-kelas.puml](03-manajemen-kelas.puml)** - Alur Manajemen Kelas
4. **[04-materi-pembelajaran.puml](04-materi-pembelajaran.puml)** - Alur Materi Pembelajaran
5. **[05-soal-latihan.puml](05-soal-latihan.puml)** - Alur Soal dan Latihan
6. **[06-tugas-penilaian.puml](06-tugas-penilaian.puml)** - Alur Tugas dan Penilaian
7. **[07-laporan-harian.puml](07-laporan-harian.puml)** - Alur Laporan Harian
8. **[08-rekap-nilai.puml](08-rekap-nilai.puml)** - Alur Rekap Nilai
9. **[09-kelas-online.puml](09-kelas-online.puml)** - Alur Kelas Online (Jitsi Meet)

### Fitur AI Question Generator (NEW)

**📊 Complete Diagram Suite:**

**Core AI Generator System:**
- **[10-ai-question-generator-system.puml](10-ai-question-generator-system.puml)** - Sistem Keseluruhan (Component Diagram)
- **[10c-ai-dual-source-architecture.puml](10c-ai-dual-source-architecture.puml)** - Arsitektur Teknis (Architecture Diagram)
- **[10d-ai-generator-sequence.puml](10d-ai-generator-sequence.puml)** - Interaksi Komponen (Sequence Diagram)
- **[10e-ai-generator-usecase.puml](10e-ai-generator-usecase.puml)** - Use Case Diagram
- **[10f-ai-generator-erd.puml](10f-ai-generator-erd.puml)** - Entity Relationship Diagram (Database Schema)
- **[10g-ai-generator-flowchart.puml](10g-ai-generator-flowchart.puml)** - Flowchart (Decision & Process Flow)
- **[10h-ai-generator-state-diagram.puml](10h-ai-generator-state-diagram.puml)** - State Diagram (Request & Question Lifecycle)

**Activity Diagrams (User Processes):**
- **[10b-ai-generator-detailed-flow.puml](10b-ai-generator-detailed-flow.puml)** - Generate Soal dengan AI (Guru)
- **[11-admin-material-upload-activity.puml](11-admin-material-upload-activity.puml)** - Admin Upload & Manage Material
- **[12-student-answer-activity.puml](12-student-answer-activity.puml)** - Student Answer Questions
- **[13-teacher-share-edit-activity.puml](13-teacher-share-edit-activity.puml)** - Teacher Share & Edit Questions

📖 **Dokumentasi AI Generator:** [AI_GENERATOR_DIAGRAMS_README.md](AI_GENERATOR_DIAGRAMS_README.md)  
📖 **Panduan Lengkap:** [AI_GENERATOR_DIAGRAMS_COMPLETE_GUIDE.md](AI_GENERATOR_DIAGRAMS_COMPLETE_GUIDE.md)

| Diagram | Tipe | Fokus |
|---------|------|-------|
| 10 | Component | Overview sistem keseluruhan |
| 10b | Activity | Generate soal (Guru) |
| 10c | Architecture | Detail teknis & layer system |
| 10d | Sequence | Timeline & interaksi komponen |
| 10e | Use Case | User requirements & features |
| 10f | ERD | Database structure & relationships |
| 10g | Flowchart | Decision logic & process flow |
| 10h | State | Request/Question lifecycle |
| 11 | Activity | Admin upload material |
| 12 | Activity | Student answer questions |
| 13 | Activity | Teacher share & edit |

## Cara Menggunakan

### Online (Recommended)

Gunakan PlantUML Online Editor:

1. Buka http://www.plantuml.com/plantuml/uml/
2. Copy isi file .puml
3. Paste di editor
4. Diagram akan otomatis di-render

### VS Code Extension

1. Install extension: **PlantUML** by jebbs

   ```
   ext install jebbs.plantuml
   ```

2. Install GraphViz:

   - **Windows**: Download dari https://graphviz.org/download/
   - **Ubuntu/Debian**: `sudo apt-get install graphviz`
   - **macOS**: `brew install graphviz`

3. Cara melihat diagram:
   - Buka file .puml
   - Tekan `Alt + D` atau klik kanan → "Preview Current Diagram"

### Generate PNG/SVG

Dari VS Code dengan PlantUML extension:

- Klik kanan pada file .puml
- Pilih "Export Current Diagram"
- Pilih format: PNG, SVG, PDF, dll.

### Command Line (Java)

```bash
# Install PlantUML
java -jar plantuml.jar

# Generate PNG
java -jar plantuml.jar diagram.puml

# Generate SVG
java -jar plantuml.jar -tsvg diagram.puml
```

## Syntax PlantUML

### Activity Diagram

```plantuml
@startuml
start
:Action;
if (Condition?) then (yes)
  :Action if yes;
else (no)
  :Action if no;
endif
stop
@enduml
```

### Common Elements

- `start` / `stop` - Mulai/akhir diagram
- `:Action;` - Activity/action
- `if (condition?) then (yes) else (no) endif` - Decision
- `switch (value?) case (option1) case (option2) endswitch` - Switch case
- `repeat :action; repeat while (condition?) is (yes)` - Loop
- `partition "Name" { }` - Grouping

## Export untuk Dokumentasi

Setelah generate gambar, simpan di folder `docs/images/diagrams/` untuk digunakan dalam dokumentasi.

## Reference

- PlantUML Official: https://plantuml.com/
- Activity Diagram Guide: https://plantuml.com/activity-diagram-beta
- Online Editor: http://www.plantuml.com/plantuml/uml/

---

**Catatan:** Semua diagram ini merupakan bagian dari dokumentasi sistem DashboardGuru dan harus diupdate seiring dengan perubahan sistem.
