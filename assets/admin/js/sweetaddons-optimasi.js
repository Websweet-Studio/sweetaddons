/* global jQuery, SweetaddonsRedis */
(function ($) {
    'use strict';

    var SWA = SweetaddonsRedis;

    function showFeedback(msg, type) {
        var $el = $('#sweetaddons-redis-feedback');
        $el.removeClass('notice-success notice-error')
            .addClass('notice notice-' + (type === 'success' ? 'success' : 'error') + ' inline')
            .html('<p>' + msg + '</p>')
            .show();
    }

    function setButtonLoading($btn, loadingText) {
        $btn.prop('disabled', true).data('orig-text', $btn.text()).text(loadingText);
    }

    function resetButton($btn) {
        $btn.prop('disabled', false).text($btn.data('orig-text'));
    }

    function getConfig() {
        return {
            host: $('#sweetaddons-redis-host').val().trim() || '127.0.0.1',
            port: parseInt($('#sweetaddons-redis-port').val(), 10) || 6379
        };
    }

    // Tes koneksi
    $('#sweetaddons-redis-test').on('click', function () {
        var $btn = $(this);
        setButtonLoading($btn, SWA.i18n.testing);
        $('#sweetaddons-redis-feedback').hide();

        $.post(SWA.ajax_url, {
            action: 'sweetaddons_redis_test',
            nonce: SWA.nonce,
            host: getConfig().host,
            port: getConfig().port
        }, function (resp) {
            resetButton($btn);
            if (resp.success) {
                var msg = resp.data.message;
                if (resp.data.version) {
                    msg += ' (' + resp.data.version + ')';
                }
                showFeedback(msg, 'success');
            } else {
                showFeedback(resp.data.message, 'error');
            }
        }).fail(function () {
            resetButton($btn);
            showFeedback('AJAX error - coba lagi.', 'error');
        });
    });

    // Flush cache (dengan konfirmasi)
    $('#sweetaddons-redis-flush').on('click', function () {
        if (!confirm(SWA.i18n.confirmFlush)) {
            return;
        }
        var $btn = $(this);
        setButtonLoading($btn, SWA.i18n.flushing);
        $('#sweetaddons-redis-feedback').hide();

        $.post(SWA.ajax_url, {
            action: 'sweetaddons_redis_flush',
            nonce: SWA.nonce
        }, function (resp) {
            resetButton($btn);
            if (resp.success) {
                showFeedback(resp.data.message, 'success');
            } else {
                showFeedback(resp.data.message, 'error');
            }
        }).fail(function () {
            resetButton($btn);
            showFeedback('AJAX error - coba lagi.', 'error');
        });
    });

    // Refresh statistik
    $('#sweetaddons-redis-refresh-stats').on('click', function () {
        var $btn = $(this);
        setButtonLoading($btn, SWA.i18n.loading);

        $.post(SWA.ajax_url, {
            action: 'sweetaddons_redis_stats',
            nonce: SWA.nonce
        }, function (resp) {
            resetButton($btn);
            if (resp.success && resp.data) {
                var d = resp.data;
                $('#sweetaddons-redis-stats [data-stat="driver"]').text(d.driver || '—');
                $('#sweetaddons-redis-stats [data-stat="hits"]').text(d.hits);
                $('#sweetaddons-redis-stats [data-stat="misses"]').text(d.misses);
                $('#sweetaddons-redis-stats [data-stat="ratio"]').text(d.ratio + '%');
            } else {
                showFeedback(resp.data ? resp.data.message : 'Gagal memuat statistik.', 'error');
            }
        }).fail(function () {
            resetButton($btn);
            showFeedback('AJAX error - coba lagi.', 'error');
        });
    });

    // Install & aktivasi plugin Redis Object Cache
    $('#sweetaddons-install-redis-plugin').on('click', function () {
        var $btn = $(this);
        setButtonLoading($btn, 'Menginstall...');
        $('#sweetaddons-redis-feedback').hide();

        $.post(SWA.ajax_url, {
            action: 'sweetaddons_redis_install_plugin',
            nonce: SWA.nonce
        }, function (resp) {
            resetButton($btn);
            if (resp.success) {
                showFeedback(resp.data.message, 'success');
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                showFeedback(resp.data.message, 'error');
            }
        }).fail(function () {
            resetButton($btn);
            showFeedback('AJAX error - coba lagi.', 'error');
        });
    });

})(jQuery);
