function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}

function containsSpecialCharacters(str){
    var regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g;
	return regex.test(str);
}

function jscontains() {


	var skill_nameVar = document.getElementById("skill_name");
    var skill_name = skill_nameVar.value.trim();
    skill_name = skill_name.toLowerCase();


	var skill_invocationVar = document.getElementById("skill_invocation");
    var skill_invocation = skill_invocationVar.value.trim();
    skill_invocation = skill_invocation.toLowerCase();

	var beta_tester_emailVar = document.getElementById("beta_tester_email");
	beta_tester_email = beta_tester_emailVar.value.trim();

	var errorarray = [];

	var form_has_error = false;
	if( skill_name === null || skill_name === '' ) {

		errorarray.push( "Skill name can not be empty.")
		form_has_error = true;

	}else if ( (skill_name.indexOf('the ')!==-1) || (skill_name.indexOf('a ')!==-1) || (skill_name.indexOf('an ')!==-1)  || (skill_name.indexOf('for ')!==-1)  || (skill_name.indexOf('to ')!==-1)  || (skill_name.indexOf('of ')!==-1) || (skill_name.indexOf('about')!==-1) || (skill_name.indexOf('up ')!==-1)  || (skill_name.indexOf('by ')!==-1)  || (skill_name.indexOf('at ')!==-1) || (skill_name.indexOf('off ')!==-1) || (skill_name.indexOf('from')!==-1) || (skill_name.indexOf('in ')!==-1)  || (skill_name.indexOf('using')!==-1)  || (skill_name.indexOf('with ')!==-1)  || (skill_name.indexOf('that ')!==-1) || (skill_name.indexOf('if ')!==-1) || (skill_name.indexOf('and ')!==-1)  || (skill_name.indexOf('whether')!==-1)  || (skill_name.indexOf('alexa')!==-1) || (skill_name.indexOf('amazon')!==-1) || (skill_name.indexOf('echo')!==-1) || (skill_name.indexOf('skill')!==-1)  || (skill_name.indexOf('app')!==-1) ) {

		errorarray.push("This error message is here because the Skill name and/or Invocation name you used was something Amazon will not like. Your Skill name and/or Invocation name cannot have certain words including but not limited to: the, a, an, for, to, of, about, up, by, at, off, from, in, using, with, that, if, and, whether, alexa, amazon, echo, skill, app.");
		form_has_error = true;

	}else if ( containsSpecialCharacters(skill_name) ) {

		errorarray.push('Skill name contains illegal special characters.');
		form_has_error = true;

	}else if ( skill_name.split(' ').length < 2 ) {

		errorarray.push( "Skill name requires atleast 2 words.");
		form_has_error = true;

	}else if ( skill_name.length > 36 ) {

		errorarray.push( "Skill name allowed Only 36 characters.");
		form_has_error = true;

	}else if( skill_invocation == null || skill_invocation == '' ) {

		errorarray.push("Skill Invocation can not be empty.");
		form_has_error = true;

	}else if ( (skill_invocation.indexOf('the ')!==-1) || (skill_invocation.indexOf('a ')!==-1) || (skill_invocation.indexOf('an ')!==-1)  || (skill_invocation.indexOf('for ')!==-1)  || (skill_invocation.indexOf('to ')!==-1)  || (skill_invocation.indexOf('of ')!==-1) || (skill_invocation.indexOf('about')!==-1) || (skill_invocation.indexOf('up ')!==-1)  || (skill_invocation.indexOf('by ')!==-1)  || (skill_invocation.indexOf('at ')!==-1) || (skill_invocation.indexOf('off ')!==-1) || (skill_invocation.indexOf('from')!==-1) || (skill_invocation.indexOf('in ')!==-1)  || (skill_invocation.indexOf('using')!==-1)  || (skill_invocation.indexOf('with ')!==-1)  || (skill_invocation.indexOf('that ')!==-1) || (skill_invocation.indexOf('if ')!==-1) || (skill_invocation.indexOf('and ')!==-1)  || (skill_invocation.indexOf('whether ')!==-1)  || (skill_invocation.indexOf('alexa')!==-1) || (skill_invocation.indexOf('amazon')!==-1) || (skill_invocation.indexOf('echo')!==-1) || (skill_invocation.indexOf('skill')!==-1)  || (skill_invocation.indexOf('app')!==-1) ) {

		errorarray.push("This error message is here because the Skill name and/or Invocation name you used was something Amazon will not like. Your Skill name and/or Invocation name cannot have certain words including but not limited to: the, a, an, for, to, of, about, up, by, at, off, from, in, using, with, that, if, and, whether, alexa, amazon, echo, skill, app.");

		form_has_error = true;

	}else if ( containsSpecialCharacters(skill_invocation) ) {

		errorarray.push('Skill invocation contains illegal special characters.');
		form_has_error = true;

	}else if ( skill_invocation.split(' ').length < 2 ) {

		errorarray.push("Skill Invocation requires atleast 2 words.");
		form_has_error = true;

	}else if( beta_tester_email === null || beta_tester_email === '' ) {

		errorarray.push("Please enter beta tester email.");
		form_has_error = true;

	}else if(IsEmail(beta_tester_email)==false){

		errorarray.push("Please enter valid email.");
		form_has_error = true;

	}else{

	}

	if ( form_has_error ) {

		var filtered = errorarray.filter(function (el) {
		  return el != null;
		});

		//console.log(errorarray);

		document.getElementById("alexaskill_msg").innerHTML = '<div class="alert alert-danger col-sm-12">'+filtered.join(' ')+'</div>';
		jQuery('.boxMessage').show();
		return false;
	}else{

		return true;
	}


}

