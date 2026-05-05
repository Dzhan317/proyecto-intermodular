/**
 * auth.js — Comportamientos de las pantallas de autenticación.
 * Usa window.APP_URL definido en el layout.
 */

function initPasswordToggle(inputId, buttonId, iconId) {
    var input=document.getElementById(inputId), button=document.getElementById(buttonId), icon=document.getElementById(iconId);
    if (!input||!button||!icon) return;
    var iconShow=window.APP_URL+'/assets/img/icons/ojo.svg', iconHide=window.APP_URL+'/assets/img/icons/ojos-cruzados.svg';
    button.addEventListener('click', function(){
        var isHidden=input.type==='password';
        input.type=isHidden?'text':'password'; icon.src=isHidden?iconHide:iconShow; icon.alt=isHidden?'Ocultar contraseña':'Mostrar contraseña';
    });
}

var PASSWORD_RULES={
    'req-length':  function(v){return v.length>=10;},
    'req-upper':   function(v){return(v.match(/[A-Z]/g)||[]).length>=2;},
    'req-lower':   function(v){return(v.match(/[a-z]/g)||[]).length>=2;},
    'req-number':  function(v){return(v.match(/[0-9]/g)||[]).length>=2;},
    'req-special': function(v){return/[^A-Za-z0-9]/.test(v);},
};

function initPasswordStrength(inputId) {
    var input=document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function(){
        Object.keys(PASSWORD_RULES).forEach(function(id){
            var el=document.getElementById(id); if(!el) return;
            var dot=el.querySelector('.req-dot'), ok=PASSWORD_RULES[id](input.value);
            el.classList.toggle('text-req-ok',ok); el.classList.toggle('text-req-pending',!ok);
            if(dot){dot.classList.toggle('bg-req-ok',ok); dot.classList.toggle('bg-req-pending',!ok);}
        });
    });
}

function initPasswordMatch(passwordId, confirmId, errorId) {
    var password=document.getElementById(passwordId), confirm=document.getElementById(confirmId), error=document.getElementById(errorId);
    if (!password||!confirm||!error) return;
    confirm.addEventListener('input', function(){
        error.classList.toggle('hidden', !(confirm.value.length>0 && confirm.value!==password.value));
    });
}

function initTwoFactorInputs(containerId, hiddenId, submitId, formId) {
    var container=document.getElementById(containerId), hidden=document.getElementById(hiddenId);
    var submitBtn=document.getElementById(submitId), form=document.getElementById(formId);
    if (!container||!hidden||!submitBtn||!form) return;
    var inputs=Array.from(container.querySelectorAll('input'));

    function updateHidden(){
        var code=inputs.map(function(i){return i.value;}).join('');
        hidden.value=code; submitBtn.disabled=code.length<6;
    }
    function focusInput(index){
        if(index>=0&&index<inputs.length){inputs[index].focus();inputs[index].select();}
    }

    inputs.forEach(function(input,index){
        input.addEventListener('keydown',function(e){
            if(e.key==='Backspace'){if(input.value){input.value='';}else{focusInput(index-1);}updateHidden();e.preventDefault();return;}
            if(e.key==='ArrowLeft'){focusInput(index-1);e.preventDefault();return;}
            if(e.key==='ArrowRight'){focusInput(index+1);e.preventDefault();return;}
            if(!/^\d$/.test(e.key)&&!['Tab','Enter'].includes(e.key)&&!e.ctrlKey&&!e.metaKey){e.preventDefault();}
        });
        input.addEventListener('input',function(){
            var val=input.value.replace(/\D/g,'');
            if(val.length>1){val.split('').forEach(function(digit,i){if(inputs[index+i])inputs[index+i].value=digit;});focusInput(Math.min(index+val.length,inputs.length-1));}
            else{input.value=val; if(val)focusInput(index+1);}
            updateHidden();
            if(hidden.value.length===6)form.submit();
        });
        input.addEventListener('paste',function(e){
            e.preventDefault();
            var digits=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
            digits.split('').forEach(function(digit,i){if(inputs[i])inputs[i].value=digit;});
            focusInput(Math.min(digits.length,inputs.length-1)); updateHidden();
            if(digits.length===6)form.submit();
        });
    });
    focusInput(0);
}
