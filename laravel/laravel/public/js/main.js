/* Nina.Sucks front-end template: behavior
   Sections: 1) menu  2) hero slider  3) age gate + cookie  4) feed  */
(function(){
  'use strict';
  const $  = (s,r=document)=>r.querySelector(s);
  const $$ = (s,r=document)=>[...r.querySelectorAll(s)];
  const store = {
    get(k){try{return localStorage.getItem(k)}catch(e){return null}},
    set(k,v){try{localStorage.setItem(k,v)}catch(e){}}
  };

  /* ---------- 1) mobile menu ---------- */
  const side=$('#side'), scrim=$('#scrim'), menuBtn=$('#menuBtn');
  function toggleMenu(open){
    side.classList.toggle('open',open); scrim.classList.toggle('show',open);
    menuBtn.setAttribute('aria-expanded',open);
  }
  menuBtn.addEventListener('click',()=>toggleMenu(!side.classList.contains('open')));
  scrim.addEventListener('click',()=>toggleMenu(false));
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') toggleMenu(false); });

  /* ---------- 2) hero slider ---------- */
  (function(){
    const hero=$('#hero'); if(!hero) return;              /* only on the home page */
    const slides=$$('.slide',hero), dots=$$('.dot',hero);
    if (!slides.length) return;
    let cur=0;
    function go(n){
      cur=(n+slides.length)%slides.length;
      slides.forEach((s,i)=>{ s.classList.toggle('active',i===cur); s.setAttribute('aria-hidden',i!==cur); });
      dots.forEach((d,i)=>{
        d.classList.remove('active'); d.classList.toggle('seen',i<cur);
        d.setAttribute('aria-selected',i===cur);
      });
      void hero.offsetWidth;               /* restart progress animation */
      if (dots[cur]) dots[cur].classList.add('active');
    }
    dots.forEach((d,i)=>{
      d.addEventListener('click',(e)=>{ e.preventDefault(); go(i); });
      const iEl = $('i',d);
      if (iEl) {
        iEl.addEventListener('animationend',()=>go(cur+1));   /* auto-advance */
      }
    });

    const nextBtn = $('#next');
    const prevBtn = $('#prev');
    if (nextBtn) {
      nextBtn.addEventListener('click',(e)=>{ e.preventDefault(); e.stopPropagation(); go(cur+1); });
    }
    if (prevBtn) {
      prevBtn.addEventListener('click',(e)=>{ e.preventDefault(); e.stopPropagation(); go(cur-1); });
    }

    /* Global click delegation so clicks on icons or svgs inside prev/next buttons always work */
    document.addEventListener('click', (e) => {
      const nextTarget = e.target.closest('#next, [data-slide-next]');
      if (nextTarget) { e.preventDefault(); go(cur + 1); return; }
      const prevTarget = e.target.closest('#prev, [data-slide-prev]');
      if (prevTarget) { e.preventDefault(); go(cur - 1); return; }
    });

    const pause=()=>hero.classList.add('paused'), play=()=>hero.classList.remove('paused');
    hero.addEventListener('mouseenter',pause); hero.addEventListener('mouseleave',play);
    hero.addEventListener('focusin',pause);    hero.addEventListener('focusout',play);
    document.addEventListener('visibilitychange',()=>document.hidden?pause():play());
    document.addEventListener('keydown',e=>{
      if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA')) return;
      if (e.key==='ArrowRight') go(cur+1);
      if (e.key==='ArrowLeft')  go(cur-1);
    });

    let x0=null;                                                /* swipe */
    hero.addEventListener('pointerdown',e=>{ x0=e.clientX; });
    hero.addEventListener('pointerup',e=>{
      if(x0===null) return; const dx=e.clientX-x0; x0=null;
      if(Math.abs(dx)>50) go(dx<0?cur+1:cur-1);
    });
    go(0);
  })();

  /* ---------- 3) age gate + cookie notice ----------
     The overlay is added by JavaScript only. The page content is always in the HTML,
     so search engines can read every page. Known crawlers never see the gate. */
  (function(){
    const gate=$('#gate'), cookie=$('#cookie');
    const bot=/bot|crawl|spider|slurp|bing|duckduck|baidu|yandex|facebookexternalhit|lighthouse|preview/i.test(navigator.userAgent);
    function showCookie(){ if(!bot && !store.get('cookieOk')) cookie.hidden=false; }
    if(!bot && !store.get('age18')){
      gate.hidden=false; document.body.classList.add('locked'); $('#gateYes').focus();
    } else showCookie();
    $('#gateYes').addEventListener('click',()=>{
      store.set('age18','1'); gate.hidden=true; if(!$('.modal:not([hidden])')) document.body.classList.remove('locked'); showCookie();
    });
    $('#cookieOk').addEventListener('click',()=>{ store.set('cookieOk','1'); cookie.hidden=true; });
  })();

  /* ---------- 4) feed: PHP renders the first posts, feed.php returns the next batches ----------
     Grid view = masonry columns, list view = one wide column. Order is always newest to oldest. */
  (function(){
    const feed=$('#feed'), sentinel=$('#sentinel');
    if(!feed||!sentinel) return;                           /* only on the home page */
    const sText=$('.s-text',sentinel);
    const base=(window.NINA&&window.NINA.base)||'';
    const mobileMQ=matchMedia('(max-width:640px)');
    const BATCH=6, FIRST=8;
    let items=[...feed.querySelectorAll(':scope > .card')];   /* cards printed by PHP */
    let filter=feed.dataset.type||'all', offset=parseInt(feed.dataset.next,10)||items.length;
    let view=store.get('feedView')||feed.dataset.view||'grid';
    let loading=false, done=false, cols=[];

    /* --- layout --- */
    function colCount(){ return view==='list'||mobileMQ.matches?1:2; }
    function build(){
      feed.dataset.view=view;
      feed.style.minHeight=feed.offsetHeight+'px';          /* avoid a scroll jump while rebuilding */
      feed.innerHTML=''; cols=[];
      for(let i=0;i<colCount();i++){ const c=document.createElement('div'); c.className='col'; feed.appendChild(c); cols.push(c); }
      items.forEach(place);
      feed.style.minHeight='';
    }
    function place(el){
      const shortest=cols.reduce((a,b)=>a.offsetHeight<=b.offsetHeight?a:b);
      shortest.appendChild(el);
    }
    function setView(v){
      view=v; store.set('feedView',v);
      $$('.view-toggle button').forEach(b=>{ const on=b.dataset.view===v; b.classList.toggle('is-on',on); b.setAttribute('aria-pressed',on); });
      build();
    }

    /* --- data --- */
    async function fetchCards(off,limit){
      const res=await fetch(`${base}/feed.php?offset=${off}&limit=${limit}&type=${encodeURIComponent(filter)}`,{headers:{'X-Requested-With':'fetch'}});
      if(!res.ok) throw new Error(res.status);
      const tpl=document.createElement('template'); tpl.innerHTML=(await res.text()).trim();
      return [...tpl.content.children].filter(el=>el.classList.contains('card'));
    }
    function setStatus(html,end){
      sentinel.hidden=false; sentinel.querySelector('.loader').style.display=end?'none':'';
      sText.innerHTML=html;
    }
    async function more(limit=BATCH){
      if(loading||done) return; loading=true;
      try{
        const cards=await fetchCards(offset,limit);
        if(!cards.length){ done=true; setStatus('You have reached the oldest post.',true); io.unobserve(sentinel); }
        else{ offset+=cards.length; cards.forEach(c=>{ items.push(c); place(c); }); setStatus('Loading older posts'); }
      }catch(err){
        setStatus('Could not load posts. <button type="button" id="retry">Try again</button>',true);
        $('#retry').addEventListener('click',()=>{ setStatus('Loading older posts'); loading=false; more(); },{once:true});
        return;
      }
      loading=false;
      io.unobserve(sentinel); io.observe(sentinel);          /* re-check: is the sentinel still on screen? */
    }
    async function reset(newFilter){
      filter=newFilter; offset=0; done=false; items=[]; loading=true;
      feed.innerHTML=''; setStatus('Loading posts');
      try{
        const cards=await fetchCards(0,FIRST);
        offset=cards.length; items=cards; build();
        if(!cards.length){ feed.innerHTML='<p class="empty">No posts in this format yet.</p>'; done=true; setStatus('',true); }
      }catch(err){ feed.innerHTML='<p class="empty">Could not load posts.</p>'; }
      loading=false; io.unobserve(sentinel); io.observe(sentinel);
    }

    /* interactions (like/follow/reblog/report/video) are wired sitewide in section 4b below */

    $$('.view-toggle button').forEach(b=>b.addEventListener('click',()=>setView(b.dataset.view)));

    const chips=$$('.filters .chip');
    chips.forEach(ch=>ch.addEventListener('click',e=>{
      e.preventDefault();
      chips.forEach(c=>c.classList.remove('is-on')); ch.classList.add('is-on');
      const f=ch.dataset.f;
      history.replaceState(null,'',f==='all'?location.pathname:`?type=${f}`);
      reset(f);
    }));

    mobileMQ.addEventListener('change',build);

    /* endless scroll: never blocked by a login wall */
    const io=new IntersectionObserver(entries=>{ if(entries[0].isIntersecting) more(); },{rootMargin:'900px 0px'});

    setView(view);                 /* builds the columns from the cards PHP printed */
    io.observe(sentinel);
  })();

  /* ---------- 4b) card interactions: like / follow / reblog / report / play video ----------
     Works on every page that shows post cards (home feed, trending, a blog, a single post),
     not just the infinite-scroll home page. */
  (function(){
    function requireLogin(){ if(!(window.NINA&&window.NINA.loggedIn)){ location.href=(window.NINA&&window.NINA.loginUrl)||'login.php'; return false; } return true; }
    async function postAction(path,data){
      const csrfToken = (window.NINA&&window.NINA.csrf)||'';
      const body=new URLSearchParams({_token:csrfToken,csrf:csrfToken,...data});
      const res=await fetch(`${(window.NINA&&window.NINA.base)||''}/${path}`,{
        method:'POST',
        headers:{
          'Content-Type':'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body
      });
      return res.json();
    }
    document.addEventListener('click',async e=>{
      const play=e.target.closest('.play');
      if(play){
        const fig=play.closest('figure'); const img=$('img',fig); const vid=$('video',fig);
        if(vid&&play.dataset.videoSrc){ vid.src=play.dataset.videoSrc; vid.style.display='block'; if(img) img.style.display='none'; play.style.display='none'; vid.play(); }
        return;
      }
      const like=e.target.closest('.like');
      if(like){
        if(!requireLogin()) return;
        const id=like.dataset.postId||like.closest('[data-post-id]').dataset.postId;
        like.classList.toggle('on');
        const r=await postAction('actions/like.php',{post_id:id});
        if(r.ok){ like.classList.toggle('on',r.liked); const s=like.querySelector('span'); if(s) s.textContent=r.count; }
        return;
      }
      const rb=e.target.closest('.reblog');
      if(rb){
        if(!requireLogin()) return;
        const r=await postAction('actions/reblog.php',{post_id:rb.dataset.postId});
        if(r.ok){ const s=rb.querySelector('span'); if(s) s.textContent=(parseInt(s.textContent,10)||0)+1; rb.classList.add('on'); }
        else alert(r.message||'Could not reblog.');
        return;
      }
      const rep=e.target.closest('.report');
      if(rep){
        const reason=prompt('Tell us what\'s wrong with this post:');
        if(reason===null) return;
        const r=await postAction('actions/report.php',{target_type:'post',target_id:rep.dataset.postId,reason});
        alert(r.message||'Reported.');
        return;
      }
      const f=e.target.closest('.follow');
      if(f&&f.dataset.blog){
        if(!requireLogin()) return;
        const r=await postAction('actions/follow.php',{blog:f.dataset.blog});
        if(r.ok){ f.classList.toggle('on',r.following); f.textContent=r.following?'Following':'Follow'; }
      }
    });
  })();

  /* ---------- 5) login / register / forgot-password popups ----------
     Links and buttons with data-modal="login|register|forgot" open a popup.
     Their real href (login.php etc.) still works for visitors without JavaScript. */
  (function(){
    const modals=$$('.modal'); let opener=null;
    const gateOpen=()=>!$('#gate').hidden;
    function open(name){
      const m=modals.find(x=>x.dataset.name===name); if(!m) return;
      if(!$('.modal:not([hidden])')) opener=document.activeElement;
      modals.forEach(x=>x.hidden=(x!==m));
      document.body.classList.add('locked'); toggleMenu(false);
      const f=$('input:not([type=hidden])',m); if(f&&!gateOpen()) f.focus();
    }
    function close(){
      modals.forEach(x=>x.hidden=true);
      if(!gateOpen()) document.body.classList.remove('locked');
      if(opener&&opener.focus) opener.focus();
    }
    document.addEventListener('click',e=>{
      const trg=e.target.closest('[data-modal]');
      if(trg){ e.preventDefault(); open(trg.dataset.modal); return; }
      if(e.target.classList.contains('modal')||e.target.closest('[data-close]')){ close(); return; }
      const pw=e.target.closest('.pw-toggle');
      if(pw){
        const inp=$('input',pw.parentElement), show=inp.type==='password';
        inp.type=show?'text':'password'; pw.classList.toggle('on',show);
        pw.setAttribute('aria-label',show?'Hide password':'Show password');
      }
    });
    document.addEventListener('keydown',e=>{
      const m=$('.modal:not([hidden])'); if(!m) return;
      if(e.key==='Escape'){ close(); return; }
      if(e.key==='Tab'){                                  /* keep focus inside the popup */
        const f=$$('a[href],button:not([disabled]),input:not([type=hidden]),iframe',m).filter(x=>x.offsetParent!==null);
        if(!f.length) return;
        const first=f[0], last=f[f.length-1];
        if(e.shiftKey&&document.activeElement===first){ e.preventDefault(); last.focus(); }
        else if(!e.shiftKey&&document.activeElement===last){ e.preventDefault(); first.focus(); }
      }
    });
    /* open from PHP ($open_modal) or from the URL hash, e.g. /#login */
    const start=(window.NINA&&window.NINA.openModal)||location.hash.replace('#','');
    if(['login','register','forgot'].includes(start)) open(start);
  })();
})();
