$(document).ready(function () {
    $('#add-order-form #obtainingField').change((e) => {
        if ($('#add-order-form #obtainingField').children('option:selected').val() == 'add-new-method-of-obtaining') {
            $('#add-order-form #obtainingField').parent().fadeOut(500);
            setTimeout(() => {
                $('#add-order-form #obtainingField').parent().remove();
                $('#add-order-form .modal-inputs').append('<p>Способ получения<input id="obtainingField"  data-validation="required"  placeholder="Способ получения" type="text" name="obtaining-method" ></p>')
            }, 520)
        }

    });
    $('#edit-order-form #editObtainingField').change((e) => {
        if ($('#edit-order-form #editObtainingField').children('option:selected').val() == 'add-new-method-of-obtaining') {
            $('#edit-order-form #editObtainingField').parent().fadeOut(500);
            setTimeout(() => {
                $('#edit-order-form #editObtainingField').parent().remove();
                $('#edit-order-form .modal-inputs').append('<p>Способ получения<input id="editObtainingField"  data-validation="required"  placeholder="Способ получения" type="text" name="obtaining-method" ></p>')
            }, 520)
        }

    });

    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }

    $('.genpass').click(function () {
        const inpt = $(this).parent().find("input");
        const letters = ['a', 'v', 'r', 'e', 'N', 'W', 'Z', 'O', 'T', 'y'];
        const randomVal = [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1].map(n => {
            if (Math.random() > .5)
                return Math.floor(Math.random() * 9 + 1 - n);
            else
                return letters[Math.floor(Math.random() * 9 + 1 - n)];
        }).join('');
        inpt.val(randomVal);
    });
    $('.genid').click(function () {
        const inpt = $(this).parent().find("input");
        const randomVal = [0, 1, 1, 1, 1, 1].map(n => {
            return Math.floor(Math.random() * 9 + 1 - n);
        }).join('');
        inpt.val(randomVal);
    });
    $('#copy-btn').click(function () {
        copyToClipboard('#error-url')
    });
    let icon = $('#menu-burger > i');
    $('#menu-burger').click(function () {
        icon.toggleClass('fa-bars');
        icon.toggleClass('fa-times');
    });

    $('#owners-lists-container').on('click', '#open-invisible-owner-list', () => {
        $('#owners-list-invisible').toggleClass('open');
        if ($('#owners-list-invisible').hasClass('open')) {
            $('#open-invisible-owner-list').html('Скрыть');
            $('#owners-list-invisible').animate({
                height: $('#owners-list-invisible').get(0).scrollHeight
            }, 1000, function () {
                $('#owners-list-invisible').height('auto');
            });
        } else {
            $('#open-invisible-owner-list').html('Показать всех');
            $('#owners-list-invisible').animate({
                height: '0px'
            }, 1000);

        }

    });
    $('#open-invisible-owner-edit-list').click(() => {
        $('#edit-owners-list-invisible').toggleClass('open');
        if ($('#edit-owners-list-invisible').hasClass('open')) {
            $('#open-invisible-owner-edit-list').html('Скрыть');
            $('#edit-owners-list-invisible').animate({
                height: $('#edit-owners-list-invisible').get(0).scrollHeight
            }, 1000, function () {
                $('#edit-owners-list-invisible').height('auto');
            });
        } else {
            $('#open-invisible-owner-edit-list').html('Показать всех');
            $('#edit-owners-list-invisible').animate({
                height: '0px'
            }, 1000);

        }
    });
    $('#owners-lists-container').on('change', '.owner-percent-input', () => {
        $('.owner-percent-input').each(function () {
            $(this).attr('value', $(this).val());
        })
    });


    $('#menu-burger').click(() => {

        if ($('#menu-burger').hasClass('menu-burger--active')) {
            $('#menu-burger').removeClass('menu-burger--active');
            $('#menu').removeClass('menu--open');
            $('#menu-invisible').removeClass('menu--open');
        } else {
            $('#menu-burger').addClass('menu-burger--active');
            $('#menu').addClass('menu--open');
            $('#menu-invisible').addClass('menu--open');

        }
    });
    $('.close-btn-box').click(() => {
        if ($('.custom-alert').hasClass('custom-alert--active'))
            $('.custom-alert').removeClass('custom-alert--active');
    });

    function handle_mousedown(e) {
        if ('DIV' !== e.target.tagName) return;
        const my_dragging = {};
        my_dragging.pageX0 = e.pageX;
        my_dragging.pageY0 = e.pageY;
        my_dragging.elem = this;
        my_dragging.offset0 = $(this).offset();

        function handle_dragging(e) {
            const left = my_dragging.offset0.left + (e.pageX - my_dragging.pageX0);
            const top = my_dragging.offset0.top + (e.pageY - my_dragging.pageY0);
            $(my_dragging.elem)
                .offset({top: top, left: left});
        }

        const body = $('body');

        function handle_mouseup(e) {
            body
                .off('mousemove', handle_dragging)
                .off('mouseup', handle_mouseup);
        }

        body
            .on('mouseup', handle_mouseup)
            .on('mousemove', handle_dragging);
    }

    $('.modal/*.table-container*/').mousedown(handle_mousedown);


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

    function yFixedNoJquerry() {
        if (window.innerWidth > 524) {
            $('.table-wrapper').scroll(function (e) {
                $(this).find('thead').css({transform: 'translateY(' + this.scrollTop + 'px)'});
            });
        }
    }

    function initFilters() {
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
                        const same = (a) => {
                            let cb = (a, b = data) => a.toUpperCase().includes(b.toUpperCase());
                            if (data.includes('<=')) {
                                cb = (a) => +a <= +data.split('<=')[1];
                            } else if (data.includes('>=')) {
                                cb = (a) => +a >= +data.split('>=')[1];
                            } else if (data.includes('>')) {
                                cb = (a) => +a > +data.split('>')[1];
                            } else if (data.includes('<')) {
                                cb = (a) => +a < +data.split('<')[1];
                            } else if (data.includes('=')) {
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
                // .click(function () {
                //     $filled = null;
                //     $('input').each(function () {
                //         if ($(this).prop('id') !== `${key}-i`) {
                //             this.value = '';
                //         } else {
                //             $filled = $(this);
                //         }
                //     });
                //     if (this.value.length === 0) {
                //         const jo = _this.find("#tbody").find("tr");
                //         jo.show();
                //     }
                // });
            });
        });
    }

    if ($("table").length > 0) {
        initFilters();
        yFixedNoJquerry();
        filterIcons();
    }
    $('.checkbox').click(function () {
        $('.loader').show();
        let parent = $(this).parent().parent().parent();
        let id = parent.attr('itemid');
        let type = $('.table-menu>h2').attr('type');
        let url = '';
        let data = {id};
        switch (type) {
            case "Project":
                url = 'projectActivity';
                break;
            case "globalVG":
                url = 'globalActivity';
                break;
            case "Branch":
                url = 'branchActivity';
                break;
            case "MethodsOfObtaining":
                url = "methodOfObtaining";
                data['active'] = parent.find('.checkbox.status').prop('checked') ? 0 : 1;
                data['participates_in_balance'] = parent.find('.checkbox.participates').prop('checked') ? 0 : 1;
                break;
            default:
                url = 'activity';
                break;
        }
        $(this).toggleClass('checked');
        $.ajax({
            url: "../api/operate/" + url + ".php",
            type: "POST",
            data: data,
            dataType: "JSON",
            cache: false,
            success: function (res) {
                // if (res.error)
                //     createAlertTable(res.error, '');
                location.reload();
            },
            error: function () {
                createAlertTable('failed', '');
            },
            complete: function () {
                $('.loader').fadeOut('fast');
            }
        })
    });

    (function range() {
        if (typeof moment !== "function") return;
        const start = moment().day("Sunday");
        const end = moment();

        function cb(start, end) {
            $('#reportrange1 span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            $('.loader').show();
            $.ajax({
                url: "../api/select/ownerSums.php",
                type: "POST",
                data: {start: start.format('YYYY-MM-DD'), end: end.format('YYYY-MM-DD')},
                cache: false,
                success: function (res) {
					if(!res || res == null) return;
					
                    res = JSON.parse(res);
                    if (res.error) {
                        createAlertTable(res.error, "Данные владельцев");
                        return;
                    }
                    res.forEach(r => {
                        const cell = $('.Owner-Stats [itemid*=' + r.id + '] .1-f');
                        cell.attr('title', r.sum || 0);
                        cell.text((+r.sum).toFixed(2) || 0);
                    })

                },
                error: function () {
                    createAlertTable("connectionError", "Расход");
                },
                complete: function () {
                    $('.loader').fadeOut('fast');
                }
            });
        }

        $('#reportrange1').daterangepicker({
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
                'Неделя': [moment().day("Sunday"), moment()],
                'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Все время': [moment('1970-01-01', 'YYYY-MM-DD'), moment('2100-01-01', 'YYYY-MM-DD')]
            }
        }, cb);

        cb(start, end);
    })();

    const vgcl = $('#VG-Modal #nameVgnField');
    vgcl.change(function (e) {
        $('#VG-Modal #nameField').val($('#VG-Modal #nameVgnField option:selected').text());
    });
    $('.main-header .fa-coins').click(function () {
        $('.loader').show();
        $.ajax({
            url: "../api/select/branchSums.php",
            type: "POST",
            cache: false,
            dataType: 'JSON',
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, "Данные предприятия");
                    return;
                }
                const modal = $("#Branch-money-info-modal");
                modal.css({left: $('.fa-coins').offset().left - 50, top: 50});
                $("#Branch-money-info-modal .fiats").html(res.map(line => `<p>${line.sum} ${line.full_name}</p>`))
                modal.modal({
                    blockerClass: '',
                });
            },
            error: function () {
                createAlertTable("connectionError", "Деньги");
            },
            complete: function () {
                $('.loader').fadeOut('fast');
            }
        });

    });
    $('#Branch-money-info-modal').on($.modal.BLOCK, function (event, modal) {
        $('.blocker.current').removeClass('blocker');
    });
});


