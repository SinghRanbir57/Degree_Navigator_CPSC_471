<?php

// Ends the session and returns to the login page,

session_start();
session_unset();
session_destroy();

//here we invalidate the session cookie, so that other tabs also get logged out once one tab gets logged out.
if (ini_get("session.use_cookies") ) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        
        $params['secure'], $params['httponly']
    );
}

//  important ! : this force-clears PHP session cookie
setcookie("PHPSESSID", "", time() - 3600, "/"); 

header('Location: ../../Frontend/html/login.html');
exit;
?>
