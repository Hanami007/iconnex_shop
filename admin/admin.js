let courses = [];

async function fetchCourses() {
    try {
        const res = await fetch('api_courses.php');
        const json = await res.json();
        if (json.success) {
            courses = json.data;
            renderCourses();
        }
    } catch (err) {
        console.error("Failed to fetch courses:", err);
    }
}
let users = [];
let orders = [];
let notifications = [];
let selectedUsers = []; // For custom audience
const API_NOTI = 'api_notifications.php';
const API_CAT = '../api/categories.php';
let categories = [];

async function fetchCategories() {
    try {
        const res = await fetch(API_CAT + '?action=list');
        const json = await res.json();
        if (json.success) {
            categories = json.data;
            updateCategoryDropdown();
        }
    } catch (err) {
        console.error("Failed to fetch categories:", err);
    }
}

function updateCategoryDropdown() {
    const select = document.getElementById('courseCategory');
    if (!select) return;
    
    // Keep first 2 options: placeholder and Add New
    const currentVal = select.value;
    select.innerHTML = `
        <option value="">-- Select Category --</option>
        ${categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
        <option value="add_new">＋ Add New Category</option>
    `;
    if (currentVal && currentVal !== 'add_new') select.value = currentVal;
}

function handleCategoryChange(select) {
    const wrap = document.getElementById('newCategoryWrap');
    if (select.value === 'add_new') {
        wrap.style.display = 'flex';
        document.getElementById('newCategoryName').focus();
    } else {
        wrap.style.display = 'none';
    }
}

async function saveNewCategory() {
    const name = document.getElementById('newCategoryName').value.trim();
    if (!name) return;
    
    try {
        const fd = new FormData();
        fd.append('name', name);
        const res = await fetch(API_CAT + '?action=create', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd
        });
        const json = await res.json();
        if (json.success) {
            showToast('✅', 'Category added!');
            await fetchCategories();
            document.getElementById('courseCategory').value = json.id;
            cancelNewCategory();
        } else {
            showToast('❌', json.error);
        }
    } catch (err) {
        showToast('❌', 'Failed to save category');
    }
}

function cancelNewCategory() {
    document.getElementById('newCategoryWrap').style.display = 'none';
    document.getElementById('newCategoryName').value = '';
    if (document.getElementById('courseCategory').value === 'add_new') {
        document.getElementById('courseCategory').value = '';
    }
}

async function fetchOrders() {
    try {
        const res = await fetch('api_orders.php');
        const json = await res.json();
        if (json.success) {
            orders = json.data;
            renderOrders();
        }
    } catch(err) {
        console.error('Error fetching orders:', err);
    }
}

async function updateOrderStatus(id, status) {
    let msg = status === 'completed' ? 'ยืนยันการอนุมัติออเดอร์นี้?' : 'ยืนยันการเปลี่ยนสถานะ?';
    if (!confirm(msg)) return;
    try {
        const fd = new URLSearchParams();
        fd.append('action', 'update_status');
        fd.append('id', id);
        fd.append('status', status);
        const res = await fetch('api_orders.php', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd
        });
        const json = await res.json();
        if (json.success) {
            showToast('✅', 'อัปเดตสถานะเรียบร้อยแล้ว!');
            fetchOrders(); 
            closeModal('orderModal');
        } else {
            showToast('❌', 'Error: ' + json.error);
        }
    } catch(err) {
        showToast('❌', 'Request failed');
    }
}

async function fetchUsers() {
    try {
        const res = await fetch('api_users.php');
        const json = await res.json();
        if (json.success) {
            users = json.data;
            renderUsers();
        }
    } catch(err) {
        console.error('Error fetching users:', err);
    }
}

function paginate(data,page,per=6){const s=(page-1)*per;return{items:data.slice(s,s+per),total:data.length};}

function renderPagination(id,total,cur,fn,per=6){
  const el=document.getElementById(id);if(!el)return;
  const pages=Math.ceil(total/per);let h='';
  if(cur>1)h+=`<button class="page-btn" onclick="${fn}(${cur-1})">‹</button>`;
  for(let i=1;i<=pages;i++)h+=`<button class="page-btn ${i===cur?'active':''}" onclick="${fn}(${i})">${i}</button>`;
  if(cur<pages)h+=`<button class="page-btn" onclick="${fn}(${cur+1})">›</button>`;
  el.innerHTML=h;
}

