function addCardClickListeners() {
    // Chwytamy wszystkie ikonki z kartami kredytowymi
    var creditCards = document.getElementsByClassName("credit-card-icon");

    // Dodajemy dla każdej ikonki nasłuchiwacz dla kliknięcia
    for (var i=0; i<creditCards.length; i++) {
        creditCards[i].addEventListener('click', selectCreditCard);
    }
}

function selectCreditCard(id) {
    // Najpierw usuwamy zaznaczenie ze wszystkich ikonek
    var creditCards = document.getElementsByClassName("credit-card-icon");
    for (var i=0; i<creditCards.length; i++) {
        creditCards[i].classList.remove("card-selected");
    }

    // Dodajemy zaznaczenie tylko dla klikniętej ikonki
    var card = document.getElementById(id.target.id);
    card.classList.add("card-selected");
}


addCardClickListeners();