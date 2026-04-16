<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Overview - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/Schedule.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

  <!-- ================= HEADER WITH FILTERS ================= -->
  <div class="header">
    <h1>Team Overview</h1>
    <div class="header-actions">
      <div class="filter-group">
        <select id="roleFilter" class="filter-select">
          <option value="all">All Team Members</option>
          <option value="supervisor">Supervisors</option>
          <option value="mechanic">Mechanics</option>
          <option value="receptionist">Receptionists</option>
        </select>
      </div>
      <a href="<?= BASE_URL ?>/manager/schedule/add-member" class="btn-primary">
      <i class="fas fa-user-plus"></i> Add Team Member
    </a>
    </div>
  </div>

  <!-- ================= TEAM STATS ================= -->
  <div class="team-stats">
    <div class="stat-card">
      <span class="stat-value" id="totalCount"><?= count($users) ?></span>
      <span class="stat-label">Total Members</span>
    </div>
    <div class="stat-card">
      <span class="stat-value" id="supervisorCount"><?= count(array_filter($users, fn($u) => $u['role'] === 'supervisor')) ?></span>
      <span class="stat-label">Supervisors</span>
    </div>
    <div class="stat-card">
      <span class="stat-value" id="mechanicCount"><?= count(array_filter($users, fn($u) => $u['role'] === 'mechanic')) ?></span>
      <span class="stat-label">Mechanics</span>
    </div>
    <div class="stat-card">
      <span class="stat-value" id="receptionistCount"><?= count(array_filter($users, fn($u) => $u['role'] === 'receptionist')) ?></span>
      <span class="stat-label">Receptionists</span>
    </div>
  </div>

  <!-- ================= TEAM CARDS ================= -->
  <div class="cards" id="teamCards">

    <?php foreach ($users as $user): ?>

    <?php
      $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
      
      // Role badge class
      $roleClass = match($user['role']) {
          'supervisor' => 'supervisor',
          'mechanic' => 'mechanic',
          'receptionist' => 'receptionist',
          default => 'mechanic'
      };
      
      $tasks = (int) $user['tasks_today'];
      $isAvailable = ($user['user_status'] ?? 'active') === 'active';
      $statusClass = $isAvailable ? 'available' : 'unavailable';
      $statusText = $isAvailable ? 'Active' : 'Inactive';
      
      // Check if clickable (only mechanics and supervisors)
      $isClickable = in_array($user['role'], ['mechanic', 'supervisor']);
    ?>

    <div class="card <?= !$isClickable ? 'non-clickable' : '' ?>" 
         data-role="<?= $user['role'] ?>"
         data-status="<?= $isAvailable ? 'available' : 'unavailable' ?>"
         data-name="<?= strtolower($user['first_name'] . ' ' . $user['last_name']) ?>">
      
      <?php if ($isClickable): ?>
        <a href="<?= BASE_URL ?>/manager/schedule/personal?id=<?= $user['user_id'] ?>" class="card-link">
      <?php else: ?>
        <div class="card-content">
      <?php endif; ?>
        
        <!-- Status Indicator -->
        <div class="status-indicator <?= $statusClass ?>">
          <span class="status-dot"></span>
          <span class="status-text"><?= $statusText ?></span>
        </div>

        <div class="profile">
          <div class="avatar"><?= $initials ?></div>
          <div>
            <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
            <span class="badge <?= $roleClass ?>"><?= ucfirst($user['role']) ?></span>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
          <?php if (!empty($user['email'])): ?>
            <p class="info"><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
          <?php endif; ?>
          <?php if (!empty($user['phone'])): ?>
            <p class="info"><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Role-specific Details -->
        <?php if ($user['role'] === 'mechanic'): ?>
          <?php if (!empty($user['specialization'])): ?>
            <p class="info"><i class="fas fa-tools"></i> <?= htmlspecialchars($user['specialization']) ?></p>
          <?php endif; ?>
          <?php if (!empty($user['experience_years'])): ?>
            <p class="info"><i class="fas fa-briefcase"></i> <?= $user['experience_years'] ?> years exp.</p>
          <?php endif; ?>
          <?php if (!empty($user['mechanic_code'])): ?>
            <p class="info"><i class="fas fa-id-badge"></i> <?= htmlspecialchars($user['mechanic_code']) ?></p>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($user['role'] === 'supervisor' && !empty($user['supervisor_code'])): ?>
          <p class="info"><i class="fas fa-id-badge"></i> <?= htmlspecialchars($user['supervisor_code']) ?></p>
        <?php endif; ?>
        
        <?php if ($user['role'] === 'receptionist' && !empty($user['receptionist_code'])): ?>
          <p class="info"><i class="fas fa-id-badge"></i> <?= htmlspecialchars($user['receptionist_code']) ?></p>
        <?php endif; ?>

        <p class="info"><i class="fas fa-map-marker-alt"></i> Branch <?= $user['branch_id'] ?></p>
        
        <?php if ($user['role'] === 'mechanic'): ?>
          <div class="task-count">
            <i class="fas fa-tasks"></i> 
            <span><?= $tasks ?> task<?= $tasks == 1 ? '' : 's' ?> today</span>
          </div>
        <?php elseif ($user['role'] === 'supervisor'): ?>
          <div class="task-count supervisor-task">
            <i class="fas fa-clipboard-list"></i> 
            <span>Team Supervisor</span>
          </div>
        <?php else: ?>
          <div class="task-count receptionist-task">
            <i class="fas fa-calendar-check"></i> 
            <span>Front Desk</span>
          </div>
        <?php endif; ?>
        
      <?php if ($isClickable): ?>
        </a>
      <?php else: ?>
        </div>
      <?php endif; ?>
    </div>
   
    <?php endforeach; ?>

  </div><!-- /.cards -->

  <!-- No Results Message -->
  <div id="noResults" class="no-results" style="display: none;">
    <i class="fas fa-users-slash"></i>
    <p>No team members match your filters</p>
    <button class="btn-secondary" onclick="clearFilters()">
      <i class="fas fa-times"></i> Clear Filters
    </button>
  </div>

