// Navigate to detail page
function goToDetail(courseId) {
  window.location.href = "dic_product.php?id=" + courseId;
}

// Tab: portfolio
function switchPortfolioTab(btn, type) {
  document.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"));
  btn.classList.add("active");
  const photo = document.getElementById('portfolio-photo-content');
  const video = document.getElementById('portfolio-video-content');
  if (type === 'video') {
    if(photo) photo.style.display = 'none';
    if(video) video.style.display = 'block';
  } else {
    if(photo) photo.style.display = 'block';
    if(video) video.style.display = 'none';
  }
}

// Category filter & Pagination logic
let showingAll = false;
function setFilter(btn, category) {
  if (btn) {
    document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }

  showingAll = false;
  const seeMoreBtn = document.getElementById('see-more-container');
  const cards = document.querySelectorAll(".course-card");
  
  let count = 0;
  const limit = 3;
  let totalMatch = 0;

  cards.forEach((card) => {
    const cardCat = card.getAttribute('data-category');
    const isMatch = category === 'all' || cardCat === category;
    
    if (isMatch) {
      totalMatch++;
      if (category === 'all') {
        if (count < limit) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
        count++;
      } else {
        card.style.display = "flex";
      }
    } else {
      card.style.display = "none";
    }
  });

  if (seeMoreBtn) {
    if (category === 'all' && totalMatch > limit) {
      seeMoreBtn.style.display = 'flex';
    } else {
      seeMoreBtn.style.display = 'none';
    }
  }
}

function showAllCourses() {
  showingAll = true;
  const cards = document.querySelectorAll(".course-card");
  cards.forEach(card => {
    card.style.display = "flex";
  });
  const seeMoreBtn = document.getElementById('see-more-container');
  if (seeMoreBtn) seeMoreBtn.style.display = 'none';
}

// FAQ accordion
document.querySelectorAll(".faq-item").forEach((item) => {
  const question = item.querySelector(".faq-question");
  if (question) {
    question.addEventListener("click", () => {
      const isOpen = item.classList.contains("open");
      document.querySelectorAll(".faq-item").forEach((i) => i.classList.remove("open"));
      if (!isOpen) item.classList.add("open");
    });
  }
});

/* 🛒 PREMIUM CART LOGIC (Sync with Checkout) */
async function addToCart(id) {
  try {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('course_id', id);
    
    const res = await fetch('api/cart.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if (data.success) {
      updateCartBadge(data.count);
      showToast("เพิ่มลงตะกร้าเรียบร้อยแล้ว");
      return true;
    } else {
      showToast(data.message || "เกิดข้อผิดพลาด");
      return false;
    }
  } catch (err) {
    console.error(err);
    showToast("เชื่อมต่อเซิร์ฟเวอร์ล้มเหลว");
    return false;
  }
}

function updateCartBadge(count) {
  const badges = document.querySelectorAll('#cart-count');
  badges.forEach(badge => {
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  });
}

async function openCartModal(e) {
  if (e) e.preventDefault();
  let modal = document.getElementById('cart-modal');
  if (!modal) { createCartModal(); modal = document.getElementById('cart-modal'); }
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  await renderCart(true);
}

function closeCartModal() {
  const modal = document.getElementById('cart-modal');
  if (modal) modal.style.display = 'none';
  document.body.style.overflow = '';
}

async function renderCart(showLoading = false) {
  const list = document.getElementById('cart-items-list');
  const totalEl = document.getElementById('cart-total-price');
  const countEl = document.getElementById('cart-subtitle-count');
  if (!list) return;

  if (showLoading) {
    list.innerHTML = '<div style="text-align:center; padding: 40px; color: var(--gold-soft);">กำลังโหลดข้อมูล...</div>';
  }

  const formData = new FormData();
  formData.append('action', 'get_cart');
  const res = await fetch('api/cart.php', { method: 'POST', body: formData });
  const data = await res.json();

  if (data.success && data.items.length > 0) {
    countEl.textContent = `คุณมี ${data.items.length} รายการในตะกร้า`;
    list.innerHTML = '';
    data.items.forEach(item => {
      const itemEl = document.createElement('div');
      itemEl.id = `cart-item-${item.id}`;
      itemEl.className = 'cart-item';
      itemEl.style = "display: grid; grid-template-columns: 120px 1fr 30px; gap: 20px; margin-bottom: 25px; align-items: center; padding-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.3s;";
      
      const imgPath = item.image.startsWith('uploads/') ? '/' + item.image : '/src/assets/img/' + item.image;
      
      itemEl.innerHTML = `
        <div style="width: 120px; height: 75px; border-radius: 12px; overflow: hidden; border: 1px solid var(--navy-border);">
          <img src="${imgPath}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 5px;">
          <div style="font-weight: 700; color: #fff; font-size: 1.05rem;">${item.name}</div>
          <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-muted);">
             <div style="width: 18px; height: 18px; background: var(--navy-light); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-user" style="font-size: 0.6rem;"></i></div>
             ${item.instructor}
          </div>
          <div style="display: flex; align-items: baseline; gap: 10px; margin-top: 5px;">
            <span style="color: var(--gold); font-weight: 800; font-size: 1.1rem;">฿${item.price.toLocaleString()}</span>
            ${item.old_price > item.price ? `<span style="color: var(--text-subtle); text-decoration: line-through; font-size: 0.85rem;">฿${item.old_price.toLocaleString()}</span>` : ''}
          </div>
        </div>
        <button onclick="silentRemove(${item.id})" style="background: none; border: none; color: var(--text-subtle); cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#ff4757'" onmouseout="this.style.color='var(--text-subtle)'">
          <i class="far fa-trash-alt" style="font-size: 1.1rem;"></i>
        </button>
      `;
      list.appendChild(itemEl);
    });
    totalEl.textContent = `฿${data.total.toLocaleString()}`;
  } else {
    countEl.textContent = 'ตะกร้าของคุณยังว่างอยู่';
    list.innerHTML = '<div style="text-align:center; padding: 60px; color: var(--text-muted); font-size: 0.95rem;">ยังไม่มีสินค้าในตะกร้าของคุณ</div>';
    totalEl.textContent = '฿0';
  }
}

