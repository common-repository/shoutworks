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

	setTimeout(function() {
		jQuery('.boxMessage,.publishskill_msg').fadeOut('slow');
	}, 100000);

	jQuery("form#createSkillForm").on('submit',function( e ) {
		e.preventDefault();
		if (!jscontains()) {
			return;
		}


		var skill_name = jQuery("#skill_name").val().toLowerCase();
		var keyHash = jQuery("#key_hash").val();
		var skill_invocation = jQuery("#skill_invocation").val().toLowerCase();
		var beta_tester_email =  jQuery("#beta_tester_email").val().toLowerCase();
		var feedUrl =  jQuery("#feed_url").val();
		var skillCreationTabUrl =  jQuery("#skill_creation_tab").val();
		var  queryString = feedUrl+ "&skill_name=" + skill_name + "&skill_invocation=" + skill_invocation + "&email=" + beta_tester_email;

		var ajax_url_val = "https://shoutworks.com/wp-json/shoutworx/v1/skill?" + queryString ;

		document.getElementById("alexaskill_msg").innerHTML = "";
		jQuery("#previewSubmit").attr("disabled", true);
		jQuery("#creating-label").show();
		jQuery(".previecreatenote").show();
		jQuery("#create-skill-label").hide();

		jQuery.ajax({
			type:'POST',
			beforeSend: function(xhr){xhr.setRequestHeader('authorization', keyHash);},
			url: ajax_url_val,
			success : function( response ) {
				//console.log( response.status );
				console.log( response );
				if( response.status == 'Success'){
					jQuery("#creating-label").hide();
					jQuery("#create-skill-label").show();
					jQuery('#publishskill_msg').show();
					jQuery(".previecreatenote").hide();
					var skill_name_f = jQuery("#skill_name").val().toLowerCase();
					var skill_invocation_f = jQuery("#skill_invocation").val().toLowerCase();
					
					window.location = skillCreationTabUrl + "&skill_created=true&status=success" + "&skill_name=" + skill_name + "&skill_invocation=" + skill_invocation + "&email=" + beta_tester_email ;
				}else{
					jQuery("#creating-label").hide();
					jQuery("#create-skill-label").show();
					jQuery("#previewSubmit").attr("disabled", false);
					document.getElementById("alexaskill_msg").innerHTML = "<div class='alert alert-danger col-sm-12'> Something went wrong. Do not create with duplicate data.</div>";
					jQuery('#alexaskill_msg').show();
					jQuery(".previecreatenote").hide();
				}
			},
			error: function (error) {
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
			beforeSend: function(xhr){xhr.setRequestHeader('authorization', keyHash);},
			type:'POST',
			dataType: "json",
			url: "https://shoutworks.com/wp-json/shoutworx/v1/skill/publish?skill_name=" + skill_name,
			success : function( response ) {
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
});

function update_published( ajax_url_val ){

	var skill_nameVar = document.getElementById("skill_name");
	var skill_name = skill_nameVar.value;
	skill_name = skill_name.toLowerCase();

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
