const siteConfig = window.aitrongcayTheme || {};
const rootUrl = siteConfig.rootUrl || '/';
const navItems = Array.isArray(siteConfig.nav) ? siteConfig.nav : [
  { label: 'Trang chủ', url: rootUrl },
  { label: 'Cách hoạt động', url: `${rootUrl.replace(/\/$/, '')}/cach-hoat-dong/` },
  { label: 'Chợ quê', url: `${rootUrl.replace(/\/$/, '')}/cho-que/` },
  { label: 'An toàn thực phẩm', url: `${rootUrl.replace(/\/$/, '')}/an-toan-thuc-pham/` },
  { label: 'Chuyện nhà nông', url: `${rootUrl.replace(/\/$/, '')}/chuyen-nha-nong/` },
  { label: 'FAQ', url: `${rootUrl.replace(/\/$/, '')}/faq/` },
];

const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobilePanel = document.querySelector('[data-mobile-panel]');

if (mobileToggle && mobilePanel && !mobilePanel.dataset.initialized) {
  mobilePanel.innerHTML = `
    <div class="mobile-panel-inner">
      ${navItems.map(item => `<a href="${item.url}">${item.label}</a>`).join('')}
    </div>`;
  mobilePanel.dataset.initialized = 'true';

  mobileToggle.addEventListener('click', () => {
    const opened = mobilePanel.style.display === 'block';
    mobilePanel.style.display = opened ? 'none' : 'block';
  });
}

const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
document.querySelectorAll('.nav-menu a').forEach(link => {
  const href = link.getAttribute('href') || '';
  try {
    const linkPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
    if (linkPath === currentPath) link.classList.add('active');
  } catch (error) {
    // ignore malformed URLs in prototype content
  }
});

document.querySelectorAll('[data-current-year]').forEach(el => {
  el.textContent = new Date().getFullYear();
});

document.querySelectorAll('[data-fake-submit]').forEach(form => {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const notice = form.querySelector('.form-result') || document.createElement('div');
    notice.className = 'notice form-result';
    notice.style.marginTop = '16px';
    notice.textContent = 'Đã ghi nhận thông tin của anh/chị. Đội ngũ Ai trồng cây sẽ liên hệ lại để tư vấn gói phù hợp trong thời gian sớm nhất.';
    form.appendChild(notice);
  });
});

const now = new Date();
document.querySelectorAll('[data-now]').forEach(el => {
  el.textContent = now.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' });
});

document.querySelectorAll('[data-accordion-item]').forEach(item => {
  const button = item.querySelector('[data-accordion-button]');
  if (!button) return;
  button.addEventListener('click', () => {
    item.classList.toggle('open');
  });
});

document.querySelectorAll('[data-tabs]').forEach(wrapper => {
  const buttons = wrapper.querySelectorAll('[data-tab-button]');
  const panels = wrapper.parentElement.querySelectorAll('[data-tab-panel]');
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      const target = button.dataset.tabButton;
      buttons.forEach(btn => btn.classList.toggle('active', btn === button));
      panels.forEach(panel => panel.classList.toggle('active', panel.dataset.tabPanel === target));
    });
  });
});

document.querySelectorAll('[data-progress]').forEach(bar => {
  const value = Number(bar.dataset.progress || 0);
  requestAnimationFrame(() => {
    bar.style.width = `${value}%`;
  });
});

document.querySelectorAll('[data-rotating-text]').forEach(el => {
  const items = (el.dataset.rotatingText || '').split('|').map(x => x.trim()).filter(Boolean);
  if (!items.length) return;
  let index = 0;
  el.textContent = items[0];
  setInterval(() => {
    index = (index + 1) % items.length;
    el.textContent = items[index];
  }, 2400);
});
