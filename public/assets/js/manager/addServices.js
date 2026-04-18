document.addEventListener('DOMContentLoaded', () => {
  const typeSelect = document.getElementById('typeSelect');

  const serviceFields = document.getElementById('serviceFields');
  const packageFields = document.getElementById('packageFields');

  const serviceCodeGroup = document.getElementById('serviceCodeGroup');
  const packageCodeGroup = document.getElementById('packageCodeGroup');

  // Package fields that are required
  const packageServices = packageFields.querySelector('select[name="services[]"]');
  const packageDuration = packageFields.querySelector('input[name="total_duration"]');
  const packagePrice = packageFields.querySelector('input[name="total_price"]');

  // Service fields that are required (if you ever want to add more)
  const serviceName = serviceFields.querySelector('input[name="name"]');
  const serviceDuration = serviceFields.querySelector('input[name="duration"]');
  const servicePrice = serviceFields.querySelector('input[name="price"]');
  const serviceTypeId = serviceFields.querySelector('select[name="type_id"]');

  if (!typeSelect || !serviceFields || !packageFields || !serviceCodeGroup || !packageCodeGroup) {
    console.error('Required elements are missing');
    return;
  }

  function toggleFields() {
    const isService = typeSelect.value === 'service';

    // Toggle visibility
    serviceFields.style.display = isService ? 'block' : 'none';
    serviceCodeGroup.style.display = isService ? 'block' : 'none';

    packageFields.style.display = isService ? 'none' : 'block';
    packageCodeGroup.style.display = isService ? 'none' : 'block';

    // Toggle required attributes
    packageServices.required = !isService;
    packageDuration.required = !isService;
    packagePrice.required = !isService;

    // Optional: if you want service fields to be required
    // serviceName.required = isService;
    // serviceDuration.required = isService;
    // servicePrice.required = isService;
    // serviceTypeId.required = isService;
  }

  // Initial toggle on page load
  toggleFields();

  // Toggle fields when type changes
  typeSelect.addEventListener('change', toggleFields);
});
