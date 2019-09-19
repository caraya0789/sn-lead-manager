(function($){

	var Stats = {
		init : function() {
			var ctx = $(".js-stats-chart");

			var data = ctx.data('stats');

			var myLineChart = new Chart(ctx, {
			    type: 'line',
			    data: data,
			    options: {}
			});
		}
	};

	$( Stats.init );

})(window.jQuery);