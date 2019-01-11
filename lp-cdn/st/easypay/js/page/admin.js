(function ($) {
	$(function () {
		$('#jq_index_login_out').bind('click', function () {
			layer.confirm('您是否要退出系统？', function () {
				$.ajax({
					url: './?c=Login&a=Logout',
					type: 'post',
					success: function (result) {
						if (result.status) {
							window.location.href = './?c=Login&a=Default';
						} else {
							layui.open({
								title: '提示:'
								, area: 'auto'
								, content: result.msg
							});
						}
					}
				});
			});
		});

		$('#jq_index_update_pwd').bind('click', function () {
			active.show('./?c=Admin_Admin&a=ChangePwd', {
				id: 'jq_index_update_pwd_admin',
				title: '修改密码',
				width: 430,
				height: 350
			});
		});

		//refresh tab
		$('#refresh_tab').bind('click', function () {
			var selectedTab = $('#index_tabs').tabs('getSelected');
			var url = selectedTab.data('url');

			refreshTab(url);
		});

		// $(document).on('click', '.changePwd-btn-save', function () {
		//     var _data = $(this).parents('form').serialize();
		//     var data  = $(this).parents('form').serializeArray();
		//
		//     if (data[2].value == "") {
		//         layer.msg('请输入旧密码！', {icon : 5});
		//         return false;
		//     }
		//
		//     if (data[3].value == "") {
		//         layer.msg('请输入新密码！', {icon : 5});
		//         return false;
		//     }
		//
		//     if (data[4].value == "") {
		//         layer.msg('请输入确认密码！', {icon : 5});
		//         return false;
		//     }
		//
		//     $.post('./?c=Admin_Admin&a=ChangePwd&e=exec', _data, function (res) {
		//         if (res.status > 0) {
		//             layer.close(layer.index); //关闭最新弹出的层
		//         } else {
		//             layer.msg(res.msg, {icon : 5});
		//         }
		//
		//         layer.closeAll('tips');
		//     });
		//
		//     return false;
		// });
	});
})(jQuery);
