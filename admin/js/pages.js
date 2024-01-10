$(document).ready(function(){
	var postsData = $('#pagesList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_pages.php",
			type:"POST",
			data:{action:'pagesListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[3,4,5],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var pageId = $(this).attr("id");		
		var action = "pageDelete";
		if(confirm("Are you sure you want to delete this post?")) {
			$.ajax({
				url:"manage_pages.php",
				method:"POST",
				data:{pageId:pageId, action:action},
				success:function(data) {					
					postsData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	

	$(document).on('click', '.add', function(){
		var pageId = $(this).attr("id");		
		var action = "pageAddToMenu";
		if(confirm("Are you sure you want to add this page to menu")) {
			$.ajax({
				url:"manage_pages.php",
				method:"POST",
				data:{pageId:pageId, action:action},
				success:function(data) {					
					postsData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	

});