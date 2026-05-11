// Load course details from localStorage
document.addEventListener('DOMContentLoaded', () => {
  const courseJson = localStorage.getItem('selectedCourse');
  
  if (!courseJson) {
    window.location.href = 'index.html#courses';
    return;
  }

  const course = JSON.parse(courseJson);

  // Update course details
  document.getElementById('course-icon').textContent = course.image || '🎬';
  document.getElementById('course-name').textContent = course.name;
  document.getElementById('course-instructor').textContent = course.instructor;
  document.getElementById('course-category').textContent = course.category;
  document.getElementById('course-lessons').textContent = course.lessons;
  document.getElementById('course-hours').textContent = course.hours;
  document.getElementById('course-rating').textContent = course.rating;
  document.getElementById('course-reviews').textContent = course.reviews.toLocaleString();
  document.getElementById('course-description').textContent = course.description;
  document.getElementById('course-full-description').textContent = course.fullDescription;

  // Update summary
  document.getElementById('summary-course').textContent = course.name;
  document.getElementById('summary-price').textContent = `฿ ${course.price.toLocaleString()}`;
  document.getElementById('total-price').textContent = `฿ ${course.price.toLocaleString()}`;
});

// Payment method selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
  radio.addEventListener('change', (e) => {
    const method = e.target.value;
    
    // Hide all payment fields
    document.getElementById('card-fields').style.display = 'none';
    document.getElementById('promptpay-fields').style.display = 'none';
    document.getElementById('bank-fields').style.display = 'none';

    // Show selected payment method fields
    if (method === 'card') {
      document.getElementById('card-fields').style.display = 'block';
      document.getElementById('card-number').required = true;
      document.getElementById('card-expiry').required = true;
      document.getElementById('card-cvc').required = true;
    } else if (method === 'prompt_pay') {
      document.getElementById('promptpay-fields').style.display = 'block';
      document.getElementById('promptpay-id').required = true;
    } else if (method === 'bank_transfer') {
      document.getElementById('bank-fields').style.display = 'block';
      document.getElementById('bank-slip').required = true;
    }
  });
});

// Format card number input
document.getElementById('card-number').addEventListener('input', (e) => {
  let value = e.target.value.replace(/\s/g, '');
  let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
  e.target.value = formattedValue;
});

// Format expiry date input
document.getElementById('card-expiry').addEventListener('input', (e) => {
  let value = e.target.value.replace(/\D/g, '');
  if (value.length >= 2) {
    value = value.slice(0, 2) + '/' + value.slice(2, 4);
  }
  e.target.value = value;
});

// Only allow numbers for CVC
document.getElementById('card-cvc').addEventListener('input', (e) => {
  e.target.value = e.target.value.replace(/\D/g, '');
});

// Form submission
document.getElementById('payment-form').addEventListener('submit', (e) => {
  e.preventDefault();
  
  const formData = new FormData(document.getElementById('payment-form'));
  const method = formData.get('payment_method');
  
  // Basic validation
  if (!formData.get('fullname') || !formData.get('email') || !formData.get('phone')) {
    alert('กรุณากรอกข้อมูลส่วนตัวให้ครบถ้วน');
    return;
  }

  if (method === 'card') {
    if (!formData.get('card_number') || !formData.get('card_expiry') || !formData.get('card_cvc')) {
      alert('กรุณากรอกข้อมูลบัตรให้ครบถ้วน');
      return;
    }
  }

  // Show success message
  showSuccessMessage();
});

// Success message
function showSuccessMessage() {
  const course = JSON.parse(localStorage.getItem('selectedCourse'));
  
  const message = `
✓ ชำระเงินสำเร็จ!

ขอบคุณที่สมัครสมาชิกคอร์ส "${course.name}"

จะได้รับอีเมลยืนยันในเร็วๆ นี้
คุณสามารถเข้าเรียนได้ทันทีผ่าน portal ของเรา

สนใจคอร์สอื่นๆ? 
ไปที่: ไปที่หน้าหลัก
  `;
  
  alert(message);
  localStorage.removeItem('selectedCourse');
  window.location.href = 'index.html#courses';
}
