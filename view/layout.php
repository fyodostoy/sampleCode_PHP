<!DOCTYPE html>
<!--[if IEMobile 7 ]>    <html class="no-js iem7"> <![endif]-->
<!--[if (gt IEMobile 7)|!(IEMobile)]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <title><?php echo $title ?></title>
        <meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="cleartype" content="on">

        <link rel="stylesheet" href="css/normalize.css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
        <!-- ADDED BY ME -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>       
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> 
        <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script> 
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
        <script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script> 
        <link rel="stylesheet" href="css/themes/custom-theme.min.css" />
        <link rel="stylesheet" href="css/themes/jquery.mobile.icons.min.css" />  
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile.structure-1.4.4.min.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <header>    
            <article id="web-nav">
                <nav>	
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="add_log_controller.php">Create New Log</a></li>
                        <li><a href="logs_list_controller.php">My Logs</a></li>
                        <li><a href="others_logs_list_controller.php">Explore</a></li>
                    </ul>
                </nav>
            </article>    
            <article id="mob-nav" data-role="header" class="mob-nav-bar" data-theme="a">
                <nav data-role="navbar" >
                <ul>
                    <li><a id="home" href="index.php" data-icon="custom">Home</a></li>
                    <li><a id="create" href="add_log_controller.php" data-icon="custom">Create</a></li>
                    <li><a id="my-logs" href="logs_list_controller.php" data-icon="custom">My Logs</a></li>
                    <li><a id="explore" href="others_logs_list_controller.php" data-icon="custom">Explore</a></li>
                </ul>
                </nav>
            </article>
        </header>
        <div class="header-placeholder"></div>
        
        <!--    CONTENT     -->
        <main>
            <?php echo $content;?>
        </main>
        <!-- END OF CONTENT -->
        
        <footer> 
            <div class="container">
            <p>E SAILING LOG: BETA<p>
            <p>This is a BETA version. For testing purposes only. 
              Any log you create at this time might be deleted later.</p>
            </div>
        </footer>
        <script src="js/vendor/zepto.min.js"></script>
        <script src="js/helper.js"></script>
        <script src="js/main.js"></script> 
    </body>
</html>