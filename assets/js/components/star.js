document.addEventListener('click', (evt) => {
  const element = evt.target.closest('.js-star');
  if (!element) {
    return;
  }
  const icon = element.querySelector('i');
  if (!icon) {
    return;
  }

  fetch(element.href, {
    headers: {
      'Content-Type': 'application/json',
    }
  })
    .then(response => {
      response.json()
        .then(data => {
          icon.classList = [];
          if (data.starred) {
            icon.classList.add(...element.dataset.starred.split(' '));
          } else {
            icon.classList.add(...element.dataset.unstarred.split(' '));
          }
        });
    });
  evt.preventDefault();
  return false;
});