function renderCourses(page=1){
  const{items,total}=paginate(courses,page);
  document.getElementById('courseTableBody').innerHTML=items.map(c=>`
    <tr>
      <td class="td-primary"><div class="td-course"><div class="course-thumb">${c.img}</div><span>${c.title}</span></div></td>
      <td><span class="badge badge-purple">${c.category}</span></td>
      <td style="font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600">${c.price}</td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.8rem">${c.students.toLocaleString()}</td>
      <td><span class="badge badge-green">${c.status.charAt(0).toUpperCase()+c.status.slice(1)}</span></td>
      <td><div class="actions">
        <button class="act-btn" onclick="editCourse(${c.id})">✏️</button>
        <button class="act-btn danger" onclick="deleteCourse(${c.id})">🗑</button>
      </div></td>
    </tr>`).join('');
  renderPagination('coursePagination',total,page,'renderCourses');
}

function renderUsers(page=1){
  const{items,total}=paginate(users,page);
  const rc={Student:'badge-blue',Instructor:'badge-purple',Admin:'badge-yellow'};
  document.getElementById('userTableBody').innerHTML=items.map(u=>`
    <tr>
      <td class="td-primary"><div style="display:flex;align-items:center;gap:10px"><div class="avatar" style="width:30px;height:30px;font-size:.7rem">${u.name.split(' ').map(n=>n[0]).join('')}</div>${u.name}</div></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.78rem">${u.email}</td>
      <td><span class="badge ${rc[u.role]||'badge-blue'}">${u.role}</span></td>
      <td>${u.enrolled}</td>
      <td>${u.status==='active'?'<span class="badge badge-green">Active</span>':u.status==='suspended'?'<span class="badge badge-red">Suspended</span>':'<span class="badge badge-yellow">Pending</span>'}</td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.77rem">${u.joined}</td>
      <td><div class="actions">
        <button class="act-btn" onclick="openSendToUser(${u.id}, '${u.name.replace(/'/g, "\\'")}')" title="Send Notification">🔔</button>
        <button class="act-btn" onclick="showToast('✏️','User editor opened')">✏️</button>
        <button class="act-btn danger" onclick="showToast('⛔','User deactivated')">⛔</button>
      </div></td>
    </tr>`).join('');
  renderPagination('userPagination',total,page,'renderUsers');
}

function renderOrders(page=1){
  const{items,total}=paginate(orders,page);
  const sc={completed:'badge-green',pending:'badge-yellow',cancelled:'badge-red'};
  document.getElementById('orderTableBody').innerHTML=items.map(o=>`
    <tr>
      <td style="font-family:'JetBrains Mono',monospace;font-weight:600;color:var(--accent-2)">${o.order_no}</td>
      <td class="td-primary">${o.student}</td>
      <td><span style="font-size:0.85rem; color:var(--text-3)">${o.course}</span></td>
      <td style="font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600">${o.amount}</td>
      <td><span class="badge ${sc[o.status]}">${o.status.charAt(0).toUpperCase()+o.status.slice(1)}</span></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.77rem">${o.date}</td>
      <td><div class="actions">
        <button class="act-btn" onclick="viewOrderDetails(${o.id})" title="View Details">👁</button>
        <button class="act-btn" onclick="openSendToOrder(${o.id}, '${o.student.replace(/'/g, "\\'")}')" title="Notify Customer">🔔</button>
      </div></td>
    </tr>`).join('');
  renderPagination('orderPagination',total,page,'renderOrders');
}

