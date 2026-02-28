const invoices = [
  { id: "INV-001", customer: "John Smith", service: "Oil Change & Filter Replacement", amount: "$89.99", date: "Aug 10, 2025", status: "Paid" },
  { id: "INV-002", customer: "Sarah Williams", service: "Brake Pad Replacement", amount: "$249.99", date: "Aug 9, 2025", status: "Pending" },
  { id: "INV-003", customer: "Michael Johnson", service: "Tire Rotation & Alignment", amount: "$129.99", date: "Aug 8, 2025", status: "Paid" },
  { id: "INV-004", customer: "Emily Davis", service: "Full Service", amount: "$349.99", date: "Aug 7, 2025", status: "Paid" },
  { id: "INV-005", customer: "Robert Brown", service: "Engine Diagnostic", amount: "$99.99", date: "Aug 6, 2025", status: "Overdue" },
  { id: "INV-006", customer: "Lisa Chen", service: "Transmission Fluid Change", amount: "$149.99", date: "Aug 5, 2025", status: "Paid" },
  { id: "INV-007", customer: "David Wilson", service: "Battery Replacement", amount: "$189.99", date: "Aug 4, 2025", status: "Pending" },
];

const tableBody = document.getElementById('invoiceTable');

invoices.forEach(inv => {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>${inv.id}</td>
    <td>${inv.customer}</td>
    <td>${inv.service}</td>
    <td>${inv.amount}</td>
    <td>${inv.date}</td>
    <td><span class="status ${inv.status}">${inv.status}</span></td>
    <td class="actions">📩 🔽 ✉️</td>
  `;
  tableBody.appendChild(tr);
});
