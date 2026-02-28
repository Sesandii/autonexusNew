const managers = [
  {
    name: "James Wilson",
    branch: "Downtown",
    phone: "(555) 123-9876",
    email: "james.w@example.com",
    status: "Active"
  },
  {
    name: "Patricia Clark",
    branch: "Westside",
    phone: "(555) 456-7890",
    email: "patricia.c@example.com",
    status: "Active"
  },
  {
    name: "Richard Harris",
    branch: "Northside",
    phone: "(555) 789-4561",
    email: "richard.h@example.com",
    status: "Inactive"
  }
];

const tbody = document.getElementById('manager-list');

managers.forEach(m => {
  const tr = document.createElement('tr');

  tr.innerHTML = `
    <td>${m.name}</td>
    <td>${m.branch}</td>
    <td>${m.phone}</td>
    <td>${m.email}</td>
    <td><span class="status ${m.status.toLowerCase()}">${m.status}</span></td>
    <td class="actions">
      <i class="fas fa-eye"></i>
      <i class="fas fa-pen"></i>
      <i class="fas fa-trash"></i>
    </td>
  `;

  tbody.appendChild(tr);
});
