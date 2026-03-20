


kmIntersect(Array.from(document.querySelectorAll(".km-counter-block.animation-on")), {
  observerProps: {
    threshold: [0.9] // 90% of element is visible
  },
  on: (item, observer) => {
    const instance = item.target;
    const numbers = Array.from(instance.getElementsByClassName('km-cbi-number'));
    numbers.forEach(number => {
      animateValue(number, 0, number.dataset.number, 500);
    });
    observer.unobserve(instance); // Run animation just once
  }
});




