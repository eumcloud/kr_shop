(function($) {
		  
$.fn.fliptimer = function(options){
	var defaults = {onDone:function(){  },year:0,month:0,days:0,hours:0,minutes:0,seconds:0,ratio:100,path:"/pages/skin/V2/img/"};
	var options = $.extend(defaults, options);
	var root = $(this);
	var width=16 * (options.ratio/100),height=25 * (options.ratio/100);
	
	if(root.length>1)
	{
		$.each(root,function(){
						
						$(this).fliptimer(options);
						
						})
		return;
	}
	
	var targetTime = new Date();
	targetTime.setDate(options.days);
	targetTime.setMonth(options.month-1);
	targetTime.setFullYear(options.year);
	targetTime.setHours(options.hours);
	targetTime.setMinutes(options.minutes);
	targetTime.setSeconds(options.seconds);
	
	var nowTime = new Date(),diffSecs = Math.floor((targetTime.valueOf()-nowTime.valueOf())/1000);
	var seconds = diffSecs % 60,
	 minutes = Math.floor(diffSecs/60)%60,
	 hours = Math.floor(diffSecs/60/60)%24,
	 day = Math.floor(diffSecs/60/60/24);	
	
	var limit=10,structure='',c,sc1 =(seconds%10)+1,sc2 = (seconds -(seconds%10))/10+1,m1=(minutes%10) + 1,m2 = (minutes - (minutes%10))/10 + 1,no_animation=false,i;
	var h1 = (hours%10) + 1,h2 = (hours - (hours%10))/10 + 1;
	
	
    var img_array = ['','','','','','','','','','','',''];
   
    root.wrap("<div class='ft' />");
	var flip = root.parent();
	
	if(diffSecs<=0)
	{
	 
	for(i=0;i<3;i++) {
	
	 root.prepend("<div class='holder'><img src='"+options.path+"0u.png' /><img src='"+options.path+"0b.png' style='margin-top:-16px;' /></div>");
	  
	}
	 no_animation=true;
	
	 
	}
	else{
		
	i=0;
	 var n = -3;
	  if($.browser.msie&&$.browser.version==7)
		   {
			  n = -2;
		   }
    while (day!=0) {
	  
	  c = day%10;
	  if(day==0)
	  break;
	  day = parseInt(day/10);
	  root.prepend("<div class='holder'><img src='"+options.path+c+"u.png' /><img src='"+options.path+c+"b.png' style='margin-top:"+n+"px;' /></div>");
	  i++;
	}
	i = 3 - i 
	for(var j=0;j<i;j++)
	 root.prepend("<div class='holder'><img src='"+options.path+"0u.png' /><img src='"+options.path+"0b.png' style='margin-top:"+n+"px;' /></div>");
	 
	
	}
	
     
 
   
	for(i=0;i<3;i++)
	 structure += "<div class='holder gap'><div></div><div></div></div>"+"<div class='holder '><div></div><div></div></div>";
	 
	root.append(structure);
	
	flip.find("div").find("div:last").css({marginTop:width-2});
     flip.find(".holder").css("width",width+width/2);
	flip.css({height:height*2});
	
		
	var div_arrays = flip.find(".holder");
    var i,j,temp = [ 
				[ div_arrays.eq("8").find("div:first"),div_arrays.eq("8").find("div:last") ],
				[ div_arrays.eq("6").find("div:first"),div_arrays.eq("6").find("div:last") ],
				[ div_arrays.eq("4").find("div:first"),div_arrays.eq("4").find("div:last") ],
				[ div_arrays.eq("7").find("div:first"),div_arrays.eq("7").find("div:last") ],
				[ div_arrays.eq("5").find("div:first"),div_arrays.eq("5").find("div:last") ],
				[ div_arrays.eq("3").find("div:first"),div_arrays.eq("3").find("div:last") ] ];
	
	if(no_animation==true)
	{
		for(i=0,j=0;i<6;i++)
	{
		temp[i][0].html("<img class=reset src='"+options.path+"0u.png' />");
		temp[i][1].html("<img class=reset src='"+options.path+"0b.png' style='margin-Top:3px;' />");
		
		j++;
	}
	options.onDone();
	return;
	}
	
	if(minutes==0&&hours==0)
	{
		limit = 4; //rare occasion
	h1 = -1;
	}
	for(i=0;i<10;i++)
	{
		if(sc1==10)
		sc1 = 0;
		if(m1==10)
		m1 = 0;
		if(h1==limit)
		h1 = 0;
		
		img_array[0] = img_array[0] + "<img class=reset src='"+options.path+sc1+"u.png' />";
		img_array[1] = img_array[1] + "<img class=reset src='"+options.path+sc1+"b.png' />";
		sc1++;
		
		
		img_array[2] = img_array[2] + "<img class=reset src='"+options.path+m1+"u.png' />";
		img_array[3] = img_array[3] + "<img class=reset src='"+options.path+m1+"b.png' />";
		m1++;
		
		
		img_array[4] = img_array[4] + "<img class=reset src='"+options.path+h1+"u.png' />";
		img_array[5] = img_array[5] + "<img class=reset src='"+options.path+h1+"b.png' />";
		h1++;
		
		if(i<6)
		{
		if(sc2>=6)
		sc2 = 0;
		if(m2>=6)
		m2 = 0;
		
		img_array[6] = img_array[6] + "<img class=reset src='"+options.path+sc2+"u.png' />";
		img_array[7] = img_array[7] + "<img class=reset src='"+options.path+sc2+"b.png' />";
		sc2++;
		
		img_array[8] = img_array[8] + "<img class=reset src='"+options.path+m2+"u.png' />";
		img_array[9] = img_array[9] + "<img class=reset src='"+options.path+m2+"b.png' />";
		m2++;
		}
		
		if(i<3)
		{
		if(h2>=3)
		h2 = 0;
		img_array[10] = img_array[10] +"<img class=reset src='"+options.path+h2+"u.png' />";
		img_array[11] = img_array[11] + "<img class=reset src='"+options.path+h2+"b.png' />";
		h2++;
	
		}
		
		
	}
	
	for(i=0,j=0;i<6;i++)
	{
		temp[i][0].html(img_array[j++]);
		temp[i][1].html(img_array[j]);
		
		j++;
	}
	

	root.find("img").css({width:width+20,height:height});
    
	
	var mod =[ $.makeArray(temp[0][0].find("img")),
	  $.makeArray(temp[0][1].find("img")),
	
	  $.makeArray(temp[3][0].find("img")),
	  $.makeArray(temp[3][1].find("img")),
	
	  $.makeArray(temp[1][0].find("img")),
	  $.makeArray(temp[1][1].find("img")),
	
	  $.makeArray(temp[4][0].find("img")),
	  $.makeArray(temp[4][1].find("img")),
	
	  $.makeArray(temp[2][0].find("img")),
	  $.makeArray(temp[2][1].find("img")),
	
	  $.makeArray(temp[5][0].find("img")),
	  $.makeArray(temp[5][1].find("img"))];
	
	
	var th,sr,temp,last,first;
	
	
     if(no_animation==false)
	init();
	
		function init(){
	var timer = setInterval(function(){
				    sc1--;
					
					
					if(diffSecs<=0)
					{
						options.onDone();
						clearInterval(timer);
						return;
					}
					
					if(sc1==0)
					{
						sc1=10;
						sc2--;
						animateFlip(mod[2],mod[3]);
					}
					if(sc2==0)
					{
						sc2=6;
						m1--;
						animateFlip(mod[4],mod[5]);
					}
					if(m1==0)
					{
						m1=10;
						m2--;
						animateFlip(mod[6],mod[7]);
					}	
					if(m2==0)
					{
						m2=6;
						h1--;
						animateFlip(mod[8],mod[9]);
						if(limit==4)
						{
location.reload(true);

						}
					}	
				    if(h1==0)
					{
						h1=10;
						animateFlip(mod[10],mod[11]);
						
					}
					diffSecs--;
					animateFlip(mod[0],mod[1]);
						 
						 },1000);
	 
		}
	 
	
	function animateFlip(up,down)
	{
		 $(up[up.length-1]).animate({height:0,width:width+20,marginTop:width+1},400,function(){  incrementz(up,false);  });
						
						setTimeout(function(){
											
						 incrementz(down,true);
						 $(down[down.length-1]).animate({height:height,width:width+20},500);
						 
											},250);
	
	    
	}
	 function incrementz(array,flag)
	 {
				
		temp = array.pop();
		array.unshift(temp);
		last = $(array[array.length-1]); first = $(array[0]);
		if(first.hasClass('active'))
			first.toggleClass('active reset');
		last.toggleClass('active reset');
		if(flag==false)
			$(array[array.length-2]).css({display:"block",opacity:1.0,height:height,marginTop:0});
			else
			{
				last.css({display:"block",opacity:1.0,height:0,width:width+20});
				$(array[1]).css({display:"block",opacity:1.0,height:0,width:width+20});
			}
	
	 };

} 
})(jQuery);