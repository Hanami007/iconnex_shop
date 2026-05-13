<?php
require_once '../src/bootstrap.php';
useService('db');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch real stats
$stmt = $pdo->query("SELECT count(*) FROM courses");
$total_courses = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT count(*) FROM users WHERE role = 'user'");
$total_students = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'");
$total_revenue = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT count(*) FROM orders WHERE status = 'pending'");
$total_orders = $stmt->fetchColumn() ?: 0;

$completion_rate = "0%"; // Keep simple for now
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CourseFlow — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css?v=1.2">
<script>const csrfToken = "<?php echo getCsrfToken(); ?>";</script>
<script src="/admin/admin.js?v=1.2"></script>        
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<!-- MODAL: COURSE -->
<div class="modal-overlay" id="courseModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="courseModalTitle">✦ Add New Course</span>
      <button class="modal-close" onclick="closeModal('courseModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <input type="hidden" id="courseId" value="">
        <div class="form-group full"><label class="form-label">Course Title</label><input id="courseTitle" class="form-control" placeholder="e.g. Complete Web Development Bootcamp"/></div>
        <div class="form-group full"><label class="form-label">Description</label><textarea id="courseDesc" class="form-control" placeholder="Describe what students will learn..."></textarea></div>
        <div class="form-group"><label class="form-label">Instructor</label><input id="courseInstructor" class="form-control" placeholder="e.g. John Doe"/></div>
        <div class="form-group"><label class="form-label">Category</label>
          <div id="categoryContainer" style="display:flex; flex-direction:column; gap:8px;">
            <select id="courseCategory" class="form-control" onchange="handleCategoryChange(this)">
              <option value="">-- Select Category --</option>
              <option value="add_new">＋ Add New Category</option>
            </select>
            <div id="newCategoryWrap" style="display:none; gap:8px; align-items:center;">
              <input type="text" id="newCategoryName" class="form-control" placeholder="New category name..." style="flex:1;">
              <button type="button" class="btn btn-sm btn-primary" onclick="saveNewCategory()">Add</button>
              <button type="button" class="btn btn-sm btn-ghost" onclick="cancelNewCategory()">✕</button>
            </div>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Price (THB)</label><input id="coursePrice" class="form-control" type="number" placeholder="1500"/></div>
        <div class="form-group"><label class="form-label">Lessons (Count)</label><input id="courseLessons" class="form-control" type="number" placeholder="10" value="10"/></div>
        <div class="form-group"><label class="form-label">Duration (Hours)</label><input id="courseHours" class="form-control" type="number" placeholder="20" value="20"/></div>
        <div class="form-group"><label class="form-label">Course Thumbnail</label>
          <input type="file" id="courseImage" class="form-control" accept="image/*" style="padding: 10px;">
        </div>
        <div class="form-group full"><label class="form-label">Course Sections / Modules</label>
          <div style="display:flex;flex-direction:column;gap:15px" id="sectionList">
            <div class="section-block" style="background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #eee;">
              <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
                <input class="form-control course-section-input" placeholder="Section Title (e.g. Chapter 1)" style="flex:1; font-weight:bold;"/>
                <button type="button" class="act-btn danger" onclick="this.parentElement.parentElement.remove()">🗑</button>
              </div>
              <div class="lesson-list" style="display:flex;flex-direction:column;gap:8px;padding-left:20px;margin-bottom:10px;">
                <div style="display:flex;gap:8px;align-items:center;">
                  <input class="form-control course-lesson-input" placeholder="Lesson/Detail (e.g. 1.1 Introduction)" style="flex:1; font-size:13px;"/>
                  <button type="button" class="act-btn danger" style="width:24px;height:24px;font-size:12px;" onclick="this.parentElement.remove()">✕</button>
                </div>
              </div>
              <button type="button" class="btn btn-ghost btn-sm" style="font-size:12px; padding:4px 8px; margin-left:20px;" onclick="addLesson(this)">＋ Add Lesson</button>
            </div>
          </div>
          <button type="button" class="btn btn-ghost btn-sm" style="margin-top:8px;align-self:flex-start" onclick="addSection()">＋ Add Section</button>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('courseModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveCourse()">Save Course</button>
    </div>
  </div>
</div>

<!-- MODAL: ORDER DETAILS -->
<div class="modal-overlay" id="orderModal">
  <div class="modal" style="max-width: 850px;">
    <div class="modal-header">
      <span class="modal-title" id="orderModalTitle">✦ Order Details</span>
      <button class="modal-close" onclick="closeModal('orderModal')">✕</button>
    </div>
    <div class="modal-body" id="orderModalBody" style="padding: 25px;">
      <!-- Details will be injected here via JS -->
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('orderModal')">Close</button>
    </div>
  </div>
</div>

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
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
        Courses <span class="nav-badge"><?php echo $total_courses; ?></span>
      </div>
      <div class="nav-item" onclick="navigate('users',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Users
      </div>
      <div class="nav-item" onclick="navigate('orders',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Orders <span class="nav-badge" style="background:var(--green)"><?php echo $total_orders; ?></span>
      </div>
      <div class="nav-item" onclick="navigate('notifications',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Notifications
      </div>
    </div>
    <div class="nav-section">
      <div class="nav-label">System</div>
      <div class="nav-item" onclick="navigate('settings',this)">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.42 1.42M4.93 4.93l1.42 1.42M19.07 19.07l-1.42-1.42M4.93 19.07l1.42-1.42M20 12h2M2 12h2M12 20v2M12 2v2"/></svg>
        Settings
      </div>
    </div>
  </nav>
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">AD</div>
      <div class="user-info"><div class="user-name">Admin User</div><div class="user-role">Super Admin</div></div>
      <a href="../logout.php" style="color:var(--red);font-size:18px; text-decoration:none;" title="Logout">🚪</a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <header class="topbar">
    <div class="admin-hamburger" onclick="toggleSidebar()">☰</div>
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
        <button class="btn btn-primary" onclick="openAddCourseModal()">＋ Add New Course</button>
      </div>

      <div class="stat-grid">
        <div class="stat-card" style="--glow-color:rgba(108,99,255,.2)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(108,99,255,.12);color:var(--accent-2)">📚</div><span class="stat-change up"></span></div>
          <div class="stat-value"><?php echo number_format($total_courses); ?></div><div class="stat-label">Total Courses</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(34,211,160,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(34,211,160,.12);color:var(--green)">👩‍🎓</div><span class="stat-change up"></span></div>
          <div class="stat-value"><?php echo number_format($total_students); ?></div><div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(251,191,36,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(251,191,36,.12);color:var(--yellow)">💰</div><span class="stat-change up"></span></div>
          <div class="stat-value">฿<?php echo number_format($total_revenue); ?></div><div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card" style="--glow-color:rgba(56,189,248,.15)">
          <div class="stat-top"><div class="stat-icon" style="background:rgba(56,189,248,.12);color:var(--blue)">📈</div><span class="stat-change down"></span></div>
          <div class="stat-value"><?php echo $completion_rate; ?></div><div class="stat-label">Completion Rate</div>
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
          </div>
        </div>
      </div>
    </div>

    <!-- COURSES -->
    <div class="page" id="page-courses">
      <div class="page-header">
        <div class="page-header-left"><h1>Courses</h1><p>Manage all published and draft courses on your platform.</p></div>
        <button class="btn btn-primary" onclick="openAddCourseModal()">＋ Add New Course</button>
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
      <div class="card">
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Order #</th><th>Student</th><th>Course</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="orderTableBody"></tbody>
          </table>
        </div>
        <div class="pagination" id="orderPagination"></div>
      </div>
    </div>

    <!-- NOTIFICATIONS -->
    <div class="page" id="page-notifications">
      <div class="page-header">
        <div class="page-header-left"><h1>Notifications</h1><p>Send and manage platform announcements.</p></div>
        <button class="btn btn-primary" onclick="openNotiModal()">＋ Create Notification</button>
      </div>

      <div class="mini-stats" id="notiStats">
        <div class="mini-stat"><div class="mini-stat-val" id="ns-total">-</div><div class="mini-stat-label">Total Sent</div></div>
        <div class="mini-stat"><div class="mini-stat-val" id="ns-active" style="color:var(--green)">-</div><div class="mini-stat-label">Active</div></div>
        <div class="mini-stat"><div class="mini-stat-val" id="ns-unread" style="color:var(--red)">-</div><div class="mini-stat-label">Unread</div></div>
      </div>

      <div class="card">
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Notification</th>
                <th>Audience</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="notiTableBody">
              <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-3)">Loading notifications...</td></tr>
            </tbody>
          </table>
        </div>
        <div class="pagination" id="notiPagination"></div>
      </div>
    </div>

  </div>
</main>

<!-- MODAL: NOTIFICATION -->
<div class="modal-overlay" id="notiModal">
  <div class="modal" style="max-width: 580px;">
    <div class="modal-header">
      <span class="modal-title" id="notiModalTitle">✦ Create Announcement</span>
      <button class="modal-close" onclick="closeModal('notiModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full">
          <label class="form-label">Title</label>
          <input id="notiTitle" class="form-control" placeholder="Notification heading..."/>
        </div>
        <div class="form-group full">
          <label class="form-label">Message</label>
          <textarea id="notiMessage" class="form-control" placeholder="Write your message here..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Type</label>
          <select id="notiType" class="form-control">
            <option value="default">Default</option>
            <option value="order">Order/Purchase</option>
            <option value="promo">Promotion</option>
            <option value="warning">Warning</option>
            <option value="system">System Update</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Priority</label>
          <select id="notiPriority" class="form-control">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
          </select>
        </div>
        
        <div class="form-group full" id="audienceGroup">
          <label class="form-label">Target Audience</label>
          <div class="audience-selector" style="display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; margin-top:5px;">
             <label class="audience-option" style="display:flex; align-items:center; gap:8px; padding:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="audience" value="all" checked onchange="toggleAudienceUI()">
                <div style="font-size:13px;"><strong>Everyone</strong><div style="font-size:11px;color:var(--text-3)">All users</div></div>
             </label>
             <label class="audience-option" style="display:flex; align-items:center; gap:8px; padding:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="audience" value="purchased" onchange="toggleAudienceUI()">
                <div style="font-size:13px;"><strong>All Buyers</strong><div style="font-size:11px;color:var(--text-3)">Users with any purchase</div></div>
             </label>
             <label class="audience-option" style="display:flex; align-items:center; gap:8px; padding:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="audience" value="course_buyers" onchange="toggleAudienceUI()">
                <div style="font-size:13px;"><strong>Course Buyers</strong><div style="font-size:11px;color:var(--text-3)">Buyers of specific course</div></div>
             </label>
             <label class="audience-option" style="display:flex; align-items:center; gap:8px; padding:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="audience" value="custom" onchange="toggleAudienceUI()">
                <div style="font-size:13px;"><strong>Individual</strong><div style="font-size:11px;color:var(--text-3)">Pick specific users</div></div>
             </label>
          </div>
        </div>

        <div class="form-group full" id="courseSelectRow" style="display:none">
          <label class="form-label">Select Course</label>
          <select id="notiCourseId" class="form-control"></select>
        </div>

        <div class="form-group full" id="userSearchRow" style="display:none">
          <label class="form-label">Search Users</label>
          <div style="position:relative">
            <input id="notiUserSearch" class="form-control" placeholder="Search name or email..." oninput="searchUsersForNoti(this.value)"/>
            <div id="userSearchResults" style="position:absolute; top:100%; left:0; right:0; background:var(--surface-3); border:1px solid var(--border-hi); border-radius:8px; z-index:10; display:none; max-height:200px; overflow-y:auto; box-shadow:var(--shadow-lg);"></div>
          </div>
          <div id="selectedUsers" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('notiModal')">Cancel</button>
      <button class="btn btn-primary" id="btnSendNoti" onclick="sendNotification()">Send Notification</button>
    </div>
  </div>
</div>

<style>
.audience-option:has(input:checked) { border-color: var(--accent) !important; background: rgba(108,99,255,0.1) !important; }
.user-res-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--border); transition: background .15s; }
.user-res-item:hover { background: var(--surface-1); }
.user-res-item:last-child { border-bottom: none; }
.user-tag { background: var(--surface-3); border: 1px solid var(--border-hi); padding: 4px 10px; border-radius: 6px; font-size: 12px; display: flex; align-items: center; gap: 8px; }
.user-tag button { color: var(--red); font-size: 14px; line-height: 1; }
</style>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('active');
  overlay.classList.toggle('active');
}
</script>

</body>
</html>
