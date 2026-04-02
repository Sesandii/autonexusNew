const items = document.querySelectorAll('.schedule-item');

items.forEach(item => {
  item.addEventListener('click', () => {
    item.classList.toggle('active');  // toggle open/close

    // optional: close others when one opens
    // items.forEach(i => {
    //   if (i !== item) i.classList.remove('active');
    // });
  });
});
