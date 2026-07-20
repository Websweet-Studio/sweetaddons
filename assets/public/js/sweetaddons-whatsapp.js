(function () {
    var widget = document.getElementById('sweetaddons-whatsapp-widget');
    if (!widget) {
        return;
    }

    var panel = document.getElementById('sweetaddons-wa-panel');
    var trigger = widget.querySelector('.sweetaddons-wa-trigger');
    if (!panel || !trigger) {
        return;
    }

    function openPanel() {
        panel.hidden = false;
        panel.classList.add('is-open');
        widget.classList.add('sweetaddons-wa-panel-open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closePanel() {
        panel.classList.remove('is-open');
        panel.hidden = true;
        widget.classList.remove('sweetaddons-wa-panel-open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', function () {
        if (panel.hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    document.addEventListener('click', function (e) {
        if (panel.hidden) {
            return;
        }
        if (widget.contains(e.target)) {
            return;
        }
        closePanel();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) {
            closePanel();
        }
    });
})();
