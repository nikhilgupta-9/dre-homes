
var tot=0;
var tot2=0;
var tot3=0
var tot4=0;
var tot5=0;
var tot6=0;
var tot7=0;
$('.add_more_gallery_image').on('click',function(e,index){
tot++;


	e.preventDefault();

$('.gallery_img').after(' <div class="row gallery_img_'+tot+'"> <div class="col-md-4 mt-3"> <input type="file" name="gallery_image[]" class="form-control"> </div><div class="col-md-4 mt-3"> <input type="text" name="gallery_image_alt[]" class="form-control" placeholder="Gallery Image Alt"> </div><div class="col-md-4 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_gallery_img del_'+tot+'" del-id='+tot+'><i class="flaticon-delete"></i></a> </div></div>');
});


$(document).delegate('.del_more_gallery_img','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.gallery_img_'+id+'').remove();
});


$('.add_more_feat_fac').on('click',function(e,index){
tot2++;


	e.preventDefault();

$('.feat_fac_icon').after(' <div class="row feat_fac_icon'+tot2+'"> <div class="col-md-4 mt-3"> <input type="file" name="feat_fac_img[]" class="form-control"> </div><div class="col-md-4 mt-3"> <input type="text" name="feat_fac_heading[]" class="form-control" placeholder="Featured Facility Heading"> </div><div class="col-md-4 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_feat_fac del_'+tot2+'" del-id='+tot2+'><i class="flaticon-delete"></i></a> </div></div>');
});


$(document).delegate('.del_more_feat_fac','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.feat_fac_icon'+id+'').remove();
});






$('.add_more_faq').on('click',function(e,index){
tot3++;


	e.preventDefault();

$('.faq').after(' <div class="row faq'+tot3+'"> <div class="col-md-4 mt-3"> <input type="text" name="faq_heading[]" class="form-control" placeholder="Heading"> </div><div class="col-md-4 mt-3"> <textarea name="faq_descp" class="form-control" placeholder="Description"></textarea></div><div class="col-md-4 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_faq del_'+tot3+'" del-id='+tot3+'><i class="flaticon-delete"></i></a> </div></div>');
});



$(document).delegate('.del_more_faq','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.faq'+id+'').remove();
});

$('.add_more_payplan').on('click',function(e,index){
tot4++;


	e.preventDefault();

$('.payplan').after(' <div class="row payplan'+tot4+'"> <div class="col-md-3 mt-3"> <input type="text" name="payplan_heading[]" class="form-control" placeholder="Heading"> </div><div class="col-md-3 mt-3"> <input type="text" name="payplan_percentage[]" class="form-control" placeholder="Percentage"></div><div class="col-md-3 mt-3"><input type="text" name="payplan_status[]" class="form-control" placeholder="Status"></div><div class="col-md-3 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_payplan del_'+tot4+'" del-id='+tot4+'><i class="flaticon-delete"></i></a> </div></div>');
});

$(document).delegate('.del_more_payplan','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.payplan'+id+'').remove();
});


$('.add_more_guide_sec').on('click',function(e,index){
tot5++;


	e.preventDefault();

$('.guide_sec').after(' <div class="row guide_sec_'+tot5+'"> <div class="col-md-3 mt-3"> <input type="file" name="guide_image[]" class="form-control"> </div><div class="col-md-3 mt-3"> <input type="text" name="guide_head[]" class="form-control" placeholder="Heading"> </div><div class="col-md-3 mt-3"> <textarea name="guide_short_descp[]" class="form-control" placeholder="Short Description"></textarea></div><div class="col-md-3 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_guide_sec del_'+tot5+'" del-id='+tot5+'><i class="flaticon-delete"></i></a> </div></div>');
});


$(document).delegate('.del_more_guide_sec','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.guide_sec_'+id+'').remove();
});




$('.add_more_comm_loc').on('click',function(e,index){
tot3++;


	e.preventDefault();

$('.comm_loc').after(' <div class="row comm_loc'+tot3+'"> <div class="col-md-4 mt-3"> <input type="text" name="comm_loc_heading[]" class="form-control" placeholder="Heading"> </div><div class="col-md-4 mt-3"> <textarea name="comm_loc_description[]" class="form-control" placeholder="Description"></textarea></div><div class="col-md-4 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_comm_loc del_'+tot3+'" del-id='+tot3+'><i class="flaticon-delete"></i></a> </div></div>');
});



$(document).delegate('.del_more_comm_loc','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.comm_loc'+id+'').remove();
});




