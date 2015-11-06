function ready(fn) {
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
}

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
        u2f.sign(request, function(data){
            if(!data.errorCode) {
                codeField.value = JSON.stringify(data);
                form.submit();
            }
        });
    }

    if('reg' === type) {
        u2f.register([request[0]], request[1], function(data){
            if(!data.errorCode) {
                codeField.value = JSON.stringify(data);
                form.submit();
            }
        });
    }
});
