(() => {
'use strict';
const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
const header = document.querySelector('.site-header');
const menu = document.getElementById('site-menu');
const toggle = document.querySelector('.menu-toggle');
const progress = document.querySelector('.scroll-progress');
const closeMenu = () => { menu?.classList.remove('is-open'); toggle?.setAttribute('aria-expanded', 'false'); };
toggle?.addEventListener('click', () => { const open = !menu.classList.contains('is-open'); menu.classList.toggle('is-open', open); toggle.setAttribute('aria-expanded', String(open)); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && menu?.classList.contains('is-open')) { closeMenu(); toggle.focus(); } });
document.addEventListener('click', e => { if (!header?.contains(e.target)) closeMenu(); });
menu?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
window.matchMedia('(max-width: 800px)').addEventListener('change', closeMenu);
let scrollFrame = 0;
const updateScroll = () => { header?.classList.toggle('is-scrolled', window.scrollY > 40); const h = document.documentElement; const distance = h.scrollHeight - h.clientHeight; if (progress) progress.style.transform = 'scaleX(' + (distance > 0 ? Math.min(1, Math.max(0, window.scrollY / distance)) : 0) + ')'; scrollFrame = 0; };
window.addEventListener('scroll', () => { if (!scrollFrame) scrollFrame = requestAnimationFrame(updateScroll); }, {passive:true});
updateScroll();
// Content stays readable without JavaScript. Only off-screen items are queued.
const revealItems = document.querySelectorAll('.rb-reveal, .rb-stagger, .reveal');
if (!motion.matches && 'IntersectionObserver' in window) {
 const observer = new IntersectionObserver(entries => entries.forEach(entry => { if (!entry.isIntersecting) return; entry.target.classList.remove('reveal-pending'); entry.target.classList.add('rb-in'); observer.unobserve(entry.target); }), {threshold:0.01, rootMargin:'0px 0px 120px 0px'});
 revealItems.forEach(el => { if (el.getBoundingClientRect().top > window.innerHeight + 50) { el.classList.add('reveal-pending'); observer.observe(el); } else el.classList.add('rb-in'); });
 document.documentElement.classList.add('motion-ready');
 motion.addEventListener('change', () => { if(motion.matches){ revealItems.forEach(el => {el.classList.remove('reveal-pending');el.classList.add('rb-in');});observer.disconnect();} });
} else revealItems.forEach(el => el.classList.add('rb-in'));
// Homepage capability cards are usable with a mouse or keyboard.
const serviceIds = ['transport','vehicles','storage','field-supplies'];
document.querySelectorAll('.rb-cap:not(.featured)').forEach((card,i) => { const url = document.querySelector('.rb-cap-link')?.href; if (!url) return; const link=document.createElement('a');link.href=url+'#'+serviceIds[i];link.className='service-card-link';link.setAttribute('aria-label','Explore '+card.querySelector('h3').textContent);card.appendChild(link); });
// Combined text search, client selection, and service category filter.
const portfolio = document.querySelector('[data-portfolio]');
if (portfolio) {
 const search = document.getElementById('project-search'); const client = document.getElementById('project-client'); const cards = [...portfolio.querySelectorAll('[data-project]')]; const result = document.getElementById('project-results'); const empty = document.getElementById('project-empty'); let category = 'all';
 const filter = () => { const query=search.value.trim().toLocaleLowerCase(); const selected=client.value;let count=0;cards.forEach(card=>{ const match=(!query||card.dataset.search.includes(query))&&(selected==='all'||card.dataset.client===selected)&&(category==='all'||card.dataset.category===category);card.hidden=!match;if(match)count++; }); result.textContent=count+' of '+cards.length+' assignments';empty.hidden=count!==0; };
 search.addEventListener('input',filter);client.addEventListener('change',filter);
 portfolio.querySelectorAll('[data-filter]').forEach(button=>button.addEventListener('click',()=>{category=button.dataset.filter;portfolio.querySelectorAll('[data-filter]').forEach(b=>b.setAttribute('aria-pressed',String(b===button)));filter();}));
 document.getElementById('reset-projects')?.addEventListener('click',()=>{search.value='';client.value='all';category='all';portfolio.querySelectorAll('[data-filter]').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.filter==='all')));filter();search.focus();});
 filter();
}
const form = document.querySelector('.contact-form');
form?.addEventListener('submit', e => { if (!form.checkValidity()) return; const button=form.querySelector('button[type=submit]'); if(button.disabled){e.preventDefault();return;}button.disabled=true;button.textContent='Submitting…'; });
window.addEventListener('pageshow',()=>{const button=form?.querySelector('button[type=submit]');if(button){button.disabled=false;button.innerHTML='Submit inquiry <span aria-hidden="true">↗</span>';}});
// A lightweight route network, drawn at a capped pixel density. Runs only in view.
document.querySelectorAll('.live-hero').forEach(hero => {
 const canvas=document.createElement('canvas');canvas.className='live-canvas';canvas.setAttribute('aria-hidden','true');hero.prepend(canvas);const ctx=canvas.getContext('2d');if(!ctx)return;
 let width=0,height=0,frame=0,visible=true,last=0,elapsed=0;
 const nodes=Array.from({length:window.innerWidth<600?20:36},()=>({x:Math.random(),y:Math.random(),vx:(Math.random()-.5)*.007,vy:(Math.random()-.5)*.007}));
 const resize=()=>{ const box=hero.getBoundingClientRect();width=box.width;height=box.height;const ratio=Math.min(window.devicePixelRatio||1,1.5);canvas.width=Math.round(width*ratio);canvas.height=Math.round(height*ratio);ctx.setTransform(ratio,0,0,ratio,0,0); };
 const draw=now=>{frame=0;if(!visible||document.hidden||motion.matches)return;const dt=last?Math.min((now-last)/1000,.04):0;last=now;elapsed+=dt;ctx.clearRect(0,0,width,height);
 for(const n of nodes){n.x+=n.vx*dt;n.y+=n.vy*dt;if(n.x<0||n.x>1)n.vx*=-1;if(n.y<0||n.y>1)n.vy*=-1;}
 const offset=width>800?width*.35:0;const area=width-offset;
 for(let i=0;i<nodes.length;i++){const a=nodes[i];const ax=offset+a.x*area,ay=a.y*height;for(let j=i+1;j<nodes.length;j++){const b=nodes[j];const bx=offset+b.x*area,by=b.y*height;const distance=Math.hypot(ax-bx,ay-by);if(distance>190)continue;ctx.strokeStyle='rgba(155,210,231,'+((1-distance/190)*.22)+')';ctx.lineWidth=.7;ctx.beginPath();ctx.moveTo(ax,ay);ctx.lineTo(bx,by);ctx.stroke();if((i+j)%7===0){const p=(elapsed*.12+i*.15)%1;ctx.fillStyle='rgba(248,141,123,.8)';ctx.beginPath();ctx.arc(ax+(bx-ax)*p,ay+(by-ay)*p,2,0,Math.PI*2);ctx.fill();}}
 ctx.fillStyle=i%5===0?'rgba(248,141,123,.65)':'rgba(180,225,242,.38)';ctx.beginPath();ctx.arc(ax,ay,i%5===0?2.5:1.5,0,Math.PI*2);ctx.fill();}
 frame=requestAnimationFrame(draw);};
 const sync=()=>{if(frame){cancelAnimationFrame(frame);frame=0;}last=0;if(visible&&!document.hidden&&!motion.matches)frame=requestAnimationFrame(draw);};
 resize();if('ResizeObserver' in window)new ResizeObserver(resize).observe(hero);else window.addEventListener('resize',resize,{passive:true});
 if('IntersectionObserver' in window)new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;sync();},{threshold:0}).observe(hero);
 document.addEventListener('visibilitychange',sync);motion.addEventListener('change',sync);sync();
});
document.addEventListener('visibilitychange',()=>document.documentElement.classList.toggle('motion-paused',document.hidden));
})();
