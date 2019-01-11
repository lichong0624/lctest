var indexAddTbl;

//刷新当前标签Tabs
function refreshTab(url) {
	url = arguments[0] ? arguments[0] : '';
	var currentTab = $('#index_tabs').tabs('getSelected');
	var optionEle = $(currentTab.panel('options'));
	var tabId = optionEle.attr('menuId');
	if (url === '') {
		url = optionEle.attr('href');
	}

	$('#index_tabs').tabs('update', {
		tab: currentTab,
		options: {
			href: url
		}
	});
	currentTab.panel('refresh');
	currentTab.attr("id", "tab" + tabId);
	currentTab.attr("data-url", url);
}

function refreshModalDiv(url, index) {
	$.get(url, function (str) {
		$('#' + index).find('.modal-body').html(str);
	});
}

(function ($) {
	bindMenuEvent();

	function bindMenuEvent() {
		$('.jq_module_li_a_class').bind('click', function () {
			if ($(this).hasClass('on1')) {
				$(this).removeClass("on1").next().slideUp(300);
			} else {
				$(".left_nav .on1").removeClass('on1').next().slideUp(300);
				$(this).addClass('on1').next().slideDown(300);
			}
		});
		$(".jq_menu_li_a_class_1").click(function () {
			if ($(this).hasClass('on2')) {
				$(this).removeClass("on2").next().slideUp(300);
			} else {
				$(".left_nav .on2").removeClass('on2').next().slideUp(300);
				$(this).addClass('on2').next().slideDown(300);
			}
		});

		$(".jq_menu_li_a_class_2").click(function () {
			$(this).parents(".sub_item").find(".on2").removeClass("on2");
			$(this).parents(".sub_item2").prev().addClass("on2");
			$(".sub_item2 a.on3").removeClass("on3");
			$(this).addClass("on3");
		});

		$(".jq_menu_li_a_class_5").click(function () {
			$(".jq_menu_li_a_class_5").removeClass("on3");
			$(this).addClass("on3");
		});

		$('.jq_menu_li_a_class_url').bind('click', function () {
			var url = $(this).data('url');
			var title = $(this).data('title');
			var menuId = $(this).data('id');

			if (url && url !== '#') {
				addTab({
					url: url,
					title: title,
					menuId: menuId,
					iconCls: ''
				});
			}
		});
	}

	function show() {
		$("#loading").fadeOut("normal", function () {
			$(this).remove();
		});
	}

	var delayTime;
	$.parser.onComplete = function () {
		if (delayTime) clearTimeout(delayTime);
		delayTime = setTimeout(show, 50);
	};

	var addTab = function (params) {
		var t = $('#index_tabs');
		var opts = {
			title: params.title,
			href: params.url,
			closable: true,
			iconCls: params.iconCls,
			menuId: params.menuId,
			border: false,
			fit: true,
			loadingMessage: ''
		};

		if (t.tabs('exists', opts.title)) {
			t.tabs('select', opts.title);
			var activeTab = t.tabs('getSelected');
			var charts = activeTab.find('.my_chart-container').highcharts();
			charts && charts.reflow();
		} else {
			t.tabs('add', opts);
			var selectedTab = $('#index_tabs').tabs('getSelected');
			selectedTab.attr("id", "tab" + params.menuId);
			selectedTab.attr("data-url", params.url);
		}
	};
	indexAddTbl = addTab;

	var index_layout;
	var index_tabs;
	var index_tabsMenu;
	$(function () {
		index_layout = $('#index_layout').layout({
			fit: true
		});

		index_tabs = $('#index_tabs').tabs({
			fit: true,
			border: false,
			cache: false,
			tabHeight: 36,
			plain: true,
			pill: true,
			onContextMenu: function (e, title) {
				e.preventDefault();
				index_tabsMenu.menu('show', {
					left: e.pageX,
					top: e.pageY
				}).data('tabTitle', title);
			},
			onAdd: function (title, index) {

			}
		});

		index_tabsMenu = $('#index_tabsMenu').menu({
			onClick: function (item) {
				var curTabTitle = $(this).data('tabTitle');
				var type = $(item.target).attr('title');

				if (type === 'refresh') {
					index_tabs.tabs('getTab', curTabTitle).panel('refresh');
					return;
				}

				if (type === 'close') {
					var t = index_tabs.tabs('getTab', curTabTitle);
					if (t.panel('options').closable) {
						index_tabs.tabs('close', curTabTitle);
					}
					return;
				}

				var allTabs = index_tabs.tabs('tabs');
				var closeTabsTitle = [];

				$.each(allTabs, function () {
					var opt = $(this).panel('options');
					if (opt.closable && opt.title !== curTabTitle && type === 'closeOther') {
						closeTabsTitle.push(opt.title);
					} else if (opt.closable && type === 'closeAll') {
						closeTabsTitle.push(opt.title);
					}
				});

				for (var i = 0; i < closeTabsTitle.length; i++) {
					index_tabs.tabs('close', closeTabsTitle[i]);
				}
			}
		});
	});
})(jQuery);
