/*
 * Copyright lin 2017
 */

$(document).ready( function() {

    //左菜单栏
    $('.templatemo-sidebar-menu li.sub a').click(function(){
        if($(this).parent().hasClass('open')) {
            $(this).parent().removeClass('open');
        } else {
            $(this).parent().addClass('open');
        }
    });

	//管理员
	$(".admin_edit").click(function() {
		adminid = $(this).data('id');
		$.post("/admin/get_info", {id:adminid}, function(data){
			$("#adminid").val(data.data.id);
			$("#username").val(data.data.username);
			$("#name").val(data.data.name);
		},'json');
		$("#myModal").modal();
	});

});

function save(){
    var adminid = $("#adminid").val();
    var username = $("#username").val();
    var name = $("#name").val();
    $.post('/admin/update',{id:adminid,username:username,name:name}, function(data){
        if(data.code==200){
            $('#myModal').modal('hide');
            $("#success").modal("show");
            setTimeout(function(){window.location.reload();},1000);
        } else {
            $('#myModal').modal('hide');
            $('#error').modal('show');
            setTimeout(function(){$("#error").modal("hide")},1000);
        }
    },'json');
}