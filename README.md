# Mini Inventory

Mini Inventory adalah platform inventaris untuk produk kami (bukan inventaris nyata). Menggunakan PHP untuk backend, MySQL untuk database, dan ReactJS untuk frontend.

## Database

Kami menggunakan database MySQL untuk proyek ini, dengan nama database "pweb_ujian". Hanya terdapat satu tabel untuk projek ini, yang dinamakan tabel "products". tabel ini berisikan:

- id, sebagai integer, AUTO_INCREMENT
- name, sebagai varchar
- quantity, sebagai integer
- image, sebagai varchar (akan diisikan image URL)

dengan query sebagai berikut:

```bash
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(255) NOT NULL,     
    quantity INT NOT NULL,        
    image VARCHAR(255) NOT NULL
);
```

## Backend

Untuk bagian backend, kami membuat program CRUD rest API sederhana.

Disini kami memilih bahasa PHP dan web server apache, projeknya hanya menggunakan single file bernama crud.php. berisikan:

- CORS Header
- Koneksi ke MySQL
- HTTP Method
- CRUD sederhana (GET, POST, PUT, DELETE) 

## Frontend

Bagian frontend, kami menggunakan framework ReactJS untuk mempermudah design, library UI yang kami pilih adalah chakra-ui. ada beberapa bagian yang kami bagi untuk setiap fungsi nya,

1. Components, berisikan komponen penting untuk menampilkan bagian yang penting di setiap  halaman website, dibagi menjadi 3, yaitu:
- Navbar, berisikan tombol home (nama aplikasinya), tambah produk, ganti mode (light dan dark)
- Footer, berisikan nama developer aplikasi nya
- ProductCard, sebuah pop up kartu untuk mengubah data pada salah satu produk yang dipilih

2. Pages, berisikan halaman yang di bagi menjadi 2 bagian:
- HomePage, menampilkan semua produk, bisa edit dan hapus produk
- CreatePage, menampilkan halaman untuk menambah produk baru

3. Store, untuk pengaturan fetching API dari sisi backend. Kami menggunakan zustand untuk mempermudah manajemen state, serta memudahkan untuk simpan dan akses data dari backend

4. App, menggabungkan semua components dan pages ke satu file ini

5. main, untuk render App ke index.html untuk ditampilkan di browser

## Instalasi

Ubah ke directory frontend, lalu lakukan instalasi untuk membangun aplikasi ReactJS, setelah itu bisa memulai programnya dengan mengakses ke http://localhost:5173/ di browser anda

```bash
cd frontend
npm install
npm run dev
```
Jangan lupa nyalahkan web server apache dan MySQL nya