async function silentRemove(id) {
  const itemEl = document.getElementById(`cart-item-${id}`);
  if (itemEl) { itemEl.style.opacity = '0'; itemEl.style.transform = 'translateX(20px)'; }
  const formData = new FormData();
  formData.append('action', 'remove');
  formData.append('course_id', id);
  const res = await fetch('api/cart.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    updateCartBadge(data.count);
    await renderCart(false);
  }
}

async function goToPayment() {
  // Sync PHP Cart with LocalStorage for pay2.1
  const formData = new FormData();
  formData.append('action', 'get_cart');
  const res = await fetch('api/cart.php', { method: 'POST', body: formData });
  const data = await res.json();
  
  if (data.success && data.items.length > 0) {
    localStorage.setItem('checkoutCart', JSON.stringify(data.items));
    window.location.href = 'pay2.1/index.php';
  } else {
    showToast("ไม่มีสินค้าในตะกร้า");
  }
}

function createCartModal() {
  const modal = document.createElement('div');
  modal.id = 'cart-modal';
  modal.style = `
    position: fixed; inset: 0; background: rgba(7, 12, 22, 0.85); backdrop-filter: blur(15px);
    z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px;
    font-family: 'Prompt', sans-serif;
  `;
  modal.innerHTML = `
    <div style="background: var(--navy-card); width: 100%; max-width: 580px; border-radius: 32px; border: 1px solid var(--navy-border2); overflow: hidden; box-shadow: 0 50px 120px rgba(0,0,0,0.8);">
      <div style="padding: 35px 40px 25px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <h2 style="margin: 0 0 8px; color: #fff; font-family: var(--font-display); font-weight: 800; font-size: 1.6rem;">ตะกร้าสินค้าของคุณ</h2>
          <p id="cart-subtitle-count" style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">กำลังคำนวณรายการ...</p>
        </div>
        <button onclick="closeCartModal()" style="background: rgba(255,255,255,0.05); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">&times;</button>
      </div>
      <div id="cart-items-list" style="padding: 10px 40px; max-height: 420px; overflow-y: auto;"></div>
      <div style="padding: 35px 40px 45px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-top: 1px solid var(--navy-border); padding-top: 30px;">
          <span style="color: var(--text-muted); font-weight: 600; font-size: 1.1rem;">ยอดชำระทั้งหมด</span>
          <span id="cart-total-price" style="font-size: 2.2rem; font-weight: 800; color: #fff; font-family: var(--font-display);">฿0</span>
        </div>
        <button onclick="goToPayment()" style="
          width: 100%; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-rich) 100%); 
          color: var(--navy-deep); border: none; padding: 20px; border-radius: 18px; 
          font-weight: 800; font-size: 1.2rem; cursor: pointer; transition: 0.4s;
          box-shadow: 0 15px 35px rgba(201, 168, 76, 0.25);
        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 45px rgba(201, 168, 76, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 35px rgba(201, 168, 76, 0.25)';">
          ดำเนินการชำระเงิน
        </button>
      </div>
    </div>
  `;
  modal.onclick = (e) => { if (e.target === modal) closeCartModal(); };
  document.body.appendChild(modal);
}

// DOM Ready
document.addEventListener("DOMContentLoaded", () => {
  const activeF = document.querySelector('.filter-pill.active');
  if (activeF) setFilter(activeF, 'all');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('section, .course-card').forEach(el => observer.observe(el));

  const navbar = document.querySelector('nav');
  if (navbar) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    });
  }

  // Testimonials Marquee Logic
  const track = document.querySelector('.testimonials-track');
  if (track) {
    const cards = Array.from(track.children);
    cards.forEach(c => track.appendChild(c.cloneNode(true)));
  }

  fetch('api/cart.php', { method: 'POST', body: new URLSearchParams({ action: 'count' }) })
    .then(res => res.json()).then(data => updateCartBadge(data.count));

  const checkLogin = typeof isLoggedIn !== 'undefined' ? isLoggedIn : (window.isLoggedIn || false);
  if (checkLogin) {
    console.log("User logged in, starting notification polling...");
    updateNotiBadge();
    setInterval(updateNotiBadge, 30000);
  }
});



function showToast(msg) {
  let toast = document.getElementById('toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'toast';
    toast.style = `
      position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
      background: rgba(0,0,0,0.95); color: white; padding: 14px 28px;
      border-radius: 50px; z-index: 10000; font-family: 'Prompt', sans-serif;
      font-size: 0.95rem; box-shadow: 0 10px 40px rgba(0,0,0,0.4); display: none;
      border: 1px solid var(--navy-border);
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.display = 'block';
  setTimeout(() => { toast.style.display = 'none'; }, 3000);
}