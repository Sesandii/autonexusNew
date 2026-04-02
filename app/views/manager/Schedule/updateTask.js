fetch('/update', {
  method: 'POST',
  body: new FormData(formElement)
}).then(res => res.json())
  .then(data => alert('Updated successfully!'));
