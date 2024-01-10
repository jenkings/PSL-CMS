$(document).ready(function(){
	var categoryData = $('#menuList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_menu.php",
			type:"POST",
			data:{action:'menuListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0,1,2,3,4],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var menuId = $(this).attr("id");		
		var action = "menuDelete";
		if(confirm("Are you sure you want to delete this menu item?")) {
			$.ajax({
				url:"manage_menu.php",
				method:"POST",
				data:{menuId:menuId, action:action},
				success:function(data) {					
					categoryData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	

	$(document).on('click', '.moveup', function(){
		var menuId = $(this).attr("id");		
		var action = "menuMoveUp";
		$.ajax({
			url:"manage_menu.php",
			method:"POST",
			data:{menuId:menuId, action:action},
			success:function(data) {					
				categoryData.ajax.reload();
			}
		})
	});	

	$(document).on('click', '.movedown', function(){
		var menuId = $(this).attr("id");		
		var action = "menuMoveDown";
		$.ajax({
			url:"manage_menu.php",
			method:"POST",
			data:{menuId:menuId, action:action},
			success:function(data) {					
				categoryData.ajax.reload();
			}
		})
	});	
});