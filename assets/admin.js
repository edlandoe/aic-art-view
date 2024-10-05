document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".artwork-card");

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      if (card.classList.contains("selected")) {
        card.classList.remove("selected");
        card.querySelector(".artwork-radio").checked = false;
      } else {
        cards.forEach((c) => {
          c.classList.remove("selected");
          c.querySelector(".artwork-radio").checked = false;
        });

        card.classList.add("selected");
        card.querySelector(".artwork-radio").checked = true;
      }
    });
  });
});
