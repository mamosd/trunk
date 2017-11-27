var IE6 = (navigator.userAgent.indexOf("MSIE 6")>=0) ? true : false; //Detecting if the browser is IE6 to easily take browser-specific actions


$(document).ready(function() {  				   
	
	//DATEPICKER START
	$(function() {
		$("#datepicker").datepicker();
	});
	//DATEPICKER END				   
						   
	
	//TOP NAVIGATION DROPDOWN MENU START
	$(function(){
		$("ul.dropdown li").hover(function(){
			$('ul:first',this).css('visibility', 'visible');
		}, function(){
			$('ul:first',this).css('visibility', 'hidden');
		});
		
		$("ul.dropdown li ul li").hover(function(){
			var offset = $(this).offset();
			var offset2 = $('#topNavigationWrap').offset();
			$('ul:first',this).css('margin-top', offset.top-offset2.top-45);//This is where we calculate the position of submenus to add a margin-top for them.
		});
		$("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
	});
	//TOP NAVIGATION DROPDOWN MENU END
	
	
	//CHARTS START
	if (!(IE6)) { //function to disable charts for IE6
		$(function(){
			//Supports pie, bar, area and line values as chart types
			$('#chart').visualize({type: 'line', width: '640px', height: '200px'});
		});
	}
	//CHARTS END
	
	
	//ACCORDION START
      $(function() {
		$("#accordion").accordion({ autoHeight:false });
		$("#accordion2").accordion({ autoHeight:false });
		$("#tabs").tabs();
	});
	//ACCORDION END
	
	
	//WYSIWYG START
	$('#shortInfo').wysiwyg();
	//WYSIWYG END
	
	
	//COLORBOX START
	$(".colorBoxElement").colorbox();
	//COLORBOX END


	//ADVANCED SEARCH START
	$("#advancedSearchWrap").hide();
	$("#advancedSearchLink").click( function() {
		$("#advancedSearchWrap").slideToggle("slow");
		return false;
	});
	//ADVANCED SEARCH END


	//LOGIN FORM START
	$("#forgotPassword").hide();
	$("#forgotPasswordLink").click( function() {
		$("#login").slideUp("slow", function(){
			$("#forgotPassword").slideDown("slow");							  
		});
		return false;
	});
	
	$("#loginLink").click( function() {
		$("#forgotPassword").slideUp("slow", function(){
			$("#login").slideDown("slow");							  
		});
		return false;
	});
	//LOGIN FORM END

});