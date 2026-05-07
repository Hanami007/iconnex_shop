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
        badge.textContent = data.count;
        badge.style.display = "flex";
        showToast("เพิ่มลงตะกร้าแล้ว ✓");
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
      badge.textContent = data.count;
      badge.style.display = "flex";
    }
  });

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