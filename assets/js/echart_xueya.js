//模块化定制引入
// 路径配置

require.config({
	baseUrl: "/assets/js",
	paths: {
		'jquery':'jquery-1.11.3.min',
		'echarts': 'echarts.amd',
		'echarts/chart/line':'echarts.amd',
		'echarts/chart/bar':'echarts.amd'
	},
	shim: {
		'ztx_public':{
            deps: ['jquery'],
            exports: 'ztx_public'
        }
	}
});

// 使用
require([
	'jquery',
	'ztx_public',
	'echarts',
	'echarts/chart/bar',
	'echarts/chart/line'
],function (jquery,ztx_public,ec) {
    var dataZoom = {};
    var URLParams = {} ;
    var aParams = decodeURIComponent(document.location.search.substr(1));
    if(aParams.indexOf("&") > -1){
        aParams = aParams.split('&');
        for (i=0 ; i < aParams.length ; i++) {
            var aParam = aParams[i].split('=') ;
            URLParams[aParam[0]] = aParam[1] ;
        }
    }else{
        var aParam = aParams.split('=') ;
        URLParams[aParam[0]] = aParam[1] ;
    }
    if(URLParams.天数&&URLParams.天数 == "所有"){
        dataZoom = {
            show: true,
            start : 10,
            end : 90
        }
    }

    $(".tj_history_menu a").click(function(){
		var $this = $(this),elem,query = {};
		var index = $this.parent("li").index();
		var $echart_elem = $(".echart_data").eq(index);
		$this.parent("li").addClass("current").siblings().removeClass("current");

        query.最近体检时间 = URLParams.最近体检时间;
        query.天数 = URLParams.天数;
        query.用户编号 = URLParams.用户编号;

		if(index == "0"){
            query.类型 = "血压";
			if(!!$("#szy").length){
				$(".echart_data").hide().eq(index).fadeIn();
				return false;
			}else{
				$(".echart_data").hide().eq(index).show().append($("#loading").show());
				elem = '<div id="ssy" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="szy" class="echart_diy lfloat" style="height:400px;width:620px;">';
			}
		}else if(index == "1"){
            query.类型 = "血糖";
			if(!!$("#t1").length){
				$(".echart_data").hide().eq(index).fadeIn();
				return false;
			}else{
				$(".echart_data").hide().eq(index).show().append($("#loading").show());
                //elem = '<div id="t1" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="t2" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="t3" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="t4" class="echart_diy lfloat" style="height:400px;width:620px;"></div>';
                elem = '<div id="t1" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="t2" class="echart_diy lfloat" style="height:400px;width:620px;"></div>';
			}
		}else if(index == "2"){
            query.类型 = "血氧";
			if(!!$("#dmx").length){
				$(".echart_data").hide().eq(index).fadeIn();
				return false;
			}else{
				$(".echart_data").hide().eq(index).show().append($("#loading").show());
				elem = '<div id="dmx" class="echart_diy lfloat" style="height:400px;width:620px;"></div><div id="jmx" class="echart_diy lfloat" style="height:400px;width:620px;"></div>';
			}
		}
		
		ztx_public.ajax('/体检/用户数据',query,function(obj){
			$("#loading").hide();
			$echart_elem.append(elem);
			index==0?echart_xueya(obj):index==1?echart_xuetang(obj):echart_xueyang(obj);
		},function(obj){
			alert("没有体检数据！");
            $("#loading").hide();
		},'get');
		return false;
	}).eq(0).trigger("click");
	
	//血压
	function echart_xueya(obj){
		// 基于准备好的dom，初始化echarts图表
		var szy = ec.init(document.getElementById('szy')); 
		var ssy = ec.init(document.getElementById('ssy')); 
		
		var ssy_option = {
			title : {
				text: '收缩压',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['收缩压']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.ssy.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					axisLabel : {
						formatter: '{value} mmHg'
					},
					data:[0,20,40,60,80,100,120,140,160,180,200]
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[130],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[90],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'收缩压',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.ssy.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
			
		var szy_option = {
				title : {
					text: '舒张压',
					subtext: ''
				},
				tooltip : {
					trigger: 'axis'
				},
                dataZoom: dataZoom,
				legend: {
					data:['舒张压']
				},
				toolbox: {
					show : true,
					feature : {
						mark : {show: false},
						dataView : {show: true, readOnly: true},
						magicType : {show: true, type: ['line', 'bar']},
						restore : {show: true},
						saveAsImage : {show: true}
					}
				},
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data:obj.szy.time
						//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
					}
				],
				yAxis : [
					{
						type : 'value',
						axisLabel : {
							formatter: '{value} mmHg'
						},
						data:[0,20,40,60,80,100,120,140]
					}
				],
				series : [
					{
						name:'最高参考值',
						tooltip : {
							trigger: 'item',
							formatter: function (params,ticket,callback) {
								var res = params.seriesName+'：<br/>'+params.value;
								return res;
							}
						},
						type:'line',
						data:[85],
						markLine : {
							data : [
								{type : 'max', name: '上限'}
							]
						}
					},
					{
						name:'最低参考值',
						tooltip : {
							trigger: 'item',
							formatter: function (params,ticket,callback) {
								var res = params.seriesName+'：<br/>'+params.value;
								return res;
							}
						},
						type:'line',
						data:[60],
						markLine : {
							data : [
								{type : 'min', name: '下限'}
							]
						}
					},
					{
						name:'舒张压',
						symbol:'emptyCircle',
						symbolSize:4,
						itemStyle: {
							normal: {
								color:"#0173b2",
								lineStyle: {
									color: '#009df4'
								},
								borderColor:'#0173b2'
							}
						},
						tooltip : {
							formatter: function (params,ticket,callback) {
								var res =  params[0].name;
								for (var i = 0, l = params.length; i < l; i++) {
									res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
								}
								return res;
							}
						},
						type:'line',
						data:obj.szy.value,
						markPoint : {
							//symbol:'diamond',
							data : [
								{type : 'max', name: '最大值'},
								{type : 'min', name: '最小值'}
							]
						}
					}
				]
			};
								
	
		// 为echarts对象加载数据 
		ssy.setOption(ssy_option);
		szy.setOption(szy_option); 
		window.setTimeout(function(){
			ssy.dom.style.height = "auto";
			szy.dom.style.height = "auto";
			ssy.dom.style.width = "auto";
			szy.dom.style.width = "auto";
		},0);
	}
	
	//血糖
	function echart_xuetang(obj){
		// 基于准备好的dom，初始化echarts图表
		var t1 = ec.init(document.getElementById('t1')); 
		var t2 = ec.init(document.getElementById('t2')); 
		//var t3 = ec.init(document.getElementById('t3'));
		//var t4 = ec.init(document.getElementById('t4'));
		
		var t1_option = {
			title : {
				text: '空腹血糖',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['空腹血糖']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.t1.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					axisLabel : {
						formatter: '{value} mmol/L'
					},
					data:[0,1,2,3,4,5,6,7,8,9,10]
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[5.9],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[3.9],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'空腹血糖',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.t1.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
		
		var t2_option = {
			title : {
				text: '糖化血红蛋白',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['糖化血红蛋白']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.t2.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					axisLabel : {
						formatter: '{value} mmol/L'
					},
					data:[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[7],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[4],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'糖化血红蛋白',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.t2.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
		
		/*
		var t3_option = {
			title : {
				text: '糖化血红蛋白A1c',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['糖化血红蛋白A1c']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.t3.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					axisLabel : {
						formatter: '{value} %'
					},
					data:[0,20,40,60,80,100,120,140,160,180,200]
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[160],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[100],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'糖化血红蛋白A1c',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.t3.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
		
		var t4_option = {
			title : {
				text: '糖化血红蛋白A1',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['糖化血红蛋白A1']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.t4.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					axisLabel : {
						formatter: '{value} %'
					},
					data:[0,20,40,60,80,100,120,140,160,180,200]
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[160],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[100],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'糖化血红蛋白A1',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.t4.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
		*/
	
		// 为echarts对象加载数据 
		t1.setOption(t1_option); 
		t2.setOption(t2_option); 
		//t3.setOption(t3_option);
		//t4.setOption(t4_option);
		window.setTimeout(function(){
			t1.dom.style.height = "auto";
			t2.dom.style.height = "auto";
			//t3.dom.style.height = "auto";
			//t4.dom.style.height = "auto";
			t1.dom.style.width = "auto";
			t2.dom.style.width = "auto";
			//t3.dom.style.width = "auto";
			//t4.dom.style.width = "auto";
		},0);
	}
	
	//血氧
	function echart_xueyang(obj){
		// 基于准备好的dom，初始化echarts图表
		var dmx = ec.init(document.getElementById('dmx')); 
		var jmx = ec.init(document.getElementById('jmx')); 
		
		var dmx_option = {
			title : {
				text: '动脉血',
				subtext: ''
			},
			tooltip : {
				trigger: 'axis'
			},
            dataZoom: dataZoom,
			legend: {
				data:['动脉血']
			},
			toolbox: {
				show : true,
				feature : {
					mark : {show: false},
					dataView : {show: true, readOnly: true},
					magicType : {show: true, type: ['line', 'bar']},
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data:obj.dmx.time
					//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
				}
			],
			yAxis : [
				{
					type : 'value',
					scale:true,
					boundaryGap: [0.01, 0.01],
					axisLabel : {
						formatter: '{value} %'
					}
				}
			],
			series : [
				{
					name:'最高参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[99.5],
					markLine : {
						data : [
							{type : 'max', name: '上限'}
						]
					}
				},
				{
					name:'最低参考值',
					tooltip : {
						trigger: 'item',
						formatter: function (params,ticket,callback) {
							var res = params.seriesName+'：<br/>'+params.value;
							return res;
						}
					},
					type:'line',
					data:[94],
					markLine : {
						data : [
							{type : 'min', name: '下限'}
						]
					}
				},
				{
					name:'动脉血',
					symbol:'emptyCircle',
					symbolSize:4,
					itemStyle: {
						normal: {
							color:"orange",
							lineStyle: {
								color: '#ffcd35'
							},
							borderColor:'orange'
						}
					},
					tooltip : {
						formatter: function (params,ticket,callback) {
							var res =  params[0].name;
							for (var i = 0, l = params.length; i < l; i++) {
								res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
							}
							return res;
						}
					},
					type:'line',
					data:obj.dmx.value,
					markPoint : {
						//symbol:'diamond',
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					}
				}
			]
		};
			
		var jmx_option = {
				title : {
					text: '静脉血',
					subtext: ''
				},
				tooltip : {
					trigger: 'axis'
				},
                dataZoom: dataZoom,
				legend: {
					data:['静脉血']
				},
				toolbox: {
					show : true,
					feature : {
						mark : {show: false},
						dataView : {show: true, readOnly: true},
						magicType : {show: true, type: ['line', 'bar']},
						restore : {show: true},
						saveAsImage : {show: true}
					}
				},
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data:obj.jmx.time
						//data : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]
					}
				],
				yAxis : [
					{
						type : 'value',
						scale:true,
						boundaryGap: [0.01, 0.01],
						axisLabel : {
							formatter: '{value} %'
						}
					}
				],
				series : [
					{
						name:'最高参考值',
						tooltip : {
							trigger: 'item',
							formatter: function (params,ticket,callback) {
								var res = params.seriesName+'：<br/>'+params.value;
								return res;
							}
						},
						type:'line',
						data:[80],
						markLine : {
							data : [
								{type : 'max', name: '上限'}
							]
						}
					},
					{
						name:'最低参考值',
						tooltip : {
							trigger: 'item',
							formatter: function (params,ticket,callback) {
								var res = params.seriesName+'：<br/>'+params.value;
								return res;
							}
						},
						type:'line',
						data:[65],
						markLine : {
							data : [
								{type : 'min', name: '下限'}
							]
						}
					},
					{
						name:'静脉血',
						symbol:'emptyCircle',
						symbolSize:4,
						itemStyle: {
							normal: {
								color:"#0173b2",
								lineStyle: {
									color: '#009df4'
								},
								borderColor:'#0173b2'
							}
						},
						tooltip : {
							formatter: function (params,ticket,callback) {
								var res =  params[0].name;
								for (var i = 0, l = params.length; i < l; i++) {
									res += '<br/>' + params[i].seriesName + ' : ' + params[i].value;
								}
								return res;
							}
						},
						type:'line',
						data:obj.jmx.value,
						markPoint : {
							//symbol:'diamond',
							data : [
								{type : 'max', name: '最大值'},
								{type : 'min', name: '最小值'}
							]
						}
					}
				]
			};
								
	
		// 为echarts对象加载数据 
		dmx.setOption(dmx_option); 
		jmx.setOption(jmx_option); 
		window.setTimeout(function(){
			dmx.dom.style.height = "auto";
			jmx.dom.style.height = "auto";
			dmx.dom.style.width = "auto";
			jmx.dom.style.width = "auto";
		},0);
	}
	
	
});