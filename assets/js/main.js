const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobilePanel = document.querySelector('[data-mobile-panel]');

if (mobileToggle && mobilePanel) {
  if (!mobilePanel.dataset.initialized) {
    const prefix = window.location.pathname.includes('/portal/') || window.location.pathname.includes('/auth/') || window.location.pathname.includes('/signup/') ? '../' : '';
    mobilePanel.innerHTML = `
      <div class="mobile-panel-inner">
        <a href="${prefix}index.html">Trang chủ</a>
        <a href="${prefix}how-it-works.html">Cách hoạt động</a>
        <a href="${prefix}packages.html">Chợ quê</a>
        <a href="${prefix}food-safety.html">An toàn thực phẩm</a>
        <a href="${prefix}your-garden-story.html">Chuyện nhà nông</a>
        <a href="${prefix}faq.html">FAQ</a>
      </div>`;
    mobilePanel.dataset.initialized = 'true';
  }
  mobileToggle.addEventListener('click', () => {
    const opened = mobilePanel.style.display === 'block';
    mobilePanel.style.display = opened ? 'none' : 'block';
  });
}

const currentPath = window.location.pathname.split('/').pop() || 'index.html';
document.querySelectorAll('.nav-menu a').forEach(link => {
  const href = link.getAttribute('href') || '';
  if (href.endsWith(currentPath)) link.classList.add('active');
});

document.querySelectorAll('[data-current-year]').forEach(el => {
  el.textContent = new Date().getFullYear();
});

const storage = {
  read(key, fallback = null) {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch {
      return fallback;
    }
  },
  write(key, value) {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch {}
  }
};

const defaultProfile = {
  fullName: 'Gia đình Minh Anh',
  email: 'anhchi@email.com',
  phone: '0983 660 988',
  familySize: '3–4 người',
  goal: 'Rau sạch mỗi ngày',
  package: 'Family',
  focus: 'Muốn có webcam, care log rõ và rau sạch ổn định cho cả nhà.',
  gardenName: 'Vườn nhà Minh Anh',
  notifyWindow: '18:00–20:00',
  plantingFocus: 'Rau ăn lá',
  sharedWith: 'Bố mẹ và bé Bin',
  expectation: 'Mỗi tối mở portal xem vườn, cuối tuần xem timelapse cùng cả nhà.',
  onboardingCompleted: false,
  source: 'demo'
};

const authProfileKey = 'aitrongcay.authProfile';
const authSessionKey = 'aitrongcay.authSession';
const authFlowKey = 'aitrongcay.authFlow';

const normalizePhone = value => (value || '').replace(/\D+/g, '');
const getProfile = () => ({ ...defaultProfile, ...(storage.read(authProfileKey, {}) || {}) });
const setProfile = (patch) => {
  const next = { ...getProfile(), ...patch };
  storage.write(authProfileKey, next);
  return next;
};
const setSession = (patch) => {
  const current = storage.read(authSessionKey, {}) || {};
  const next = { ...current, ...patch, updatedAt: new Date().toISOString() };
  storage.write(authSessionKey, next);
  return next;
};
const setFlow = (patch) => {
  const current = storage.read(authFlowKey, {}) || {};
  storage.write(authFlowKey, { ...current, ...patch, updatedAt: new Date().toISOString() });
};

const showFormResult = (form, message, type = 'success') => {
  let notice = form.querySelector('.form-result');
  if (!notice) {
    notice = document.createElement('div');
    notice.className = 'form-result';
    form.appendChild(notice);
  }
  notice.className = `form-result is-${type}`;
  notice.textContent = message;
};

const redirectSoon = (url) => {
  if (!url) return;
  window.setTimeout(() => {
    window.location.href = url;
  }, 900);
};

const formValue = (form, name) => (form.elements.namedItem(name)?.value || '').trim();

const prefillInputs = () => {
  const profile = getProfile();
  document.querySelectorAll('[data-prefill]').forEach(input => {
    const key = input.dataset.prefill;
    if (!key || input.value) return;
    if (profile[key]) input.value = profile[key];
  });
};

prefillInputs();

document.querySelectorAll('[data-fake-submit]').forEach(form => {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    showFormResult(form, 'Đã ghi nhận thông tin của anh/chị. Đội ngũ Ai trồng cây sẽ liên hệ lại để tư vấn gói phù hợp trong thời gian sớm nhất.');
  });
});

