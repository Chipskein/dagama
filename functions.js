function verificar(){
    //validar inputs
    document.getElementById("form").submit();
};

function showRegister(){
    var login = document.getElementById('loginContainer');
    var register = document.getElementById('registerContainerHidden');
    login.id = 'loginContainerHidden';
    register.id = 'registerContainer';
}

function register(){
    //validar inputs
    document.getElementById("formRegister").submit();
}

function login(){
    var login = document.getElementById('loginContainerHidden');
    var register = document.getElementById('registerContainer');
    login.id = 'loginContainer';
    register.id = 'registerContainerHidden';
}

function openModal(id){
    var modal = document.getElementById(id);
    modal.style.opacity = 1;
    modal.style.pointerEvents = 'auto';
}
function closeModal(id){
    var modal = document.getElementById(id);
    modal.style.opacity = 0;
    modal.style.pointerEvents = 'none';
}

var postSelectArr = '';
function newPostSelect(value) {
    var insert = document.getElementsByClassName('insert-interacao')[0];
    var divLocal = document.getElementsByClassName('post-divLocal')[0];
    var divPessoas = document.getElementsByClassName('post-divPessoas')[0];
    var divAssuntos = document.getElementsByClassName('post-divAssuntos')[0];
    var hr = document.getElementById('post-hr');
    if(postSelectArr == value){
        postSelectArr = '';
        hr.style.display = 'none';
        divLocal.style.display = 'none';
        divPessoas.style.display = 'none';
        divAssuntos.style.display = 'none';
        insert.style.height = '262px';
    } else {
        insert.style.height = '400px';
        if(value == 'local'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'block';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'none';
        }
        if(value == 'pessoas'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'block';
            divAssuntos.style.display = 'none';
        }
        if(value == 'assuntos'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'block';
        }
    }
}