function viewOrderDetails(orderId) {
    const order = orders.find(o => o.id == orderId);
    if (!order) return;

    document.getElementById('orderModalTitle').textContent = `Order Details: ${order.order_no}`;
    
    let itemsHtml = order.items.map(item => `
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #eee; padding-bottom:5px;">
            <span>${item.name} x ${item.qty || 1}</span>
            <span style="font-family:'JetBrains Mono'; font-weight:600;">฿${(item.price * (item.qty || 1)).toLocaleString()}</span>
        </div>
    `).join('');

    const slipHtml = order.slip 
        ? `<img src="../${order.slip}" style="max-width:100%; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); cursor:pointer;" onclick="window.open('../${order.slip}')" title="Click to enlarge">`
        : `<div style="color:#aaa; font-style:italic;">No payment slip uploaded</div>`;

    document.getElementById('orderModalBody').innerHTML = `
        <div style="display:grid; grid-template-columns: 1.1fr 0.9fr; gap: 32px;">
            <!-- Left Column: Info & Items -->
            <div>
                <!-- Customer Info -->
                <div style="margin-bottom: 30px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; color:var(--accent-2);">
                        <span style="font-size:1.2rem;">👤</span>
                        <h4 style="margin:0; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; font-size:0.9rem;">Customer Information</h4>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px; background:var(--surface-2); padding:20px; border-radius:12px; border:1px solid var(--border);">
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <span style="font-size:0.7rem; color:var(--text-3); text-transform:uppercase; font-weight:700;">Name</span>
                            <span style="font-weight:600; font-size:0.95rem;">${order.student}</span>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <span style="font-size:0.7rem; color:var(--text-3); text-transform:uppercase; font-weight:700;">Email</span>
                            <span style="font-size:0.85rem; color:var(--text-2); overflow:hidden; text-overflow:ellipsis;">${order.email}</span>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <span style="font-size:0.7rem; color:var(--text-3); text-transform:uppercase; font-weight:700;">Phone</span>
                            <span style="font-size:0.9rem;">${order.phone || '-'}</span>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <span style="font-size:0.7rem; color:var(--text-3); text-transform:uppercase; font-weight:700;">Line ID</span>
                            <span style="font-size:0.9rem; color:var(--green); font-weight:600;">${order.line_id || '-'}</span>
                        </div>
                    </div>
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding:0 5px;">
                        <span style="font-size:0.75rem; color:var(--text-3);">Ordered on: <strong style="color:var(--text-2)">${order.date}</strong></span>
                        <span class="badge ${order.status === 'completed' ? 'badge-green' : (order.status === 'pending' ? 'badge-yellow' : 'badge-red')}" style="font-size:0.65rem;">${order.status.toUpperCase()}</span>
                    </div>
                </div>

                <!-- Order Items -->
                <div>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px; color:var(--accent-2);">
                        <span style="font-size:1.2rem;">🛒</span>
                        <h4 style="margin:0; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; font-size:0.9rem;">Order Items</h4>
                    </div>
                    
                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
                        <div style="padding:15px 20px; border-bottom:1px solid var(--border); background:rgba(255,255,255,0.03); display:flex; font-size:0.7rem; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:1px;">
                            <span style="flex:1;">Product Name</span>
                            <span style="width:80px; text-align:right;">Subtotal</span>
                        </div>
                        <div style="max-height:200px; overflow-y:auto; padding:5px 0;">
                            ${order.items.map(item => `
                                <div style="display:flex; align-items:center; padding:12px 20px; border-bottom:1px solid rgba(255,255,255,0.03);">
                                    <div style="flex:1;">
                                        <div style="font-weight:600; font-size:0.9rem; color:var(--text-1);">${item.name}</div>
                                        <div style="font-size:0.75rem; color:var(--text-3);">Quantity: ${item.qty || 1}</div>
                                    </div>
                                    <div style="width:100px; text-align:right; font-family:'JetBrains Mono'; font-weight:600; color:var(--text-2);">
                                        ฿${(item.price * (item.qty || 1)).toLocaleString()}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <div style="background:rgba(34,211,160,0.05); padding:18px 20px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.8rem; font-weight:700; color:var(--text-3); text-transform:uppercase;">Grand Total</span>
                            <span style="font-size:1.4rem; font-weight:800; color:var(--green); font-family:'JetBrains Mono';">
                                ${order.amount}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                ${order.status === 'pending' ? `
                <div style="margin-top:32px; display:flex; gap:12px;">
                    <button class="btn btn-primary" style="flex:1.5; justify-content:center; padding:14px; font-size:0.9rem; box-shadow:0 10px 20px rgba(108,99,255,0.2);" onclick="updateOrderStatus(${order.id}, 'completed')">
                        ✅ Approve Order
                    </button>
                    <button class="btn btn-ghost" style="flex:1; justify-content:center; padding:14px; border-color:var(--red); color:var(--red); font-size:0.9rem;" onclick="updateOrderStatus(${order.id}, 'cancelled')">
                        ❌ Reject
                    </button>
                </div>
                ` : ''}
            </div>

            <!-- Right Column: Slip -->
            <div style="text-align:center; display:flex; flex-direction:column;">
                <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:18px; color:var(--accent-2);">
                    <span style="font-size:1.2rem;">🧾</span>
                    <h4 style="margin:0; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; font-size:0.9rem;">Payment Proof</h4>
                </div>
                
                <div style="flex:1; background:var(--surface-2); border:1px solid var(--border); border-radius:16px; padding:15px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                    ${order.slip 
                        ? `<img src="../${order.slip}" style="max-width:100%; max-height:450px; border-radius:8px; box-shadow:var(--shadow); cursor:zoom-in; transition:transform 0.3s;" onclick="window.open('../${order.slip}')" title="Click to view full image">`
                        : `<div style="padding:40px; color:var(--text-3); text-align:center;">
                            <div style="font-size:3rem; margin-bottom:10px; opacity:0.3;">📄</div>
                            <div style="font-style:italic;">No payment slip uploaded</div>
                           </div>`
                    }
                    ${order.slip ? `<div style="position:absolute; bottom:25px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,0.6); color:white; font-size:0.65rem; padding:5px 12px; border-radius:20px; backdrop-filter:blur(4px); pointer-events:none;">Click to enlarge</div>` : ''}
                </div>
            </div>
        </div>
    `;

    openModal('orderModal');
}

function openMailModal(orderId) {
    const order = orders.find(o => o.id == orderId);
    if (!order) return;
    
    document.getElementById('mailOrderId').value = order.id;
    document.getElementById('mailStudentName').value = order.student;
    document.getElementById('mailStudentEmail').value = order.email;
    document.getElementById('mailMessage').value = `สวัสดีคุณ ${order.student},\n\nขอบคุณที่เลือกเรียนกับเรา! นี่คือข้อมูลการเข้าเรียนสำหรับคอร์สของคุณ:\n\n[ลิงก์เข้าเรียนที่นี่]\n\nขอให้สนุกกับการเรียนนะครับ!`;
    
    openModal('mailModal');
}

async function sendEmail() {
    const orderId = document.getElementById('mailOrderId').value;
    const subject = document.getElementById('mailSubject').value;
    const message = document.getElementById('mailMessage').value;
    
    if (!message) {
        showToast('⚠️', 'Please enter a message');
        return;
    }
    
    const btn = document.getElementById('btnSendMail');
    const originalText = btn.textContent;
    btn.textContent = 'Sending...';
    btn.disabled = true;
    
    try {
        const fd = new FormData();
        fd.append('order_id', orderId);
        fd.append('subject', subject);
        fd.append('message', message);
        
        const res = await fetch('api_send_mail.php', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd
        });
        const json = await res.json();
        
        if (json.success) {
            showToast('✅', 'Email sent successfully!');
            closeModal('mailModal');
        } else {
            showToast('❌', 'Error: ' + json.error);
        }
    } catch(err) {
        showToast('❌', 'Failed to send email');
    } finally {
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

function navigate(page,el){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  const target = document.getElementById('page-'+page);
  if(target) target.classList.add('active');
  
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  if(el) el.classList.add('active');
  
  const titles={dashboard:'Dashboard',courses:'Course Management',users:'User Management',orders:'Orders & Enrollments',invoices:'Tax Invoices',notifications:'Notifications',settings:'Settings'};
  const titleEl = document.getElementById('topbarTitle');
  if(titleEl) titleEl.textContent=titles[page]||page;

  if(page === 'notifications') {
      fetchNotifications();
      fetchNotiStats();
  }
  if(page === 'invoices') {
      fetchInvoices();
  }
}

let invoices = [];
async function fetchInvoices() {
    try {
        const res = await fetch('api_invoices.php');
        const json = await res.json();
        if (json.success) {
            invoices = json.data;
            renderInvoices();
        }
    } catch(err) {
        console.error('Error fetching invoices:', err);
    }
}

function renderInvoices(page=1) {
    const {items, total} = paginate(invoices, page, 10);
    const sc = {active: 'badge-green', cancelled: 'badge-red'};
    const tbody = document.getElementById('invoiceTableBody');
    if (!tbody) return;
    
    if (items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-3)">No invoices found.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(inv => `
        <tr>
            <td style="font-family:'JetBrains Mono',monospace;font-weight:600;color:var(--accent-2)">${inv.invoice_no}</td>
            <td class="td-primary">${inv.student}</td>
            <td style="font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600">${inv.amount}</td>
            <td><span class="badge ${sc[inv.status] || 'badge-blue'}">${inv.status.toUpperCase()}</span></td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:.77rem">${inv.date}</td>
            <td><div class="actions">
                <a href="../invoice_view.php?id=${inv.id}" target="_blank" class="act-btn" title="View Invoice">👁</a>
                <a href="../api/invoices/download.php?id=${inv.id}" target="_blank" class="act-btn" title="Download PDF">📥</a>
            </div></td>
        </tr>`).join('');
    renderPagination('invoicePagination', total, page, 'renderInvoices', 10);
}

function openModal(id){
    const el = document.getElementById(id);
    if(el) el.classList.add('open');
}
function closeModal(id){
    const el = document.getElementById(id);
    if(el) el.classList.remove('open');
}

// Global modal overlay click to close
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
    }
});

function showToast(icon,msg){
  const c=document.getElementById('toastContainer');
  if(!c) return;
  const t=document.createElement('div');t.className='toast';
  t.innerHTML=`<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span><button class="toast-close" onclick="this.parentElement.remove()">✕</button>`;
  c.appendChild(t);setTimeout(()=>t.remove(),3500);
}

function filterTable(val,id){
  document.querySelectorAll(`#${id} tbody tr`).forEach(r=>r.style.display=r.textContent.toLowerCase().includes(val.toLowerCase())?'':'none');
}

