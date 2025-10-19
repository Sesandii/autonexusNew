document.addEventListener('DOMContentLoaded', () => {
  // --- sample data; swap to fetch() later
  const servicesData = [
    { title:'Oil Change', desc:'Regular oil changes to keep your engine running smoothly.' },
    { title:'Engine Diagnostics', desc:'Advanced diagnostic tools to identify engine issues quickly.' },
    { title:'Battery Service', desc:'Testing and replacement services for all battery types.' },
    { title:'Brake Service', desc:'Inspection and repair of your vehicle’s braking system.' },
    { title:'Tire Service', desc:'Rotation, balancing, and replacement of worn tires.' },
    { title:'General Maintenance', desc:'Complete vehicle maintenance to ensure optimal performance.' }
  ];
  const testimonials = [
    { author:'Michael Johnson', role:'Business Owner', text:'AutoNexus simplified my fleet maintenance process. Real-time tracking saves me hours of follow-up calls.' },
    { author:'Priya Sen', role:'Car Owner', text:'Professional mechanics and clear pricing — I trust them with my vehicle.' },
    { author:'D. Kumar', role:'Delivery Driver', text:'Fast service and helpful updates. Highly recommended.' }
  ];

  // services grid
  function renderServices(){
    const grid = document.getElementById('servicesGrid');
    if(!grid) return;
    grid.innerHTML = '';
    servicesData.forEach(s=>{
      const el = document.createElement('div');
      el.className = 'card-service';
      el.innerHTML = `<h3>${s.title}</h3><p>${s.desc}</p><div class="underline"></div>`;
      grid.appendChild(el);
    });
  }

  // testimonials
  let idx = 0;
  const tEl = document.getElementById('testimonial');
  const dots = document.getElementById('dots');
  const prev = document.getElementById('prevBtn');
  const next = document.getElementById('nextBtn');

  function paint(i){
    if(!tEl || !dots) return;
    const t = testimonials[i];
    tEl.innerHTML = `<div class="author">${t.author}</div><div class="role">${t.role}</div><div class="text">${t.text}</div>`;
    [...dots.children].forEach((d,di)=>d.classList.toggle('active', di===i));
  }
  function mkDots(){
    if(!dots) return;
    dots.innerHTML='';
    testimonials.forEach((_,i)=>{
      const d=document.createElement('div');
      d.className='dot'+(i===0?' active':'');
      d.addEventListener('click', ()=>{ idx=i; paint(idx); });
      dots.appendChild(d);
    });
  }
  prev && prev.addEventListener('click', ()=>{ idx=(idx-1+testimonials.length)%testimonials.length; paint(idx); });
  next && next.addEventListener('click', ()=>{ idx=(idx+1)%testimonials.length; paint(idx); });
  if (tEl) setInterval(()=>{ idx=(idx+1)%testimonials.length; paint(idx); }, 6000);

  // modal open/close
  const servicesLinks = [
    document.getElementById('servicesNavLink'),
    document.getElementById('servicesFooterLink')
  ].filter(Boolean);
  const modal = document.getElementById('servicesModal');
  const closeBtn = modal ? modal.querySelector('.close-btn') : null;

  servicesLinks.forEach(a=>{
    a.addEventListener('click', e=>{
      if(!modal) return;
      e.preventDefault();
      modal.style.display='flex';
      modal.setAttribute('aria-hidden','false');
    });
  });
  closeBtn && closeBtn.addEventListener('click', ()=>{
    modal.style.display='none';
    modal.setAttribute('aria-hidden','true');
  });
  window.addEventListener('click', e=>{
    if(modal && e.target===modal){
      modal.style.display='none';
      modal.setAttribute('aria-hidden','true');
    }
  });

  // continue -> services page (logged-in flow)
  const goBtn = document.getElementById('goToServices');
  goBtn && goBtn.addEventListener('click', ()=>{
    const sel = (document.getElementById('branchSelect')||{}).value;
    if(!sel){ alert('Please select a branch first.'); return; }
    const base = (typeof BASE_URL!=='undefined') ? BASE_URL.replace(/\/+$/,'') : '';
    location.href = `${base}/services/available?branch=${encodeURIComponent(sel)}`;
  });

  // init
  renderServices(); mkDots(); paint(0);
});
