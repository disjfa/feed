document.addEventListener('click', (evt) => {
  const element = evt.target.closest('.js-scroll-into-view');
  if (!element) {
    return;
  }

  const { target } = element.dataset;
  const el = document.querySelector(target);
  if (!el) {
    return;
  }
  evt.preventDefault();
  el.scrollIntoView({ behavior: "smooth", block: "start", inline: "start" })
  return false;
});
