const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobilePanel = document.querySelector('[data-mobile-panel]');
if (mobileToggle && mobilePanel) {
  mobileToggle.addEventListener('click', () => mobilePanel.classList.toggle('open'));
}

document.querySelectorAll('[data-fake-submit]').forEach(form => {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const notice = form.querySelector('.form-result') || document.createElement('div');
    notice.className = 'notice form-result';
    notice.style.marginTop = '16px';
    notice.textContent = 'Đã ghi nhận thông tin demo. Bản prototype này chưa kết nối backend thật, nhưng luồng trải nghiệm đã sẵn sàng để duyệt.';
    form.appendChild(notice);
  });
});

const now = new Date();
document.querySelectorAll('[data-now]').forEach(el => {
  el.textContent = now.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' });
});
