# HotelHub – Proiect DAW (Tema 1–4)

HotelHub este o aplicație web pentru managementul unui hotel, dezvoltată progresiv în cadrul disciplinei **DAW**, pe parcursul temelor 1–4.

---

## Conținut pe teme

### 🟦 Tema 1 – Site public
Partea publică prezintă hotelul și informații generale (pagini de prezentare, layout, imagini, structură HTML/CSS).

---

### 🟩 Tema 2 – PHP + MySQL + CRUD
Aplicația devine dinamică și folosește o bază de date MySQL. Sunt implementate operații CRUD și pagini interconectate:
- autentificare (login/logout) și sesiuni
- CRUD utilizatori (admin)
- CRUD camere (admin/angajat)
- CRUD rezervări (client + admin/angajat)
- facturi (admin/angajat emit, client vede)
- servicii (admin)

---

### 🟨 Tema 3 – Roluri + securitate + înregistrare + protecție formulare
Se adaugă:
- înregistrare utilizatori
- separarea strictă a rolurilor, verificată server-side
- protecții împotriva atacurilor comune (SQL Injection, XSS, CSRF etc.)
- Google reCAPTCHA pe formulare publice (ex: înregistrare și contact)

---

### 🟧 Tema 4 – Funcționalități avansate

#### 🌍 Integrare externă (parsare conținut)
- import **live** din Wikipedia prin API și parsare conținut
- conținutul este modelat și salvat în MySQL
- caching în DB pentru a evita request-uri repetate
- afișare într-o pagină publică (ex: listă parcuri/atracții)

#### 📊 Website analytics + element multimedia
- tracking vizite/pagini accesate în tabela `page_views`
- pagină de analytics cu statistici și grafice (Chart.js)

#### 📧 Email
- formular de contact (public)
- trimitere email către recepție prin SMTP (PHPMailer)
- validări server-side + protecție CSRF + reCAPTCHA

#### 📤 Import / Export
- export rezervări în format Excel (HTML → `.xls`, download)
- export factură în PDF (Dompdf)
- exporturile respectă rolurile:
  - client: doar datele proprii
  - admin/angajat: toate datele

#### 🌐 Compatibilitate cross-browser
- layout responsive
- Bootstrap pentru grid, formulare și componente
- testat în Chrome / Firefox / Edge

---

## 👤 Roluri și acces

### 🔴 Administrator
- gestionează camere, servicii și conturi
- vede toate rezervările și facturile
- acces la import extern + analytics/statistici

### 🟠 Angajat (Recepționer)
- gestionează rezervări
- emite facturi
- acces la analytics/statistici

### 🟢 Client
- caută camere disponibile într-o perioadă
- realizează rezervări online
- vede rezervările proprii
- exportă rezervările (Excel)
- vede și exportă facturile proprii (PDF)
- trimite mesaje prin formularul de contact
- consultă pagina publică cu conținut extern (parcuri/atracții)

Separarea rolurilor este realizată cu:
- `requireLogin()`
- `requireRole([...])`

---

## 🗄️ Baza de date

Tabele principale:
- `user`
- `camere`
- `rezervari`
- `factura`
- `serviciu`
- `atractii`
- `page_views`
- `external_cache`

---

## 🛠️ Tehnologii utilizate

- HTML / CSS
- Bootstrap
- PHP
- MySQL
- Chart.js
- Google reCAPTCHA
- PHPMailer (SMTP)
- Dompdf
- phpMyAdmin
