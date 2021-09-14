function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function styleFlip(selObj,strClassName){
	selObj.className = strClassName;
}

function dspChange(){
  var j,pj,vj,objSpan,argsj=dspChange.arguments;
  for (j=0; j<(argsj.length-2); j+=3) if ((objSpan=MM_findObj(argsj[j]))!=null) { v=argsj[j+2];
    if (objSpan.style) { objSpan=objSpan.style; v=(v=='show')?'':(v=='hide')?'none':v; }
    objSpan.display=v; }
}

function DisplayDirect(index) {
	for (i=1; i<3; i++){
		if ( i==index ) {
			document.images['img'+index].src="/images/notice_tit_"+index+"_on.gif";
			document.getElementById('direct' + index).style.display='';
			document.getElementById('direct' + index + '_more').style.display='';
		}
		else {
			document.images['img'+i].src="/images/notice_tit_"+i+".gif";
			document.getElementById('direct' + i).style.display='none';
			document.getElementById('direct' + i + '_more').style.display='none';
		}
	}
}


function DisplayDirect2(index) {
	for (i=1; i<4; i++){
		if ( i==index ) {
			document.getElementById('direct' + index).style.display='';			
		}
		else {
			document.getElementById('direct' + i).style.display='none';
			eval("document.find_id_form.groups"+i+".checked=false"); 
		}
	}
}



// 숫자만 입력 체크
function onlyNum() {
	if(((event.keyCode<48)||(event.keyCode>57))&&(event.keyCode!=13)) {
		event.returnValue=false;
	}

	len = event.srcElement.value.length; 
	if(len > 1) {
		var ch = event.srcElement.value;
		var isnum = /^\d+$/.test(event.srcElement.value);
		
		if (isnum == false) {
			alert(ch + " 숫자만 입력 가능합니다.");
			event.srcElement.value = "";     
            event.returnValue=false;
        }       
    }
}

function goto_content_in_submain(contentUrl, leftUrl)
{
	contentUrl = escape(contentUrl);
	var url = "/jumpmain.htm?&left=" + leftUrl + "&content=" + contentUrl;
	try
	{
		top.iflg_body.iflg_main.location.href = url;
	} catch(e) { alert(e);}
}

function Isnum_c(ch) {   
    return ((ch > 0));
}

function setLeftPage() {
	var leftPath = getLeftPath();
	if( arguments.length >= 1 ) leftPath = arguments[0];
	var iflg_left = parent.iflg_left;
	try
	{
		if ( iflg_left.location.pathname != leftPath ) {
			iflg_left.location.replace("http://"+location.hostname+leftPath);
		}
	}
	catch(exception)
	{	// left가 빈페이지일 경우 발생 가능.
		iflg_left.location.replace("http://"+location.hostname+leftPath);
	}
}

function open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable) 
{ 
toolbar_str = toolbar ? 'yes' : 'no'; 
menubar_str = menubar ? 'yes' : 'no'; 
statusbar_str = statusbar ? 'yes' : 'no'; 
scrollbar_str = scrollbar ? 'yes' : 'no'; 
resizable_str = resizable ? 'yes' : 'no'; 
window.open(url, name, 'left='+left+',top='+top+',width='+width+',height='+height+',toolbar='+toolbar_str+',menubar='+menubar_str+',status='+statusbar_str+',scrollbars='+scrollbar_str+',resizable='+resizable_str); 
}




// 전체선택/해제 
function chkBox(bool) { 
	var obj = document.getElementsByName("chk[]"); 
	for (var i=0; i<obj.length; i++) {
		obj[i].checked = bool; 
	}
} 




