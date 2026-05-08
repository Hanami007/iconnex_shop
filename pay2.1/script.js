/* =============================================
   EduCourse — Checkout Page Script
============================================= */

/* ---------- Constants ---------- */
const VAT_RATE   = 0.07;
const PROMPTPAY  = '0-8612-34567';

const BANK_DATA = {
  scb: {
    name:    'ธนาคารไทยพาณิชย์ (SCB)',
    branch:  'สาขาสีลม',
    holder:  'บริษัท เอดูคอร์ส จำกัด',
    account: '123-4-56789-0',
  },
  ktb: {
    name:    'ธนาคารกรุงไทย (KTB)',
    branch:  'สาขาหลักสี่',
    holder:  'บริษัท เอดูคอร์ส จำกัด',
    account: '987-6-54321-0',
  },
  bbl: {
    name:    'ธนาคารกรุงเทพ (BBL)',
    branch:  'สาขาสยาม',
    holder:  'บริษัท เอดูคอร์ส จำกัด',
    account: '555-6-66777-0',
  },
};

/* ---------- State ---------- */
let addedCourses = {}; // Extras from the "Add More" section
let baseCourse   = { price: 0, name: 'ไม่มีสินค้า' }; 
let payMethod    = ''; 
let orderId      = '';

// New state for carousel
let fullCartItems = [];
let currentHeroIndex = 0;

/* ---------- Load Cart ---------- */
function loadCartData() {
  const data = localStorage.getItem('checkoutCart');
  if (!data) return;

  const cart = JSON.parse(data);
  if (cart.length === 0) return;

  fullCartItems = cart;
  currentHeroIndex = 0;

  // Clear receipt lines
  document.getElementById('receipt-lines').innerHTML = '';

  // Add all items to the receipt and carousel
  cart.forEach((item, index) => {
    // The first item is our initial baseCourse for calculation
    if (index === 0) {
      baseCourse = {
        price: item.price,
        name: item.name,
        instructor: item.instructor,
        image: item.image,
        description: item.description,
        category: item.category
      };
    }
    
    // Use toggleCourse to add it to the UI and addedCourses (if not the base)
    // Actually, let's make them all "Added" so they all have remove buttons
    // and we'll just set baseCourse.price to 0 to avoid double counting.
    // Wait, the existing calcSubtotal adds baseCourse.price.
    // Let's set baseCourse.price = 0 and add everything via toggleCourse.
  });

  baseCourse.price = 0; 
  cart.forEach(item => {
    toggleCourse('cart-' + item.id, item.price, item.name);
  });

  updateHeroDisplay();
}

function updateHeroDisplay() {
  if (fullCartItems.length === 0) {
    document.getElementById('hero-banner-title').textContent = 'ไม่มีสินค้าในตะกร้า';
    document.getElementById('hero-banner-sub').textContent   = '-';
    document.getElementById('hero-course-name').textContent  = 'กรุณาเลือกคอร์สเรียน';
    document.getElementById('hero-course-desc').textContent  = 'คุณยังไม่ได้เลือกคอร์สเรียนใดๆ กรุณากลับไปเลือกคอร์สที่ต้องการ';
    document.getElementById('hero-icon-box').innerHTML       = `<i class="ti ti-shopping-cart-off" style="font-size:32px;color:var(--gold)"></i>`;
    document.getElementById('hero-counter').textContent      = '0 / 0';
    document.getElementById('hero-prev').style.display       = 'none';
    document.getElementById('hero-next').style.display       = 'none';
    document.getElementById('hero-features-grid').innerHTML  = '';
    return;
  }

  const course = fullCartItems[currentHeroIndex];
  if (!course) return;

  // Update Hero Section
  document.getElementById('hero-banner-title').textContent = course.name;
  document.getElementById('hero-banner-sub').textContent   = course.category;
  document.getElementById('hero-course-name').textContent  = course.name;
  document.getElementById('hero-course-desc').textContent  = course.description;
  document.getElementById('hero-icon-box').innerHTML       = `<span style="font-size:32px">${course.image}</span>`;

  // Update Features Grid
  const grid = document.getElementById('hero-features-grid');
  grid.innerHTML = `
    <div class="feature-item"><span class="feature-dot"></span>วิดีโอ ${course.hours}+ ชั่วโมง</div>
    <div class="feature-item"><span class="feature-dot"></span>เนื้อหา ${course.lessons} บทเรียน</div>
    <div class="feature-item"><span class="feature-dot"></span>ใบรับรองเมื่อจบคอร์ส</div>
    <div class="feature-item"><span class="feature-dot"></span>กลุ่ม Community หลังเรียน</div>
    <div class="feature-item"><span class="feature-dot"></span>อัปเดตเนื้อหาตลอดชีพ</div>
    <div class="feature-item"><span class="feature-dot"></span>ถามตอบกับอาจารย์</div>
  `;

  // Update Counter
  document.getElementById('hero-counter').textContent = `${currentHeroIndex + 1} / ${fullCartItems.length}`;

  // Toggle arrow visibility
  document.getElementById('hero-prev').style.display = currentHeroIndex === 0 ? 'none' : 'flex';
  document.getElementById('hero-next').style.display = currentHeroIndex === fullCartItems.length - 1 ? 'none' : 'flex';
}

