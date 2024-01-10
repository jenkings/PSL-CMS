$(document).ready(function(){
	var usersData = $('#imagesList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_image.php",
			type:"POST",
			data:{action:'imageListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2],
				"orderable":false,
			},
		],
		"pageLength": 100000000
	});		
	$(document).on('click', '.delete', function(){
		var imageId = $(this).attr("id");		
		var action = "imageDelete";
		if(confirm("Are you sure you want to delete this image?")) {
			$.ajax({
				url:"manage_image.php",
				method:"POST",
				data:{imageId:imageId, action:action},
				success:function(data) {					
					usersData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});

});