class CardsForSell{
    static arr;
    static idButton;
    cardsForSell;
    sellCardsArr;
    allButtons;
    maxCards = 21;
    allCards = 34;
    
    constructor(){        
        this.allButtons = document.querySelector("#buttons");
        this.cardsForSell = document.querySelector(".cards");
    }
    
    constructorExample(){
        this.arr = new Array();
        this.idButton = 0;
        
        for (let i = 0; i< this.allCards; i++){
            this.arr.push(this.generations('Белочка - ' + String(i)));
        }
        
        if (this.arr.length > 0){
            for (let i = 0; i < (this.arr.length / this.maxCards); i++){
                this.allButtons.innerHTML += "<button id=\"" + String(i) +"\">" + String(i+1) + "</button>";
            }
        }
        
        this.vizibles(this.idButton);
    }
    
    generations(n){
        let r = Math.floor(Math.random() * 60) + 32;
        let m = Math.floor(Math.random() * 20) + 1;
        let a = Math.floor(Math.random() * 10) + 1;
        let h = Math.floor(r - m - a);
        if (h < 0){
            h = -1 *h;
        }
        
        return {
            'name' : n,
            'cell' : r,
            'mana' : m,
            'attack' : a,
            'health' : h
        };
    }
    
    vizibles(idButtons){
        this.sellCardsArr = new Array();
        for (let i = 0; i < this.maxCards; i++){
             this.sellCardsArr.push(this.arr[(i + idButtons * this.maxCards)]);
        }
        
        this.idButton = idButtons;
        this.cardsForSell.innerHTML = "";
        
        for (let i = 0; i < this.sellCardsArr.length; i++){
            const card = this.sellCardsArr[i];
            this.cardsForSell.innerHTML += `<div class="sell">
                                                <div id="frame_background">
                                                    <div id="image_card" style="background-image: url(../png/Screenshot_27.png);">
                                                        <div id="card_frame">
                                                            <div class="settings_up">
                                                                <div class="mana">
                                                                    <div>`+String(card.mana)+`</div>
                                                                </div>
                                                            </div>
                                                            <div class="settings_center">
                                                                <div class="name_card">
                                                                    `+card.name+`
                                                                </div>
                                                            </div>
                                                            <div class="settings_down">
                                                                <div class="attack">
                                                                    <div>`+String(card.attack)+`</div>
                                                                </div>
                                                                <div class="hp">
                                                                    <div>`+String(card.health)+`</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                           </div>`;
        }
    }
}

const auks = new CardsForSell();
if (!Array.isArray(auks.arr)){
    auks.constructorExample();
}

let buttons = document.querySelector("#buttons").querySelectorAll("button");

buttons.forEach(function(elem, index){
    elem.addEventListener("click", function(event){
        auks.vizibles(Number(event.srcElement.id));
    });
});

// https://ru.stackoverflow.com/questions/453355/javascript-получить-get-параметр