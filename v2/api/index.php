<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
securityHeaders();
try {
    $endpoint=strtolower(textInput('endpoint',40)); $action=strtolower(textInput('action',40,'list'));
    if($endpoint==='user') jsonResponse(authAction($action));
    $user=requireUser();
    rateLimit('api-user-'.$user['id'],180,60);
    if($endpoint==='main') {
        $servers=array_values(array_map(static fn($p)=>array_intersect_key($p,array_flip(['id','name','enabled','search','kind','reason'])),providers()));
        jsonResponse(['servers'=>$servers,'banners'=>[],'user'=>$user,'csrf'=>$user['csrf']]);
    }
    if($endpoint==='home') {
        $id=positiveInt('server',14,16);$page=positiveInt('page');$search=textInput('search',200);
        $result=cachedProvider($id,'browse',['page'=>$page,'search'=>$search]);
        jsonResponse($result['data']+['cache'=>$result['cache']]);
    }
    if($endpoint==='catalog') {
        $filters=['media_type'=>textInput('media_type',16),'year'=>textInput('year',4),'language'=>textInput('language',16),'genre'=>textInput('genre',80),'provider'=>textInput('provider',3)];
        jsonResponse(catalogSearch(textInput('search',200),positiveInt('page'),$filters));
    }
    if($endpoint==='title') jsonResponse(titleById(textInput('id',64)));
    if(in_array($endpoint,['more','servers'],true)) {
        $source=sourceById(textInput('source',64)); $href=textInput('href',4096,$source['href']);
        assertSourceHref($source,$href);
        $result=cachedProvider((int)$source['provider_id'],$endpoint==='more' ? 'details' : 'streams',['href'=>$href,'source_id'=>$source['id']]);
        jsonResponse($result['data']+['cache'=>$result['cache']]);
    }
    if($endpoint==='favorites') {
        if($action==='list') {
            $rows=query('SELECT t.* FROM favourites f JOIN titles t ON t.id=f.title_id WHERE f.user_id=? ORDER BY f.created_at DESC LIMIT 200',[$user['id']])->fetchAll();
            jsonResponse(['favorites'=>array_map('hydrateTitle',$rows)]);
        }
        mutation($user); $id=textInput('title_id',64); titleById($id);
        if($action==='add') query('INSERT IGNORE INTO favourites(user_id,title_id) VALUES(?,?)',[$user['id'],$id]);
        elseif($action==='remove') query('DELETE FROM favourites WHERE user_id=? AND title_id=?',[$user['id'],$id]);
        else throw new AppError('unknown_action','Unknown favorite action.',404);
        jsonResponse(['msg'=>$action==='add' ? 'Added to your list.' : 'Removed from your list.']);
    }
    if($endpoint==='history') {
        if($action==='list') {
            $rows=query('SELECT p.*,t.title,t.poster,t.media_type FROM progress p JOIN titles t ON t.id=p.title_id WHERE p.user_id=? ORDER BY p.updated_at DESC LIMIT 100',[$user['id']])->fetchAll();
            jsonResponse(['history'=>$rows]);
        }
        mutation($user);
        if($action==='clear') {query('DELETE FROM progress WHERE user_id=?',[$user['id']]);jsonResponse(['msg'=>'History cleared.']);}
        if(!in_array($action,['opened','progress'],true)) throw new AppError('unknown_action','Unknown history action.',404);
        $source=sourceById(textInput('source',64));$href=textInput('href',4096,$source['href']);assertSourceHref($source,$href);
        $season=input('season_number');$episode=input('episode_number');
        foreach([$season,$episode] as $number) if($number!==null && $number!=='' && (filter_var($number,FILTER_VALIDATE_INT)===false || (int)$number<0 || (int)$number>10000)) throw new AppError('invalid_episode','Invalid episode number.');
        $season=$season!==null && $season!=='' ? (int)$season : null;$episode=$episode!==null && $episode!=='' ? (int)$episode : null;
        if(in_array((int)$source['provider_id'],[13,14],true) && str_contains($href,'/')) {[$id,$season,$episode]=array_map('intval',explode('/',$href));}
        $item=itemIdentity($source,$href,$season,$episode);$label=textInput('label',255,titleById($source['title_id'])['title']);
        $position=$action==='progress' ? positiveInt('position',1,604800)-1 : 0;
        $duration=$action==='progress' ? positiveInt('duration',1,604800)-1 : 0;
        if($duration>0 && $position>$duration+2) throw new AppError('invalid_progress','Position exceeds duration.');
        $completed=$duration>0 && $position/$duration>=0.95 ? 1 : 0;
        $tracking=$action==='progress' ? 'direct' : 'opened';
        query('INSERT INTO progress(user_id,item_id,title_id,source_id,href,label,season_number,episode_number,position_seconds,duration_seconds,completed,tracking) VALUES(?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE source_id=VALUES(source_id),href=VALUES(href),label=VALUES(label),position_seconds=IF(VALUES(tracking)=\'direct\',VALUES(position_seconds),position_seconds),duration_seconds=IF(VALUES(tracking)=\'direct\',VALUES(duration_seconds),duration_seconds),completed=IF(VALUES(tracking)=\'direct\',VALUES(completed),completed),tracking=IF(VALUES(tracking)=\'direct\',VALUES(tracking),tracking),updated_at=UTC_TIMESTAMP()',[$user['id'],$item,$source['title_id'],$source['id'],$href,$label,$season,$episode,$position,$duration,$completed,$tracking]);
        jsonResponse(['msg'=>'Playback history saved.','item_id'=>$item]);
    }
    if($endpoint==='match') {
        $admin=requireUser(true); mutation($admin);
        $from=titleById(textInput('from',64));$to=titleById(textInput('to',64));
        if($from['id']===$to['id']) throw new AppError('same_title','Choose two different titles.');
        if($from['media_type']!=='unknown' && $from['media_type']!==$to['media_type']) throw new AppError('type_mismatch','Movie and TV identities cannot be merged.');
        if($from['year'] && $to['year'] && $from['year']!==$to['year']) throw new AppError('year_mismatch','Release years do not match.');
        db()->beginTransaction();
        try {
            query('INSERT IGNORE INTO favourites(user_id,title_id,created_at) SELECT user_id,?,created_at FROM favourites WHERE title_id=?',[$to['id'],$from['id']]);
            query('DELETE FROM favourites WHERE title_id=?',[$from['id']]);
            foreach(query('SELECT p.*,s.href AS source_href FROM progress p JOIN sources s ON s.id=p.source_id WHERE p.title_id=?',[$from['id']])->fetchAll() as $row) {
                $identity=itemIdentity(['title_id'=>$to['id'],'href'=>$row['source_href']],$row['href'],$row['season_number']!==null ? (int)$row['season_number'] : null,$row['episode_number']!==null ? (int)$row['episode_number'] : null);
                query('INSERT INTO progress(user_id,item_id,title_id,source_id,href,label,season_number,episode_number,position_seconds,duration_seconds,completed,tracking,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE position_seconds=GREATEST(position_seconds,VALUES(position_seconds)),duration_seconds=GREATEST(duration_seconds,VALUES(duration_seconds)),completed=GREATEST(completed,VALUES(completed)),updated_at=GREATEST(updated_at,VALUES(updated_at))',[$row['user_id'],$identity,$to['id'],$row['source_id'],$row['href'],$row['label'],$row['season_number'],$row['episode_number'],$row['position_seconds'],$row['duration_seconds'],$row['completed'],$row['tracking'],$row['updated_at']]);
            }
            query('DELETE FROM progress WHERE title_id=?',[$from['id']]);
            query('UPDATE sources SET title_id=? WHERE title_id=?',[$to['id'],$from['id']]);
            query('DELETE FROM titles WHERE id=?',[$from['id']]);
            query('DELETE FROM cache'); db()->commit();
        } catch(Throwable $error) {db()->rollBack();throw $error;}
        jsonResponse(['msg'=>'Source matched to the canonical title.']);
    }
    if($endpoint==='health') {
        requireUser(true);
        jsonResponse(['providers'=>array_values(array_map(static function($provider){$health=query('SELECT * FROM provider_health WHERE provider_id=?',[$provider['id']])->fetch();return array_intersect_key($provider,array_flip(['id','name','enabled','reason']))+($health ?: ['state'=>'not_checked']);},providers()))]);
    }
    if($endpoint==='settings') jsonResponse(['about'=>'Q8Flix V2 is a source-aware movie and series catalog. Playback availability is independent of metadata.','policy'=>'Your isolated V2 account stores favorites and viewing history. You can clear history or delete your account in Settings.','terms'=>'Only access sources you are authorized to use. Provider terms and content rights apply.']);
    if($endpoint==='version') jsonResponse(['version'=>'2.0.0','web'=>basePath().'/']);
    if(in_array($endpoint,['downloader','live','liveold','videoplayer','qacategories','qaquestions','qarooms','submitroom','games/home','firebase','news'],true)) throw new AppError('integration_pending','This legacy integration is not enabled in V2 until it has a tested, authorized provider contract.',503);
    throw new AppError('not_found','API endpoint not found.',404);
} catch(AppError $error) {jsonResponse(null,$error->status,$error);}
catch(Throwable $error) {logEvent('api_failure',['class'=>get_class($error)]);jsonResponse(null,503,new AppError('service_unavailable','V2 is unavailable. Check its database setup and private logs.',503));}
