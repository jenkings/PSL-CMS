$(document).ready(function(){
	var usersData = $('#userList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_user.php",
			type:"POST",
			data:{action:'userListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 4, 5],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var userId = $(this).attr("id");		
		var action = "userDelete";
		if(confirm("Are you sure you want to delete this user?")) {
			$.ajax({
				url:"manage_user.php",
				method:"POST",
				data:{userId:userId, action:action},
				success:function(data) {					
					usersData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});

});