let addItems = document.getElementById('addItems');
let calculadoraBtn = document.getElementById('calculadoraBtn');
let newRequest = '';

/* CUSTOM ON LOAD FUNCTIONS */
function proyectoCustomLoad() {
    "use strict";
    console.log('Functions Correctly Loaded');
}

document.addEventListener("DOMContentLoaded", proyectoCustomLoad, false);

addItems.addEventListener('click', function(e) {
    e.preventDefault();
    /* SEND AJAX */
    var info = 'action=add_calculadora_items';
    newRequest = new XMLHttpRequest();
    newRequest.open('POST', custom_admin_url.ajax_url, true);
    newRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    newRequest.onload = function() {
        var respuesta = JSON.parse(newRequest.response);
        var ajaxResponse = document.getElementById('calculadoraWrapper');
        ajaxResponse.insertAdjacentHTML('beforeend', respuesta.data);
        var deleteItems = document.getElementsByClassName('deleteItems');
        for (item of deleteItems) {
            item.addEventListener('click', delete_calculadora_item, false);
        }
    };
    newRequest.send(info);
});

calculadoraBtn.addEventListener('click', function(e) {
    var passd = true;
    e.preventDefault();
    var formEl = document.forms.calculadoraForm;
    var formData = new FormData(formEl);
    var cantidad_globos = formData.getAll('cantidad_globos[]');
    var modelo_globo = formData.getAll('modelo_globo[]');
    if (modelo_globo.length === 0) {
        passd = false;
    } else {
        for (let index = 0; index < cantidad_globos.length; index++) {
            if (modelo_globo[index] == null) {
                passd = false;
            }
        }
    }
    if (passd == true) {
        document.getElementById('calculadoraError').classList.add('no-display');
        mainCalculate(cantidad_globos, modelo_globo);
    } else {
        document.getElementById('calculadoraError').classList.remove('no-display');
    }
});

function delete_calculadora_item() {
    var currentItem = this.parentElement;
    var currentWrapper = currentItem.parentElement;
    currentWrapper.remove();
}

function mainCalculate(cantidad_globos, modelo_globo) {
    var acum = 0;
    for (let index = 0; index < cantidad_globos.length; index++) {
        var total = parseInt(cantidad_globos[index]) * parseFloat(modelo_globo[index]);
        acum += total;
    }
    document.getElementById('calculadoraNumber').innerHTML = acum;
}