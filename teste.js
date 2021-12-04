import './components/teste.js';

window.addEventListener('load', () => {
  getNews();
});
let article = {name:'Nome', name2:"Data", name3:'Participantes'}

 function getNews() {
  const main = document.getElementById('teste');
    const el = document.createElement('news-article');
    el.article=article
    main.appendChild(el);
  }