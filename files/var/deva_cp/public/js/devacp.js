/*! DevA 2 - github.com/mignz/DevA2 */

function requestServiceRestart(service, callback) {
  $.ajax({
    url: '/reload/' + service,
    timeout: 5000
  }).done(callback);
}

function showNewVersionBadge() {
  $('#versions_header .btn').append('<span class="red-dot"></span>');
}

function checkPhalconVersion() {
  var currentPhalconVer = $('.phalcon-version-actual').html();
  $.getJSON('https://api.github.com/repos/phalcon/cphalcon/tags', function (data) {
    var latestPhalconVersion = data[0].name;
    $('.phalcon-version').html(latestPhalconVersion.replace('v', ''));
    if (latestPhalconVersion.replace('v', '') != currentPhalconVer) {
      showNewVersionBadge();
      $('.phalcon-version').append(' &mdash; <a href="/upgrade"><small>CLICK TO UPGRADE</small></a>');
    }
  });
}

function getPHPVersion() {
  $.getJSON('/version/php', function (data) {
    var major = Object.keys(data);
    var lastMajor = major[major.length - 1];
    $('.php-version').html(data[lastMajor].version);
  });
}

function getDeva2Version() {
  $.ajax({
    url: 'https://raw.githubusercontent.com/mignz/DevA2/master/files/etc/deva_version',
    success: function (data) {
      $('.deva-version').html(data);
      if ($('.deva-version-actual').html() != data.trim()) {
        showNewVersionBadge();
        $('.deva-version').html('<span class="badge badge-success">' + data + '</span>');
      }
    }
  });
}

function requestPhalconUpgrade() {
  if (confirm('Phalcon will now be upgraded, it may take a few minutes...\nPress OK to start.')) {
    $.ajax({
      url: '/upgrade/sh',
      timeout: 0
    }).done(function (data) {
      $('.phalcon-upgrade').html('<pre>' + data + '</pre>');
      window.scrollTo(0, document.body.scrollHeight);
      requestServiceRestart('php-fpm', function () {
        if (confirm('Phalcon upgraded! Click OK to go back or Cancel to see the log.')) {
          window.location = '/';
        }
      });
    });
  } else {
    window.location = '/';
  }
}

function enableTabInput(id) {
  var el = document.getElementById(id);
  el.onkeydown = function (e) {
    if (e.keyCode === 9) {
      var val = this.value,
        start = this.selectionStart,
        end = this.selectionEnd;
      this.value = val.substring(0, start) + "    " + val.substring(end);
      this.selectionStart = this.selectionEnd = start + 4;
      return false;
    }
  };
}

function testEmail() {
  var email = prompt('Email address to send to:');
  window.location = '/test/' + email;
}

$(function () {
  // Get latest services versions
  if ($('.phalcon-version').length > 0) {
    checkPhalconVersion();
    getPHPVersion();
    getDeva2Version();
  }
  // Request phalcon upgrade
  if ($('.phalcon-upgrade').length > 0) {
    requestPhalconUpgrade();
  }
  // Handle service restart request
  $('.restart-service').click(function (e) {
    e.preventDefault();
    requestServiceRestart($(this).attr('service'), function (res) {
      if (res != '1') {
        alert('Failed to restart service!');
      } else {
        alert('Service is restarting!\nPlease allow a few seconds and refresh the page.');
      }
    });
  });
  // Enable tooltips and popovers
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover();
  // Form validator
  var forms = document.getElementsByClassName('needs-validation');
  var validation = Array.prototype.filter.call(forms, function (form) {
    form.addEventListener('submit', function (event) {
      if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
  // Virtual host form automations
  if ($('#websiteconfig').length > 0) {
    // Auto fill path based on the domain name
    $('#domainname').blur(function () {
      if ($('#websitepath').val() == '') {
        $('#websitepath').val($(this).val());
      }
    });
    // Enable tab input on textarea
    enableTabInput('customconfig');
    // Show custom config textarea when timely
    $(document).on('change', '#websiteconfig', function () {
      if ($(this).val() == 'custom') {
        $('.custom-config').show();
      } else {
        $('.custom-config').hide();
      }
    });
  }
});
