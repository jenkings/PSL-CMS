$(document).ready(function(){
	var postsData = $('#postsList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_posts.php",
			type:"POST",
			data:{action:'postListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 6, 7],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var postId = $(this).attr("id");		
		var action = "postDelete";
		if(confirm("Are you sure you want to delete this post?")) {
			$.ajax({
				url:"manage_posts.php",
				method:"POST",
				data:{postId:postId, action:action},
				success:function(data) {					
					postsData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
});