</div><!-- /.main -->

<!-- ================= ADD TEAM MEMBER MODAL ================= -->
<div id="addMemberModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fas fa-user-plus"></i> Add Team Member</h2>
      <button class="close-btn" onclick="closeAddMemberModal()">&times;</button>
    </div>
    
    <div class="modal-body">
      <!-- Search & Filter -->
      <div class="modal-filters">
        <div class="search-wrapper">
          <i class="fas fa-search"></i>
          <input type="text" id="modalSearch" placeholder="Search by name or email..." class="search-input">
        </div>
        <select id="modalRoleFilter" class="filter-select">
          <option value="all">All Roles</option>
          <option value="supervisor">Supervisors Only</option>
          <option value="mechanic">Mechanics Only</option>
          <option value="receptionist">Receptionists Only</option>
        </select>
      </div>
      
      <!-- Available Employees List -->
      <div class="available-employees">
        <h3>Available Employees</h3>
        <div class="employee-list" id="employeeList">
          <?php foreach ($availableEmployees as $emp): ?>
          <div class="employee-item" 
               data-id="<?= $emp['user_id'] ?>" 
               data-role="<?= $emp['role'] ?>"
               data-name="<?= strtolower($emp['first_name'] . ' ' . $emp['last_name']) ?>">
            
            <div class="employee-avatar">
              <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?>
            </div>
            
            <div class="employee-info">
              <h4><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></h4>
              <?php
                $modalBadgeClass = match($emp['role']) {
                    'supervisor' => 'supervisor',
                    'mechanic' => 'mechanic',
                    'receptionist' => 'receptionist',
                    default => 'mechanic'
                };
              ?>
              <span class="badge <?= $modalBadgeClass ?>">
                <?= ucfirst($emp['role']) ?>
              </span>
              
              <p class="employee-detail">
                <i class="fas fa-envelope"></i> <?= htmlspecialchars($emp['email'] ?? 'N/A') ?>
              </p>
              <p class="employee-detail">
                <i class="fas fa-phone"></i> <?= htmlspecialchars($emp['phone'] ?? 'N/A') ?>
              </p>
              
              <?php if ($emp['role'] === 'mechanic' && !empty($emp['specialization'])): ?>
                <p class="employee-detail">
                  <i class="fas fa-tools"></i> <?= htmlspecialchars($emp['specialization']) ?>
                </p>
              <?php endif; ?>
              
              <p class="employee-detail">
                <i class="fas fa-building"></i> 
                <?= $emp['branch_id'] ? 'Branch ' . $emp['branch_id'] : 'Unassigned' ?>
              </p>
            </div>
            
            <button class="btn-add" onclick="addToTeam(<?= $emp['user_id'] ?>, '<?= $emp['role'] ?>', this)">
              <i class="fas fa-plus"></i> Add
            </button>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($availableEmployees)): ?>
            <div class="no-results-modal">
              <i class="fas fa-user-check"></i>
              <p>No available employees to add</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
  const roleFilter = document.getElementById('roleFilter');
  const statusFilter = document.getElementById('statusFilter');
  const searchInput = document.getElementById('searchMember');
  const cards = document.querySelectorAll('.card');
  const noResults = document.getElementById('noResults');
  const cardsContainer = document.getElementById('teamCards');

  function filterCards() {
    const role = roleFilter ? roleFilter.value : 'all';
    const status = statusFilter ? statusFilter.value : 'all';
    const search = searchInput ? searchInput.value.toLowerCase() : '';
    
    let visibleCount = 0;
    let supervisorCount = 0;
    let mechanicCount = 0;
    let receptionistCount = 0;

    cards.forEach(card => {
      const cardRole = card.dataset.role;
      const cardStatus = card.dataset.status;
      const cardName = card.dataset.name || '';

      let show = true;

      if (role !== 'all' && cardRole !== role) show = false;
      if (status !== 'all' && cardStatus !== status) show = false;
      if (search && !cardName.includes(search)) show = false;

      card.style.display = show ? 'block' : 'none';
      
      if (show) {
        visibleCount++;
        if (cardRole === 'supervisor') supervisorCount++;
        else if (cardRole === 'mechanic') mechanicCount++;
        else if (cardRole === 'receptionist') receptionistCount++;
      }
    });

    // Update stats
    document.getElementById('totalCount').textContent = visibleCount;
    document.getElementById('supervisorCount').textContent = supervisorCount;
    document.getElementById('mechanicCount').textContent = mechanicCount;
    document.getElementById('receptionistCount').textContent = receptionistCount;

    // Show/hide no results message
    if (visibleCount === 0) {
      cardsContainer.style.display = 'none';
      noResults.style.display = 'block';
    } else {
      cardsContainer.style.display = 'grid';
      noResults.style.display = 'none';
    }
  }

  // Clear filters function
  window.clearFilters = function() {
    if (roleFilter) roleFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';
    if (searchInput) searchInput.value = '';
    filterCards();
  };

  // Add event listeners
  if (roleFilter) roleFilter.addEventListener('change', filterCards);
  if (statusFilter) statusFilter.addEventListener('change', filterCards);
  if (searchInput) searchInput.addEventListener('keyup', filterCards);
});

