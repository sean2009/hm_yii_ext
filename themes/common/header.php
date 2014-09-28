<script type="text/javascript" src="<?php echo DATAJS_DOMAIN1;?>/hmcms/hmcommon.js"></script>
<div class="m_header">
	<!--toolbar start-->
	<div class="topAd_mode"></div>
	<div class="m_toolbarWrap" id="m_toolbarWrap">
		<div class="m_toolbar">
			<div class="quickArea">
				<a href="javascript:addfavorite();" class="ico m_fav" title="收藏本站">收藏本站</a><span class="line">|</span>
				<b class="ico m_ensure" title="品质保障">品质保障</b>
				<b class="ico m_sale" title="无忧售后">无忧售后</b>
			</div>
			<ul class="m_toolbar_nav" id="Js_m_toolbar_nav">
				<li class="item m_account">
					<div class="m_menu">
						<a href="<?php echo DOMAIN_USER;?>user.php" class="m_menu_hd m_login_url">我的星易家<b class="ico ico_drop"></b></a>
						<ul class="m_menu_bd">
							<li>
								<a href="<?php echo DOMAIN_USER;?>index.php?con=MallOrder&act=Index" class="m_login_url">已购买商品</a>
							</li>
							<li>
								<a href="<?php echo DOMAIN_USER;?>index.php?con=goodsCollect&act=index" class="m_login_url">我的收藏</a>
							</li>
						</ul>
					</div>
				</li>
				<li class="item m_myOrder">
					<a href="<?php echo DOMAIN_USER;?>index.php?con=MallOrder&act=Index" class="m_login_url">我的订单</a>
				</li>
				<li class="item m_service">
					<div class="m_menu">
						<a href="<?php echo DOMAIN_WWW;?>service.html" class="m_menu_hd">服务中心<b class="ico ico_drop"></b></a>
						<ul class="m_menu_bd">
							<li>
								<a href="<?php echo DOMAIN_WWW;?>help/guide.html">帮助中心</a>
							</li>
							<li>
								<a href="<?php echo DOMAIN_USER;?>index.php?con=MallRefund&act=list" class="m_login_url">售后管理</a>
							</li>
							<li>
								<a class="serviceIM" href="javascript:;">在线客服</a>
							</li>
							<li>
								<a href="<?php echo DOMAIN_WWW;?>help/contact_service.html#area02">邮件客服</a>
							</li>
							<li>
								<a href="<?php echo DOMAIN_WWW;?>help/contact_service.html#area03">电话客服</a>
							</li>
							<li>
								<a href="<?php echo DOMAIN_WWW;?>help/contact_service.html#area05">微信客服</a>
							</li>
						</ul>
					</div>
				</li>
				<li class="item m_siteNav">
					<div class="m_menu">
						<a href="<?php echo DOMAIN_WWW;?>sitemap.html" class="m_menu_hd">网站导航<b class="ico ico_drop"></b></a>
						<div class="m_menu_bd">
							<dl>
								<dt>
									购物
								</dt>
								<dd>
									<a href="<?php echo DOMAIN_WWW;?>store/index-14639.html">折扣店</a>
									<a href="<?php echo DOMAIN_WWW;?>jiaju/">家具城</a>
									<a href="<?php echo DOMAIN_WWW;?>jiancai/">建材城</a>
									<a href="<?php echo DOMAIN_WWW;?>ju/">家居城</a>
									<a href="<?php echo DOMAIN_TUAN;?>">现场团购</a>
								</dd>
							</dl>
							<dl>
								<dt>
									装修服务
								</dt>
								<dd>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/company.html" >找装修公司</a>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/article.html">装修攻略</a>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/designer.html">找设计师</a>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/">装修汇</a>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/tu/">装修效果图</a>
									<a href="<?php echo DOMAIN_WWW;?>zhuangxiu/guanjia/">装客管家</a>
								</dd>
							</dl>
							<dl>
								<dt>
									商家指南
								</dt>
								<dd>
									<a href="<?php echo DOMAIN_MAI;?>index.php?con=index&act=login">商户中心</a>
								</dd>
							</dl>
							<dl>
								<dt>
									客户服务
								</dt>
								<dd>
									<a href="<?php echo DOMAIN_WWW;?>service.html">服务中心</a>
								</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="item m_tel">
					<div class="m_menu">
						<strong class="ico ico_tel m_menu_hd">在线咨询:4000-213-213<b class="ico ico_drop"></b></strong>
						<div class="m_menu_bd">
							<p>
								<span class="tel">咨询电话</span><span class="num">021-61819659
									<br>
									转 8051/8052/8053</span><a href="javascript:;" class="tel telIcon servicePreIM">购买咨询</a>
							</p>
							<p>
								<span class="tel">售后电话</span><span class="num num2">4000-213-213</span><a href="javascript:;" class="tel telIcon serviceLine_next">售后服务</a>
							</p>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		void function(window, $) {
			var data = hmcommon.child.topbanner.data[0];
			if (data && data.image_url) {
				$('.topAd_mode').html('<div class="topAd_main"><a name="__AD_DT-1" target="_blank" title="' + data.title + '" href="' + data.cms_url + '" target="_blank"  style="background:url(' + data.image_url + ') no-repeat center top"></a></div>');
			}
		}(this, jQuery);
	</script>
	<!--toolbar end-->
	<!--search start-->
	<div class="m_headWrap">
		<div class="m_head">
			<div class="m_logo">
				<div class="logo_bg">
					<a target="_blank" title="红星美凯龙星易家" href="http://www.mmall.com/"><img src="<?php echo CSS_DOMAIN1;?>/images/logo/mmall_logo.png"></a>
				</div>
			</div>
			<!--{if $tg_show_city_list eq 'yes'}-->
		   <div class="location" id="this_city" style="display:none;"><b class="">{$tg_nowCityName}</b>
		   		<!--{if $tg_display_change_city eq 'yes'}-->
		   		<a href="{$domain_tuan}cityList.html" class="selectCity">[切换城市]</a>
		        <!--{/if}-->
		   </div>
		   <div class="marketArea" id="this_shop_id" style="display:none;">
		   		<b title="{$tg_nowShopName}">{$tg_nowShopName}</b>
		   		<a href="javascript:onShowShop();" class="selectMarket">[切换商场]</a>
				<ul class="market_list hidden">
		        	<!--{foreach from=$tg_shop_list item=tg_shop}-->
					<li><a href="javascript:onSelectShop('{$tg_city_info.pingyin}','{$tg_shop.id}');" title="{$tg_shop.name}">{$tg_shop.name|truncate:8}</a></li>
		            <!--{/foreach}-->
				</ul>
			</div>
		    <script type="text/javascript">
		    	<!--{if $tg_show_city_list neq 'yes' || $tg_display_change_city neq 'yes'}-->//<!--{/if}-->$('#this_city').show();
		    	<!--{if !$tg_shop_list}-->//<!--{/if}-->$('#this_shop_id').show();
			  function onShowShop(){
				  $('.market_list').toggle('hidden');
			  }
			  function onSelectShop(pingyin,shopId){
				  var str = 'original_shop_id' + "=" + escape(shopId);
				  var date = new Date();
				  var ms = 30*24*3600*1000;//30天过期
				  date.setTime(date.getTime() + ms);
				  str += "; expires=" + date.toGMTString();
				  str +=';path=/'+';domain={$cookie_domian}';
				  document.cookie = str;
				  window.location.href = '{$domain_tuan}'+pingyin+'/'+shopId+'/';
			  }
			 </script>
		   <!--{/if}-->
			<div class="m_search">
				<ul class="m_search_tabs">
					<li class="selected">
						<a title="商品" href="javascript:;">商品</a>
					</li>
					<li>
						<a title="店铺" href="javascript:;">店铺</a>
					</li>
					<li>
						<a title="效果图" href="javascript:;">效果图</a>
					</li>
					<li>
						<a title="找装修" href="javascript:;">找装修</a>
					</li>
				</ul>
				<div class="m_search_con">
					<form id="top_search" action="<?php echo DOMAIN_SEARCH;?>search.php" method="get" name="top_search" target="_blank" autocomplete="off">
						<label id="def_search_k" for="keyword" class="search_label"></label>
						<input id="keyword" type="text" name="keyword" class="keyword" x-webkit-speech />
						<input type="hidden" value="encode" name="action" />
						<input name="DB-1" id="sub_search" type="button" class="search_btn" value="搜索" title="搜索">
					</form>
				</div>
				<p class="hotKey"></p>
			</div>
			<div class="m_myCart">
				<a name="DB-2" href="<?php echo DOMAIN_CART;?>?from=top" title="去购物车结算">去购物车结算</a><i style="top:0;opacity:0;">0</i>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		void function(window, $) {
			var data = hmcommon.child.search.child;
			var search_html = [];
			$.each(data.fast.data, function(i) {
				search_html.push((i == 0 ? '' : '|') + '<a target="_blank" href="' + this.cms_url + '">' + this.brief + '</a>');
			});
			$('.m_head .hotKey').html(search_html.join(''));
		}(this, jQuery);
	</script>
	<!--search end-->
	<!--navbar start-->
	<div class="m_mainNavWrap">
		<div class="m_mainNav">
			<div class="m_category" id="Js_m_category">
				<h2 class="m_category_hd">全部商品分类<i class="ico_arrow"></i></h2>
				<div class="m_category_bd">
					<ul class="m_category_bd_con">

					</ul>
				</div>
			</div>
			<ul class="m_mainNav-main" id="Js_m_mainNav-main"></ul>
			<ul class="m_mainNav-others"></ul>
		</div>
	</div>
	<script type="text/javascript">