$('.add_more_comm_map').on('click',function(e,index){
tot3++;


	e.preventDefault();

$('.comm_maps').after(' <div class="row comm_maps'+tot3+'"> <div class="col-md-3 mt-3"> <input type="text" name="comm_map_fa_icon[]" class="form-control" placeholder="Fa Icon"> </div><div class="col-md-3 mt-3"><input type="text" name="comm_map_heading[]" class="form-control" placeholder="Heading"></div><div class="col-md-3 mt-3"><input type="text" name="comm_map_distance[]" class="form-control" placeholder="Distance"></div><div class="col-md-3 mt-3"> <a href="javascript:void(0)" class="btn btn-danger del_more_comm_maps del_'+tot3+'" del-id='+tot3+'><i class="flaticon-delete"></i></a> </div></div>');
});



$(document).delegate('.del_more_comm_maps','click',function(e){
var id=$(this).attr('del-id')	
$(this).closest('.comm_maps'+id+'').remove();
});





/*Delete record confirmation box starts*/

$('.del_rec').on('click',function(e){
	e.preventDefault();

var $t=$(this);
var redirect_url=$t.attr('data-url');


Swal.fire({
  icon: 'question',
  title: 'Are you sure you want to delete ?',
  showDenyButton: true,
  confirmButtonText: 'Yes',
  denyButtonText: `No`,
}).then((result) => {
  if (result.isConfirmed) {
    window.location=redirect_url;
  } else if (result.isDenied) {
    return false;
  }
});

});


/*Delete record confirmation box ends*/


$('#login-form').validate({


rules:
{
	login_email:
	{
		required:true,
		email:true
	},
	login_password:
	{
		required:true
	}
},
messages:
{
	login_email:
	{
		required:'Email ID is required',
		email:'Enter a valid Email ID'
	},
	login_password:
	{
		required:'Password is required'
	}
},
submitHandler:function(form)
{
	$.ajax({

		url:base_url+'ajax/login.php',
		data:$('#login-form').serializeArray(),
		type:'POST',
		dataType:'json',
		beforeSend:function()
		{
			$('button[name="login_submit"]').text('WAIT...');
			$('button[name="login_submit"]').prop('disabled',true);

		},
		success:function(r)
		{
			if(r.status=='success')
			{
				window.location=base_url+'dashboard.php';
			}
			else
			{
				$('.err_msg').addClass('alert alert-danger').text(r.msg);
				$("#login-form")[0].reset();
				$('button[name="login_submit"]').text('Log In');
			    $('button[name="login_submit"]').prop('disabled',false);
			}

		}



	});
	
}


});


