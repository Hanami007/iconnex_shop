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
function addToCart(courseId) {
  if (!checkLogin()) return;

  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=add&course_id=${courseId}`,
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Update badge
      const badge = document.getElementById("cart-count");
      if (badge) {
        badge.textContent = data.count;
        badge.style.display = data.count > 0 ? "flex" : "none";
      }
      // Show success toast
      showToast(data.message || "เพิ่มลงตะกร้าเรียบร้อยแล้ว");
      // ไม่ต้องเปิดตะกร้าเด้งขึ้นมาตามคำขอผู้ใช้
    }
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
        <h2>ตะกร้าสินค้าของคุณ</h2>
        <button class="close-modal" onclick="closeCartModal()">&times;</button>
      </div>
      <div class="cart-modal-body" style="padding: 60px 40px; text-align: center;">
        <div class="spinner"></div>
        <p style="margin-top: 16px; color: var(--text-muted);">กำลังโหลดข้อมูล...</p>
      </div>
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
  if (!checkLogin()) return;

  // Fetch full cart data to ensure localStorage is in sync before redirect
  fetch("cart_handler.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=get_cart",
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.items.length > 0) {
      localStorage.setItem('checkoutCart', JSON.stringify(data.items));
      window.location.href = 'pay2.1/index.php';
    } else {
      showToast("ตะกร้าว่างเปล่า");
    }
  })
  .catch(err => {
    console.error(err);
    showToast("เกิดข้อผิดพลาดในการชำระเงิน");
  });
}

function renderCartModal(items, total, count) {
  const modal = document.getElementById('cartModal');
  if (!modal) return;
  
  if (items.length === 0) {
    modal.innerHTML = `
      <div class="cart-modal">
        <div class="cart-modal-header">
          <h2>ตะกร้าสินค้าของคุณ</h2>
          <button class="close-modal" onclick="closeCartModal()">&times;</button>
        </div>
        <div class="cart-empty">
          <div class="cart-empty-icon">🛒</div>
          <p class="cart-empty-text">ตะกร้าสินค้าของคุณยังว่างเปล่า</p>
          <button class="btn-checkout" onclick="closeCartModal()" style="margin-top: 24px; max-width: 200px;">ไปเลือกคอร์สเรียน</button>
        </div>
      </div>
    `;
    return;
  }

  let itemsHtml = items.map(item => {
    let priceHtml = `<span class="cart-item-price">฿${Number(item.price).toLocaleString()}</span>`;
    if (item.old_price && item.old_price > item.price) {
      priceHtml += `<span class="cart-item-price-old">฿${Number(item.old_price).toLocaleString()}</span>`;
    }
    
    let img_src = item.image;
    if (img_src.includes('<img')) {
      const m = img_src.match(/src="([^"]+)"/);
      if (m) img_src = m[1];
    } else if (img_src.includes('.')) {
      img_src = img_src.startsWith('uploads/') ? '/' + img_src : 'IMG/' + img_src;
    }

    return `
      <div class="cart-item-row">
        <img src="${img_src}" class="cart-item-img" alt="${item.name}">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-instructor">
            <img src="${item.instructor_avatar || 'IMG/default-avatar.png'}" style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">
            <span>${item.instructor}</span>
          </div>
          <div>${priceHtml}</div>
        </div>
        <button class="cart-item-remove" onclick="removeFromCartModal(${item.id})" title="ลบรายการนี้">
          <i class="far fa-trash-alt"></i>
        </button>
      </div>
    `;
  }).join('');

  modal.innerHTML = `
    <div class="cart-modal">
      <div class="cart-modal-header">
        <div>
          <h2>ตะกร้าสินค้าของคุณ</h2>
          <div class="cart-count-text">คุณมี ${count} รายการในตะกร้า</div>
        </div>
        <button class="close-modal" onclick="closeCartModal()">&times;</button>
      </div>
      <div class="cart-modal-body">
        ${itemsHtml}
      </div>
      <div class="cart-modal-footer">
        <div class="cart-total-row">
          <div class="cart-total-label">ยอดชำระทั้งหมด</div>
          <div class="cart-total-value">฿${Number(total).toLocaleString()}</div>
        </div>
        <button class="btn-checkout" onclick="checkoutCartModal()">
          ดำเนินการชำระเงิน
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
  // Course Carousel Logic
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

  // Testimonials Marquee Clone
  const testimonialsTrack = document.querySelector('.testimonials-track');
  if (testimonialsTrack) {
    const cards = Array.from(testimonialsTrack.children);
    cards.forEach(card => {
      const clone = card.cloneNode(true);
      testimonialsTrack.appendChild(clone);
    });
  }

  // Scroll Reveal Animation Logic
  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.15
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // Stop observing once revealed
      }
    });
  }, observerOptions);

  document.querySelectorAll('.reveal').forEach(el => {
    observer.observe(el);
  });

  // Navbar Blur on Scroll
  const navbar = document.querySelector('nav');
  if (navbar) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }
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