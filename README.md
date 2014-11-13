Installation
- Upload all files from folder "upload" into your webmcr directory

Using

/*************************** Install API ***************************/

// Loading API

require_once(MCR_ROOT."instruments/modules/qexy/api/api.class.php");


// Set default url for module

$api->url = "?mode=YOUR_MODULE";
 
 
// Set default style path for module

$api->style = STYLE_URL;

// Set default module config

$api->cfg = YOUR_MODULE_CONFIG;


/* If you use webmcr 2.4+ */

Open file style/Default/index.html and add strings
<script src="<?php View::URL('modules/qexy/api/js/jquery.js') ?>"></script>
<script src="<?php View::URL('modules/qexy/api/js/bootstrap.min.js') ?>"></script>
Before
<?php echo $content_js ?>

And remove string
<script src="<?php View::URL('js/bootstrap-without-jquery.js') ?>"></script>


/*************************** Install API ***************************/
