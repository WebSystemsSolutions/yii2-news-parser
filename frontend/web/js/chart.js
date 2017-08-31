/**
/**
/**
 * Created by andrew on 4/27/17.
 */

//hide deprecated info
moment.suppressDeprecationWarnings = true;

$(function () {
    Site.init().drawChart.init();
});

var Site = new function () {
    var object = this;

    this.circleByGraphTimer = null;

    $('.graph').hover(function () {
        object.timerCircleDraw();
    }, function () {
        clearInterval(object.circleByGraphTimer);
    });

    this.model= {};
    this.init = function () {
        var d = $('#searchform-dateto').val().split('-'),
            dataOfFilter = d[0]+'-'+d[1]+'-'+d[2].substring('2');

        this.model.$emptyPath = $('svg.hidden path');
        this.model.heightArrow = 20;
        this.model.hints = {};
        this.model.priceInOnePx = null; // price 1 px
        this.model.vAxisOffset = 100;
        this.model.forecast = {
            old: 7,
            next: parseInt($('#searchform-days').val()),
        };
        this.model.search = {
            fromMoment : null,
            toMoment : Site.date.toMoment(dataOfFilter),
            to: dataOfFilter,
        }
        this.model.momentFormat = 'DD-MM-YY';
        this.model.values = window['chartData'].values || []; // context in hints
        this.model.prices = window['chartData'].prices.prev || [];
        this.model.pricesOfNext = window['chartData'].prices.next || [];
        this.model.offsetForecastInBottom = 50; // offset forecast line and points in bottom side
        this.model.countsForecastNotVisible = 0; // count counts of forecast what not disabled by graphs

        this.getParamOfGraph();

        //for debugger
        // this.model.values["02-05-17"] = '0.1';
        // this.model.values["03-05-17"] = '-0.1';
        // this.model.values["04-05-17"] = '-0.35';
        // this.model.values["05-05-17"] = '-0.16';
        // this.model.values["08-05-17"] = '-0.153';
        // this.model.values["09-05-17"] = '0.1';
        // this.model.values["10-05-17"] = '-0.1';
        // this.model.values["11-05-17"] = '0.1';
        // this.model.values["12-05-17"] = '0.6211';
        // this.model.values["13-05-17"] = '0.1';
        // this.model.values["14-05-17"] = '-0.5';
        // this.model.values["15-05-17"] = '0.1';
        // this.model.values["16-05-17"] = '-0.1';
        // this.model.values["17-05-17"] = '0.1';
        // this.model.values["18-05-17"] = '-0.1';
        // this.model.values["19-05-17"] = '0.1';
        // this.model.values["20-05-17"] = '0.1';
        // this.model.values["21-05-17"] = '0.1';
        // this.model.values["22-05-17"] = '0.1';
        // this.model.values["23-05-17"] = '0.1';
        // this.model.values["24-05-17"] = '-0.1';
        // this.model.values["25-05-17"] = '0.1';
        // this.model.values["26-05-17"] = '0.1';
        // this.model.values["27-05-17"] = '-0.1';
        // this.model.values["28-05-17"] = '0.1';
        // this.model.values["29-05-17"] = '0.1';
        // this.model.values["30-05-17"] = '-0.1';
        //
        // this.model.listOfData = "<tr><td>161</td><td><span class='date'>16-05-17 00:26:09</span></td><td>en</td><td>50 Sq. Mm Single Core Red Thin Walled Flex. Elast Cable With Copper Conductor Up To 750 Volts Conforming To Rdso Letter No. El/2.2.37 Dated 23.03.2...</td><td><span class='koefs'>0.18</span></td></tr>" +
        //                         "<tr><td>161</td><td><span class='date'>17-05-17 00:26:09</span></td><td>en</td><td>50 Sq. Mm Single Core Red Thin Walled Flex. Elast Cable With Copper Conductor Up To 750 Volts Conforming To Rdso Letter No. El/2.2.37 Dated 23.03.2...</td><td><span class='koefs'>0.18</span></td></tr>" +
        //                         "<tr><td>161</td><td><span class='date'>18-05-17 00:26:09</span></td><td>en</td><td>50 Sq. Mm Single Core Red Thin Walled Flex. Elast Cable With Copper Conductor Up To 750 Volts Conforming To Rdso Letter No. El/2.2.37 Dated 23.03.2...</td><td><span class='koefs'>0.18</span></td></tr>" +
        //                         "<tr><td>161</td><td><span class='date'>19-05-17 00:26:09</span></td><td>en</td><td>50 Sq. Mm Single Core Red Thin Walled Flex. Elast Cable With Copper Conductor Up To 750 Volts Conforming To Rdso Letter No. El/2.2.37 Dated 23.03.2...</td><td><span class='koefs'>0.18</span></td></tr>";
        // $('.b_table__grid tr:last').after(Site.model.listOfData);

        return this;
    };
    this.date = {
        toMoment : function (date) {
            var d = date.split('-');
            return moment(d[0] + '-' + d[1] + '-' + d[2], Site.model.momentFormat);
        }
    },
    this.getParamOfGraph = function () {
        var offset = this.model.vAxisOffset,
            max = Math.max.apply(Math,this.model.prices.map(function(o){return o[1];})),
            min = Math.min.apply(Math,this.model.prices.map(function(o){return o[1];})),
            maxN = Math.max.apply(Math,this.model.pricesOfNext.map(function(o){return o[1];})),
            minN = Math.min.apply(Math,this.model.pricesOfNext.map(function(o){return o[1];}));

        if (maxN!=-Infinity) {
            max = Math.max.apply(Math, [max, maxN]);
        }
        if (minN!=Infinity) {
            min = Math.min.apply(Math, [min, minN]);
        }
        // max = Math.max.apply(Math, [max, maxN].map(function(o){return o[1];}));
        // min = Math.max.apply(Math, [min, minN].map(function(o){return o[1];}));

        this.model.maxValue = (max + offset) - (max + offset) % offset;
        this.model.minValue = (min - offset) - (min - offset) % offset;
    };
    this.convertPrices = function () {
        var obj = {}
        $.each(this.model.prices, function (key, value) {
            obj[value[0]] = value[1];
        })
        this.model.priceObject = obj;
    },
    this.convertData = function () {
        var obj= {};
        $.each(object.model.prices, function (key, value) {
            obj[value[0]] = value[1];
        });

        return obj;
    }
    this.getArchiveToObject = function () {
        var obj = {};

        $.each(this.model.prices, function (index, value) {
            var data = value[0].replace(new RegExp(' ', 'g'), '_');

            obj[data] = value[1];
        });

        return obj;
    };
    this.timerCircleDraw = function () {
        var $svg = $('.block-charts svg').not('.hide'),
            $points = $svg.find('circle');

        clearInterval(object.circleByGraphTimer);
        object.circleByGraphTimer = setInterval(function () {
            $points.attr('fill-opacity', 1).filter('.forecast').attr('fill', '#727272');
        }, 30);
    };
    this.calculateOnePx = function () {
        var price, path, cy,
            $svg = $('.block-charts svg').not('.hide'),
            $circle = $svg.find('circle.forecast').first();

        this.convertPrices();
        price = this.model.priceObject[$circle.attr('data-key')];

        path = this.model.maxValue - price;
        cy = parseFloat($circle.attr('cy'));
        this.model.priceInOnePx = path/cy;

    },
    this.hints = function () {
        var $hintsData, $target,
            object = this,
            $svg = $('.block-charts svg').not('.hide'),
            $svgHidden = $('svg.hide'),
            $hintsDefault = $svgHidden.find('.hints'),
            $circles = $svg.find('circle'),
            $arrCirclesForecast = $circles.filter('.forecast'),
            $arrCirclesCommon = $circles.not('.forecast');

        this.model.hintsCommon = {};
        //set weight for hints forecast
        $.each(this.model.prices, function (index, value) {
            var content, d, $element, key, price,
                data = value[0],
                f = object.model.momentFormat;

            $element = $circles.filter('[data-key="'+ data +'"]');

            if ($element.length) {
                d = Site.date.toMoment(data);
                if ($element.filter('.forecast').length) {
                    key = d.add(-Site.model.forecast.next,'days').format(f);
                    content = object.model.values[key];
                    price = object.model.priceObject[key];
                    object.model.hints[data] ={
                        data: key,
                        articles: 'articles: ' + (content && content.count ? content.count : '0'),
                        weight : 'weight: ' + (content && content.value ? content.value : '0'),
                        price : 'price: ' + (price ? price : '0')
                    }
                }
                if ($element.not('.forecast').length){
                    key = value[0];
                    price = object.model.priceObject[key];
                    object.model.hintsCommon[key] ={
                        data: key,
                        price : 'price: ' + (price ? price : '0')
                    }
                }
            }
        });

        $svg.find('defs').append($svgHidden.find('filter#rablfilter5'));

        $svg.find('g').eq(7).addClass('hide'); // tooltip hide
        $svg.append($hintsDefault.clone());

        $hintsData = $svg.find('.hints');

        var direction = function (coordinate, $element) {
            var key, data, coordY,
                offset = 15;

            coordY = function () {
                //50% - y
                if (coordinate.baseY < coordinate.height50) {
                    coordinate.offsetY = 5;
                    coordinate.offsetX = coordinate.baseX + offset;
                } else {
                    //100% - y
                    var zm = coordinate.height + offset;
                    if (coordinate.baseY > zm) {
                        coordinate.offsetY = coordinate.baseY - zm;
                    } else {
                        coordinate.offsetY = coordinate.baseY - coordinate.height50;
                        coordinate.offsetX = coordinate.baseX + offset;
                    }
                }
            };

            //50% -x
            if (coordinate.baseX > coordinate.width50) {
                coordinate.offsetX = coordinate.baseX - coordinate.width50
            }
            coordY(); // for first element

            // last
            if (Site.compareData(Site.model.prices[object.model.prices.length-1][0], $target.attr('data-key'))) {
                coordinate.offsetX = coordinate.baseX - coordinate.width - offset;
                //50% - y
                if (coordinate.baseY < coordinate.height50) {
                    coordinate.offsetY = 5;
                } else {
                    //>50% - y
                    if (coordinate.height50 < coordinate.baseY) {
                        coordinate.offsetY = coordinate.baseY - coordinate.height50;
                    }
                }
            }

            // Y<5
            if (coordinate.offsetY >-1 && coordinate.offsetY < coordinate.minWidth) {
                coordinate.offsetY = coordinate.minWidth;
                coordinate.offsetX = coordinate.baseX + 15;
            }

            coordinate.offsetY = coordinate.offsetY>0 ? coordinate.offsetY : coordinate.baseY;
            coordinate.offsetX = coordinate.offsetX>0 ? coordinate.offsetX : coordinate.baseX;

            var lastCx = parseFloat($arrCirclesForecast.last().attr('cx'));
            if (coordinate.offsetX + coordinate.width >= lastCx) {
                coordinate.offsetX = lastCx - coordinate.width - ($element.next().is('circle') ? 0 : 15)
            }
        };

        $arrCirclesForecast.hover(function (source) {
            $target = $(source.target);
            var coordinate, height,
                $rect = $hintsData.find('rect'),
                $rectDefault = $hintsDefault.find('rect'),
                hints = object.model.hints[$target.data('key')],
                offset = parseInt($rectDefault.attr('width')) + 40,
                $date = $hintsData.find('.date'),
                $article = $hintsData.find('.article').show(),
                $price = $hintsData.find('.price').show(),
                $weight = $hintsData.find('.weight');

            height = parseInt($rectDefault.attr('height')) + 25;

            coordinate = {
                baseX : parseFloat($target.attr('cx')),
                baseY : parseFloat($target.attr('cy')),
                height: height,
                width: offset,
                height50: height/2,
                width50: offset/2,
                offsetY: -1,
                offsetX: -1,
                minWidth: 5
            },

            direction(coordinate, $(this));

            $rect.attr({
                x: coordinate.offsetX,
                y: coordinate.offsetY,
                width: coordinate.width,
                height: coordinate.height
            });

            offset = coordinate.offsetX + 15;
            var offsetY = 26,
                offsetY1 = 23;

            $date.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY
            }).text(!hints ? '' : hints.data);
            $price.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY + offsetY1
            }).text(!hints ? '0' : hints.price);
            $weight.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY + offsetY1*2
            }).text(!hints ? '0' : hints.weight);
            $article.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY + offsetY1*3
            }).text(!hints ? '0' : hints.articles);

            $hintsData.removeClass('hide');
        }, function () {
            $hintsData.addClass('hide');
        });

        $arrCirclesCommon.hover(function (source) {
            $target = $(source.target);
            var $rect = $hintsData.find('rect'),
                offsetX = 15,
                $rectDefault = $hintsDefault.find('rect'),
                hints = object.model.hintsCommon[$target.data('key')],
                offset = parseInt($rectDefault.attr('width')) + 30,
                coordinate = {
                    baseX : parseFloat($target.attr('cx')),
                    baseY : parseFloat($target.attr('cy')),
                    height: parseInt($rectDefault.attr('height')),
                    width: offset,
                    height50: parseInt($rectDefault.attr('height'))/2,
                    width50: offset/2,
                    offsetY: -1,
                    offsetX: -1,
                    minWidth: 5
                },
                $date = $hintsData.find('.date'),
                $price = $hintsData.find('.weight');

            $hintsData.find('.article').hide();
            $hintsData.find('.price').hide();

            direction(coordinate, $(this));

            if ( coordinate.offsetY < coordinate.baseY && coordinate.baseY < coordinate.offsetY + coordinate.height ) {
                coordinate.offsetY = coordinate.baseY;
            }

            coordinate.offsetY += offsetX;
            $rect.attr({
                x: coordinate.offsetX,
                y: coordinate.offsetY,
                width: parseFloat($rectDefault.attr('width')) + offsetX*2,
                height: parseFloat($rectDefault.attr('height')) - offsetX

            });

            offset = coordinate.offsetX + offsetX;
            var offsetY = 26,
                offsetY1 = 23;

            $date.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY
            }).text(!hints ? '' : hints.data);
            $price.text('').attr({
                x: offset,
                y: coordinate.offsetY + offsetY + offsetY1
            }).text(!hints ? '0' : hints.price);
            $hintsData.removeClass('hide');
        }, function () {
            $hintsData.addClass('hide');
        });
    };
    this.compareData = function (d1, d2) {
        d1 = d1.split('-');
        d2 = d2.split('-');

        return (Number(d1[0])==Number(d2[0]) && Number(d1[1])==Number(d2[1]) && Number(d1[2])==Number(d2[2])) ? true : false;
    }
    this.compareDataHight = function (d1, d2) {
        var momentFrom = Site.date.toMoment(d1),
            momentTo = Site.date.toMoment(d2);

        return momentFrom.unix() > momentTo.unix() ? true : false;
    }
    this.cutWeek = function (data) {
        var dataOfFilter,
            array = [],
            label = false,
            object = this,
            d = $('#searchform-datefrom').val().split('-');

        dataOfFilter = d[0]+'-'+d[1]+'-'+d[2].substring('2');
        $.each(data, function (key, value) {
            if (label || object.compareData(value[0], dataOfFilter) || object.compareDataHight(value[0], dataOfFilter)) {
                array.push(value);
                label = true;
            } else {
                Site.model.countsForecastNotVisible++;
            }
        })
        return array;
    }
    this.drawChart = {
        options : {
            legend: {},
            height: 330,
            colors:['white'],
            backgroundColor: {
                fill: '#444444'
            },
            curveType: 'function',
            chartArea: {
                backgroundColor: '#444444'
            },
            hAxis: {
                gridlines:{
                    color: '#333',
                    count: 0
                },
                textStyle:{
                    color: '#ffffff',
                    fontSize: 10,
                    fontName: 'Arial'
                }
            },
            vAxis: {
                gridlines:{
                    color: 'transparent',
                    count: 0
                },
                textStyle:{
                    color: '#ffffff',
                    fontSize: 10,
                    fontName: 'Arial'
                },
                viewWindowMode: 'explicit',
                viewWindow: null
            },
            series: {
                1: {
                    pointsVisible : true,
                    pointSize: 4
                }
            },
            tooltip: {
                trigger: 'none',
                textStyle:{color: '#FF0000'},
                showColorCode: true
            },
            lineWidth: 3,
            orientation: 'horizontale',
            axisTitlesPosition: 'none',
            is3D: true,
            textStyle: {
                color: '#ffffff'
            }
        },
        init : function () {
            Site.forecastBefore();

            google.charts.load('current', {'packages':['line']});
            google.charts.setOnLoadCallback(this.draw);
        },
        draw : function () {
            var chart,
                data = new google.visualization.DataTable();

            data.addColumn('string', '');
            data.addColumn('number', '');
            data.addRows(object.cutWeek(object.model.prices));

            Site.drawChart.options.vAxis.viewWindow = {
                max: Site.model.maxValue,
                min: Site.model.minValue
            }

            chart = new google.charts.Line(document.getElementById('linechart_material'));
            chart.draw(data, google.charts.Line.convertOptions(Site.drawChart.options));

            Site.drawChart.about();
        },
        about : function () {
            var timer = setInterval(function () {
                var $block = $('svg circle');

                if ($block.length) {
                    object.convertPrices();
                    var $circle = $('.graph svg').find('circle').attr({'fill-opacity':1});

                    $block.attr({'fill-opacity':1});
                    object.timerCircleDraw();

                    clearInterval(timer);
                    clearInterval(object.circleByGraphTimer);

                    var data,
                        labelCircleAsForecast = false;

                    if (Site.model.pricesOfNext.length) {
                        var elements = Site.model.pricesOfNext;
                        data = elements[elements.length - 1][0];
                    } else {
                        data = Site.model.search.to;
                    }

                    $.each($circle, function (index, value) {
                        var $value = $(value),
                            key = object.model.prices[index + Site.model.countsForecastNotVisible][0];;

                        $value.attr('data-key', key);
                        $value.after($value.clone().addClass('forecast hide').attr({
                            fill: '#727272'
                        }));

                        if (Site.compareDataHight(key, data)) {
                            labelCircleAsForecast = true;
                            $value.remove();
                        }
                    });
                    object.forecastAfter();
                    object.hints();
                } else {
                    if (!object.model.prices.length) {
                        $('.graph').append('<div class="no-data">No results found.</div>');
                        clearInterval(timer);
                    }
                }
            }, 50);
        }
    }
    this.forecastBefore = function() {
        var data, currentDate,
            listOfDates = this.getArchiveToObject();

        this.model.countCircleOfForecast = 0;
        this.convertPrices();
        var lastIndex = this.model.prices.length;
        for (var i = 0; i <= lastIndex - 1; i++) {
            this.model.countCircleOfForecast++;

            currentDate = this.model.prices[i][0].replace(new RegExp(' ', 'g'), '_');
            data = Site.date.toMoment(currentDate).add(Site.model.forecast.next, 'days').format('DD-MM-YY');

            if (!Site.model.priceObject[data]) {
                var price = listOfDates[currentDate];

                $.each(Site.model.pricesOfNext, function (key, value) {
                    if (Site.compareData(data, value[0])) {
                        price = value[1];
                    }
                });
                this.model.prices.push([data, price]);
            }
        }
    };
    this.forecastAfter = function() {
        var d, $circleForecast, $circleOfCommon, forecastPathD, edge, maxPriceInPx,
            object = this,
            count = -1,
            $svg = $('.block-charts svg:not(.hide)'),
            $pathCommon = $svg.find('path').first(),
            $forecastPath = this.model.$emptyPath.clone().addClass('common-forecast'),
            ar = $pathCommon.attr('d').split(' ');

        $circleForecast = $svg.find('circle.forecast');
        $circleOfCommon = $svg.find('circle').not('.forecast');

        Site.model.$lastElementOfCommonLine = $circleOfCommon.last();

        //clear Path
        var off,
            _par = ar.toString().split('L');

        d = _par[0];
        for(var i = 1; i< $circleOfCommon.length; i++) {
            off = ' L '+_par[i];
            d += off;
        }
        d = d.replace(new RegExp(',', 'g'), ' ');
        $pathCommon.attr('d', d);

        object.calculateOnePx();

        $.each($circleOfCommon, function (key, value) {
            var arrData, data, $nextCircle,
                $value = $(value);

            data = $value.attr('data-key');
            arrData = data.split('-');
            arrData = moment(arrData[0]+'-'+arrData[1]+'-'+arrData[2],'DD-MM-YY').add(+Site.model.forecast.next,'days').format('DD-MM-YY');

            $nextCircle = $circleForecast.filter('[data-key="'+ arrData +'"]');
            if ($nextCircle.length) {
                count++;
                $nextCircle.attr({
                    cx: parseFloat($nextCircle.attr('cx')),
                    cy: $value.attr('cy')
                });
            }
        })


        //edge left circle for forecast offset
        //edge = object.model.dataEdgeLeftForecast ? Site.date.toMoment(object.model.dataEdgeLeftForecast) : null;
        edge = Site.date.toMoment($circleOfCommon.last().attr('data-key'));
        maxPriceInPx = (this.model.maxValue - this.model.minValue) / this.model.priceInOnePx;

        $.each($circleForecast, function (index, value) {
            var cy, data, d, offset,
                $value = $(value);

            if (index <= Site.model.countsForecastNotVisible) {
                var price,
                    key = $value.attr('data-key');

                data = Site.date.toMoment(key).add(-Site.model.forecast.next,'days').format('DD-MM-YY');
                price = Site.model.priceObject[data];
                offset = price ? (Site.model.maxValue - price) / Site.model.priceInOnePx : 0;
            } else {
                cy = parseFloat($value.attr('cy'));
                offset = cy; //* object.model.priceInOnePx;
            }

            $value.attr({
                cy: (offset > maxPriceInPx) ? maxPriceInPx - 21 : offset
            })

            d = Site.date.toMoment($value.attr('data-key'));
            if (d.isSame(edge)) {
                return false; // exit
            }
        });
        //====================================

        //direction
        forecastPathD = 'M';
        count = -1;
        var corr=0, incorr=0;
        $.each($circleForecast, function (index, element) {
            var data, value, path, price,
                label = false,
                $element = $(element),
                coord = {
                    cx: $element.attr('cx'),
                    cy: $element.attr('cy')
                },
                key = $element.attr('data-key');

            data = Site.date.toMoment(key).add(-Site.model.forecast.next,'days').format('DD-MM-YY');

            value = object.model.values[data];
            value = value ? Number(value.value) : null;
            price = object.model.priceObject[data];

            if (!price) {
                $element.remove();
                return true; //continue
            }
            //if (Math.abs(price - object.model.priceObject[key]) <= 30
            if (Math.abs(parseFloat($('circle[data-key="'+key+'"]').attr('cy')) - parseFloat($element.attr('cy'))) <= 20
                && parseFloat(Site.model.$lastElementOfCommonLine.attr('cx')) >= parseFloat($element.attr('cx'))) {
                label = true;
            }

            if (value && parseFloat(Site.model.$lastElementOfCommonLine.attr('cx')) >= parseFloat($element.attr('cx'))) {
                // Accuracy of the model
                if (value>0 && parseFloat($('circle[data-key="'+key+'"]').attr('cy')) < parseFloat($element.attr('cy')) || 
                    value<0 && parseFloat($('circle[data-key="'+key+'"]').attr('cy')) > parseFloat($element.attr('cy'))) {
                    corr++;
                } else {
                    incorr++;
                }
            } 

            count++;
            $element.removeClass('hide');
            path = count ? ' L ' : ' ';
            forecastPathD += (path + coord.cx + ' ' + coord.cy);

            if (value && !label) {
                object.forecastPathDraw($element , value);
            }
        });

        $forecastPath.attr({
            stroke: '#727272',
            'stroke-dasharray': 10,
            d: forecastPathD
        }).removeClass('forecast');

        $pathCommon.after($forecastPath);

        var total = corr+incorr,
            percPerPoint = 100/total,
            accu = Math.round(corr * percPerPoint);
        if (total){
            $('.graph').after('<div class="pull-right">Accuracy of the model <b>'+accu+'%</b></div>');
        }
    };
    this.forecastPathDraw = function ($element, weight) {
        var object, coord;

        coord = {
            x: parseFloat($element.attr('cx')),
            y1: parseFloat($element.attr('cy')),
            y2: null,
            corner1: {},
            corner2: {}
        }

        if (weight>0) { // up
            coord.y2 = coord.y1 - this.model.heightArrow;
            coord.y1 =  coord.y1 - 4;
            coord.corner2.y = coord.y2 + 5;
            object = {
                stroke: '#cda568'
            }
        } else { // down
            coord.y2 =  coord.y1 + this.model.heightArrow;
            coord.y1 = coord.y1 + 4;
            coord.corner2.y = coord.y2 - 5;
            object = {
                stroke: '#60f6f5'
            }
        }

        coord.corner1.x = coord.x - 2.5;
        coord.corner2.x = coord.x + 2.5;

        object.d = ['M ' + coord.x +' ' + coord.y1 + ' L ' + coord.x + ' ' + coord.y2
        + ' L ' + coord.corner1.x + ' ' + coord.corner2.y +' L ' + coord.corner2.x + ' ' + coord.corner2.y +' L ' + coord.x + ' ' + coord.y2]; // + 'L '262 121.5 L 262 126.5 L 277 124'

        $('.graph svg').append(this.model.$emptyPath.clone().addClass('forecast').attr(object));
    }
};