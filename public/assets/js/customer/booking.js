document.addEventListener('DOMContentLoaded', () => {
  // ----- Expand / collapse accordion
  const acc = document.getElementById('serviceAccordion');
  document.getElementById('expandAll')?.addEventListener('click', () => {
    acc?.querySelectorAll('details').forEach(d => d.setAttribute('open', 'open'));
  });
  document.getElementById('collapseAll')?.addEventListener('click', () => {
    acc?.querySelectorAll('details').forEach(d => d.removeAttribute('open'));
  });

  // ----- Pre-select services from query (?items=[{name,price,qty}])
  try {
    let items = [];
    if (typeof PRESELECTED_ITEMS === 'string' && PRESELECTED_ITEMS.trim().length) {
      items = JSON.parse(PRESELECTED_ITEMS);
    } else if (Array.isArray(PRESELECTED_ITEMS)) {
      items = PRESELECTED_ITEMS;
    } else if (typeof PRESELECTED_ITEMS === 'object') {
      items = [PRESELECTED_ITEMS];
    }
    const names = new Set(items.map(i => i.name));
    document.querySelectorAll('.acc-panel input[type="checkbox"]').forEach(cb => {
      if (names.has(cb.dataset.name)) cb.checked = true;
    });
  } catch(e){ /* ignore */ }

  // ----- Simple slot generator
  const amWrap = document.querySelector('.slot__sessions[data-period="am"]');
  const pmWrap = document.querySelector('.slot__sessions[data-period="pm"]');

  function makeBtn(label, disabled=false, cls=''){
    const b = document.createElement('button');
    b.className = 'session' + (cls ? ' ' + cls : '');
    b.textContent = label;
    if (disabled) {
      b.classList.add('is-reserved');
      b.disabled = true;
    } else {
      b.addEventListener('click', () => {
        document.querySelectorAll('.session.is-selected').forEach(s=>s.classList.remove('is-selected'));
        b.classList.add('is-selected');
      });
    }
    return b;
  }

  function renderSlots(){
    if (!amWrap || !pmWrap) return;
    amWrap.innerHTML = ''; pmWrap.innerHTML = '';
    // dummy slots – replace with API if needed
    ['08:30','09:00','09:30','10:00','10:30','11:00'].forEach((t,i)=>{
      amWrap.appendChild(makeBtn(t, i===2)); // mark one reserved
    });
    ['13:00','13:30','14:00','14:30','15:00','15:30'].forEach((t,i)=>{
      pmWrap.appendChild(makeBtn(t, i===4));
    });
  }

  document.getElementById('date')?.addEventListener('change', renderSlots);
  renderSlots();

  // ----- Book Now
  document.getElementById('bookNow')?.addEventListener('click', () => {
    const branch = document.querySelector('input[name="branch"]:checked')?.value || SELECTED_BRANCH_CODE || '';
    const services = [...document.querySelectorAll('.acc-panel input[type="checkbox"]:checked')]
      .map(cb => ({ name: cb.dataset.name, price: parseFloat(cb.dataset.price||'0') }));
    const date = document.getElementById('date')?.value || '';
    const slot = document.querySelector('.session.is-selected')?.textContent || '';

    if (!branch) return toast('Please select a branch.');
    if (!services.length) return toast('Please select at least one service.');
    if (!date) return toast('Please choose a date.');
    if (!slot) return toast('Please choose a time slot.');

    // TODO: POST to backend
    // fetch(`${BASE_URL}/customer/book`, { method:'POST', headers:{'Content-Type':'application/json'},
    //   body: JSON.stringify({ branch, services, date, slot })})

    toast('Booking created! (demo)');
  });

  function toast(msg){
    const el = document.getElementById('toast');
    if (!el) return alert(msg);
    el.textContent = msg;
    el.classList.add('show');
    setTimeout(()=> el.classList.remove('show'), 2200);
  }
});
