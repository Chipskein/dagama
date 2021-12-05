function verifydate(day,month,year){
    let day_qt //31,30,29,28
    let bissexto = false;
    if ((year % 4 == 0 && year % 100 !== 0) || (year % 400 == 0)) bissexto = true;//
    switch (month) {
      case 1:
        day_qt = 31
        break
      case 2:
        if (bissexto) day_qt = 29
        else day_qt = 28
        break
      case 3:
        day_qt = 31
        break
      case 4:
        day_qt = 30
        break
      case 5:
        day_qt = 31
        break
      case 6:
        day_qt = 30
        break
      case 7:
        day_qt = 31
        break
      case 8:
        day_qt = 31
        break
      case 9:
        day_qt = 30
        break
      case 10:
        day_qt = 31
        break
      case 11:
        day_qt = 30
        break
      case 12:
        day_qt = 31
        break
    }
    if (day <= day_qt) return true
    else return false
}
function verificar(){
    let email=document.getElementsByName("email")[0];
    let pass=document.getElementsByName("password")[0];
    let regex=new RegExp("^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$");
    if(pass.value.trim()!=''){
        if(!regex.test(email.value)){
            email.value='';
            email.focus();
        }
        else document.getElementById("form").submit();
    }
    else {
        pass.value='';
        pass.focus();
    }
};

function showRegister(){
    var login = document.getElementById('loginContainer');
    var register = document.getElementById('registerContainerHidden');
    login.id = 'loginContainerHidden';
    register.id = 'registerContainer';
}

function register(){
    //validar inputs
    let passed=false;
    let username=document.getElementsByName("username")[0];
    let email=document.getElementsByName("email")[1];
    let pass=document.getElementsByName("password")[1];
    let cpass=document.getElementsByName("cpassword")[0];
    let genero=document.getElementsByName("genero")[0];
    let bdate=document.getElementsByName("bdate")[0];
    let pais=document.getElementsByName("pais")[0];
    let estado=document.getElementsByName("estado")[0];
    let cidade=document.getElementsByName("cidade")[0];
    let newpais=document.getElementsByName("newpais")[0];
    let newestado=document.getElementsByName("newestado")[0];
    let newcidade=document.getElementsByName("newcidade")[0];
    let termos=document.getElementsByName("termos")[0];
    let regex_number=new RegExp("")
    let regex_email=new RegExp("^[a-zA-Z0-9\.]*@[a-z0-9\.]*\.[a-z]*$");
    let regex_bdate=new RegExp("^[0-9]*-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])$");
    let regex_text=new RegExp("^([a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?(( [a-z0-9áạàảãăắặằẳẵâấậầẩẫéẹèẻẽêếệềểễíịìỉĩóọòỏõôốộồổỗơớợờởỡúụùủũưứựừửữýỵỳỷỹđA-ZÁẠÀẢÃĂẮẶẰẲẴÂẤẬẦẨẪÉẸÈẺẼÊẾỆỀỂỄÍỊÌỈĨÓỌÒỎÕÔỐỘỒỔỖƠỚỢỜỞỠÚỤÙỦŨƯỨỰỪỬỮÝỴỲỶỸĐ]+)?)+$");
    if(username.value.trim()==''){
        username.value='';
        username.focus();
        return
    }
    if(!regex_email.test(email.value)){
        email.value='';
        email.focus();
        return;
    }
    if(pass.value!=cpass.value){
        pass.value='';
        cpass.value='';
        pass.focus();
        return
    }
    if(pass.value.trim()==''){
        pass.value='';
        pass.focus();
    }
    if(!termos.checked){
        termos.focus();
        return
    }
    if(genero.value!='M'&&genero.value!='F'&&genero.value!='O'){
        genero.focus();
        return
    }
    if(regex_bdate.test(bdate.value)){
        console.log(bdate.value);
        let year=parseInt(bdate.value.substr(0,4));
        let month=parseInt(bdate.value.substr(5,2));
        let day=parseInt(bdate.value.substr(8,2));
        console.log(year);
        console.log(month);
        console.log(day);
        if(!verifydate(day,month,year)){
            bdate.value='';
            bdate.focus();
            return;
        };
    }
    else{
        bdate.value='';
        bdate.focus();
        return;
    }
    if(pais.value=='null'){
        pais.focus();
        return
    }
    if(estado.value=='null'){
        estado.focus();
        return
        
    }
    if(cidade.value=='null'){
        cidade.focus();
        return
    }
    if(pais.value=='outro'){
        if(!regex_text.test(newpais.value)){
            newpais.value='';
            newpais.focus();
            return
        }
    }
    if(estado.value=='outro'){
        if(!regex_text.test(newestado.value)){
            newestado.value='';
            newestado.focus();
            return
        }
    }
    if(cidade.value=='outro'){
        if(!regex_text.test(newcidade.value)){
            newcidade.value='';
            newcidade.focus();
            return
        }
    }
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