function handleSearch(v){
  const activePage = document.querySelector('.page.active');
  if(!activePage) return;
  const p=activePage.id.replace('page-','');
  if(p==='courses')filterTable(v,'courseTable');
  if(p==='orders') {
      const orderTable = activePage.querySelector('table');
      if(orderTable) {
          const rows = orderTable.querySelectorAll('tbody tr');
          rows.forEach(r=>r.style.display=r.textContent.toLowerCase().includes(v.toLowerCase())?'':'none');
      }
  }
}

function addLesson(btn) {
  const lessonList = btn.previousElementSibling;
  const d = document.createElement('div');
  d.style.cssText = 'display:flex;gap:8px;align-items:center;';
  d.innerHTML = `<input class="form-control course-lesson-input" placeholder="Lesson/Detail" style="flex:1; font-size:13px;"/><button type="button" class="act-btn danger" style="width:24px;height:24px;font-size:12px;" onclick="this.parentElement.remove()">✕</button>`;
  lessonList.appendChild(d);
}

function addSection(){
  const list=document.getElementById('sectionList');
  const d=document.createElement('div');
  d.className = 'section-block';
  d.style.cssText='background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #eee;';
  d.innerHTML=`
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
      <input class="form-control course-section-input" placeholder="Section Title" style="flex:1; font-weight:bold;"/>
      <button type="button" class="act-btn danger" onclick="this.parentElement.parentElement.remove()">🗑</button>
    </div>
    <div class="lesson-list" style="display:flex;flex-direction:column;gap:8px;padding-left:20px;margin-bottom:10px;">
      <div style="display:flex;gap:8px;align-items:center;">
        <input class="form-control course-lesson-input" placeholder="Lesson/Detail" style="flex:1; font-size:13px;"/>
        <button type="button" class="act-btn danger" style="width:24px;height:24px;font-size:12px;" onclick="this.parentElement.remove()">✕</button>
      </div>
    </div>
    <button type="button" class="btn btn-ghost btn-sm" style="font-size:12px; padding:4px 8px; margin-left:20px;" onclick="addLesson(this)">＋ Add Lesson</button>
  `;
  list.appendChild(d);
}

