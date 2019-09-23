function yFixedNoJquerry() {
    if (window.innerWidth > 524) {
        $('.table-wrapper').scroll(function (e) {
            $(this).find('thead').css({transform: 'translateY(' + this.scrollTop + 'px)'});
        });
    }
}

function filterIcons() {
    $('th').each(function () {
        const _this = $(this);
        _this.click(function (e) {
            if (e.target.tagName === 'INPUT') return;
            setTimeout(() => {
                $('th').each(function () {
                    const span = $(this).children().first().children().first().children().last();
                    span.removeClass();
                });
                const span = $(this).children().first().children().first().children().last();
                if ($(this).attr('aria-sort') === 'descending') {
                    span.addClass('fas fa-arrow-down');
                } else if ($(this).attr('aria-sort') === 'ascending') {
                    span.addClass('fas fa-arrow-up');
                }
            }, 10);
        })
    });
    $('tr').each(function () {
        const el = $(this);
        el.click(function (e) {
            if (e.target.tagName === 'I') return;
            el.toggleClass('clicked');

            function unclick() {
                el.toggleClass('clicked');
                window.removeEventListener('click', unclick);
            }

            setTimeout(() => window.addEventListener('click', unclick), 100);
        });
    })
}

function initSorterAndFilters() {
    $(function () {
        $(".table-container").tablesorter();
    });
    $(".table-container").each(function () {
        const clone = $(this).find("#tbody > tr").first().clone();
        const _this = $(this);
        clone.attr('id', "spec");
        clone.css({visibility: 'hidden'});
        clone.children().each(function () {
            $(this).css({padding: '0', fontSize: '0px'});
        });
        _this.find("#tbody").append(clone);
        const cols = _this.find("thead > tr > th").length;

        function fill(arr, cols) {
            arr.push(cols);
            return cols ? fill(arr, --cols) : arr.reverse();
        }

        const maxcols = fill([], cols);
        maxcols.forEach(key => {
            _this.find(`#${key}-i`).keyup(function () {
                const data = this.value;
                let jo = _this.find("#tbody").find("tr");
                jo.hide();
                maxcols.forEach(k => {
                    const data = _this.find(`#${k}-i`)[0] && _this.find(`#${k}-i`)[0].value;
                    if (!data || !data.length) return;
                    const same = (a)=>{
                        let cb = (a, b = data)=> a.toUpperCase().includes(b.toUpperCase());
                        if(data.includes('<=')){
                            cb = (a) => +a <= +data.split('<=')[1];
                        }else if(data.includes('>=')){
                            cb = (a) => +a >= +data.split('>=')[1];
                        }else if(data.includes('>')){
                            cb = (a) => +a > +data.split('>')[1];
                        }else if(data.includes('<')){
                            cb = (a) => +a < +data.split('<')[1];
                        }else if(data.includes('=')){
                            cb = (a) => +a === +data.split('=')[1];
                        }
                        return cb(a);
                    };
                    jo = jo.filter(function checkRows() {
                        return same($(this).children(`.${k}-f`).first()[0].innerText) || $(this).prop('id') === 'spec';
                    });
                });
                jo.show();

            }).focus(function () {
                this.value = "";
                $(this).css({
                    "color": "black"
                });
                $(this).unbind('focus');
            }).css({
                "color": "#C0C0C0"
            })
        });
    });
}

$(window).load(function () {
    if (!window.location.pathname.includes('statistics')
        && !window.location.pathname.includes('turnover'))
            $('.loader').fadeOut('slow');
});

