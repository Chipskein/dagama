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