  let isEditing = false;
  let passwordEditing = false;

  const EDITABLE_FIELDS = ['namaLengkap', 'username', 'email', 'noTelepon', 'tanggalLahir'];

  const SAVE_ICON = `<path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>`;
  const EDIT_ICON = `<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>`;

  function handleAction() {
    if (!isEditing) {
      startEdit();
    } else {
      simpanData();
    }
  }

  function startEdit() {
    isEditing = true;
    EDITABLE_FIELDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.removeAttribute('readonly');
    });
    document.getElementById('jenisKelamin').disabled = false;
    // Sembunyikan btn-ubah saat mode edit (password dikelola sendiri)
    document.getElementById('actionIcon').innerHTML = SAVE_ICON;
    document.getElementById('actionLabel').textContent = 'Simpan';
  }

  function simpanData() {
    const nama  = document.getElementById('namaLengkap').value.trim();
    const user  = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    if (!nama || !user || !email) { showToast('Lengkapi semua field!', 'error'); return; }

    // Kunci semua field kembali
    isEditing = false;
    EDITABLE_FIELDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.setAttribute('readonly', true);
    });
    document.getElementById('jenisKelamin').disabled = true;

    // Pastikan password juga terkunci kalau sedang dibuka
    if (passwordEditing) togglePassword();

    // Update user chip
    const chip = document.querySelector('.user-chip');
    if (chip) chip.childNodes[chip.childNodes.length - 1].textContent = ' ' + user;

    document.getElementById('actionIcon').innerHTML = EDIT_ICON;
    document.getElementById('actionLabel').textContent = 'Edit';

    showToast('Data berhasil disimpan!', 'success');
  }

  // ── PASSWORD TOGGLE ──
  function togglePassword() {
    const input = document.getElementById('password');
    const btn   = document.getElementById('btnUbah');
    passwordEditing = !passwordEditing;
    if (passwordEditing) {
      input.removeAttribute('readonly');
      input.type = 'text';
      input.focus();
      btn.textContent = 'Selesai';
      btn.style.background = '#432825';
      btn.style.color = '#fff';
      btn.style.borderColor = '#432825';
    } else {
      input.setAttribute('readonly', true);
      input.type = 'password';
      btn.textContent = 'Ubah';
      btn.style.background = '';
      btn.style.color = '';
      btn.style.borderColor = '';
    }
  }

  // ── LOGOUT MODAL ──
  function openLogout() { document.getElementById('overlay').classList.add('active'); }
  function closeModal()  { document.getElementById('overlay').classList.remove('active'); }

  // ── NOTIFIKASI ──
  let notifications = [
    { id:1, type:'green', text:'Profil berhasil diperbarui terakhir kali.', time:'2 hari lalu', read:false },
    { id:2, type:'gray',  text:'Selamat datang, <strong>Pak Dwiki</strong>!', time:'01 Feb 2023', read:true },
  ];
  function renderNotifList() {
    document.getElementById('notifList').innerHTML = notifications.map(n => `
      <div class="notif-item${n.read ? '' : ' unread'}" onclick="readNotif(${n.id})">
        <div class="notif-dot ${n.type}"></div>
        <div>
          <div class="notif-text">${n.text}</div>
          <div class="notif-time">${n.time}</div>
        </div>
      </div>`).join('');
  }
  function renderNotifBadge() {
    document.getElementById('bellBadge').style.display = notifications.filter(n => !n.read).length > 0 ? 'block' : 'none';
  }
  function readNotif(id) { const n = notifications.find(x => x.id === id); if (n) n.read = true; renderNotifList(); renderNotifBadge(); }
  function markAllRead() { notifications.forEach(n => n.read = true); renderNotifList(); renderNotifBadge(); showToast('Semua notifikasi ditandai dibaca', 'info'); }
  function toggleNotif() {
    const panel = document.getElementById('notifPanel'), ov = document.getElementById('notifOverlay');
    const open = panel.classList.contains('open');
    panel.classList.toggle('open', !open); ov.classList.toggle('active', !open);
    if (!open) renderNotifList();
  }
  function closeNotif() { document.getElementById('notifPanel').classList.remove('open'); document.getElementById('notifOverlay').classList.remove('active'); }

  // ── TOAST ──
  function showToast(msg, type = 'info') {
    const icons = {
      success: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
      info:    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
    };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon">${icons[type]||icons.info}</span>${msg}<div class="toast-bar"></div>`;
    document.getElementById('toastContainer').appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(20px)'; el.style.transition='all 0.3s'; setTimeout(()=>el.remove(),300); }, 3000);
  }

  // ── INIT ──
  renderNotifBadge();