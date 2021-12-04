class NewsArticle extends HTMLElement {
    constructor() {
      super();
      this.root = this.attachShadow({ mode: 'open' });
    }
    set article(article) {
      this.root.innerHTML = `
            <style>
            .insert-interacao-submit {
              position: relative;
              width: 139px;
              height: 38px;
              background-color: #DB6624;
              color: #fff;
              border-radius: 10px;
              border-width: 0px;
              margin-top: 10px;
            }
            </style>
            <select id="select-filtro" name="select-filtro">
            <option value="${article.name}">${article.name}</option>
            <option value="${article.name2}">${article.name2}</option>
            <option value="${article.name3}">${article.name3}</option>
            </select>
            <select id=\"select-ordenar-2\" name=\"select-ordenar-2\">
            <option value=\"cres\">Cres</option>
            <option value=\"decre\">Decre</option>
            </select>
            <button class=\"insert-interacao-submit\" name=\"ordenarBtn\">Ordenar<button/>`;
    }
  }
  
  customElements.define('news-article', NewsArticle);