
// 根据屏幕宽屏还是窄屏自动调整字体大小
// （等日后再完善，把导航字体大小、正文内容字体大小，还有两边面板DIV区域的大小比等等）
window.onload=font; 
function font(){ 
   if (screen.width <1024) 
       document.styleSheets[0].addRule("body","font-size:12px;"); 
   else if (screen.width ==1024) 
         document.styleSheets[0].addRule("body","font-size:14px;"); 
      else if (screen.width >1024) 
           document.styleSheets[0].addRule("body","font-size:16px;"); 
         else 
             document.styleSheets[0].addRule("body","font-size:16px;"); 
}

$(document).ready(function() {
	if ($('#loading').length > 0) $('#loading').fadeOut(200);
});