// Modal Functions
function openAddMemberModal() {
  document.getElementById('addMemberModal').classList.add('show');
}

function closeAddMemberModal() {
  document.getElementById('addMemberModal').classList.remove('show');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
  const modal = document.getElementById('addMemberModal');
  if (e.target === modal) {
    closeAddMemberModal();
  }
});

// Add to team function
function addToTeam(userId, role, button) {
  if (!confirm('Add this employee to your team?')) {
    return;
  }
  
  button.disabled = true;
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
  
  fetch('<?= BASE_URL ?>/manager/schedule/add-to-team', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'user_id=' + userId + '&role=' + role
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const employeeItem = button.closest('.employee-item');
      employeeItem.style.transition = 'all 0.3s';
      employeeItem.style.opacity = '0';
      employeeItem.style.transform = 'translateX(20px)';
      
      setTimeout(() => {
        employeeItem.remove();
        
        const employeeList = document.getElementById('employeeList');
        const visibleItems = Array.from(employeeList.children).filter(
          item => item.style.display !== 'none' && !item.classList.contains('no-results-modal')
        );
        
        if (visibleItems.length === 0) {
          location.reload();
        }
      }, 300);
    } else {
      alert(data.message || 'Failed to add team member');
      button.disabled = false;
      button.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.');
    button.disabled = false;
    button.innerHTML = originalText;
  });
}

// Modal filtering
document.addEventListener('DOMContentLoaded', function() {
  const modalSearch = document.getElementById('modalSearch');
  const modalRoleFilter = document.getElementById('modalRoleFilter');
  
  function filterModalEmployees() {
    const search = modalSearch ? modalSearch.value.toLowerCase() : '';
    const role = modalRoleFilter ? modalRoleFilter.value : 'all';
    const items = document.querySelectorAll('.employee-item');
    
    items.forEach(item => {
      const name = item.dataset.name || '';
      const itemRole = item.dataset.role || '';
      
      let show = true;
      
      if (search && !name.includes(search)) show = false;
      if (role !== 'all' && itemRole !== role) show = false;
      
      item.style.display = show ? 'flex' : 'none';
    });
  }
  
  if (modalSearch) modalSearch.addEventListener('keyup', filterModalEmployees);
  if (modalRoleFilter) modalRoleFilter.addEventListener('change', filterModalEmployees);
});
</script>

</body>
</html>