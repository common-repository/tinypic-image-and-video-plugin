<?php
/*
 Info for WordPress:
 ==============================================================================
 Plugin Name: Tinypic Uploader 
 Plugin URI: http://www.tinypic.com/
 Description: Allows you to upload images to Tinypic.com directly from your posting screen.
 Version: 0.2
 Author: Anthony Sangiuliano
 Author URI: http://www.tinypic.com/


License and Copyright:
 ==============================================================================
 Copyright 2008  

 This software is provided "as is", without any guarantee of warranty of any kind.

*/

/* Begin configurable options */

define('TP_PLUGIN_IMAGE_ON',true);
define('TP_PLUGIN_VIDEO_ON',true);
/* see language list on bottom of tinypic.com for full list of available languages */
define('TP_PLUGIN_LANGUAGE','en');
define('TP_PLUGIN_POST_IT_TEXT','Use this '.$_GET['type'].' in WordPress');

/* End configurable options */


class Tinypic {

	function Tinypic() {
		add_filter("media_upload_tabs",array($this,"HtmlAddTab"));
		add_action('media_upload_tinypic',array($this,"HtmlGetTab"));
	}
	function Activate() {
		if(!isset($GLOBALS["is_instance"])) {			
			
			$GLOBALS["is_instance"]=new Tinypic();
			
		}
	}	

	function HtmlAddTab($tabs) {
		$tabs['tinypic'] = __("Tinypic","tinypic");
		return $tabs;
	}
	
