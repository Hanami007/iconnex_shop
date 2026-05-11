<div class="noti-container">
    <a href="#" onclick="toggleNotiDropdown(event)" style="font-size: 1.25rem; color: #fff; text-decoration:none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; transition: 0.3s; position: relative;" onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--gold)'" onmouseout="this.style.background='transparent'; this.style.color='#fff'">
        <i class="fas fa-bell"></i>
        <span id="noti-badge">0</span>
    </a>
    <div id="noti-dropdown" class="noti-dropdown">
        <div class="noti-header">
            <span>Notifications</span>
            <button onclick="markAllAsRead()" style="background:none; border:none; color:var(--gold); font-size:0.7rem; cursor:pointer;">Mark all as read</button>
        </div>
        <div id="noti-list" class="noti-list">
            <div style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.85rem;">ไม่พบการแจ้งเตือนใหม่</div>
        </div>
    </div>
</div>