void function(window, $){
var data = hmcommon.child;
var navbar = [];
$.each(data.navbar.data, function(i){
navbar.push('<li '+(i==0 ? 'class="except"' : '')+'><a href="'+this.cms_url+'" title="'+this.brief+'"><span>'+this.brief+'</span></a>'+(this.is_light ? '<i class="mod_nav_ico mod_nav_xianshi"></i>' : '')+'</li>');
});
$('#Js_m_mainNav-main').html(navbar.join(''));
$('#Js_m_mainNav-main > li').last().addClass('last');
var bbs = data.navbar.child.bbs.data[0];
bbs && $('.m_mainNav-others').html('<li><a href="'+bbs.cms_url+'" title="'+bbs.brief+'" target="_blank">'+bbs.brief+'</a></li>');

var category = [];
$.each(data.category.child, function(k){
var item_hd = '';
switch(k){
case 'furniture':
item_hd = '<a target="_blank" href="'+window.domainCfg.www+'jiaju/" title="'+this.name+'" class="ico_title">'+this.name+'</a>';
break;
case 'material':
item_hd = '<a target="_blank" href="'+window.domainCfg.www+'jiancai/" title="'+this.name+'" class="ico_title">'+this.name+'</a>';
break;
case 'house':
item_hd = '<a target="_blank" href="'+window.domainCfg.www+'ju/" title="'+this.name+'" class="ico_title">'+this.name+'</a>';
break;
default:
item_hd = '<span class="ico_title">'+this.name+'</span>';
}

category.push(['<li id="m_'+k+'" class="m_category_item">',
'<h3 class="item-hd">'+item_hd+'</h3>',
'<div class="item-colBox"><p class="item-col">'].join(''));
$.each(this.data, function(){
category.push('<a href="'+this.cms_url+'" title="'+this.brief+'" target="_blank">'+this.brief+'</a>');
});
category.push('</p></div>');
if(this.child.length != 0){
category.push([
'<div class="m_sub_category" style="position:absolute;">',
'<div class="m_subItemCon01">',
'<ul class="m_conList">',
].join(''));
if(this.child.cat){
$.each(this.child.cat.child, function(){
if(this.data.length && this.child.cat2.data.length){
category.push([
'<li class="subItem">',
'<h3 class="subItem-hd">',
'<a href="javascript:;" style="cursor:default;">'+this.child.cat2.data[0].brief+'</a></h3>',
'<p class="subItem-col">'
].join(''));
$.each(this.data, function(){
category.push('<a href="'+this.cms_url+'" title="'+this.brief+'" target="_blank">'+this.brief+'</a>');
});
category.push('</p></li>');
}
});
}
category.push('</ul>');
if(this.child.channel){
$.each(this.child.channel.data, function(){
this.brief && category.push('<div class="enter_channel"><a href="'+this.cms_url+'" title="'+this.brief+'">'+this.brief+'</a></div>');
});
}
category.push([
'</div>',
'<div class="m_subItemCon02">',
'<div class="m_hotBrands">',
'<h3>促销品牌</h3>',
'<textarea class="lazyload" style="display:none;">',
'<ul>'].join(''));
if(this.child.brand){
$.each(this.child.brand.data, function(){
category.push('<li><a href="'+this.cms_url+'" title="'+this.brief+'" target="_blank"><img src="'+this.image_url+'" alt="'+this.brief+'"></a></li>');
});
}
category.push(['</ul>',
'</textarea>',
'</div>'].join(''));
if(this.child.act && this.child.act.data.length){
category.push('<div class="m_hotAct"><h3>热门活动</h3>');
$.each(this.child.act.data, function(){
category.push('<p class="txt"><a href="'+this.cms_url+'" title="'+this.brief+'" target="_blank">'+this.brief+'</a></p>');
});
category.push('</div>');
}
if(this.child.img && this.child.img.data.length){
category.push([
'<div class="m_recAct">',
'<textarea class="lazyload" style="display:none;">',
'<a href="'+this.child.img.data[0].cms_url+'" title="'+this.child.img.data[0].brief+'" target="_blank"><img src="'+this.child.img.data[0].image_url+'" alt="'+this.child.img.data[0].brief+'"></a></div>',
'</textarea>'].join(''));
}
category.push('</div></div>');
}
category.push('</li>');
});
$('#Js_m_category .m_category_bd_con').html(category.join(''));
}(this, jQuery);
	</script>
	<!--navbar end-->
</div>
