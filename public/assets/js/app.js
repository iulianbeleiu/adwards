'use strict';

(function (window, $) {
    window.App = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.handleChartReport();

        this.$wrapper.on(
            'click',
            this._selectors.generateRandomStuff,
            this.handleGenerateRandomStuff.bind(this)
        )
    };

    $.extend(window.App.prototype, {
        _selectors: {
            generateRandomStuff: '.js-generate-random-stuff',
            chart: '.js-chart',
        },

        handleGenerateRandomStuff: function (e) {
            this._generateDailyBudget();
            this._generateDailyCosts();
            this.handleChartReport();
        },

        handleChartReport: function (e) {
            var $chartElement = $(this._selectors.chart);
            var $chart = new Chart($chartElement, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Budget',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: [10, 30, 39, 20, 25, 34, -10],
                        fill: false,
                    }, {
                        label: 'Cost',
                        fill: false,
                        backgroundColor: 'rgb(54, 162, 235)',
                        borderColor: 'rgb(54, 162, 235)',
                        data: [18, 33, 22, 19, 11, 39, 30],
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Max Budget / Day  VS Total Cost Generated / Day'
                    },
                    scales: {
                        y: {
                            suggestedMin: 10,
                            suggestedMax: 50
                        }
                    }
                }
            });

            this._getChartReport()
                .then(function (response) {
                    $chart.data.datasets[0].data = response.budget;
                    $chart.data.datasets[1].data = response.costs;
                    $chart.data.labels = response.days;
                    $chart.update();
                })
        },

        _generateDailyBudget: function () {
            let $url = '/generate-budget';
            let $method = 'POST';

            this._executeAjax($url, $method)
                .then(function (data) {
                    if (data.code !== 201) {
                        alert('Could not generate budget.')
                    }
                });
        },

        _generateDailyCosts: function () {
            let $url = '/generate-costs';
            let $method = 'POST';

            this._executeAjax($url, $method)
                .then(function (data) {
                    if (data.code !== 201) {
                        alert('Could not generate costs.')
                    }
                });
        },

        _getChartReport: function ()
        {
            let $url = '/daily-report';
            let $method = 'GET';

            return this._executeAjax($url, $method);
        },

        _executeAjax: function ($url, $method) {
            return $.ajax({
                url: $url,
                method: $method,
            });
        }
    });
})(window, jQuery);