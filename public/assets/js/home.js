/* home.js
   - Populate services + testimonials (can be replaced by API)
   - Testimonial carousel + autoplay
   - Mobile nav toggle
   - “Services” modal open/close + redirect to /services/available
*/

document.addEventListener('DOMContentLoaded', () => {
  /* ==========================
     SAMPLE DATA (Replace with API calls)
     ========================== */
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

  /* ==========================
     Populate services grid
     ========================== */
  function renderServices() {
    const grid = document.getElementById('servicesGrid');
    if (!grid) return;
    grid.innerHTML = '';
    servicesData.forEach(s => {
      const el = document.createElement('div');
      el.className = 'card-service';
      el.innerHTML = `
        <h3>${s.title}</h3>
        <p>${s.desc}</p>
        <div class="underline"></div>
      `;
      grid.appendChild(el);
    });
  }

  /* ==========================
     Testimonial carousel logic
     ========================== */
  let currentIndex = 0;
  const testimonialEl = document.getElementById('testimonial');
  const dotsEl = document.getElementById('dots');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  function renderTestimonial(index) {
    if (!testimonialEl || !dotsEl) return;
    const t = testimonials[index];
    testimonialEl.innerHTML = `
      <div class="author">${t.author}</div>
      <div class="role">${t.role}</div>
      <div class="text">${t.text}</div>
    `;
    const dots = Array.from(dotsEl.children);
    dots.forEach((d,i)=> d.classList.toggle('active', i===index));
  }

  function createDots(){
    if(!dotsEl) return;
    dotsEl.innerHTML='';
    testimonials.forEach((_,i)=>{
      const dot = document.createElement('div');
      dot.className='dot' + (i===0? ' active':'');
      dot.addEventListener('click', ()=> {
        currentIndex = i;
        renderTestimonial(currentIndex);
      });
      dotsEl.appendChild(dot);
    });
  }

  prevBtn && prevBtn.addEventListener('click', ()=>{
    currentIndex = (currentIndex -1 + testimonials.length) % testimonials.length;
    renderTestimonial(currentIndex);
  });

  nextBtn && nextBtn.addEventListener('click', ()=>{
    currentIndex = (currentIndex +1) % testimonials.length;
    renderTestimonial(currentIndex);
  });

  // autoplay (only if testimonial container exists)
  if (testimonialEl) {
    setInterval(()=> {
      currentIndex = (currentIndex +1) % testimonials.length;
      renderTestimonial(currentIndex);
    }, 6000);
  }

  /* ==========================
     Mobile nav toggle
     ========================== */
  const navToggle = document.getElementById('navToggle');
  const mainNav = document.getElementById('mainNav');
  navToggle && navToggle.addEventListener('click', ()=> {
    mainNav && mainNav.classList.toggle('open');
  });

  /* ==========================
     “Services” modal + redirect
     ========================== */
  const servicesLinks = [
    document.getElementById('servicesNavLink'),
    document.getElementById('servicesFooterLink')
  ].filter(Boolean);

  const modal    = document.getElementById('servicesModal');
  const closeBtn = modal ? modal.querySelector('.close-btn') : null;

  servicesLinks.forEach(link => {
    link.addEventListener('click', e => {
      if (!modal) return;
      e.preventDefault();
      modal.style.display = 'flex';
      modal.setAttribute('aria-hidden','false');
    });
  });

  closeBtn && closeBtn.addEventListener('click', () => {
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden','true');
  });

  window.addEventListener('click', e => {
    if (modal && e.target === modal){
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden','true');
    }
  });

  // Continue → redirect to services page (controller to be implemented)
  const goBtn = document.getElementById('goToServices');
  goBtn && goBtn.addEventListener('click', () => {
    const sel = (document.getElementById('branchSelect') || {}).value;
    if(!sel){ alert('Please select a branch first.'); return; }
    // Uses BASE_URL injected from PHP view
    const base = (typeof BASE_URL !== 'undefined') ? BASE_URL.replace(/\/+$/,'') : '';
    location.href = `${base}/services/available?branch=${encodeURIComponent(sel)}`;
  });

  /* ==========================
     Initialize
     ========================== */
  renderServices();
  createDots();
  renderTestimonial(0);

  /* ==========================
     NOTES for dynamic integration:
     - Replace `servicesData` and `testimonials` with fetch() calls
       e.g. fetch(`${BASE_URL}/api/services`).then(r=>r.json()).then(d=>{ servicesData = d; renderServices(); });
  ========================== */
});