const registerHandlers = {
  register(form) {
    const fullName = formValue(form, 'fullName');
    const phone = formValue(form, 'phone');
    const email = formValue(form, 'email');
    const familySize = formValue(form, 'familySize');
    const goal = formValue(form, 'goal');
    const packageName = formValue(form, 'package');
    const focus = formValue(form, 'focus');

    if (!fullName || !phone || !email) {
      showFormResult(form, 'Anh/chị vui lòng điền đủ họ tên, số điện thoại và email để em giữ hồ sơ tư vấn.', 'error');
      return;
    }

    const profile = setProfile({
      fullName,
      phone,
      email,
      familySize,
      goal,
      package: packageName,
      focus,
      source: 'register',
      onboardingCompleted: false
    });

    setFlow({ step: 'registered', lastAction: 'register' });
    showFormResult(form, `Đã lưu hồ sơ cho ${profile.fullName}. Mời anh/chị sang bước onboarding để đặt tên khu vườn và chốt nhịp trải nghiệm.`, 'success');
    redirectSoon(form.dataset.redirect);
  },
  onboarding(form) {
    const gardenName = formValue(form, 'gardenName');
    const notifyWindow = formValue(form, 'notifyWindow');
    const plantingFocus = formValue(form, 'plantingFocus');
    const sharedWith = formValue(form, 'sharedWith');
    const expectation = formValue(form, 'expectation');

    if (!gardenName || !notifyWindow || !plantingFocus) {
      showFormResult(form, 'Anh/chị giúp em điền tên vườn, khung giờ nhận thông báo và ưu tiên gieo trồng để portal cá nhân hoá tốt hơn.', 'error');
      return;
    }

    const profile = setProfile({
      gardenName,
      notifyWindow,
      plantingFocus,
      sharedWith,
      expectation,
      onboardingCompleted: true,
      source: 'onboarding'
    });

    setFlow({ step: 'onboarding-complete', lastAction: 'onboarding' });
    showFormResult(form, `Đã hoàn tất onboarding cho ${profile.gardenName}. Tiếp theo anh/chị đăng nhập để vào portal với hồ sơ vừa cá nhân hoá.`, 'success');
    redirectSoon(form.dataset.redirect);
  },
  login(form) {
    const identity = formValue(form, 'identity');
    const password = formValue(form, 'password');
    const remember = Boolean(form.elements.namedItem('remember')?.checked);
    const profile = getProfile();

    if (!identity) {
      showFormResult(form, 'Anh/chị nhập email hoặc số điện thoại đã dùng khi đăng ký để em nhận diện đúng khu vườn.', 'error');
      return;
    }

    if (password.length < 6) {
      showFormResult(form, 'Mật khẩu mô phỏng nên vẫn cần tối thiểu 6 ký tự để sát luồng thật hơn.', 'error');
      return;
    }

    const identityMatches = [profile.email?.toLowerCase(), normalizePhone(profile.phone)]
      .filter(Boolean)
      .includes(identity.toLowerCase()) || normalizePhone(identity) === normalizePhone(profile.phone);

    if (!identityMatches) {
      showFormResult(form, 'Em chưa thấy hồ sơ khớp trong trình duyệt này. Anh/chị có thể đăng ký mới hoặc dùng đúng email / số điện thoại đã điền trước đó.', 'error');
      return;
    }

    setSession({
      loggedIn: true,
      remember,
      identity,
      fullName: profile.fullName,
      gardenName: profile.gardenName || defaultProfile.gardenName,
      onboardingCompleted: !!profile.onboardingCompleted
    });
    setFlow({ step: 'logged-in', lastAction: 'login' });

    const nextCopy = profile.onboardingCompleted
      ? `Đăng nhập thành công. Đang mở ${profile.gardenName || 'khu vườn của anh/chị'} trong portal...`
      : 'Đăng nhập thành công. Portal vẫn mở được ngay, và em sẽ nhắc anh/chị hoàn tất onboarding ở bước tiếp theo.';
    showFormResult(form, nextCopy, 'success');
    redirectSoon(form.dataset.redirect);
  }
};

document.querySelectorAll('[data-auth-form]').forEach(form => {
  const type = form.dataset.authForm;
  if (!registerHandlers[type]) return;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    registerHandlers[type](form);
  });
});

document.querySelectorAll('[data-auth-provider="google"]').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const page = window.location.pathname;
    const msg = page.includes('/login')
      ? 'Đăng nhập Google sẽ được nối ở bước user system thật. Hiện tại anh/chị có thể dùng email/số điện thoại đã đăng ký để trải nghiệm luồng portal.'
      : 'Đăng ký Google sẽ được bật khi website nối sang hệ thống tài khoản thật. Hiện tại form này đã đủ gần luồng production để anh/chị trải nghiệm.';
    window.alert(msg);
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
  const panels = wrapper.querySelectorAll('[data-tab-panel]');
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