function nextHeroCourse() {
  if (currentHeroIndex < fullCartItems.length - 1) {
    currentHeroIndex++;
    updateHeroDisplay();
  }
}

function prevHeroCourse() {
  if (currentHeroIndex > 0) {
    currentHeroIndex--;
    updateHeroDisplay();
  }
}

/* ---------- Helpers ---------- */
function formatThb(amount) {
  return amount.toLocaleString('th-TH') + ' ฿';
}

function calcSubtotal() {
  const extras = Object.values(addedCourses).reduce((sum, c) => sum + c.price, 0);
  return baseCourse.price + extras;
}

function calcVat(subtotal) {
  return Math.round(subtotal * VAT_RATE);
}

function calcGrandTotal() {
  const sub = calcSubtotal();
  return sub + calcVat(sub);
}

function generateOrderId() {
  return 'ORD-' + Math.floor(100000 + Math.random() * 900000);
}

/* ---------- Init ---------- */
function initMeta() {
  orderId = generateOrderId();

  // Header & receipt order number
  document.getElementById('hdr-order').textContent  = orderId;
  document.getElementById('meta-order').textContent = orderId;

  // Today's date
  const now = new Date();
  document.getElementById('meta-date').textContent = now.toLocaleDateString('th-TH', {
    day: '2-digit', month: '2-digit', year: '2-digit',
  });

  // Expiry = 24 hours later
  const exp = new Date(now.getTime() + 24 * 60 * 60 * 1000);
  document.getElementById('meta-exp').textContent = exp.toLocaleString('th-TH', {
    day: '2-digit', month: '2-digit', year: '2-digit',
    hour: '2-digit', minute: '2-digit',
  }).replace(',', ' ');
}

/* ---------- Totals ---------- */
function updateTotals() {
  const sub   = calcSubtotal();
  const vat   = calcVat(sub);
  const grand = sub + vat;

  document.getElementById('tot-sub').textContent      = formatThb(sub);
  document.getElementById('tot-vat').textContent      = formatThb(vat);
  document.getElementById('tot-grand').textContent    = formatThb(grand);
  document.getElementById('btn-amount-lbl').textContent = formatThb(grand);
  document.getElementById('qr-amount-lbl').textContent = grand.toLocaleString('th-TH') + ' บาท';

  // Keep bank amount in sync if visible
  const biAmount = document.getElementById('bi-amount');
  if (biAmount.textContent !== '—') {
    biAmount.textContent = grand.toLocaleString('th-TH') + ' บาท';
  }
}

/* ---------- Course Toggle ---------- */
function toggleCourse(catId, price, name) {
  const toggleBtn = document.getElementById('toggle-' + catId);
  const wrapper = toggleBtn ? toggleBtn.closest('.catalogue-wrapper') : null;
  const btn  = wrapper ? wrapper.querySelector('.cat-add-btn') : null;

  if (addedCourses[catId]) {
    // Remove course
    delete addedCourses[catId];
    if (wrapper) wrapper.classList.remove('selected');
    if (btn) btn.innerHTML = '<i class="ti ti-plus"></i> เพิ่มไปยังตะกร้า';
    document.getElementById('rline-' + catId)?.remove();

    // Remove from carousel data
    const itemIndex = fullCartItems.findIndex(item => ('cart-' + item.id) === catId);
    if (itemIndex > -1) {
      fullCartItems.splice(itemIndex, 1);
      // If we removed the current item or an item before it, adjust index
      if (currentHeroIndex >= itemIndex && currentHeroIndex > 0) {
        currentHeroIndex--;
      }
      updateHeroDisplay();
    }
  } else {
    // Add course
    addedCourses[catId] = { price, name };
    if (wrapper) wrapper.classList.add('selected');
    if (btn) btn.innerHTML = '<i class="ti ti-check"></i> เพิ่มแล้ว';

    const line = document.createElement('div');
    line.className = 'receipt-line';
    line.id        = 'rline-' + catId;
    line.innerHTML = `
      <div class="rline-info">
        <div class="rline-name">${name}</div>
        <div class="rline-sub">เรียนออนไลน์</div>
      </div>
      <div class="rline-price">${price.toLocaleString('th-TH')} ฿</div>
      <button class="rline-remove" data-id="${catId}" data-price="${price}" data-name="${name}">✕</button>
    `;
    document.getElementById('receipt-lines').appendChild(line);

    // Delegate remove click on newly created button
    line.querySelector('.rline-remove').addEventListener('click', function () {
      toggleCourse(this.dataset.id, Number(this.dataset.price), this.dataset.name);
    });
  }

  updateTotals();
}

