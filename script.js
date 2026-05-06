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
      // trigger reflow
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
        e.target.style.opacity = "1";
        e.target.style.transform = "translateY(0)";
      }
    });
  },
  { threshold: 0.1 },
);

document.querySelectorAll("section, .video-wrap").forEach((el) => {
  el.style.opacity = "0";
  el.style.transform = "translateY(24px)";
  el.style.transition = "opacity .6s ease, transform .6s ease";
  observer.observe(el);
});

function addToCart(courseId) {
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

// Function for sorting tab
function switchCourseTab(val) {
  // Logic to handle popular/latest sorting can be added here
  console.log('Switched tab to:', val);
}

// Initialize Course Carousels
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.course-grid').forEach(grid => {
    // Check if grid has more than 3 cards
    if (grid.children.length > 3) {
      // Wrap grid
      const wrapper = document.createElement('div');
      wrapper.className = 'course-carousel-container';
      grid.parentNode.insertBefore(wrapper, grid);
      wrapper.appendChild(grid);

      // Create buttons
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
      
      // Remove margin from grid since wrapper has it
      grid.style.margin = '0';
    }
  });
});