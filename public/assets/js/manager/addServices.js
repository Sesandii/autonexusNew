document.addEventListener('DOMContentLoaded', () => {

  const typeSelect = document.getElementById('typeSelect');
  const codeGroup = document.getElementById('codeGroup');
  const packageFields = document.getElementById('packageFields');
  const serviceCode = document.getElementById('serviceCode');

  function toggleFields() {
    const isService = typeSelect.value === 'service';

    // Package fields only shown for packages
    packageFields.style.display = isService ? 'none' : 'block';

    // Same code value for both, always visible
    serviceCode.value = window.lastCode || 'SER001';
  }

  typeSelect.addEventListener('change', toggleFields);
  toggleFields();
});