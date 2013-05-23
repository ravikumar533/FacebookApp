<?php
require_once('AppInfo.php');
$con=mysqli_connect("myapp.com","root","","facebook");
// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');
require_once('sdk/src/facebook.php');
$config = array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
);
$facebook = new Facebook($config);
$user_id = $facebook->getUser();

if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
$images = mysqli_query($con,"SELECT * FROM Image");


?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="My first app" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="javascript/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="javascript/ajaxupload.3.6.js"></script>
    <script type="text/javascript" src="javascript/image.js"></script>
    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper">
      <div id="fb-root"></div>
      <script type="text/javascript">
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '<?php echo AppInfo::appID(); ?>', // App ID
            channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true // parse XFBML
          });

          // Listen to the auth.login which will be called when the user logs in
          // using the Login button
          FB.Event.subscribe('auth.login', function(response) {
            // We want to reload the page now so PHP can read the cookie that the
            // Javascript SDK sat. But we don't want to use
            // window.location.reload() because if this is in a canvas there was a
            // post made to this page and a reload will trigger a message to the
            // user asking if they want to send data again.
            window.location = window.location;
          });

          FB.Canvas.setAutoGrow();
        };

        // Load the SDK Asynchronously
        (function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
      </script>
      <script type="text/javascript">
        $(function(){
          var ImageBlock = $.Image({
            ImagesCount :<?php echo $images->num_rows; ?>,
            Element : ".friends li"
          });
          ImageBlock.loadImages();
        });
    </script>
      <header class="clearfix">
        <?php if (isset($basic)) { ?>
          <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>

          <div>
            <h1>Welcome, <strong><?php echo he(idx($basic, 'name')); ?></strong></h1>
          </div>
        <?php } else { ?>
          <div>
            <h1>Welcome</h1>
            <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
          </div>
        <?php } ?>
      </header>
        <!--Upload Image block-->
      <section id="UploadImage" class="clearfix">
        <?php if($user_id){?>
          <form id="imageForm" action="upload.php" method="post" enctype="multipart/form-data">
            <div id="imageUpload" >Upload Image</div>
          </form>
           <script type="text/javascript">
             $(function(){
               $(window).load(function(){
                 new AjaxUpload('imageUpload', {
                   action: 'upload.php',
                   name: 'image',
                   data:{ Name : "<?php echo idx($basic,"name");?>" , Fbid : "<?php echo he(idx($basic,"id")); ?>",Url:"<?php echo he(idx($basic,"link")); ?>",Email:"<?php echo he(idx($basic,"email"));?>"},
                   onChange: function(file, extension){
                     $('#imageUpload').css('padding','9px').html('<img src="images/loader.gif" />');
                   },
                   onComplete: function(file, response) {
                    
                   }
                 });

               });
             });
            </script>
          <?php }?>
     </section>
        <!-- Image List block-->
     <section id="ImageList" class="clearfix">
         <?php if($user_id){?>
         <div class="list">
            <ul class="friends">
              <?php
                foreach ($images as $img) {
                  // Extract the pieces of info we need from the requests above
                  $id = idx($img, 'id');
                  $Path = idx($img, 'Path');
                  $FbId = idx($img,'Fbid');
              ?>
              <li style="display:block;float:left;">
                <a href="https://www.facebook.com/<?php echo he($FbId); ?>" target="_top">
                  <img src="<?php echo he($Path) ?>" url="<?php echo he($Path) ?>" >
                </a>
              </li>
              <?php
                }
              ?>
            </ul>
          </div>
          <?php }?>
     </section>
    </div>
  </body>
</html>
