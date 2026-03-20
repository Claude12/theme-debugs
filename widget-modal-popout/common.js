window.addEventListener('DOMContentLoaded', function() {
  function initModals() {

    // Initiate all timed actions
    const modals = Array.from(document.querySelectorAll('.km-timed-modal'));
    
    modals.forEach(function(modal, index) {
      let counter = index + 1;
      let sessionStorageKey = modal.dataset.kmModalSskey || 'timed-modal-' + counter;
      let modalType = modal.dataset.modalType || 'timed';
      let btns = Array.from(modal.querySelectorAll('.ktm-close'));

      btns.forEach(function(btn) {
        btn.addEventListener(
          'click',
          function() {
            modal.classList.remove('km-modal-visible');
            let existingModals = Array.from(document.querySelectorAll('.km-modal-visible'));
            if (existingModals.length === 0)  setTimeout(() => document.body.classList.remove('kmm-active'), 200);
          },
          false
        );
      });

      // Manual popup? no need for timed action
      if(modalType === 'manual') return;

      if (!sessionStorage.getItem(sessionStorageKey + '-popped')) {
        let props = {
          time: modal.dataset.kmModalTime || 10,
          sskey: sessionStorageKey,
          on: {
            end: () => activateModal(modal,sessionStorageKey)
          }
        };

        new TimedAction(props);

      }
    });
  }

  function activateModal(modal,sessionStorageKey) {
    document.body.classList.add('kmm-active');
    modal.classList.add('km-modal-visible');
    sessionStorage.setItem(sessionStorageKey + '-popped', true);
  }

  initModals();
});

