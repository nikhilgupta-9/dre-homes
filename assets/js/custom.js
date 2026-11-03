/*
 * DRE Homes — our own JavaScript for the website (rule.md 9.1a).
 * Loaded on every page after the design's main.js and site.js (common/footer.php).
 * Pages never contain inline <script> code: add behaviour here, grouped by page,
 * and attach it to elements by id / class / data-* so it only runs where those elements exist.
 */
(function () {
  "use strict";

  /* ---- Walls with a "Show more" button (Our Media, Our Team, Testimonials, …):
     load the next page automatically on scroll, instead of waiting for a click.
     Markup from site.js: <div class="md-more"><button id="xxMore">Show more …</button></div>;
     the wrapper div is hidden by site.js once every item is shown. The button itself is left
     working and visible, so a keyboard or screen-reader visitor can still use it directly. ---- */
  var wraps = document.querySelectorAll(".md-more");
  if (!wraps.length || !("IntersectionObserver" in window)) return;

  // Only once the visitor has actually scrolled — a tall viewport must not
  // auto-load everything the moment the page opens.
  var scrolled = false;
  window.addEventListener(
    "scroll",
    function () {
      scrolled = true;
    },
    { passive: true, once: true }
  );

  wraps.forEach(function (wrap) {
    var button = wrap.querySelector("button[id]");
    if (!button) return;

    var busy = false;
    var safety = 0; // stops a runaway loop if a page's "no more items" state is ever missed

    var observer = new IntersectionObserver(
      function (entries) {
        var entry = entries[0];
        if (!scrolled || !entry.isIntersecting || wrap.hidden || busy || safety > 500) return;
        busy = true;
        safety++;
        button.click();
        // site.js re-renders synchronously, so the next frame has the final hidden state
        requestAnimationFrame(function () {
          busy = false;
          if (wrap.hidden) observer.disconnect();
        });
      },
      { rootMargin: "0px 0px 600px 0px" } // start loading a little before the button is actually on screen
    );
    observer.observe(wrap);
  });

  /* ---- Brand film: handle local video playback if chosen in admin ---- */
  (function () {
    var f = document.getElementById("filmFrame");
    var b = document.getElementById("filmPlay");
    if (!f || !b) return;
    if (b.dataset.source === "local" && b.dataset.video) {
      b.onclick = function (e) {
        if (e) {
          e.preventDefault();
          e.stopPropagation();
        }
        var v = document.createElement("video");
        v.src = b.dataset.video;
        v.controls = true;
        v.autoplay = true;
        v.playsInline = true;
        v.style.position = "absolute";
        v.style.inset = "0";
        v.style.width = "100%";
        v.style.height = "100%";
        v.style.objectFit = "cover";
        f.appendChild(v);
        f.classList.add("playing");
        v.play();
      };
    }
  })();
})();

