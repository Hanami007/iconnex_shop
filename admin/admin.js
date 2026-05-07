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
const users=[
  {name:'Maria Chen',email:'maria@example.com',role:'Student',enrolled:5,status:'active',joined:'Jan 12, 2024'},
  {name:'James Lee',email:'james@example.com',role:'Instructor',enrolled:0,status:'active',joined:'Mar 4, 2023'},
  {name:'Alex Johnson',email:'alex@example.com',role:'Student',enrolled:3,status:'active',joined:'Feb 20, 2024'},
  {name:'Sarah Park',email:'sarah@example.com',role:'Instructor',enrolled:0,status:'pending',joined:'May 1, 2025'},
  {name:'Daniel Wu',email:'daniel@example.com',role:'Student',enrolled:8,status:'active',joined:'Oct 15, 2023'},
  {name:'Emma Wilson',email:'emma@example.com',role:'Admin',enrolled:0,status:'active',joined:'Jun 1, 2022'},
  {name:'Liam Brown',email:'liam@example.com',role:'Student',enrolled:2,status:'suspended',joined:'Dec 5, 2023'},
  {name:'Sophia Davis',email:'sophia@example.com',role:'Student',enrolled:6,status:'active',joined:'Aug 18, 2023'},
];
const orders=[
  {id:'#4821',student:'Alex Johnson',course:'UI/UX Masterclass',amount:'$59',status:'completed',date:'May 4, 2025'},
  {id:'#4820',student:'Maria Chen',course:'React Advanced',amount:'$99',status:'completed',date:'May 3, 2025'},
  {id:'#4819',student:'Daniel Wu',course:'Python DS & ML',amount:'$79',status:'pending',date:'May 3, 2025'},
  {id:'#4818',student:'Liam Brown',course:'Web Dev Bootcamp',amount:'$89',status:'refunded',date:'May 2, 2025'},
  {id:'#4817',student:'Sophia Davis',course:'AWS Cloud',amount:'$49',status:'completed',date:'May 1, 2025'},
  {id:'#4816',student:'Emma Wilson',course:'Flutter Dev',amount:'$69',status:'completed',date:'Apr 30, 2025'},
  {id:'#4815',student:'James Lee',course:'Cybersecurity',amount:'$69',status:'pending',date:'Apr 29, 2025'},
];

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
      <td>${c.students.toLocaleString()}</td>
      <td>${c.status==='published'?'<span class="badge badge-green">Published</span>':'<span class="badge badge-yellow">Draft</span>'}</td>
      <td><div class="actions">
        <button class="act-btn" onclick="showToast('✏️','Course editor opened')">✏️</button>
        <button class="act-btn">👁</button>
        <button class="act-btn danger" onclick="showToast('🗑','Course deleted')">🗑</button>
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
  const sc={completed:'badge-green',pending:'badge-yellow',refunded:'badge-red'};
  document.getElementById('orderTableBody').innerHTML=items.map(o=>`
    <tr>
      <td style="font-family:'JetBrains Mono',monospace;font-weight:600;color:var(--accent-2)">${o.id}</td>
      <td class="td-primary">${o.student}</td>
      <td>${o.course}</td>
      <td style="font-family:'JetBrains Mono',monospace;color:var(--green);font-weight:600">${o.amount}</td>
      <td><span class="badge ${sc[o.status]}">${o.status.charAt(0).toUpperCase()+o.status.slice(1)}</span></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:.77rem">${o.date}</td>
      <td><div class="actions">
        <button class="act-btn">👁</button>
        <button class="act-btn" onclick="showToast('📧','Receipt sent!')">📧</button>
      </div></td>
    </tr>`).join('');
  renderPagination('orderPagination',total,page,'renderOrders');
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

function addSection(){
  const list=document.getElementById('sectionList');
  const i=list.children.length+1;
  const d=document.createElement('div');
  d.style.cssText='display:flex;gap:8px;align-items:center';
  d.innerHTML=`<input class="form-control" placeholder="Section ${i}: " style="flex:1"/><button class="act-btn danger" onclick="this.parentElement.remove()">🗑</button>`;
  list.appendChild(d);
}

async function saveCourse() {
    const title = document.querySelectorAll('#courseModal .form-control')[0].value;
    const description = document.querySelector('#courseModal textarea').value;
    const category = document.querySelector('#courseModal select').value;
    const price = document.querySelector('#courseModal input[type="number"]').value;
    
    if (!title || !price) {
        showToast('⚠️', 'Please fill required fields');
        return;
    }
    
    try {
        const res = await fetch('/admin/api_courses.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({title, description, category, price})
        });
        const json = await res.json();
        
        if (json.success) {
            showToast('✅', 'Course saved successfully!');
            closeModal('courseModal');
            fetchCourses(); // Reload list
            
            // clear form
            document.querySelectorAll('#courseModal .form-control')[0].value = '';
            document.querySelector('#courseModal textarea').value = '';
            document.querySelector('#courseModal input[type="number"]').value = '';
        } else {
            showToast('❌', 'Error saving course: ' + json.error);
        }
    } catch (err) {
        showToast('❌', 'Request failed');
    }
}

fetchCourses();renderUsers();renderOrders();
