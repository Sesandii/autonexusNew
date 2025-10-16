<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel - Customers Management</title>

  <!-- Shared neutral styles -->
  <link rel="stylesheet" href="../admin-shared/management.css">
  <!-- Sidebar styles -->
  <link rel="stylesheet" href="../admin-sidebar/styles.css">
  <!-- Icons (optional) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
  </style>
</head>
<body>
  <?php include("../admin-sidebar/sidebar.php"); ?>

  <main class="main-content">
    <div class="management">
      <div class="management-header">
        <h2>Customers Management</h2>

        <div class="tools">
          <input type="text" class="search-input" id="searchInput" placeholder="Search by customer id/name..." />
          <select class="status-filter" id="statusFilter">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
          </select>
          <button class="add-btn" id="addCustomerBtn">+ Add New Customer</button>
        </div>
      </div>

      <table id="customersTable">
        <thead>
          <tr>
            <th>Customer ID</th>
            <th>Full Name</th>
            <th>NIC</th>
            <th>Email</th>
            <th>Contact Number</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr data-status="active">
            <td>CUST001</td>
            <td>John Smith</td>
            <td>200312345678</td>
            <td>abc@email.com</td>
            <td>(555) 123-4567</td>
            <td class="status--active">Active</td>
            <td>2025-08-10</td>
            <td>
              <button class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
              <button class="icon-btn" title="Edit"><i class="fas fa-pen"></i></button>
              <button class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>

          <tr data-status="inactive">
            <td>CUST002</td>
            <td>Jane Doe</td>
            <td>200412345679</td>
            <td>jane.doe@email.com</td>
            <td>(555) 234-5678</td>
            <td class="status--inactive">Inactive</td>
            <td>2025-07-20</td>
            <td>
              <button class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
              <button class="icon-btn" title="Edit"><i class="fas fa-pen"></i></button>
              <button class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>

          <tr data-status="active">
            <td>CUST003</td>
            <td>Michael Johnson</td>
            <td>200512345680</td>
            <td>m.johnson@email.com</td>
            <td>(555) 345-6789</td>
            <td class="status--active">Active</td>
            <td>2025-06-15</td>
            <td>
              <button class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
              <button class="icon-btn" title="Edit"><i class="fas fa-pen"></i></button>
              <button class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>

          <tr data-status="pending">
            <td>CUST004</td>
            <td>Emily Davis</td>
            <td>200612345681</td>
            <td>emily.davis@email.com</td>
            <td>(555) 456-7890</td>
            <td class="status--pending">Pending</td>
            <td>2025-09-01</td>
            <td>
              <button class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
              <button class="icon-btn" title="Edit"><i class="fas fa-pen"></i></button>
              <button class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>

          <!-- more rows ... -->
        </tbody>
      </table>
    </div>

    <!-- ========== View Customer Modal ========== -->
    <div class="modal" id="customerModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>

      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="custModalTitle">Customer Details</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>

        <div class="modal__body">
          <div class="modal__status">
            <span id="custModalStatus" class="status--active">Active</span>
          </div>

          <div class="modal__grid">
            <div>
              <p class="label">Customer ID</p>
              <p id="v_customerId">—</p>
            </div>
            <div>
              <p class="label">Full Name</p>
              <p id="v_fullName">—</p>
            </div>
            <div>
              <p class="label">NIC</p>
              <p id="v_nic">—</p>
            </div>
            <div>
              <p class="label">Email</p>
              <p id="v_email">—</p>
            </div>
            <div>
              <p class="label">Contact</p>
              <p id="v_contact">—</p>
            </div>
            <div>
              <p class="label">Created At</p>
              <p id="v_created">—</p>
            </div>
          </div>

          <div class="modal__section">
            <p class="label">Notes</p>
            <p id="v_notes">—</p>
          </div>
        </div>

        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Close</button>
          <button type="button" class="btn-primary" id="openEditCustomerFromView">
            <i class="fas fa-pen"></i> Edit Customer
          </button>
        </footer>
      </div>
    </div>

    <!-- ========== Edit / Update Customer Modal ========== -->
    <div class="modal" id="editCustomerModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>

      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="editCustModalTitle">Edit Customer</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>

        <form class="modal__body" id="editCustomerForm" method="post" action="#">
          <div class="modal__status">
            <span id="editCustStatusPill" class="status--active">Active</span>
          </div>

          <div class="modal__grid">
            <div>
              <p class="label">Customer ID</p>
              <input id="e_customerId" name="customer_id" type="text" class="input" readonly>
            </div>
            <div>
              <p class="label">Full Name</p>
              <input id="e_fullName" name="full_name" type="text" class="input" required>
            </div>
            <div>
              <p class="label">NIC</p>
              <input id="e_nic" name="nic" type="text" class="input" required>
            </div>
            <div>
              <p class="label">Email</p>
              <input id="e_email" name="email" type="email" class="input">
            </div>
            <div>
              <p class="label">Contact</p>
              <input id="e_contact" name="contact" type="text" class="input">
            </div>
            <div>
              <p class="label">Status</p>
              <select id="e_status" name="status" class="input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <div>
              <p class="label">Created At</p>
              <input id="e_created" name="created_at" type="date" class="input">
            </div>
            <div style="grid-column:1/-1;">
              <p class="label">Notes</p>
              <textarea id="e_notes" name="notes" class="input" rows="3"></textarea>
            </div>
          </div>
        </form>

        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="editCustomerForm" class="btn-primary">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </footer>
      </div>
    </div>

    <!-- ========== Add / Create Customer Modal ========== -->
    <div class="modal" id="addCustomerModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>

      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="addCustModalTitle">Add New Customer</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>

        <form class="modal__body" id="addCustomerForm" method="post" action="#">
          <div class="modal__status">
            <span id="addCustStatusPill" class="status--active">Active</span>
          </div>

          <div class="modal__grid">
            <!-- If auto-generated server-side, you can hide this -->
            <div>
              <p class="label">Customer Code</p>
              <input id="a_customerCode" name="customer_code" type="text" class="input" placeholder="e.g. CUST010">
            </div>
            <div>
              <p class="label">Full Name</p>
              <input id="a_fullName" name="full_name" type="text" class="input" required>
            </div>
            <div>
              <p class="label">NIC</p>
              <input id="a_nic" name="nic" type="text" class="input" required>
            </div>
            <div>
              <p class="label">Email</p>
              <input id="a_email" name="email" type="email" class="input">
            </div>
            <div>
              <p class="label">Contact</p>
              <input id="a_contact" name="contact" type="text" class="input">
            </div>
            <div>
              <p class="label">Status</p>
              <select id="a_status" name="status" class="input">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <div>
              <p class="label">Created At</p>
              <input id="a_created" name="created_at" type="date" class="input">
            </div>
            <div style="grid-column:1/-1;">
              <p class="label">Notes</p>
              <textarea id="a_notes" name="notes" class="input" rows="3"></textarea>
            </div>
          </div>
        </form>

        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="addCustomerForm" class="btn-primary">
            <i class="fas fa-plus"></i> Create Customer
          </button>
        </footer>
      </div>
    </div>
  </main>

  <script>
  (function(){
    // Helpers
    var $  = function(sel, root){ return (root||document).querySelector(sel); };
    var $$ = function(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); };
    var setText = function(el, val){ if(el) el.textContent = (val && String(val).trim()) || '—'; };

    /* =======================
     * VIEW MODAL (Customers)
     * ======================= */
    var viewModal   = document.getElementById('customerModal');
    var viewCloseEl = $$('#customerModal [data-close-modal]');
    var viewTitle   = document.getElementById('custModalTitle');
    var viewStatus  = document.getElementById('custModalStatus');
    var v = {
      id:      document.getElementById('v_customerId'),
      name:    document.getElementById('v_fullName'),
      nic:     document.getElementById('v_nic'),
      email:   document.getElementById('v_email'),
      contact: document.getElementById('v_contact'),
      created: document.getElementById('v_created'),
      notes:   document.getElementById('v_notes')
    };
    var lastViewedRow = null;

    function openViewFromRow(tr){
      lastViewedRow = tr;

      var status = tr.dataset.status || (tr.querySelector('.status--inactive') ? 'inactive'
                         : tr.querySelector('.status--pending') ? 'pending' : 'active');
      viewStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);
      viewStatus.className = status === 'inactive' ? 'status--inactive'
                             : status === 'pending' ? 'status--pending' : 'status--active';

      setText(viewTitle, (tr.children[1] && tr.children[1].textContent) || 'Customer Details');
      setText(v.id,      tr.children[0] && tr.children[0].textContent);
      setText(v.name,    tr.children[1] && tr.children[1].textContent);
      setText(v.nic,     tr.children[2] && tr.children[2].textContent);
      setText(v.email,   tr.children[3] && tr.children[3].textContent);
      setText(v.contact, tr.children[4] && tr.children[4].textContent);
      setText(v.created, tr.children[6] && tr.children[6].textContent);
      // Optional: tr.dataset.notes if you later add data-notes on row
      setText(v.notes,   tr.dataset.notes);

      viewModal.classList.add('is-open');
      viewModal.setAttribute('aria-hidden', 'false');
    }

    function closeView(){
      viewModal.classList.remove('is-open');
      viewModal.setAttribute('aria-hidden', 'true');
    }

    // Wire up "View" buttons
    $$('#customersTable tbody tr').forEach(function(tr){
      var btn = tr.querySelector('.icon-btn[title="View"]');
      if(btn) btn.addEventListener('click', function(){ openViewFromRow(tr); });
    });

    // Close handlers for view modal
    viewCloseEl.forEach(function(el){ el.addEventListener('click', closeView); });
    viewModal.addEventListener('click', function(e){ if(e.target.matches('.modal__backdrop')) closeView(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && viewModal.classList.contains('is-open')) closeView(); });

    /* =======================
     * EDIT MODAL (Customers)
     * ======================= */
    var editModal      = document.getElementById('editCustomerModal');
    var editCloseEl    = $$('#editCustomerModal [data-close-modal]');
    var editTitle      = document.getElementById('editCustModalTitle');
    var editStatusPill = document.getElementById('editCustStatusPill');

    var ei = {
      id:      document.getElementById('e_customerId'),
      name:    document.getElementById('e_fullName'),
      nic:     document.getElementById('e_nic'),
      email:   document.getElementById('e_email'),
      contact: document.getElementById('e_contact'),
      status:  document.getElementById('e_status'),
      created: document.getElementById('e_created'),
      notes:   document.getElementById('e_notes')
    };

    function openEditFromRow(tr){
      var id      = tr.children[0] && tr.children[0].textContent.trim();
      var name    = tr.children[1] && tr.children[1].textContent.trim();
      var nic     = tr.children[2] && tr.children[2].textContent.trim();
      var email   = tr.children[3] && tr.children[3].textContent.trim();
      var contact = tr.children[4] && tr.children[4].textContent.trim();
      var created = tr.children[6] && tr.children[6].textContent.trim();
      var status  = tr.dataset.status || (tr.querySelector('.status--inactive') ? 'inactive'
                       : tr.querySelector('.status--pending') ? 'pending' : 'active');

      editTitle.textContent = 'Edit: ' + (name || id || 'Customer');
      editStatusPill.textContent = status.charAt(0).toUpperCase() + status.slice(1);
      editStatusPill.className = status === 'inactive' ? 'status--inactive'
                               : status === 'pending' ? 'status--pending' : 'status--active';

      ei.id.value      = id || '';
      ei.name.value    = name || '';
      ei.nic.value     = nic || '';
      ei.email.value   = email || '';
      ei.contact.value = contact || '';
      ei.status.value  = status;
      // Try dataset ISO first (like branches), fallback to cell text
      ei.created.value = (tr.dataset.created_iso || '').substring(0,10) || (created || '');
      ei.notes.value   = tr.dataset.notes || '';

      editModal.classList.add('is-open');
      editModal.setAttribute('aria-hidden', 'false');
    }

    function closeEdit(){
      editModal.classList.remove('is-open');
      editModal.setAttribute('aria-hidden', 'true');
    }

    // Wire up "Edit" buttons
    $$('#customersTable tbody tr').forEach(function(tr){
      var btn = tr.querySelector('.icon-btn[title="Edit"]');
      if(btn) btn.addEventListener('click', function(){ openEditFromRow(tr); });
    });

    // Keep Edit pill synced with select
    if(ei.status){
      ei.status.addEventListener('change', function(){
        var s = ei.status.value;
        editStatusPill.textContent = s.charAt(0).toUpperCase() + s.slice(1);
        editStatusPill.className = s === 'inactive' ? 'status--inactive'
                              : s === 'pending' ? 'status--pending' : 'status--active';
      });
    }

    // Close handlers for edit modal
    editCloseEl.forEach(function(el){ el.addEventListener('click', closeEdit); });
    editModal.addEventListener('click', function(e){ if(e.target.matches('.modal__backdrop')) closeEdit(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && editModal.classList.contains('is-open')) closeEdit(); });

    // Submit Edit (hook PHP later)
    var editForm = document.getElementById('editCustomerForm');
    if(editForm){
      editForm.addEventListener('submit', function(e){
        e.preventDefault();
        // TODO: fetch('update-customer.php', { method:'POST', body:new FormData(editForm) })
        alert('Edit submitted (hook to your PHP endpoint).');
        // closeEdit();
      });
    }

    // “Edit Customer” inside the View modal -> open Edit
    var openEditFromViewBtn = document.getElementById('openEditCustomerFromView');
    if(openEditFromViewBtn){
      openEditFromViewBtn.addEventListener('click', function(){
        closeView();
        if(lastViewedRow) openEditFromRow(lastViewedRow);
      });
    }

    /* =======================
     * ADD MODAL (Customers)
     * ======================= */
    var addModal   = document.getElementById('addCustomerModal');
    var addCloseEl = $$('#addCustomerModal [data-close-modal]');
    var addBtn     = document.getElementById('addCustomerBtn');
    var addPill    = document.getElementById('addCustStatusPill');

    var A = function(id){ return document.getElementById(id); };
    var ai = {
      code:    A('a_customerCode'),
      name:    A('a_fullName'),
      nic:     A('a_nic'),
      email:   A('a_email'),
      contact: A('a_contact'),
      status:  A('a_status'),
      created: A('a_created'),
      notes:   A('a_notes')
    };

    function resetAddForm(){
      if(ai.code) ai.code.value = '';
      ai.name.value = '';
      ai.nic.value = '';
      ai.email.value = '';
      ai.contact.value = '';
      ai.status.value = 'active';
      ai.created.value = new Date().toISOString().slice(0,10);
      ai.notes.value = '';

      addPill.textContent = 'Active';
      addPill.className = 'status--active';
    }

    function openAdd(){ resetAddForm(); addModal.classList.add('is-open'); addModal.setAttribute('aria-hidden','false'); }
    function closeAdd(){ addModal.classList.remove('is-open'); addModal.setAttribute('aria-hidden','true'); }

    if(addBtn) addBtn.addEventListener('click', openAdd);
    addCloseEl.forEach(function(el){ el.addEventListener('click', closeAdd); });
    addModal.addEventListener('click', function(e){ if(e.target.matches('.modal__backdrop')) closeAdd(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && addModal.classList.contains('is-open')) closeAdd(); });

    if(ai.status){
      ai.status.addEventListener('change', function(){
        var s = ai.status.value;
        addPill.textContent = s.charAt(0).toUpperCase() + s.slice(1);
        addPill.className = s === 'inactive' ? 'status--inactive'
                        : s === 'pending' ? 'status--pending' : 'status--active';
      });
    }

    // Submit Add (hook PHP later)
    var addForm = document.getElementById('addCustomerForm');
    if(addForm){
      addForm.addEventListener('submit', function(e){
        e.preventDefault();
        // TODO: fetch('create-customer.php', { method:'POST', body:new FormData(addForm) })
        alert('Create submitted (hook to your PHP endpoint).');
        // Optionally append a new row, then closeAdd();
      });
    }
  })();
  </script>
</body>
</html>
