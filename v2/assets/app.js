'use strict';
const $ = (selector, root = document) => root.querySelector(selector);
const params = new URLSearchParams(location.search);
const favorites = new Set();
let favoritesReady = Promise.resolve();
function node(tag, text, className) { const el = document.createElement(tag); if (text != null) el.textContent = String(text); if (className) el.className = className; return el; }
function link(text, url, className) { const el = node('a', text, className); el.href = url; return el; }
function detailUrl(id, extra = {}) { return 'index.php?' + new URLSearchParams({v: 'More', id, ...extra}); }
function toast(message) { const el = $('#toast'); el.textContent = message; el.hidden = false; clearTimeout(toast.timer); toast.timer = setTimeout(() => { el.hidden = true; }, 3500); }
async function api(endpoint, query = {}, body = null, signal = null) {
    const url = new URL('api/index.php', location.href); url.search = new URLSearchParams({endpoint, ...query});
    const headers = body ? {'X-CSRF-Token': $('meta[name="csrf-token"]')?.content || ''} : {};
    const response = await fetch(url, {method: body ? 'POST' : 'GET', body: body ? new URLSearchParams(body) : null, headers, signal, credentials: 'same-origin', cache: 'no-store'});
    let result; try { result = await response.json(); } catch { throw new Error('The server returned an invalid response.'); }
    if (!response.ok || !result.ok) { if (response.status === 401 && !['Login','Register','Forget','Reset'].includes(document.body.dataset.view)) location.href = 'index.php?v=Login'; throw new Error(result.error?.msg || result.data?.msg || 'Request failed.'); }
    return result.data;
}
function image(url, alt, className = '') { const el = node('img', null, className); el.alt = alt; el.loading = 'lazy'; el.decoding = 'async'; const parsed = new URL(url || 'assets/poster.svg', location.href); el.src = ['https:', location.protocol].includes(parsed.protocol) ? parsed.href : 'assets/poster.svg'; el.addEventListener('error', () => { el.src = 'assets/poster.svg'; }, {once:true}); return el; }
function skeletons(container) { container.replaceChildren(...Array.from({length:6}, () => node('div', null, 'skeleton'))); container.setAttribute('aria-busy','true'); }
function rowError(container, error, retry) { container.removeAttribute('aria-busy'); const box=node('div',null,'row-error');box.append(node('p',error.message));if(retry){const button=node('button','Try again','button secondary');button.onclick=retry;box.append(button);}container.replaceChildren(box); }
function empty(container, text) {container.replaceChildren(node('p', text, 'empty-state'));container.removeAttribute('aria-busy');}
async function toggleFavorite(id, button) {
    const selected = favorites.has(id); button.disabled = true;
    try { await api('Favorites',{action:selected ? 'remove' : 'add'},{title_id:id}); if(selected) favorites.delete(id); else favorites.add(id); updateFavorites(); toast(selected ? 'Removed from My List' : 'Added to My List'); }
    catch(error){toast(error.message);}finally{button.disabled=false;}
}
function updateFavorites() { document.querySelectorAll('[data-favorite]').forEach(button => {const selected=favorites.has(button.dataset.favorite);button.setAttribute('aria-pressed',String(selected));button.textContent=button.classList.contains('favorite-button') ? (selected ? '♥' : '♡') : (selected ? 'Remove from My List' : 'Add to My List');}); }
function card(item, extra = {}) {
    const el=node('article',null,'movie-card');const anchor=link('',detailUrl(item.id,extra),'poster-link');anchor.append(image(item.poster,item.title));
    if(item.rating!=null) anchor.append(node('span','★ '+Number(item.rating).toFixed(1),'rating'));
    const fav=node('button','♡','favorite-button');fav.type='button';fav.dataset.favorite=item.id;fav.setAttribute('aria-label','Toggle favorite: '+item.title);fav.onclick=()=>toggleFavorite(item.id,fav);
    const title=node('h3',item.title);title.title=item.title;
    el.append(anchor,fav,title,node('div',[item.year,item.media_type==='tv' ? 'Series' : item.media_type==='movie' ? 'Movie' : 'Source title'].filter(Boolean).join(' · '),'meta'));return el;
}
function renderCards(container, items, append = false) { if(!append) container.replaceChildren(); const seen=new Set([...container.children].map(el=>el.dataset.id)); for(const item of items){if(seen.has(item.id))continue;const el=card(item);el.dataset.id=item.id;container.append(el);seen.add(item.id);}container.removeAttribute('aria-busy');updateFavorites(); }
async function loadProviderRow(container, id, useHero = false) {
    skeletons(container);
    try {const data=await api('Home',{server:id,page:1});renderCards(container,data.shows);if(!data.shows.length)empty(container,'No titles returned.');if(data.cache==='stale')container.title='Showing the last successful catalog while a refresh is queued.';
        if(useHero && data.shows[0]) {const item=data.shows[0];$('#hero-title').textContent=item.title;$('#hero-overview').textContent=item.overview || 'Discover this title and choose from its available sources.';$('#hero-link').href=detailUrl(item.id);$('#hero-link').textContent='View title';if(item.backdrop)$('#hero-art').style.backgroundImage=`url("${item.backdrop.replace(/["\\\n\r]/g,'')}")`;}
    } catch(error){rowError(container,error,()=>loadProviderRow(container,id,useHero));}
}
async function home() {
    loadProviderRow($('#movies-row'),14,true);
    const series=$('#series-row');skeletons(series);
    const observer=new IntersectionObserver(entries=>{if(entries.some(entry=>entry.isIntersecting)){observer.disconnect();loadProviderRow(series,13);}}, {rootMargin:'100px'});observer.observe(series);
    try {const recent=await api('Catalog');renderCards($('#recent-row'),recent.shows);if(!recent.shows.length)empty($('#recent-row'),'Browse a source to discover more titles.');}catch(error){rowError($('#recent-row'),error);}
    try {const data=await api('Main');const box=$('#provider-links');for(const provider of data.servers){if(provider.enabled)box.append(link(provider.name,'index.php?'+new URLSearchParams({v:'Search',provider:provider.id}),'button secondary'));}}catch(error){toast(error.message);}
    try {const data=await api('History');const resumable=data.history.filter(item=>item.tracking==='direct' && !Number(item.completed));if(resumable.length){$('#resume-row').hidden=false;for(const item of resumable.slice(0,12))$('#resume-items').append(historyCard(item));}}catch(error){toast(error.message);}
}
async function search() {
    const form=$('#search-form'),container=$('#search-results'),status=$('#search-status'),more=$('#load-more');let page=1,controller=null,hasMore=false;
    const main=await api('Main');for(const provider of main.servers.filter(p=>p.enabled)){const option=node('option',provider.name);option.value=provider.id;$('#provider-select').append(option);}
    for(const [key,value] of params){const field=form.elements.namedItem(key);if(field && key!=='v')field.value=value;}
    async function run(append=false) {
        if(controller)controller.abort();controller=new AbortController();const active=controller; if(!append){page=1;skeletons(container);}else more.disabled=true;
        status.textContent='Loading…';const data=Object.fromEntries(new FormData(form));
        try {
            let result;
            if(data.provider){result=await api('Home',{server:data.provider,page,search:data.search},null,active.signal);if(data.year || data.language || data.genre || data.media_type){const local=await api('Catalog',{...data,page},null,active.signal);result={...local,has_more:local.has_more || result.has_more};}}
            else result=await api('Catalog',{...data,page},null,active.signal);
            if(active!==controller)return;
            renderCards(container,result.shows,append);hasMore=result.has_more;more.hidden=!hasMore;status.textContent=result.cache==='stale' ? 'Showing cached results while a refresh is queued.' : `${container.children.length} titles`;
            if(!container.children.length)empty(container,'No titles found. Try another source or title.');
        } catch(error){if(error.name!=='AbortError'){status.textContent=error.message;if(!append)rowError(container,error,()=>run());else page--;}}
        finally{if(active===controller){more.disabled=false;container.removeAttribute('aria-busy');}}
    }
    form.onsubmit=event=>{event.preventDefault();run();};more.onclick=()=>{if(hasMore){page++;run(true);}};await run();
}
function historyCard(item) {
    const el=card({id:item.title_id,title:item.label || item.title,poster:item.poster,media_type:item.media_type},{source:item.source_id,href:item.href,resume:'1'});
    if(item.tracking==='direct'){const progress=node('progress',null,'progress-bar');progress.max=Number(item.duration_seconds)||1;progress.value=Number(item.position_seconds);el.append(progress,node('div',`${Math.floor(Number(item.position_seconds)/60)} min watched${Number(item.completed) ? ' · Completed' : ''}`,'meta'));}
    else el.append(node('div','Recently opened embed','meta'));return el;
}
async function lists(view) {
    const container=$(view==='Favorites' ? '#favorites-results' : '#history-results');skeletons(container);
    try {const data=await api(view==='Favorites' ? 'Favorites' : 'History');const items=view==='Favorites' ? data.favorites : data.history;container.replaceChildren();for(const item of items)container.append(view==='Favorites' ? card(item) : historyCard(item));if(!items.length)empty(container,view==='Favorites' ? 'Your list is empty. Discover a title and tap its heart.' : 'Nothing watched yet.');updateFavorites();container.removeAttribute('aria-busy');}
    catch(error){rowError(container,error);}
    if(view==='History')$('#clear-history').onclick=async()=>{if(!confirm('Clear all your viewing history?'))return;try{await api('History',{action:'clear'},{});await lists(view);toast('History cleared.');}catch(error){toast(error.message);}};
}
async function details() {
    const id=params.get('id');const status=$('#detail-status');let title,source,episodes=[],selected=null,playerCleanup=()=>{},seasonHref=null;
    function showError(error){status.textContent=error.message;status.className='error';}
    try {title=await api('Title',{id});$('#detail-title').textContent=title.title;$('#detail-type').textContent=title.media_type==='unknown' ? 'SOURCE TITLE · TYPE NOT YET VERIFIED' : title.media_type==='tv' ? 'SERIES' : 'MOVIE';$('#detail-overview').textContent=title.overview;$('#detail-meta').textContent=[title.year,title.rating!=null ? '★ '+Number(title.rating).toFixed(1) : '',title.language].filter(Boolean).join(' · ');if(title.backdrop){$('#detail-backdrop').src=title.backdrop;$('#detail-backdrop').hidden=false;}const fav=$('#detail-favorite');fav.dataset.favorite=id;fav.onclick=()=>toggleFavorite(id,fav);updateFavorites();for(const entry of title.sources){const option=node('option',entry.label);option.value=entry.id;$('#detail-source').append(option);}if(params.get('source'))$('#detail-source').value=params.get('source');}
    catch(error){showError(error);$('#show-streams').disabled=true;return;}
    function activeEntry(){return title.sources.find(entry=>entry.id===$('#detail-source').value);}
    async function loadDetails(href=null){playerCleanup();$('#player').replaceChildren();$('#playback-section').hidden=true;source=activeEntry();if(!source)return;status.textContent='Loading seasons and details…';status.className='';selected=null;
        try {const data=await api('More',{source:source.id,...(href ? {href} : {})});seasonHref=href;episodes=data.episodes;$('#detail-overview').textContent=data.overview || title.overview;$('#season-controls').replaceChildren();$('#episodes').replaceChildren();if(data.backdrop){$('#detail-backdrop').src=data.backdrop;$('#detail-backdrop').hidden=false;}
            for(const season of data.seasons){const button=node('button',season.title || 'Season '+season.season_number,'button secondary');button.onclick=()=>loadDetails(season.link);$('#season-controls').append(button);}
            for(const episode of episodes){const button=node('button',null,'episode');if(episode.poster)button.append(image(episode.poster,''));button.append(node('strong',(episode.episode_number!=null ? episode.episode_number+'. ' : '')+episode.title));if(episode.air_date)button.append(node('small',episode.released===false ? 'Not yet released · '+episode.air_date : episode.air_date));button.disabled=episode.released===false;button.onclick=()=>chooseEpisode(episode);$('#episodes').append(button);}
            status.textContent=data.retrieval_status==='unclassified' ? 'This source did not expose a season list. You can still request playback sources; its media type is not assumed.' : data.episodes.length ? 'Choose an episode to see playback sources.' : 'Movie details loaded. Choose a playback source below.';
            $('#show-streams').hidden=episodes.length>0;
            const requested=params.get('href');if(requested && episodes.some(ep=>ep.link===requested))chooseEpisode(episodes.find(ep=>ep.link===requested));
            else if(requested && Number(source.provider_id)===13 && /^\d+\/\d+\/\d+$/.test(requested) && !href){const [show,season]=requested.split('/');await loadDetails(show+'/season/'+season);}
        }catch(error){showError(error);$('#show-streams').hidden=false;}
    }
    async function chooseEpisode(episode){selected=episode;document.querySelectorAll('.episode').forEach((button,index)=>button.classList.toggle('selected',episodes[index]===episode));await streams(episode.link,episode.title);}
    function navigation(){const index=episodes.indexOf(selected);for(const [selector,target] of [['#previous-episode',index-1],['#next-episode',index+1]]){const button=$(selector);button.hidden=index<0 || !episodes[target] || episodes[target].released===false;button.onclick=()=>chooseEpisode(episodes[target]);}}
    async function streams(href=null,label=title.title){playerCleanup();$('#player').replaceChildren();$('#playback-section').hidden=false;$('#playing-label').textContent=label;$('#stream-options').replaceChildren(node('p','Looking up playback sources…'));navigation();const query={source:source.id,...(href ? {href} : {})};
        try {const data=await api('Servers',query);$('#stream-options').replaceChildren();for(const stream of data.streams){const button=node('button',stream.name+' · '+stream.type,'button secondary');button.onclick=()=>play(stream,query,label,button);$('#stream-options').append(button);}$('#player-status').textContent='Availability and quality are unverified until playback succeeds.';}
        catch(error){rowError($('#stream-options'),error,()=>streams(href,label));}
    }
    async function play(stream,query,label,button){playerCleanup();$('#player').replaceChildren();$('#stream-options').querySelectorAll('button').forEach(el=>el.classList.toggle('selected',el===button));const history={source:source.id,href:query.href || source.href,label,...(selected?.season_number!=null ? {season_number:selected.season_number} : {}),...(selected?.episode_number!=null ? {episode_number:selected.episode_number} : {})};if(!history.href)delete history.href;
        if(stream.type==='embed'){const frame=node('iframe');frame.title=stream.name+' player';frame.src=stream.link;frame.allow='fullscreen; picture-in-picture';frame.referrerPolicy='no-referrer';frame.setAttribute('sandbox','allow-scripts allow-same-origin allow-forms allow-presentation');frame.allowFullscreen=true;$('#player').append(frame);$('#player-status').textContent='Third-party embed. Recorded as recently opened, not completed or resumable playback.';try{await api('History',{action:'opened'},history);}catch(error){toast(error.message);}return;}
        $('#player-status').textContent='Loading direct playback…';
        const video=node('video');video.controls=true;video.playsInline=true;video.preload='metadata';$('#player').append(video);let hls=null,lastSaved=0,ready=false;
        const playButton=node('button','Play video','button secondary');playButton.onclick=()=>video.play().catch(error=>{$('#player-status').textContent='Playback could not start: '+error.message;});$('#stream-options').append(playButton);
        if(stream.type==='hls' && !video.canPlayType('application/vnd.apple.mpegurl')){try{await loadHls();if(!window.Hls.isSupported())throw new Error('HLS playback is unavailable in this browser.');hls=new window.Hls();hls.loadSource(stream.link);hls.attachMedia(video);hls.on(window.Hls.Events.ERROR,(_event,data)=>{if(data.fatal)$('#player-status').textContent='Playback failed. Try another source.';});}catch(error){$('#player-status').textContent=error.message;return;}}else video.src=stream.link;
        video.addEventListener('loadedmetadata',async()=>{$('#player-status').textContent='Ready to play. Your position will be saved after playback starts.';if(params.get('resume')==='1'){try{const data=await api('History');const previous=data.history.find(item=>item.source_id===history.source && item.href===history.href);if(previous && !Number(previous.completed))video.currentTime=Math.min(Number(previous.position_seconds),Math.max(0,video.duration-2));}catch(error){toast(error.message);}}});
        const save=async()=>{if(!ready || !Number.isFinite(video.duration))return;lastSaved=Date.now();try{await api('History',{action:'progress'},{...history,position:Math.floor(video.currentTime)+1,duration:Math.floor(video.duration)+1});}catch(error){$('#player-status').textContent='Playback is running, but saving progress failed: '+error.message;}};
        video.addEventListener('playing',()=>{ready=true;$('#player-status').textContent='Direct playback. Your position is saved automatically.';save();});video.addEventListener('timeupdate',()=>{if(Date.now()-lastSaved>15000)save();});video.addEventListener('pause',save);video.addEventListener('ended',save);video.addEventListener('error',()=>{$('#player-status').textContent='This source could not play. Try another source (CORS, expiry, or format may be the cause).';});
        playerCleanup=()=>{save();video.pause();if(hls)hls.destroy();video.removeAttribute('src');video.load();};
    }
    $('#detail-source').onchange=()=>loadDetails();$('#show-streams').onclick=()=>streams();await loadDetails();
}
let hlsPromise;
function loadHls(){if(window.Hls)return Promise.resolve();if(!hlsPromise)hlsPromise=new Promise((resolve,reject)=>{const script=node('script');script.src='assets/hls.min.js';script.onload=resolve;script.onerror=()=>reject(new Error('The HLS library is unavailable.'));document.head.append(script);});return hlsPromise;}
async function authForms(){document.querySelectorAll('[data-auth]').forEach(form=>{form.onsubmit=async event=>{event.preventDefault();const button=$('button[type="submit"]',form),message=$('.form-message',form);button.disabled=true;message.textContent='Please wait…';try{const data=await api('User',{action:form.dataset.auth},Object.fromEntries(new FormData(form)));message.textContent=data.msg || 'Success';message.className='form-message success';if(['login','register'].includes(form.dataset.auth))location.href='index.php';}catch(error){message.textContent=error.message;message.className='form-message error';}finally{button.disabled=false;}};});}
async function settings(){for(const [selector,action] of [['#profile-form','profile'],['#password-form','change']]){const form=$(selector);form.onsubmit=async event=>{event.preventDefault();const message=$('.form-message',form);try{const data=await api('User',{action},Object.fromEntries(new FormData(form)));message.textContent=data.msg;if(action==='change')location.href='index.php?v=Login';}catch(error){message.textContent=error.message;}};}
    $('#logout').onclick=async()=>{try{await api('User',{action:'logout'},{});location.href='index.php?v=Login';}catch(error){toast(error.message);}};
    $('#delete-account').onclick=async()=>{if(!confirm('Delete your V2 account, favorites, and history? This cannot be undone.'))return;try{await api('User',{action:'delete'},{});location.href='index.php?v=Register';}catch(error){toast(error.message);}};
}
async function health(){const data=await api('Health');for(const provider of data.providers){const panel=node('article',null,'panel');panel.append(node('h2',provider.name),node('span',provider.enabled ? provider.state : 'disabled','badge'),node('p',provider.reason || provider.error_code || 'No errors reported.','muted'),node('p',`Last success: ${provider.last_success || 'not yet checked'} · ${provider.latency_ms || 0} ms`));$('#health-results').append(panel);}$('#merge-form').onsubmit=async event=>{event.preventDefault();if(!confirm('Confirm these are the same title and episode numbering before merging?'))return;try{const result=await api('Match',{},Object.fromEntries(new FormData(event.target)));$('.form-message',event.target).textContent=result.msg;}catch(error){$('.form-message',event.target).textContent=error.message;}};}
document.addEventListener('DOMContentLoaded',async()=>{
    const view=document.body.dataset.view;
    await authForms();
    if(!['Login','Register','Forget','Reset','About','Policy','Terms','NotFound'].includes(view)){favoritesReady=api('Favorites').then(data=>{for(const item of data.favorites)favorites.add(item.id);updateFavorites();}).catch(error=>toast(error.message));await favoritesReady;}
    try{if(view==='Home')await home();else if(['Search','Category'].includes(view))await search();else if(['More','Servers'].includes(view))await details();else if(['Favorites','History'].includes(view))await lists(view);else if(['Settings','Profile'].includes(view))await settings();else if(view==='Health')await health();}catch(error){toast(error.message);}
    if('serviceWorker' in navigator){try{await navigator.serviceWorker.register('sw.js');}catch(error){console.warn('Offline shell could not be registered:',error.message);}}
});
