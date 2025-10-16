<?php $current = 'receptionists'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Receptionists Management</title>

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
      <h2>Receptionists Management</h2>

      <div class="tools">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by receptionist id/name..." />
        <select class="status-filter">
          <option value="all">All Status</option>
          <option value="all">Active</option>
          <option value="all">Inactive</option>
        </select>
        <button class="add-btn">+ Add New Receptionist</button>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th title="Receptionist ID">Rec ID</th>
          <th>Full Name</th>
          <th>NIC</th>
          <th>Email</th>
          <th>Contact Number</th>
          <th>Branch</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>REC001</td>
          <td>Anna Perera</td>
          <td>200245678901</td>
          <td>anna.perera@email.com</td>
          <td>(555) 111-2222</td>
          <td>Colombo Main Branch</td>
          <td class="status--active">Active</td>
          <td>2025-08-05</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>REC002</td>
          <td>Kamal Fernando</td>
          <td>199845678912</td>
          <td>kamal.fernando@email.com</td>
          <td>(555) 222-3333</td>
          <td>Kandy Branch</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-07-18</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>REC003</td>
          <td>Shalini Silva</td>
          <td>200145678923</td>
          <td>shalini.silva@email.com</td>
          <td>(555) 333-4444</td>
          <td>Galle Branch</td>
          <td class="status--active">Active</td>
          <td>2025-06-22</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>REC004</td>
          <td>Ruwan Jayasinghe</td>
          <td>199945678934</td>
          <td>ruwan.jayasinghe@email.com</td>
          <td>(555) 444-5555</td>
          <td>Negombo Branch</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-05-30</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>REC005</td>
          <td>Nadeesha Karunaratne</td>
          <td>200345678945</td>
          <td>nadeesha.karunaratne@email.com</td>
          <td>(555) 555-6666</td>
          <td>Matara Branch</td>
          <td class="status--active">Active</td>
          <td>2025-04-12</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <!-- more rows -->
      </tbody>
    </table>

    <!-- ========= View Receptionist Modal ========= -->
    <div class="modal" id="receptionistModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="recViewTitle">Receptionist Details</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <div class="modal__body">
          <div class="modal__status">
            <span id="recViewStatus" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Receptionist ID</p><p id="rv_id">—</p></div>
            <div><p class="label">Full Name</p><p id="rv_name">—</p></div>
            <div><p class="label">NIC</p><p id="rv_nic">—</p></div>
            <div><p class="label">Email</p><p id="rv_email">—</p></div>
            <div><p class="label">Contact</p><p id="rv_contact">—</p></div>
            <div><p class="label">Branch</p><p id="rv_branch">—</p></div>
            <div><p class="label">Created At</p><p id="rv_created">—</p></div>
          </div>
          <div class="modal__section" style="margin-top:8px;">
            <p class="label">Notes</p>
            <p id="rv_notes">—</p>
          </div>
        </div>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Close</button>
          <button type="button" class="btn-primary" id="openEditReceptionistFromView">
            <i class="fas fa-pen"></i> Edit Receptionist
          </button>
        </footer>
      </div>
    </div>

    <!-- ========= Edit Receptionist Modal ========= -->
    <div class="modal" id="editReceptionistModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="recEditTitle">Edit Receptionist</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="editReceptionistForm" method="post" action="#">
          <div class="modal__status">
            <span id="recEditStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Receptionist ID</p><input id="re_id" name="receptionist_id" class="input" type="text" readonly></div>
            <div><p class="label">Full Name</p><input id="re_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">NIC</p><input id="re_nic" name="nic" class="input" type="text" required></div>
            <div><p class="label">Email</p><input id="re_email" name="email" class="input" type="email"></div>
            <div><p class="label">Contact</p><input id="re_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="re_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Status</p>
              <select id="re_status" name="status" class="input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="re_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="re_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="editReceptionistForm" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </footer>
      </div>
    </div>

    <!-- ========= Add Receptionist Modal ========= -->
    <div class="modal" id="addReceptionistModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="recAddTitle">Add New Receptionist</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="addReceptionistForm" method="post" action="#">
          <div class="modal__status">
            <span id="recAddStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Receptionist Code</p><input id="ra_code" name="receptionist_code" class="input" type="text" placeholder="e.g. REC010"></div>
            <div><p class="label">Full Name</p><input id="ra_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">NIC</p><input id="ra_nic" name="nic" class="input" type="text" required></div>
            <div><p class="label">Email</p><input id="ra_email" name="email" class="input" type="email"></div>
            <div><p class="label">Contact</p><input id="ra_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="ra_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Status</p>
              <select id="ra_status" name="status" class="input">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="ra_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="ra_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="addReceptionistForm" class="btn-primary"><i class="fas fa-plus"></i> Create Receptionist</button>
        </footer>
      </div>
    </div>
  </main>

  <script>
  (function(){
    var $=function(s,r){return(r||document).querySelector(s)}, $$=function(s,r){return[].slice.call((r||document).querySelectorAll(s))};
    var setText=function(el,v){ if(el) el.textContent=(v&&String(v).trim())||'—'; };
    function statusFrom(tr){ return tr.querySelector('.status--inactive') ? 'inactive' : 'active'; }

    // VIEW
    var vm=document.getElementById('receptionistModal'), vClose=$$('#receptionistModal [data-close-modal]');
    var vTitle=document.getElementById('recViewTitle'), vPill=document.getElementById('recViewStatus');
    var V={
      id:document.getElementById('rv_id'),
      name:document.getElementById('rv_name'),
      nic:document.getElementById('rv_nic'),
      email:document.getElementById('rv_email'),
      contact:document.getElementById('rv_contact'),
      branch:document.getElementById('rv_branch'),
      created:document.getElementById('rv_created'),
      notes:document.getElementById('rv_notes')
    };
    var lastRow=null;
    function openView(tr){
      lastRow=tr; var s=tr.dataset.status||statusFrom(tr);
      vPill.textContent=s.charAt(0).toUpperCase()+s.slice(1); vPill.className=s==='inactive'?'status--inactive':'status--active';
      setText(vTitle, tr.children[1]&&tr.children[1].textContent);
      setText(V.id,      tr.children[0]&&tr.children[0].textContent);
      setText(V.name,    tr.children[1]&&tr.children[1].textContent);
      setText(V.nic,     tr.children[2]&&tr.children[2].textContent);
      setText(V.email,   tr.children[3]&&tr.children[3].textContent);
      setText(V.contact, tr.children[4]&&tr.children[4].textContent);
      setText(V.branch,  tr.children[5]&&tr.children[5].textContent);
      setText(V.created, tr.children[7]&&tr.children[7].textContent);
      setText(V.notes,   tr.dataset.notes);
      vm.classList.add('is-open'); vm.setAttribute('aria-hidden','false');
    }
    function closeView(){ vm.classList.remove('is-open'); vm.setAttribute('aria-hidden','true'); }
    $$( 'table tbody tr').forEach(function(tr){ var b=tr.querySelector('.icon-btn[title="View"]'); if(b) b.addEventListener('click',function(){openView(tr);}); });
    vClose.forEach(function(x){x.addEventListener('click',closeView);});
    vm.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeView(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && vm.classList.contains('is-open')) closeView(); });

    // EDIT
    var em=document.getElementById('editReceptionistModal'), eClose=$$('#editReceptionistModal [data-close-modal]');
    var eTitle=document.getElementById('recEditTitle'), ePill=document.getElementById('recEditStatusPill');
    var E={
      id:document.getElementById('re_id'),
      name:document.getElementById('re_name'),
      nic:document.getElementById('re_nic'),
      email:document.getElementById('re_email'),
      contact:document.getElementById('re_contact'),
      branch:document.getElementById('re_branch'),
      status:document.getElementById('re_status'),
      created:document.getElementById('re_created'),
      notes:document.getElementById('re_notes')
    };
    function openEdit(tr){
      var s=tr.dataset.status||statusFrom(tr);
      eTitle.textContent='Edit: '+((tr.children[1]&&tr.children[1].textContent) || (tr.children[0]&&tr.children[0].textContent) || 'Receptionist');
      ePill.textContent=s.charAt(0).toUpperCase()+s.slice(1); ePill.className=s==='inactive'?'status--inactive':'status--active';
      E.id.value      =(tr.children[0]&&tr.children[0].textContent.trim())||'';
      E.name.value    =(tr.children[1]&&tr.children[1].textContent.trim())||'';
      E.nic.value     =(tr.children[2]&&tr.children[2].textContent.trim())||'';
      E.email.value   =(tr.children[3]&&tr.children[3].textContent.trim())||'';
      E.contact.value =(tr.children[4]&&tr.children[4].textContent.trim())||'';
      E.branch.value  = tr.dataset.branch || (tr.children[5]&&tr.children[5].textContent.trim()) || '';
      E.status.value  = s;
      E.created.value = (tr.dataset.created_iso||'').substring(0,10) || ((tr.children[7]&&tr.children[7].textContent.trim())||'');
      E.notes.value   = tr.dataset.notes || '';
      em.classList.add('is-open'); em.setAttribute('aria-hidden','false');
    }
    function closeEdit(){ em.classList.remove('is-open'); em.setAttribute('aria-hidden','true'); }
    $$('table tbody tr').forEach(function(tr){ var b=tr.querySelector('.icon-btn[title="Edit"]'); if(b) b.addEventListener('click',function(){openEdit(tr);}); });
    if(E.status){ E.status.addEventListener('change',function(){ var s=E.status.value; ePill.textContent=s.charAt(0).toUpperCase()+s.slice(1); ePill.className=s==='inactive'?'status--inactive':'status--active'; }); }
    eClose.forEach(function(x){x.addEventListener('click',closeEdit);});
    em.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeEdit(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && em.classList.contains('is-open')) closeEdit(); });

    var openEditFromViewBtn=document.getElementById('openEditReceptionistFromView');
    if(openEditFromViewBtn){ openEditFromViewBtn.addEventListener('click', function(){ closeView(); if(lastRow) openEdit(lastRow); }); }

    var editForm=document.getElementById('editReceptionistForm');
    if(editForm){
      editForm.addEventListener('submit',function(ev){
        ev.preventDefault();
        // TODO: POST to update-receptionist.php
        alert('Edit submitted (hook to your PHP endpoint).');
      });
    }

    // ADD
    var am=document.getElementById('addReceptionistModal'), aClose=$$('#addReceptionistModal [data-close-modal]'), addBtn=document.querySelector('.add-btn'), aPill=document.getElementById('recAddStatusPill');
    var A={
      code:document.getElementById('ra_code'),
      name:document.getElementById('ra_name'),
      nic:document.getElementById('ra_nic'),
      email:document.getElementById('ra_email'),
      contact:document.getElementById('ra_contact'),
      branch:document.getElementById('ra_branch'),
      status:document.getElementById('ra_status'),
      created:document.getElementById('ra_created'),
      notes:document.getElementById('ra_notes')
    };
    function resetAdd(){
      if(A.code)A.code.value='';
      A.name.value=''; A.nic.value=''; A.email.value=''; A.contact.value=''; A.branch.value='';
      A.status.value='active'; A.created.value=new Date().toISOString().slice(0,10); A.notes.value='';
      aPill.textContent='Active'; aPill.className='status--active';
    }
    function openAdd(){ resetAdd(); am.classList.add('is-open'); am.setAttribute('aria-hidden','false'); }
    function closeAdd(){ am.classList.remove('is-open'); am.setAttribute('aria-hidden','true'); }
    if(addBtn) addBtn.addEventListener('click',openAdd);
    aClose.forEach(function(x){x.addEventListener('click',closeAdd);});
    am.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeAdd(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && am.classList.contains('is-open')) closeAdd(); });
    if(A.status){ A.status.addEventListener('change',function(){ var s=A.status.value; aPill.textContent=s.charAt(0).toUpperCase()+s.slice(1); aPill.className=s==='inactive'?'status--inactive':'status--active'; }); }
    var addForm=document.getElementById('addReceptionistForm');
    if(addForm){
      addForm.addEventListener('submit',function(ev){
        ev.preventDefault();
        // TODO: POST to create-receptionist.php
        alert('Create submitted (hook to your PHP endpoint).');
      });
    }
  })();
  </script>
</body>
</html>
