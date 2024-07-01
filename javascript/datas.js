export function formAjax(url, form, success) {
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
        if (xhr.status==500) {
            if( xhr.responseText.length !== 0 && log === false){
                console.log(xhr.responseText);
            }
        }
    };
    xhr.send(form);
    return xhr;
}

export function getPhpMessage(url, success) {
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('GET', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
        if (xhr.status==500) {
            if( xhr.responseText.length !== 0 && log === false){
                console.log(xhr.responseText);
            }
        }
    };
    xhr.send();
    return xhr;
}

export function getCookie(param){
    var values = "";
    var cookieMassive = new Array();
    cookieMassive = document.cookie.split('; ');

    for (var i = 0; i <cookieMassive.length; i++){
          var values = cookieMassive[i].split('=');
          var key = values[0];
          var valuer = values[1];
          if (key == param){
                values = valuer;
                break;
          }
    }

    return values;
}
