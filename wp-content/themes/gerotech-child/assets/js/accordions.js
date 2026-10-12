/**
 * Accordions — one open drawer per group.
 *
 * Any container marked `data-accordion="single"` keeps at most one of its
 * direct child <details> open at a time. Groups are keyed by the element's own
 * parent, so nested <details> are left alone.
 *
 * The `toggle` event does not bubble, but it still runs the capture phase, so a
 * capture listener on `document` sees every <details> on the page — including
 * ones injected after load (the ES card modals clone a <template> into
 * `.mcs-modal__detail` when a card is opened).
 *
 * Deliberately NOT applied to the mobile-nav disclosure groups
 * (`.mobile-nav__group`): those are navigation, not an accordion, and may be
 * opened independently. They are not marked with `data-accordion`.
 */
(function () {
  var CONTAINER = '[data-accordion="single"]';

  document.addEventListener('toggle', function (event) {
    var details = event.target;
    if (!details || details.tagName !== 'DETAILS' || !details.open) return;

    var group = details.parentElement;
    if (!group || !group.closest(CONTAINER)) return;

    Array.prototype.forEach.call(group.children, function (child) {
      if (child === details) return;
      if (child.tagName === 'DETAILS' && child.open) child.open = false;
    });
  }, true);
})();
