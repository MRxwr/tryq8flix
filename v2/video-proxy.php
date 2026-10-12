<?php
require __DIR__.'/app/bootstrap.php';
securityHeaders();
try {requireUser();throw new AppError('proxy_disabled','Open proxying is disabled. Use authenticated title playback; a provider-specific range proxy requires a tested contract.',410);}
catch(AppError $error){jsonResponse(null,$error->status,$error);}