function del($href) {
  if(confirm("정말 삭제하시겠습니까?")) {
    document.location.href = $href;
  }
}
function cancel($href) {
  if(confirm("정말 취소하시겠습니까?")) {
    document.location.href = $href;
  }
}


	function service_send(str) {
		document.frm.action = str + "form.php";
		document.frm.submit();
	}

	function mainflash(Str1, Str2, Str3){
		document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+Str2+'" height="'+Str3+'" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id=ShockwaveFlash1>'
		+'<param name="movie" value="'+Str1+'">'
		+'<param name="quality" value="high">'
		+'<param name="wmode" value="transparent">'
		+'<embed src="'+Str1+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'+Str2+'" height="'+Str3+'" type="application/x-shockwave-flash"></embed>'
		+'</object>');
	}



	function setHyphen(string)
	{
		var chk_str = eval("document.frm."+string);
		var str = checkDigit(chk_str.value);
		var retValue = "";
		var len = str.length;

		if (len == 8 || len == 9 || len == 10 || len == 11)
		{
			if (len == 8) {
				retValue = retValue + str.substring(0, 4) + "-" + str.substring(4, 8);
			} 
			else if (len == 9) {
				retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 5) + "-" + str.substring(5, 9);
			} 
			else if (len == 10) {
				if( str.substring(0, 2) == "02" ) {
					retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 6) + "-" + str.substring(6,10);
				}
				else {
					retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 6) + "-" + str.substring(6, 10);
				}
			} 
			else {
				retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 7) + "-" + str.substring(7, 11);
			}
		}
		else {
//			alert("다시 한번 확인해 주세요");
			retValue = str;
		}
		chk_str.value = retValue;
	}



	function setHyphen_frm(frm, string)
	{
		var chk_str = eval("document."+frm+"."+string);
		var str = checkDigit(chk_str.value);
		var retValue = "";
		var len = str.length;

		if (len == 8 || len == 9 || len == 10 || len == 11)
		{
			if (len == 8) {
				retValue = retValue + str.substring(0, 4) + "-" + str.substring(4, 8);
			} 
			else if (len == 9) {
				retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 5) + "-" + str.substring(5, 9);
			} 
			else if (len == 10) {
				if( str.substring(0, 2) == "02" ) {
					retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 6) + "-" + str.substring(6,10);
				}
				else {
					retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 6) + "-" + str.substring(6, 10);
				}
			} 
			else {
				retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 7) + "-" + str.substring(7, 11);
			}
		}
		else {
//			alert("다시 한번 확인해 주세요");
			retValue = str;
		}
		chk_str.value = retValue;
	}


	// 입력값중에 공백 및 기타 문자를 날려버리는 함수요.
	function checkDigit(num)
	{
		var Digit = "1234567890";
		var string = num;
		var len = string.length
		var retVal = "";

		for (i = 0; i < len; i++)
		{
			if (Digit.indexOf(string.substring(i, i+1)) >= 0)
			{
				retVal = retVal + string.substring(i, i+1);
			}
		}
		return retVal;
	}


	// 스크립트내 스크립트 넣기 - 반드시 jquery 아래에 있어야 함 
	function innerHTMLJS(obj,content) {
		// if(typeof(obj) != 'object' && typeof(content) != 'string') return;
		obj = $("#" + obj);

		// avoid IE innerHTML bug
		content = '<body>' + content.replace(/<\/?head>/gi, '')
					.replace(/<\/?html>/gi, '')
					.replace(/<body/gi, '<div')
					.replace(/<\/body/gi, '</div') + '</body>';

		obj.append(content);

		var scripts = obj.attr('script');

		if(scripts == false) return true; // no node script == no problem !

		for(var i=0; i<scripts.length; i++) {
			var scriptclone = document.createElement('script');
			if(scripts[i].attributes.length > 0) { /* boucle de copie des attributs du script dans le nouveau node */
				for(var j in scripts[i].attributes) {
					if(typeof(scripts[i].attributes[j]) != 'undefined'
						&& typeof(scripts[i].attributes[j].nodeName) != 'undefined' /* IE needs it */
						&& scripts[i].attributes[j].nodeValue != null
						&& scripts[i].attributes[j].nodeValue != '' /* IE needs it ou il copie des nodes vides */)
					{
						scriptclone.setAttribute(scripts[i].attributes[j].nodeName, scripts[i].attributes[j].nodeValue);
					}
				}
			}
			scriptclone.text = scripts[i].text; // on copie le corp du script
			/*
				la j'ai pas compris, si je ne return pas sous opera ici : le javascript s'execute 2 fois -
				mais la : le script s'execute mais n'est pas a ce moment la place entre les balises scripts !
				et si je return juste apres le innerHTML, le script n'est pas execute... ---o(<
			*/

			if (navigator.userAgent.indexOf("Opera")>0) { return; }
			/* on force le remplacement du node par dom, qui a pour effet de forcer le parsing du javascript */
			scripts[i].parentNode.replaceChild(scriptclone, scripts[i]);
		}
		return true;
	}


	// 트위터
	function twt_share(title,url) {
		window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url), 'twt_share', '');
	}
	// 미투데이
	function me2_share(title,url,tag) {
		window.open("http://me2day.net/posts/new?new_post[body]=" + encodeURIComponent(title) + " " + encodeURIComponent(url) + "&new_post[tags]=" + encodeURIComponent(tag), 'me2_share', '');
	}


	// - 페이스북 적용 ---
	function postToFeedCom(_link , _pic , _name  , _description) {
		// meta tag 변경
		$("link[rel=image_src]").attr("href" , _pic);
		$("meta[name=description]").attr("content" , _description);
		// facebook meta tag 변경
		$('meta[property^=og]').each(function(){
			var app_fbstr = $(this).attr("property");
			if(app_fbstr == "og:title"){$(this).attr("content" , _name);}
			else if(app_fbstr == "og:url"){$(this).attr("content" , _link);}
			else if(app_fbstr == "og:image"){$(this).attr("content" , _pic);}
			else if(app_fbstr == "og:site_name"){$(this).attr("content" , _name);}
			else if(app_fbstr == "og:description"){$(this).attr("content" , _description);}
		});

		// calling the API ...
		var obj = {
			method: 'feed',
			link: _link,
			picture: _pic,
			name: _name,
			description: _description
		};
		function callbackCom(response) {
			document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
		}
		FB.ui(obj, callbackCom);
	}
	// - 페이스북 적용 ---


	// - Comma 적용 ---
	String.prototype.comma=function() { 
		var l_text=this.replace(/,/g,''); 

		if(l_text == "0") return "0";

		var l_pattern=/^(-?\d+)(\d{3})($|\..*$)/; 

		if(l_pattern.test(l_text)){ 
			l_text=l_text.replace(l_pattern,function(str,p1,p2,p3) 
			{ 
			  return p1.comma() + ("," + p2 + p3); 
			}); 
		} 
		return l_text; 
	}
	// - Comma 적용 ---



	// - input 내 url 링크 적용 ---
	function click_link(field) {
		if($("input[name="+field+"]").val().length == 0 ) {
			alert("url을 입력해 주세요");
		}
		else {
			window.open('http://' + $("input[name="+field+"]").val().replace("http://", ""), 'click_link', ''); 
		}
	}
	// - input 내 url 링크 적용 ---



	// 로그인 알럿
	function login_alert() {
		alert("로그인 후 이용할수 있습니다.");
		login_view();
	}

	function Only_Numeric() {
		if((event.keyCode<48 || event.keyCode>57 || event.keyCode==45) && event.keyCode!=13) event.returnValue=false;
	}

	// 오브젝트 값의 길이가 limit 보다 길때 alert을 띄우고 자른다.
	function length_limit(obj,limit) {
	   var p, len=0;  // 한글문자열 체크를 위함
	   for(p=0; p< obj.value.length; p++) {
		(obj.value.charCodeAt(p)  > 255) ? len+=2 : len++;  // 한글체크

		if(len>limit) {
			alert("영문/숫자 기준 " +limit+" 글자 이상 입력할 수 없습니다."+getObjectLength(obj));
			obj.value = obj.value.substr(0,p);
			return;
		}
	   }

	}

	// 오브젝트 값의 길이를 구한다.
	function getObjectLength(obj) {
	   var p, len=0;  // 한글문자열 체크를 위함
	   for(p=0; p< obj.value.length; p++)
	   {
		(obj.value.charCodeAt(p)  > 255) ? len+=2 : len++;  // 한글체크
	   }
		return len;
	 }

	// 시작페이지 설정
	function set_start_page(url) 
	 {
		document.body.style.behavior='url(#default#homepage)';
		document.body.setHomePage('http://'+url);
	 }