$('#add-user').validate({


rules:
{
	username:
	{
		required:true,
		remote:
		{
			url:base_url+'ajax/check-username.php',
			type:'post'
		}
	},
	login_email:
	{
		required:true,
		email:true,
		remote:
		{
			url:base_url+'ajax/check-email.php',
			type:'post'
		}
	},
	login_password:
	{
		required:true
	}
},
messages:
{
	username:
	{
		required:'Username is required',
		remote: jQuery.validator.format("{0} is already taken.")
	},
	login_email:
	{
		required:'Email ID is required',
		email:'Email ID is required',
		remote: jQuery.validator.format("{0} is already taken.")
	},
	login_password:
	{
		required:'Password is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('#add-user')[0].submit();
}


});

$('#edit-user').validate({


rules:
{
	username:
	{
		required:true
	}
},
messages:
{
	username:
	{
		required:'Username is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('#edit-user')[0].submit();
}


});


$('#add-about-slider').validate({

rules:
{
	abt_slider_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	}
},
messages:
{
	abt_slider_img:
	{
		required:'Image is required',
		extension:'Image must be in jpg | jpeg | png format'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-about-slider')[0].submit();
}


});


$('#edit-about-slider').validate({


submitHandler:function(form)
{
	document.getElementById('edit-about-slider')[0].submit();
}


});



$('#edit-about-content').validate({

	ignore:[],

rules:
{

	abt_content_descp:
	{
		required:true
	}
},
messages:
{
	abt_content_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-about-content')[0].submit();
}


});


/*$('#edit-about-service-image').validate({

rules:
{
	abt_serv_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	}
},
messages:
{
	abt_serv_img:
	{
		required:'Image is required',
		extension:'Image must be in jpg | jpeg | png format'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-about-service-image')[0].submit();
}

});*/


$('#add-about-service').validate({


ignore:[],
rules:
{
	abt_serv_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	abt_serv_img_alt:
	{
		required:true
	},
	abt_serv_heading:
	{
		required:true
	},
	abt_serv_descp:
	{
		required:true
	}
},
messages:
{
	abt_serv_img:
	{
		required:'Image is required',
		extension:'Image must be in jpg | jpeg | png format'
	},
	abt_serv_img_alt:
	{
		required:'Image Alt Tag is required'
	},
	abt_serv_heading:
	{
		required:'Heading is required'
	},
	abt_serv_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-about-service')[0].submit();
}


});



$('#edit-about-service').validate({


ignore:[],
rules:
{

	abt_serv_img_alt:
	{
		required:true
	},
	abt_serv_heading:
	{
		required:true
	},
	abt_serv_descp:
	{
		required:true
	}
},
messages:
{

	abt_serv_img_alt:
	{
		required:'Image Alt Tag is required'
	},
	abt_serv_heading:
	{
		required:'Heading is required'
	},
	abt_serv_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-about-service')[0].submit();
}


});


$('#add-team').validate({

rules:
{
	team_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	team_name:
	{
		required:true
	}
},
messages:
{
	team_img:
	{
		required:'Image is required',
		extension:'Image must be in jpg | jpeg | png format'
	},
	team_name:
	{
		required:'Name is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-team')[0].submit();
}


});


$('#edit-team').validate({

rules:
{
	team_name:
	{
		required:true
	}
},
messages:
{
	team_name:
	{
		required:'Name is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-team')[0].submit();
}


});



$('#edit-team-details').validate({

rules:
{

},
messages:
{
	

},
submitHandler:function(form)
{
	document.getElementById('edit-team-details')[0].submit();
}


});



$('#edit-mission-vision').validate({

ignore:[],
rules:
{
	
	mission_img_alt:
	{
		required:true
	},
	mission_content:
	{
		required:true
	},
	vision_img_alt:
	{
		required:true
	},
	vision_content:
	{
		required:true
	}
},
messages:
{
	
	mission_img_alt:
	{
		required:'Mission Image Alt Tag is required'
	},
	mission_content:
	{
		required:'Mission Content is required'
	},
	vision_img_alt:
	{
		required:'Vision Image Alt Tag is required'
	},
	vision_content:
	{
		required:'Vision Content is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-mission-vision')[0].submit();
}


});


$('#edit-testimonial-content').validate({


rules:
{
	test_heading:
	{
		required:true
	}
},
messages:
{
	test_heading:
	{
		required:'Heading is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-testimonial-content')[0].submit();
}


});


$('#edit-awards-content').validate({


rules:
{
	award_cont_heading:
	{
		required:true
	}
},
messages:
{
	award_cont_heading:
	{
		required:'Heading is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-awards-content')[0].submit();
}


});


$('#add-testimonial').validate({

ignore:[],
rules:
{
 
	test_client_name:
	{
		required:true
	},
	test_rating:
	{
		required:true,
		number:true
	},
	test_descp:
	{
		required:true
	},
	test_posted_on:
	{
		required:true
	}
},
messages:
{
	
	test_client_name:
	{
		required:'Client Name is required'
	},
	test_rating:
	{
		required:'Rating is required',
		number:'Please enter only number'
	},
	test_descp:
	{
		required:'Description is required'
	},
	test_posted_on:
	{
		required:'Date of Posting is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-testimonial')[0].submit();
}


});


$('#edit-testimonial').validate({

ignore:[],
rules:
{
  test_client_name:
	{
		required:true
	},
	test_rating:
	{
		required:true,
		number:true
	},
	test_descp:
	{
		required:true
	}
},
messages:
{
	test_client_name:
	{
		required:'Client Name is required'
	},
	test_rating:
	{
		required:'Rating is required',
		number:'Please enter only number'
	},
	test_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-testimonial')[0].submit();
}


});


$('#add-awards').validate({

ignore:[],
rules:
{
   award_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	award_img_alt:
	{
		required:true
	},
	award_descp:
	{
		required:true
	}
},
messages:
{
	test_img:
	{
		required:'Image is required',
		extension:'Image must be in jpg | jpeg | png format'
	},
	test_img_alt:
	{
		required:'Image Alt Tag is required'
	},
	test_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-awards')[0].submit();
}


});


$('#edit-awards').validate({

ignore:[],
rules:
{
  
	award_img_alt:
	{
		required:true
	},
	award_descp:
	{
		required:true
	}
},
messages:
{
	
	award_img_alt:
	{
		required:'Image Alt Tag is required'
	},
	award_descp:
	{
		required:'Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-awards')[0].submit();
}


});


$('#edit-blog-content').validate({


rules:
{

},
messages:
{
	
},
submitHandler:function(form)
{
	document.getElementById('edit-blog-content')[0].submit();
}


});


$('#add-blog').validate({

ignore:[],
rules:
{
   blog_thumb_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	blog_thumb_img_alt:
	{
		required:true
	},
	blog_heading:
	{
		required:true
	},
	blog_short_descp:
	{
		required:true
	},
	 blog_inner_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	blog_inner_img_alt:
	{
		required:true
	},
	blog_long_descp:
	{
		required:true
	},
	blog_slug:
	{
		required:true
	},
		blog_meta_title:
	{
		required:true
	},
		blog_meta_keyword:
	{
		required:true
	},
		blog_meta_descp:
	{
		required:true
	},
		blog_posted_on:
	{
		required:true
	}
},
messages:
{
	blog_thumb_img:
	{
		required:'Blog Thumb Image is required',
		extension:"Blog Thumb Image must be in jpg|jpeg|png format"
	},
	blog_thumb_img_alt:
	{
		required:'Blog Thumb Image Alt Tag is required'
	},
	blog_heading:
	{
		required:'Blog Heading is required'
	},
	blog_short_descp:
	{
		required:'Blog Short Description is required'
	},
	 blog_inner_img:
	{
		required:'Blog Inner Image is required',
		extension:"Blog Inner Image must be in jpg|jpeg|png format"
	},
	blog_inner_img_alt:
	{
		required:'Blog Inner Image Alt Tag is required'
	},
	blog_long_descp:
	{
		required:'Blog Long Description is required'
	},
	blog_slug:
	{
		required:'Blog Slug is required'
	},
		blog_meta_title:
	{
		required:'Blog Meta title is required'
	},
		blog_meta_keyword:
	{
		required:'Blog Meta keyword is required'
	},
		blog_meta_descp:
	{
		required:'Blog Meta description is required'
	},
		blog_posted_on:
	{
		required:'Blog Posted On is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-blog')[0].submit();
}


});



$('#edit-blog').validate({

ignore:[],
rules:
{

	blog_thumb_img_alt:
	{
		required:true
	},
	blog_heading:
	{
		required:true
	},
	blog_short_descp:
	{
		required:true
	},
	blog_inner_img_alt:
	{
		required:true
	},
	blog_long_descp:
	{
		required:true
	},
	blog_slug:
	{
		required:true
	},
		blog_meta_title:
	{
		required:true
	},
		blog_meta_keyword:
	{
		required:true
	},
		blog_meta_descp:
	{
		required:true
	},
		blog_posted_on:
	{
		required:true
	}
},
messages:
{
	
	blog_thumb_img_alt:
	{
		required:'Blog Thumb Image Alt Tag is required'
	},
	blog_heading:
	{
		required:'Blog Heading is required'
	},
	blog_short_descp:
	{
		required:'Blog Short Description is required'
	},
	blog_inner_img_alt:
	{
		required:'Blog Inner Image Alt Tag is required'
	},
	blog_long_descp:
	{
		required:'Blog Long Description is required'
	},
	blog_slug:
	{
		required:'Blog Slug is required'
	},
		blog_meta_title:
	{
		required:'Blog Meta title is required'
	},
		blog_meta_keyword:
	{
		required:'Blog Meta keyword is required'
	},
		blog_meta_descp:
	{
		required:'Blog Meta description is required'
	},
		blog_posted_on:
	{
		required:'Blog Posted On is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-blog')[0].submit();
}


});




$('#add-service').validate({

ignore:[],
rules:
{
   serv_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	serv_img_alt:
	{
		required:true
	},
	serv_heading:
	{
		required:true
	},
	serv_descp:
	{
		required:true
	}


},
messages:
{
	 serv_img:
	{
		required:'Service image is required',
		extension:"Service image must be in jpg|jpeg|png format"
	},
	serv_img_alt:
	{
		required:'Service Thumb image Alt tag is required'
	},
	serv_heading:
	{
		required:'Service Heading is required'
	},
	serv_descp:
	{
		required:'Service Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-service')[0].submit();
}


});



$('#edit-service').validate({

ignore:[],
rules:
{
  
	serv_img_alt:
	{
		required:true
	},
	serv_heading:
	{
		required:true
	},
	serv_descp:
	{
		required:true
	}


},
messages:
{
	
	serv_img_alt:
	{
		required:'Service Thumb image Alt tag is required'
	},
	serv_heading:
	{
		required:'Service Heading is required'
	},
	serv_descp:
	{
		required:'Service Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-service')[0].submit();
}


});




$('#add-developer').validate({

ignore:[],
rules:
{
   dev_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	dev_img_alt:
	{
		required:true
	},
	dev_heading:
	{
		required:true
	},
	dev_short_descp:
	{
		required:true
	},
	dev_long_descp:
	{
		required:true
	}


},
messages:
{
	 dev_img:
	{
		required:'Developer image is required',
		extension:"Developer image must be in jpg|jpeg|png format"
	},
	dev_img_alt:
	{
		required:'Developer Image Alt tag is required'
	},
	dev_heading:
	{
		required:'Developer Heading is required'
	},
	dev_short_descp:
	{
		required:'Developer Short Description is required'
	},
	dev_long_descp:
	{
		required:'Developer Long Description is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-developer')[0].submit();
}


});


$('#edit-developer').validate({

ignore:[],
rules:
{
  
	dev_img_alt:
	{
		required:true
	},
	dev_heading:
	{
		required:true
	},
	dev_short_descp:
	{
		required:true
	},
	dev_long_descp:
	{
		required:true
	}


},
messages:
{
	
	dev_img_alt:
	{
		required:'Developer Image Alt tag is required'
	},
	dev_heading:
	{
		required:'Developer Heading is required'
	},
	dev_short_descp:
	{
		required:'Developer Short Description is required'
	},
	dev_long_descp:
	{
		required:'Developer Long Description is required'
	}
},
submitHandler:function(form)
{
	//document.getElementById('edit-developer')[0].submit();
	form.submit();
}


});




$('#add-facilities').validate({

ignore:[],
rules:
{
   fac_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	fac_img_alt_tag:
	{
		required:true
	},
	fac_heading:
	{
		required:true
	}


},
messages:
{
	 fac_img:
	{
		required:'Facility image is required',
		extension:"Facility image must be in jpg|jpeg|png format"
	},
	fac_img_alt_tag:
	{
		required:'Facility Image Alt tag is required'
	},
	fac_heading:
	{
		required:'Facility Heading is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('add-facilities')[0].submit();
}


});



$('#edit-facilities').validate({

ignore:[],
rules:
{
   
	fac_img_alt_tag:
	{
		required:true
	},
	fac_heading:
	{
		required:true
	}


},
messages:
{
	
	fac_img_alt_tag:
	{
		required:'Facility Image Alt tag is required'
	},
	fac_heading:
	{
		required:'Facility Heading is required'
	}
},
submitHandler:function(form)
{
	document.getElementById('edit-facilities')[0].submit();
}


});



$('#add-services').validate({

ignore:[],
rules:
{
   serv_img:
	{
		required:true,
		extension:"jpg|jpeg|png|webp"
	},
	serv_img_alt_tag:
	{
		required:true
	},
	serv_heading:
	{
		required:true
	}


},
messages:
{
	 serv_img:
	{
		required:'Service image is required',
		extension:"Service image must be in jpg|jpeg|png format"
	},
	serv_img_alt_tag:
	{
		required:'Service Image Alt tag is required'
	},
	serv_heading:
	{
		required:'Service Heading is required'
	}

},
submitHandler:function(form)
{
	document.getElementById('add-services')[0].submit();
}


});




$('#edit-services').validate({

ignore:[],
rules:
{
	serv_img_alt_tag:
	{
		required:true
	},
	serv_heading:
	{
		required:true
	}


},
messages:
{
	serv_img_alt_tag:
	{
		required:'Service Image Alt tag is required'
	},
	serv_heading:
	{
		required:'Service Heading is required'
	}

},
submitHandler:function(form)
{
	document.getElementById('edit-services')[0].submit();
}


});





/*Delete Thumb Image start*/

$(document).delegate('.del_thumb_img','click',function(e){

var $t=$(this);
var del_id=$t.attr('del-id');
var del_name=$t.attr('del-name');

var conf_check=confirm('Are you sure you want to delete ?');
if(conf_check==true)
{

	$.ajax({

		url:base_url+'ajax/proj_thumb_img.php',
		data:{del_id:del_id,del_name:del_name},
		type:'POST',
		dataType:'json',
		beforeSend:function()
		{

		},
		success:function(r)
		{

			if(r.status=='success')
			{
				window.location.reload();
			}
			else
			{
				alert('Error ! Try after sometime');
			}

		}


	});

}
else
{
	return false;
}

});

/*Delete Thumb Image ends*/


/*PDF remove starts*/

$(document).delegate('.pdf_remove','click',function(e){

var $t=$(this);
var pdf_id=$t.attr('pdf-id');
var pdf_type=$t.attr('pdf-type');
var pdf_name=$t.attr('pdf-name');

var conf_check=confirm('Are you sure you want to delete ?');


if(conf_check==true)
{
  	$.ajax({

		url:base_url+'ajax/pdf_remove.php',
		data:{pdf_id:pdf_id,pdf_type:pdf_type,pdf_name:pdf_name},
		type:'POST',
		dataType:'json',
		beforeSend:function()
		{

		},
		success:function(r)
		{

			if(r.status=='success')
			{
				window.location.reload();
			}
			else
			{
				alert('Error ! Try after sometime');
			}

		}


	});
    
}
else
{
	return false;
}

});


/*PDF remove ends*/




/*Image remove starts*/

$(document).delegate('.img_remove','click',function(e){

var $t=$(this);
var img_id=$t.attr('img-id');
var img_type=$t.attr('img-type');
var img_name=$t.attr('img-name');

var conf_check=confirm('Are you sure you want to delete ?');


if(conf_check==true)
{
  	$.ajax({

		url:base_url+'ajax/img_remove.php',
		data:{img_id:img_id,img_type:img_type,img_name:img_name},
		type:'POST',
		dataType:'json',
		beforeSend:function()
		{

		},
		success:function(r)
		{

			if(r.status=='success')
			{
				window.location.reload();
			}
			else
			{
				alert('Error ! Try after sometime');
			}

		}


	});
    
}
else
{
	return false;
}

});


/*Image remove ends*/



    $('#proj_bedroom').select2({
      tags: true,
      placeholder: "Add bedrooms",
      allowClear: true,
      width: '100%'
    });
 $(document).ready(function () {
	// Initialize DataTable with filtering
	var table = $('#example').DataTable({
		"pageLength": 10,
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		"language": {
			"search": "Search:",
			"lengthMenu": "Show _MENU_ entries",
			"info": "Showing _START_ to _END_ of _TOTAL_ entries",
			"infoEmpty": "Showing 0 to 0 of 0 entries",
			"infoFiltered": "(filtered from _MAX_ total entries)",
			"zeroRecords": "No matching records found"
		}
	});

	// Initialize tooltips - only on hover
	$('[data-toggle="tooltip"]').tooltip({
		trigger: 'hover',
		placement: 'top',
		html: true,
		animation: true,
		delay: { show: 300, hide: 100 }
	});

	// Re-initialize tooltips after table redraw (pagination, search, etc.)
	$('#example').on('draw.dt', function () {
		$('[data-toggle="tooltip"]').tooltip({
			trigger: 'hover',
			placement: 'top',
			html: true,
			animation: true,
			delay: { show: 300, hide: 100 }
		});
	});
});


$(document).ready(function () {
	var table = $('#example').DataTable();

	$('#statusFilter').on('change', function () {
		var value = $(this).val();

		if (value == '1') {
			table.column(6).search('Active').draw();
		} else if (value == '2') {
			table.column(6).search('Inactive').draw();
		} else {
			table.column(6).search('').draw();
		}
	});

	$('#completionFilter').on('change', function () {
		var value = $(this).val();

		if (value == 'complete') {
			table.column(5).search("^0 Pending$", true, false).draw();
		} else if (value == 'incomplete') {
			table.column(5).search("Pending", true, false).draw();
		} else {
			table.column(5).search('').draw();
		}
	});

	$('#pending-search-filter').on('keyup', function () {
		table.search(this.value).draw();
	})

	$('#clearFilters').on('click', function () {
		$('#statusFilter').val('');
		$('#completionFilter').val('');
		$('#pending-search-filter').val('');

		table.search('').columns().search('').draw();
	});

	$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

		var selectedColumn = $('#completionFilter').val();

		if (!selectedColumn) return true;

		var rowNode = table.row(dataIndex).node();
		var emptyFields = $(rowNode).attr('data-empty-fields');

		if (!emptyFields) return false;

		var fieldsArray = emptyFields.split(',');

		return fieldsArray.includes(selectedColumn);
	});

	$('#completionFilter').on('change', function () {
		table.draw();
	});
})
// this code use for update order of the team memeber 
    
$(document).ready(function () {
    // Handle form submission for order update - Using delegated event for dynamically loaded content
    $(document).on('submit', 'form[id^="update_team_order_"]', function (e) {
        e.preventDefault(); // Prevent normal form submission

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var $input = $form.find('input[name="order_no"]');
        var team_id = $form.find('input[name="team_id"]').val();
        var team_desg_id = $form.find('input[name="team_desg_id"]').val();
        var order_no = $input.val();

        // Clear previous validation styling
        $input.css('border', '');

        // Validation - Check for empty
        if (order_no === '' || order_no === null) {
            toastr.warning('Order number cannot be empty');
            $input.css('border', '2px solid #ffc107');
            return false;
        }

        if (team_desg_id === '' || team_desg_id === null) {
            toastr.warning('Team Member designation missing');
            $input.css('border', '2px solid #ffc107');
            return false;
        }

        // Validation - Check if numeric
        if (isNaN(order_no)) {
            toastr.warning('Please enter a valid number');
            $input.css('border', '2px solid #ffc107');
            return false;
        }

        // Validation - Check if number is valid range
        var orderNum = parseInt(order_no);
        if (orderNum < 0) {
            toastr.warning('Order number cannot be negative');
            $input.css('border', '2px solid #ffc107');
            return false;
        }

        // Save original button text
        var originalButtonHtml = $button.html();

        // Show loading state
        $button.prop('disabled', true);
        $button.html('<i class="fa fa-spinner fa-spin"></i>');
        $input.css('border', '2px solid #007bff');

        // Make AJAX request
        $.ajax({
            url: base_url + 'ajax/update-team-order.php',
            type: 'POST',
            dataType: 'json',
            data: {
                team_id: team_id,
                order_no: order_no,
                team_desg_id : team_desg_id
            },
            success: function (response) {
                console.log('Response: - custom.js:1557', response);

                if (response.status === 'success') {
                    toastr.success(response.message);
                    $input.css('border', '2px solid #28a745');

                    // Store the new value as original
                    $input.data('original-value', order_no);

                    // Reset border after 2 seconds
                    setTimeout(function () {
                        $input.css('border', '');
                    }, 2000);

                } else if (response.status === 'invalid') {
                    toastr.warning(response.message);
                    $input.css('border', '2px solid #ffc107');

                    // Reset to previous value if validation fails
                    var prevValue = $input.data('original-value');
                    if (prevValue !== undefined) {
                        $input.val(prevValue);
                    }

                } else {
                    toastr.error(response.message);
                    $input.css('border', '2px solid #dc3545');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: - custom.js:1587', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });

                let errorMsg = 'Server error! ';
                if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        errorMsg += response.message || 'Please try again.';
                    } catch (e) {
                        errorMsg += 'Please try again.';
                    }
                } else {
                    errorMsg += 'Status: ' + status;
                }

                toastr.error(errorMsg);
                $input.css('border', '2px solid #dc3545');

                // Reset to previous value on error
                var prevValue = $input.data('original-value');
                if (prevValue !== undefined) {
                    $input.val(prevValue);
                }
            },
            complete: function () {
                // Restore button state
                $button.prop('disabled', false);
                $button.html(originalButtonHtml);
            }
        });
    });

    // Store original value when input gets focus
    $(document).on('focus', 'input[name="order_no"]', function () {
        var $this = $(this);
        if (!$this.data('original-value')) {
            $this.data('original-value', $this.val());
        }
    });

    // Auto-submit on Enter key
    $(document).on('keypress', 'input[name="order_no"]', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            $(this).closest('form').submit();
        }
    });
});

