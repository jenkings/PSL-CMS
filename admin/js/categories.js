$(document).ready(function(){
	var categoryData = $('#categoryList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"manage_categories.php",
			type:"POST",
			data:{action:'categoryListing'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 2, 3],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$(document).on('click', '.delete', function(){
		var categoryId = $(this).attr("id");		
		var action = "categoryDelete";
		if(confirm("Are you sure you want to delete this category?")) {
			$.ajax({
				url:"manage_categories.php",
				method:"POST",
				data:{categoryId:categoryId, action:action},
				success:function(data) {					
					categoryData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
});