$(document).ready(function() {

	/* ----- default 값 자동입력 및 처리. ---------- */
	input_dv_insert();// default 값 입력

	// input 포커스가 들어왔을때 default 값을 삭제한다.
	$("input[type=text], textarea").focus(function() {
		dv = $(this).attr("defaultValue");
		if($(this).val() == dv) 
			$(this).val("");
	});

	// input 값이 없이 포커스가 나가면 default 값을 삽입한다.
	$("input[type=text], textarea").blur(function() {
		dv = $(this).attr("defaultValue");
		if(!$(this).val() && dv) 
			$(this).val(dv);
	});	
	/* ----- // default 값 자동입력 및 처리. ---------- */





	/* ----- 숫자만 입력받고 천단위 콤마를 삽입한다. ---------- */
	$( ".number_style" ).bind( "keypress keyup", function() {

		// 숫자만 입력
		if( (event.keyCode<48 || event.keyCode>57) && event.keyCode!=45 && event.keyCode!=13) {
			event.returnValue=false;
		}

		// 천단위 콤마
		this.value = this.value.comma();

	});


	obj = $(".number_style");

	if(obj.length > 0)
		for(var i in obj) 
			if(obj[i].value != undefined) 
				obj[i].value = obj[i].value.comma();

	/* ----- // 숫자만 입력받고 천단위 콤마를 삽입한다. ---------- */

});

	function print_r(obj) {
		var a = "";
		var tmp = 1;
		for(key in obj) {
			if(tmp++ % 10 == 0) {alert(a);a="";}
			a+="`"+key+"` = "+obj[key]+"\n";
		}
		alert(a);
	}

	// defaultValue 값 입력
	function input_dv_insert() {

		obj = $("input[type=text], textarea");
		for(i=0;i<obj.length;i++) {
			dv = $(obj).eq(i).attr("defaultValue");
			!$(obj).eq(i).val() && dv ? $(obj).eq(i).val(dv) : null;
		}

	}

	// 입력전 defaultValue 값 제거
	function input_dv_delete() {

		obj = $("input[type=text], textarea");
		for(i=0;i<obj.length;i++) {
			dv = $(obj).eq(i).attr("defaultValue");
			$(obj).eq(i).val() == dv ? $(obj).eq(i).val("") : null;
		}

	}

	// 천단위 콤마 제거
	function number_style_comma_delete() {

		obj = $(".number_style");

		if(obj.length > 0)
			for(var i in obj) 
				if(obj[i].value != undefined) 
					obj[i].value = obj[i].value.replace(/,/g,"");
	}

	// 폼 서브밋을 위한 초기화
	function formSubmitSet() {

		input_dv_delete();
		number_style_comma_delete();
	
	}


//숫자앞에 0을 채운다.
function ZeroNum(num,length) {
    var pow_num = Math.pow(10,length);
    var rt_value = Number(pow_num)+Number(num);
    CharNum = String(rt_value).substring(1,rt_value.length);
    return CharNum;
}