function resetCourseForm() {
    document.getElementById('courseId').value = '';
    document.getElementById('courseTitle').value = '';
    document.getElementById('courseDesc').value = '';
    document.getElementById('courseInstructor').value = '';
    document.getElementById('courseLessons').value = '10';
    document.getElementById('courseHours').value = '20';
    document.getElementById('courseCategory').selectedIndex = 0;
    document.getElementById('coursePrice').value = '';
    document.getElementById('courseImage').value = '';
    document.getElementById('courseModalTitle').textContent = '✦ Add New Course';
    document.getElementById('sectionList').innerHTML = `
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
    `;
}

function openAddCourseModal() {
    resetCourseForm();
    openModal('courseModal');
}

function editCourse(id) {
    const course = courses.find(c => c.id == id);
    if (!course) return;
    
    resetCourseForm();
    document.getElementById('courseId').value = course.id;
    document.getElementById('courseTitle').value = course.title;
    document.getElementById('courseDesc').value = course.description || '';
    document.getElementById('courseInstructor').value = course.instructor || '';
    document.getElementById('courseLessons').value = course.lessons || 10;
    document.getElementById('courseHours').value = course.hours || 20;
    document.getElementById('courseCategory').value = course.category_id || '';
    document.getElementById('coursePrice').value = course.raw_price || course.price.replace(/[^0-9]/g, '');
    document.getElementById('courseModalTitle').textContent = '✦ Edit Course';
    
    if (course.content_json) {
        try {
            const sections = JSON.parse(course.content_json);
            const list = document.getElementById('sectionList');
            list.innerHTML = '';
            if (sections && sections.length > 0) {
                sections.forEach(sec => {
                    const d = document.createElement('div');
                    d.className = 'section-block';
                    d.style.cssText = 'background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #eee;';
                    
                    let lessonsHtml = '';
                    if (sec.lessons && sec.lessons.length > 0) {
                        sec.lessons.forEach(lesson => {
                            lessonsHtml += `
                            <div style="display:flex;gap:8px;align-items:center;">
                              <input class="form-control course-lesson-input" value="${lesson}" placeholder="Lesson/Detail" style="flex:1; font-size:13px;"/>
                              <button type="button" class="act-btn danger" style="width:24px;height:24px;font-size:12px;" onclick="this.parentElement.remove()">✕</button>
                            </div>`;
                        });
                    } else {
                        lessonsHtml = `
                            <div style="display:flex;gap:8px;align-items:center;">
                              <input class="form-control course-lesson-input" placeholder="Lesson/Detail" style="flex:1; font-size:13px;"/>
                              <button type="button" class="act-btn danger" style="width:24px;height:24px;font-size:12px;" onclick="this.parentElement.remove()">✕</button>
                            </div>`;
                    }

                    d.innerHTML = `
                      <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
                        <input class="form-control course-section-input" value="${sec.section}" placeholder="Section Title" style="flex:1; font-weight:bold;"/>
                        <button type="button" class="act-btn danger" onclick="this.parentElement.parentElement.remove()">🗑</button>
                      </div>
                      <div class="lesson-list" style="display:flex;flex-direction:column;gap:8px;padding-left:20px;margin-bottom:10px;">
                        ${lessonsHtml}
                      </div>
                      <button type="button" class="btn btn-ghost btn-sm" style="font-size:12px; padding:4px 8px; margin-left:20px;" onclick="addLesson(this)">＋ Add Lesson</button>
                    `;
                    list.appendChild(d);
                });
            } else {
                resetCourseForm();
                document.getElementById('courseModalTitle').textContent = '✦ Edit Course';
            }
        } catch(e) {
            console.error('Error parsing sections', e);
        }
    }
    
    openModal('courseModal');
}

