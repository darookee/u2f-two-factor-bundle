'use strict';

var u2fauth = u2fauth || {};

u2fauth.formId = 'u2fForm';
u2fauth.authCodeId = '_auth_code';
u2fauth.keynameId = 'u2fkeyname';
u2fauth.errorTranslation = {
    1: 'Unknown Error',
    2: 'Bad Request',
    3: 'Client configuration not supported',
    4: 'Device already registered or ineligible',
    5: 'Timeout',
};

u2fauth.ready = function(fn) {
    if ('loading' !== document.readyState){
        fn();
    } else if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        document.attachEvent('onreadystatechange', function() {
            if ('loading' !== document.readyState)
                fn();
        });
    }
};

u2fauth.authenticate = function() {
    var form = document.getElementById(u2fauth.formId);
    var codeField = document.getElementById(u2fauth.authCodeId);
    var request = JSON.parse(form.dataset.request);

    u2f.sign(request[0].appId, request[0].challenge, request, function(data){
        if(!data.errorCode) {
            codeField.value = JSON.stringify(data);
            form.submit();
        } else {
            u2fauth.showError(data.errorCode, function(){u2fauth.authenticate(request, codeField, form);});
        }
    });
};

u2fauth.register = function() {
    var keyname = document.getElementById(u2fauth.keynameId);
    keyname.style.display = "none"; 
    var form = document.getElementById(u2fauth.formId);
    var codeField = document.getElementById(u2fauth.authCodeId);
    var request = JSON.parse(form.dataset.request);

    u2f.register(request[0].appId, [request[0]], request[1], function(data){
        if(!data.errorCode) {
            codeField.value = JSON.stringify(data);
            form.submit();
        } else {
            u2fauth.showError(data.errorCode, function(){u2fauth.register(request, codeField, form);});
        }
    });
};

u2fauth.showError = function(error, callback) {
    var errorDisplay;

    errorDisplay = document.getElementById('u2fError');
    errorDisplay.innerText = u2fauth.errorTranslation[error];
    errorDisplay.onclick = callback;
};

u2fauth.ready(function(){
    var form = document.getElementById('u2fForm');
    var type = form.dataset.action;

    if('auth' === type) {
        u2fauth.authenticate();
    }
});
