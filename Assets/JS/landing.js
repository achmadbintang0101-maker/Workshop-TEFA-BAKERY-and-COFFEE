// ==========================================
// 1. FITUR SCROLL ACTIVE LINK (DARI UI BARU)
// ==========================================
const sections = document.querySelectorAll("section");
const navLinks = document.querySelectorAll(".navbar-links a:not(.btn-nav)");

window.addEventListener("scroll", () => {
  let current = "";

  // Mendeteksi posisi section mana yang sedang dilihat di layar
  sections.forEach((section) => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.clientHeight;
    
    // Jika scroll sudah masuk ke area section tersebut
    if (pageYOffset >= sectionTop - (sectionHeight / 3)) {
      current = section.getAttribute("id");
    }
  });

  // Menghapus garis aktif yang lama dan menambahkannya ke menu yang baru
  navLinks.forEach((link) => {
    link.classList.remove("active");
    if (link && link.getAttribute("href") && link.getAttribute("href").includes(current)) {
      link.classList.add("active");
    }
  });
});

// ==========================================
// 2. LOGIKA MODAL LOGIN (DARI UI LAMA)
// ==========================================
const modal = document.getElementById("modalLogin");
const btnBuka = document.getElementById("btnBukaLogin");
const btnTutup = document.getElementById("btnTutupLogin");

// Pastikan elemen ada sebelum menjalankan event listener untuk mencegah error
if (btnBuka && modal && btnTutup) {
    // Saat tombol 'LOGIN' di navbar diklik, tampilkan modal
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
}