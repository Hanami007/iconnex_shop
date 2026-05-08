// Course data

// Navigate to detail page
function goToDetail(courseId) {
  window.location.href = "dic_product.php?id=" + courseId;
}

// Tab: portfolio
function switchTab(btn, type) {
  document
    .querySelectorAll(".tab-btn")
    .forEach((b) => b.classList.remove("active"));
  btn.classList.add("active");
  if (type === 'video') {
    document.getElementById('portfolio-photo-content').style.display = 'none';
    document.getElementById('portfolio-video-content').style.display = 'block';
  } else {
    document.getElementById('portfolio-photo-content').style.display = 'block';
    document.getElementById('portfolio-video-content').style.display = 'none';
  }
}

// Category filter
function filterCategory(category) {
  document.querySelectorAll(".category-section").forEach((section) => {
    if (category === 'all' || section.getAttribute('data-category') === category) {
      section.style.display = "block";
      void section.offsetWidth;
      section.style.opacity = "1";
    } else {
      section.style.display = "none";
      section.style.opacity = "0";
    }
  });
}

// FAQ accordion
document.querySelectorAll(".faq-item").forEach((item) => {
  item.querySelector(".faq-question").addEventListener("click", () => {
    const isOpen = item.classList.contains("open");
    document
      .querySelectorAll(".faq-item")
      .forEach((i) => i.classList.remove("open"));
    if (!isOpen) item.classList.add("open");
  });
});

// Scroll reveal
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        e.target.classList.add('reveal-pro');
      }
    });
  },
  { threshold: 0.1 },
);

document.querySelectorAll("section, .video-wrap, .category-section, .course-card").forEach((el) => {
  observer.observe(el);
});

// Login Check Helper
function checkLogin() {
  if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
    showLoginModal();
    return false;
  }
  return true;
}

function showLoginModal() {
  // Create modal if not exists
  let modal = document.getElementById('loginRequiredModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'loginRequiredModal';
    modal.className = 'modal-overlay';
    modal.innerHTML = `
      <div class="login-modal">
        <button class="close-modal" onclick="closeLoginModal()">&times;</button>
        <div class="modal-icon">👤</div>
        <h2>${langData.cart_popup_title}</h2>
        <p>${langData.cart_popup_desc}</p>
        <div class="modal-actions">
          <a href="login.php" class="modal-btn modal-btn-login">${langData.btn_login}</a>
          <a href="register.php" class="modal-btn modal-btn-register">${langData.btn_register}</a>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    
    // Close on click overlay
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeLoginModal();
    });
  }
  
  modal.style.display = 'flex';
  setTimeout(() => modal.classList.add('active'), 10);
}

function closeLoginModal() {
  const modal = document.getElementById('loginRequiredModal');
  if (modal) {
    modal.classList.remove('active');
    setTimeout(() => {
      modal.style.display = 'none';
    }, 300);
  }
}

function addToCart(courseId) {
  if (!checkLogin()) return;

  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=add&course_id=${courseId}`,
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.success) {
        const badge = document.getElementById("cart-count");
        if (badge) {
          badge.textContent = data.count;
          badge.style.display = "flex";
        }
        showToast("เพิ่มลงตะกร้าแล้ว ✓");
      } else {
        showToast("เกิดข้อผิดพลาด: " + (data.message || "ไม่สามารถเพิ่มสินค้าได้"));
      }
    })
    .catch((err) => {
      console.error(err);
      showToast("เกิดข้อผิดพลาดในการเชื่อมต่อ");
    });
}

function buyNow(course) {
  if (!checkLogin()) return;

  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=add&course_id=${course.id}`,
  }).then(() => {
    const item = {
      id: course.id,
      name: course.name,
      price: course.price,
      qty: 1,
      instructor: course.instructor,
      image: course.image,
      description: course.description,
      category: course.category
    };
    localStorage.setItem('checkoutCart', JSON.stringify([item]));
    window.location.href = 'pay2.1/index.php';
  });
}

// โหลด count ตะกร้าตอนเปิดหน้า
fetch("cart_handler.php", {
  method: "POST",
  headers: { "Content-Type": "application/x-www-form-urlencoded" },
  body: "action=count",
})
  .then((r) => r.json())
  .then((data) => {
    if (data.count > 0) {
      const badge = document.getElementById("cart-count");
      if (badge) {
        badge.textContent = data.count;
        badge.style.display = "flex";
      }
    }
  });

// --- CART MODAL LOGIC ---
function openCartModal(e) {
  if (e) e.preventDefault();
  
  let modal = document.getElementById('cartModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'cartModal';
    modal.className = 'modal-overlay';
    document.body.appendChild(modal);
    
    // Close on click overlay
    modal.addEventListener('click', (ev) => {
      if (ev.target === modal) closeCartModal();
    });
  }
  
  modal.style.display = 'flex';
  
  // Show loading
  modal.innerHTML = `
    <div class="cart-modal">
      <div class="cart-modal-header">
        <div style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a;">ตะกร้าสินค้าของคุณ</div>
        <button class="close-modal" onclick="closeCartModal()" style="font-size: 1.5rem; color: #666; background: none; border: none; cursor: pointer;">&times;</button>
      </div>
      <div class="cart-modal-body" style="padding: 40px; text-align: center;">กำลังโหลด...</div>
    </div>
  `;
  setTimeout(() => modal.classList.add('active'), 10);

  // Fetch cart data
  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=get_cart",
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      renderCartModal(data.items, data.total, data.count);
    }
  });
}

function closeCartModal() {
  const modal = document.getElementById('cartModal');
  if (modal) {
    modal.classList.remove('active');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
  }
}

function removeFromCartModal(courseId) {
  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=remove&course_id=${courseId}`,
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const badge = document.getElementById("cart-count");
      if (badge) {
        badge.textContent = data.count;
        badge.style.display = data.count > 0 ? "flex" : "none";
      }
      openCartModal(); // Re-render
    }
  });
}

