var ready = function(fn) {
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

var authenticate = function(request, codeField, form) {
    u2f.sign(request, function(data){
        if(!data.errorCode) {
            codeField.value = JSON.stringify(data);
            form.submit();
        } else {
            showError(data.errorCode, function(){authenticate(request, codeField, form);});
        }
    });
};

var register = function(request, codeField, form) {
    u2f.register([request[0]], request[1], function(data){
        if(!data.errorCode) {
            codeField.value = JSON.stringify(data);
            form.submit();
        } else {
            showError(data.errorCode, function(){register(request, codeField, form);});
        }
    });
};

var showError = function(error, callback) {
    var errorDisplay;

    errorDisplay = document.getElementById('u2fError');
    errorDisplay.innerText = error;
    errorDisplay.onclick = callback;
};

ready(function(){
    var form,
        codeField,
        request,
        type;

    form = document.getElementById('u2fForm');
    codeField = document.getElementById('_auth_code');

    type = form.dataset.action;
    request = JSON.parse(form.dataset.request);

    if('auth' === type) {
        authenticate(request, codeField, form);
    }

    if('reg' === type) {
        register(request, codeField, form);
    }
});
