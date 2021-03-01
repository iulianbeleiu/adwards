'use strict';

(function (window, $) {
    window.App = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            this._selectors.generateBudget,
            this._generateDailyBudget.bind(this)
        )

        this.$wrapper.on(
            'click',
            this._selectors.generateCosts,
            this._generateDailyCosts.bind(this)
        )
    };

    $.extend(window.App.prototype, {
        _selectors: {
            generateRandomStuff: '.js-generate-random-stuff',
            generateBudget: '.js-generate-budget',
            generateCosts: '.js-generate-costs',
            chart: '.js-chart',
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

        _executeAjax: function ($url, $method) {
            return $.ajax({
                url: $url,
                method: $method,
            });
        }
    });
})(window, jQuery);