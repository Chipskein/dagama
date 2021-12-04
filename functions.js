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

// bgl la das postagens
var tmpLocal = 0;
function addLocal(){
    var local = document.getElementById('select-local').value;
    local = JSON.parse(local);
    var div = document.getElementById('divCidade');
    if(local !== 0){
    var option = document.getElementById('optionLocal'+local.id);
    option.remove();
    tmpLocal = local.id;
    const p = document.createElement('p')
    p.id='local'+local.id
    p.innerHTML += `${local.name} <button type="button" onclick="removeLocal('${local.id}', '${local.name}')">❌</button>`;
    div.append(p)
    }else{
      const buttonAddCidade = document.createElement('button')
      buttonAddCidade.textContent='adicioar cidade';
      buttonAddCidade.id = 'buttonCidade';
      buttonAddCidade.type = 'button';
      // button.onclick = () => {  }
      const inputCidade = document.createElement('input')
    inputCidade.id='InputCidade'
    inputCidade.className='StylesInputs'
    inputCidade.placeholder='cidade'
    const inputEstado = document.createElement('input')
    inputEstado.id='InputEstado'
    inputEstado.className='StylesInputs'
    inputEstado.placeholder='estado'
    const selectPais = document.createElement('select')
    selectPais.id='Inputpais'
    selectPais.className='StylesInputs'
    for(c=0;c<paises.length;c++){
      const options = document.createElement('option')
      options.value = paises[c].codigo
      options.innerHTML = paises[c].nome
      selectPais.append(options)
    }
    div.append(inputCidade)
    div.append(inputEstado)
    div.append(selectPais)
    div.append(buttonAddCidade)
    }
    document.getElementById('select-local').disabled = true;
    document.getElementById('select-local-button').disabled = true;
}
function removeLocal(id, name){
    var div = document.getElementById('divCidade');
    var p = document.getElementById('local'+id);
    var select = document.getElementById('select-local');
    select.innerHTML += `<option id='optionLocal${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    tmpLocal = 0;
    document.getElementById('select-local').disabled = false;
    document.getElementById('select-local-button').disabled = false;
}

var pessoas = [];
function addPessoas(){
    var pessoa = document.getElementById('select-pessoas').value;
    pessoa = JSON.parse(pessoa);
    var div = document.getElementById('divPessoas');
    var option = document.getElementById('optionPessoa'+pessoa.id);
    option.remove();
    pessoas.push(pessoa.id);
    const p = document.createElement('p')
    p.id='pessoas'+pessoa.id
    p.innerHTML += `${pessoa.name} <button type="button" onclick="removePessoas('${pessoa.id}', '${pessoa.name}')">❌</button>`;
    div.append(p)
}
function removePessoas(id, name){
    var div = document.getElementById('divPessoas');
    var p = document.getElementById('pessoas'+id);
    var select = document.getElementById('select-pessoas');
    select.innerHTML += `<option id='optionPessoa${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    for(var i = 0; i < pessoas.length; i++){ 
        if ( pessoas[i] == id) {
            pessoas.splice(i, 1); 
        }    
    }
}

var assuntos = [];
function addAssuntos(){
    var assunto = document.getElementById('select-assuntos').value;
    assunto = JSON.parse(assunto);
    var div = document.getElementById('divAssuntos');
    if(assunto !== 0){
    var option = document.getElementById('optionAssunto'+assunto.id);
    option.remove();
    assuntos.push(assunto.id);
    const p = document.createElement('p');
    const input = document.createElement('input');
    p.id='assunto'+assunto.id;
    p.innerHTML += `${assunto.name} <button type="button" onclick="removeAssuntos('${assunto.id}', '${assunto.name}')">❌</button>`;
    input.type = 'hidden';
    input.name= 'assunto'+assunto.id;
    input.id='assuntoInput'+assunto.id;
    input.value = assunto.id;
    div.append(p);
    div.append(input);
    } else{
        const buttonAddAssuntos = document.createElement('button')
        buttonAddAssuntos.textContent='adicioar assunto';
        buttonAddAssuntos.id = 'buttonAssunto';
        buttonAddAssuntos.type = 'button';
        // button.onclick = () => {  }
        const inputAssunto = document.createElement('input')
        inputAssunto.id='InputCidade'
        inputAssunto.className='StylesInputs'
        inputAssunto.placeholder='adicione o assunto'
        div.append(inputAssunto)
        div.append(buttonAddAssuntos)
        document.getElementById('select-assunto').disabled = true;
        document.getElementById('select-assunto-button').disabled = true;
    }
  }
function removeAssuntos(id, name){
    var div = document.getElementById('divAssuntos');
    var p = document.getElementById('assunto'+id);
    var input = document.getElementById('assuntoInput'+id);
    var select = document.getElementById('select-assuntos');
    select.innerHTML += `<option id='optionAssunto${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    input.remove();
    for(var i = 0; i < assuntos.length; i++){ 
        if ( assuntos[i] == id) {
            assuntos.splice(i, 1); 
        }    
    }
}
function unsetError(){
  console.log('rosca direta')
}