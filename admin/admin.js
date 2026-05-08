let courses = [];

async function fetchCourses() {
    try {
        const res = await fetch('/admin/api_courses.php');
        const json = await res.json();
        if (json.success) {
            courses = json.data;
            renderCourses();
        } else {
            console.error("Error fetching courses:", json.error);
        }
    } catch (err) {
        console.error("Failed to fetch courses:", err);
    }
}
let users = [];
let orders = [];

async function fetchOrders() {
    try {
        const res = await fetch('/admin/api_orders.php');
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
    if (!confirm('ยืนยันการอนุมัติและส่งคอร์สเรียนนี้?')) return;
    try {
        const fd = new URLSearchParams();
        fd.append('action', 'update_status');
        fd.append('id', id);
        fd.append('status', status);
        const res = await fetch('/admin/api_orders.php', {
            method: 'POST',
            body: fd
        });
        const json = await res.json();
        if (json.success) {
            showToast('✅', 'อนุมัติคำสั่งซื้อเรียบร้อยแล้ว!');
            fetchOrders(); // Refresh table
            // Optionally reload the page to update stats
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('❌', 'Error updating status: ' + json.error);
        }
    } catch(err) {
        showToast('❌', 'Request failed');
    }
}

async function fetchUsers() {
    try {
        const res = await fetch('/admin/api_users.php');
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
      <td class="td-primary">
        <div>${o.student}</div>
        <div style="font-size:12px; color:var(--text-3);">LINE: ${o.line_id || '-'}</div>
      </td>
      <td>${o.course}</td>
      <td style="font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600">${o.amount}</td>
      <td><span class="badge ${sc[o.status]}">${o.status.charAt(0).toUpperCase()+o.status.slice(1)}</span></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.77rem">${o.date}</td>
      <td><div class="actions">
        ${o.slip ? `<a href="/${o.slip}" target="_blank" class="act-btn" style="text-decoration:none;" title="ดูสลิปโอนเงิน">📄</a>` : ''}
        ${o.status === 'pending' ? `<button class="btn btn-sm btn-primary" onclick="updateOrderStatus(${o.id}, 'completed')">✅ อนุมัติ</button>` : ''}
        <button class="act-btn" onclick="viewOrderDetails(${o.id})" title="ดูรายละเอียด">👁</button>
      </div></td>
    </tr>`).join('');
  renderPagination('orderPagination',total,page,'renderOrders');
}

function viewOrderDetails(id) {
  const o = orders.find(x => x.id === id);
  if (!o) return;
  
  const itemsHtml = o.items.map(item => `
    <div style="padding: 10px; border: 1px solid #eee; border-radius: 4px; margin-bottom: 8px;">
      <div style="font-weight: 600;">${item.name}</div>
      <div style="font-size: 12px; color: var(--text-3);">${item.category || ''}</div>
      <div style="font-size: 13px; margin-top: 4px; color: var(--green); font-family: 'JetBrains Mono', monospace;">฿${Number(item.price).toLocaleString('th-TH')}</div>
    </div>
  `).join('');

  const html = `
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
      <div>
        <h4 style="margin-bottom: 10px; color: var(--accent-1);"><i class="ti ti-user"></i> ข้อมูลลูกค้า</h4>
        <div style="font-size: 14px; line-height: 1.6;">
          <div><strong>ชื่อ-นามสกุล:</strong> ${o.student}</div>
          <div><strong>อีเมล:</strong> ${o.email}</div>
          <div><strong>เบอร์โทร:</strong> ${o.phone || '-'}</div>
          <div><strong>LINE ID:</strong> ${o.line_id || '-'}</div>
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 10px; color: var(--accent-1);"><i class="ti ti-receipt"></i> ข้อมูลการสั่งซื้อ</h4>
        <div style="font-size: 14px; line-height: 1.6;">
          <div><strong>หมายเลข:</strong> <span style="font-family:'JetBrains Mono',monospace;">${o.order_no}</span></div>
          <div><strong>วันที่:</strong> ${o.date}</div>
          <div><strong>วิธีชำระเงิน:</strong> ${o.payment_method === 'bank' ? 'โอนเงินผ่านธนาคาร' : (o.payment_method === 'qr' ? 'QR PromptPay' : o.payment_method)}</div>
          <div><strong>ยอดรวม:</strong> <span style="color:var(--green); font-weight:bold; font-family:'JetBrains Mono',monospace;">฿${Number(o.raw_amount).toLocaleString('th-TH')}</span></div>
          <div><strong>สถานะ:</strong> ${o.status.toUpperCase()}</div>
        </div>
      </div>
    </div>
    
    <div>
      <h4 style="margin-bottom: 10px; color: var(--accent-1);"><i class="ti ti-books"></i> รายการคอร์สเรียน</h4>
      ${itemsHtml}
    </div>
    
    ${o.slip ? `
    <div style="margin-top: 20px; text-align: center;">
      <a href="/${o.slip}" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="ti ti-external-link"></i> ดูรูปสลิปเต็ม
      </a>
    </div>
    ` : ''}
  `;

  document.getElementById('orderModalBody').innerHTML = html;
  document.getElementById('orderModalTitle').textContent = `✦ รายละเอียดคำสั่งซื้อ: ${o.order_no}`;
  openModal('orderModal');
}

function navigate(page,el){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById('page-'+page).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  if(el)el.classList.add('active');
  const titles={dashboard:'Dashboard',courses:'Course Management',users:'User Management',orders:'Orders & Enrollments',settings:'Settings'};
  document.getElementById('topbarTitle').textContent=titles[page]||page;
}

function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open')}));

function showToast(icon,msg){
  const c=document.getElementById('toastContainer');
  const t=document.createElement('div');t.className='toast';
  t.innerHTML=`<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span><button class="toast-close" onclick="this.parentElement.remove()">✕</button>`;
  c.appendChild(t);setTimeout(()=>t.remove(),3500);
}

function filterTable(val,id){
  document.querySelectorAll(`#${id} tbody tr`).forEach(r=>r.style.display=r.textContent.toLowerCase().includes(val.toLowerCase())?'':'none');
}
function handleSearch(v){
  const p=document.querySelector('.page.active').id.replace('page-','');
  if(p==='courses')filterTable(v,'courseTable');
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
    
    // Render sections
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
                // Default empty section
                resetCourseForm(); // will set the default empty block
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
        
        const res = await fetch('/admin/api_courses.php', { method: 'POST', body: fd });
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
    
    // Gather sections
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
        const res = await fetch('/admin/api_courses.php', {
            method: 'POST',
            body: fd
        });
        const json = await res.json();
        
        if (json.success) {
            showToast('✅', id ? 'Course updated successfully!' : 'Course added successfully!');
            closeModal('courseModal');
            fetchCourses(); // Reload list
        } else {
            showToast('❌', 'Error saving course: ' + json.error);
        }
    } catch (err) {
        showToast('❌', 'Request failed');
    }
}

fetchCourses();fetchUsers();fetchOrders();
