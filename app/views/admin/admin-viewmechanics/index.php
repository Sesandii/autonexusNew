<?php $current = 'mechanics'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Service Managers Management</title>

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
      <h2>Service Managers Management</h2>

      <div class="tools">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by manager id/name..." />
        <select class="status-filter">
          <option value="all">All Status</option>
          <option value="all">Active</option>
          <option value="all">Inactive</option>
        </select>
        <button class="add-btn">+ Add New Mechanic</button>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Mechanic ID</th>
          <th>Full Name</th>
          <th>Specialization</th>
          <th>Experience Years</th>
          <th>Contact Number</th>
          <th>Branch</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>MEC001</td>
          <td>John Smith</td>
          <td>Engine Repair</td>
          <td>10</td>
          <td>(555) 123-4567</td>
          <td>Colombo Main Branch</td>
          <td class="status--active">Active</td>
          <td>2025-08-10</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>MEC002</td>
          <td>Jane Doe</td>
          <td>Transmission Specialist</td>
          <td>7</td>
          <td>(555) 234-5678</td>
          <td>Kandy Branch</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-07-20</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>MEC003</td>
          <td>Robert Johnson</td>
          <td>Brake Systems</td>
          <td>8</td>
          <td>(555) 345-6789</td>
          <td>Galle Branch</td>
          <td class="status--active">Active</td>
          <td>2025-06-18</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>MEC004</td>
          <td>Emily Davis</td>
          <td>Electrical Systems</td>
          <td>5</td>
          <td>(555) 456-7890</td>
          <td>Negombo Branch</td>
          <td class="status--inactive">Inactive</td>
          <td>2025-05-22</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <tr>
          <td>MEC005</td>
          <td>Michael Brown</td>
          <td>AC & Cooling Systems</td>
          <td>6</td>
          <td>(555) 567-8901</td>
          <td>Matara Branch</td>
          <td class="status--active">Active</td>
          <td>2025-08-01</td>
          <td>
            <button type="button" class="icon-btn" title="View"><i class="fas fa-eye"></i></button>
            <button type="button" class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
            <button type="button" class="icon-btn" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <!-- more rows -->
      </tbody>
    </table>

    <!-- ========= View Mechanic Modal ========= -->
    <div class="modal" id="mechanicModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="mecViewTitle">Mechanic Details</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <div class="modal__body">
          <div class="modal__status">
            <span id="mecViewStatus" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Mechanic ID</p><p id="mv_id">—</p></div>
            <div><p class="label">Full Name</p><p id="mv_name">—</p></div>
            <div><p class="label">Specialization</p><p id="mv_spec">—</p></div>
            <div><p class="label">Experience (years)</p><p id="mv_exp">—</p></div>
            <div><p class="label">Contact</p><p id="mv_contact">—</p></div>
            <div><p class="label">Branch</p><p id="mv_branch">—</p></div>
            <div><p class="label">Created At</p><p id="mv_created">—</p></div>
          </div>
          <div class="modal__section" style="margin-top:8px;">
            <p class="label">Notes</p>
            <p id="mv_notes">—</p>
          </div>
        </div>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Close</button>
          <button type="button" class="btn-primary" id="openEditMechanicFromView">
            <i class="fas fa-pen"></i> Edit Mechanic
          </button>
        </footer>
      </div>
    </div>

    <!-- ========= Edit Mechanic Modal ========= -->
    <div class="modal" id="editMechanicModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="mecEditTitle">Edit Mechanic</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="editMechanicForm" method="post" action="#">
          <div class="modal__status">
            <span id="mecEditStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Mechanic ID</p><input id="me_id" name="mechanic_id" class="input" type="text" readonly></div>
            <div><p class="label">Full Name</p><input id="me_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">Specialization</p><input id="me_spec" name="specialization" class="input" type="text"></div>
            <div><p class="label">Experience (years)</p><input id="me_exp" name="experience_years" class="input" type="number" min="0"></div>
            <div><p class="label">Contact</p><input id="me_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="me_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Status</p>
              <select id="me_status" name="status" class="input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="me_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="me_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="editMechanicForm" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </footer>
      </div>
    </div>

    <!-- ========= Add Mechanic Modal ========= -->
    <div class="modal" id="addMechanicModal" aria-hidden="true" role="dialog" aria-modal="true">
      <div class="modal__backdrop" data-close-modal></div>
      <div class="modal__dialog" role="document">
        <header class="modal__header">
          <h3 id="mecAddTitle">Add New Mechanic</h3>
          <button type="button" class="modal__close" title="Close" aria-label="Close" data-close-modal>×</button>
        </header>
        <form class="modal__body" id="addMechanicForm" method="post" action="#">
          <div class="modal__status">
            <span id="mecAddStatusPill" class="status--active">Active</span>
          </div>
          <div class="modal__grid">
            <div><p class="label">Mechanic Code</p><input id="ma_code" name="mechanic_code" class="input" type="text" placeholder="e.g. MEC010"></div>
            <div><p class="label">Full Name</p><input id="ma_name" name="full_name" class="input" type="text" required></div>
            <div><p class="label">Specialization</p><input id="ma_spec" name="specialization" class="input" type="text"></div>
            <div><p class="label">Experience (years)</p><input id="ma_exp" name="experience_years" class="input" type="number" min="0" value="0"></div>
            <div><p class="label">Contact</p><input id="ma_contact" name="contact" class="input" type="text"></div>
            <div><p class="label">Branch</p><input id="ma_branch" name="branch" class="input" type="text"></div>
            <div><p class="label">Status</p>
              <select id="ma_status" name="status" class="input">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div><p class="label">Created At</p><input id="ma_created" name="created_at" class="input" type="date"></div>
            <div style="grid-column:1/-1;"><p class="label">Notes</p><textarea id="ma_notes" name="notes" class="input" rows="3"></textarea></div>
          </div>
        </form>
        <footer class="modal__footer">
          <button type="button" class="btn-secondary" data-close-modal>Cancel</button>
          <button type="submit" form="addMechanicForm" class="btn-primary"><i class="fas fa-plus"></i> Create Mechanic</button>
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
    var vm=document.getElementById('mechanicModal'), vClose=$$('#mechanicModal [data-close-modal]');
    var vTitle=document.getElementById('mecViewTitle'), vPill=document.getElementById('mecViewStatus');
    var V={
      id:document.getElementById('mv_id'),
      name:document.getElementById('mv_name'),
      spec:document.getElementById('mv_spec'),
      exp:document.getElementById('mv_exp'),
      contact:document.getElementById('mv_contact'),
      branch:document.getElementById('mv_branch'),
      created:document.getElementById('mv_created'),
      notes:document.getElementById('mv_notes')
    };
    var lastRow=null;
    function openView(tr){
      lastRow=tr; var s=tr.dataset.status||statusFrom(tr);
      vPill.textContent=s.charAt(0).toUpperCase()+s.slice(1); vPill.className=s==='inactive'?'status--inactive':'status--active';
      setText(vTitle, tr.children[1]&&tr.children[1].textContent);
      setText(V.id,      tr.children[0]&&tr.children[0].textContent);
      setText(V.name,    tr.children[1]&&tr.children[1].textContent);
      setText(V.spec,    tr.children[2]&&tr.children[2].textContent);
      setText(V.exp,     tr.children[3]&&tr.children[3].textContent);
      setText(V.contact, tr.children[4]&&tr.children[4].textContent);
      setText(V.branch,  tr.children[5]&&tr.children[5].textContent);
      setText(V.created, tr.children[7]&&tr.children[7].textContent);
      setText(V.notes,   tr.dataset.notes);
      vm.classList.add('is-open'); vm.setAttribute('aria-hidden','false');
    }
    function closeView(){ vm.classList.remove('is-open'); vm.setAttribute('aria-hidden','true'); }
    $$('table tbody tr').forEach(function(tr){ var b=tr.querySelector('.icon-btn[title="View"]'); if(b) b.addEventListener('click',function(){openView(tr);}); });
    vClose.forEach(function(x){x.addEventListener('click',closeView);});
    vm.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeView(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && vm.classList.contains('is-open')) closeView(); });

    // EDIT
    var em=document.getElementById('editMechanicModal'), eClose=$$('#editMechanicModal [data-close-modal]');
    var eTitle=document.getElementById('mecEditTitle'), ePill=document.getElementById('mecEditStatusPill');
    var E={
      id:document.getElementById('me_id'),
      name:document.getElementById('me_name'),
      spec:document.getElementById('me_spec'),
      exp:document.getElementById('me_exp'),
      contact:document.getElementById('me_contact'),
      branch:document.getElementById('me_branch'),
      status:document.getElementById('me_status'),
      created:document.getElementById('me_created'),
      notes:document.getElementById('me_notes')
    };
    function openEdit(tr){
      var s=tr.dataset.status||statusFrom(tr);
      eTitle.textContent='Edit: '+((tr.children[1]&&tr.children[1].textContent)|| (tr.children[0]&&tr.children[0].textContent) || 'Mechanic');
      ePill.textContent=s.charAt(0).toUpperCase()+s.slice(1); ePill.className=s==='inactive'?'status--inactive':'status--active';
      E.id.value      =(tr.children[0]&&tr.children[0].textContent.trim())||'';
      E.name.value    =(tr.children[1]&&tr.children[1].textContent.trim())||'';
      E.spec.value    =(tr.children[2]&&tr.children[2].textContent.trim())||'';
      E.exp.value     =(tr.children[3]&&tr.children[3].textContent.trim())||'';
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

    var openEditFromViewBtn = document.getElementById('openEditMechanicFromView');
    if(openEditFromViewBtn){ openEditFromViewBtn.addEventListener('click', function(){ closeView(); if(lastRow) openEdit(lastRow); }); }

    var editForm=document.getElementById('editMechanicForm');
    if(editForm){
      editForm.addEventListener('submit',function(ev){
        ev.preventDefault();
        // TODO: POST to update-mechanic.php
        alert('Edit submitted (hook to your PHP endpoint).');
      });
    }

    // ADD
    var am=document.getElementById('addMechanicModal'), aClose=$$('#addMechanicModal [data-close-modal]'), addBtn=document.querySelector('.add-btn'), aPill=document.getElementById('mecAddStatusPill');
    var A={
      code:document.getElementById('ma_code'),
      name:document.getElementById('ma_name'),
      spec:document.getElementById('ma_spec'),
      exp:document.getElementById('ma_exp'),
      contact:document.getElementById('ma_contact'),
      branch:document.getElementById('ma_branch'),
      status:document.getElementById('ma_status'),
      created:document.getElementById('ma_created'),
      notes:document.getElementById('ma_notes')
    };
    function resetAdd(){
      if(A.code)A.code.value='';
      A.name.value=''; A.spec.value=''; A.exp.value=0; A.contact.value=''; A.branch.value='';
      A.status.value='active'; A.created.value=new Date().toISOString().slice(0,10); A.notes.value='';
      aPill.textContent='Active'; aPill.className='status--active';
    }
    function openAdd(){ resetAdd(); am.classList.add('is-open'); am.setAttribute('aria-hidden','false'); }
    function closeAdd(){ am.classList.remove('is-open'); am.setAttribute('aria-hidden','true'); }
    if(addBtn) addBtn.addEventListener('click',openAdd);
    aClose.forEach(function(x){x.addEventListener('click',closeAdd);});
    am.addEventListener('click',function(e){ if(e.target.matches('.modal__backdrop')) closeAdd(); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && am.classList.contains('is-open')) closeAdd(); });
    if(A.status){ A.status.addEventListener('change', function(){ var s=A.status.value; aPill.textContent=s.charAt(0).toUpperCase()+s.slice(1); aPill.className=s==='inactive'?'status--inactive':'status--active'; }); }
    var addForm=document.getElementById('addMechanicForm');
    if(addForm){
      addForm.addEventListener('submit',function(ev){
        ev.preventDefault();
        // TODO: POST to create-mechanic.php
        alert('Create submitted (hook to your PHP endpoint).');
      });
    }
  })();
  </script>
</body>
</html>
