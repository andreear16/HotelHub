# HotelHub – Tema 1 și Tema 2 DAW

Acest proiect conține atât Tema 1 (site public), cât și Tema 2 (panou administrare cu CRUD) pentru aplicația HotelHub.
Link site: https://aradu.daw.ssmr.ro/proiect/

---

## 🟦 TEMA 1 – Site Public

Pagina publică prezintă hotelul și include:

- pagină principală (index.html)
- prezentarea hotelului
- poze și descriere
- structură HTML + CSS
- arhitectură și modelul bazei de date (descriere.html, baza_date.html)

Nu include funcționalități dinamice.

---

## 🟩 TEMA 2 – Panou Administrare (PHP + MySQL)

Implementări incluse:

### 🔐 Autentificare
- login / logout
- protecție pagini prin sesiune

### 👥 CRUD Utilizatori
- admin, angajat, client (folosesc același tabel `user`)
- creare, afișare, editare, ștergere

### 🏨 CRUD Camere
- număr cameră
- tip cameră
- preț / noapte
- disponibilitate

### 📅 CRUD Rezervări
- rezervări pentru clienți făcute de angajați sau admini
- selectare client, angajat, cameră
- check-in, check-out, status rezervare

---
## 🗄️ Baza de date

### Tabele implementate:
- **user**
- **camere**
- **rezervari**

Fiecare tabel respectă structura din modelul de date din Tema 1.

---

## 🛠️ Tehnologii

- HTML, CSS
- PHP
- MySQL
- phpMyAdmin

---

## 🔗 Acces panou administrare

app/auth/login.php

---