async function deleteCourse(id) {
    if (!confirm('ยืนยันการลบคอร์สนี้? ข้อมูลจะไม่สามารถกู้คืนได้')) return;
    try {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fd.append('csrf_token', typeof csrfToken !== 'undefined' ? csrfToken : '');
        
        const res = await fetch('api_courses.php', { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd 
        });
        const json = await res.json();
        
        if (json.success) {
            showToast('✅', 'ลบคอร์สสำเร็จ!');
            fetchCourses();
        } else {
            showToast('❌', 'Error: ' + json.error);
        }
    } catch(err) {
        showToast('❌', 'Request failed');
    }
}

async function saveCourse() {
    const id = document.getElementById('courseId').value;
    const title = document.getElementById('courseTitle').value;
    const description = document.getElementById('courseDesc').value;
    const category = document.getElementById('courseCategory').value;
    const price = document.getElementById('coursePrice').value;
    const instructor = document.getElementById('courseInstructor').value;
    const lessons = document.getElementById('courseLessons').value;
    const hours = document.getElementById('courseHours').value;
    const categoryId = document.getElementById('courseCategory').value;
    const imageFile = document.getElementById('courseImage').files[0];
    
    if (!title || !price) {
        showToast('⚠️', 'Please fill required fields (Title, Price)');
        return;
    }
    
    const sectionBlocks = document.querySelectorAll('.section-block');
    const sections = [];
    sectionBlocks.forEach(block => {
        const secInput = block.querySelector('.course-section-input');
        if (secInput && secInput.value.trim() !== '') {
            const lessonInputs = block.querySelectorAll('.course-lesson-input');
            const lessons = [];
            lessonInputs.forEach(lInput => {
                if (lInput.value.trim() !== '') lessons.push(lInput.value.trim());
            });
            sections.push({ section: secInput.value.trim(), lessons: lessons });
        }
    });
    
    const fd = new FormData();
    fd.append('action', id ? 'edit' : 'add');
    if (id) fd.append('id', id);
    fd.append('title', title);
    fd.append('description', description);
    fd.append('category_id', categoryId);
    fd.append('price', price);
    fd.append('instructor', instructor);
    fd.append('lessons', lessons);
    fd.append('hours', hours);
    fd.append('content_json', JSON.stringify(sections));
    
    if (imageFile) fd.append('image', imageFile);
    fd.append('csrf_token', typeof csrfToken !== 'undefined' ? csrfToken : '');
    
    try {
        const res = await fetch('api_courses.php', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd
        });
        const json = await res.json();
        
        if (json.success) {
            showToast('✅', id ? 'Course updated successfully!' : 'Course added successfully!');
            closeModal('courseModal');
            fetchCourses();
        } else {
            showToast('❌', 'Error saving course: ' + json.error);
        }
    } catch (err) {
        showToast('❌', 'Request failed');
    }
}

