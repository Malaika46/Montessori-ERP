<div align="center">

# 🌿 Montessori ERP & Gamified Learning Management System

[![Laravel](https://img.shields.io/badge/Laravel-9.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

**A comprehensive, multi-campus SaaS enterprise solution tailored specifically for authentic Montessori Schools & Academies.**  
Features 6-level Role-Based Access Control (RBAC), Montessori rubric assessments, narrative report cards, real-time parent tracking, and an interactive **Gamified LMS**.

</div>

---

## 🚀 Key System Features

### 👑 1. Role-Based Access Control (RBAC)
Strict multi-tenant & role authorization backed by real database relationships:
* **Superadmin:** Global system settings, user CRUD, audit logs, multi-campus governance.
* **Principal:** Academic quality control, narrative report approval & release workflow.
* **Admin:** Student/Parent onboarding, environment allocations, PKR fee management.
* **Teacher / Guide:** Classroom rubric scoring (*Introduced*, *Working*, *Mastered*), daily observations, quest creator.
* **Student:** Learning journey, peer list, attendance calendar, PDF report card download, and Gamified LMS.
* **Parent:** Real-time child attendance, directress narrative notes, PKR fee vouchers, verified PDF report downloads.

---

### 🌿 2. Core Montessori Academic Engine
Built according to traditional Montessori principles across 4 foundational domains:
1. **Practical Life** (Pouring, Dressing Frames, Care of Self & Environment)
2. **Sensorial** (Pink Tower, Broad Stair, Knobbed Cylinders, Colour Tablets)
3. **Mathematics** (Spindle Box, Golden Beads, Stamp Game, Bead Chains)
4. **Language & Phonics** (Sandpaper Letters, Moveable Alphabet, Pink/Blue/Green Series)

---

### 🎮 3. Interactive Gamified LMS
* **🎯 MCQ Quiz Quest:** 10 curriculum-specific questions per topic (Math, Phonics, Sensorial, Practical Life, Cultural, Science).
* **🧩 3D Memory Match Game:** Flip cards to match identical Montessori terms & materials.
* **🔀 Word Scramble:** Unscramble Montessori terminology with instant hint helpers.
* **⭐ XP & Leveling System:** Live XP counter, level progress bar (Level 1, Level 2...), and achievements.

---

### 📄 4. Verified Narrative PDF Report Cards
Formal academic workflow:
`Draft (Teacher)` ➔ `Review (Principal)` ➔ `Released (Parent/Student)` ➔ **One-Click PDF Download**

---

## 🔑 Demo Login Credentials

> **Default Password for All Accounts:** `12341234`

| Role | User Name | Email Address | Roll / Employee ID | Password |
| :--- | :--- | :--- | :--- | :--- |
| **Superadmin** | Maham Mir | `mirm09845@gmail.com` | `SUP-001` | `12341234` |
| **Principal** | Executive Principal | `principal@montessori.edu.pk` | `PRN-001` | `12341234` |
| **Admin** | Campus Admin | `admin@montessori.edu.pk` | `ADM-001` | `12341234` |
| **Teacher / Guide** | maha yes | `aimanhm302@gmail.com` | `TCH-002` | `12341234` |
| **Student** | eshar dfgh | `zunairamunir39@gmail.com` | `STU-00008` | `12341234` |
| **Parent 1** | Sehar No | `malaika6603@gmail.com` | `PAR-001` | `12341234` |
| **Parent 2** | mir n no | `malaikaminer46@gmail.com` | `PAR-003` | `12341234` |

---

## 🛠️ Technology Stack

* **Backend Framework:** Laravel 9.x (PHP 8.1+)
* **Database:** MySQL 8.0 / MariaDB
* **Frontend:** Blade Templates, Bootstrap 5.3, Vanilla CSS Design System (`montessori-theme.css`)
* **Icons & Fonts:** Bootstrap Icons, Inter & Outfit Google Fonts
* **PDF Generation:** DomPDF / Laravel PDF Engine

---

## 💻 Installation & Setup Guide

### 1. Clone the Repository
```bash
git clone https://github.com/Malaika46/Montessori-ERP.git
cd Montessori-ERP
```

### 2. Install Composer Dependencies
```bash
composer install
```

### 3. Environment Configuration
Create a `.env` file from `.env.example`:
```bash
cp .env.example .env
```

Configure your MySQL database settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=montessori_erp
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations & Database Seeders
```bash
php artisan migrate --seed
```

### 6. Start Development Server
```bash
php artisan serve --port=8080
```
Visit **`http://127.0.0.1:8080`** in your browser.

---

## 📄 License
This project is open-source and available under the [MIT License](LICENSE).

---

<div align="center">
  <sub>Built with ❤️ for Montessori Educators & Students</sub>
</div>
