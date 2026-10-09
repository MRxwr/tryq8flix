<?php
require __DIR__.'/app/bootstrap.php';
securityHeaders();
try {requireUser();throw new AppError('source_required','Open a title in V2 and choose one of its approved playback sources.',400);}
catch(AppError $error){http_response_code($error->status);header('Content-Type: text/plain; charset=utf-8');echo $error->getMessage();}