// Initial fetch
document.addEventListener('DOMContentLoaded', () => {
    fetchCourses();
    fetchUsers();
    fetchOrders();
    fetchInvoices();
    fetchCategories();
});

/* ── NOTIFICATIONS LOGIC ─────────────────────────────────────────── */
async function fetchNotifications(page = 1) {
    try {
        const res = await fetch(`${API_NOTI}?action=admin_list&page=${page}`);
        const json = await res.json();
        if (json.success) {
            notifications = json.data;
            renderNotifications(page, json.total);
        }
    } catch (err) {
        console.error('Failed to fetch notifications:', err);
    }
}

async function fetchNotiStats() {
    try {
        const res = await fetch(`${API_NOTI}?action=admin_stats`);
        const json = await res.json();
        if (json.success) {
            const s = json.data;
            document.getElementById('ns-total').textContent = s.total;
            document.getElementById('ns-active').textContent = s.active;
            document.getElementById('ns-unread').textContent = s.unread;
        }
    } catch (err) {}
}

function renderNotifications(page, total) {
    const tbody = document.getElementById('notiTableBody');
    if (!notifications.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-3)">No notifications found.</td></tr>`;
        return;
    }
    const priorityBadge = { low: 'badge-green', medium: 'badge-yellow', high: 'badge-red' };
    
    tbody.innerHTML = notifications.map(n => `
        <tr>
            <td class="td-primary">
                <div style="font-weight:600">${n.title}</div>
                <div style="font-size:12px;color:var(--text-3);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${n.message}</div>
            </td>
            <td><span style="font-size:12px;text-transform:capitalize">${n.target_audience.replace('_',' ')}</span></td>
            <td><span class="badge badge-purple">${n.type}</span></td>
            <td><span class="badge ${priorityBadge[n.priority] || 'badge-blue'}">${n.status}</span></td>
            <td style="font-family:'JetBrains Mono';font-size:11px">${n.created_at}</td>
            <td>
                <div class="actions">
                    <button class="act-btn danger" onclick="deleteNoti(${n.id})">🗑</button>
                </div>
            </td>
        </tr>
    `).join('');
    renderPagination('notiPagination', total, page, 'fetchNotifications', 20);
}

function openNotiModal() {
    // Reset form
    document.getElementById('notiTitle').value = '';
    document.getElementById('notiMessage').value = '';
    document.getElementById('notiType').value = 'default';
    document.getElementById('notiPriority').value = 'medium';
    document.querySelector('input[name="audience"][value="all"]').checked = true;
    selectedUsers = [];
    renderSelectedUsers();
    toggleAudienceUI();
    
    // Fill course select
    const cSel = document.getElementById('notiCourseId');
    cSel.innerHTML = courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('');
    
    document.getElementById('audienceGroup').style.display = 'block';
    document.getElementById('notiModalTitle').textContent = '✦ Create Announcement';
    openModal('notiModal');
}

function toggleAudienceUI() {
    const aud = document.querySelector('input[name="audience"]:checked').value;
    document.getElementById('courseSelectRow').style.display = aud === 'course_buyers' ? 'block' : 'none';
    document.getElementById('userSearchRow').style.display = aud === 'custom' ? 'block' : 'none';
}