$(document).ready(function () {
    if (!window.location.pathname.includes('statistics') &&
    !window.location.pathname.includes('types')) return;
    $('.loader').show();
    const toDate = (dates) => ([dates[0].format(FORMATTER) + START, dates[1].format(FORMATTER) + END]);

    const FORMATTER = 'YYYY-MM-DD';
    const START = ' 00:00:00';
    const END = ' 23:59:59';
    const curYear = toDate([moment().startOf('year'), moment()]).concat(
        [moment()
            .startOf('year')
            .year()]
    );
    const manually = toDate([moment(), moment()]);

    const lastYear = toDate([moment().startOf('year').add(-1, 'years'), moment().startOf('year').add(-1, 'days')]).concat(
        [moment()
            .startOf('year')
            .add(-1, 'days')
            .year()]
    );

    const all = ['0000-01-01 00:00:00', '3000-01-01 00:00:00', 'все время'];
    let months = [0, 1, 2, 3, 4, 5, 6].map(off =>
        toDate(
            [
                moment()
                    .startOf('month')
                    .add(-off, 'month'),
                moment()
                    .startOf('month')
                    .add(-off + 1, 'months')
                    .add(-1, 'days')
            ]
        ).concat(
            [moment()
                .startOf('month')
                .add(-off, 'month')
                .month()
                ,
                moment()
                    .startOf('month')
                    .add(-off, 'month')
                    .year()]
        )
    );
    let weeks = [manually].concat([0, 1, 2].map(off => toDate([moment().add(-off, 'weeks').day("Sunday"), moment().add(-off, 'weeks').day('Saturday')])));


    function range() {
        if (typeof moment !== "function") return;
        const start = moment().day("Sunday");
        const end = moment();
        const OPTIONS = {
            startDate: start,
            endDate: end,
            locale: {
                "format": "MM/DD/YYYY",
                "separator": " - ",
                "applyLabel": "Применить",
                "cancelLabel": "Отмена",
                "fromLabel": "От",
                "toLabel": "До",
                "customRangeLabel": "Вручную",
                "weekLabel": "W",
                "daysOfWeek": [
                    "Вс",
                    "Пн",
                    "Вт",
                    "Ср",
                    "Чт",
                    "Пт",
                    "Сб"
                ],
                "monthNames": [
                    "Янв",
                    "Фев",
                    "Мар",
                    "Апр",
                    "Май",
                    "Июн",
                    "Июл",
                    "Авг",
                    "Сен",
                    "Окт",
                    "Ноя",
                    "Дек"
                ],
                "firstDay": 1
            },
            ranges: {
                'Сегодня': [moment(), moment()],
                'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Неделя': [moment().day("Sunday"), moment().day("Sunday").add(6, 'days')],
                'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Все время': [moment('1970-01-01', 'YYYY-MM-DD'), moment('2100-01-01', 'YYYY-MM-DD')]
            }
        };

        function cb(start, end) {
            $('#reportrange1 span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            // $('.loader').show();
            $.ajax({
                url: "../api/select/userStat.php",
                type: "POST",
                data: {start: start.format(FORMATTER) + START, end: end.format(FORMATTER) + END},
                cache: false,
                success: function (res) {
                    res = JSON.parse(res);
                    if(res.error){
                        createAlertTable(res.error, "Статистика");
                        return;
                    }
                    if (!res || !res.length) return;
                    res.forEach(r => {
                        const cell = $('.Stat1 [itemid=' + r.id + '] .2-f');
                        cell.attr('title', r.sum || 0);
                        cell.text(r.sum || '-');
                    })
                },
                error: function () {
                    createAlertTable("connectionError", "Расход");
                },
                complete: function () {
                    // $('.loader').fadeOut('fast');
                }
            });
        }

        function cb2(start, end) {
            $('#reportrange2 span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            // $('.loader').show();
            $.ajax({
                url: "../api/select/userStat.php",
                type: "POST",
                data: {start: start.format(FORMATTER) + START, end: end.format(FORMATTER) + END, type: 2},
                cache: false,
                success: function (res) {
                    res = JSON.parse(res);
                    if(res.error){
                        createAlertTable(res.error, "Статистика");
                        return;
                    }
                    if (!res || !res.length) return;
                    res.forEach(r => {
                        const cell = $('.Stat2 [itemid="' + r.id + '"] .1-f');
                        cell.attr('title', r.sum || 0);
                        cell.text(r.sum || '-');
                    });
                },
                error: function () {
                    createAlertTable("connectionError", "Расход");
                },
                complete: function () {
                    // $('.loader').fadeOut('fast');
                }
            });
        }

        let withoutFiats = false;
        const switcher = $('.switch-vg-stat');
        if (!withoutFiats) {
            $('.Stat2').hide();
            $('.Stat3').show();
            switcher.text('VG по валютам');
        } else {
            $('.Stat2').show();
            $('.Stat3').hide();
            switcher.text('Сумма по VG');
        }
        switcher.click(function () {
            if (withoutFiats) {
                $('.Stat2').hide();
                $('.Stat3').show();
                switcher.text('VG по валютам');
            } else {
                $('.Stat2').show();
                $('.Stat3').hide();
                switcher.text('Сумма по VG');
            }
            withoutFiats = !withoutFiats;
        });

        function cb3(start, end) {
            $('#reportrange3 span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            // $('.loader').show();
            $.ajax({
                url: "../api/select/userStat.php",
                type: "POST",
                data: {start: start.format(FORMATTER) + START, end: end.format(FORMATTER) + END, type: 3},
                cache: false,
                success: function (res) {
                    res = JSON.parse(res);
                    if(res.error){
                        createAlertTable(res.error, "Статистика");
                        return;
                    }
                    if (!res || !res.length) return;
                    res.forEach(r => {
                        const cell = $('.Stat3 [itemid="' + r.id + '"] .2-f');
                        cell.attr('title', r.sum || '-');
                        cell.text(r.sum || '-');
                    });
                },
                error: function () {
                    createAlertTable("connectionError", "Расход");
                },
                complete: function () {
                    // $('.loader').fadeOut('fast');
                }
            });
        }

        function cb4(start, end) {
            $('#reportrange-types span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            $('.loader').show();
            setTimeout(()=> {
                $.ajax({
                    url: "../api/select/types-stat.php",
                    type: "POST",
                    data: {start: start.format(FORMATTER) + START, end: end.format(FORMATTER) + END},
                    cache: false,
                    success: function (res) {
                        if(!res){
                            $('.name-wrapper .forsum').text("");
                            $('.loader').fadeOut('fast');
                            return;
                        }
                        res = JSON.parse(res);
                        if(res.error){
                            createAlertTable(res.error, "Типы расходов");
                            return;
                        }
                        if (!res || !res.length) return;

                        res.forEach(({outgo_type_id, sum}, i)=>{
                            res[i].sum = +sum;
                            $('[itemid="' +outgo_type_id + '"] .name-wrapper .forsum').text("")
                        });
                        res.forEach(({outgo_type_id, fiat_name, sum})=>{
                            if(!+sum) return;
                            res
                                .filter(({outgo_type_id: outgo_type_id2, fiat_name: fiat_name2}) =>{
                                    const regexp = new RegExp(`^${outgo_type_id2}.*`);
                                    return outgo_type_id2.length < outgo_type_id.length &&
                                        regexp.test(outgo_type_id)
                                })
                                .reduce((acc, elem, i)=>{
                                    if(acc.find(e=>e.outgo_type_id === elem.outgo_type_id)){
                                        return acc;
                                    }
                                    acc.push(elem);
                                    return  acc;
                                }, [])


                                .forEach(({outgo_type_id: outgo_type_id2, fiat_name: fiat_name2, sum: sum2})=>{
                                    const idx = res.findIndex(({outgo_type_id: outgo_type_id3, fiat_name: fiat_name3})=>
                                        outgo_type_id3 === outgo_type_id2 &&
                                        fiat_name3 === fiat_name);
                                    if(idx >= 0){
                                        res[idx] = {
                                            ...res[idx],
                                            fiat_name,
                                            sum: +res[idx].sum + +sum
                                        }
                                    }else{
                                        res.push({
                                            outgo_type_id: outgo_type_id2,
                                            fiat_name,
                                            sum
                                        });
                                    }
                                });
                        });
                        res.forEach(({outgo_type_id, fiat_name, sum}) => {
                            if(!fiat_name || !+sum) return;
                            const cell = $('[itemid="' +outgo_type_id + '"] > .row-wrapper .name-wrapper .forsum');
                            cell.append(`<span>--${sum}${fiat_name}</span>`)
                        });
                    },
                    error: function () {
                        createAlertTable("connectionError", "Типы расходов");
                        $('.loader').fadeOut('fast');
                    },
                    complete: function () {
                        $('.loader').fadeOut('fast');
                    }
                });
            }, 1000);
        }



        if(window.location.pathname.includes('statistic')){

            cb(start, end);
            cb2(start, end);
            cb3(start, end);
            $('#reportrange1').daterangepicker(OPTIONS, cb);
            $('#reportrange2').daterangepicker(OPTIONS, cb2);
            $('#reportrange3').daterangepicker(OPTIONS, cb3);
        }else{
            cb4(start, end);
            $('#reportrange-types').daterangepicker(OPTIONS, cb4);
        }
    }
    if(window.location.pathname.includes('statistic')){
        $.ajax({
            url: "../api/select/statistics.php",
            type: "POST",
            data: {years: [curYear, lastYear, all], weeks, months},
            cache: false,
            success: function (res) {
                $('.loader').fadeOut("fast");
                try {
                    res = JSON.parse(res);
                } catch (e) {
                }
                $('#wrapper').html(res);
                initSorterAndFilters();
                range();
                yFixedNoJquerry();
                filterIcons();
            },
            error: function () {
                createAlertTable("connectionError", "Деньги");
            },
            complete: function () {
                // $('.loader').fadeOut('fast');
            }
        });
    }else{
        range();

    }

});

$(document).ready(function () {
    if (!window.location.pathname.includes('turnover')) return;
    $('.loader').show();
    $.ajax({
        url: "../api/select/turnover.php",
        type: "POST",
        cache: false,
        success: function (res) {
            try {
                res = JSON.parse(res);
            } catch (e) {
            }
            if(res.error){
                createAlertTable(res.error, "Статистика");
                return;
            }
            $('#wrapper').html(res);
            initSorterAndFilters();
            yFixedNoJquerry();
            filterIcons();
        },
        error: function () {
            createAlertTable("connectionError", "Деньги");
        },
        complete: function () {
            $('.loader').fadeOut('fast');
        }
    });
});



