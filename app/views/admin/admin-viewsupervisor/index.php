<?php $current = 'supervisors'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Workshop Supervisors Management</title>

  <!-- Shared neutral styles -->
  <link rel="stylesheet" href="../admin-shared/management.css">
  <link rel="stylesheet" href="../admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
  </style>
</head>
<body>
  <?php include("../admin-sidebar/sidebar.php"); ?>

  <main class="main-content">
    <div class="management-header">
      <h2>Workshop Supervisors Management</h2>

      <div class="tools">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by supervisor id/name..." />
        <select class="status-filter">
          <option value="all">All Status</option>
          <option value="all">Active</option>
          <option value="all">Inactive</option>
        </select>
        <button class="add-btn">+ Add New Supervisor</button>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Supervisor ID</th>
          <th>Full Name</th>
          <th>Contact Number</th>
          <th>Branch</th>
          <th>Email</th>
          <th>Experience Years</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>SUP001</td>
          <td>Alex Carter</td>
          <td>(555) 101-2020</td>
          <td>Colombo Main Branch</td>
          <td>alex.carter@email.com</td>
          <td>12</td>
          <td class="status--active">Active</td>
          <td>2025-08-15</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>SUP002</td>
          <td>Linda Brown</td>
          <td>(555) 202-3030</td>
          <td>Kandy Branch</td>
          <td>linda.brown@email.com</td>
          <td>10</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-07-10</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>SUP003</td>
          <td>David Miller</td>
          <td>(555) 303-4040</td>
          <td>Negombo Branch</td>
          <td>david.miller@email.com</td>
          <td>15</td>
          <td class="status--active">Active</td>
          <td>2025-06-25</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>SUP004</td>
          <td>Sophia Turner</td>
          <td>(555) 404-5050</td>
          <td>Galle Branch</td>
          <td>sophia.turner@email.com</td>
          <td>8</td>
          <td class="status--active">Active</td>
          <td>2025-05-12</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>SUP005</td>
          <td>Henry Wilson</td>
          <td>(555) 505-6060</td>
          <td>Matara Branch</td>
          <td>henry.wilson@email.com</td>
          <td>9</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-04-08</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <!-- more rows -->
      </tbody>
    </table>

    <!-- ========= View Supervisor Modal ========= -->
    <div class="modal" id="supervisorModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="supViewTitle">Supervisor Details</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <div class="modal__body">
          <div class="modal__status">
            <span id="supViewStatus" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Supervisor ID</p><p id="sv_id">—</p></div>
            <div><p class="label">Full Name</p><p id="sv_name">—</p></div>
            <div><p class="label">Contact</p><p id="sv_contact">—</p></div>
            <div><p class="label">Branch</p><p id="sv_branch">—</p></div>
            <div><p class="label">Email</p><p id="sv_email">—</p></div>
            <div><p class="label">Experience (years)</p><p id="sv_exp">—</p></div>
            <div><p class="label">Created At</p><p id="sv_created">—</p></div>
          </div>
          <div class="modal__section" style="margin-top:8px;">
            <p class="label">Notes</p>
            <p id="sv_notes">—</p>
          </div>
        </div>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Close</button>
          <button type="button" class="btn-primary" id="openEditSupervisorFromView">
            <i class="fas fa-pen"></i> Edit Supervisor
          </button>
        </footer>
      </div>
    </div>

    <!-- ========= Edit Supervisor Modal ========= -->
    <div class="modal" id="editSupervisorModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="supEditTitle">Edit Supervisor</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="editSupervisorForm" method="post" action="#">
          <div class="modal__status">
            <span id="supEditStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Supervisor ID</p><input id="se_id" name="supervisor_id" class="input" type="text" readonly></div>
            <div><p class="label">Full Name</p><input id="se_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">Contact</p><input id="se_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="se_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Email</p><input id="se_email" name="email" class="input" type="email"></div>
            <div><p class="label">Experience (years)</p><input id="se_exp" name="experience_years" class="input" type="number" min="0"></div>
            <div><p class="label">Status</p>
              <select id="se_status" name="status" class="input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="se_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="se_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="editSupervisorForm" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </footer>
      </div>
    </div>

    <!-- ========= Add Supervisor Modal ========= -->
    <div class="modal" id="addSupervisorModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="supAddTitle">Add New Supervisor</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="addSupervisorForm" method="post" action="#">
          <div class="modal__status">
            <span id="supAddStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Supervisor Code</p><input id="sa_code" name="supervisor_code" class="input" type="text" placeholder="e.g. SUP010"></div>
            <div><p class="label">Full Name</p><input id="sa_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">Contact</p><input id="sa_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="sa_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Email</p><input id="sa_email" name="email" class="input" type="email"></div>
            <div><p class="label">Experience (years)</p><input id="sa_exp" name="experience_years" class="input" type="number" min="0" value="0"></div>
            <div><p class="label">Status</p>
              <select id="sa_status" name="status" class="input">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="sa_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="sa_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="addSupervisorForm" class="btn-primary"><i class="fas fa-plus"></i> Create Supervisor</button>
        </footer>
      </div>
    </div>
  </main>

  <script>
  (function(){
    var $=function(s,r){return(r||document).querySelector(s)}, $$=function(s,r){return[].slice.call((r||document).querySelectorAll(s))};
    var setText=function(el,v){ if(el) el.textContent = (v&&String(v).trim()) || '—'; };
    function rowStatus(tr){ return tr.querySelector('.status--inactive') ? 'inactive' : 'active'; }

    // VIEW
    var vModal = document.getElementById('supervisorModal');
    var vClose = $$('#supervisorModal [data-close-modal]');
    var vTitle = document.getElementById('supViewTitle');
    var vStatus= document.getElementById('supViewStatus');
    var V={
      id:document.getElementById('sv_id'),
      name:document.getElementById('sv_name'),
      contact:document.getElementById('sv_contact'),
      branch:document.getElementById('sv_branch'),
      email:document.getElementById('sv_email'),
      exp:document.getElementById('sv_exp'),
      created:document.getElementById('sv_created'),
      notes:document.getElementById('sv_notes')
    };
    var lastRow=null;

    function openView(tr){
      lastRow=tr;
      var s = tr.dataset.status || rowStatus(tr);
      vStatus.textContent = s.charAt(0).toUpperCase()+s.slice(1);
      vStatus.className = s==='inactive'?'status--inactive':'status--active';

      setText(vTitle, tr.children[1] && tr.children[1].textContent);
      setText(V.id,      tr.children[0] && tr.children[0].textContent);
      setText(V.name,    tr.children[1] && tr.children[1].textContent);
      setText(V.contact, tr.children[2] && tr.children[2].textContent);
      setText(V.branch,  tr.children[3] && tr.children[3].textContent);
      setText(V.email,   tr.children[4] && tr.children[4].textContent);
      setText(V.exp,     tr.children[5] && tr.children[5].textContent);
      setText(V.created, tr.children[7] && tr.children[7].textContent);
      setText(V.notes,   tr.dataset.notes);

      vModal.classList.add('is-open'); vModal.setAttribute('aria-hidden','false');
    }
    function closeView(){ vModal.classList.remove('is-open'); vModal.setAttribute('aria-hidden','true'); }
    $$('table tbody tr').forEach(function(tr){ var b=tr.querySelector('.icon-btn[title="View"]'); if(b) b.addEventListener('click',function(){openView(tr);}); });
    vClose.forEach(function(x){x.addEventListener('click',closeView);});
    vModal.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeView(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && vModal.classList.contains('is-open')) closeView(); });

    // EDIT
    var eModal = document.getElementById('editSupervisorModal');
    var eClose = $$('#editSupervisorModal [data-close-modal]');
    var eTitle = document.getElementById('supEditTitle');
    var ePill  = document.getElementById('supEditStatusPill');
    var E={
      id:document.getElementById('se_id'),
      name:document.getElementById('se_name'),
      contact:document.getElementById('se_contact'),
      branch:document.getElementById('se_branch'),
      email:document.getElementById('se_email'),
      exp:document.getElementById('se_exp'),
      status:document.getElementById('se_status'),
      created:document.getElementById('se_created'),
      notes:document.getElementById('se_notes')
    };

    function openEdit(tr){
      var s = tr.dataset.status || rowStatus(tr);
      eTitle.textContent = 'Edit: ' + ((tr.children[1]&&tr.children[1].textContent) || (tr.children[0]&&tr.children[0].textContent) || 'Supervisor');
      ePill.textContent = s.charAt(0).toUpperCase()+s.slice(1);
      ePill.className = s==='inactive'?'status--inactive':'status--active';

      E.id.value      = (tr.children[0]&&tr.children[0].textContent.trim()) || '';
      E.name.value    = (tr.children[1]&&tr.children[1].textContent.trim()) || '';
      E.contact.value = (tr.children[2]&&tr.children[2].textContent.trim()) || '';
      E.branch.value  = tr.dataset.branch || (tr.children[3]&&tr.children[3].textContent.trim()) || '';
      E.email.value   = (tr.children[4]&&tr.children[4].textContent.trim()) || '';
      E.exp.value     = (tr.children[5]&&tr.children[5].textContent.trim()) || '';
      E.status.value  = s;
      E.created.value = (tr.dataset.created_iso||'').substring(0,10) || ((tr.children[7]&&tr.children[7].textContent.trim())||'');
      E.notes.value   = tr.dataset.notes || '';

      eModal.classList.add('is-open'); eModal.setAttribute('aria-hidden','false');
    }
    function closeEdit(){ eModal.classList.remove('is-open'); eModal.setAttribute('aria-hidden','true'); }
    $$('table tbody tr').forEach(function(tr){ var b=tr.querySelector('.icon-btn[title="Edit"]'); if(b) b.addEventListener('click',function(){openEdit(tr);}); });
    if(E.status){ E.status.addEventListener('change',function(){ var s=E.status.value; ePill.textContent=s.charAt(0).toUpperCase()+s.slice(1); ePill.className=s==='inactive'?'status--inactive':'status--active'; }); }
    eClose.forEach(function(x){x.addEventListener('click',closeEdit);});
    eModal.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeEdit(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && eModal.classList.contains('is-open')) closeEdit(); });

    var openEditFromViewBtn = document.getElementById('openEditSupervisorFromView');
    if(openEditFromViewBtn){ openEditFromViewBtn.addEventListener('click', function(){ closeView(); if(lastRow) openEdit(lastRow); }); }

    var editForm = document.getElementById('editSupervisorForm');
    if(editForm){
      editForm.addEventListener('submit', function(ev){
        ev.preventDefault();
        // TODO: POST to update-supervisor.php
        alert('Edit submitted (hook to your PHP endpoint).');
      });
    }

    // ADD
    var aModal=document.getElementById('addSupervisorModal'), aClose=$$('#addSupervisorModal [data-close-modal]'), addBtn=document.querySelector('.add-btn'), aPill=document.getElementById('supAddStatusPill');
    var A={
      code:document.getElementById('sa_code'),
      name:document.getElementById('sa_name'),
      contact:document.getElementById('sa_contact'),
      branch:document.getElementById('sa_branch'),
      email:document.getElementById('sa_email'),
      exp:document.getElementById('sa_exp'),
      status:document.getElementById('sa_status'),
      created:document.getElementById('sa_created'),
      notes:document.getElementById('sa_notes')
    };

    function resetAdd(){
      if(A.code)A.code.value='';
      A.name.value=''; A.contact.value=''; A.branch.value=''; A.email.value='';
      A.exp.value=0; A.status.value='active'; A.created.value=new Date().toISOString().slice(0,10); A.notes.value='';
      aPill.textContent='Active'; aPill.className='status--active';
    }
    function openAdd(){ resetAdd(); aModal.classList.add('is-open'); aModal.setAttribute('aria-hidden','false'); }
    function closeAdd(){ aModal.classList.remove('is-open'); aModal.setAttribute('aria-hidden','true'); }
    if(addBtn) addBtn.addEventListener('click',openAdd);
    aClose.forEach(function(x){x.addEventListener('click',closeAdd);});
    aModal.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeAdd(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && aModal.classList.contains('is-open')) closeAdd(); });
    if(A.status){ A.status.addEventListener('change', function(){ var s=A.status.value; aPill.textContent=s.charAt(0).toUpperCase()+s.slice(1); aPill.className=s==='inactive'?'status--inactive':'status--active'; }); }
    var addForm=document.getElementById('addSupervisorForm');
    if(addForm){
      addForm.addEventListener('submit', function(ev){
        ev.preventDefault();
        // TODO: POST to create-supervisor.php
        alert('Create submitted (hook to your PHP endpoint).');
      });
    }
  })();
  </script>
</body>
</html>
