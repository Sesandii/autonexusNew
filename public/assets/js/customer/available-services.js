document.addEventListener("DOMContentLoaded", () => {
  const chips = Array.from(document.querySelectorAll(".chip"));
  const grids = Array.from(document.querySelectorAll(".service-grid"));
  const servicesRoot = document.getElementById("servicesRoot");

  function applyFilter(category) {
    chips.forEach((chip) => {
      chip.classList.toggle("active", chip.dataset.filter === category);
    });

    grids.forEach((grid) => {
      const match = category === "all" || grid.dataset.cat === category;
      grid.style.display = match ? "grid" : "none";

      const heading = document.querySelector(`.category[data-cat="${grid.dataset.cat}"]`);
      if (heading) {
        heading.style.display = match ? "block" : "none";
      }
    });

    if (servicesRoot) {
      servicesRoot.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }

  chips.forEach((chip) => {
    chip.addEventListener("click", () => {
      const target = chip.dataset.filter || "all";
      chips.forEach((c) => c.setAttribute("aria-selected", "false"));
      chip.setAttribute("aria-selected", "true");
      applyFilter(target);
    });
  });

  applyFilter("all");

  const cartList = document.getElementById("cart-items");
  const subtotalElem = document.getElementById("subtotal");
  const taxElem = document.getElementById("tax");
  const totalElem = document.getElementById("total");
  const calculateBtn = document.getElementById("calculate");
  const checkoutBtn = document.getElementById("checkout");
  const addButtons = Array.from(document.querySelectorAll(".add"));

  let cart = [];

  function parsePriceToCents(card) {
    const centsAttr = Number.parseInt(card.getAttribute("data-price-cents") || "0", 10);
    if (Number.isFinite(centsAttr) && centsAttr > 0) {
      return centsAttr;
    }

    const raw = String(card.getAttribute("data-price") || "0");
    const normalized = raw.replace(/,/g, "").trim();
    const amount = Number.parseFloat(normalized);

    if (!Number.isFinite(amount) || amount < 0) {
      return 0;
    }

    return Math.round(amount * 100);
  }

  function formatAmount(cents) {
    return (cents / 100).toFixed(2);
  }

  function isSameItem(a, serviceId, name) {
    if (a.serviceId > 0 && serviceId > 0) {
      return a.serviceId === serviceId;
    }
    return a.name === name;
  }

  function renderCart() {
    if (!cartList || !subtotalElem || !taxElem || !totalElem) {
      return;
    }

    cartList.innerHTML = "";

    if (!cart.length) {
      cartList.innerHTML = '<li class="muted">No services added yet.</li>';
      subtotalElem.textContent = "0.00";
      taxElem.textContent = "0.00";
      totalElem.textContent = "0.00";
      return;
    }

    let subtotalCents = 0;

    cart.forEach((item) => {
      const rowTotalCents = item.unitPriceCents * item.qty;
      subtotalCents += rowTotalCents;

      const li = document.createElement("li");

      const left = document.createElement("span");
      left.textContent = `${item.name} x${item.qty}`;

      const right = document.createElement("div");
      right.innerHTML = `
        $${formatAmount(rowTotalCents)}
        <button class="remove" aria-label="Remove ${item.name}"><i class="fa-solid fa-xmark"></i></button>
      `;

      const removeBtn = right.querySelector(".remove");
      if (removeBtn) {
        removeBtn.addEventListener("click", () => {
          cart = cart.filter((i) => !isSameItem(i, item.serviceId, item.name));
          renderCart();
        });
      }

      li.appendChild(left);
      li.appendChild(right);
      cartList.appendChild(li);
    });

    const taxCents = Math.round(subtotalCents * 0.08);
    const totalCents = subtotalCents + taxCents;

    subtotalElem.textContent = formatAmount(subtotalCents);
    taxElem.textContent = formatAmount(taxCents);
    totalElem.textContent = formatAmount(totalCents);
  }

  addButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const card = button.closest(".card");
      if (!card) {
        return;
      }

      const name = String(card.getAttribute("data-name") || "Service").trim();
      const serviceId = Number.parseInt(card.getAttribute("data-service-id") || "0", 10) || 0;
      const unitPriceCents = parsePriceToCents(card);

      const existing = cart.find((item) => isSameItem(item, serviceId, name));
      if (existing) {
        existing.qty += 1;
      } else {
        cart.push({ serviceId, name, unitPriceCents, qty: 1 });
      }

      renderCart();
    });
  });

  if (calculateBtn) {
    calculateBtn.addEventListener("click", () => {
      renderCart();
      if (!cart.length) {
        alert("Please add at least one service.");
      }
    });
  }

  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", () => {
      if (!cart.length) {
        alert("Add some services before booking.");
        return;
      }

      const base = typeof BASE_URL === "string" ? BASE_URL.replace(/\/+$/, "") : "";
      const branch = typeof BRANCH_CODE === "string" ? BRANCH_CODE.trim() : "";

      if (!branch) {
        alert("Branch is missing. Please open Available Services from a branch and try again.");
        return;
      }

      const firstWithServiceId = cart.find((item) => item.serviceId > 0);
      const selectedServiceId = firstWithServiceId ? firstWithServiceId.serviceId : 0;
      const selectedServiceIds = [...new Set(cart.map((item) => item.serviceId).filter((id) => id > 0))];

      const itemsPayload = cart.map((item) => ({
        service_id: item.serviceId,
        name: item.name,
        qty: item.qty,
        unit_price: formatAmount(item.unitPriceCents),
      }));

      const params = new URLSearchParams();
      params.set("branch", branch);
      if (selectedServiceId > 0) {
        params.set("service_id", String(selectedServiceId));
      }
      if (selectedServiceIds.length > 0) {
        params.set("service_ids", selectedServiceIds.join(","));
      }
      params.set("items", JSON.stringify(itemsPayload));

      window.location.href = `${base}/customer/book?${params.toString()}`;
    });
  }

  renderCart();
});
