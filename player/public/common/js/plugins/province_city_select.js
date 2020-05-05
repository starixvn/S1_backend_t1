$(function(){
	var provinceCity = {
		"北京" : ["北京"],
		"上海" : ["上海"],
		"天津" : ["天津"],
		"重庆" : ["重庆"],
		"香港" : ["香港"],
		"澳门" : ["澳门"],
		"黑龙江" : ["哈尔滨", "齐齐哈尔", "牡丹江", "大庆", "伊春", "双鸭山", "鹤岗", "鸡西", "佳木斯", "七台河", "黑河", "绥化", "大兴安岭"],
		"吉林" : ["长春", "延吉", "吉林", "白山", "白城", "四平", "松原", "辽源", "大安", "通化"],
		"辽宁" : ["沈阳", "大连", "葫芦岛", "盘锦", "本溪", "抚顺", "铁岭", "辽阳", "营口", "阜新", "朝阳", "锦州", "丹东", "鞍山", "开原市"],
		"内蒙古" : ["呼和浩特", "包头", "乌海", "赤峰", "通辽", "鄂尔多斯", "呼伦贝尔", "巴彦淖尔", "乌兰察布", "兴安盟", "锡林郭勒盟", "阿拉善盟"],
		"河北" : ["石家庄", "唐山", "张家口", "廊坊", "邢台", "邯郸", "沧州", "衡水", "承德", "保定", "秦皇岛"],
		"河南" : ["郑州", "开封", "洛阳", "平顶山", "焦作", "鹤壁", "新乡", "安阳", "濮阳", "许昌", "漯河", "三门峡", "南阳", "商丘", "信阳", "周口", "驻马店"],
		"山东" : ["济南", "青岛", "淄博", "威海", "曲阜", "临沂", "烟台", "枣庄", "聊城", "济宁", "菏泽", "泰安", "日照", "东营", "德州", "滨州", "莱芜", "潍坊"],
		"山西" : ["太原", "阳泉", "晋城", "晋中", "临汾", "运城", "长治", "朔州", "忻州", "大同", "吕梁"],
		"江苏" : ["南京", "苏州", "昆山", "南通", "太仓", "吴县", "徐州", "宜兴", "镇江", "淮安", "常熟", "盐城", "无锡", "连云港", "扬州", "常州", "宿迁", "泰州", "靖江"],
		"安徽" : ["合肥", "巢湖", "蚌埠", "安庆", "六安", "滁州", "马鞍山", "阜阳", "宣城", "铜陵", "淮北", "芜湖", "毫州", "宿州", "淮南", "池州", "黃山"],
		"陕西" : ["西安", "韩城", "安康", "汉中", "宝鸡", "咸阳", "榆林", "渭南", "商洛", "铜川", "延安"],
		"宁夏" : ["银川", "固原", "中卫", "石嘴山", "吴忠"],
		"甘肃" : ["兰州", "天水", "庆阳", "武威", "酒泉", "张掖", "白银", "定西", "平凉", "临夏", "金昌", "甘南", "陇南","嘉峪关"],
		"青海" : ["黄南", "西宁", "玉树", "海西", "海南", "海北", "海东", "果洛"],
		"湖北" : ["黄石", "黄冈", "随州", "鄂州", "襄阳", "荆门", "荆州", "神农架", "武汉", "恩施", "宜昌", "孝感", "咸宁", "十堰", "仙桃"],
		"湖南" : ["韶山", "长沙", "郴州", "邵阳", "衡阳", "益阳", "湘潭", "永州", "株洲", "怀化", "张家界", "常德", "岳阳", "娄底", "吉首"],
		"浙江" : ["金华", "衢州", "舟山", "绍兴", "湖州", "温州", "杭州", "宁波", "嘉兴", "台州", "丽水"],
		"江西" : ["鹰潭", "赣州", "萍乡", "景德镇", "新余", "抚州", "宜春", "吉安", "南昌", "九江", "上饶"],
		"福建" : ["龙岩", "莆田", "福州", "漳州", "泉州", "宁德", "厦门", "南平", "三明"],
		"贵州" : ["铜仁", "都匀", "遵义", "赤水", "贵阳", "毕节", "安顺", "凯里", "六盘水"],
		"四川" : ["雅安", "阿坝", "遂宁", "达州", "资阳", "自贡", "绵阳", "眉山", "甘孜州", "泸州", "攀枝花", "成都", "德阳", "广安", "广元", "巴中", "宜宾", "南充", "凉山", "内江", "乐山"],
		"广东" : ["韶关", "阳江", "茂名", "肇庆", "珠海", "潮州", "湛江", "清远", "深圳", "河源", "江门", "汕尾", "汕头", "梅州", "揭阳", "惠州", "德庆", "广州", "佛山", "云浮", "中山", "东莞"],
		"广西" : ["阳朔", "防城港", "钦州", "贺州", "贵港", "百色", "玉林", "河池", "梧州", "桂林", "桂平", "柳州", "来宾", "崇左", "南宁", "北海"],
		"云南" : ["红河", "玉溪", "楚雄", "曲靖", "昭通", "昆明", "文山", "思茅", "怒江", "德宏", "大理", "保山", "丽江", "临沧", "香格里拉市", "西双版纳"],
		"海南" : ["通什", "琼山", "海口", "文昌", "儋州", "三亚"],
		"新疆" : ["阿勒泰", "阿克苏", "石河子", "昌吉", "库尔勒", "塔城", "喀什", "哈密", "和田", "吐鲁番", "克拉玛依", "伊宁", "乌鲁木齐"],
		"西藏" : ["阿里", "林芝", "昌都", "日喀则", "拉萨", "山南", "那曲"],
		"台湾" : ["高雄", "台北"]
	};

    function ProvinceCity(){
        this.list = provinceCity;
    };

    ProvinceCity.prototype.bind = function(ProvinceClass, CityClass){
        ProvinceClass = (ProvinceClass === undefined || ProvinceClass.length <= 0) ? 'province' : ProvinceClass;
        CityClass = (CityClass === undefined || CityClass.length <= 0) ? 'city' : CityClass;

        var default_select = $("select." + ProvinceClass).data('value');

        if($("select." + ProvinceClass).length <= 0){
        	console.log('ProvinceCity related element does not exist: ' + "select." + ProvinceClass);
        	return;
		}

        $.each(provinceCity, function(name, value){
            var province = name;

            if(province.length <= 0){
            	return;
			}

            var option = $('<option></option>').val(province).html(province);
            if(default_select === province){
                option.attr('selected', 'selected').prop('selected', true);
            }
            $("select." + ProvinceClass).append(option);
        });

        $("select." + ProvinceClass).change(function(){
        	var select_val = $("select." + ProvinceClass).val();
            $("select." + CityClass + " option").each(function(){
            	if($(this).attr('value') != ''){
            		$(this).remove();
            	}
            });

        	if(!provinceCity.hasOwnProperty(select_val)){
        		return;
			}

            var cityArray = provinceCity[select_val];
            $.each(cityArray, function(index, value){
                var option = $('<option></option>').val(value).html(value);
                if($("select." + CityClass).data('value') == value){
                    option.attr('selected', 'selected').prop('selected', true);
                }
                $("select." + CityClass).append(option);
            });

            $("select." + CityClass).trigger('change');
        });

        $("select." + ProvinceClass).trigger('change');
    };

    window.ProvinceCity = new ProvinceCity();
});