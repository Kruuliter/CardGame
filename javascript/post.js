import * as control from "./datas.js";

function addChilds(alph){
    
    let childs = document.createElement("a");
    childs.classList.add("usersMessage");
    if (alph.who == control.getCookie("userLogin")){
        childs.innerHTML = "Вы";
    }else{
        childs.innerHTML = alph.who;
    }
    childs.setAttribute("href", "#");
    
    let parents = document.createElement("div");
    parents.classList.add("dataUsers");
    parents.appendChild(childs);
    
    childs = document.createElement("div");
    childs.classList.add("datatime");
    childs.innerHTML = alph.date;
    
    parents.appendChild(childs);
    
    childs = parents;
    
    parents = document.createElement("div");
    parents.classList.add("containerMessage");
    parents.setAttribute("id", alph.id);
    parents.appendChild(childs);
    
    childs = document.createElement("div");
    childs.classList.add("message");
    childs.innerHTML = alph.what;
    
    parents.appendChild(childs);
    
    return parents;
}


var idLastMessage = 0;
let messagesBox = document.getElementsByClassName('messages');
control.getPhpMessage("adm/getMessage.php", function(data){
    var settings = JSON.parse(data);
    if(settings["get"]){
        var massM = new Array();
        for (var i = 0; i < settings["countM"]; i++){
            massM.push({
                id : settings[i]["id"],
                who : settings[i]["who"],
                what : settings[i]["what"],
                date : settings[i]["whenDate"]
            });
        }

        massM.sort(function(a, b){
            return new Date(a.date) - new Date(b.date);
        });

        idLastMessage = massM[massM.length - 1].id;

        for (var i = 0; i < massM.length; i++){
            messagesBox[0].appendChild(addChilds(massM[i]));
        }

    }else{
        if(settings["err"] != undefined){
            alert("что-то пошло не так\n" + settings["err"]);
        }
    }
});

//messagesBox[0].scrollHeight(messagesBox[0].childElementCount);

let butt = document.getElementById('send');

butt.onclick = function(){
    let textValue = document.getElementById('sendText');
    if (textValue.value != ""){
        var formData = new FormData();
        formData.append("user", control.getCookie("userLogin"));
        formData.append("message", textValue.value);
        control.formAjax("adm/sendMessage.php", formData, function(data){
            var settings = JSON.parse(data);
            if(!settings["send"]){
                if(settings["err"] != undefined){
                    alert("что-то пошло не так\n" + settings["err"]);
                    console.log(settings["err"]);
                }
            }
        });
        textValue.value = "";
    }
};

let delay = 500;
let itSend = false;
var updateMessage;

let timerId = setTimeout(function request() {
    //...отправить запрос...
    if (!itSend){
        itSend = !itSend;
        updateMessage = control.getPhpMessage("adm/getMessage.php", function(data){
            var settings = JSON.parse(data);
            if(settings["get"]){
                var massM = new Array();
                for (var i = 0; i < settings["countM"]; i++){
                    massM.push({
                        id : settings[i]["id"],
                        who : settings[i]["who"],
                        what : settings[i]["what"],
                        date : settings[i]["whenDate"]
                    });
                }
        
                massM.sort(function(a, b){
                    return new Date(a.date) - new Date(b.date);
                });

                if(idLastMessage != massM[massM.length - 1].id){
                    idLastMessage = massM[massM.length - 1].id;
                    messagesBox[0].appendChild(addChilds(massM[massM.length - 1]));
                }
            }else{
                if(settings["err"] != undefined){
                    alert("что-то пошло не так\n" + settings["err"]);
                }
            }
            itSend = false;
        });
    }

    console.log(delay);

    if (!(updateMessage.readyState>3 && updateMessage.status==200)) {
        delay = 500;
    }else{
        delay += 100;
    }

    timerId = setTimeout(request, delay);

}, delay);