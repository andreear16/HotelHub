# HotelHub – Tema 1, Tema 2, Tema 3 și Tema 4 (DAW)

Acest proiect reprezintă o aplicație web completă pentru managementul unui hotel, dezvoltată progresiv pe parcursul mai multor teme din cadrul disciplinei **DAW**.

Proiectul conține:
- **Tema 1** – site public (prezentare hotel)
- **Tema 2** – panou de administrare + CRUD (PHP + MySQL)
- **Tema 3** – roluri + securizare + înregistrare + reCAPTCHA
- **Tema 4** – funcționalități avansate: import/export, conținut extern, email, element multimedia, compatibilitate cross-browser

---

## 🟦 TEMA 1 – Site Public (Static)

Partea publică a aplicației prezintă hotelul și informații generale.

### Conținut:
- pagină principală (`index.html`)
- prezentarea hotelului
- imagini și descrieri
- structură HTML + CSS
- arhitectură aplicație + model bază de date (ex: `descriere.html`, `baza_date.html`)

Nu include funcționalități dinamice.

---

## 🟩 TEMA 2 – Panou Administrare (PHP + MySQL)

Implementări principale:

### 🔐 Autentificare
- login / logout
- protecție pagini prin sesiune

### 👥 CRUD Utilizatori (admin-only)
- creare, afișare, editare, ștergere
- roluri: `admin`, `angajat`, `client`

### 🛏️ CRUD Camere (admin + angajat)
- număr cameră
- tip cameră
- preț / noapte
- disponibilitate

### 📅 CRUD Rezervări
- admin / angajat: gestionare rezervări
- client: rezervări proprii

### 🧾 Facturi
- angajat / admin: emitere facturi
- client: vizualizare facturi proprii

### 🛎️ Servicii (admin-only)
- CRUD servicii hotel (denumire + preț)

---

## 🟨 TEMA 3 – Roluri + Securitate + Înregistrare + Protecție Formulare

Tema 3 adaugă:

- separarea strictă a rolurilor (verificată server-side)
- securizarea aplicației împotriva atacurilor comune
- **înregistrare utilizatori**
- **reCAPTCHA pe formularele publice**

### 🔐 Securitate implementată:
- SQL Injection  
  - prepared statements (`$conn->prepare(...)`)
- XSS  
  - afișare sigură cu `htmlspecialchars(...)`
- CSRF  
  - token CSRF în sesiune  
  - verificare CSRF pentru request-uri POST
- Form Spoofing / HTTP Request Spoofing  
  - validări server-side
  - filtrare tipuri (int / float / date)
- Protecție formulare publice  
  - `register.php` protejat cu **Google reCAPTCHA**

---

## 🟧 TEMA 4 – Funcționalități Avansate

Tema 4 extinde aplicația cu funcționalități suplimentare cerute pentru o aplicație web completă.

### 🌍 Conținut extern parsat / modelat
- import de **atracții turistice** dintr-un fișier extern (`atractii.txt`)
- datele sunt procesate și salvate în baza de date
- sursa informațiilor: **Wikipedia**
- afișare într-o pagină dedicată accesibilă clientului

### 📧 Trimitere email
- formular de **Contact** accesibil clientului
- trimitere email către recepție folosind SMTP (Gmail App Password)
- validări server-side + protecție CSRF

### 📤 Import / Export date
- **Export Excel**:
  - rezervări
- **Export PDF**:
  - facturi individuale (folosind Dompdf)
- exportul este securizat în funcție de rol:
  - client → doar datele proprii
  - admin / angajat → toate datele

### 📊 Element multimedia (statistici)
- pagină de **Statistici**
- grafic cu:
  - număr de rezervări
  - încasări totale
- implementat cu librărie JavaScript pentru grafice

### 🌐 Compatibilitate cross-browser
- layout responsive
- utilizarea **Bootstrap** pentru:
  - grid
  - carduri
  - formulare
- aplicația funcționează corect în:
  - Chrome
  - Firefox
  - Edge

---

## 👤 Roluri și acces

### 🔴 Administrator
- gestionează camere, tarife, servicii
- gestionează conturi angajați
- vede toate rezervările și toate facturile
- acces la statistici și import date externe

### 🟠 Angajat (Recepționer)
- verifică disponibilitatea camerelor
- gestionează rezervări
- emite facturi
- acces la statistici

### 🟢 Client
- caută camere disponibile într-o perioadă
- realizează rezervări online
- vede rezervările proprii
- vede și exportă facturile proprii (PDF)
- trimite mesaje prin formularul de contact
- consultă atracțiile turistice

Separarea rolurilor este realizată cu:
- `requireLogin()`
- `requireRole([...])`

---

## 🗄️ Baza de date

### Tabele utilizate:
- `user`
- `camere`
- `rezervari`
- `factura`
- `serviciu`
- `atractii`

---

## 🛠️ Tehnologii utilizate

- HTML
- CSS
- Bootstrap
- PHP
- MySQL
- Google reCAPTCHA
- Dompdf
- SMTP (email)
- phpMyAdmin

---
