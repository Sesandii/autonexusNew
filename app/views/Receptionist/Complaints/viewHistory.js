const serviceHistory = document.querySelectorAll('.history-item');

serviceHistory.forEach(item => {
  item.addEventListener('click', () => {
    item.classList.toggle('active');  // toggle open/close

    // optional: close others when one opens
    // items.forEach(i => {
    //   if (i !== item) i.classList.remove('active');
    // });
  });
});

const complaintHistory = document.querySelectorAll('.complaint-item');

complaintHistory.forEach(item => {
  item.addEventListener('click', () => {
    item.classList.toggle('active');  // toggle open/close

    // optional: close others when one opens
    // items.forEach(i => {
    //   if (i !== item) i.classList.remove('active');
    // });
  });
});
