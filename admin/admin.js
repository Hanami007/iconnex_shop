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
        <button class="act-btn">👁</button>
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
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h4 style="margin-bottom:15px; color:var(--accent-2)">👤 Customer Information</h4>
                <p style="margin-bottom:8px;"><strong>Name:</strong> ${order.student}</p>
                <p style="margin-bottom:8px;"><strong>Email:</strong> ${order.email}</p>
                <p style="margin-bottom:8px;"><strong>Phone:</strong> ${order.phone || '-'}</p>
                <p style="margin-bottom:8px;"><strong>Line ID:</strong> ${order.line_id || '-'}</p>
                <p style="margin-bottom:8px;"><strong>Date:</strong> ${order.date}</p>
                <p style="margin-bottom:15px;"><strong>Status:</strong> <span class="badge ${order.status === 'completed' ? 'badge-green' : (order.status === 'pending' ? 'badge-yellow' : 'badge-red')}">${order.status.toUpperCase()}</span></p>

                <h4 style="margin:20px 0 10px; color:var(--accent-2)">🛒 Order Items</h4>
                <div style="background:#f8f9fa; padding:15px; border-radius:10px;">
                    ${itemsHtml}
                    <div style="text-align:right; font-weight:bold; font-size:1.1rem; margin-top:10px; color:var(--green);">
                        Total: ${order.amount}
                    </div>
                </div>
                
                <div style="margin-top:25px; display:flex; gap:10px;">
                    ${order.status === 'pending' ? `
                        <button class="btn btn-primary" style="flex:1" onclick="updateOrderStatus(${order.id}, 'completed')">✅ Approve Order</button>
                        <button class="btn btn-ghost" style="flex:1; border-color:var(--red); color:var(--red)" onclick="updateOrderStatus(${order.id}, 'cancelled')">❌ Reject</button>
                    ` : ''}
                </div>
            </div>
            <div style="text-align:center;">
                <h4 style="margin-bottom:15px; color:var(--accent-2)">🖼 Payment Proof (Slip)</h4>
                ${slipHtml}
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
  
  const titles={dashboard:'Dashboard',courses:'Course Management',users:'User Management',orders:'Orders & Enrollments',settings:'Settings'};
  const titleEl = document.getElementById('topbarTitle');
  if(titleEl) titleEl.textContent=titles[page]||page;
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
    document.getElementById('courseCategory').value = course.category;
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
        
        const res = await fetch('api_courses.php', { method: 'POST', body: fd });
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
    fd.append('category', category);
    fd.append('price', price);
    fd.append('instructor', instructor);
    fd.append('lessons', lessons);
    fd.append('hours', hours);
    fd.append('content_json', JSON.stringify(sections));
    
    if (imageFile) fd.append('image', imageFile);
    
    try {
        const res = await fetch('api_courses.php', {
            method: 'POST',
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
});
