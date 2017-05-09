/*
 * JS Document for 0.1C°体温计系统
 * Date: 2016-xx-xx
 * 1、初始化一些动画效果
 * 2、输入框提示语显示及隐藏效果
 * 3、用户名、密码输入限制
 * 3.1、登录按钮
 * 3.2、首次登录修改密码按钮
 * 4、管理首页左侧菜单项点击
 * 5、内嵌页加载效果
 * 6、内嵌页公用模版数据展现
 * 7、用户管理详情\编辑\新增点击
 * 8、显示正在保存
 * 9、账号权限页面的  编辑\新增
 *10、个人信息
 */

/*--1、初始化一些动画效果--*/
if (document.querySelectorAll) {
	if(!(parseInt((navigator.userAgent).slice(navigator.userAgent.indexOf("MSIE")+5,navigator.userAgent.indexOf("MSIE")+9)) <= 9)){
		//return false;
		window.WOW&&new WOW().init();
	}
}

require.config({
	baseUrl: "/assets/js",
	paths: {
		'jquery':'jquery-1.11.3.min',
		'layer':'layer2_3/layer',
		'jquery_cityLinkage':"jquery_cityLinkage",
		'jqueryui':'jquery-ui/jquery-ui.min'
	},
	urlArgs: {
		"main":true,
		"ztx_public":true,
		"bust":"bust=" + 20161129
	},
	shim: {
		'layer':{
			deps: ['jquery'],
			exports: 'layer'
		},
		'ztx_public':{
			deps: ['jquery'],
			exports: 'ztx_public'
		},
		'jquery_cityLinkage':['jquery'],
		'jqueryui':['jquery']
	}
});
require(['jquery','ztx_public','layer','jquery_cityLinkage','jqueryui'],function(jquery,ztx_public,layer){
	layer.config({
		path: '/assets/js/layer2_3/' //layer.js所在的目录，可以是绝对目录，也可以是相对目录
	});
	/*--2、输入框提示语显示及隐藏效果--*/
	$(".tips_click").on('focus blur keydown keyup',function(){
		var $this = $(this);
		if(!!$this.val()){
			$this.siblings("label").hide();
		}else{
			$this.siblings("label").show();
		};
	}).each(function(index, element) {
		var $this = $(this);
		if($this.val()!=""){
			$this.siblings("label").hide();
		}
	});
	/*--3、用户名、密码输入限制--*/
	$(".check_username,.check_password").on('keyup',function(e){
		var $this = $(this);
		var val = $this.val();
		if(/[\u4e00-\u9fa5\s]/g.test(val)){
			$this.val(val.replace(/[\u4e00-\u9fa5\s]/g,''));
		}
	});
	/*--3.1、登录按钮--*/
	//回车键触发登录
	if($("#loginAdmin").length > 0){
		$(document).keydown(function(event){
			if(event.which ==13){
				$("#loginAdmin").click();
			}
		});
	}
	$("#loginAdmin").click(function(){
		var $this = $(this);
		if($this.hasClass("disabled_btn")){
			return false;
		};
		var username = $.trim($("#username").val());
		var password = $.trim($("#password").val());
		if(username != "" && password != ""){
			$this.addClass("disabled_btn").html('登录中<img src="/assets/images/loading_small.gif" alt="" />');
			ztx_public.ajax('/admin/login_check',{"username":username,"password":password},function(obj) {
				if (obj.code == 200) {
					window.location.href = obj.data.url;
				}else {
					$(".err_tips").text(obj.message).fadeIn(function(){
						window.setTimeout(function(){
							$(".err_tips").fadeOut();
							$this.removeClass("disabled_btn").html('登录');
						},2000);
					});
				}
			},function(){
				$(".err_tips").text("网络连接失败").fadeIn(function(){
					window.setTimeout(function(){
						$(".err_tips").fadeOut();
						$this.removeClass("disabled_btn").html('登录');
					},2000);
				});
			});
		}else{
			$(".err_tips").text("账号或密码不能为空！").fadeIn(function(){
				if(username == ""){
					$("#username").focus();
				}else if(password == ""){
					$("#password").focus();
				}
				window.setTimeout(function(){
					$(".err_tips").fadeOut();
				},2000);
			});
		}
	});

	/*--5、内嵌页加载效果--*/
	$("#loading").fadeOut();
	var hash = window.location.hash;
	if(!!hash&&!!$(".embed_page").length){
		//var $currentMenu = $(window.parent.document).find(hash);
		//if((!!$currentMenu.length)&&(!$currentMenu.hasClass("current"))){
		//$currentMenu.addClass("current").siblings().removeClass("current");
		//}
		$(".embed_page a").on("click",function(){
			var $this = $(this);
			var href = $this.attr("href");
			$this.attr("href",href+hash);
		});
		$("form button[type='submit']").on("click",function(){
			var $this = $(this).parents('form');
			var action = $this.attr("action");
			$this.attr("action",action+hash);
		});
	}

	/*--6、内嵌页公用模版数据展现--*/
	function show_Template(uTemplate,obj){
		var txt = $(uTemplate).html();
		$.each(obj,function(i,n){
			var reg = new RegExp('\\{%'+i+'%\\}','g');
			txt = txt.replace(reg,!!n?n:"");
		});
		return txt;
	}

	/*--8、显示正在保存--*/
	function showSave(layero,txt){
		layero.append('<div class="showSave" style="display:none;">'+txt+'&nbsp;<img src="/assets/images/loading_small.gif" alt="" /></div>');
		$(".showSave").css({
			"position":"absolute",
			"top":0,
			"left":0,
			"height":layero.outerHeight(true),
			"line-height":layero.outerHeight(true)+"px",
			"width":layero.outerWidth(true),
			"opacity":.5,
			"color":"#fff",
			"font-size":"1.15em",
			"text-align":"center",
			"background-color":"#000"
		});
	}

	//权限管理
	(function(){
		if(!$("#Admin").length){
			return false;
		}

		//权限新增用户
		$(document).on("click","#authorityAdd",function(){
			var txt = $("#uTemplateadd").html();

			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '480px',
				closeBtn: 0,
				content: txt,
				btn: ['保存修改', '关闭'],
				yes: function(index, layero){

					var authorityId = $("#addauthorityId").val();
					var username = $("#addusername").val();
					var password = $("#addpassword").val();
					var name = $("#addname").val();
                    if(!username){
                        layer.tips("请填写用户名",$("#addusername"),{time:2000});
                        return false;
                    }
					if(!name){
						layer.tips("请填写姓名",$("#addname"),{time:2000});
						return false;
					}
					showSave(layero,"正在保存");
					$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
						ztx_public.ajax('/admin/add',{"role_id":authorityId,"username":username,"password":password,"name":name,"status":$("#addstatus").val()},function(obj) {
							if (obj.code == 200) {
								$(".showSave").html("保存成功！");
								window.setTimeout(function(){
									window.location.reload();
								},1000);
							}else {
								$(".showSave").html(obj.message);
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							}
						},function(){
							$(".showSave").html("保存失败！");
							window.setTimeout(function(){
								$(".showSave").slideUp(function(){
									$(".showSave").remove();
								});
							},2000);
						});

						//layer.closeAll();
					});
				},cancel: function(index){
					//code
				},
				success: function(layero, index){
					$("#authorityLoginName").keyup(function(){
						var $this = $(this);
						var val = $this.val();
						if(/[^\d]/.test(val)){
							val = val.replace(/[^\d]/,"");
							$this.val(val);
							layer.tips("只允许输入数字",$this);
						}
					});
				}
			});

		});
		//权限用户编辑
		$(".yj_edit").unbind("click").bind("click",function(){
			var $this = $(this);
			var txt = $("#uTemplate").html();
			var userData = $this.data("authority");

			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '480px',
				closeBtn: 0,
				content: txt,
				btn: ['重置密码','保存修改', '关闭'],
				btn1:function(index, layero){
					var confirm_tips = layer.confirm('<div style="padding:2em 1em;text-align:center;">是否确定重置密码？</div>', {
						type:1,
						title:'温馨提示',
						area: '300px',
						btn: ['是','否'] //按钮
					}, function(){
						showSave(layero,"正在重置密码");
						$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
							ztx_public.ajax('/admin/reset',{
								"id":userData.id
							},function(obj) {
								if (obj.code == 200) {
									$(".showSave").html("重置密码成功！");
								}else {
									$(".showSave").html(obj.message);
								}
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);

							},function(){
								$(".showSave").html("重置密码失败！");
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							});
						});
						layer.close(confirm_tips);
					}, function(){

					});
				},
				btn2: function(index, layero){
					var authorityId = $("#authorityId").val();
					var name = $("#name").val();

					showSave(layero,"正在保存");
					$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
						ztx_public.ajax('/admin/update',{"id":userData.id,"role_id":authorityId,"name":name,"status":$(".authority_disabled select").val()},function(obj) {
							if (obj.code == 200) {
								$(".showSave").html("保存成功！");
								window.setTimeout(function(){
									window.location.reload();
								},1000);
							}else {
								$(".showSave").html(obj.message);
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							}
						},function(){
							$(".showSave").html("保存失败！");
							window.setTimeout(function(){
								$(".showSave").slideUp(function(){
									$(".showSave").remove();
								});
							},2000);
						});

						//layer.closeAll();
					});

					return false;
				},
				cancel: function(index){
					//code
				},
				success: function(layero, index){
					$("#name").val(userData.name);
					$(".authority_disabled select").val(userData.status);
					$("#authorityId").val(userData.role_id);
					$(".authority_pass").hide();
					$("#authorityLoginrealName").val(userData.name).attr({"readonly":true}).removeAttr("title").css("border","none");
					$("#authorityLoginName").val(userData.username).attr({"readonly":true}).removeAttr("title").css("border","none");
				}
			});
		});

		//角色新增
		$(document).on("click","#roleAdd",function(){
			var txt = $("#uTemplate").html();

			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '590px',
				closeBtn: 0,
				content: txt,
				btn: ['保存修改', '关闭'],
				yes: function(index, layero){

					var roleName = $.trim($("#roleName").val());
					var roleNote = $.trim($("#roleNote").val());
					var s = [];
					$("#authorityTree1 .checkbox_ui").each(function(i,n){
						if($(n).hasClass("checked") || $(n).hasClass("checkeds")){
							var val = $(n).data("val");
							val&&s.push(val);
						}
					});
					if(!roleName){
						layer.tips("请输入角色名称",$("#roleName"),{time:2000});
						return false;
					}
					if(s.length > 0){
						//console.log(s.join(","));
						showSave(layero,"正在保存");
						$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
							ztx_public.ajax('/admin/role_add',{"role_name":roleName,"remark":roleNote,"role_node":s.join(",")},function(obj) {
								if (obj.code == 200) {
									$(".showSave").html("保存成功！");
									window.setTimeout(function(){
										window.location.reload();
									},1000);
								}else {
									$(".showSave").html(obj.message);
									window.setTimeout(function(){
										$(".showSave").slideUp(function(){
											$(".showSave").remove();
										});
									},2000);
								}
							},function(){
								$(".showSave").html("保存失败！");
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							});

							//layer.closeAll();
						});
					}else{
						layer.tips("请选择权限",$("#authorityAll"),{time:2000});
						return false;
					}

				},cancel: function(index){
					//code

				},
				success: function(layero, index){

				}
			});

		});
		//角色编辑
		$(document).on("click",".role_edit",function(){
			var $this = $(this);
			var txt = $("#uTemplate").html();
			var userData = $this.parent(".cz").data("authority");
			var authorityArr = userData.role_node.split(",");

			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '590px',
				closeBtn: 0,
				content: txt,
				btn: ['保存修改', '关闭'],
				yes: function(index, layero){
					var roleName = $.trim($("#roleName").val());
					var roleNote = $.trim($("#roleNote").val());
					var s = [];
					$("#authorityTree1 .checkbox_ui").each(function(i,n){
						if($(n).hasClass("checked") || $(n).hasClass("checkeds")){
							var val = $(n).data("val");
							val&&s.push(val);
						}
					});

					if(s.length > 0){
						//console.log(s.join(","));
						var show = $("#allUser .checkbox_ui").hasClass("checked")?1:0;
						showSave(layero,"正在保存");
						$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
							ztx_public.ajax('/admin/role_update',{"id":userData.id,"role_name":roleName,"remark":roleNote,"role_node":s.join(",")},function(obj) {
								if (obj.code == 200) {
									$(".showSave").html("保存成功！");
									window.setTimeout(function(){
										window.location.reload();
									},1000);
								}else {
									$(".showSave").html(obj.message);
									window.setTimeout(function(){
										$(".showSave").slideUp(function(){
											$(".showSave").remove();
										});
									},2000);
								}
							},function(){
								$(".showSave").html("保存失败！");
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							});

							//layer.closeAll();
						});
					}else{
						layer.tips("请选择权限",$("#authorityTree_0 .bbit-tree-node-anchor"),{time:2000});
						return false;
					}

				},cancel: function(index){
					//code
				},
				success: function(layero, index){
					$("#roleName").val(userData.role_name);
					$("#roleNote").val(userData.remark);
					for(var i=0;i<authorityArr.length;i++){
						var $checkbox = $(".checkbox_ui[data-val='"+authorityArr[i]+"']");
						if($checkbox.parent().hasClass("authority_1st")){
							if(!$checkbox.hasClass("checked")){
								$checkbox.addClass("checkeds");
							}
						}else if($checkbox.parent().hasClass("authority_2nd")){
							$(".checkbox_ui[data-val='"+authorityArr[i]+"']").trigger("click");
						}

					}
				}
			});
		});

		$(document).on("click",".role_del",function(){
			var id = $(this).data("id");
			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '350px',
				closeBtn: 0,
				content: '</br><h1 align="center">该操作无法恢复，确定要删除吗？</h1></br>',
				btn: ['删除', '取消'],
				yes: function(index, layero){

					showSave(layero,"正在保存");
					$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
						ztx_public.ajax('/admin/role_del',{"id":id},function(obj) {
							if (obj.code == 200) {
								$(".showSave").html("保存成功！");
								window.setTimeout(function(){
									window.location.reload();
								},1000);
							}else {
								$(".showSave").html(obj.message);
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							}
						},function(){
							$(".showSave").html("保存失败！");
							window.setTimeout(function(){
								$(".showSave").slideUp(function(){
									$(".showSave").remove();
								});
							},2000);
						});
					});

				}
			});

		});

		/*--9、账号权限页面的  编辑\新增--*/

		function changerows(){
			//隔行变色
			$("#tb1 tbody tr:nth-child(odd),.tb1 tbody tr:nth-child(odd)").css("background","#ececec");
			//第一列隔行变色样式
			$("#tb1 tbody tr:nth-child(odd) th,.tb1 tbody tr:nth-child(odd) th").css("background","#434e65");
			//第一行隔列变色
			$("#tb1 thead tr th:nth-child(even),.tb1 thead tr th:nth-child(even)").css("background","#434e65");
		}
		changerows();

		//权限模版
		$(document).on("click","#authorityManage",function(){

			var $this = $(this);
			var txt = $("#uTemplate").html();

			layer.open({
				skin:'popup-authority',
				title:'&nbsp;',
				area: '590px',
				closeBtn: 0,
				content: txt,
				btn: ['保存修改', '关闭'],
				yes: function(index, layero){
					var authorityId = $(".authority_type.current").data("authority");
					var s = [];
					$("#authorityTree1 .checkbox_ui").each(function(i,n){
						if($(n).hasClass("checked") || $(n).hasClass("checkeds")){
							var val = $(n).data("val");
							val&&s.push(val);
						}
					});

					if(s.length > 0){
						//console.log(s.join(","));
						var show = $("#allUser .checkbox_ui").hasClass("checked")?1:0;
						showSave(layero,"正在保存");
						$(".showSave").css({"top":"-3px","left":"-3px"}).slideDown(function(){
							ztx_public.ajax('/admin/role_update',{"id":authorityId.id,"role_node":s.join(",")},function(obj) {
								if (obj.code == 200) {
									$(".showSave").html("保存成功！");
									window.setTimeout(function(){
										window.location.reload();
									},1000);
								}else {
									$(".showSave").html(obj.message);
									window.setTimeout(function(){
										$(".showSave").slideUp(function(){
											$(".showSave").remove();
										});
									},2000);
								}
							},function(){
								$(".showSave").html("保存失败！");
								window.setTimeout(function(){
									$(".showSave").slideUp(function(){
										$(".showSave").remove();
									});
								},2000);
							});

							//layer.closeAll();
						});
					}else{
						layer.tips("请选择权限",$("#authorityTree_0 .bbit-tree-node-anchor"),{time:2000});
						return false;
					}

				},cancel: function(index){
					//code
				},
				success: function(layero, index){
					$(".authority_box header .authority_type_box").show().siblings().hide();
					$(".authority_type").click(function(){
						var $this = $(this);
						var authorityId = $this.data("authority");
						var authorityArr = authorityId.role_node.split(",");
						$this.addClass("current").siblings().removeClass("current");
						$(".checkbox_ui").removeClass("checkeds checked");
						for(var i=0;i<authorityArr.length;i++){
							var $checkbox = $(".checkbox_ui[data-val='"+authorityArr[i]+"']");
							if($checkbox.parent().hasClass("authority_1st")){
								if(!$checkbox.hasClass("checked")){
									$checkbox.addClass("checkeds");
								}
							}else if($checkbox.parent().hasClass("authority_2nd")){
								$(".checkbox_ui[data-val='"+authorityArr[i]+"']").trigger("click");
							}
						}
					}).eq(0).trigger("click");

				}
			});
		});

		//点击权限列表选择框
		$(document).on("click","#authorityTree1 label",function(){
			var $this = $(this);
			if($this[0].id == "authorityAll"){
				var $checkbox = $this.children(".checkbox_ui");
				if($checkbox.hasClass("checked")){
					$checkbox.removeClass("checked");
					$("#authorityTree1 tbody .checkbox_ui").removeClass("checkeds checked");
				}else{
					$checkbox.removeClass("checkeds").addClass("checked");
					$("#authorityTree1 tbody .checkbox_ui").removeClass("checkeds").addClass("checked");
				}
			}else if($this[0].id == "allUser"){
				var $checkbox = $this.children(".checkbox_ui");
				if($checkbox.hasClass("checked")){
					$checkbox.removeClass("checked");
				}else{
					$checkbox.addClass("checked");
				}
			}else if($this.hasClass("authority_1st")){
				var $checkbox = $this.children(".checkbox_ui");
				var $childCheckbox = $this.parent("td").next("td").find(".checkbox_ui");
				if($checkbox.hasClass("checked")){
					var parentType = false;
					$checkbox.removeClass("checked");
					$childCheckbox.removeClass("checkeds checked");
					$(".authority_1st").each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if($checkbox.hasClass("checkeds")||$checkbox.hasClass("checked")){
							parentType = true;
							return false;
						}
					});
					if(parentType){
						$("#authorityAll .checkbox_ui").removeClass("checked").addClass("checkeds");
					}else{
						$("#authorityAll .checkbox_ui").removeClass("checked checkeds");
					}
				}else{
					var parentType = false;
					$checkbox.removeClass("checkeds").addClass("checked");
					$childCheckbox.removeClass("checkeds").addClass("checked");
					$(".authority_1st").each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if(!$checkbox.hasClass("checked")){
							parentType = true;
							return false;
						}
					});
					if(parentType){
						$("#authorityAll .checkbox_ui").removeClass("checked").addClass("checkeds");
					}else{
						$("#authorityAll .checkbox_ui").removeClass("checkeds").addClass("checked");
					}
				}
			}else if($this.hasClass("authority_2nd")){
				var parentType = false,parentsType = false;
				var $checkbox = $this.children(".checkbox_ui");
				var $siblings = $this.siblings();
				var $parentCheckbox = $this.parent("td").prev().find(".checkbox_ui");
				if($checkbox.hasClass("checked")){
					$checkbox.removeClass("checked");
					$siblings.each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if($checkbox.hasClass("checked")){
							parentType = true;
							return false;
						}
					});
					if(parentType){
						$parentCheckbox.removeClass("checked").addClass("checkeds");
					}else{
						$parentCheckbox.removeClass("checkeds checked");
					}

					$(".authority_1st").each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if($checkbox.hasClass("checkeds")||$checkbox.hasClass("checked")){
							parentsType = true;
							return false;
						}
					});
					if(parentsType){
						$("#authorityAll .checkbox_ui").removeClass("checked").addClass("checkeds");
					}else{
						$("#authorityAll .checkbox_ui").removeClass("checked checkeds");
					}

				}else{
					$checkbox.addClass("checked");
					$siblings.each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if(!$checkbox.hasClass("checked")){
							parentType = true;
							return false;
						}
					});
					if(parentType){
						$parentCheckbox.removeClass("checked").addClass("checkeds");
					}else{
						$parentCheckbox.removeClass("checkeds").addClass("checked");
					}

					$(".authority_1st").each(function(i,n){
						var $checkbox = $(n).children(".checkbox_ui");
						if(!$checkbox.hasClass("checked")){
							parentsType = true;
							return false;
						}
					});
					if(parentsType){
						$("#authorityAll .checkbox_ui").removeClass("checked").addClass("checkeds");
					}else{
						$("#authorityAll .checkbox_ui").removeClass("checkeds").addClass("checked");
					}

				}
			}
		});
	})();
	(function(){
		$(".menu li").unbind("click").bind("click",function(){
			$(".menu li").removeClass("current");
			$(this).addClass("current");
		});
	})();

	/*--个人信息--*/
	$(".dingding_userinfo_menu li").click(function(){
		var $this = $(this);
		var index = $this.index();
		$this.addClass("current").siblings("li").removeClass("current");
		$(".dingding_userinfo_data").hide().eq(index).show();
		return false;
	});
	$(".dingding_changepass .submit").click(function(){
		var oldpwd =$(".dingding_changepass .oldpwd").val();
		var newpwd =$(".dingding_changepass .newpwd").val();
		var newpwd2 =$(".dingding_changepass .newpwd2").val();
		if(!newpwd){
			layer.tips('该字段不能为空', '.newpwd');
			return false;
		}
		if(!newpwd2){
			layer.tips('该字段不能为空', '.newpwd2');
			return false;
		}
		if(newpwd != newpwd2){
			layer.tips('两次密码不一致', '.newpwd2');
			return false;
		}
		layer.open({
			skin:'popup-class',
			title:'&nbsp;',
			area: '380px',
			closeBtn: 0,
			content: "<h1 align='center'>确定要修改密码吗？</h1>",
			btn: ['确定', '取消'],
			yes: function(index, layero){
				ztx_public.ajax('/admin/password_update',{"old_password":oldpwd,"new_password":newpwd},function(obj) {
					if (obj.code == 200) {
						layer.alert("操作成功！", {
							skin: 'layui-layer-lan' //样式类名
							,closeBtn: 0
						});
						window.setTimeout(function(){
							window.location.reload();
						},1000);
					}else {
						layer.alert(obj.message, {
							skin: 'layui-layer-lan' //样式类名
							,closeBtn: 0
						});
					}
				},function(){
					window.setTimeout(function(){
						layer.alert('操作失败！', {
							skin: 'layui-layer-lan' //样式类名
							,closeBtn: 0
						});
					},2000);
				});
			},
			cancel: function(index){
				//code
			}
		});
	});

	$(".menu").hover(function(){
		$(this).addClass("scrollbar");
	},function(){
		$(this).removeClass("scrollbar");
	});

});
