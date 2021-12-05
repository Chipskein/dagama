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
    var divReacoes = document.getElementsByClassName('post-divReacoes')[0];
    var divCompart = document.getElementsByClassName('post-divCompart')[0];
    var hr = document.getElementById('post-hr');
    if(postSelectArr == value){
        postSelectArr = '';
        hr.style.display = 'none';
        divLocal.style.display = 'none';
        divPessoas.style.display = 'none';
        divAssuntos.style.display = 'none';
        divReacoes.style.display = 'none';
        divCompart.style.display = 'none';
        insert.style.height = '262px';
    } else {
        insert.style.height = '400px';
        if(value == 'local'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'block';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'none';
            divReacoes.style.display = 'none';
            divCompart.style.display = 'none';
        }
        if(value == 'pessoas'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'block';
            divAssuntos.style.display = 'none';
            divReacoes.style.display = 'none';
            divCompart.style.display = 'none';
        }
        if(value == 'assuntos'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'block';
            divReacoes.style.display = 'none';
            divCompart.style.display = 'none';
        }
        if(value == 'reacoes'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'none';
            divReacoes.style.display = 'block';
            divCompart.style.display = 'none';
        }
        if(value == 'compartilhar'){
            postSelectArr = value;
            hr.style.display = 'flex';
            divLocal.style.display = 'none';
            divPessoas.style.display = 'none';
            divAssuntos.style.display = 'none';
            divReacoes.style.display = 'none';
            divCompart.style.display = 'block';
        }
    }
}

function selectPais(id){
    var selectPais = id.value;
    var selectEstados = document.getElementsByClassName('select-estado-pais');
    
    var inputCodPais = document.getElementById('insert-codigo-pais');
    var inputCodEstado = document.getElementById('insert-codigo-estado');
    var inputCodCidade = document.getElementById('insert-codigo-cidade');

    for(let c = 0; c < selectEstados.length; c++){
        selectEstados[c].style.display = 'none';
    }
    var selectCidades = document.getElementsByClassName('select-cidade-estado');
    for(let c = 0; c < selectCidades.length; c++){
        selectCidades[c].style.display = 'none';
    }
    var inputPais = document.getElementById('insert-nome-pais');
    var inputEstado = document.getElementById('insert-nome-estado');
    var inputCidade = document.getElementById('insert-nome-cidade');
    inputPais.style.display = 'none';
    inputEstado.style.display = 'none';
    inputCidade.style.display = 'none';
    if(selectPais == 0){
        inputPais.style.display = 'flex';
        inputEstado.style.display = 'flex';
        inputCidade.style.display = 'flex';

        inputCodPais.value = 0;
        inputCodEstado.value = 0;
        inputCodCidade.value = 0;
    } else {
        inputCodPais.value = selectPais;
        inputCodEstado.value = '';
        inputCodCidade.value = '';
        var selectEstado = document.getElementById('select-estado-pais'+selectPais);
        selectEstado.style.display = 'flex';        
    }
}
function selectEstado(id){
    var selectEstado = id.value;
    var selectCidades = document.getElementsByClassName('select-cidade-estado');

    var inputCodEstado = document.getElementById('insert-codigo-estado');
    var inputCodCidade = document.getElementById('insert-codigo-cidade');

    for(let c = 0; c < selectCidades.length; c++){
        selectCidades[c].style.display = 'none';
    }
    var inputEstado = document.getElementById('insert-nome-estado');
    var inputCidade = document.getElementById('insert-nome-cidade');
    inputEstado.style.display = 'none';
    inputCidade.style.display = 'none';
    if(selectEstado == 0){
        inputEstado.style.display = 'flex';
        inputCidade.style.display = 'flex';
        inputCodEstado.value = 0;
        inputCodCidade.value = 0;
    } else {
        var selectCidade = document.getElementById('select-cidade-estado'+selectEstado);
        selectCidade.style.display = 'flex';
        inputCodEstado.value = selectEstado;
        inputCodCidade.value = '';
    }    
}
function selectCidade(id){
    var selectCidade = JSON.parse(id.value);
    var inputCidade = document.getElementById('insert-nome-cidade');
    inputCidade.style.display = 'none';
    var estado = id.id.split('select-cidade-estado')[1];

    var inputCodCidade = document.getElementById('insert-codigo-cidade');

    if(selectCidade.id == 0){
        inputCidade.style.display = 'flex';
        inputCodCidade.value = 0;
    } else {
        var selectCidade = document.getElementById('select-cidade-estado'+estado);
        selectCidade.style.display = 'flex';
        inputCodCidade.value = selectCidade.id;
    }  
}

