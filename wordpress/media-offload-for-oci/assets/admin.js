// OCI Offload admin JS (migrated from inline <script> to enqueue)
document.addEventListener('DOMContentLoaded', function () {
  // Region custom input toggle (replaces inline onchange)
  var regionSelect = document.getElementById('region');
  var customWrap = document.getElementById('region_custom_wrap');
  function toggleCustom() {
    if (!regionSelect || !customWrap) return;
    customWrap.style.display = (regionSelect.value === 'custom') ? 'block' : 'none';
  }
  if (regionSelect) {
    regionSelect.addEventListener('change', toggleCustom);
    toggleCustom();
  }

  // Health check
  var hb  = document.getElementById('artimeof-btn') || document.getElementById('oci-health-btn');
  var out = document.getElementById('artimeof-out') || document.getElementById('oci-health-out');
  if (hb) {
    hb.addEventListener('click', function (ev) { ev.preventDefault();
      hb.disabled = true; hb.textContent = 'Checking...';
      var params = new URLSearchParams();
      params.append('action', 'artimeof_health');
      params.append('_ajax_nonce', (window.artimeof && artimeof.nonce) ? artimeof.nonce : '');
      var ajaxUrl = (window.artimeof && artimeof.ajaxUrl) ? artimeof.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
      fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: params.toString()
      })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        hb.disabled = false; hb.textContent = 'Run Health Check';
        if (j && j.success) {
          var url = (j.data && j.data.url) ? j.data.url : '';
          out.textContent = 'OK — ' + url;
        } else {
          var msg = (j && j.data && j.data.msg) ? j.data.msg : 'error';
          out.textContent = 'Failed — ' + msg;
        }
      })
      .catch(function (e) {
        hb.disabled = false; hb.textContent = 'Run Health Check';
        alert(e);
      });
    });
  }
});
