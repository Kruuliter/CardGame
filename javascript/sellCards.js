import * as control from "./datas.js";

var params = window
    .location
    .search
    .replace('?','')
    .split('&')
    .reduce(
        function(p,e){
            var a = e.split('=');
            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        },
        {}
    );

let mainForSell = document.getElementsByClassName("cards_sell")[0];
let buyCards = mainForSell.querySelectorAll('button');
buyCards.forEach(function(elem){
    elem.addEventListener('click', function(event){
        var formData = new FormData();
        formData.append("user", control.getCookie("userLogin"));
        formData.append("idCard", this.id);
        control.formAjax("adm/buyCard.php", formData, function(data){
            var settings = JSON.parse(data);
            if(settings["bougth"]){
                elem.remove();
            }else{
                if(settings["errCash"] != undefined){
                    alert("что-то пошло не так\n" + settings["errCash"]);
                }

                if(settings["err"] != undefined){
                    alert("что-то пошло не так\n" + settings["err"]);
                }
            }
        });
    });
});
// https://ru.stackoverflow.com/questions/453355/javascript-получить-get-параметр