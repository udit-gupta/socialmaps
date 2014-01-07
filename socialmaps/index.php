<html>
    <head>
        <link rel="icon" type="image/png" href="images/favicon.ico" />
        <title>Social Maps</title>
        <style type="text/css">
            * {margin:0;padding:0;}
            html, body {height: 100%;}
            #wrap {min-height: 100%;}
            #main {overflow:auto; padding-bottom: 250px;}
            #footer {position: relative; margin-top: -250px; height: 150px; clear:both;}
            body:before { content:""; height:100%; float:left; width:0; margin-top:-32767px;} /*Opera Fix*/
        </style>
        <!-- [if !IE 7]>
            <style type="text/css">
                #wrap {display:table;height:100%} /*IE Fix*/
            </style>
        <![endif]-->        
    </head>
    <body style="background-image:url('images/Clouds.jpg'); background-repeat:no-repeat; background-attachment:fixed; position:relative">
        <div id="fb-root"></div>
        <script type="text/javascript"  src="http://connect.facebook.net/en_US/all.js"></script>
        <script type="text/javascript">
         FB.init({
            appId:'<app_id>', cookie:true,
            status:true, xfbml:true
         });
        </script>
        <div id="wrap">
            <div id="main">
                <fb:like href="http://<example.domain.net>/socialmaps/" send="true" width="450" show_faces="true" colorscheme="dark"></fb:like>
            </div>
        </div>
        <div id="footer">
            <center>
                <div style="position: relative; margin-top: 10px;	height: 150px; clear:both;">
                    <img alt="Social World" style="vertical-align:middle;" width="100" height="100" src="images/Globe.gif" />
                    <p>
                    <span style='font-size: 50'>[</span>
                    <a href="https://www.facebook.com/dialog/oauth?client_id=<app_id>redirect_uri=http://<example.domain.net>/socialmaps/map.php&scope=offline_access">
    <img alt="Facebook Login"  src="images/fb-login-button.png" /></a>
                    <span style="font-size: 50">]</span>
                    </p>
                    <img alt="Social World" src="images/logo.png" />
                </div>
            </center>
        </div>
    </body>
</html>
