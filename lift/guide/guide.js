/* Lift guide: remembers, only in this browser, which steps were done and where the visitor stopped. */
(function () {
  var KEY = 'lift_guide_wp';
  function load() {
    try { return JSON.parse(localStorage.getItem(KEY)) || { done: [], last: '' }; }
    catch (e) { return { done: [], last: '' }; }
  }
  function save(s) { try { localStorage.setItem(KEY, JSON.stringify(s)); } catch (e) {} }

  var s = load();
  var here = document.querySelector('[data-step]');
  if (here) { s.last = here.getAttribute('data-step'); save(s); }

  // tick done steps in every list of steps
  var items = document.querySelectorAll('.steps li[data-s]');
  for (var i = 0; i < items.length; i++) {
    if (s.done.indexOf(items[i].getAttribute('data-s')) > -1) items[i].classList.add('done');
  }

  // "done, next" marks the step as done
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('[data-done]');
    if (!a) return;
    var id = a.getAttribute('data-done');
    if (s.done.indexOf(id) < 0) s.done.push(id);
    save(s);
  });

  // overview: offer to continue where the visitor stopped
  var box = document.getElementById('resume');
  if (box && s.last) {
    var li = document.querySelector('.steps li[data-s="' + s.last + '"]');
    if (li) {
      var link = li.querySelector('a');
      var go = box.querySelector('a');
      go.href = link.getAttribute('href');
      box.querySelector('span').textContent = link.textContent;
      box.classList.add('on');
    }
  }

  // close the steps dropdown when clicking elsewhere
  document.addEventListener('click', function (e) {
    var d = document.querySelector('.prog .all[open]');
    if (d && !d.contains(e.target)) d.removeAttribute('open');
  });
})();