function checkoutCartModal() {
  window.location.href = 'pay2.1/index.php';
}

function renderCartModal(items, total, count) {
  const modal = document.getElementById('cartModal');
  if (!modal) return;
  
  if (items.length === 0) {
    modal.innerHTML = `
      <div class="cart-modal">
        <div class="cart-modal-header">
          <div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a;">ตะกร้าสินค้าของคุณ</div>
            <div style="font-size: 0.9rem; color: #666; margin-top: 4px;">คุณไม่มีรายการในตะกร้าสินค้า</div>
          </div>
          <button class="close-modal" onclick="closeCartModal()" style="font-size: 1.5rem; color: #666; background: none; border: none; cursor: pointer;">&times;</button>
        </div>
        <div class="cart-modal-body" style="padding: 40px; text-align: center; color: #888;">
          <div style="font-size: 40px; margin-bottom: 16px;">🛒</div>
          ตะกร้าสินค้าว่างเปล่า
        </div>
      </div>
    `;
    return;
  }

  let itemsHtml = items.map(item => {
    let priceHtml = `<span style="color: #e11d48; font-weight: 700; font-size: 1.1rem; font-family: 'Prompt', sans-serif;">฿${Number(item.price).toLocaleString()}</span>`;
    if (item.old_price && item.old_price > item.price) {
      priceHtml += ` <span style="color: #9ca3af; text-decoration: line-through; font-size: 0.85rem; margin-left: 8px;">${Number(item.old_price).toLocaleString()}</span>`;
    }
    
    let img_src = item.image;
    if (img_src.includes('<img')) {
      // rough extract if it's html
      const m = img_src.match(/src="([^"]+)"/);
      if (m) img_src = m[1];
    } else if (img_src.includes('.')) {
      img_src = img_src.startsWith('uploads/') ? '/' + img_src : 'IMG/' + img_src;
    }

    return `
      <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #f0f0f0; position: relative;">
        <img src="${img_src}" style="width: 120px; height: 75px; object-fit: cover; border-radius: 8px;" alt="${item.name}">
        <div style="flex: 1; padding-right: 24px;">
          <div style="font-weight: 600; color: #1f2937; font-size: 0.95rem; line-height: 1.3; margin-bottom: 8px;">${item.name}</div>
          <div style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #4b5563; margin-bottom: 8px;">
            <img src="${item.instructor_avatar || 'IMG/default-avatar.png'}" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
            ${item.instructor}
          </div>
          <div>${priceHtml}</div>
        </div>
        <button onclick="removeFromCartModal(${item.id})" style="position: absolute; right: 0; top: 16px; background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 1.1rem;">
          <i class="far fa-trash-alt"></i>
        </button>
      </div>
    `;
  }).join('');

  modal.innerHTML = `
    <div class="cart-modal">
      <div class="cart-modal-header" style="padding: 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <div style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a; font-family: 'Prompt', sans-serif;">ตะกร้าสินค้าของคุณ</div>
          <div style="font-size: 0.9rem; color: #6b7280; margin-top: 4px;">คุณมี ${count} รายการ ในตะกร้าสินค้า</div>
        </div>
        <button class="close-modal" onclick="closeCartModal()" style="font-size: 1.5rem; color: #6b7280; background: none; border: none; cursor: pointer;">&times;</button>
      </div>
      <div class="cart-modal-body" style="padding: 0 24px; max-height: 400px; overflow-y: auto;">
        ${itemsHtml}
      </div>
      <div class="cart-modal-footer" style="padding: 24px; background: #f9fafb; border-top: 1px solid #f0f0f0; border-radius: 0 0 16px 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div style="color: #4b5563; font-size: 0.95rem;">ยอดชำระทั้งหมด</div>
          <div style="color: #8b5cf6; font-size: 1.25rem; font-weight: 700; font-family: 'Prompt', sans-serif;">฿${Number(total).toLocaleString()}</div>
        </div>
        <button onclick="checkoutCartModal()" style="width: 100%; background: #8b5cf6; color: white; border: none; padding: 14px; border-radius: 8px; font-family: 'Prompt', sans-serif; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
          ชำระเงิน
        </button>
      </div>
    </div>
  `;
}

function setTab(el, val) {
  document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  switchCourseTab(val);
}
function setFilter(el, val) {
  document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  filterCategory(val);
}
function switchCourseTab(val) {
  console.log('Switched tab to:', val);
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.course-grid').forEach(grid => {
    if (grid.children.length > 3) {
      const wrapper = document.createElement('div');
      wrapper.className = 'course-carousel-container';
      grid.parentNode.insertBefore(wrapper, grid);
      wrapper.appendChild(grid);
      const prevBtn = document.createElement('button');
      prevBtn.className = 'carousel-btn prev-btn';
      prevBtn.innerHTML = '❮';
      prevBtn.onclick = () => { grid.scrollBy({ left: -320, behavior: 'smooth' }); };
      const nextBtn = document.createElement('button');
      nextBtn.className = 'carousel-btn next-btn';
      nextBtn.innerHTML = '❯';
      nextBtn.onclick = () => { grid.scrollBy({ left: 320, behavior: 'smooth' }); };
      wrapper.appendChild(prevBtn);
      wrapper.appendChild(nextBtn);
      grid.style.margin = '0';
    }
  });
});

// Toast Helper (if not defined elsewhere)
function showToast(msg) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.style = `
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 3000;
            font-family: 'Prompt', sans-serif;
            font-size: 0.9rem;
            display: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        `;
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}