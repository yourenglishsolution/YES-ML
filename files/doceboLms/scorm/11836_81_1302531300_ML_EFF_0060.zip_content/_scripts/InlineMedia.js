/**
 * Makes the replacement of all inline medias in all texts that have the class ProcessInlineImages
 *
 * Encoding: UTF-8
 * @package Easyquizz Pro
 *
 * @author Epistema {@link http://www.epistema.com}
 * @copyright Copyright 2001 - 2007, Epistema
 *
 * @version $Rev$
 */

function GetFileType(strFileName)
{
	if (strFileName.match(/(.gif$)|(.jpg$)|(.jpeg$)|(.png$)/i))
		return 'image';

	if (strFileName.match(/(.mp3$)/i))
		return 'mp3';

	if (strFileName.match(/(.aif$)|(.asf$)|(.aifc$)|(.aiff$)|(.au$)|(.mid$)|(.midi$)|(.mp3$)|(.mpa$)|(.rmi$)|(.snd$)|(.wav$)|(.wma$)/i))
		return 'sound';

	if (strFileName.match(/(.avi$)|(.ivf$)|(.mp2$)|(.mpe$)|(.mpeg$)|(.mpg$)|(.mpv2$)/i))
		return 'video';

	if (strFileName.match(/(.asf$)|(.wmv$)/i))
		return 'windowsmedia';

	if (strFileName.match(/(.mov$)|(.qt$)/i))
		return 'quicktime';

	if (strFileName.match(/(.real$)|(.rm$)/i))
		return 'realvideo';

	if (strFileName.match(/(.ra$)|(.ram$)/i))
		return 'realaudio';

	if (strFileName.match(/(.swf$)/i))
		return 'flash';

	if (strFileName.match(/(.flv$)/i))
		return 'flashvideo';

	return 'file';
}

/**
 * Find all occurences of "inline media" and replace it with the appropriate tag
 * @param Element el the element to process (including any child element)
 */
