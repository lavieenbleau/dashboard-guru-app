# AI Question Generator - Complete Diagram Guide

Panduan lengkap untuk semua 8 diagram tipe yang mendokumentasikan AI Question Generator feature dengan dual-source material system.

---

## 📊 Ringkasan 8 Diagram Tipe

### 1️⃣ Component Diagram (10-ai-question-generator-system.puml)
**Tipe:** System Architecture Overview  
**Audience:** Project Manager, Technical Lead, Architect

**Menampilkan:**
- Aktor: Guru, Admin, OpenAI API
- Major components: Controller, Service, Database
- External integrations
- Data flow antar komponen

**Gunakan untuk:**
- Presentasi stakeholder
- Dokumentasi sistem keseluruhan
- Onboarding developer baru
- Architecture review

```
Guru → SoalController → OpenAIService → OpenAI API
                     ↓
              Database (Posts, Lessons, Exercise)
```

---

### 2️⃣ Activity Diagram (10b-ai-generator-detailed-flow.puml)
**Tipe:** Business Process Flow  
**Audience:** Business Analyst, QA, Product Manager

**Menampilkan:**
- User interactions
- System processes
- Decision points
- Error handling
- Parallel flows

**Gunakan untuk:**
- Dokumentasi proses bisnis
- User training
- Test case creation
- Business requirements validation

**Alur Utama:**
```
Guru pilih materi → Backend validasi → OpenAI call 
→ Preview hasil → User review → Simpan ke database
```

---

### 3️⃣ Architecture Diagram (10c-ai-dual-source-architecture.puml)
**Tipe:** Technical Implementation Detail  
**Audience:** Backend Developer, DevOps, Architect

**Menampilkan:**
- Layer-by-layer architecture
- Token resolver & dual-source logic
- Database queries dengan filtering
- Caching strategy
- OpenAI SDK configuration

**Gunakan untuk:**
- Code implementation guidance
- Technical documentation
- Performance optimization discussion
- Security review

**Key Features:**
```
Token Parser: post: vs lesson:
Material Fetcher: DB queries dengan validation
Cache Manager: TTL-based caching
AI Client: OpenAI SDK integration
```

---

### 4️⃣ Sequence Diagram (10d-ai-generator-sequence.puml)
**Tipe:** Interaction Timeline  
**Audience:** Backend Developer, QA Engineer, DevOps

**Menampilkan:**
- HTTP request/response cycles
- Database query timeline
- OpenAI API call timing
- Error scenarios
- State transitions

**Gunakan untuk:**
- Debugging & troubleshooting
- Performance analysis
- Tracing request flow
- Understanding race conditions
- Error scenario testing

**Timeline:**
```
1. Frontend submit → 2. Validate & query DB 
→ 3. Cache check → 4. OpenAI call → 5. Parse & validate 
→ 6. Persist to DB → 7. Return to UI
```

---

### 5️⃣ Use Case Diagram (10e-ai-generator-usecase.puml)
**Tipe:** Requirements & Features  
**Audience:** Product Manager, Business Analyst, QA

**Menampilkan:**
- Actors: Guru, Admin, Student, OpenAI
- Use cases: Generate, Review, Share, Save
- Relationships: includes, extends, precedes
- Actor responsibilities

**Gunakan untuk:**
- Requirements gathering
- Feature prioritization
- System boundary definition
- Scope management
- Test case planning

**Main Use Cases:**
```
Guru:
- Access Generator → Select Material → Configure → Generate 
- Review → Edit → Save → Share

Admin:
- Upload Materials → Configure System → Monitor Usage

System:
- Read Material → Cache → Build Prompt → Call API 
- Parse → Validate → Store
```

---

### 6️⃣ Entity Relationship Diagram/ERD (10f-ai-generator-erd.puml)
**Tipe:** Database Schema  
**Audience:** DBA, Backend Developer, Architect

**Menampilkan:**
- Entity tables & attributes
- Primary keys & foreign keys
- Relationships (1:1, 1:N, M:N)
- Data types
- Constraints

**Gunakan untuk:**
- Database design review
- Migration planning
- Query optimization
- Data integrity validation
- ORM model generation

**Core Entities:**
```
Posts (teacher material)
├── has serial_id (per teacher)
└── links to Exercise

Lessons (admin material)
├── has category='materi'
└── links to Exercise

Exercise ──┬─ belongs to Serial
          ├─ has ExerciseItems
          └─ has ExerciseType

ExerciseItem ─── has ExerciseItemAnswers

AIGenerationLog ─ tracks usage & costs
```

---

### 7️⃣ Flowchart (10g-ai-generator-flowchart.puml)
**Tipe:** Decision & Process Flow  
**Audience:** Developer, QA, Business Analyst

**Menampilkan:**
- Start/end points
- Process boxes
- Decision diamonds
- Error paths
- Conditional flows

**Gunakan untuk:**
- Implementation logic
- Test case development
- Error handling planning
- Process communication
- Complexity analysis

**Main Decisions:**
```
Material valid? 
├─ NO → Error
└─ YES → Continue

Input valid?
├─ NO → Error & retry
└─ YES → Send to API

API success?
├─ NO → Handle error
└─ YES → Parse response

JSON valid?
├─ NO → Error
└─ YES → Validate questions

All questions valid?
├─ NO → Filter invalid
└─ YES → Store & preview

User review?
├─ Accept → Save
├─ Edit → Modify & retry
├─ Regenerate → Start over
└─ Cancel → Discard
```