// Optional: Auto-submit on blur (when leaving the field)
$('input[name="order_no"]').on('blur', function() {
    var $this = $(this);
    var originalValue = $this.data('original-value');
    var currentValue = $this.val();

    // Only submit if value changed and not empty
    if (originalValue !== currentValue && currentValue !== '') {
        $this.closest('form').submit();
    }
});

$(document).ready(function() {

    var table = $('#example').DataTable();

    $('#filter_status').on('change', function() {
        table.column(7).search(this.value).draw();
    });

    $('#filter_designation').on('change', function() {
        table.column(4).search(this.value).draw();
    });

});
// here we apply sidebar search filter 

$(document).ready(function(){
    $('#menuSearch').on('keyup', function(){
        var value = $(this).val().toLowerCase();

        $('.menu-item').each(function(){

            // skip search box itself
            if($(this).find('#menuSearch').length) return;

            var text = $(this).text().toLowerCase();

            if(text.indexOf(value) > -1){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    })
})
// here we add project add preview document 
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;

    const previewLink = input.parentElement.querySelector('.preview-link');

    // Create temporary URL
    const fileURL = URL.createObjectURL(file);

    previewLink.href = fileURL;
    previewLink.style.display = 'inline-block';

    // Optional: change button text
    previewLink.innerText = "Preview " + file.name;
}
// developer order no submit code 

$(document).ready(function () {
    let input = $('#devOrderNoInput').val();
    let devId = $('#devId').val();
    let Msg = $('#devOrderMsg').hide();

    $('#devOrderSubmitBtn').on('click', function () {
        let currentInput = $('#devOrderNoInput').val();
        let currentDevId = $('#devId').val();

        // Validation
        
        $.ajax({
            url: base_url + "ajax/dev-order-update.php",
            method: "POST",
            data: {
                input: currentInput,
                devId: currentDevId
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    Msg.show().html(res.message).css('color', 'green');
                    setTimeout(() => {
                        Msg.fadeOut();
                    }, 2000)
                } else {
                    Msg.show().html(res.message).css('color', 'red');
                    setTimeout(() => {
                        Msg.fadeOut();
                    }, 2000)
                }
            },
            error: function (xhr, status, error) {
                Msg.show().html('Error: ' + error).css('color', 'red');
                setTimeout(() => {
                    Msg.fadeOut();
                }, 2000)
            }
        });
    });
});
// this is developer page filer code   
$(document).ready(function() {
    // Auto-submit when filter changes
    $('#filterHomeDeveloper').on('change', function() {
        var filterValue = $(this).val();
        var currentUrl = window.location.href.split('?')[0]; // Get base URL without parameters
        var url = new URL(currentUrl);
        
        if (filterValue && filterValue != '0') {
            url.searchParams.set('filter_home', filterValue);
        } else {
            url.searchParams.delete('filter_home');
        }
        
        window.location.href = url.toString();
    });
});
// this is push data in my crm 
$(document).on('click', '.pushToCRM', function () {

    $.ajax({
        url: base_url + 'ajax/push_to_crm.php',
        type: 'POST',
        dataType: 'json',
        data: {
            lead_id: $(this).data('id'),
            source: $(this).data('source'),
            first_name: $(this).data('firstname'),
            email: $(this).data('email'),
            phone: $(this).data('phoneno'),
            lead_source: $(this).data('leadsource'),
            developer: $(this).data('developer'),
            unit_type: $(this).data('unittype'),
            campaign_name: $(this).data('campaignname'),
            ip_address: $(this).data('ipaddress'),
            sub_location: $(this).data('sublocation')
        },
        success: function (response) {
            alert(response.message);
            console.log(response);
        },
        error: function (xhr) {
            console.log(xhr.responseText);
            alert('CRM Push Failed');
        }
    });

});
$(document).ready(function () {
    $('#mediaTypeToggle').on('change', function () {
        if ($(this).is(':checked')) {
            // Switch to Upload Local Video
            $('#youtube_wrapper').slideUp();
            $('#youtube_link').val(''); // YouTube field empty karein
            
            $('#video_wrapper').slideDown();
        } else {
            // Switch back to YouTube URL
            $('#video_wrapper').slideUp();
            $('#video_file').val(''); // File field empty karein
            
            $('#youtube_wrapper').slideDown();
        }
    });
});
// remove image from header on page edit-header-info.php page 
function removeImageAjax(imageType, id, headerId) {
    if (confirm('Are you sure you want to remove this image?')) {
        const container = document.getElementById(imageType + '_container');
        if (container) {
            container.innerHTML = '<div class="mt-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Removing...</div>';
        }
        
        // AJAX call to remove the image
        $.ajax({
            url: base_url + 'ajax/remove_header_image.php',
            type: 'POST',
            data: {
                image_type: imageType,
                header_id: headerId,
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showNotification('success', response.message);
                    // Reload after a short delay to show the notification
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error removing image: ' + response.message);
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error connecting to server: ' + error);
                location.reload();
            }
        });
    }
}

// Helper function for notifications (if not already defined)
function showNotification(type, message) {
    // Remove existing notifications
    const existingNotif = document.querySelector('.custom-notification');
    if (existingNotif) {
        existingNotif.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'custom-notification alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '400px';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    notification.innerHTML = `
        <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}