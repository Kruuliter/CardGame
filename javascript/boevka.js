import * as control from "./datas.js";

class Boevka {
    static cardsInBattle;
    static statusPlayer;
    static cardsInPlayer;
    static spans;
    static idInterval;
    static round;
    static flag;
    static oldTimes;
    static nowPlayer;
    
    constructor(){
        this.cardsInBattle = document.querySelector("#battle").querySelector(".one").querySelectorAll("#frame_background");
        this.statusPlayer = document.querySelector("#player_one").querySelector(".user");
        let _this = this;
        this.round = 1;
        this.flag = false;
        console.log(!this.flag);
        this.oldTimes = 0;
        this.nowPlayer = "User1";
        
        this.cardsInBattle.forEach(function(parent, index){
            parent.addEventListener('dragover', function(event){
                event.preventDefault();
            });

            parent.addEventListener('drop', function(event){
                if (_this.flag){
                    alert("не твой ход");
                    return;
                }
                
                let index = event.dataTransfer.getData('text_move');
                let current = _this.cardsInPlayer[index];

                let health = Number(_this.statusPlayer.querySelector(".mp").textContent) - Number(current.querySelector(".mana").textContent);
                

                if (health >= 0)
                {

                    while(event.currentTarget.firstChild){
                        event.currentTarget.removeChild(event.currentTarget.firstChild);
                    }

                    event.currentTarget.innerHTML = current.innerHTML;
                    current.parentNode.removeChild(current);
                    _this.statusPlayer.querySelector(".mp").innerHTML = "<div>" + String(health) + "</div>";
                }else{
                    alert("недостаточно маны");
                }
            });
        });
        
        this.updateStatusCards();
    }

    getInfo(){
        let mass = [];
        let str = ""
        for (let i = 0; i < this.cardsInBattle.length; i++){
            str += parseInt(this.cardsInBattle.querySelector(".id").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInBattle.querySelector(".hp").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInBattle.querySelector(".mana").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInBattle.querySelector(".attack").match(/\d+/));
            str += ";";
            str += this.cardsInBattle.querySelector(".name_card");
            str += ";";
            str += this.cardsInBattle.querySelector(".move");
            if(i < (this.cardsInBattle.length - 1)){
                str += "|";
            }
        }
        mass.append(str);
        str = "";
        for (let i = 0; i < this.cardsInPlayer.length; i++){
            str += parseInt(this.cardsInPlayer.querySelector(".id").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInPlayer.querySelector(".hp").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInPlayer.querySelector(".mana").match(/\d+/));
            str += ";";
            str += parseInt(this.cardsInPlayer.querySelector(".attack").match(/\d+/));
            str += ";";
            str += this.cardsInPlayer.querySelector(".name_card");
            str += ";";
            str += this.cardsInPlayer.querySelector(".move");
            if(i < (this.cardsInPlayer.length - 1)){
                str += "|";
            }
        }
        mass.append(str);
        return mass;
    }
    
    updateStatusCards(){
        this.cardsInPlayer = document.querySelector("#player_one").querySelectorAll("#frame_background");
        let _this = this;
        
        this.cardsInPlayer.forEach(function(elem, index){
            if (elem.draggable != true){
                elem.draggable = true;
                elem.addEventListener('dragstart', function(event) {
                    //event.dataTransfer.setData('text_move', this.innerHTML);
                    event.dataTransfer.setData('text_move', index);
                });
            }
        });
    }
    
    start(){
        this.initializeClock('history');
    }
    
    initializeClock(id){
        var historys = document.getElementById(id);
        this.spans = {
            'secondsSpan' : historys.querySelector('.seconds'),
            'roundsSpan' : historys.querySelector('.round'),
            'changesSpan' : historys.querySelector('.changePlayers')
        }
        this.spans.roundsSpan.innerHTML = this.round;
        this.spans.changesSpan.innerHTML = this.nowPlayer;
        let _this = this;
        this.getNewTime();
        this.updateClock(_this);
        this.idInterval = setInterval(() => {_this.updateClock(_this); }, 1000);
        console.log(this.idInterval);
    }
    
    getNewTime(){
        var secondsToAdd = 60;
        var currentDate = new Date();
        this.oldTimes = new Date(currentDate.getTime() + secondsToAdd*1000);
    }
    
    updateClock(thisClass){
        var t = thisClass.getTimerRemaining();
        thisClass.spans.secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
        if (t.total <= 0){
            if (thisClass.flag){
                thisClass.round = thisClass.round + 1;
                thisClass.nowPlayer = "P1";
            }else{
                thisClass.nowPlayer = "P2";
            }
            thisClass.spans.roundsSpan.innerHTML = this.round;
            thisClass.spans.changesSpan.innerHTML = thisClass.nowPlayer;
            thisClass.flag = !thisClass.flag;
            thisClass.getNewTime();
        }
    }
    
    getTimerRemaining(){
        var t = Date.parse(this.oldTimes) - Date.parse(new Date());
        var seconds = Math.floor(t / 1000);
        return {
            'total' : t,
            'seconds' : seconds
        };
    }
}

window.onload = function(){
    let isNext = false;
    const bove = new Boevka();
    let elem = document.querySelector("#next");
    elem.onclick = function(){
        isNext = true;
    }
    let delay = 1000;
    var updateMessage;

    let timerId = setTimeout(function request() {
        //...отправить запрос...
        var formData = new FormData();
        formData.append("user", control.getCookie("userLogin"));
        formData.append("isNext", isNext);
        formData.append("info", bove.getInfo());
        updateMessage = control.formAjax("adm/rule_battle.php", function(data){
            var settings = JSON.parse(data);
            
        });

        if (!(updateMessage.readyState>3 && updateMessage.status==200)) {
            delay = 1000;
        }else{
            delay = 1000;
        }

        timerId = setTimeout(request, delay);

    }, delay);
}

