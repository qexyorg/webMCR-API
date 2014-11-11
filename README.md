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

/*************************** Install API ***************************/
