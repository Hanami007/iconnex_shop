/**
 * Notification UI Component Logic
 */

async function updateNotiBadge() {
  const data = await window.NotificationService.getUnreadCount();
  const badge = document.getElementById('noti-badge');
  if (badge) {
    if (data.success && data.count > 0) {
      badge.textContent = data.count > 9 ? '9+' : data.count;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  }
}

async function toggleNotiDropdown(e) {
  if (e) e.preventDefault();
  const dropdown = document.getElementById('noti-dropdown');
  if (!dropdown) return;
  
  const isOpen = dropdown.classList.contains('active');
  if (!isOpen) {
    dropdown.classList.add('active');
    fetchNotifications();
  } else {
    dropdown.classList.remove('active');
  }
}

async function fetchNotifications() {
  const list = document.getElementById('noti-list');
  if (!list) return;

  const linkify = (text) => {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, (url) => `<a href="${url}" target="_blank" onclick="event.stopPropagation()" style="color:var(--gold); text-decoration:underline;">${url}</a>`);
  };
  
  list.innerHTML = '<div style="padding:20px; text-align:center; color:var(--gold-soft);">กำลังโหลด...</div>';
  
  const data = await window.NotificationService.getNotifications();
  
  if (data.success && data.data.length > 0) {
    list.innerHTML = data.data.map(n => {
      const hasLink = n.link && n.link.trim() !== '';
      const clickAction = hasLink ? `window.open('${n.link.trim()}', '_blank');` : '';
      
      let typeIcon = '<i class="fas fa-bell"></i>';
      if (n.type === 'order') typeIcon = '<i class="fas fa-shopping-basket"></i>';
      if (n.type === 'promo') typeIcon = '<i class="fas fa-tag"></i>';

      return `
        <div class="noti-item ${n.is_read == 0 ? 'unread' : ''}" 
             onclick="${clickAction} markAsRead(${n.id}, this)"
             style="${hasLink ? 'cursor:pointer;' : ''}">
          <div class="noti-item-header">
            <span class="noti-type type-${n.type || 'default'}">${typeIcon} ${(n.type || 'DEFAULT').toUpperCase()}</span>
            <span class="noti-time">${formatDate(n.created_at)}</span>
          </div>
          <div class="noti-title">${n.title}</div>
          <div class="noti-msg">${linkify(n.message)}</div>
          ${hasLink ? `<div style="margin-top:8px; font-size:0.75rem; color:var(--gold); font-weight:700;"><i class="fas fa-external-link-alt"></i> ดูรายละเอียด</div>` : ''}
        </div>
      `;
    }).join('');
  } else {
    list.innerHTML = '<div style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.85rem;">ไม่พบการแจ้งเตือนใหม่</div>';
  }
}

async function markAsRead(id, el) {
  if (el && !el.classList.contains('unread')) return;
  const data = await window.NotificationService.markAsRead(id);
  if (data.success) {
    if (el) el.classList.remove('unread');
    updateNotiBadge();
  }
}

async function markAllAsRead() {
    const unreadItems = document.querySelectorAll('.noti-item.unread');
    for (let item of unreadItems) {
        const id_match = item.getAttribute('onclick').match(/\d+/);
        if (id_match) {
            markAsRead(id_match[0], item);
        }
    }
}

// Internal helper
function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr.replace(/-/g, "/"));
  if (isNaN(date.getTime())) return '';
  const now = new Date();
  const diff = Math.floor((now - date) / 1000);
  if (diff < 60) return 'เมื่อสักครู่';
  if (diff < 3600) return Math.floor(diff / 60) + ' นาทีที่แล้ว';
  if (diff < 86400) return Math.floor(diff / 3600) + ' ชม. ที่แล้ว';
  return date.toLocaleDateString('th-TH', { day: 'numeric', month: 'short' });
}

// Global click handler for dropdown closure
document.addEventListener('click', (e) => {
  const container = document.querySelector('.noti-container');
  const dropdown = document.getElementById('noti-dropdown');
  if (container && !container.contains(e.target) && dropdown) {
    dropdown.classList.remove('active');
  }
});

// Export to global
window.updateNotiBadge = updateNotiBadge;
window.toggleNotiDropdown = toggleNotiDropdown;
window.markAsRead = markAsRead;
window.markAllAsRead = markAllAsRead;
