(function($){
	"use strict";

	var Savings = {
		init : function() {

			var chartData = $('#savingsChart').data('data');
			if(!chartData)
				return;
			
			var sections = chartData.current.time > chartData.new.time ? chartData.current.time : chartData.new.time;

			
			var currentBalance = chartData.current.amount;
			var minimun_rate = chartData.current.minimun;
			var interest_rate = chartData.current.interest;

			var currentData = Savings.calculate(minimun_rate, interest_rate, currentBalance);

			//currentData.push(0);

			var newData = [];
			var newBalance = chartData.new.amount;
			for(var i=0;i<chartData.new.time;i++) {
				newData.push(newBalance)
				newBalance = newBalance - chartData.new.payment;
			}
			newData.push(0);

			var dataSets = [];
			var sections = (newData.length > currentData.length)
					? Math.ceil(newData.length / currentData.length)
					: Math.ceil(currentData.length / newData.length);
			var unit = (newData.length > currentData.length) ? currentData.length : newData.length;

			var sanatized = { current:[], new:[], currentZero:false, newZero:false };
			for(var i = 0; i <= sections; i++) {
				if(newData[i * unit])
					sanatized.new.push(newData[i * unit]);
				else if(!sanatized.newZero) {
					sanatized.new.push(0);
					sanatized.newZero = true;
				}
				if(currentData[i * unit])
					sanatized.current.push(currentData[i * unit]);
				else if(!sanatized.currentZero) {
					sanatized.current.push(0);
					sanatized.currentZero = true;
				}
			}

			var labels = [];
			for(var i = 0; i <= sections; i++) {
				labels.push('');
			}

			var myLineChart = new Chart($("#savingsChart"), {
			    type: 'line',
			    data: {
			    	labels: labels,
				    datasets: [{
			            label: "Credit Counselling",
			            fill: true,
			            backgroundColor: "rgba(160,96,166,1)",
			            borderColor: "rgba(160,96,166,0)",
			            borderCapStyle: 'round',
			            borderDash: [],
			            borderDashOffset: 0,
			            borderJoinStyle: 'round',
			            pointBorderWidth: 0,
			            pointHoverRadius: 0,
			            pointHoverBorderWidth: 0,
			            pointRadius: 0,
			            pointHitRadius: 0,
			            data: sanatized.new
			        },{
			            label: "Minimum Payments",
			            fill: true,
			            backgroundColor: "rgba(102,181,254,1)",
			            borderColor: "rgba(102,181,254,0)",
			            borderCapStyle: 'round',
			            borderDash: [],
			            borderDashOffset: 0,
			            borderJoinStyle: 'round',
			            pointBorderWidth: 0,
			            pointHoverRadius: 0,
			            pointHoverBorderWidth: 0,
			            pointRadius: 0,
			            pointHitRadius: 0,
			            data: sanatized.current
			        }]
			    },
			    options: {
			    	legend: {
			    		position:'bottom',
			    		labels: {
			    			boxWidth: 15
			    		}
			    	},
			    	tooltips: {
			    		enabled:false
			    	},
			    	scales: {
			    		xAxes : [{
			    			ticks:{
				    			display:false
				    		},
			    			gridLines : {
			    				display:false
			    			},
			    			scaleLabel: {
			    				display:true,
			    				labelString:'Months to be debt free'
			    			}
			    		}],
			    		yAxes : [{
			    			gridLines : {
			    				display:false
			    			},
			    			ticks: {
			    				callback:function(value) {
			    					return '$'+value;
			    				}
			    			}
			    		}]
			    	}
			    }
			});

			$('.savings-tooltip').each(function(){
				var $el = $(this);

				var closeToltip = function(e) {
					if ($(e.target).closest($el).size()) 
			            return;
			        $('body').off('touchend', closeToltip);
        			$el.tooltip('close');
				};

				$el.tooltip({
					position: {
				        my: "bottom-10",
				        at: "left top"
			      	},
			      	open: function () {
			            $('body').on('touchend', closeToltip);
					}
				});
			});
		},

		calculate : function(minPaymentRate, interestRate, amount) {
			var data = [];
			var rate = interestRate / 12;

			var balance = amount;

			var months = 0;
			var interest_paid = 0;

			while(balance > 0) {
				var payment = balance * minPaymentRate;
				if(payment < 15)
					payment = 15;

				data.push(balance);

				var interest = (balance * rate);
				var interest_paid = interest_paid+interest;

				balance = (balance + interest) - payment;
				months++;
			}

			data.push(0);

			return data;
		}
	};

	$(Savings.init);

})(window.jQuery);