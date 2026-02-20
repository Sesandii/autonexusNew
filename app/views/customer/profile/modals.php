<?php /* Customer Profile modals */ ?>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal" hidden>
  <div class="modal-content">
    <button type="button" class="close" data-close aria-label="Close">×</button>

    <h3>Edit Profile</h3>
    <p class="section-subtitle">Update your basic contact information.</p>

    <form id="editProfileForm" class="modal-form" method="post" action="<?= $base ?>/customer/profile/update">
      <?php 
        $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
      ?>
      <label>Full Name
        <input type="text" name="name" id="edit_name" value="<?= htmlspecialchars($fullName) ?>" required />
      </label>

      <label>Phone
        <input type="text" name="phone" id="edit_phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" />
      </label>

      <label>Alt. Phone
        <input type="text" name="alt_phone" id="edit_alt_phone" value="<?= htmlspecialchars($profile['alt_phone'] ?? '') ?>" />
      </label>

      <label>Street Address
        <input type="text" name="street_address" id="edit_street" value="<?= htmlspecialchars($profile['street_address'] ?? '') ?>" />
      </label>

      <div class="grid-2">
        <label>City
          <input type="text" name="city" id="edit_city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" />
        </label>
        <label>State
          <input type="text" name="state" id="edit_state" value="<?= htmlspecialchars($profile['state'] ?? '') ?>" />
        </label>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary">Save</button>
        <button type="button" class="btn-ghost" data-close>Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Vehicle Modal -->
<div id="vehicleModal" class="modal" hidden>
  <div class="modal-content">
    <button type="button" class="close" data-close aria-label="Close">×</button>

    <h3 id="vehicleModalTitle">Add Vehicle</h3>
    <p class="section-subtitle">Save your vehicle details to book and manage services faster.</p>

    <form id="vehicleForm" class="modal-form" method="post" action="<?= $base ?>/customer/profile/vehicle">
      <input type="hidden" name="vehicle_id" id="veh_id" />

      <label>License Plate
        <input type="text" name="reg_no" id="veh_plate" required />
      </label>

      <div class="grid-2">
        <label>Make (Brand)
          <input type="text" name="brand" id="veh_make" required />
        </label>
        <label>Model
          <input type="text" name="model" id="veh_model" required />
        </label>
      </div>

      <div class="grid-2">
        <label>Year
          <input type="number" name="year" id="veh_year" min="1950" max="<?= date('Y') + 1 ?>" />
        </label>
        <label>Color
          <input type="text" name="color" id="veh_color" />
        </label>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary">Save</button>
        <button type="button" class="btn-ghost" data-close>Cancel</button>
      </div>
    </form>
  </div>
</div>