async function searchUsersForNoti(q) {
    if (q.length < 2) {
        document.getElementById('userSearchResults').style.display = 'none';
        return;
    }
    try {
        const res = await fetch(`${API_NOTI}?action=search_users&q=${encodeURIComponent(q)}`);
        const json = await res.json();
        if (json.success) {
            const results = json.data;
            const container = document.getElementById('userSearchResults');
            if (results.length === 0) {
                container.innerHTML = `<div class="user-res-item" style="color:var(--text-3)">No users found</div>`;
            } else {
                container.innerHTML = results.map(u => `
                    <div class="user-res-item" onclick="selectUserForNoti(${u.id}, '${u.username.replace(/'/g, "\\'")}', '${u.email}')">
                        <strong>${u.username}</strong>
                        <div style="font-size:11px;color:var(--text-3)">${u.email}</div>
                    </div>
                `).join('');
            }
            container.style.display = 'block';
        }
    } catch (err) {}
}

function selectUserForNoti(id, name, email) {
    if (!selectedUsers.find(u => u.id === id)) {
        selectedUsers.push({ id, name, email });
        renderSelectedUsers();
    }
    document.getElementById('userSearchResults').style.display = 'none';
    document.getElementById('notiUserSearch').value = '';
}

function removeUserFromNoti(id) {
    selectedUsers = selectedUsers.filter(u => u.id !== id);
    renderSelectedUsers();
}

function renderSelectedUsers() {
    const container = document.getElementById('selectedUsers');
    container.innerHTML = selectedUsers.map(u => `
        <div class="user-tag">
            <span>${u.name}</span>
            <button onclick="removeUserFromNoti(${u.id})">✕</button>
        </div>
    `).join('');
}

async function sendNotification() {
    const title = document.getElementById('notiTitle').value;
    const message = document.getElementById('notiMessage').value;
    const type = document.getElementById('notiType').value;
    const priority = document.getElementById('notiPriority').value;
    const audience = document.querySelector('input[name="audience"]:checked').value;
    
    if (!title || !message) {
        showToast('⚠️', 'Please enter title and message');
        return;
    }

    const fd = new FormData();
    fd.append('action', 'create');
    fd.append('title', title);
    fd.append('message', message);
    fd.append('type', type);
    fd.append('priority', priority);
    fd.append('target_audience', audience);
    fd.append('csrf_token', typeof csrfToken !== 'undefined' ? csrfToken : '');

    if (audience === 'course_buyers') {
        fd.append('custom_user_ids', document.getElementById('notiCourseId').value);
    } else if (audience === 'custom') {
        if (selectedUsers.length === 0) {
            showToast('⚠️', 'Please select at least one user');
            return;
        }
        fd.append('custom_user_ids', selectedUsers.map(u => u.id).join(','));
    }

    const btn = document.getElementById('btnSendNoti');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    try {
        const res = await fetch(API_NOTI, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd 
        });
        const json = await res.json();
        if (json.success) {
            showToast('✅', 'Notification sent successfully!');
            closeModal('notiModal');
            if (document.getElementById('page-notifications').classList.contains('active')) {
                fetchNotifications();
                fetchNotiStats();
            }
        } else {
            showToast('❌', json.error || 'Failed to send');
        }
    } catch (err) {
        showToast('❌', 'Connection error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Send Notification';
    }
}

async function deleteNoti(id) {
    if (!confirm('Delete this notification?')) return;
    try {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fd.append('csrf_token', typeof csrfToken !== 'undefined' ? csrfToken : '');
        const res = await fetch(API_NOTI, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': typeof csrfToken !== 'undefined' ? csrfToken : '' },
            body: fd 
        });
        const json = await res.json();
        if (json.success) {
            showToast('✅', 'Deleted');
            fetchNotifications();
            fetchNotiStats();
        }
    } catch (err) {}
}

function openSendToUser(userId, userName) {
    openNotiModal();
    document.getElementById('notiModalTitle').textContent = `✦ Message to ${userName}`;
    document.querySelector('input[name="audience"][value="custom"]').checked = true;
    selectedUsers = [{ id: userId, name: userName, email: '' }];
    renderSelectedUsers();
    toggleAudienceUI();
    document.getElementById('audienceGroup').style.display = 'none'; // Hide selection to focus on this user
}

function openSendToOrder(orderId, customerName) {
    // We need to find the user ID for this order
    const order = orders.find(o => o.id === orderId);
    if (!order) return;
    
    // In our api_orders.php or api_notifications.php, we might need a way to get user_id from order
    // But since orders has student email, we can find user in `users` array if loaded
    const user = users.find(u => u.email === order.email);
    if (user) {
        openSendToUser(user.id, customerName);
        document.getElementById('notiTitle').value = `Update for Order ${order.order_no}`;
        document.getElementById('notiType').value = 'order';
    } else {
        showToast('⚠️', 'User account not found for this email');
    }
}
