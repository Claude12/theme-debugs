function initManualModals(){
  const triggers = Array.prototype.slice.call(document.getElementsByClassName('km-cta-popup-trigger'));
  triggers.forEach(trigger => trigger.addEventListener('click', showModal,false));
}

function showModal(item){
  const trigger = item.currentTarget;
  const target = trigger.dataset.modal;
  const modals = Array.prototype.slice.call(document.getElementsByClassName('km-timed-modal'));

  modals.forEach(modal => {
    if(trigger && modal && modal.dataset.kmModalSskey === target) {
      document.body.classList.add('kmm-active');
      modal.classList.add('km-modal-visible');
    }
  });
}

initManualModals();