const personalizePortal = () => {
  const session = storage.read(authSessionKey, {}) || {};
  const profile = getProfile();
  const activeName = session.fullName || profile.fullName || defaultProfile.fullName;
  const gardenName = session.gardenName || profile.gardenName || defaultProfile.gardenName;
  const sharedWith = profile.sharedWith || defaultProfile.sharedWith;
  const familyCount = sharedWith ? String(sharedWith.split(',').filter(Boolean).length) : '3';
  const onboardingReady = profile.onboardingCompleted;

  const setText = (selector, value) => {
    const el = document.querySelector(selector);
    if (el && value) el.textContent = value;
  };

  setText('[data-portal-garden-label]', `${gardenName} • ${profile.package || defaultProfile.package} plan`);
  setText('[data-portal-headline]', `Tổng quan ${gardenName}`);
  setText('[data-portal-subline]', onboardingReady
    ? `Xin chào ${activeName}. Mọi điểm chạm quan trọng của ${gardenName} đều bắt đầu từ đây: webcam, care log, hồ sơ chất lượng và nhịp sống của cả nhà.`
    : `Xin chào ${activeName}. Portal vẫn mở được ngay, và phần cá nhân hoá sâu hơn sẽ hoàn thiện sau khi anh/chị điền nốt onboarding.`);
  setText('[data-portal-welcome-title]', onboardingReady ? `Chào mừng trở lại, ${activeName}` : `Xin chào ${activeName}`);
  setText('[data-portal-welcome-copy]', onboardingReady
    ? `${gardenName} đã sẵn sàng: tên vườn, người cùng theo dõi và nhịp thông báo đã được ghi nhớ trong trải nghiệm mô phỏng này.`
    : 'Anh/chị đã vào được portal. Nếu muốn cảm giác “đây đúng là khu vườn của nhà mình” rõ hơn, chỉ cần hoàn tất onboarding.' );
  setText('[data-portal-ambient]', onboardingReady
    ? `${gardenName} đang đi đúng nhịp — lá mở đều, môi trường ổn định và cả nhà có thể yên tâm mở portal theo dõi mỗi ngày.`
    : 'Khu vườn đang đi đúng nhịp và sẵn sàng cho bước cá nhân hoá tiếp theo.' );
  setText('[data-portal-hero-title]', onboardingReady
    ? `${activeName} ơi, ${gardenName} hôm nay trông rất ổn và rất dễ chịu để theo dõi`
    : `Chào mừng ${activeName} — khu vườn mẫu hôm nay trông rất ổn và rất dễ chịu để theo dõi`);
  setText('[data-portal-hero-copy]', onboardingReady
    ? `AI summary: môi trường ổn định, ${profile.plantingFocus || 'rau ăn lá'} đang lên đều, ETA thu hoạch 6 ngày. Khung giờ gợi ý mở portal: ${profile.notifyWindow || defaultProfile.notifyWindow}.`
    : 'AI summary: môi trường ổn định, lá non đều, ETA thu hoạch 6 ngày. Hoàn tất onboarding để dashboard nhắc đúng nhịp phù hợp gia đình hơn.');
  setText('[data-portal-members]', `${familyCount} người`);
  setText('[data-portal-viewer-count]', `${familyCount} người`);
  setText('[data-portal-activity-1]', onboardingReady ? `${activeName} vừa mở lại timelapse sáng nay` : 'Khách vừa vào portal mẫu sáng nay');
  setText('[data-portal-activity-2]', sharedWith ? `${sharedWith} đã được thêm vào danh sách cùng theo dõi` : 'Danh sách thành viên xem chung đang chờ cập nhật');
  setText('[data-portal-activity-3]', onboardingReady ? `AI gardener đã chuẩn bị summary theo khung ${profile.notifyWindow || defaultProfile.notifyWindow}` : 'AI gardener đã gửi summary đầu ngày');
  setText('[data-portal-sharing]', sharedWith
    ? `${familyCount} thành viên đang được mời cùng xem: ${sharedWith}. Portal sẽ ngày càng “có người thật” hơn khi nối sang user system chính thức.`
    : 'Portal hiện đang ở chế độ mẫu. Khi nối hệ thống thật, danh sách người xem chung sẽ được quản lý bằng tài khoản riêng.');
  setText('[data-portal-soft-note]', onboardingReady
    ? `Nhịp theo dõi phù hợp với gia đình hiện tại: ưu tiên ${profile.plantingFocus || 'rau ăn lá'}, xem summary trong khung ${profile.notifyWindow || defaultProfile.notifyWindow}.`
    : 'Mùa vụ hiện tại đang ở đoạn đẹp nhất để cả nhà theo dõi: thay đổi nhìn thấy rõ từng ngày.');
  setText('[data-portal-camera-label]', onboardingReady ? `Cam 01 • ${gardenName}` : 'Cam 01 • Nhà lưới');
};

if (window.location.pathname.includes('/portal/dashboard.html')) {
  personalizePortal();
}