/* ---------- Payment Method ---------- */
function setPayMethod(method) {
  payMethod = method;

  // Tab active state
  document.querySelectorAll('.pay-tab').forEach(tab => {
    tab.classList.toggle('active', tab.dataset.method === method);
  });

  // Show/hide sections
  document.getElementById('qr-section').classList.toggle('hidden', method !== 'qr');
  document.getElementById('bank-section').classList.toggle('hidden', method !== 'bank');

  if (method === 'qr') drawQR();
}

/* ---------- QR Code (pattern placeholder) ---------- */
function drawQR() {
  const box = document.getElementById('qr-visual');
  const size = box.offsetWidth || 140;

  // 7×7 pixel art pattern simulating a QR code finder pattern
  const pattern = [
    1,1,1,1,1,1,1,
    1,0,0,0,0,0,1,
    1,0,1,1,1,0,1,
    1,0,1,0,1,0,1,
    1,0,1,1,1,0,1,
    1,0,0,0,0,0,1,
    1,1,1,1,1,1,1,
  ];

  const cell = Math.floor(size / 7);
  let svg = `<svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg">`;

  pattern.forEach((v, i) => {
    if (!v) return;
    const x = (i % 7) * cell + 2;
    const y = Math.floor(i / 7) * cell + 2;
    svg += `<rect x="${x}" y="${y}" width="${cell - 1}" height="${cell - 1}" rx="1" fill="var(--navy-dark)"/>`;
  });

  // Random data dots in remaining area
  for (let row = 0; row < 7; row++) {
    for (let col = 0; col < 7; col++) {
      if (Math.random() > 0.55) {
        const x = col * cell + 2;
        const y = row * cell + 2;
        svg += `<rect x="${x}" y="${y}" width="${Math.ceil(cell * 0.6)}" height="${Math.ceil(cell * 0.6)}" rx="1" fill="var(--navy-dark)" opacity="0.5"/>`;
      }
    }
  }

  svg += '</svg>';
  box.innerHTML = svg;
}

/* ---------- Bank Info ---------- */
function updateBankInfo() {
  const val     = document.getElementById('bank-select').value;
  const infoBox = document.getElementById('bank-info-box');

  if (!val) {
    infoBox.classList.add('hidden');
    return;
  }

  const bank  = BANK_DATA[val];
  const grand = calcGrandTotal();

  document.getElementById('bi-bank').textContent   = bank.name;
  document.getElementById('bi-branch').textContent = bank.branch;
  document.getElementById('bi-name').textContent   = bank.holder;
  document.getElementById('bi-acc').textContent    = bank.account;
  document.getElementById('bi-amount').textContent = grand.toLocaleString('th-TH') + ' บาท';

  infoBox.classList.remove('hidden');
}

/* ---------- Copy Account Number ---------- */
function copyAccountNumber() {
  const acc = document.getElementById('bi-acc').textContent.replace(/-/g, '');
  const btn = document.getElementById('copy-acc-btn');

  if (navigator.clipboard) {
    navigator.clipboard.writeText(acc).then(() => {
      btn.textContent = '✓ คัดลอกแล้ว';
      setTimeout(() => { btn.textContent = 'คัดลอก'; }, 2000);
    });
  } else {
    // Fallback for older browsers
    const temp = document.createElement('input');
    temp.value = acc;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    btn.textContent = '✓ คัดลอกแล้ว';
    setTimeout(() => { btn.textContent = 'คัดลอก'; }, 2000);
  }
}

