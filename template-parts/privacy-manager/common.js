window.addEventListener('DOMContentLoaded', function () {
  function privacySettings() {
    const form = document.getElementById('pm-form');
    if (!form) return;

    const inputs = Array.prototype.slice.call(
      form.querySelectorAll('input[type="checkbox"]')
    );
    const inputsProp = inputs.map(function (input) {
      return {
        name: input.name.trim(),
        checked: input.checked,
      };
    });

    let updateRun = 0;
    const privacyManager = new PrivacyManager({
      saveAsCookie: true,
      form: form,
      on: {
        update: function (payload) {
          updateState(payload, updateRun);
          acceptCookie(updateRun);
          updateRun++;
        },
      },
      items: inputsProp,
    });

    function acceptCookie() {
      if (updateRun > 0) {
        const cookieBar = document.querySelector('.km-cookie-notification'); // '.km-cookie-notification-restricted'
        if (!cookieBar) return;
        localStorage.setItem('KMCookiesAccepted', 'true');
        cookieBar.classList.remove('kmc-active'); // kmcr-active
      }
    }

    // Initialize restore defaults button
    const restore = document.getElementById('pm-reset');
    restore.addEventListener('click', privacyManager.restoreDefaults, false);

    // Control Icon on item
    function updateState(props, count) {
      const form = props.form;

      props.data.forEach((item) => {
        let target = form[item.name];
        let add = item.checked ? 'add' : 'remove';
        let remove = item.checked ? 'remove' : 'add';
        let parent = target.parentElement;

        if (target) {
          parent.parentElement.classList[add]('pm-item-active');
          parent.parentElement.classList[remove]('pm-item-inactive');
        }
      });

      // Show visual for updates
      if (count > 0) {
        form.parentElement.classList.add('pm-updated');

        setTimeout(() => {
          form.parentElement.classList.remove('pm-updated');
          location.reload();
        }, 1000);
      }
    }

    // Modal
    new KetchupModal({
      selectors: {
        modal: '.privacy-manager',
        trigger: '.toggle-cookie-pref',
      },
      classes: {
        modalActive: 'pm-active' || null,
        triggerActive: 'pm-active' || null,
        bodyClass: 'pm-active' || null,
      },
      offClick: {
        enabled: true,
        element: '.privacy-manager .pm-modal',
      },
    });
  }

  privacySettings();
});
