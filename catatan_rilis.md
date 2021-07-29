#### [v21.07-premium-beta13]

Di rilis ini, versi 21.07-premium-beta13, menyediakan [untuk diisi]. Rilis ini juga berisi penambahan fitur dan perbaikan lain yang diminta Komunitas SID.

Terima kasih pada [untuk diisi] yang terus berkontribusi. Terima kasih pula pada [untuk diisi] yang baru mulai berkontribusi.


#### Penambahan Fitur
1. [#4224](https://github.com/OpenSID/OpenSID/issues/4224) Tambahkan kolom BPJS Ketenagakerjaan di biodata penduduk, beserta statistiknya.
2. Perjelas judul Asuransi menjadi Asuransi Kesehatan di biodata penduduk.
3. [#3598](https://github.com/OpenSID/OpenSID/issues/3598) Sekarang Buku Peraturan Desa dan Buku Keputusan Kepala Desa dapat dipilah berdasarkan tahun.
4. [#4299](https://github.com/OpenSID/OpenSID/issues/4299) Tambahkan ID BDT pada data Kependudukan > Rumah Tangga dan Statistik > Kependudukan > Rumah Tangga.
5. [#4300](https://github.com/OpenSID/OpenSID/issues/4300) Tampilkan ID BDT di Surat Keterangan Kurang Mampu.
6. [#2321](https://github.com/OpenSID/OpenSID/issues/2321) Kirim laporan Siskeudes ke OpenDK melalu API


#### Perbaikan BUG
1. [#4301](https://github.com/OpenSID/OpenSID/issues/4301) Perbaiki id validasi yang duplikat di form pembuatan surat dan global modal setting.
2. [#4305](https://github.com/OpenSID/OpenSID/issues/4301) Perbaiki peta dusun yang tidak tampil semua.
3. [#4271](https://github.com/OpenSID/OpenSID/issues/4271) Perbaiki input nama dan deskripsi produk pada Modul Lapak.
4. Sekarang impor peta data persil tersimpan dan tampil benar.
5. [#4311](https://github.com/OpenSID/OpenSID/issues/4311) Sekarang blok tanda tangan di Lembar Disposisi sesuai dengan pilihan petugas.
6. [#4287](https://github.com/OpenSID/OpenSID/issues/4287) Sekarang dari statistik kependudukan, bisa tampilkan data penduduk yang belum mengisi Kepemilikan KTP.
7. [#4309](https://github.com/OpenSID/OpenSID/issues/4309) Perbaiki paginasi pada modul Menu Statis.
8. [#4310](https://github.com/OpenSID/OpenSID/issues/4310) Perbaiki penyimpanan mutasi C-Desa dan penghapusan pemilik awal.
9. [#4313](https://github.com/OpenSID/OpenSID/issues/4313) Perbaiki form input data penduduk pada kolom data yg harus diketik, enter menyebabkan mengarah ke peta.


#### Perubahan Teknis
1. Kembalikan commit 6d7d4f776c6a39871a2310dc6d6b0b973aa9e572 yang tertimpa.
2. Rapikan script di form setting untuk kategori readonly demo_mode.
3. Otomatis simpan token pelanggan terbaru jika pelanggan melalakukan pemesanan.
4. Default server tracker/pantau dan layanan.
5. Sederhanakan modul menu