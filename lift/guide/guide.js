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

  // animations: screenshots + moving cursor + caption. Start when visible, play once, replay on demand.
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var anims = document.querySelectorAll('.anim[data-frames]');
  for (var k = 0; k < anims.length; k++) setupAnim(anims[k]);

  function setupAnim(box) {
    var frames = JSON.parse(box.getAttribute('data-frames'));
    var img = box.querySelector('img'), cur = box.querySelector('.cursor'), pan = box.querySelector('.pan');
    var cap = box.querySelector('.cap'), btn = box.querySelector('.replay');
    var timer = null, i = 0, playing = false;
    frames.forEach(function (f) { var p = new Image(); p.src = f.src; });

    function show(n, done) {
      var f = frames[n];
      cap.textContent = (n + 1) + '. ' + f.cap;
      if (img.getAttribute('src') !== f.src) {
        img.classList.add('fade');
        setTimeout(function () { img.src = f.src; img.classList.remove('fade'); }, 300);
      }
      cur.style.left = f.x + '%';
      cur.style.top = f.y + '%';
      // on small screens, zoom in around the cursor so the buttons are readable
      if (pan) {
        var zoom = box.clientWidth < 640;
        pan.style.transformOrigin = f.x + '% ' + f.y + '%';
        pan.style.transform = zoom ? 'scale(2.2)' : '';
        pan.classList.toggle('z', zoom);
      }
      timer = setTimeout(function () {
        cur.classList.remove('click'); void cur.offsetWidth; cur.classList.add('click');
        timer = setTimeout(done, still ? 2200 : 1500);
      }, still ? 0 : 1100);
    }
    function next() {
      if (i >= frames.length) { playing = false; btn.textContent = '↻ שוב'; return; }
      show(i++, next);
    }
    function play() {
      clearTimeout(timer); i = 0; playing = true; btn.textContent = '■ עצירה';
      cur.style.transition = 'none'; cur.style.left = '50%'; cur.style.top = '60%';
      void cur.offsetWidth; cur.style.transition = '';
      next();
    }
    btn.addEventListener('click', function () {
      if (playing) { clearTimeout(timer); playing = false; btn.textContent = '▶ הפעלה'; } else play();
    });
    if ('IntersectionObserver' in window && !still) {
      var io = new IntersectionObserver(function (es) {
        if (es[0].isIntersecting) { io.disconnect(); play(); }
      }, { threshold: 0.6 });
      io.observe(box);
    }
  }

  // close the steps dropdown when clicking elsewhere
  document.addEventListener('click', function (e) {
    var d = document.querySelector('.prog .all[open]');
    if (d && !d.contains(e.target)) d.removeAttribute('open');
  });
})();
