import { readFile } from 'node:fs/promises';
import vm from 'node:vm';
import assert from 'node:assert/strict';

const code = await readFile(new URL('../public/js/site.js', import.meta.url), 'utf8');
const records = JSON.parse(await readFile(new URL('../resources/data/projects.json', import.meta.url), 'utf8'));
class Element {
  constructor() { this.events = {}; this.attributes = {}; this.style = {}; this.value = ''; this.hidden = false; this.disabled = false; this.classes = new Set(); this.classList = { add: name => this.classes.add(name), remove: name => this.classes.delete(name), contains: name => this.classes.has(name), toggle: (name, force) => { const value = force ?? !this.classes.has(name); value ? this.classes.add(name) : this.classes.delete(name); return value; } }; }
  addEventListener(name, callback) { (this.events[name] ??= []).push(callback); }
  fire(name, event = {}) { for (const callback of this.events[name] ?? []) callback(event); }
  setAttribute(name, value) { this.attributes[name] = value; }
  getAttribute(name) { return this.attributes[name]; }
  focus() { this.focused = true; }
}
const header = new Element(), menu = new Element(), toggle = new Element(), progress = new Element(), search = new Element(), client = new Element(), result = new Element(), empty = new Element(), reset = new Element(), form = new Element(), submit = new Element();
const documentEvents = new Element(), windowEvents = new Element(), root = new Element();
root.scrollHeight = 1800; root.clientHeight = 900;
const cards = records.map(record => { const el = new Element(); const service = record.service.toLowerCase(); el.dataset = { search: [record.description,record.service,record.client,record.location,record.year].join(' ').toLowerCase(), client:record.client, category:service.includes('printing')?'printing':service.includes('construction')?'construction':service.includes('transportation')?'transport':'procurement' }; return el; });
const chips = ['all','procurement','transport','printing','construction'].map(category => { const el = new Element(); el.dataset = {filter:category}; return el; });
const portfolio = { querySelectorAll: selector => selector === '[data-project]' ? cards : chips };
const ids = {'site-menu':menu,'project-search':search,'project-client':client,'project-results':result,'project-empty':empty,'reset-projects':reset};
client.value = 'all';
menu.querySelectorAll = () => [];
header.contains = target => target === toggle || target === menu || target === header;
form.querySelector = () => submit;
form.checkValidity = () => true;
const document = {documentElement:root,hidden:false,getElementById:id=>ids[id],querySelector:selector=>({'.site-header':header,'.menu-toggle':toggle,'.scroll-progress':progress,'[data-portfolio]':portfolio,'.contact-form':form})[selector],querySelectorAll:()=>[],addEventListener:(...args)=>documentEvents.addEventListener(...args)};
const motion = new Element();motion.matches = true;
const window = {scrollY:0,innerHeight:900,innerWidth:1280,matchMedia:()=>motion,addEventListener:(...args)=>windowEvents.addEventListener(...args)};
vm.runInNewContext(code,{document,window,requestAnimationFrame:()=>1,cancelAnimationFrame:()=>{}});
let checks = 0;
const verify = (condition,label) => { assert.ok(condition,label); checks++; console.log('PASS '+label); };
verify(cards.filter(c=>!c.hidden).length===53,'All 53 assignments initially visible');
search.value='Faryab';search.fire('input');verify(cards.filter(c=>!c.hidden).length===2,'Province search returns both Faryab assignments');
client.value=records.find(r=>r.client.includes('(DRC)')).client;client.fire('change');verify(cards.every(c=>c.hidden)&&!empty.hidden,'Combined search and client filters show an empty state');
reset.fire('click');verify(cards.filter(c=>!c.hidden).length===53&&search.focused,'Reset restores portfolio and focus');
chips.find(c=>c.dataset.filter==='printing').fire('click');verify(cards.filter(c=>!c.hidden).length===5,'Printing filter returns five recorded assignments');
search.value='Logar';search.fire('input');verify(cards.filter(c=>!c.hidden).length===1,'Category and province filters combine');
verify(chips.find(c=>c.dataset.filter==='printing').getAttribute('aria-pressed')==='true','Selected service filter is announced');
search.value='not-a-real-assignment';search.fire('input');verify(!empty.hidden&&result.textContent==='0 of 53 assignments','Empty results update the live count');
toggle.fire('click');verify(menu.classList.contains('is-open')&&toggle.getAttribute('aria-expanded')==='true','Mobile menu opens with correct state');
documentEvents.fire('keydown',{key:'Escape'});verify(!menu.classList.contains('is-open')&&toggle.focused,'Escape closes menu and restores focus');
toggle.fire('click');documentEvents.fire('click',{target:{}});verify(!menu.classList.contains('is-open'),'Click outside closes navigation');
form.fire('submit');verify(submit.disabled&&submit.textContent==='Submitting…','Submitting state prevents duplicate submission');
let prevented=false;form.fire('submit',{preventDefault(){prevented=true;}});verify(prevented,'Second submission is prevented');
windowEvents.fire('pageshow');verify(!submit.disabled,'Back navigation restores the form button');
verify(progress.style.transform==='scaleX(0)','Scroll progress avoids an invalid initial value');
console.log(checks+' frontend checks passed.');
