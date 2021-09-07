adminClicked(document.getElementById('admin-permission'));

Array.from(document.querySelectorAll('[id^="server-all"]')).forEach(function (e) {
  serverAllPermissionsClicked(e);
});

function adminClicked(element) {
  let adminChecked = element.checked;

  Array.from(document.querySelectorAll('[id^="server-"]')).forEach(function (e) {
    if (adminChecked) {
      e.checked = false;
      if (!e.hasAttribute('disabled')) {
        e.setAttribute('disabled', true);
      }
    } else {
      if (e.hasAttribute('disabled')) {
        e.removeAttribute('disabled');
      }
    }
  });
}

function serverAllPermissionsClicked(element) {
  if (element.hasAttribute('disabled'))
    return;

  let serverChecked = element.checked;

  let id = element.id.split('-')[2];

  let elements = [];
  elements.push(document.getElementById('server-' + id + '-view'));
  elements.push(document.getElementById('server-' + id + '-console'));
  elements.push(document.getElementById('server-' + id + '-commands'));
  elements.push(document.getElementById('server-' + id + '-edit'));
  elements.push(document.getElementById('server-' + id + '-ftp'));

  elements.forEach(function (e) {
    if (serverChecked) {
      e.checked = false;
      if (!e.hasAttribute('disabled')) {
        e.setAttribute('disabled', true);
      }
    } else {
      if (e.hasAttribute('disabled')) {
        e.removeAttribute('disabled');
      }
    }
  });
}

function validateUserForm() {
  let email = document.getElementById('email');
  let password = document.getElementById('password');
  let passwordConfirm = document.getElementById('password-confirm');
  let requiredFieldsError = document.getElementById('required-fields-error');
  let passwordMatchError = document.getElementById('password-match-error');

  if (email.value === '') {
    requiredFieldsError.classList.remove('no-display');
    return false;
  }

  if (password.value !== '') {
    if (passwordConfirm.value === '') {
      if (!passwordMatchError.classList.contains('no-display')) {
        passwordMatchError.classList.add('no-display');
      }

      requiredFieldsError.classList.remove('no-display');
      return false;
    } else if (password.value !== passwordConfirm.value) {
      if (!requiredFieldsError.classList.contains('no-display')) {
        requiredFieldsError.classList.add('no-display');
      }

      passwordMatchError.classList.remove('no-display');
      return false;
    }
  }

  return true;
}