function addLocal(){
    var selectPais = document.getElementById('select-pais');
    selectPais.style.display = 'none';
    var selectEstados = document.getElementsByClassName('select-estado-pais');
    for(let c = 0; c < selectEstados.length; c++){
        selectEstados[c].style.display = 'none';
    }
    var selectCidades = document.getElementsByClassName('select-cidade-estado');
    for(let c = 0; c < selectCidades.length; c++){
        selectCidades[c].style.display = 'none';
    }
    var inputPais = document.getElementById('insert-nome-pais');
    var inputEstado = document.getElementById('insert-nome-estado');
    var inputCidade = document.getElementById('insert-nome-cidade');
    inputPais.style.display = 'none';
    inputEstado.style.display = 'none';
    inputCidade.style.display = 'none';
    var btn = document.getElementById('select-local-button');
    var div = document.getElementById('divCidade');
    if(selectPais.value == 0){
        var p = document.createElement('p');
        p.id = 'localNovo';
        p.innerHTML += `${inputCidade.value} <button type="button" onclick="removeLocal('${0}', '${inputCidade.value}')">❌</button>`;
        div.append(p);
        btn.style.display = 'none';
    } else {
        var pais = selectPais.value;
        var estado = document.getElementById('select-estado-pais'+pais).value;
        if(estado == 0){
            var p = document.createElement('p');
            p.id = 'localNovo';
            p.innerHTML += `${inputCidade.value} <button type="button" onclick="removeLocal('${0}', '${inputCidade.value}')">❌</button>`;
            div.append(p);
            btn.style.display = 'none';
        } else {
            var cidade = JSON.parse(document.getElementById('select-cidade-estado'+estado).value);
            if(cidade.id == 0){
                var p = document.createElement('p');
                p.id = 'localNovo';
                p.innerHTML += `${inputCidade.value} <button type="button" onclick="removeLocal('${0}', '${inputCidade.value}')">❌</button>`;
                div.append(p);
                btn.style.display = 'none';
            } else {
                var div = document.getElementById('divCidade');
                var option = document.getElementById('optionCidade'+cidade.id);
                option.remove();
                const p = document.createElement('p');
                const input = document.createElement('input');
                p.id = 'cidade'+cidade.id;
                p.innerHTML += `${cidade.name} <button type="button" onclick="removeLocal('${cidade.id}', '${cidade.name}')">❌</button>`;
                input.type = 'hidden';
                input.id = 'cidadeInput'+cidade.id;
                input.name = 'cidade';
                input.value = cidade.id;
                div.append(p);
                div.append(input);
                btn.style.display = 'none';
            }
        }
    }
}
function removeLocal(id, name){
    var div = document.getElementById('divCidade');
    if(id == 0 ){
        var p = document.getElementById('localNovo');
        p.remove();
        var inputCodPais = document.getElementById('insert-codigo-pais');
        var inputCodEstado = document.getElementById('insert-codigo-estado');
        var inputCodCidade = document.getElementById('insert-codigo-cidade');
        inputCodPais = '';
        inputCodEstado = '';
        inputCodCidade = '';

        var inputPais = document.getElementById('insert-nome-pais');
        var inputEstado = document.getElementById('insert-nome-estado');
        var inputCidade = document.getElementById('insert-nome-cidade');
        inputPais = '';
        inputCidade = '';
        inputEstado = '';
    } else {
        var p = document.getElementById('local'+id);
        var select = document.getElementById('select-local');
        select.innerHTML += `<option id='optionLocal${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
        p.remove();
    }
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
    const p = document.createElement('p');
    const input = document.createElement('input');
    p.id='pessoas'+pessoa.id;
    p.innerHTML += `${pessoa.name} <button type="button" onclick="removePessoas('${pessoa.id}', '${pessoa.name}')">❌</button>`;
    input.type = 'hidden';
    input.id = 'pessoaInput'+pessoa.id;
    input.name = 'pessoa'+pessoa.id;
    input.value = pessoa.id;
    div.append(p);
    div.append(input)
}
function removePessoas(id, name){
    var div = document.getElementById('divPessoas');
    var p = document.getElementById('pessoas'+id);
    var input = document.getElementById('pessoaInput'+id);
    var select = document.getElementById('select-pessoas');
    select.innerHTML += `<option id='optionPessoa${id}' value='{ "id": "${id}", "name": "${name}" }'>${name}</option>\n`;
    p.remove();
    input.remove();
    for(var i = 0; i < pessoas.length; i++){ 
        if ( pessoas[i] == id) {
            pessoas.splice(i, 1); 
        }    
    }
}

let cNewAssunto = 0;
function selectAssunto(id){
    var assunto = JSON.parse(id.value);
    var div = document.getElementById('divNovosAssuntos');
    var input = document.getElementById('insert-nome-assunto');
    input.style.display = 'none';
    if(assunto == 0){
        input.style.display = 'flex'
    }
}
var assuntos = [];
function addAssuntos(){
    var assunto = document.getElementById('select-assuntos').value;
    assunto = JSON.parse(assunto);
    var div = document.getElementById('divAssuntos');
    var divNew = document.getElementById('divNewAssuntos');
    if(assunto != 0){
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
        cNewAssunto++;
        var input = document.getElementById('insert-nome-assunto');
        var newInput = "<input id=\"insert-new-assunto"+cNewAssunto+"\" name=\"insert-new-assunto"+cNewAssunto+"\" type=\"hidden\" value=\""+input.value+"\">";
        divNew.innerHTML += newInput;
        const p = document.createElement('p');
        p.id='newAssunto'+cNewAssunto;
        p.innerHTML += `${input.value} <button type="button" onclick="removeNewAssuntos('newAssunto${cNewAssunto}', 'insert-new-assunto${cNewAssunto}')">❌</button>`;
        div.append(p);
        var input = document.getElementById('insert-nome-assunto');
        input.style.display = 'none';
        if(cNewAssunto >= 5) {
            var button = document.getElementById('select-assunto-button');
            button.disabled = true;
            alert('Você atingiu o limite de 5 assuntos novos');
        }
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
function removeNewAssuntos(idP, idInput){
    cNewAssunto--;
    var input = document.getElementById(idInput);
    input.remove();
    var p = document.getElementById(idP);
    p.remove();
    var qtd = document.getElementById('newAssuntosQtd');
    if(cNewAssunto < 5) {
        var button = document.getElementById('select-assunto-button');
        button.disabled = false;
    }
}

var reacoes = '';
function addReacoes(){
    var reacao = document.getElementById('select-reacoes').value;
    reacao = JSON.parse(reacao);
    var div = document.getElementById('divReacoes');
    console.log(reacao);
    var option = document.getElementById('optionReacao'+reacao.id);
    var btn = document.getElementById('select-reacao-button');
    option.remove();
    reacoes = reacao.id;
    const p = document.createElement('p');
    const input = document.createElement('input');
    p.id = 'reacao'+reacao.id;
    p.innerHTML += `${reacao.name} ${reacao.id} <button type="button" onclick="removeReacoes('${reacao.id}', '${reacao.name}')">❌</button>`;
    input.type = 'hidden';
    input.name= 'reacao';
    input.id= 'reacaoInput'+reacao.id;
    input.value = reacao.id;
    btn.disabled = true;
    div.append(p);
    div.append(input);
}
function removeReacoes(id, name){
    var div = document.getElementById('divReacoes');
    var p = document.getElementById('reacao'+id);
    var input = document.getElementById('reacaoInput'+id);
    var select = document.getElementById('select-reacoes');
    select.innerHTML += `<option id='optionReacao${id}' value='{ "id": "${id}", "name": "${name}" }'>${name} ${id}</option>\n`;
    p.remove();
    input.remove();
    reacoes = '';
    var btn = document.getElementById('select-reacao-button');
    btn.disabled = false;
}


function unsetError(){
  console.log('Eae');
}