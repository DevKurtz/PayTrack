# 🎓 PayTrack — Student Tuition & Assessment Management System
**Institutional Platform for National College of Science and Technology (NCST)**

---

## 📖 Talaan ng Nilalaman (Table of Contents)
1. [Tungkol sa Sistema (About PayTrack)](#-tungkol-sa-sistema-about-paytrack)
2. [Tech Stack & Mga Tool na Ginamit (With Meaning & Application)](#-tech-stack--mga-tool-na-ginamit)
3. [Arkitektura ng Seguridad (Security Tools & Practices)](#-arkitektura-ng-seguridad-security-tools--practices)
4. [Mga Pangunahing Tampok (Key Features)](#-mga-pangunahing-tampok-key-features)
5. [Gabay sa Pag-install at Setup (Installation & Setup Guide)](#-gabay-sa-pag-install-at-setup)
6. [Mga Default na Akawnt at Kredensyal (Default Credentials)](#-mga-default-na-akawnt-at-kredensyal)
7. [Gabay sa Paggamit ng Sistema (How to Use PayTrack)](#-gabay-sa-paggamit-ng-sistema-how-to-use-paytrack)
   - [A. Para sa School Administrator](#a-para-sa-school-administrator)
   - [B. Para sa Estudyante at Magulang](#b-para-sa-estudyante-at-magulang)
8. [Folder at File Structure](#-folder-at-file-structure)

---

## 🏛️ Tungkol sa Sistema (About PayTrack)

Ang **PayTrack** ay isang modernong web-based application na binuo para sa pamamahala ng assessment ng matrikula (tuition fee assessment), automated fee aspect breakdown (LMS, Laboratory, Library, Medical, Units), pagsubaybay sa bayarin, pag-isyu ng opisyal na electronic receipts (OR), at awtomatikong email notification para sa mga mag-aaral at magulang ng **National College of Science and Technology (NCST)**.

---

## 🛠️ Tech Stack & Mga Tool na Ginamit

Narito ang lahat ng teknolohiya, programming language, external libraries, at APIs na ginamit sa proyekto, kasama ang kanilang kahulugan at kung paano ito nag-aapply sa PayTrack:

### 1. Backend & Server Environment
* **PHP 8.x (Hypertext Preprocessor):**
  - **Kahulugan:** Isang open-source server-side scripting language para sa dynamic web applications.
  - **Paano nag-aapply sa PayTrack:** Ito ang core engine ng sistema. Pinapagana nito ang business logic, session handling, database query controller, role-based authentication (`admin` at `student`), dynamic routing (`?view=...`), at pag-generate ng mga dynamic views.
* **Apache HTTP Server (via XAMPP):**
  - **Kahulugan:** Ang pinakapopular na web server software na nagho-host ng mga website at nagpoproseso ng HTTP requests.
  - **Paano nag-aapply sa PayTrack:** Nagsisilbing local server gateway sa pamamagitan ng XAMPP upang mapatakbo ang PHP at maihatid ang mga web pages sa browser (`localhost`).
* **Composer (PHP Dependency Manager):**
  - **Kahulugan:** Tool para sa pag-manage at pag-install ng mga external PHP libraries at dependencies.
  - **Paano nag-aapply sa PayTrack:** Ginamit upang i-install at i-autoload ang PHPMailer package nang maayos at may PSR-4 standard.

### 2. Database & Data Storage
* **MySQL / MariaDB:**
  - **Kahulugan:** Isang Relational Database Management System (RDBMS) na nag-iimbak ng data sa mga structured tables.
  - **Paano nag-aapply sa PayTrack:** Naglalaman ng lahat ng data ng paaralan:
    - `users` — login credentials, password hashes, at user roles.
    - `students` — profile ng estudyante, Student ID, email, kurso, at kontak ng magulang.
    - `tuition_fees` — talaan ng assessments, kabuuang matrikula, at natitirang balanse.
    - `tuition_fee_items` — itemized breakdown (Subject Fee, LMS, Tech Facilities, Medical, Support).
    - `fee_categories` — configurable standard fees ng paaralan.
    - `payments` — opisyal na transaksyon ng pagbabayad at generated OR numbers.
    - `email_logs` — audit trail ng lahat ng naipadalang email notification.
* **PDO (PHP Data Objects):**
  - **Kahulugan:** Isang standardized at secure na database abstraction layer sa PHP.
  - **Paano nag-aapply sa PayTrack:** Lahat ng pakikipag-ugnayan sa MySQL database ay dumadaan sa PDO gamit ang native prepared statements.

### 3. Frontend & User Interface
* **HTML5 & Modern Semantic Web Elements:**
  - **Kahulugan:** Ang standard markup language para sa pagbuo ng web structure.
  - **Paano nag-aapply sa PayTrack:** Nagbibigay ng semantic structure para sa mga dashboard, navigation drawers, modal windows, at data forms.
* **Modern CSS3 (Custom Fintech UI Architecture):**
  - **Kahulugan:** Stylesheet language para sa disenyo, layout, at visuals.
  - **Paano nag-aapply sa PayTrack:** Gumamit ng custom fintech design system na walang mabibigat na CSS framework para sa mataas na performance:
    - Grid & Flexbox layouts para sa KPI cards at responsive tables.
    - Interactive hover states at prominent visual hierarchy.
    - **Print CSS (`@media print`)**: Espesyal na CSS rules na nag-aalis ng sidebar, buttons, at topbar tuwing magpi-print upang ang malinis na official receipt voucher lamang ang lilitaw sa portrait paper.
* **Vanilla JavaScript (ES6+):**
  - **Kahulugan:** Client-side scripting language para sa interactivity.
  - **Paano nag-aapply sa PayTrack:**
    - Live search filter sa mga table nang hindi nagre-reload ang page.
    - Dynamic section generator (`11A1`, `12A2`) na awtomatikong nagko-compute batay sa Year, Semester, Shift, at Section Number.
    - Safe DOM rendering gamit ang `.textContent` para sa official receipt modal.
    - One-click copy-to-clipboard para sa student credentials.
    - Modal backdrop controllers at drawer sidebar toggles para sa mobile.

### 4. External Libraries & Third-Party APIs
* **PHPMailer (v6.9.1):**
  - **Kahulugan:** Isang kilalang library para sa pagpapadala ng email gamit ang PHP.
  - **Paano nag-aapply sa PayTrack:** Pinapagana ang automated transactional email notifications. Nagpapadala ng credentials kapag may bagong gawang estudyante, confirmation kapag nagbayad, at payment reminders na may magagandang responsive HTML templates.
* **Google Gmail SMTP API (App Passwords Gateway):**
  - **Kahulugan:** Simple Mail Transfer Protocol (SMTP) server ng Google para sa cloud email delivery.
  - **Paano nag-aapply sa PayTrack:** Nagsisilbing relay ng system upang makapagpadala ng tunay na email galing sa official NCST system mailbox patungo sa aktwal na Gmail/inbox ng mga estudyante at magulang.
* **SweetAlert2 (CDN):**
  - **Kahulugan:** Isang magandang JavaScript modal and alert library na pamalit sa default browser popups.
  - **Paano nag-aapply sa PayTrack:** Ginagamit sa mga sumusunod:
    - Kumpirmasyon bago mag-delete ng student, fee, o category.
    - Kumpirmasyon bago mag-sign out.
    - Magarang credential card display pagkatapos gumawa ng bagong estudyante na may instant copy buttons.
    - Toast notifications para sa tagumpay o error messages.

---

## 🔒 Arkitektura ng Seguridad (Security Tools & Practices)

Ang PayTrack ay dumaan sa masusing security audit at naglalaman ng mga sumusunod na panangga sa cyber threats:

| Security Feature | Tool / Mekanismo | Paano Nag-aapply sa PayTrack |
| :--- | :--- | :--- |
| **SQL Injection Defense** | `PDO` + Native Prepared Statements (`PDO::ATTR_EMULATE_PREPARES => false`) | 100% ng dynamic queries ay gumagamit ng parameter placeholders (`?`). Hindi kailanman dinidugtong ang user input sa query string, kaya hindi mapapatakbo ang `' OR '1'='1` o anumang SQL injection script. |
| **Password Security** | `password_hash()` + `PASSWORD_BCRYPT` | Lahat ng passwords sa database ay naka-hash gamit ang one-way cryptographically salted Bcrypt algorithm. Hindi naka-store bilang plain text. |
| **CSRF Protection** | Cryptographic Token (`bin2hex(random_bytes(32))`) + `hash_equals()` | Lahat ng forms (Login, Create Student, Assign Fee, Delete, Pay Tuition, Change Password) ay may `<?= csrf_field() ?>` at `verify_csrf()`. Pinipigilan nito ang unauthorized requests mula sa ibang websites. |
| **Session Fixation Defense** | `session_regenerate_id(true)` | Tuwing matagumpay na mag-login ang user, binubura ang lumang session ID at nagbibigay ng panibagong secure ID. |
| **Session Hijacking Defense** | Soft `HTTP_USER_AGENT` Fingerprint Binding | Itinatali ang aktibong session sa browser fingerprint ng nag-login. Kung may sumubok magnakaw ng session cookie at gamitin ito sa ibang browser, awtomatikong puputulin ang access. |
| **Inactivity Session Timeout** | `$_SESSION['last_activity']` Tracker (3,600s / 1 Hour) | Kapag naiwang nakabukas ang computer lab o kiosk nang lampas 1 oras na walang aktibidad, kusa itong magla-logout para iwas access ng ibang tao. |
| **Secure Cookie Flags** | `httponly=true`, `samesite=Lax`, `use_strict_mode=1` | Hindi maaaring manakaw ang session cookies sa pamamagitan ng client-side JavaScript (anti-XSS session theft). |
| **XSS Defense (Cross-Site Scripting)** | `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` via `e()` helper | Lahat ng datos mula sa database na ipinapakita sa screen ay naka-encode upang hindi ito mag-execute bilang malisyosong script. |
| **IDOR Protection** | Server-side ownership verification | Sa pagbabayad ng matrikula, sinusuri sa database kung ang `fee_id` ay lehitimong pag-aari ng naka-log in na `student_id`. Hindi maaaring bayaran o tingnan ng isang estudyante ang account ng iba. |
| **Input Whitelisting & Regex** | Strict Validation Rules | Ang mga kurso ay limitado sa 6 na pinapayagan; ang Section Code ay sumusunod sa format na `/^[1-4][1-2][MAE][1-3]$/`; ang Student ID ay `/^\d{4}-\d{5}$/`. |

---

## ✨ Mga Pangunahing Tampok (Key Features)

1. **Dalawang Dedicated Portals:**
   - **Administrator Portal:** Buong kontrol sa paggawa ng account, fee assessment, fee breakdown, transaksyon, at email logs.
   - **Student / Parent Portal:** Personalized na pagtingin sa balanse, breakdown ng bayarin, online payment simulation, at printable official receipts.
2. **Dedicated Home Overview Page (Makapal na Sidebar):**
   - Admin: 4 KPI metric cards (Total Collections, Receivables, Students, ORs) at recent payment feed.
   - Student: Welcome banner, 4 quick balance summary cards, recent payments ledger, at quick action links.
3. **Automated Tuition Assessment Breakdown:**
   - Kapag naglagay ng matrikula ang Admin, kusa itong hinahati sa:
     - *Subject Fee (Units / Academic)*
     - *LMS & E-Learning Fee*
     - *Technology & Laboratory Fee*
     - *Library & Academic Support*
     - *Medical & Clinic Facilities*
4. **NCST Printable Official Electronic Receipts (OR):**
   - Kumpleto sa header ng **Republic of the Philippines** at **National College of Science and Technology (NCST)**.
   - May OR Number, Date, Student Details, Course/Section, Payment Channel, Breakdowns, at Authorized Signatures.
   - 100% printer-friendly (Modal print preview o standalone page via `?view=receipt&or=...`).
5. **Section Code Generator Pattern:**
   - Awtomatikong bumubuo ng section code batay sa pattern:
     - `1st Digit`: Year Level (1, 2, 3, 4)
     - `2nd Digit`: Semester (1, 2) — *naka-sync sa assessment semester*
     - `3rd Digit`: Shift / Timeline (M = Morning, A = Afternoon, E = Evening)
     - `4th Digit`: Section Number (1, 2, 3)
     - *Halimbawa:* `BSCS 11A1`, `BSIT 12A2`, `BSEE 21A3`.
6. **Mabilisang Pagkopya ng Kredensyal:**
   - One-click copy para sa Student ID, default password, o kumpletong portal details.

---

## 🚀 Gabay sa Pag-install at Setup

Sundin ang mga hakbang na ito upang mapatakbo ang sistema sa inyong computer o sa computer ng inyong ka-grupo:

### Hakbang 1: Ihanda ang XAMPP
1. Siguraduhing may naka-install na **XAMPP** na may **PHP 8.0 o mas mataas**.
2. Buksan ang **XAMPP Control Panel** at i-click ang **Start** sa **Apache** at **MySQL**.

### Hakbang 2: Ilagay ang Project sa `htdocs`
1. Ilagay ang folder ng proyekto sa loob ng:
   ```
   C:\xampp\htdocs\paytrack
   ```
   *(Tandaan: Kung ang pangalan ng folder ay `SystemProposal` o `paytrack`, awtomatikong idi-detect ng system ang tamang URL path dahil sa dynamic auto-detection sa `config/config.php`)*.

### Hakbang 3: I-import ang Database
1. Buksan ang inyong browser at pumunta sa: `http://localhost/phpmyadmin/`
2. Gumawa ng bagong database na may pangalang:
   ```
   paytrack
   ```
   *(Collation: `utf8mb4_unicode_ci`)*
3. I-click ang database na **paytrack**, pumunta sa tab na **Import**.
4. Piliin ang file na:
   ```
   database/paytrack.sql
   ```
5. I-click ang button na **Import** o **Go** sa ibaba.

### Hakbang 4: I-configure ang Email (Opsyonal kung gagamit ng live SMTP)
Kung nais makatanggap ng aktwal na Gmail notifications:
1. Buksan ang `config/mailer.php`.
2. I-update ang:
   ```php
   define('SMTP_USER', 'inyong_email@gmail.com');
   define('SMTP_PASS', 'inyong_16_digit_google_app_password');
   ```

### Hakbang 5: Buksan ang PayTrack sa Browser
Pumunta sa alinman sa mga sumusunod na URL:
```
http://localhost/paytrack/public/
```
o
```
http://localhost/SystemProposal/public/
```

---

## 🔑 Mga Default na Akawnt at Kredensyal

| Role | Username / ID | Password | Access Portal |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | Admin Sign In |
| **Sample Student 1** | `2023-53512` | `DELACRUZ` | Student Sign In |
| **Sample Student 2** | `2024-00001` | `SANTOS` | Student Sign In |

> 💡 **Tandaan sa Bagong Gawang Estudyante:**
> Kapag gumawa ang Admin ng bagong student account, ang default password ay ang mismong **Apelyido (Last Name)** ng mag-aaral (case-insensitive, halimbawa: `MENDOZA` o `mendoza`). Maaari itong palitan ng mag-aaral sa loob ng kanilang portal sa tab na **Change Password**.

---

## 🖥️ Gabay sa Paggamit ng Sistema (How to Use PayTrack)

### A. Para sa School Administrator

1. **Pag-sign In bilang Admin:**
   - Sa landing page, i-click ang **Sign In**.
   - I-click ang link na **"Sign in as Administrator →"** sa ilalim ng form.
   - Ilagay ang Username: `admin` at Password: `admin123`.
2. **Home Overview (`?view=home`):**
   - Tingnan ang real-time dashboard: Total Collections, Outstanding Receivables, Bilang ng Estudyante, at Naipadalang Resibo.
   - Makikita rin ang pinakahuling 5 transaksyon ng pagbabayad.
3. **Paggawa ng Bagong Estudyante (`?view=students`):**
   - Pumunta sa **Students & Balances** sa sidebar.
   - I-click ang berdeng buton na **"+ Create Student"** sa itaas.
   - Punan ang impormasyon ng estudyante:
     - *Student ID*: Awtomatikong may mask na 4 na numero, gitling, 5 numero (hal. `2024-12345`).
     - *Pangalan, Email, Kontak ng Magulang*.
     - *Course*: Piliin sa dropdown (`BSCS`, `BSIT`, `BSEE`, `BSHM`, `BSIE`, `BSCrim`).
     - *Section Generator*: Piliin ang Year (1-4), Timeline (M/A/E), at Section Number (1-3).
     - *Tuition Assessment*: Ilagay ang kabuuang matrikula at Semester. Awtomatikong magko-compute ang section code para tumugma sa semester!
   - I-click ang **Create Student Account**.
   - May lalabas na SweetAlert credential card kung saan maaari mong kopyahin ang ID at default password para ibigay sa estudyante.
4. **Pag-check sa mga Transaksyon (`?view=transactions`):**
   - Dito nakatala ang lahat ng opisyal na pagbabayad ng mga estudyante kasama ang kanilang OR number, petsa, halaga, at paraan ng pagbabayad.
5. **Pamamahala ng Fee Categories (`?view=categories`):**
   - Maaaring magdagdag o mag-edit ng fixed fees tulad ng LMS, Laboratory Fee, atbp.

---

### B. Para sa Estudyante at Magulang

1. **Pag-sign In bilang Estudyante:**
   - Sa landing page, i-click ang **Sign In**.
   - Piliin ang **Student / Parent Sign In**.
   - Ilagay ang inyong **Student ID** (hal. `2023-53512`) at ang inyong default password (**Apelyido**).
2. **Student Home Overview (`?view=home`):**
   - Makikita ang inyong Remaining Balance, Total Paid, Enrollment Status, at mga pinakahuling binayaran.
3. **Tuition Breakdown & Balance (`?view=fees`):**
   - Makikita ang visual progress bar ng inyong bayarin at ang itemized fee breakdown (mga unit ng kurso, lab, medical, at e-learning fee).
4. **Pagbabayad ng Matrikula (Online Payment Simulation):**
   - I-click ang buton na **"Pay Tuition Now →"**.
   - Maaaring gamitin ang quick select pills: `Full Balance`, `50% Balance`, `₱1,000`, o mag-type ng sariling halaga.
   - Piliin ang channel (GCash, Maya, Bank Transfer, o Online Portal).
   - I-click ang **Confirm & Submit Payment**.
   - Agad na mababawasan ang inyong balanse at maglalabas ng confirmation pop-up.
5. **Official Electronic Receipts (OR) Directory (`?view=receipts`):**
   - Pumunta sa **Official Receipts** sa sidebar.
   - Makikita ang bawat resibo na may verified badge.
   - I-click ang **"View Details"** upang buksan ang electronic receipt modal.
   - I-click ang **"Print Voucher"** o **"Print / Save PDF"** upang i-print ito gamit ang opisyal na format ng **National College of Science and Technology**.
6. **Pagpapalit ng Password (`?view=password`):**
   - Pumunta sa **Change Password** upang palitan ang default password at mapanatiling ligtas ang inyong account.

---

## 📁 Folder at File Structure

```
c:/xampp/htdocs/paytrack/
├── assets/                  # CSS stylesheets, JavaScript files, at imagery
│   ├── css/                 # Custom modular styles (admin, student, auth, main)
│   └── js/                  # Client-side scripts (modals, masks, prints)
├── config/                  # Configuration layer
│   ├── config.php           # App path auto-detection, constants, session name
│   ├── database.php         # PDO database singleton connection (emulate prepares disabled)
│   └── mailer.php           # PHPMailer SMTP email configuration
├── controllers/             # Application Controllers
│   ├── AdminController.php  # Pangangasiwa sa students, fees, categories, at CSRF
│   ├── AuthController.php   # Login, session fixation defense, at logout logic
│   └── StudentController.php# Tuition payments, IDOR checks, at password update
├── core/                    # Core Infrastructure
│   ├── Auth.php             # Session hardening, timeout, user-agent check, RBAC
│   └── helpers.php          # CSRF token generator, output escaping e(), pesong format
├── database/                # Database Assets
│   ├── paytrack.sql         # Kumpletong MySQL schema at initial seed data
│   ├── schema.sql           # Table structure definitions
│   └── seed.sql             # Demo accounts at sample fees
├── mail/                    # Responsive HTML Email Templates
│   ├── account_created.php  # Email template para sa bagong gawang account
│   └── payment_confirmation.php # Resibo template para sa kumpirmadong bayad
├── models/                  # Database Models (Data Layer)
│   ├── FeeCategory.php      # Fee aspects at fixed categories
│   ├── Payment.php          # Transactions log at OR number tracking
│   ├── Student.php          # Student records at academic data
│   ├── TuitionFee.php       # Assessments at itemized fee calculation
│   └── User.php             # Credentials at Bcrypt password hashing
├── public/                  # Public Web Root & Entry Points
│   ├── index.php            # Landing page at Login route
│   ├── admin/index.php      # Admin route controller (?view=home, students, etc.)
│   └── student/index.php    # Student route controller (?view=home, fees, receipts)
├── views/                   # Presentation Layer (HTML Views)
│   ├── admin/dashboard.php  # Kumpletong Admin management dashboard
│   ├── auth/login.php       # Landing page, hero showcase, at login modal
│   ├── student/dashboard.php# Student portal, tuition breakdown, at OR modal
│   └── student/receipt.php  # Standalone 100% printable NCST official receipt page
├── composer.json            # Composer dependencies configuration
└── README.md                # Kumpletong gabay at dokumentasyon ng system
```

---

## ⚖️ Lisensya at Pagmamay-ari (Credits)
Binuo para sa **National College of Science and Technology (NCST)**  
*Student Tuition & Assessment Management System (PayTrack)*  
Lahat ng karapatan ay nakalaan &copy; <?= date('Y') ?>.
