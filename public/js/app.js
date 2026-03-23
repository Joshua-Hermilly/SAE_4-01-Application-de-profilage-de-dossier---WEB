document.addEventListener('DOMContentLoaded', () => {

  // Duplique le contenu du ticker pour créer une boucle seamless
  const ticker = document.querySelector('.ticker-inner');
  if (ticker) {
    ticker.innerHTML += ticker.innerHTML;
  }

});
