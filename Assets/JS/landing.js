// Mengambil elemen-elemen yang dibutuhkan
const modal = document.getElementById("modalLogin");
const btnBuka = document.getElementById("btnBukaLogin");
const btnTutup = document.getElementById("btnTutupLogin");

// Saat tombol 'Login Staff' di navbar diklik, tampilkan modal
btnBuka.addEventListener('click', function(e) {
    e.preventDefault();
    modal.style.display = "block";
});

// Saat tombol 'X' di dalam modal diklik, sembunyikan modal
btnTutup.addEventListener('click', function() {
    modal.style.display = "none";
});

// Jika user mengklik area abu-abu di luar kotak form, tutup modal juga
window.addEventListener('click', function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
});