/* ---------- Validation & Confirm ---------- */
async function confirmPayment() {
  const name    = document.getElementById('inp-name').value.trim();
  const surname = document.getElementById('inp-surname').value.trim();
  const email   = document.getElementById('inp-email').value.trim();
  const phone   = document.getElementById('inp-phone').value.trim();
  const lineId  = document.getElementById('inp-line').value.trim();
  const slip    = document.getElementById('inp-slip').files[0];

  if (!name || !surname || !email || !phone || !lineId) {
    alert('กรุณากรอกข้อมูลผู้ชำระเงินและ LINE ID ให้ครบถ้วน');
    return;
  }

  if (!payMethod) {
    alert('กรุณาเลือกวิธีการชำระเงิน');
    return;
  }

  if (payMethod === 'bank' && !document.getElementById('bank-select').value) {
    alert('กรุณาเลือกธนาคารสำหรับการโอนเงิน');
    return;
  }

  if (!slip) {
    alert('กรุณาแนบสลิปโอนเงินเพื่อยืนยันการชำระเงิน');
    return;
  }

  const btn = document.getElementById('confirm-btn');
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<span>กำลังประมวลผล...</span>';
  btn.disabled = true;

  const grand = calcGrandTotal();
  const fd = new FormData();
  fd.append('order_no', orderId);
  fd.append('name', name);
  fd.append('surname', surname);
  fd.append('email', email);
  fd.append('phone', phone);
  fd.append('line_id', lineId);
  fd.append('total_amount', grand);
  fd.append('payment_method', payMethod);
  fd.append('items', JSON.stringify(fullCartItems));
  fd.append('slip', slip);

  try {
    const res = await fetch('/api_checkout.php', {
      method: 'POST',
      body: fd
    });
    const data = await res.json();

    if (data.success) {
      alert(
        '✅ ระบบบันทึกข้อมูลสำเร็จ!\n' +
        'เลขที่ใบสั่งซื้อ: ' + orderId + '\n' +
        'ยอดชำระ: ' + grand.toLocaleString('th-TH') + ' บาท\n\n' +
        'เราได้รับคำสั่งซื้อของคุณแล้ว เมื่อแอดมินตรวจสอบเสร็จสิ้นจะส่งอีเมลพร้อมลิงก์เข้าเรียนไปที่ ' + email
      );
      localStorage.removeItem('checkoutCart');
      window.location.href = '/index.php#courses';
    } else {
      alert('เกิดข้อผิดพลาด: ' + (data.error || 'ไม่สามารถบันทึกข้อมูลได้'));
      btn.innerHTML = originalHtml;
      btn.disabled = false;
    }
  } catch (err) {
    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
    btn.innerHTML = originalHtml;
    btn.disabled = false;
  }
}

/* ---------- Event Listeners ---------- */
document.addEventListener('DOMContentLoaded', () => {
  loadCartData();
  initMeta();
  updateTotals();

  // Accordion toggles for course catalogues
  document.querySelectorAll('.catalogue-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
      const wrapper = toggle.closest('.catalogue-wrapper');
      const detailId = toggle.getAttribute('id').replace('toggle-', 'detail-');
      const detail = document.getElementById(detailId);
      
      const isOpen = wrapper.classList.contains('accordion-open');
      
      // Close all other accordions
      document.querySelectorAll('.catalogue-wrapper').forEach(w => {
        w.classList.remove('accordion-open');
      });
      document.querySelectorAll('.catalogue-detail').forEach(d => {
        d.classList.add('hidden');
      });
      
      // Toggle current accordion
      if (!isOpen) {
        wrapper.classList.add('accordion-open');
        detail.classList.remove('hidden');
      }
    });
  });

  // Add-course buttons (catalogue)
  document.querySelectorAll('.cat-add-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      toggleCourse(btn.dataset.id, Number(btn.dataset.price), btn.dataset.name);
    });
  });

  // Payment method tabs
  document.querySelectorAll('.pay-tab').forEach(tab => {
    tab.addEventListener('click', () => setPayMethod(tab.dataset.method));
  });

  // Bank select
  document.getElementById('bank-select').addEventListener('change', updateBankInfo);

  // Copy account number
  document.getElementById('copy-acc-btn').addEventListener('click', copyAccountNumber);

  // Confirm button
  document.getElementById('confirm-btn').addEventListener('click', confirmPayment);
});