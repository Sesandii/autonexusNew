// personalSchedule.js
let availableMechanics = [];
let allWorkOrders = []; // Store all work orders
let currentFilter = 'today';

// Load mechanics when page loads
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing...');
  loadAvailableMechanics();
  loadAllWorkOrders(); // Load all work orders on page load
  updateDateRangeDisplay('today');
});

// Load all work orders for the employee
function loadAllWorkOrders() {
  const userId = getUserIdFromUrl();
  const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
  
  if (!userId) {
    console.error('User ID not found');
    return;
  }
  
  console.log('Loading all work orders for user:', userId);
  
  fetch(baseUrl + '/manager/schedule/getAllWorkOrders?user_id=' + userId)
    .then(response => response.json())
    .then(data => {
      console.log('Work orders loaded:', data);
      allWorkOrders = data;
      filterWorkOrders(currentFilter);
    })
    .catch(error => {
      console.error('Error loading work orders:', error);
    });
}

// Get user ID from URL
function getUserIdFromUrl() {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get('id');
}

// Filter work orders based on selected range
function filterWorkOrders(filter) {
  currentFilter = filter;
  
  // Update active button state
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
  
  // Update date range display
  updateDateRangeDisplay(filter);
  
  // Filter the work orders
  const filteredOrders = getFilteredWorkOrders(filter);
  
  // Update the Kanban board
  updateKanbanBoard(filteredOrders);
}

// Get filtered work orders based on date range
function getFilteredWorkOrders(filter) {
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  
  return allWorkOrders.filter(order => {
    const orderDate = new Date(order.job_start_time || order.appointment_date);
    
    switch(filter) {
      case 'today':
        return orderDate >= today;
        
      case 'week':
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay()); // Sunday
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6); // Saturday
        return orderDate >= weekStart && orderDate <= weekEnd;
        
      case 'month':
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
        const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return orderDate >= monthStart && orderDate <= monthEnd;
        
      case 'year':
        const yearStart = new Date(now.getFullYear(), 0, 1);
        const yearEnd = new Date(now.getFullYear(), 11, 31);
        return orderDate >= yearStart && orderDate <= yearEnd;
        
      case 'all':
      default:
        return true;
    }
  });
}

// Update date range display
function updateDateRangeDisplay(filter) {
  const display = document.getElementById('dateRangeDisplay');
  if (!display) return;
  
  const now = new Date();
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  
  switch(filter) {
    case 'today':
      display.textContent = now.toLocaleDateString('en-US', options);
      break;
      
    case 'week':
      const weekStart = new Date(now);
      weekStart.setDate(now.getDate() - now.getDay());
      const weekEnd = new Date(weekStart);
      weekEnd.setDate(weekStart.getDate() + 6);
      display.textContent = `${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', options)}`;
      break;
      
    case 'month':
      const monthName = now.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
      display.textContent = monthName;
      break;
      
    case 'year':
      display.textContent = now.getFullYear();
      break;
      
    case 'all':
      display.textContent = 'All Time';
      break;
  }
}

// Update Kanban board with filtered orders
function updateKanbanBoard(orders) {
  // Update Scheduled column
  updateColumn('scheduled', orders.filter(o => o.status === 'open'));
  
  // Update In Progress column
  updateColumn('in-progress', orders.filter(o => o.status === 'in_progress'));
  
  // Update Completed column
  updateColumn('completed', orders.filter(o => o.status === 'completed'));
}

// Update individual column
function updateColumn(columnClass, orders) {
  const column = document.querySelector(`.column.${columnClass}`);
  if (!column) return;
  
  const countElement = column.querySelector('.count');
  const cardsList = column.querySelector('.cards-list');
  
  // Update count
  if (countElement) {
    countElement.textContent = orders.length;
  }
  
  // Clear and rebuild cards
  if (cardsList) {
    if (orders.length === 0) {
      const columnName = columnClass === 'scheduled' ? 'scheduled' : 
                         columnClass === 'in-progress' ? 'in progress' : 'completed';
      cardsList.innerHTML = `<div class="empty-column">No ${columnName} work orders</div>`;
    } else {
      cardsList.innerHTML = orders.map(order => createCardHTML(order, columnClass)).join('');
    }
  }
}

// Create card HTML
function createCardHTML(order, columnClass) {
  const tagClass = columnClass === 'scheduled' ? 'scheduled-tag' : 
                   columnClass === 'in-progress' ? 'in-progress-tag' : 'completed-tag';
  
  const serviceName = order.service_name || 'Service';
  const customerName = `${order.customer_first_name || ''} ${order.customer_last_name || ''}`.trim() || order.license_plate || 'N/A';
  const vehicleInfo = `${order.make || ''} ${order.model || ''} (${order.year || 'N/A'})`.trim();
  
  let timeInfo = '';
  if (columnClass === 'scheduled') {
    timeInfo = order.job_start_time ? new Date(order.job_start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '';
  } else if (columnClass === 'in-progress') {
    timeInfo = order.started_at ? `Started: ${new Date(order.started_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}` : '';
  } else {
    timeInfo = order.completed_at ? `Completed: ${new Date(order.completed_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}` : '';
  }
  
  return `
    <div class="card">
      <div class="card-header">
        <div class="tag ${tagClass}">${serviceName}</div>
        <button class="reassign-btn" onclick="openReassignModal(${order.work_order_id}, ${order.mechanic_id || 0})" title="Reassign work order">
          🔄 Reassign
        </button>
      </div>
      <div class="card-name">${customerName}</div>
      <div class="card-details">
        <span class="icon">🚗</span> ${vehicleInfo}
      </div>
      <div class="card-details">
        <span class="icon">⏰</span> ${timeInfo}
      </div>
    </div>
  `;
}

// Existing functions remain the same...
function loadAvailableMechanics() {
  // ... keep existing code ...
}

function openReassignModal(workOrderId, currentMechanicId) {
  // ... keep existing code ...
}

function closeReassignModal() {
  // ... keep existing code ...
}