
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CourseFlow — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css">
<script src="/admin/admin.js"></script>        
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<!-- MODAL -->
<div class="modal-overlay" id="courseModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">✦ Add New Course</span>
      <button class="modal-close" onclick="closeModal('courseModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full"><label class="form-label">Course Title</label><input class="form-control" placeholder="e.g. Complete Web Development Bootcamp"/></div>
        <div class="form-group full"><label class="form-label">Description</label><textarea class="form-control" placeholder="Describe what students will learn..."></textarea></div>
        <div class="form-group"><label class="form-label">Category</label>
          <select class="form-control"><option>Web Development</option><option>Data Science</option><option>UI/UX Design</option><option>Mobile Development</option><option>DevOps</option><option>Business</option></select>
        </div>
        <div class="form-group"><label class="form-label">Price (USD)</label><input class="form-control" type="number" placeholder="49.99"/></div>
        <div class="form-group full"><label class="form-label">Course Thumbnail</label>
          <div class="upload-box"><div style="font-size:28px">🖼️</div><p>Click to upload or drag & drop<br>PNG, JPG up to 5MB</p></div>
        </div>
        <div class="form-group full"><label class="form-label">Preview Video URL</label><input class="form-control" placeholder="https://youtube.com/..."/></div>
        <div class="form-group full"><label class="form-label">Course Sections / Modules</label>
          <div style="display:flex;flex-direction:column;gap:8px" id="sectionList">
            <div style="display:flex;gap:8px;align-items:center"><input class="form-control" placeholder="Section 1: Getting Started" style="flex:1"/><button class="act-btn danger" onclick="this.parentElement.remove()">🗑</button></div>
          </div>
          <button class="btn btn-ghost btn-sm" style="margin-top:8px;align-self:flex-start" onclick="addSection()">＋ Add Section</button>
        </div>
        <div class="form-group full">
          <div class="toggle-row">
            <div><div class="form-label">Publish Course</div><div style="font-size:.75rem;color:var(--text-3);margin-top:2px">Toggle to make course live immediately</div></div>
            <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('courseModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveCourse()">Save Course</button>
    </div>
  </div>
</div>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🎓</div>
    <span class="logo-text">Course<span>Flow</span></span>
  </div>
  <nav>
    <div class="nav-section">
      <div class="nav-label">Main</div>
      <div class="nav-item active" onclick="navigate('dashboard',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </div>
      <div class="nav-item" onclick="navigate('courses',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Courses <span class="nav-badge">24</span>
      </div>
      <div class="nav-item" onclick="navigate('users',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Users
      </div>
      <div class="nav-item" onclick="navigate('orders',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Orders <span class="nav-badge" style="background:var(--green)">3</span>
      </div>
    </div>
    <div class="nav-section">
      <div class="nav-label">System</div>
      <div class="nav-item" onclick="navigate('settings',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42M20 12h2M2 12h2M12 20v2M12 2v2"/></svg>
        Settings
      </div>
      <div class="nav-item">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
        Analytics
      </div>
    </div>
  </nav>
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">AD</div>
      <div class="user-info"><div class="user-name">Admin User</div><div class="user-role">Super Admin</div></div>
      <span style="color:var(--text-3);font-size:14px">⚙</span>
    </div>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <header class="topbar">
    <span class="topbar-title" id="topbarTitle">Dashboard</span>
    <div class="topbar-search">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--text-3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input placeholder="Search anything..." id="globalSearch" oninput="handleSearch(this.value)"/>
    </div>
    <div class="topbar-actions">
      <button class="icon-btn" onclick="showToast('🔔','3 new notifications')">🔔<span class="notif-dot"></span></button>
      <button class="icon-btn">🌙</button>
      <div class="avatar" style="width:34px;height:34px;font-size:.75rem;cursor:pointer">AD</div>
    </div>
  </header>

  <div class="content">

    <!-- DASHBOARD -->
    <div class="page active" id="page-dashboard">
      <div class="page-header">
        <div class="page-header-left"><h1>Good morning, Admin 👋</h1><p>Here's what's happening with your platform today.</p></div>
        <button class="btn btn-primary" onclick="openModal('courseModal')">＋ Add New Course</button>
      </div>

      <div class="stat-grid">
        <div class="stat-card" style="--glow-color:rgba(108,99,255,.2)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(108,99,255,.12);color:var(--accent-2)">📚</div><span class="stat-change up">↑ 12%</span></div>
          <div class="stat-value">24</div><div class="stat-label">Total Courses</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(34,211,160,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(34,211,160,.12);color:var(--green)">👩‍🎓</div><span class="stat-change up">↑ 8%</span></div>
          <div class="stat-value">4,821</div><div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(251,191,36,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(251,191,36,.12);color:var(--yellow)">💰</div><span class="stat-change up">↑ 23%</span></div>
          <div class="stat-value">$48.2K</div><div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(56,189,248,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(56,189,248,.12);color:var(--blue)">📈</div><span class="stat-change down">↓ 2%</span></div>
          <div class="stat-value">87%</div><div class="stat-label">Completion Rate</div>
        </div>
      </div>

      <div class="charts-row">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Revenue &amp; Enrollments — 2025</span>
            <div style="display:flex;gap:8px">
              <button class="btn btn-ghost btn-sm">Revenue</button>
              <button class="btn btn-ghost btn-sm">Users</button>
            </div>
          </div>
          <div class="card-body" style="padding-top:10px">
            <svg class="chart-svg" viewBox="0 0 560 180" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="lg1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6c63ff" stop-opacity=".4"/><stop offset="100%" stop-color="#6c63ff" stop-opacity="0"/></linearGradient>
                <linearGradient id="lg2" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#22d3a0" stop-opacity=".3"/><stop offset="100%" stop-color="#22d3a0" stop-opacity="0"/></linearGradient>
              </defs>
              <line x1="0" y1="36" x2="560" y2="36" stroke="#ffffff08" stroke-width="1"/>
              <line x1="0" y1="72" x2="560" y2="72" stroke="#ffffff08" stroke-width="1"/>
              <line x1="0" y1="108" x2="560" y2="108" stroke="#ffffff08" stroke-width="1"/>
              <line x1="0" y1="144" x2="560" y2="144" stroke="#ffffff08" stroke-width="1"/>
              <path d="M0,140 L80,110 L160,90 L240,115 L320,70 L400,50 L480,30 L560,45 L560,180 L0,180 Z" fill="url(#lg1)" opacity=".8"/>
              <path d="M0,140 L80,110 L160,90 L240,115 L320,70 L400,50 L480,30 L560,45" fill="none" stroke="#6c63ff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M0,155 L80,148 L160,140 L240,132 L320,125 L400,118 L480,105 L560,98 L560,180 L0,180 Z" fill="url(#lg2)" opacity=".7"/>
              <path d="M0,155 L80,148 L160,140 L240,132 L320,125 L400,118 L480,105 L560,98" fill="none" stroke="#22d3a0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="480" cy="30" r="4" fill="#6c63ff" stroke="#0d0f14" stroke-width="2"/>
              <circle cx="560" cy="45" r="4" fill="#6c63ff" stroke="#0d0f14" stroke-width="2"/>
              <circle cx="560" cy="98" r="4" fill="#22d3a0" stroke="#0d0f14" stroke-width="2"/>
              <text x="0" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">JAN</text>
              <text x="75" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">FEB</text>
              <text x="155" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">MAR</text>
              <text x="235" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">APR</text>
              <text x="315" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">MAY</text>
              <text x="395" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">JUN</text>
              <text x="475" y="175" fill="#555d74" font-size="9" font-family="JetBrains Mono">JUL</text>
              <circle cx="8" cy="12" r="4" fill="#6c63ff"/><text x="16" y="16" fill="#8890a8" font-size="9" font-family="Sora">Revenue</text>
              <circle cx="80" cy="12" r="4" fill="#22d3a0"/><text x="88" y="16" fill="#8890a8" font-size="9" font-family="Sora">Enrollments</text>
            </svg>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Sales by Category</span></div>
          <div class="card-body">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" width="120" height="120" style="flex-shrink:0">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#1a1e28" stroke-width="20"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="#6c63ff" stroke-width="20" stroke-dasharray="120 182" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="#22d3a0" stroke-width="20" stroke-dasharray="72 230" stroke-dashoffset="-120" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="#fbbf24" stroke-width="20" stroke-dasharray="48 254" stroke-dashoffset="-192" transform="rotate(-90 60 60)"/>
                <circle cx="60" cy="60" r="48" fill="none" stroke="#38bdf8" stroke-width="20" stroke-dasharray="32 270" stroke-dashoffset="-240" transform="rotate(-90 60 60)"/>
                <text x="60" y="57" text-anchor="middle" fill="#f0f2f8" font-size="14" font-weight="700" font-family="JetBrains Mono">$48K</text>
                <text x="60" y="70" text-anchor="middle" fill="#555d74" font-size="7" font-family="Sora">Total</text>
              </svg>
              <div class="donut-legend">
                <div class="legend-item"><span class="legend-dot" style="background:#6c63ff"></span>Web Dev<span class="legend-val">40%</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#22d3a0"></span>Data Sci<span class="legend-val">24%</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#fbbf24"></span>Design<span class="legend-val">16%</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#38bdf8"></span>Mobile<span class="legend-val">11%</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#555d74"></span>Other<span class="legend-val">9%</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">Recent Activity</span><button class="btn btn-ghost btn-sm">View all</button></div>
        <div class="card-body">
          <div class="activity-list">
            <div class="activity-item">
              <div class="activity-dot" style="background:rgba(34,211,160,.12);color:var(--green)">🎓</div>
              <div class="activity-info"><strong>Maria Chen enrolled in Complete React Course</strong><span>New enrollment · Student profile created</span></div>
              <span class="activity-time">2m ago</span>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background:rgba(108,99,255,.12);color:var(--accent-2)">📗</div>
              <div class="activity-info"><strong>New course published: Python for Beginners</strong><span>Added by instructor James Lee</span></div>
              <span class="activity-time">18m ago</span>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background:rgba(251,191,36,.12);color:var(--yellow)">💳</div>
              <div class="activity-info"><strong>Order #4821 completed — $89.00</strong><span>Alex Johnson · UI/UX Masterclass</span></div>
              <span class="activity-time">1h ago</span>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background:rgba(56,189,248,.12);color:var(--blue)">👤</div>
              <div class="activity-info"><strong>New instructor account: Sarah Park</strong><span>Awaiting email verification</span></div>
              <span class="activity-time">3h ago</span>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background:rgba(248,113,113,.12);color:var(--red)">⚠️</div>
              <div class="activity-info"><strong>Course "Node.js Advanced" flagged for review</strong><span>Content policy check required</span></div>
              <span class="activity-time">5h ago</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- COURSES -->
    <div class="page" id="page-courses">
      <div class="page-header">
        <div class="page-header-left"><h1>Courses</h1><p>Manage all published and draft courses on your platform.</p></div>
        <button class="btn btn-primary" onclick="openModal('courseModal')">＋ Add New Course</button>
      </div>
      <div class="filters-row">
        <input class="filter-input" style="flex:1;max-width:280px" placeholder="🔍  Search courses..." oninput="filterTable(this.value,'courseTable')"/>
        <select class="filter-input"><option>All Categories</option><option>Web Development</option><option>Data Science</option><option>Design</option><option>Mobile</option></select>
        <select class="filter-input"><option>All Status</option><option>Published</option><option>Draft</option></select>
        <button class="btn btn-ghost btn-sm">⬇ Export</button>
      </div>
      <div class="card">
        <div class="table-wrap">
          <table class="data-table" id="courseTable">
            <thead><tr><th>Course</th><th>Category</th><th>Price</th><th>Students</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="courseTableBody"></tbody>
          </table>
        </div>
        <div class="pagination" id="coursePagination"></div>
      </div>
    </div>

    <!-- USERS -->
    <div class="page" id="page-users">
      <div class="page-header">
        <div class="page-header-left"><h1>Users</h1><p>Manage students, instructors, and administrators.</p></div>
        <button class="btn btn-primary" onclick="showToast('👤','Invite user dialog opening...')">＋ Invite User</button>
      </div>
      <div class="filters-row">
        <input class="filter-input" style="flex:1;max-width:280px" placeholder="🔍  Search users..."/>
        <select class="filter-input"><option>All Roles</option><option>Student</option><option>Instructor</option><option>Admin</option></select>
        <select class="filter-input"><option>All Status</option><option>Active</option><option>Suspended</option></select>
      </div>
      <div class="card">
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Enrolled</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="userTableBody"></tbody>
          </table>
        </div>
        <div class="pagination" id="userPagination"></div>
      </div>
    </div>

    <!-- ORDERS -->
    <div class="page" id="page-orders">
      <div class="page-header">
        <div class="page-header-left"><h1>Orders &amp; Enrollments</h1><p>Track purchases and payment statuses across the platform.</p></div>
        <button class="btn btn-ghost">⬇ Export CSV</button>
      </div>
      <div class="mini-stats">
        <div class="mini-stat"><div class="mini-stat-val" style="color:var(--green)">$48,210</div><div class="mini-stat-label">Total Revenue</div></div>
        <div class="mini-stat"><div class="mini-stat-val">1,024</div><div class="mini-stat-label">Total Orders</div></div>
        <div class="mini-stat"><div class="mini-stat-val" style="color:var(--yellow)">17</div><div class="mini-stat-label">Pending</div></div>
        <div class="mini-stat"><div class="mini-stat-val" style="color:var(--red)">3</div><div class="mini-stat-label">Refunded</div></div>
      </div>
      <div class="filters-row">
        <input class="filter-input" style="flex:1;max-width:280px" placeholder="🔍  Search orders..."/>
        <select class="filter-input"><option>All Status</option><option>Completed</option><option>Pending</option><option>Refunded</option></select>
        <input class="filter-input" type="date"/>
      </div>
      <div class="card">
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Order #</th><th>Student</th><th>Course</th><th>Amount</th><th>Payment</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="orderTableBody"></tbody>
          </table>
        </div>
        <div class="pagination" id="orderPagination"></div>
      </div>
    </div>

    <!-- SETTINGS -->
    <div class="page" id="page-settings">
      <div class="page-header">
        <div class="page-header-left"><h1>Settings</h1><p>Configure your platform and account preferences.</p></div>
        <button class="btn btn-primary" onclick="showToast('✅','Settings saved!')">Save Changes</button>
      </div>
      <div class="settings-grid">
        <div class="settings-nav">
          <div class="settings-nav-item active">General</div>
          <div class="settings-nav-item">Appearance</div>
          <div class="settings-nav-item">Notifications</div>
          <div class="settings-nav-item">Payments</div>
          <div class="settings-nav-item">Security</div>
          <div class="settings-nav-item">Integrations</div>
        </div>
        <div class="settings-section card">
          <div class="card-header"><span class="card-title">Platform Settings</span></div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Platform Name</strong><span>Displayed across all pages</span></div>
            <input class="form-control" value="CourseFlow" style="max-width:220px"/>
          </div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Default Currency</strong><span>Used for pricing display</span></div>
            <select class="form-control" style="max-width:140px"><option>USD ($)</option><option>EUR (€)</option><option>THB (฿)</option></select>
          </div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Allow Guest Checkout</strong><span>Users can buy without registering</span></div>
            <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
          </div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Email Notifications</strong><span>Send emails on new enrollments</span></div>
            <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
          </div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Maintenance Mode</strong><span>Take the site offline temporarily</span></div>
            <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
          </div>
          <div class="settings-row">
            <div class="settings-row-info"><strong>Platform Timezone</strong><span>Used for scheduling and reports</span></div>
            <select class="form-control" style="max-width:200px"><option>UTC+7 (Bangkok)</option><option>UTC+0 (London)</option><option>UTC-5 (New York)</option></select>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>


</body>
</html>