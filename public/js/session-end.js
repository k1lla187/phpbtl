/**
 * Đăng xuất khi đóng tab - gọi sendBeacon để hủy session trên server.
 * Không gọi khi user click link/form (điều hướng nội bộ).
 */
(function() {
    var baseUrl = (typeof window.APP_BASE_URL !== 'undefined') ? window.APP_BASE_URL : '';
    var sessionEndUrl = baseUrl + '/Auth/sessionEnd';
    var isNavigating = false;

    function markNavigating() {
        isNavigating = true;
    }

    function onBeforeUnload() {
        if (!isNavigating && navigator.sendBeacon) {
            navigator.sendBeacon(sessionEndUrl);
        }
    }

    // Khi click link cùng domain -> không gửi beacon (đang điều hướng)
    document.addEventListener('click', function(e) {
        var a = e.target.closest('a');
        if (a && a.href) {
            try {
                var linkHost = new URL(a.href).host;
                if (linkHost === window.location.host) {
                    markNavigating();
                }
            } catch (err) {}
        }
    }, true);

    // Khi submit form -> không gửi beacon
    document.addEventListener('submit', function() {
        markNavigating();
    }, true);

    window.addEventListener('beforeunload', onBeforeUnload);
    window.addEventListener('pagehide', onBeforeUnload);
})();
