<?php
function getHtmlEmailTemplate($subject, $bodyContent) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $subject . '</title>
        <style>
            body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; background-color: #141414; color: #ffffff; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background-color: #181818; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.5); }
            .header { background-color: #000000; padding: 25px; text-align: center; border-bottom: 3px solid #e50914; }
            .header h1 { color: #e50914; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 1px; }
            .content { padding: 40px 30px; line-height: 1.6; color: #e5e5e5; font-size: 16px; }
            .footer { background-color: #000000; padding: 20px; text-align: center; font-size: 12px; color: #737373; border-top: 1px solid #333; }
            .btn { display: inline-block; background-color: #e50914; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin-top: 25px; font-weight: bold; }
            a { color: #e50914; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>TryQ8Flix</h1>
            </div>
            <div class="content">
                <h2 style="color: #ffffff; margin-top: 0; margin-bottom: 20px; font-size: 22px;">' . $subject . '</h2>
                ' . $bodyContent . '
            </div>
            <div class="footer">
                &copy; ' . date("Y") . ' TryQ8Flix. All rights reserved.<br>
                This is an automated message, please do not reply.
            </div>
        </div>
    </body>
    </html>';
}

function sendMail($data){
	$to = $data["to"];
	$subject = $data["subject"];
	$rawBody = $data["body"];
	$from = "noreply@tryq8flix.com";

    $body = getHtmlEmailTemplate($subject, $rawBody);

    // Headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: TryQ8Flix <" . $from . ">" . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Use PHP's built-in mail function
    mail($to, $subject, $body, $headers);
}
?>