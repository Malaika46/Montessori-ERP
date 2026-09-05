# 🏫 Montessori ERP - System Roles, User Credentials & Feature Guide

Every user role in the Montessori ERP is backed by real database relationships, Role-Based Access Control (RBAC), and specific UI/UX themes.

---

## 🔑 All Registered Users & Credentials

All account passwords have been standardized to: **`12341234`**

| Role | User Name | Email Address | Roll / Employee ID | Password | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Superadmin** | Maham Mir | `mirm09845@gmail.com` | `SUP-001` | `12341234` | Active |
| **Principal** | Executive Principal | `principal@montessori.edu.pk` | `PRN-001` | `12341234` | Active |
| **Admin** | Campus Admin | `admin@montessori.edu.pk` | `ADM-001` | `12341234` | Active |
| **Teacher / Guide** | maha yes | `aimanhm302@gmail.com` | `TCH-002` | `12341234` | Active |
| **Student** | eshar dfgh | `zunairamunir39@gmail.com` | `STU-00008` | `12341234` | Active |
| **Parent 1** | Sehar No | `malaika6603@gmail.com` | `PAR-001` | `12341234` | Active |
| **Parent 2** | mir n no | `malaikaminer46@gmail.com` | `PAR-003` | `12341234` | Active |

---

## 🎭 Role-Specific Features, Functions & Styling

### 👑 1. Superadmin Role
* **Role ID:** `1` (`superadmin`)
* **Primary Objective:** Highest-level system governance, complete multi-campus administration, and security.
* **Key Features & Functions:**
  * **System Control Center:** Full CRUD authority over all users, roles, and permissions.
  * **Campus Architecture:** Create, edit, and isolate data across multiple campuses.
  * **Audit Trail & Security Logs:** View system-wide user actions, login activity, and audit logs.
  * **System Configuration:** Manage database seeds, global defaults, and email notification settings.
* **Design & Styling:** Deep Forest Green header (`#1b4332`), rich gold badges (`#d4af37`), high-contrast metric cards with smooth hover lift animations.

---

### 🏛️ 2. Principal Role
* **Role ID:** `2` (`principal`)
* **Primary Objective:** Executive academic leadership, quality assurance, and report card verification.
* **Key Features & Functions:**
  * **Academic Performance Analytics:** View overall progress charts across Practical Life, Sensorial, Math, and Language.
  * **Report Card Approval Workflow:** Review directress narrative assessments before official release to parents.
  * **Staff Management:** Oversee Lead Directresses and Guides across assigned environments.
  * **Broadcast Communications:** Authorize official school circulars and announcements.
* **Design & Styling:** Executive Deep Emerald aesthetic with clean summary metrics, status progress bars, and quick approval modal actions.

---

### 🏢 3. Admin Role
* **Role ID:** `3` (`admin`)
* **Primary Objective:** Day-to-day campus management, student admissions, attendance, and financial tracking.
* **Key Features & Functions:**
  * **Student & Parent Onboarding:** Register new students, auto-generate Roll Numbers (`STU-XXXX`), and link Parent profiles.
  * **Classroom & Environment Allocation:** Assign students to specific Montessori Environments (e.g., Environment 2).
  * **Fee Management (PKR):** Generate and manage fee vouchers in Pakistani Rupees (PKR) with payment status tracking.
  * **Daily Attendance Log:** Monitor daily student and teacher check-ins, lateness, and absences.
* **Design & Styling:** Clean SaaS administrative layout, data tables with pagination, filterable search bars, and action badges.

---

### 👩‍🏫 4. Teacher / Guide Role
* **Role ID:** `4` (`teacher`)
* **Primary Objective:** Classroom instruction, child observation, progress tracking, and narrative assessment authoring.
* **Key Features & Functions:**
  * **Assigned Classroom View:** Filter student rosters strictly to assigned environments.
  * **Montessori Rubric Scoring:** Record mastery levels (*Introduced*, *Working*, *Mastered*) across 4 core domains:
    1. 🌿 *Practical Life* (Pouring, Dressing Frames, Care of Self)
    2. 👁 *Sensorial* (Pink Tower, Brown Stair, Knobbed Cylinders)
    3. 📐 *Mathematics* (Spindle Box, Golden Beads, Stamp Game)
    4. 🔤 *Language / Phonics* (Sandpaper Letters, Moveable Alphabet, Pink Series)
  * **Narrative Observations:** Write and log timestamped observations for individual children.
  * **Teacher-Created Quests:** Publish custom Gamified LMS learning paths for students.
* **Design & Styling:** Interactive rubric radio cards, rich text observation logs, student photo avatars, and soft green/cream background highlights.

---

### 🎓 5. Student Role
* **Role ID:** `5` (`student`)
* **Primary Objective:** Personalized interactive learning dashboard, attendance awareness, and Gamified LMS.
* **Key Features & Functions:**
  * **Student Profile Header:** Accurate full name rendering (`eshar dfgh`), student ID (`STU-00008`), and assigned environment.
  * **Assigned Lessons & Peers:** View upcoming scheduled lessons and active classmates.
  * **Attendance Progress & Calendar:** Visual attendance summary (Present, Late, Absent) with a highlighted monthly calendar view.
  * **Official Released Reports:** Download PDF Assessment Report Cards signed by the Lead Directress & Principal.
  * **🎮 Gamified LMS (Interactive Learning Center):**
    * 🎯 **MCQ Quiz Quest:** 10 curriculum-specific questions per topic (Mathematics, Phonics, Sensorial, Practical Life, Cultural, Science).
    * 🧩 **Memory Match Card Game:** 3D flip card game matching Montessori terms and symbols.
    * 🔀 **Word Scramble:** Rearrange letters to spell key Montessori vocabulary with hint helpers.
    * ⭐ **XP Points & Level Up System:** Live XP counter, level progress bar (Level 1, Level 2...), and gamified badges.
* **Design & Styling:** Playful yet polished Montessori SaaS UI, glowing XP counters, smooth 3D card flip CSS animations, progress bars, and vibrant color-coded domain pills.

---

### 👨‍👩‍👧 6. Parent Role
* **Role ID:** `6` (`parent`)
* **Primary Objective:** Transparent window into the child's daily school life, progress, and financial obligations.
* **Key Features & Functions:**
  * **Multi-Child Visibility:** Automatically linked to child records via database foreign keys (`parent_student`).
  * **Real-time Attendance Tracking:** Monitor child's daily arrival, presence, and absence logs.
  * **Directress Observations:** Read daily narrative notes and development feedback written by the child's guide.
  * **Released Report Cards:** Download official PDF progress cards once authorized by the Principal.
  * **Fee Challan & PKR Status:** View outstanding/paid tuition fees clearly formatted in PKR currency.
* **Design & Styling:** Warm, reassuring family-centric aesthetic, high-contrast status cards, readable narrative blockquotes, and one-click PDF download buttons.

---

## 🔒 Security & Data Relationship Rules
1. **Strict RBAC Enforcement:** Middleware verifies role authorization before granting route access.
2. **Database Integrity:** Foreign key constraints (`user_id`, `classroom_id`, `campus_id`) ensure users can only view authorized records.
3. **Data Isolation:** Teachers only see their assigned classrooms; Parents only see their linked children.