function ProcessElement(el)
{

//		var AvailableMedia = new Array();
//		var AvailableReducedImages = new Array();

	if (el.childNodes.length == 0)
		return;

	var regularexpression = null;

	for(var childindex = el.childNodes.length - 1; childindex >= 0; childindex--)
	{
		var child = el.childNodes[childindex];

		if (child.nodeType == 1 && child.nodeName == 'SELECT')
		{
			// Do nothing - do not parse the inside of a select
		}
		else if (child.nodeType == 1) // element node
			ProcessElement(child);
		else if (child.nodeType == 3) // text node
		{
			var str = child.nodeValue.escapeHTML();

			// replace all AvailableMedia
			for(var i = 0; i < AvailableMedia.length; i++)
			{
				regularexpression = new RegExp("\\[\\[link:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
				str = str.replace(regularexpression, '<a target="_blank" href="_attachments/@@InlineImage@@">@@InlineImage@@</a>');
			}

			for(var i = 0; i < AvailableReducedImages.length; i++)
			{
				switch (GetFileType(AvailableReducedImages[i]))
				{
					case 'image':
						regularexpression = new RegExp("\\[\\[img:" + AvailableReducedImages[i] + "\\|([^x]*)x([^|\\]]*)(|[^|\\]]*)*\\]\\]", 'gi') ;
						str = str.replace(regularexpression, '<img src="_attachments/ReducedImages/@@InlineImage@@" width="$1" height="$2" alt="" border="0" align="absmiddle">');

						regularexpression = new RegExp("\\[\\[img:" + AvailableReducedImages[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression, '<img src="_attachments/ReducedImages/@@InlineImage@@" alt="" border="0" align="absmiddle">');

						str = str.replace(AvailableReducedImages[i], '<img src="_attachments/ReducedImages/@@InlineImage@@" alt="" border="0" align="absmiddle">');
						break;
				}

				str = str.replace(/@@InlineImage@@/g, AvailableReducedImages[i]);
			}

			for(var i = 0; i < AvailableMedia.length; i++)
			{
				switch (GetFileType(AvailableMedia[i]))
				{
					case 'image':
						regularexpression = new RegExp("\\[\\[img:" + AvailableMedia[i] + "\\|([^x]*)x([^|\\]]*)(|[^|\\]]*)*\\]\\]", 'gi') ;
						str = str.replace(regularexpression, '<img src="_attachments/@@InlineImage@@" width="$1" height="$2" alt="" border="0" align="absmiddle">');

						regularexpression = new RegExp("\\[\\[img:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression, '<img src="_attachments/@@InlineImage@@" alt="" border="0" align="absmiddle">');

						str = str.replace(AvailableMedia[i], '<img src="_attachments/@@InlineImage@@" alt="" border="0" align="absmiddle">');
						break;

					case 'sound':
						regularexpression = new RegExp("\\[\\[snd:" + AvailableMedia[i] + "\\]\\]", 'gi') ;

						str = str.replace(regularexpression,
								'<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112" width="300" height="40">'
								+'<param name="filename" value="_attachments/@@InlineImage@@" />'
								+'<param name="ShowControls" value="True" />'
								+'<param name="ShowPositionControls" value="False" />'
								+'<param name="ShowAudioControls" value="True" />'
								+'<param name="ShowTracker" value="True" />'
								+'<param name="ShowDisplay" value="False" />'
								+'<param name="displaysize" value="0" />'
								+'<param name="autosize" value="True" />'
								+'<embed type="application/x-mplayer2" src="_attachments/@@InlineImage@@"></embed>'
								+'</object>');
						break;

					case 'mp3':
						regularexpression = new RegExp("\\[\\[snd:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object type="application/x-shockwave-flash" data="_images/dewplayer/dewplayer.swf" wmode="opaque" width="400" height="70">'
								+'<param name="movie" value="_images/dewplayer/dewplayer.swf" />'
								+'<param name="FlashVars" value="mp3=_attachments/@@InlineImage@@" />'
								+'</object>');
						break;

					case 'video':
						regularexpression = new RegExp("\\[\\[vid:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">'
								+'<param name="filename" value="_attachments/@@InlineImage@@" />'
								+'<param name="ShowControls" value="True" />'
								+'<param name="ShowPositionControls" value="False" />'
								+'<param name="ShowAudioControls" value="True" />'
								+'<param name="ShowTracker" value="True" />'
								+'<param name="ShowDisplay" value="False" />'
								+'<param name="displaysize" value="0" />'
								+'<param name="autosize" value="True" />'
								+'<embed type="application/x-mplayer2" src="_attachments/@@InlineImage@@"></embed>'
								+'</object>');
						break;

					case 'windowsmedia':
						regularexpression = new RegExp("\\[\\[wmedia:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">'
								+'<param name="filename" value="_attachments/@@InlineImage@@" />'
								+'<param name="ShowControls" value="True" />'
								+'<param name="ShowPositionControls" value="False" />'
								+'<param name="ShowAudioControls" value="True" />'
								+'<param name="ShowTracker" value="True" />'
								+'<param name="ShowDisplay" value="False" />'
								+'<param name="displaysize" value="0" />'
								+'<param name="autosize" value="True" />'
								+'<embed type="application/x-mplayer2" src="_attachments/@@InlineImage@@"></embed>'
								+'</object>');
						break;

					case 'quicktime':
						regularexpression = new RegExp("\\[\\[qtime:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">'
								+'<param name="SRC" value="_attachments/@@InlineImage@@" />'
								+'<param name="AUTOPLAY" value="True" />'
								+'<param name="CONTROLLER" value="True" />'
								+'<embed controller="false" pluginspage="http://www.apple.com/quicktime/download/" src="_attachments/@@InlineImage@@" autoplay="True"></embed>'
								+'</object>');
						break;

					case 'realaudio':
						regularexpression = new RegExp("\\[\\[reala:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="300" height="150">'
								+'<param name="src" value="_attachments/@@InlineImage@@" />'
								+'<param name="autostart" value="True" />'
								+'<param name="controls" value="ImageWindow,ControlPanel" />'
								+'<embed loop="false" controls="ImageWindow,ControlPanel" type="audio/x-pn-realaudio-plugin" autostart="true" src="_attachments/@@InlineImage@@"></embed>'
								+'</object>');
						break;

					case 'realvideo':
						regularexpression = new RegExp("\\[\\[realv:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								'<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="300" height="150">'
								+'<param name="src" value="_attachments/@@InlineImage@@" />'
								+'<param name="autostart" value="True" />'
								+'<param name="controls" value="ImageWindow,ControlPanel" />'
								+'<embed loop="false" controls="ImageWindow,ControlPanel" type="audio/x-pn-realaudio-plugin" autostart="true" src="_attachments/@@InlineImage@@"></embed>'
								+'</object>');
						break;

					case 'flash':
						regularexpression = new RegExp("\\[\\[flash:" + AvailableMedia[i] + "\\|([0-9]+)x([0-9]+)\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								 '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$1" height="$2">'
								+'  <param name="movie" value="_attachments/@@InlineImage@@"></param>'
								+'  <param name="wmode" value="transparent"></param>'
								+'  <param name="quality" value="autohigh"></param>'
								+'  <param name="play" value="True"></param>'
								+'  <!--[if !IE]>-->'
								+'    <object width="$1" height="$2" type="application/x-shockwave-flash" data="_attachments/@@InlineImage@@">'
								+'  		<param name="movie" value="_attachments/@@InlineImage@@"></param>'
								+'  		<param name="wmode" value="transparent"></param>'
								+'  		<param name="quality" value="autohigh"></param>'
								+'  		<param name="play" value="True" width="$1" height="$2"></param>'
								+'  <!--<![endif]-->'
								+'    <embed src="_attachments/@@InlineImage@@" type="application/x-shockwave-flash" quality="autohigh" wmode="transparent" width="$1" height="$2"></embed>'
								+'  <!--[if !IE]>-->'
								+'    </object>'
								+'  <!--<![endif]-->'
								+'</object>'
								);

						regularexpression = new RegExp("\\[\\[flash:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								 '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="550" height="400">'
								+'  <param name="movie" value="_attachments/@@InlineImage@@"></param>'
								+'  <param name="wmode" value="transparent"></param>'
								+'  <param name="quality" value="autohigh"></param>'
								+'  <param name="play" value="True"></param>'
								+'  <!--[if !IE]>-->'
								+'    <object width="550" height="400" type="application/x-shockwave-flash" data="_attachments/@@InlineImage@@">'
								+'  		<param name="movie" value="_attachments/@@InlineImage@@"></param>'
								+'  		<param name="wmode" value="transparent"></param>'
								+'  		<param name="quality" value="autohigh"></param>'
								+'  		<param name="play" value="True"></param>'
								+'  <!--<![endif]-->'
								+'    <embed width="550" height="400" src="_attachments/@@InlineImage@@" type="application/x-shockwave-flash" quality="autohigh" wmode="transparent"></embed>'
								+'  <!--[if !IE]>-->'
								+'    </object>'
								+'  <!--<![endif]-->'
								+'</object>'
								);
						break;

					case 'flashvideo':

						regularexpression = new RegExp("\\[\\[flv:" + AvailableMedia[i] + "\\|([0-9]+)x([0-9]+)\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								 '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$1" height="$2">'
								+'  <param name="movie" value="_images/flowplayer/FlowPlayer.swf"></param>'
								+'  <param name="wmode" value="transparent"></param>'
								+'  <param name="quality" value="autohigh"></param>'
								+'  <param name="flashvars" value="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></param>'
								+'  <!--[if !IE]>-->'
								+'    <object width="$1" height="$2" type="application/x-shockwave-flash" data="_images/flowplayer/FlowPlayer.swf" >'
								+'  		<param name="movie" value="_images/flowplayer/FlowPlayer.swf"></param>'
								+'  		<param name="wmode" value="opaque"></param>'
								+'  		<param name="quality" value="autohigh"></param>'
								+'  		<param name="flashvars" value="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></param>'
								+'  <!--<![endif]-->'
								+'    <embed src="_images/flowplayer/FlowPlayer.swf" type="application/x-shockwave-flash" quality="autohigh" wmode="opaque" width="$1" height="$2" flashvars="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></embed>'
								+'  <!--[if !IE]>-->'
								+'    </object>'
								+'  <!--<![endif]-->'
								+'</object>'
								);

						regularexpression = new RegExp("\\[\\[flv:" + AvailableMedia[i] + "\\]\\]", 'gi') ;
						str = str.replace(regularexpression,
								 '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="262">'
								+'  <param name="movie" value="_images/flowplayer/FlowPlayer.swf"></param>'
								+'  <param name="wmode" value="opaque"></param>'
								+'  <param name="quality" value="autohigh"></param>'
								+'  <param name="flashvars" value="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></param>'
								+'  <!--[if !IE]>-->'
								+'    <object width="320" height="262" type="application/x-shockwave-flash" data="_images/flowplayer/FlowPlayer.swf" >'
								+'  		<param name="movie" value="_images/flowplayer/FlowPlayer.swf"></param>'
								+'  		<param name="wmode" value="opaque"></param>'
								+'  		<param name="quality" value="autohigh"></param>'
								+'  		<param name="flashvars" value="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></param>'
								+'  <!--<![endif]-->'
								+'    <embed src="_images/flowplayer/FlowPlayer.swf" type="application/x-shockwave-flash" quality="autohigh" wmode="opaque" width="320" height="262" flashvars="config={showMenu: false, videoFile: \'../../_attachments/@@InlineImage@@\',initialScale:\'fit\'}"></embed>'
								+'  <!--[if !IE]>-->'
								+'    </object>'
								+'  <!--<![endif]-->'
								+'</object>'
								);

						break;

					default:
						str = str.replace(AvailableMedia[i], '<a target="_blank" href="_attachments/@@InlineImage@@">@@InlineImage@@</a>');
						break;
				}

				str = str.replace(/@@InlineImage@@/g, AvailableMedia[i]);
			}

			regularexpression = new RegExp("\\[\\[toc\\]\\]", 'gi') ;
			if (str.search(regularexpression) != -1)
			{
				var strToc = '';
				if (QuestionnaireTOC)
					strToc = '<div class="EasyquizzToc">' + GetTocForNode(QuestionnaireTOC) + '</div>';

				str = str.replace(regularexpression, strToc);
			}

			if (child.ownerDocument.createRange)
			{
				var range = child.ownerDocument.createRange();
				range.selectNodeContents(child);
				if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
				{
					Range.prototype.createContextualFragment = function(html)
					{
						var frag = document.createDocumentFragment(), 
						div = document.createElement("div");
						frag.appendChild(div);
						div.outerHTML = html;
						return frag;
					};
				}
				child.parentNode.replaceChild(
					range.createContextualFragment(str), child);
			}
			else
			{
				child.nodeValue = '';
				var temp = document.createElement("SPAN");
				temp = child.parentNode.insertBefore(temp, child);
				str = str.replace(/^ /, '&nbsp;');
				str = str.replace(/ $/, '&nbsp;');
				temp.outerHTML = str;
			}
		}
	}
}

function GetTocForNode(TocArray)
{
	var str = '';

	for (var i=0; i < TocArray.length; i++)
	{
		if (TocArray[i].type != 'folder')
		{
			str += '<p><a href="javascript:DirectAccessTo(' + TocArray[i].index +')">' + TocArray[i].name + '</a></p>';
		}
		else
		{
			str += '<h1>' + TocArray[i].name + '</h1>';
			str += '<blockquote>' + GetTocForNode(TocArray[i].children) + '</blockquote>';
		}
	}

	return str;
}

Event.observe(window, 'load', function() {
	$$('.ProcessInlineImages').each(ProcessElement);
});