jQuery(document).ready( function(){

	jQuery( '#frmShoutWorksEngage' ).submit(function(){
		jQuery( '#update-button-action-label' ).show();
		jQuery( '#update-button-default-label' ).hide();
	});

	if( jQuery('.shout_player').length ){
		jQuery('.shout_player').each(function(){
		    jQuery(this).find('audio').attr('src', jQuery(this).find('audio').data( 'src' ) );
		});
		jQuery(document).click(function( e ){
			if( jQuery('.title_edit:visible').length && !jQuery( e.target ).parents('.title_edit').length  && !jQuery( e.target ).parents('.title_view').length ){
				jQuery('.title_view').show();
				jQuery('.title_edit').hide();
			}
		});
	}
	
	jQuery(".edit_title").click(function(e){
		e.preventDefault();
		jQuery('.title_view').show();
		jQuery('.title_edit').hide();
		jQuery(this).parents(".card-header").find(".title_edit").css("display","inline-block");
		jQuery(this).parents(".card-header").find(".title_view").hide();
	});
	
	jQuery('.title_txt').keypress(function (e) {
        
        var regex = new RegExp("^[a-zA-Z 0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
			if(jQuery('.title_txt').val().length < 64)
				return true;
			else
			{
				e.preventDefault();
				alert('Maximum 64 characters allowed');
				return false;
			}
            
        }
        else
        {
			e.preventDefault();
			alert('Only letters and numbers, please! :)');
			return false;
        }
        
    });
	
	jQuery(".update_title").click(function(e){
		e.preventDefault();
		jQuery(".title_edit").removeClass("title_error");
		var ele = jQuery(this);
		var type= jQuery(this).attr("type");
		var title = jQuery(this).parents(".title_edit").find("input.title_txt").val();
		var words = jQuery.trim(title).split(" ");
		if(words.length > 4)
		{
			ele.parents(".title_edit").addClass("title_error");
			ele.parents(".card-header").append("<span class='title_error_msg'>Only 4 words allowed.</span>");
			setTimeout(function() {
				ele.parents(".card-header").find(".title_error_msg").remove();
			}, 5000);		
			return false;
		}
		var old = ele.parents(".card-header").find(".title_view").find("span").html();
		ele.parents(".card-header").find(".title_view").find("span").html(title);
		jQuery.ajax({
		  url: sw_ajax.sw_ajax_url+"?action=sw_update_title&title="+title+"&type="+type,
		  type: "POST",
		}).done(function(data) {
			console.log(data);
			if(data=="0")
			{
				ele.parents(".card-header").find(".title_view").find("span").html(old);
				return false;	
			}
			else
			{
				ele.parents(".card-header").find(".title_view").find("span").html(title);
				location.reload(true);
			}
		}).fail(function(error){
			console.log(error);
		});
		
		jQuery(this).parents(".card-header").find(".title_view").css("display","inline-block");
		jQuery(this).parents(".card-header").find(".title_edit").hide();
	});
	
	jQuery(".input.title_txt").keyup(function(e){
		jQuery(this).width(parseInt(jQuery(this).width())+pareseInt(10))
	});


	setTimeout(function() {
		jQuery('.boxMessage,.publishskill_msg').fadeOut('slow');
	}, 100000);

	jQuery("form#createSkillForm").on('submit',function( e ) {
		e.preventDefault();
		if (!jscontains()) {
			return;
		}

				document.getElementById("alexaskill_msg").innerHTML = "";
		jQuery("#previewSubmit").attr("disabled", true);
		jQuery("#creating-label").show();
		jQuery(".previecreatenote").show();
		jQuery("#create-skill-label").hide();


		/*console.log("calleddd");*/
		var skill_name = jQuery("#skill_name").val().toLowerCase();
		var original_skill_name = jQuery("#skill_name").val();
		var keyHash = jQuery("#key_hash").val();
		var skill_invocation = jQuery("#skill_invocation").val().toLowerCase();
		var beta_tester_email =  jQuery("#beta_tester_email").val().toLowerCase();
		var feedUrl =  jQuery("#feed_url").val();
		var skillCreationTabUrl =  jQuery("#skill_creation_tab").val();
		
		var skill_name_f = jQuery("#skill_name").val().toLowerCase();
		var skill_invocation_f = jQuery("#skill_invocation").val().toLowerCase();
		upload_response =  sw_upload_skill_icon(skill_name_f);
		/*console.log(upload_response.responseJSON);*/
		var icon_res = upload_response.responseJSON;
		/*console.log(icon_res.status);
		console.log(icon_res.small_url);
		console.log(icon_res.large_url);*/
		if(icon_res.status == "success")
		{
			/*console.log("success");
			console.log(icon_res.small_url);
			console.log(icon_res.large_url);*/
			if(icon_res.small_url!='')
				var skill_icon_small = icon_res.small_url;
			if(icon_res.large_url!='')
				var skill_icon_large = icon_res.large_url;
		}
		else
		{
			/*console.log("fail");*/
			jQuery(".skill_icon_error").html("Something went wrong. Please check your skill icon.");	
			jQuery(".skill_icon_error").show();
			return false;
		}
		
		var queryString = feedUrl+ "&skill_name=" + skill_name + "&skill_invocation=" + skill_invocation + "&email=" + beta_tester_email+"&small_url="+skill_icon_small+"&large_url="+skill_icon_large+ "&original_skill_name=" + original_skill_name;
		queryString += '&blog_title=' + sw_ajax.feed_names.blog_title ;
		queryString += '&quote_title=' + sw_ajax.feed_names.quote_title ;
		queryString += '&flash_title=' + sw_ajax.feed_names.flash_title ;
		queryString += '&notify_title=' + sw_ajax.feed_names.notify_title ;
		queryString += '&deal_title=' + sw_ajax.feed_names.deal_title ;
		queryString += '&podcast_title=' + sw_ajax.feed_names.podcast_title ;
		/*console.log(queryString);*/
		var ajax_url_val = "https://shoutworks.com/wp-json/shoutworx/v1/skill" ;
		jQuery.ajax({
			type:'POST',
			beforeSend: function(xhr){xhr.setRequestHeader('authorization', keyHash);},
			url: ajax_url_val,
			data: queryString,
			success : function( response ) {
				/*console.log("after autho");
				console.log( response.status );
				console.log( response );*/
				if( response.status == 'Success'){
					/*console.log("in success of skill");
					console.log(response.skill_id);*/
					jQuery("#creating-label").hide();
					jQuery("#create-skill-label").show();
					jQuery('#publishskill_msg').show();
					jQuery(".previecreatenote").hide();
					if(response.skill_id!='')
					{
						jQuery.ajax({
							url: sw_ajax.sw_ajax_url+"?action=sw_update_skill_id&skill_id="+response.skill_id,
							type: "POST",
							dataType:"json",
							async:false,   
						}).done(function(data) {
							/*console.log(data);*/
						});	
					}
					window.location = skillCreationTabUrl + "&skill_created=true&status=success" + "&skill_name=" + original_skill_name + "&skill_invocation=" + skill_invocation + "&email=" + beta_tester_email;
				}else{
					/*console.log("in else skill");*/
					jQuery("#creating-label").hide();
					jQuery("#create-skill-label").show();
					jQuery("#previewSubmit").attr("disabled", false);
					document.getElementById("alexaskill_msg").innerHTML = "<div class='alert alert-danger col-sm-12'> Something went wrong. Do not create with duplicate data.</div>";
					jQuery('#alexaskill_msg').show();
					jQuery(".previecreatenote").hide();
				}
			},
			error: function (error) {
				/*console.log("in error skill");
				console.log( error );*/
				jQuery("#creating-label").hide();
				jQuery("#create-skill-label").show();
				jQuery("#previewSubmit").attr("disabled", false);
				document.getElementById("alexaskill_msg").innerHTML = "<div class='alert alert-danger col-sm-12'> Something went wrong.</div>";
				jQuery('#alexaskill_msg').show();
				jQuery(".previecreatenote").hide();
			}
		});

	});

	jQuery('#content').on('click', 'a.rml_bttn', function(e) {
		e.preventDefault();
		var rml_post_id = jQuery(this).data( 'id' );
		jQuery.ajax({
			url : readmelater_ajax.ajax_url,
			type : 'post',
			data : {
				action : 'read_me_later',
				post_id : rml_post_id
			},
			success : function( response ) {
				jQuery('.rml_contents').html(response);
			}
		});
		jQuery(this).hide();
	});


	jQuery("form#publishSkillForm").on('submit',function( e ) {
		e.preventDefault();


		var skill_nameVar = document.getElementById("skill_name");
		var skill_name = skill_nameVar.value;
		var keyHash = jQuery("#key_hash").val();
		skill_name = skill_name.toLowerCase();
		var ajax_url = document.getElementById("ajax_url");
		document.getElementById("publishskill_msg").innerHTML = "";
		jQuery("#publishSubmit").attr("disabled", true);
		jQuery("#creating-label-publish").show();
		jQuery("#publish-skill-label").hide();
		jQuery("#msztxt").hide();
		jQuery.ajax({
			beforeSend: function(xhr){xhr.setRequestHeader('authorization', keyHash)},
			type:'POST',
			dataType: "json",
			url: "https://shoutworks.com/wp-json/shoutworx/v1/skill/publish?skill_name=" + skill_name,
			success : function( response ) {
				//console.log(response);
				if( response.status == 'Success'){
					var ajax_url = document.getElementById("ajax_url");
					var ajax_url_val = ajax_url.value;
					update_published( ajax_url_val );
					jQuery("#creating-label-publish").hide();
					jQuery("#publishSkillForm").hide();
					jQuery("#publishSubmit").attr("disabled", true);
					jQuery("#publish-skill-label").show();
					
					document.getElementById("publishskill_msg").innerHTML = "<div class='alert alert-success col-sm-12'><strong>Success!</strong> Wahoo! You've sent your new Alexa Skill off to Amazon for review. Check your email over the next few days to see progress.</div>";
					jQuery("#publishskill_msg").show();
					jQuery("#msztxt").show();

				}else{
					jQuery("#creating-label-publish").hide();
					jQuery("#publish-skill-label").show();
					jQuery("#publishSubmit").attr("disabled", false);
					document.getElementById("publishskill_msg").innerHTML = "<div class='alert alert-danger col-sm-12'> Something went wrong. Do not create with duplicate data.</div>";
					jQuery("#publishskill_msg").show();
				}

			},
			error: function (error) {
				jQuery("#creating-label-publish").hide();
				jQuery("#publish-skill-label").show();
				jQuery("#publishSubmit").attr("disabled", false);
				document.getElementById("publishskill_msg").innerHTML = "<div class='alert alert-danger col-sm-12'> Something went wrong.</div>";
				jQuery("#publishskill_msg").show();
			}
		});

	});
	
	jQuery(document).on("click",".upload_skill_icon",function(e){
		e.preventDefault();
		jQuery(".skill_icon_error").hide();
		jQuery(".file_skill_icon").trigger("click");
	});
	jQuery(".file_skill_icon").change(function(){
		var img  = jQuery(".file_skill_icon").val();
		
		if(img!='')
		{
			var ext = img.split('.').pop().toLowerCase();
			if(ext!='png')
			{
				
				jQuery(".skill_icon_error").html("Only PNG file are allowed");	
				jQuery(".skill_icon_error").show();
				return false;
			}
			var _URL = window.URL || window.webkitURL;

			var file = jQuery(this)[0].files[0];
			newimg = new Image();
			var imgwidth = 0;
			var imgheight = 0;
			var maxwidth = 512;
			var maxheight = 512;
			if(file.size > 1048576)
			{
				jQuery(".skill_icon_error").html("Image size is greater than 1MB.");	
				jQuery(".skill_icon_error").show();
				return false;
			}
			newimg.src = _URL.createObjectURL(file);
			newimg.onload = function() {
				imgwidth = this.width;
				imgheight = this.height;	
			}
			if(imgwidth > maxwidth && imgheight > maxheight){
				jQuery(".skill_icon_error").html("Image must be 512px X 512px.");	
				jQuery(".skill_icon_error").show();
				return false;
			}
			
			 var reader = new FileReader();
			 reader.readAsDataURL(jQuery(".file_skill_icon")[0].files[0]);
			 reader.onload = function (e) {
				//console.log(e.target.result);
				jQuery(".icon-skill-img").attr("src",e.target.result);
				jQuery(".icon-skill-img").show();
				
			 }
			 
		}
	});
	function sw_upload_skill_icon(skill_name)
	{
		var img  = jQuery(".file_skill_icon").val();
		/*console.log(img);*/
		if(img!='')
		{
			/*console.log("custom icon");*/
			jQuery(".sw_loader").show();
			var ajax_url = document.getElementById("ajax_url");
			var frmdata = new FormData();
			
			var files = jQuery('#file_skill_icon')[0].files[0];
			frmdata.append('sw_skill_icon',files);
			return jQuery.ajax({
				  url: sw_ajax.sw_ajax_url+"?action=sw_upload_icon&skill_name="+skill_name,
				  type: "POST",
				  dataType:"json",
				  data: frmdata,
				  enctype: 'multipart/form-data',
				  processData: false, 
				  contentType: false,
				  async:false,   
				}).done(function(data) {
					/*console.log(data);
					console.log("custom icon response");*/
					if(data.status == "success")
					{
						jQuery(".icon-skill-img").attr("src",jQuery.trim(data.small_url));
						jQuery(".sw_loader").hide();
						//jQuery(".skill_icon_success").html("Skill Icon uploaded.").show();
						return data;
					}
					else
					{
						if(data.msg=="FILE_SIZE_ERROR")
							jQuery(".skill_icon_error").html("Icon image size must be less than 1MB.").show();
						if(data.msg=="FILE_TYPE_ERROR")
							jQuery(".skill_icon_error").html("Only PNG file is allowed.").show();
						if(data.msg=="FILE_DIM_ERROR")
							jQuery(".skill_icon_error").html("Icon image must be 512px X 512px.").show();
							
						jQuery(".sw_loader").hide();
						return false;
					}
				})
		}
		else
		{
			/*console.log("default icon");*/
			return jQuery.ajax({
				  url: sw_ajax.sw_ajax_url+"?action=sw_save_deafult_icon&skill_name="+skill_name,
				  type: "POST",
				  dataType:"json",
				  async:false,
				}).done(function(data) {
					/*console.log("custom icon response");
					console.log(data);*/
					return data;
				}).fail(function(error){
					/*console.log("this is in error");*/
					console.log(error);
				});
			
		}	
	}

	
});
function update_published( ajax_url_val ){

	var skill_nameVar = document.getElementById("skill_name");
	var skill_name = skill_nameVar.value;
	//skill_name = skill_name.toLowerCase();

	var skill_invocationVar = document.getElementById("skill_invocation");
	var skill_invocation = skill_invocationVar.value;
	skill_invocation = skill_invocation.toLowerCase();

	var beta_tester_emailVar = document.getElementById("beta_tester_email");
	beta_tester_email = beta_tester_emailVar.value;

	jQuery.ajax({
		type:'POST',
        data:{action:'country_list',skill_name:skill_name,skill_invocation:skill_invocation,beta_tester_email:beta_tester_email},
        url: ajax_url_val,
		success : function( response ) {

			console.log("updated");
		},
		error: function (error) {
			console.log("error");
		}
	});
}