	function HtmlGetTab() {
		return wp_iframe( array($this,'HtmlPrintTab'),'image');
	}
	function HtmlPrintTab($type = 'image') {
		wp_admin_css('css/media');
		media_upload_header();

		$type = $_GET['type'];
		$post_id = $_GET['post_id'];
		?>

<style type="text/css">

	label { color: #333; font-size: 12px; font-weight: bold; line-height: 18px; padding-top: 1px; cursor: pointer; }
	select, input.input-radio, input.input-checkbox, input[type=file], input[type=submit] { cursor: pointer; }
	input.input-text { padding: 1px 3px; font-size: 12px; }
	textarea { padding: 2px 3px; line-height: 18px; font-size: 12px; }
	input.input-radio { margin: 2px 5px 0 0; }
	select { padding: 0; background: #fff; border-color: #ccc; }
	input, textarea, select { font: 12px "Arial", Helvetica, Geneva, sans-serif; }

	/*	--------------------------------------------------
	=Buttons
	-------------------------------------------------- */
	
	button, .button, .button:visited { overflow: visible; width: auto; margin: 0; padding: 5px 12px; color: #fff; font-size: 12px; font-weight: bold; line-height: 1; text-decoration: none; text-shadow: 0 0 0 #000; text-transform: uppercase; border: 2px solid; border-radius: 6px; -webkit-border-radius: 6px; cursor: pointer; background-position: 0 0; background-repeat: repeat-x; color: #fff !important; }
	button:hover, .button:hover { color: #fff; text-decoration: none; }
	
	/* Really new buttons, but really cool, too */
	.button, button { -webkit-box-shadow: 0 1px 2px #ccc; }
	.button.large, .button.large:visited { padding: 10px 15px; font-size: 14px; }
	.button.medium, .button.medium:visited { padding: 8px 15px; }
	.button.small, .button.small:visited { padding: 5px 15px; }
	.button:hover, button:hover { color: #fff; text-decoration: none; background-position: -36px; }
	
	button.green, .button.green { background-color: #9cc147; background-position: 0 -45px; border: 2px solid #7b9f29; text-shadow: 0 -1px 1px #7b9f29; }
	button.orange, .button.orange { background-color: #de703b; background-position: 0 -90px; border: 2px solid #bb5d30; text-shadow: 0 -1px 1px #bb5d30; }
	
	button.green:hover, .button.green:hover { background-position: 0 -216px; }
	button.orange:hover, .button.orange:hover { background-position: 0 -261px; }


	/*	--------------------------------------------------
	Generic Forms (Extracted from Acccount Settings)
	--------------------------------------------------	*/
	
	.form { margin: 0 0 18px; }
	.form textarea { margin: 0 0 18px; }
	.form label { display: block; }
	
	.form .section { margin: 0 0 18px; }
	.form .section div { margin: 0; padding: 0 0 9px; border: 0; }
	.form .section div.last { border: 0; padding-bottom: 0; }

	.form input.input-text, .form textarea, .form select { display: block; width: 300px; margin: 0 0 3px; }
	
	.form div p { font-size: 12px; color: #777; margin: 0; padding: 0; }
	.form div p a, .form div p a:visited { font-weight: normal; }


	/*	--------------------------------------------------
	=Type
	-------------------------------------------------- */
	
	h1, h2, h3, h4, h5, h6 { text-align: left; font-weight: bold; text-shadow: 0 1px 1px #fff; }
	h1 { color: #df713b; font-size: 24px; line-height: 1; margin: 6px 0; }
	h2 { color: #df713b; font-size: 18px; line-height: 1; text-shadow: 1px 1px 1px #fff; }
	h3 { color: #7b9f29; font-size: 14px; line-height: 18px; padding: 0 0 6px; }
	h4 { font-size: 1.2em; line-height: 1.5; }
	h5 { font-size: 1.1em; line-height: 1.636363; }
	
	p { font-size: 12px; line-height: 1.5; color: #333; }
	li { font-size: 12px; line-height: 18px; }
	
	small { font-size: 92%; }
	span { line-height: inherit; }
	strong { font-weight: bold; font-style: inherit; }
	em { font-weight: inherit; font-style: italic; }
	
	hr { border-top: 1px solid #ddd; border-bottom: 1px solid #fff; margin: 17px 0; height: 0; }


	/*	--------------------------------------------------
	Links
	-------------------------------------------------- */
	
	a, a:visited { color: #369; font-weight: bold; text-decoration: underline; cursor: pointer; line-height: inherit; outline: 0; }
	a:hover { color: #69c; }


	/*	--------------------------------------------------
	WP Plugin
	-------------------------------------------------- */
	
	#TB_iframeContent { width: 670px; }
	
	#tp_upload_wrapper { font: 62.5%/1 "Arial", Helvetica, Geneva, sans-serif; width: 630px; padding: 20px; text-shadow: 0 0 0 #000; }
	#tp_upload_wrapper * { font-family: "Arial", Helvetica, Geneva, sans-serif; }
		
	#tp_wp_plugin { float: left; width: 259px; overflow: hidden; }

	#tp_wp_image_form, #tp_wp_video_form { background: #fff; width: 370px; height: 509px; margin: 0 0 0 259px; border-bottom: 1px solid #ddd; }
	/* #tp_wp_video_form { width: 480px; } */
	
	#tp_wp_header { background: url(http://static.tinypic.com/i/body-header-bg.gif) repeat-x 0 0; width: 100%; height: 36px; }
		
	#tp_wp_image_insert_form { background: #f5f5f5; padding: 18px 29px 0 29px; width: 310px; height: 455px; border-left: 1px solid #fff; border-right: 1px solid #ddd; }	
	#tp_wp_video_insert_form { background: #f5f5f5; padding: 3px 19px 0; width: auto; height: 470px; border-left: 1px solid #fff; border-right: 1px solid #ddd; }	

	.registration-benefits {  }
	.registration-benefits ol, .registration-benefits ul { margin: 0 0 18px; padding: 0; }
	.registration-benefits li { margin: 0; padding: 0 0 9px; color: #333; }
	.registration-benefits ol { list-style: decimal outside; margin: 0 3em 18px; }
	.registration-benefits p { margin: 0 0 18px; }
	
	.samplevideo { margin: 0 0 18px; }

	br.clear { clear: both; }
</style>

<div id="tp_upload_wrapper">
	<div id="tp_wp_plugin">
	<?php $this->showPluginScript($type,$post_id);?>
	</div>
	
	<div id="tp_wp_<?php echo $type;?>_form">
		<div id="tp_wp_header"></div>
		
		<div id="tp_wp_<?php echo $type;?>_insert_form">
			<div id="tp_wp_messaging">
				<div class="registration-benefits">
					<h2>Save Bandwidth with TinyPic!</h2>
					<p>We make adding images to your blog posts easy.<br /> <strong>Here's how:</strong></p>
					
					<ol class="registration-benefits">
						<li>Free uploading and hosting on <a href="http://www.tinypic.com" title="Visit TinyPic.com">TinyPic</a>!</li>
						<li>Unlimited storage and bandwidth for your stuff!</li>
						<li>No sign up required!</li>
					</ol>
					
					<hr />

					<p><strong>Join TinyPic for free!</strong> It only takes 30 seconds!</p>
					
					<a href="" class="small orange button">Join Now &raquo;</a>
					
					<br /><br /><br />
				</div>
			</div>
		
			<div id="tp_wp_show_form" style="display: none;">
			<?php
					if($type == 'image') {
						$this->showImageForm();
					} elseif ($type == 'video') {
						$this->showVideoForm();
					}
			?>
			</div>
		</div>
	</div>
	
	<br class="clear" />
</div>


<?php 
		// This is the LAST thing to do.
		$this->showCopyScript();
	}
	function showImageForm() {
?>
	<form class="form" id="tp_image_form" name="tp_image_form">
		<input id="src" type="hidden" value="" name="src" />

		<div class="section">
			<label for="caption">Caption:</label>
			<input class="input-text" id="caption" type="text" value="" name="caption">
			<p class="note">A brief description that will appear below your image.</p>
		</div>

		<div class="section">
			<label>Alignment</label>
			<select name="align" id="align">
				<option value="alignnone">None (Default)</option>
				<option value="alignleft">Left</option>
				<option value="aligncenter">Center</option>
				<option value="alignright">Right</option>
			</select>
			<p class="note">Do you want your image left, right, or center aligned?</p>
		</div>

		<div class="section">
			<label for="imagesize">Image Size:</label>
			<select name="imagesize" id="imagesize">
				<option value="fullsize">Fullsize (Default)</option>
				<option value="thumb">Thumbnail</option>
				<option value="linkedthumb">Thumbnail Linked to Fullsize</option>
			</select>
		</div>

		<div class="section">
			<label for="url">Link URL:</label>
			<input class="input-text" id="url" type="text" value="" name="url">
			<p class="note">Use this to link your image to another page.</p>
		</div>

		<div class="section">
			<label for="title">Image Alt Text</label>
			<input class="input-text" id="title" type="text" value="" name="title">
			<p class="note">Get better search engine results by providing alt text.</p>
		</div>

		<button type="submit" class="medium orange button" onclick="tinypic_img_insert()" id="post_button">Add Image to Post &raquo;</button>

	</form>
<?
	}

	function showVideoForm() {
?>
	<form id="tp_video_form" name="tp_video_form">
		<input id="video_src" type="hidden" value="" name="video_src" />

		<div id="samplevideo"></div>
		<button type="submit" class="medium orange button" onclick="tinypic_vid_insert()" id="post_button">Add Video to Post &raquo;</button>

	</form>
<?
	}

// Javascript Functions

	
	function showCopyScript() {
?>
	<script type="text/javascript">
<?
if ($_GET['type'] == 'image') {
?>
		var tp_imgSrc = document.getElementById('src');
		var tp_imgTitle = document.getElementById('title');
		var tp_imgCaption = document.getElementById('caption');
		var tp_imgAlign = document.getElementById('align');
		var tp_imgSize = document.getElementById('imagesize');
		var tp_imgUrl = document.getElementById('url');
		var tp_fullImage = new Image();
		var tp_thumbImage = new Image();
		var divWidth = 0;
		var divHeight = 0;
<?
} else {
?>
		var tp_vidSrc = document.getElementById('video_src');
		var tp_sampleVid = document.getElementById('samplevideo');
		var tp_tb_win = parent.document.getElementById('TB_window');
		var tp_tb_iframe = parent.document.getElementById('TB_iframeContent');
		tp_tb_win.style.width = '670px';
		tp_tb_iframe.style.width = '670px';
		var oCode = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="440" height="420" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">';
		var oCodeEnd = '</object>';
<?
}
?>
		var tp_postBtn = document.getElementById('post_button');
		var tp_wpForm = document.getElementById('tp_wp_show_form');
		var tp_wpMessaging = document.getElementById('tp_wp_messaging');

		function postIt(code) {
			if(typeof tp_imgSrc == 'object') {
				tp_fullImage.src = code;
				var tn = new Array();
				tn = code.split("tinypic.com/");
				var fn = tn[1];
				var fname = fn.split(".");
				thumbcode = tn[0] + 'tinypic.com/' + fname[0] + '_th.' + fname[1];
				tp_thumbImage.src = thumbcode;
				var s = 'src';
			} else {
				var s = 'video_src';
			}
			var srcBox = document.getElementById(s);
			srcBox.value = code;
			checkSource();
		}

		function checkSource() {
			// Images First:

			if(typeof(tp_imgSrc) == 'object') {
				tp_postBtn.disabled = false;
				tp_wpForm.style.display = 'block';
				tp_wpMessaging.style.display = 'none';
			}
			// Videos Next
			if(typeof(tp_vidSrc) == 'object') {
				tp_postBtn.disabled = false;
				tp_wpForm.style.display = 'block';
				tp_wpMessaging.style.display = 'none';
				tp_sampleVid.innerHTML = tp_vidSrc.value;
			}
		}

		function tinypic_vid_insert() {
			var postCode = tp_vidSrc.value;
			top.send_to_editor(postCode);
			top.tb_remove();
			return;
		}
		function tinypic_img_insert() {

			var tp_imgCode = tp_imgSrc.value;
			if(tp_imgCaption.value.length > 0) {
				var tp_imgAlt = tp_imgCaption.value;
			} else {
				var tp_imgAlt = tp_imgTitle.value;
			}
			var postCode = '';

			if(tp_imgSrc.length < 10) {
				return;
			}

			if(tp_imgAlt.length == 0) {
				tp_imgAlt = 'Image and video hosting by TinyPic';
			}


			/* Handle Caption */
			if(tp_imgCaption.value.length == 0) {
				tp_imgCaption.value = 'Image hosting by TinyPic';
			}

			/* Handle URL */
			if(tp_imgUrl.value.length < 2) {
				var imgLink = 'http://tinypic.com';
			} else {
				var imgLink = tp_imgUrl.value;
			}

			/* Get image source based on selection */
			if(tp_imgSize.value == 'fullsize') {
				postCode = createImgLinked(tp_imgCode,imgLink,tp_imgAlt,'n');
			} else if(tp_imgSize.value == 'thumb') {
				postCode = createImg(tp_imgCode,tp_imgAlt,'y');
			} else if(tp_imgSize.value == 'linkedthumb') {
				postCode = createImgLinked(tp_imgCode,imgLink,tp_imgAlt,'y');
			}

			postCode = '[caption id="" align="' + tp_imgAlign.value + '" width="' + divWidth + '" caption="' + tp_imgCaption.value + '"]' + postCode + '[/caption]';

			top.send_to_editor(postCode);
			top.tb_remove();
			return;
		}

		function createImgLinked(i,l,alt,tothumb) {
			return '<a href="' + l + '" target="_blank">' + createImg(i,alt,tothumb) + '</a>';
		}

		function createImg(code,alt,thumb) {
			if(thumb == 'y') {
				code = tp_thumbImage.src;
			}
			/* Handle Image Size and Alt Attribute */
			if(tp_imgSize.value == 'fullsize') {
				divWidth = tp_fullImage.width;
				divHeight = tp_fullImage.height;
			} else {
				divWidth = tp_thumbImage.width;
				divHeight = tp_thumbImage.height;
			}
			var imgTag = '<img id="tp_' + fname[0] + '_' + thumb + '" src="' + code + '" border="0" alt="' + alt + '" title="' + alt + '" width="' + divWidth + '" height="' + divHeight + '" />';
			return imgTag;
		}
		</script>
<?
	}

	function showPluginScript($type,$post_id) {
?>
<script type="text/javascript">
tinypic_layout = 'narrow';
		<?
		printf("tinypic_type = '%ss';\n", $type);
		printf("tinypic_language = '%s';\n", TP_PLUGIN_LANGUAGE);
		echo "tinypic_callback_url = '".sprintf("%s/wp-admin/media-upload.php?post_id=%s&type=%s&tab=tinypic&jsonly=true';\n",get_option('siteurl'),$post_id,$type);
		printf("tinypic_callback_text = '%s';\n", rawurlencode(TP_PLUGIN_POST_IT_TEXT));
		$linkType = ($type == 'image') ? 'url':'html';
		printf("tinypic_links = '%s';\n", $linkType);
		?>
tinypic_search = 'true';
tinypic_autoload = true;
</script>
<script src="http://plugin.tinypic.com/j/plugin.js" type="text/javascript"></script>
<?
	}

	function CallbackJS() {

?>
<script type="text/javascript">
   window.onload = function() {
        parent.parent.postIt(getParameter(document.location.href, "code"));
   }
   function getParameter(queryString, parameterName) {
        var parameterName = parameterName + "=";
        if(queryString.length > 0) {
            begin = queryString.indexOf(parameterName);
            if(begin != -1) {
                begin += parameterName.length;
                end = queryString.indexOf("&", begin);
                if(end == -1) {
                    end = queryString.length;
                }
                return unescape(queryString.substring(begin, end));
            }
            return "";
        }
   }
</script>
<?
exit;
	}

}


/*
 * Run the Plugin for Image and Video clicks
 */
if ($_GET['jsonly'] == 'true') {
	Tinypic::CallbackJS();
} elseif ($_GET['type'] == 'image' && TP_PLUGIN_IMAGE_ON) {
	Tinypic::Activate();
} elseif ($_GET['type'] == 'video' && TP_PLUGIN_VIDEO_ON) {
	Tinypic::Activate();
}
