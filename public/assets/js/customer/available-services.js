// ===== Tabs / Category Filter =====
const chips = document.querySelectorAll(".chip");
const categoryHeadings = document.querySelectorAll(".category");
const grids = document.querySelectorAll(".service-grid");

function applyFilter(cat) {
  // Toggle active class on chips
  chips.forEach(c => c.classList.toggle("active", c.dataset.filter === cat || (cat === "all" && c.dataset.filter === undefined)));

  // Show/hide categories + grids
  let anyVisible = false;
  grids.forEach(grid => {
    const match = (cat === "all") || (grid.dataset.cat === cat);
    grid.style.display = match ? "grid" : "none";
    // match heading
    const heading = document.querySelector(`.category[data-cat="${grid.dataset.cat}"]`);
    if (heading) heading.style.display = match ? "block" : "none";
    if (match) anyVisible = true;
  });

  // Scroll to top of services on filter change
  document.getElementById("servicesRoot").scrollIntoView({ behavior: "smooth", block: "start" });
}

chips.forEach(chip => {
  chip.addEventListener("click", () => {
    const target = chip.dataset.filter || "all";
    // reset aria on tabs
    chips.forEach(c => c.setAttribute("aria-selected", "false"));
    chip.setAttribute("aria-selected", "true");
    applyFilter(target);
  });
});

// default
applyFilter("all");

// ===== Cart =====
let cart = [];

const cartList = document.getElementById("cart-items");
const subtotalElem = document.getElementById("subtotal");
const taxElem = document.getElementById("tax");
const totalElem = document.getElementById("total");

function updateCart() {
  cartList.innerHTML = "";
  if (!cart.length) {
    cartList.innerHTML = `<li class="muted">No services added yet.</li>`;
    subtotalElem.textContent = "0.00";
    taxElem.textContent = "0.00";
    totalElem.textContent = "0.00";
    return;
  }

  cart.forEach(item => {
    const li = document.createElement("li");
    li.innerHTML = `
      <span>${item.name} ×${item.qty}</span>
      <div>
        $${(item.price * item.qty).toFixed(2)}
        <button class="remove" aria-label="Remove ${item.name}"><i class="fa-solid fa-xmark"></i></button>
      </div>
    `;
    li.querySelector(".remove").addEventListener("click", () => {
      cart = cart.filter(i => i.name !== item.name);
      updateCart();
    });
    cartList.appendChild(li);
  });

  const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const tax = subtotal * 0.08;
  const total = subtotal + tax;
  subtotalElem.textContent = subtotal.toFixed(2);
  taxElem.textContent = tax.toFixed(2);
  totalElem.textContent = total.toFixed(2);
}

document.querySelectorAll(".add").forEach(btn => {
  btn.addEventListener("click", () => {
    const card = btn.closest(".card");
    const name = card.getAttribute("data-name");
    const price = parseFloat(card.getAttribute("data-price") || "0");
    const existing = cart.find(i => i.name === name);
    if (existing) existing.qty += 1;
    else cart.push({ name, price, qty: 1 });
    updateCart();
  });
});

document.getElementById("calculate").addEventListener("click", () => {
  if (!cart.length) return alert("Please add at least one service.");
  alert("Totals updated.");
});

document.getElementById("checkout").addEventListener("click", () => {
  if (!cart.length) return alert("Add some services before booking.");
  const summary = cart.map(i => `${i.name} ×${i.qty} — $${(i.price * i.qty).toFixed(2)}`).join("\n");
  alert(`Booking Summary:\n\n${summary}\n\nTotal: $${totalElem.textContent}`);
});
document.addEventListener('DOMContentLoaded', () => {
  // ===== Tabs / Category Filter =====
  const chips = document.querySelectorAll(".chip");
  const grids = document.querySelectorAll(".service-grid");

  function applyFilter(cat) {
    chips.forEach(c => c.classList.toggle(
      "active",
      c.dataset.filter === cat || (cat === "all" && (c.dataset.filter ?? "all") === "all")
    ));

    grids.forEach(grid => {
      const match = (cat === "all") || (grid.dataset.cat === cat);
      grid.style.display = match ? "grid" : "none";
      const heading = document.querySelector(`.category[data-cat="${grid.dataset.cat}"]`);
      if (heading) heading.style.display = match ? "block" : "none";
    });

    document.getElementById("servicesRoot")?.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  chips.forEach(chip => {
    chip.addEventListener("click", () => {
      const target = chip.dataset.filter || "all";
      chips.forEach(c => c.setAttribute("aria-selected", "false"));
      chip.setAttribute("aria-selected", "true");
      applyFilter(target);
    });
  });

  applyFilter("all");

  // ===== Cart =====
  let cart = [];
  const cartList = document.getElementById("cart-items");
  const subtotalElem = document.getElementById("subtotal");
  const taxElem = document.getElementById("tax");
  const totalElem = document.getElementById("total");

  function updateCart() {
    if (!cartList || !subtotalElem || !taxElem || !totalElem) return;

    cartList.innerHTML = "";
    if (!cart.length) {
      cartList.innerHTML = `<li class="muted">No services added yet.</li>`;
      subtotalElem.textContent = "0.00";
      taxElem.textContent = "0.00";
      totalElem.textContent = "0.00";
      return;
    }

    cart.forEach(item => {
      const li = document.createElement("li");
      li.innerHTML = `
        <span>${item.name} ×${item.qty}</span>
        <div>
          $${(item.price * item.qty).toFixed(2)}
          <button class="remove" aria-label="Remove ${item.name}"><i class="fa-solid fa-xmark"></i></button>
        </div>
      `;
      li.querySelector(".remove").addEventListener("click", () => {
        cart = cart.filter(i => i.name !== item.name);
        updateCart();
      });
      cartList.appendChild(li);
    });

    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const tax = subtotal * 0.08;
    const total = subtotal + tax;
    subtotalElem.textContent = subtotal.toFixed(2);
    taxElem.textContent = tax.toFixed(2);
    totalElem.textContent = total.toFixed(2);
  }

  document.querySelectorAll(".add").forEach(btn => {
    btn.addEventListener("click", () => {
      const card = btn.closest(".card");
      const name = card.getAttribute("data-name");
      const price = parseFloat(card.getAttribute("data-price") || "0");
      const existing = cart.find(i => i.name === name);
      if (existing) existing.qty += 1;
      else cart.push({ name, price, qty: 1 });
      updateCart();
    });
  });

  document.getElementById("calculate")?.addEventListener("click", () => {
    if (!cart.length) return alert("Please add at least one service.");
    alert("Totals updated.");
  });

document.getElementById("checkout")?.addEventListener("click", () => {
  if (!cart.length) return alert("Add some services before booking.");
  const base = (typeof BASE_URL!=='undefined') ? BASE_URL.replace(/\/+$/,'') : '';
  const branch = (typeof BRANCH_CODE!=='undefined' ? BRANCH_CODE : '');
  const items = encodeURIComponent(JSON.stringify(cart));  // name, price, qty
  const url = `${base}/customer/book?branch=${encodeURIComponent(branch)}&items=${items}`;
  location.href = url;
});


  // Optional: BRANCH_CODE is available if you want to tailor services later
  // console.log('Branch:', typeof BRANCH_CODE !== 'undefined' ? BRANCH_CODE : '(none)');
});