---

### 8️⃣ State Diagram (10h-ai-generator-state-diagram.puml)
**Tipe:** Lifecycle & State Transitions  
**Audience:** Backend Developer, QA, System Analyst

**Menampilkan:**
- Request states (PENDING → PUBLISHED)
- Question states (TEMP → PUBLISHED)
- Exercise states (CREATED → ARCHIVED)
- State transitions & triggers
- Terminal states

**Gunakan untuk:**
- State machine implementation
- Lifecycle documentation
- Status tracking logic
- Event handling design
- Testing state transitions

**Request Lifecycle:**
```
PENDING → VALIDATING → VALIDATED → FETCHING → FETCHED 
→ BUILDING_PROMPT → CALLING_API → PARSING → VALIDATING_QUESTIONS 
→ STORING_TEMP → PREVIEW_READY
         ↓                    ↓              ↓
       INVALID          API_FAILED    QUESTIONS_VALID
```

**Question Lifecycle:**
```
TEMP → USER_REVIEW → ACCEPTED → SAVING → PERSISTED 
→ PUBLISHED → SHAREABLE
     ↓              ↓
  EDITED       REJECTED
```

**Exercise Lifecycle:**
```
CREATED → ITEMS_ADDING → ITEMS_COMPLETE → DRAFT 
→ PUBLISHED → SHAREABLE_EXERCISE → ASSIGNED → COMPLETED 
→ ARCHIVED
                    ↓
                  DELETED
```

---

## 🎯 Diagram Usage Matrix

| Situasi | Diagram Tipe | File |
|---------|-------------|------|
| Demo ke boss/client | Component | 10 |
| Train user baru | Activity | 10b |
| Rancang implementation | Architecture | 10c |
| Debug error/issue | Sequence | 10d |
| Gather requirements | Use Case | 10e |
| Design database | ERD | 10f |
| Implement logic | Flowchart | 10g |
| Test state transitions | State | 10h |
| Dokumentasi lengkap | All (1-8) | - |
| Presentasi teknis | 10, 10c, 10d | - |
| QA planning | 10b, 10e, 10g, 10h | - |

---

## 🔄 Diagram Dependencies & Flow

```
Use Case (10e)
    ↓
    ├→ Activity flow (10b)
    │   ├→ Flowchart logic (10g)
    │   └→ Error scenarios
    │
    ├→ Component overview (10)
    │   └→ Architecture detail (10c)
    │       └→ Sequence interaction (10d)
    │
    └→ Database design (10f)
        └→ State management (10h)
```

**Rekomendasi Pembacaan:**
1. **Baru di project:** 10e (use case) → 10b (flow) → 10 (overview)
2. **Development:** 10g (logic) → 10h (states) → 10d (sequence)
3. **Database:** 10f (ERD) → 10c (architecture)
4. **Review lengkap:** 10 → 10e → 10b → 10c → 10d → 10f → 10g → 10h

---

## 🚀 Fitur Unggulan: Dual-Source Material

### Ditampilkan dalam Diagram:

**Use Case (10e):**
- Pilih sumber materi: Post vs Lesson
- Token format: post:ID vs lesson:ID

**Activity (10b):**
- Material selection logic
- Dual-source database query

**Architecture (10c):**
- Token parser untuk routing sumber
- Separate fetchers untuk Post & Lesson
- Query filtering berbeda per source

**Sequence (10d):**
- Decision branch untuk material source
- Database queries method invocation

**ERD (10f):**
- Post table dengan serial_id foreign key
- Lesson table dengan category filter
- Exercise menerima dari kedua sumber

**Flowchart (10g):**
- Material validation decision point
- Source-specific database queries

**State (10h):**
- Validation state untuk material token

---

## 📈 Metrics & Monitoring

Diagram juga menunjukkan:
- **Cache strategy:** TTL 1 jam, reduce queries
- **API calls:** Rate limiting, cost tracking
- **Error rates:** Failed requests, validation failures
- **Performance:** Processing time (ms), token usage
- **Audit trail:** AIGenerationLog table untuk semua actions

---

## 🔐 Security Considerations

Ditampilkan di semua diagram:
- ✅ Token validation (post: vs lesson:)
- ✅ Serial ID ownership check (Post queries)
- ✅ Category validation (Lesson queries)
- ✅ Content sanitization sebelum AI
- ✅ Response validation dari AI
- ✅ Access control per user

---

## 📝 Related Documentation

- [AI_QUESTION_GENERATOR.md](../AI_QUESTION_GENERATOR.md) - User guide
- [docs/SoalController implementation coverage in diagrams]
- [Database migrations untuk Exercise & ExerciseItem]
- [OpenAI API configuration (.env)]

---

## 🎓 Learning Path

**Untuk Business:**
1. Read Use Case (10e)
2. Read Activity Flow (10b)
3. Lihat Component (10)

**Untuk Developer:**
1. Read Architecture (10c)
2. Read Sequence (10d)
3. Read Flowchart (10g)
4. Read ERD (10f)
5. Read State (10h)

**Untuk QA:**
1. Read Use Case (10e)
2. Read Activity Flow (10b)
3. Read Flowchart (10g) - untuk test cases
4. Read Sequence (10d) - untuk edge cases
5. Read State (10h) - untuk state transitions

---

**Generated:** May 2026  
**Status:** ✅ Complete Suite  
**Version:** 1.0  
**Audience:** All Stakeholders
