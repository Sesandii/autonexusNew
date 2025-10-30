
<div class="sidebar">
    <div class="logo">
        <img src="<?= BASE_URL ?>/public/Images/logo.png" alt="AutoNexus Logo" width="240">
        <h2>AUTONEXUS</h2>
    </div>
    <ul class="menu">
        <li class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/dashboard/index">Dashboard</a>
        </li>
        <li class="<?= ($activePage ?? '') === 'appointments' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/appointments/index">Appointments</a>
        </li>
        <li class="<?= ($activePage ?? '') === 'services' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/services/index">Service & Packages</a>
        </li>
        <li class="<?= ($activePage ?? '') === 'complaints' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/complaints">Complaints</a>
        </li>
        <li class="<?= ($activePage ?? '') === 'billing' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/billing/index">Billing & Payments</a>
        </li>
        <li class="<?= ($activePage ?? '') === 'profiles' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/receptionist/profiles/index">Customer Profiles</a>
        </li>
    </ul>
</div>