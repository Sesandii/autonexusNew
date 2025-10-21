<?php /* Customer Profile modals */ ?>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal" hidden>
  <div class="modal-content">
    <button type="button" class="close" data-close>×</button>
    <h3>Edit Profile</h3>
    <form id="editProfileForm">
      <label>Full Name
        <input type="text" name="name" id="edit_name" />
      </label>
      <label>Phone
        <input type="text" name="phone" id="edit_phone" />
      </label>
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
    <button type="button" class="close" data-close>×</button>
    <h3 id="vehicleModalTitle">Add Vehicle</h3>
    <form id="vehicleForm">
      <input type="hidden" name="vehicle_id" id="veh_id" />
      <label>License Plate
        <input type="text" name="reg_no" id="veh_plate" />
      </label>
      <label>Make (Brand)
        <input type="text" name="brand" id="veh_make" />
      </label>
      <label>Model
        <input type="text" name="model" id="veh_model" />
      </label>
      <label>Year
        <input type="number" name="year" id="veh_year" />
      </label>
      <label>Color
        <input type="text" name="color" id="veh_color" />
      </label>
      <div class="actions">
        <button type="submit" class="btn-primary">Save</button>
        <button type="button" class="btn-ghost" data-close>Cancel</button>
      </div>
    </form>
  </div>
</div>
