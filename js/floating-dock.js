// Floating dock: drag handle, AI assistant, live video call and the dark mode switch.
// WhatsApp is a plain link. The dark theme itself lives in css/dark-mode.css.
(function () {
    'use strict';

    var POSITION_KEY = 'dre_dock_pos';
    var THEME_KEY = 'dre_theme';
    var EDGE = 8;

    var dock = document.getElementById('floating-dock');
    if (!dock) {
        return;
    }
    document.documentElement.classList.add('has-floating-dock');

    function read(key) {
        try {
            return window.localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function write(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch (error) {
            // Private mode or full storage: the dock still works for this page.
        }
    }

    // ---------------------------------------------------------------
    // Dragging (position is kept as a share of the free space, so it
    // survives a resize or a phone rotation)
    // ---------------------------------------------------------------
    var handle = dock.querySelector('.drag-handle');
    var drag = null;

    function freeSpace() {
        return {
            x: Math.max(window.innerWidth - dock.offsetWidth - EDGE * 2, 1),
            y: Math.max(window.innerHeight - dock.offsetHeight - EDGE * 2, 1)
        };
    }

    function place(shareX, shareY) {
        var space = freeSpace();
        shareX = Math.min(Math.max(shareX, 0), 1);
        shareY = Math.min(Math.max(shareY, 0), 1);
        dock.style.left = Math.round(EDGE + shareX * space.x) + 'px';
        dock.style.top = Math.round(EDGE + shareY * space.y) + 'px';
        dock.classList.add('is-placed');
        dock.classList.toggle('is-left', shareX < 0.5);
        return { x: shareX, y: shareY };
    }

    function savedPosition() {
        try {
            var saved = JSON.parse(read(POSITION_KEY) || 'null');
            if (saved && isFinite(saved.x) && isFinite(saved.y)) {
                return saved;
            }
        } catch (error) {
            // Unreadable value: fall back to the default spot.
        }
        return null;
    }

    var position = savedPosition();
    if (position) {
        position = place(position.x, position.y);
    }

    if (handle) {
        handle.addEventListener('pointerdown', function (event) {
            if (event.button) {
                return;
            }
            var box = dock.getBoundingClientRect();
            drag = { offsetX: event.clientX - box.left, offsetY: event.clientY - box.top };
            dock.classList.add('is-dragging');
            handle.setPointerCapture(event.pointerId);
            event.preventDefault();
        });

        handle.addEventListener('pointermove', function (event) {
            if (!drag) {
                return;
            }
            var space = freeSpace();
            position = place(
                (event.clientX - drag.offsetX - EDGE) / space.x,
                (event.clientY - drag.offsetY - EDGE) / space.y
            );
        });

        var drop = function () {
            if (!drag) {
                return;
            }
            drag = null;
            dock.classList.remove('is-dragging');
            if (position) {
                write(POSITION_KEY, JSON.stringify(position));
            }
        };
        handle.addEventListener('pointerup', drop);
        handle.addEventListener('pointercancel', drop);
    }

    window.addEventListener('resize', function () {
        if (position) {
            position = place(position.x, position.y);
        }
    });

    // ---------------------------------------------------------------
    // AI assistant: opens or closes the chat widget from js/ai-chat.js
    // ---------------------------------------------------------------
    var aiButton = dock.querySelector('[data-dock="ai"]');
    if (aiButton) {
        aiButton.addEventListener('click', function () {
            var chat = document.querySelector('.dre-ai');
            if (!chat) {
                return;
            }
            var target = chat.classList.contains('dre-ai-open')
                ? chat.querySelector('.dre-ai-header button[title="Close"]')
                : chat.querySelector('.dre-ai-launcher');
            if (target) {
                target.click();
            }
        });
    }

    // ---------------------------------------------------------------
    // Live video call: the Popin widget loaded in the footer
    // ---------------------------------------------------------------
    var videoButton = dock.querySelector('[data-dock="video"]');
    if (videoButton) {
        videoButton.addEventListener('click', function () {
            if (typeof window.Popin === 'function') {
                window.Popin('open');
            }
        });
    }

    // ---------------------------------------------------------------
    // Dark mode
    // ---------------------------------------------------------------
    var themeButton = document.getElementById('theme-toggle');

    function showTheme(dark) {
        if (dark) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        if (!themeButton) {
            return;
        }
        var label = dark ? 'Light Mode' : 'Dark Mode';
        var icon = themeButton.querySelector('i');
        themeButton.setAttribute('aria-pressed', dark ? 'true' : 'false');
        themeButton.setAttribute('aria-label', label);
        themeButton.setAttribute('data-label', label);
        if (icon) {
            icon.className = dark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
    }

    showTheme(read(THEME_KEY) === 'dark');

    if (themeButton) {
        themeButton.addEventListener('click', function () {
            var dark = document.documentElement.getAttribute('data-theme') !== 'dark';
            showTheme(dark);
            write(THEME_KEY, dark ? 'dark' : 'light');
        });
    }
})();
