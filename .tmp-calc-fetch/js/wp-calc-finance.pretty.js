function calc_finance_loan_consider_result(e) {
    var r, n, c, l, o, u = 0,
        s = 0,
        _ = 0,
        f = 0,
        d = 0,
        y = 0,
        v = 0,
        m = 0;
    switch (textfirstpayment = jQuery("#" + e + " .textfirstpayment").val(), textmonthlypayment = jQuery("#" + e + " .textmonthlypayment").val(), proc = jQuery("#" + e + " .nachisl").val(), 2 == proc ? (sp = Number(jQuery("#" + e + " .percent").val().replace(",", ".")), p = 12 * sp) : p = Number(jQuery("#" + e + " .percent").val().replace(",", ".")), srok = jQuery("#" + e + " .vremya").val(), 2 == srok ? (ss = Number(jQuery("#" + e + " .term").val().replace(",", ".")), t = 12 * ss) : t = Number(jQuery("#" + e + " .term").val().replace(",", ".")), credit = Number(jQuery("#" + e + " .credit").val().replace(/\s/g, "", ",", ".")), firstpay = Number(jQuery("#" + e + " .firstpay").val().replace(/\s/g, "", ",", ".")), credit -= firstpay, a = credit, jQuery("#" + e + " .Shema").val()) {
        case "classic":
            jQuery("#" + e + " .pay_header").text(textfirstpayment);
            break;
        case "annuitet":
            jQuery("#" + e + " .pay_header").text(textmonthlypayment);
            break;
        default:
            jQuery("#" + e + " .pay_header").text("")
    }
    switch (jQuery("#" + e + " .Shema").val()) {
        case "classic":
            o = a / t + a * p / 1200;
            break;
        case "annuitet":
            o = a * p / 1200 / (1 - Math.pow(1 + p / 1200, -t))
    }
    for (i = 1; 3 >= i; i++) f = Number(jQuery("#" + e + " .pr" + i).val().replace(",", ".")) * a / 100, r = Number(jQuery("#" + e + " .com" + i).val().replace(",", ".")), r && r > f && (f = r), 1 == i && (u += f), 2 == i && (s += f), 3 == i && (_ += f);
    for (c = Number(calc_finance_round(a / t, 2)), z = Number(calc_finance_round(a * p / 1200 / (1 - Math.pow(1 + p / 1200, -t)), 2)), i = 1; t > i; i++) {
        switch (jQuery("#" + e + " .Shema").val()) {
            case "classic":
                l = Number(calc_finance_round(a * p / 1200, 2)), z = Number(calc_finance_round(c + l, 2)), a -= c;
                break;
            case "annuitet":
                l = Number(calc_finance_round(a * p / 1200, 2)), c = Number(calc_finance_round(z - l, 2)), a -= c;
                break;
            default:
                l = ""
        }
        v += l, y += c, m += z
    }
    c = a, l = Number(calc_finance_round(c * p / 1200, 2)), z = Number(calc_finance_round(c + l, 2)), v += l, y += c, m += z, n = Math.floor(t / 12) == t / 12 ? t / 12 : Math.floor(t / 12), d = u + (s + o) * t + _ * n, overpayment = m - credit + u + s * t + _ * n, summAll = m + u + s * t + _ * n, summMonthlyFee = s * t, summEearFee = _ * n, overpaymentPercentage = overpayment / credit * 100, jQuery("#" + e + " .monthPay").val(calc_finance_dis_num(o, 2, ".", " ")), jQuery("#" + e + " .monthlyFee").val(calc_finance_dis_num(s, 2, ".", " ")), jQuery("#" + e + " .allPay").val(calc_finance_dis_num(summAll, 2, ".", " ")), jQuery("#" + e + " .overpayment").val(calc_finance_dis_num(overpayment, 2, ".", " ")), jQuery("#" + e + " .interestOnLoan").val(calc_finance_dis_num(v, 2, ".", " ")), jQuery("#" + e + " .monthlyPayment").val(calc_finance_dis_num(Math.round(v / t * 100) / 100, 2, ".", " ")), jQuery("#" + e + " .singleFee").val(calc_finance_dis_num(u, 2, ".", " ")), jQuery("#" + e + " .summMonthlyFee").val(calc_finance_dis_num(summMonthlyFee, 2, ".", " ")), jQuery("#" + e + " .summEearFee").val(calc_finance_dis_num(summEearFee, 2, ".", " ")), jQuery("#" + e + " .overpaymentPercentage").val(calc_finance_dis_num(overpaymentPercentage, 1, ".", " "))
}

function calc_finance_loan_schedule(e) {
    var a = Number(jQuery("#" + e + " .credit").val().replace(/\s/g, "", ",", ".")),
        t = Number(jQuery("#" + e + " .firstpay").val().replace(/\s/g, "", ",", "."));
    a -= t, proc = jQuery("#" + e + " .nachisl").val(), 2 == proc ? (sp = Number(jQuery("#" + e + " .percent").val().replace(",", ".")), percent = 12 * sp) : percent = Number(jQuery("#" + e + " .percent").val().replace(",", ".")), srok = jQuery("#" + e + " .vremya").val().replace(",", "."), 2 == srok ? (ss = Number(jQuery("#" + e + " .term").val().replace(",", ".")), term = 12 * ss) : term = Number(jQuery("#" + e + " .term").val().replace(",", "."));
    var r, n, c, l = 0,
        o = 0,
        u = 0;
    textPeriod = jQuery("#" + e + " .textPeriod").val(), textPayment = jQuery("#" + e + " .textPayment").val(), textPrincipal = jQuery("#" + e + " .textPrincipal").val(), textInterest = jQuery("#" + e + " .textInterest").val(), textBalance = jQuery("#" + e + " .textBalance").val(), textTotal = jQuery("#" + e + " .textTotal").val(), textmonths = jQuery("#" + e + " .textmonths").val(), textyear = jQuery("#" + e + " .textyear").val();
    var s = "";
    s = s + "<table class='table-schedule'><thead><tr><th>" + textPeriod + "</th><th>" + textPayment + "</th><th>" + textPrincipal + "</th><th>" + textInterest + "</th><th>" + textBalance + "</th></tr></thead><tbody>", r = Number(calc_finance_round(a / term, 2)), c = Number(calc_finance_round(a * percent / 1200 / (1 - Math.pow(1 + percent / 1200, -term)), 2));
    var _ = 0,
        p = new Array,
        f = new Array("x"),
        d = new Array(textBalance);
    for (i = 1; term >= i; i++) {
        switch (jQuery("#" + e + " .Shema").val()) {
            case "classic":
                n = Number(calc_finance_round(a * percent / 1200, 2)), c = Number(calc_finance_round(r + n, 2)), a -= r;
                break;
            case "annuitet":
                n = Number(calc_finance_round(a * percent / 1200, 2)), r = Number(calc_finance_round(c - n, 2)), a -= r;
                break;
            default:
                n = ""
        }
        var y = new Array;
        (i - 1) % 12 == 0 && (_++, s = s + "<tr><td colspan=5><b>" + _ + textyear + "</b></nobr></td></tr>", y = [_ + textyear, "", "", "", ""], p[p.length] = y, y = new Array), s = s + "<tr><td><nobr>" + i + textmonths + "</nobr></td><td><b>" + calc_finance_round(c, 2) + "</b></td><td>" + calc_finance_round(r, 2) + "</td><td>" + calc_finance_round(n, 2) + "</td><td>" + calc_finance_round(a, 2) + "</td></tr>", f[i] = i, d[i] = calc_finance_round(a, 2), y[y.length] = i + textmonths, y[y.length] = calc_finance_round(c, 2), y[y.length] = calc_finance_round(r, 2), y[y.length] = calc_finance_round(n, 2), y[y.length] = calc_finance_round(a, 2), p[p.length] = y, o += n, l += r, u += c
    }
    s = s + "<tfoot><tr><td><b>" + textTotal + "</b></td><td><b>" + calc_finance_round(u, 2) + "</b></td><td><b>" + calc_finance_round(l, 2) + "</b></td><td><b>" + calc_finance_round(o, 2) + "</b></td><td></td></tr></tfoot>", s += "</tbody></table>";
    var v = "1" == jQuery("#" + e + " .export_pdf").val(),
        m = "1" == jQuery("#" + e + " .export_graph").val();
    jQuery("#" + e + " .table_data").html((v ? '<a class="pdf" style="word-wrap: initial; cursor:pointer;"><span class="pdf_icon" title="PDF" /></a>' : "") + (m ? '<a class="graph" style="word-wrap: initial; cursor:pointer;"><span class="graph_icon" title="Graph" /></a>' : "") + "<br/>&nbsp;<br/>" + s), m && jQuery("#" + e + " .table_data a.graph").click(function() {
        var a = c3.generate({
            bindto: "#" + e + "_chart",
            data: {
                x: "x",
                columns: [f, d]
            },
            axis: {
                y: {
                    min: 1,
                    padding: {
                        top: 0,
                        bottom: 0
                    }
                }
            },
            color: {
                pattern: ["#3cc88c"]
            },
            tooltip: {
                format: {
                    title: function(e) {
                        return "Month: " + e
                    }
                }
            }
        });
        a.resize({
            height: 400,
            width: 750
        }), a.flush(), jQuery("#" + e + "_chart_modal").dialog({
            dialogClass: "wp-calc-finance-chart",
            width: 800
        })
    }), v && jQuery("#" + e + " .table_data a.pdf").click(function() {
        var a = function() {
            var a = {
                watermark: {
                    text: jQuery("#" + e + " .watermark").val() + "  ",
                    color: "red",
                    opacity: .3
                },
                content: [{
                    table: {
                        headerRows: 1,
                        widths: ["*", "*", "*", "*", "*"],
                        body: [
                            [textPeriod, textPayment, textPrincipal, textInterest, textBalance].slice(0)
                        ].concat(p)
                    }
                }]
            };
            pdfMake.createPdf(a).download("payments.pdf")
        };
        return "undefined" == typeof pdfMake ? jQuery.getScript(wpcalcfinancei18n.pdfmake_url).done(function() {
            jQuery.getScript(wpcalcfinancei18n.vfsfonts_url).done(function() {
                a()
            })
        }) : a(), !1
    })
}

function calc_finance_loan_consider_result_no(e) {
    var r, n, c, l, o, u = "1" == jQuery("#" + e + " .round_up_calc").val(),
        s = u ? 0 : 2,
        _ = jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .includeFeesLoan").prop("checked"),
        f = 12,
        d = 0,
        y = 0,
        v = 0,
        m = 0,
        j = 0,
        h = 0,
        Q = 0,
        b = 0,
        k = 0;
    for (textfirstpayment = jQuery("#" + e + " .textfirstpayment").val(), textmonthlypayment = jQuery("#" + e + " .textmonthlypayment").val(), proc = jQuery("#" + e + " .nachisl").val(), 2 == proc ? (sp = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .percent").val().replace(",", ".")), p = 12 * sp) : p = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .percent").val().replace(",", ".")), srok = jQuery("#" + e + " .vremya").val(), 2 == srok ? (ss = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .term").val().replace(",", ".")), t = 12 * ss) : t = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .term").val().replace(",", ".")), i = 0; 3 >= i; i++) {
        r = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .com" + i).val().replace(",", "."));
        var j = r;
        0 == i && (d += j), 1 == i && (y += j), 2 == i && (v += j), 3 == i && (m += j)
    }
    var x = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .credit").val().replace(/\s/g, "", ",", "."));
    _ && (x = x + y + d), firstpay = 0, x -= firstpay, a = x;
    var g = "annuitet";
    switch (g) {
        case "classic":
            jQuery("#" + e + " .pay_header").text(textfirstpayment), o = a / t + a * p / (100 * f);
            break;
        case "annuitet":
            jQuery("#" + e + " .pay_header").text(textmonthlypayment), o = a * p / (100 * f) / (1 - Math.pow(1 + p / (100 * f), -t));
            break;
        default:
            jQuery("#" + e + " .pay_header").text("")
    }
    for (c = Number(calc_finance_round(a / t, 2)), z = Number(calc_finance_round(a * p / (100 * f) / (1 - Math.pow(1 + p / (100 * f), -t)), 2)), i = 1; t > i; i++) {
        switch (l = Number(calc_finance_round((1 != i || _ ? a : a + d) * p / (100 * f), 2)), g) {
            case "classic":
                z = Number(calc_finance_round(c, 0)) + Number(calc_finance_round(l, 0));
                break;
            case "annuitet":
                c = Number(calc_finance_round(z - l, 0));
                break;
            default:
                l = ""
        }
        a -= c, b += l, Q += c, k += z
    }
    c = a, l = Number(calc_finance_round(c * p / (100 * f), 2)), z = Number(calc_finance_round(c + l, 2)), b += l, b += y + d + v * t, Q += c, k += z, n = Math.floor(t / 12) == t / 12 ? t / 12 : Math.floor(t / 12), h = y + (v + o) * t + m * n, overpayment = k - x + y + v * t + m * n, summAll = k + (_ ? 0 : y + d) + v * t + m * n, summMonthlyFee = v * t, summEearFee = m * n, overpaymentPercentage = overpayment / x * 100;
    var w = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .credit").val().replace(/\s/g, "", ",", ".")),
        F = calc_finance_newtonRaphson(w, o + v, t),
        N = Math.pow(1 + F / 100 / 12, 12) - 1;
    jQuery("#" + e + " .wp-calc-finance-result .monthPay").val(calc_finance_dis_num(o + v, s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .monthlyFee").val(calc_finance_dis_num(v, s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .allPay").val(calc_finance_dis_num(summAll, s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .interestOnLoan").val(calc_finance_dis_num(summAll - Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .credit").val().replace(/\s/g, "", ",", ".")), s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .singleFee").val(calc_finance_dis_num(y, s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .loan").val(calc_finance_dis_num(w, s, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .interestRate").val(calc_finance_dis_num(p, 2, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .effInterestRate").val(calc_finance_dis_num(100 * N, 2, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .creditTerm").val(calc_finance_dis_num(t, 0, ".", " ")), jQuery("#" + e + " .wp-calc-finance-result .paymentsPerYear").val(12)
}

function calc_finance_loan_schedule_no(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .includeFeesLoan").prop("checked"),
        n = 12,
        c = "annuitet",
        l = 0,
        o = 0,
        u = 0,
        s = 0;
    for (i = 0; 3 >= i; i++) minV = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .com" + i).val().replace(",", ".")), Pay = minV, 0 == i && (l += Pay), 1 == i && (o += Pay), 2 == i && (u += Pay), 3 == i && (s += Pay);
    var _ = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .credit").val().replace(/\s/g, "", ",", "."));
    r && (_ = _ + o + l);
    var p = 0;
    _ -= p, proc = jQuery("#" + e + " .nachisl").val(), 2 == proc ? (sp = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .percent").val().replace(",", ".")), percent = 12 * sp) : percent = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .percent").val().replace(",", ".")), srok = jQuery("#" + e + " .vremya").val().replace(",", "."), 2 == srok ? (ss = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .term").val().replace(",", ".")), term = 12 * ss) : term = Number(jQuery("#" + e + " .wp-calc-finance-contract .wp-calc-finance-val .term").val().replace(",", "."));
    var f, d, y, v = 0,
        m = 0,
        j = 0;
    textPeriod = jQuery("#" + e + " .textPeriod").val(), textPayment = jQuery("#" + e + " .textPayment").val(), textPrincipal = jQuery("#" + e + " .textPrincipal").val(), textInterest = jQuery("#" + e + " .textInterest").val(), textBalance = jQuery("#" + e + " .textBalance").val(), textTotal = jQuery("#" + e + " .textTotal").val(), textmonths = jQuery("#" + e + " .textmonths").val(), textyear = jQuery("#" + e + " .textyear").val(), textFees = jQuery("#" + e + " .textFees").val();
    var h = "";
    h = h + "<table class='table-schedule'><thead><tr><th>" + textPeriod + "</th><th>" + textPayment + "</th><th>" + textInterest + "</th><th>" + textFees + "</th><th>" + textPrincipal + "</th><th>" + textBalance + "</th></tr></thead><tbody>", f = Number(calc_finance_round(_ / term, 2)), y = Number(calc_finance_round(_ * percent / (100 * n) / (1 - Math.pow(1 + percent / (100 * n), -term)), 2));
    var Q = 0,
        b = new Array,
        k = new Array("x"),
        x = new Array(textBalance);
    for (creditNorRound = _, i = 1; term >= i; i++) {
        switch (d = Number(calc_finance_round((1 != i || r ? _ : _ + l) * percent / (100 * n), 2)), c) {
            case "classic":
                y = Number(calc_finance_round(f, 0)) + Number(calc_finance_round(d, 0));
                break;
            case "annuitet":
                f = Number(calc_finance_round(y - d, 2));
                break;
            default:
                d = ""
        }
        creditNotRound = _ - f, _ = Number(calc_finance_round(creditNotRound, 0));
        var g = new Array;
        (i - 1) % 12 == 0 && (Q++, h = h + '<tr><td class="subheader" colspan="6">' + Q + textyear + "</nobr></td></tr>", g = [Q + textyear, "", "", "", "", ""], b[b.length] = g, g = new Array);
        var w = (1 != i || r ? 0 : o + l) + u,
            F = (1 != i || r ? 0 : o + l) + y + u;
        i == term && 0 != creditNotRound && ("annuitet" == c && (F += creditNotRound), f += creditNotRound, _ = 0), h = h + "<tr><td><nobr>" + i + textmonths + '</nobr></td><td class="payment">' + calc_finance_round(F, t) + "</td><td>" + calc_finance_round(d, t) + "</td><td>" + calc_finance_round(w, t) + "</td><td>" + calc_finance_round(f, t) + "</td><td>" + calc_finance_round(_, t) + "</td></tr>", k[i] = i, x[i] = calc_finance_round(_, t), g[g.length] = i + textmonths, g[g.length] = calc_finance_round(F, t), g[g.length] = calc_finance_round(d, t), g[g.length] = calc_finance_round(w, t), g[g.length] = calc_finance_round(f, t), g[g.length] = calc_finance_round(_, t), b[b.length] = g, m += d, v += f, j += F
    }
    h = h + "<tfoot><tr><td>" + textTotal + "</td><td>" + calc_finance_round(j, t) + "</td><td>" + calc_finance_round(m, t) + "</td><td></td><td>" + calc_finance_round(v, t) + "</td><td></td></tr></tfoot>", h += "</tbody></table>";
    var N = "1" == jQuery("#" + e + " .export_pdf").val(),
        M = "1" == jQuery("#" + e + " .export_graph").val();
    jQuery("#" + e + " .table_data").html((N ? '<a class="pdf" style="word-wrap: initial; cursor:pointer;"><span class="pdf_icon" title="PDF" /></a>' : "") + (M ? '<a class="graph" style="word-wrap: initial; cursor:pointer;"><span class="graph_icon" title="Graph" /></a>' : "") + "<br/>&nbsp;<br/>" + h), M && jQuery("#" + e + " .table_data a.graph").click(function() {
        var a = c3.generate({
            bindto: "#" + e + "_chart",
            data: {
                x: "x",
                columns: [k, x]
            },
            axis: {
                y: {
                    min: 1,
                    padding: {
                        top: 0,
                        bottom: 0
                    }
                }
            },
            color: {
                pattern: ["#3cc88c"]
            },
            tooltip: {
                format: {
                    title: function(e) {
                        return "Month: " + e
                    }
                }
            }
        });
        a.resize({
            height: 400,
            width: 750
        }), a.flush(), jQuery("#" + e + "_chart_modal").dialog({
            dialogClass: "wp-calc-finance-chart",
            width: 800
        })
    }), N && jQuery("#" + e + " .table_data a.pdf").click(function() {
        var a = function() {
            var a = {
                watermark: {
                    text: jQuery("#" + e + " .watermark").val() + "  ",
                    color: "red",
                    opacity: .3
                },
                styles: {
                    table1: {
                        fontSize: 9
                    }
                },
                content: [{
                    style: "table1",
                    table: {
                        headerRows: 1,
                        widths: ["*", "*", "*", "*", "*", "*"],
                        body: [
                            [textPeriod, textPayment, textInterest, textFees, textPrincipal, textBalance].slice(0)
                        ].concat(b)
                    }
                }]
            };
            pdfMake.createPdf(a).download("payments.pdf")
        };
        return "undefined" == typeof pdfMake ? jQuery.getScript(wpcalcfinancei18n.pdfmake_url).done(function() {
            jQuery.getScript(wpcalcfinancei18n.vfsfonts_url).done(function() {
                a()
            })
        }) : a(), !1
    })
}

function calc_finance_credit_sum(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = calc_finance_format_float(jQuery("#" + e + " .p").val()),
        n = calc_finance_format_float(jQuery("#" + e + " .i").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .n").val()),
        l = jQuery("#" + e + " .comp").val();
    if ("0" == l) var o = parseFloat(n / 1200).toFixed(6),
        i = 12 * c;
    else if ("1" == l) var o = parseFloat(n / 400).toFixed(6),
        i = 4 * c;
    else if ("2" == l) var o = parseFloat(n / 200).toFixed(6),
        i = 2 * c;
    else if ("3" == l) var o = parseFloat(n / 100).toFixed(6),
        i = c;
    var u = 1 + parseFloat(o),
        s = parseFloat(r / o).toFixed(5),
        _ = 1 - parseFloat(Math.pow(u, -i)).toFixed(5);
    jQuery("#" + e + " .op").val(calc_finance_round(parseFloat(s * _).toFixed(2), t))
}

function calc_finance_format_float(e) {
    return e.replace(/,/g, ".").replace(/[^0-9\.]/g, "")
}

function calc_finance_month_payment(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = calc_finance_format_float(jQuery("#" + e + " .l").val()),
        n = calc_finance_format_float(jQuery("#" + e + " .i").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .n").val()),
        l = jQuery("#" + e + " .comp").val();
    if ("0" == l) var o = parseFloat(n / 1200).toFixed(6),
        i = 12 * c;
    if ("1" == l) var o = parseFloat(n / 400).toFixed(6),
        i = 4 * c;
    if ("2" == l) var o = parseFloat(n / 200).toFixed(6),
        i = 2 * c;
    if ("3" == l) var o = parseFloat(n / 100).toFixed(6),
        i = c;
    var u = 1 + parseFloat(o),
        s = parseFloat(r * o).toFixed(5),
        _ = 1 - parseFloat(Math.pow(u, -i)).toFixed(5);
    jQuery("#" + e + " .op").val(calc_finance_round(parseFloat(s / _).toFixed(2), t))
}

function calc_finance_payment_count(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = calc_finance_format_float(jQuery("#" + e + " .l").val()),
        n = calc_finance_format_float(jQuery("#" + e + " .i").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .p").val()),
        l = jQuery("#" + e + " .comp").val();
    if ("" == r ? jQuery("#" + e + " .l").focus() : "" == n ? jQuery("#" + e + " .i").focus() : "" == c && jQuery("#" + e + " .p").focus(), "0" == l) var o = parseFloat(n / 1200).toFixed(6);
    if ("1" == l) var o = parseFloat(n / 400).toFixed(6);
    if ("2" == l) var o = parseFloat(n / 200).toFixed(6);
    if ("3" == l) var o = parseFloat(n / 100).toFixed(6);
    var i = 1 + parseFloat(o),
        u = parseFloat(-Math.log(1 - o * r / c)).toFixed(6),
        s = parseFloat(Math.log(i)).toFixed(6);
    jQuery("#" + e + " .op").val(calc_finance_round(parseFloat(u / s).toFixed(2), t))
}

function calc_finance_loan_balance(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = calc_finance_format_float(jQuery("#" + e + " .l").val()),
        n = calc_finance_format_float(jQuery("#" + e + " .p").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .i").val()),
        l = calc_finance_format_float(jQuery("#" + e + " .n").val()),
        o = jQuery("#" + e + " .comp").val();
    if ("0" == o) var i = parseFloat(c / 1200).toFixed(6);
    else if ("1" == o) var i = parseFloat(c / 400).toFixed(6);
    else if ("2" == o) var i = parseFloat(c / 200).toFixed(6);
    else if ("3" == o) var i = parseFloat(c / 100).toFixed(6);
    var u = 1 + parseFloat(i),
        s = parseFloat(r * Math.pow(u, l)).toFixed(5),
        _ = parseFloat(n / i * (Math.pow(u, l) - 1)).toFixed(5);
    jQuery("#" + e + " .op").val(calc_finance_round(parseFloat(s - _).toFixed(2), t))
}

function calc_finance_loan_interest_rate(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = calc_finance_format_float(jQuery("#" + e + " .l").val()),
        n = calc_finance_format_float(jQuery("#" + e + " .n").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .p").val()),
        l = 12 * n,
        o = Math.log(1 + parseFloat(1 / l)),
        i = Math.log(2),
        u = parseFloat(o / i),
        s = Math.pow(1 + parseFloat(c / r), 1 / u).toFixed(4),
        _ = parseFloat(Math.pow(s - 1, u)).toFixed(4);
    jQuery("#" + e + " .op").val(calc_finance_round(parseFloat(1200 * (_ - 1)).toFixed(2), t))
}

function calc_finance_credit_twice_month(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = jQuery("#" + e + " .pamount").val(),
        n = jQuery("#" + e + " .interst").val(),
        c = jQuery("#" + e + " .year").val();
    if ("" == r) jQuery("#" + e + " .pamount").focus();
    else if ("" == n) jQuery("#" + e + " .interst").focus();
    else if ("" == c) jQuery("#" + e + " .year").focus();
    else {
        r = parseFloat(jQuery("#" + e + " .pamount").val()), n = parseFloat(jQuery("#" + e + " .interst").val()), c = parseFloat(jQuery("#" + e + " .year").val()), n = n / 12 / 100, c = 12 * c;
        var l = r * n * Math.pow(1 + n, c),
            o = Math.pow(1 + n, c) - 1,
            i = l / o;
        jQuery("#" + e + " .r1").val(calc_finance_round(Math.round(100 * i) / 100, t)), jQuery("#" + e + " .r2").val(calc_finance_round(Math.round(100 * (i / 2)) / 100, t));
        var u = Math.round(100 * (i * c - r)) / 100,
            s = u / (c - 3),
            _ = Math.round(100 * (u - s)) / 100;
        jQuery("#" + e + " .r3").val(calc_finance_round(u, t)), jQuery("#" + e + " .r4").val(calc_finance_round(_, t))
    }
}

function calc_finance_deposit(e, a) {
    var t = "1" == jQuery("#" + e + " .round_up_calc").val(),
        r = t ? 0 : 2,
        n = "",
        c = calc_finance_format_float(jQuery("#" + e + " .summa").val()),
        l = calc_finance_format_float(jQuery("#" + e + " .percent").val()),
        o = calc_finance_format_float(jQuery("#" + e + " .srok").val()),
        u = (jQuery("#" + e + " .result").val(), parseInt(jQuery("#" + e + " .was").val())),
        s = parseInt(o),
        _ = parseFloat(l / 12),
        p = 0,
        f = 0;
    if (isNaN(parseFloat(c))) return jQuery("#" + e + " .summa").focus(), !1;
    if (isNaN(parseInt(o))) return jQuery("#" + e + " .srok").focus(), !1;
    var d = jQuery("#" + e + " .textTotal").val(),
        y = jQuery("#" + e + " .textMonth").val();
    n = '<table class="table-schedule"><thead><tr><th>' + y + "</th><th>" + d + "</th></tr></thead><tbody>";
    var v = new Array,
        m = new Array("x"),
        j = new Array(d);
    for (i = 1; s >= i; i++) sum = parseFloat(1 == u ? _ / 100 * (parseFloat(c) + p) : _ / 100 * parseFloat(c)), f = Math.round(1e4 * parseFloat(sum)) / 1e4, p += f, f = calc_finance_round(f.toFixed(2), r), n = n + "<tr><td>" + i + "</td><td> " + f + " " + a + "</td></tr>", v[v.length] = [i, f], m[i] = i, j[i] = calc_finance_round(f, r);
    n += "</table>", p = Math.round(1e4 * parseFloat(p)) / 1e4, sumitog2 = Math.round(1e4 * (p + parseFloat(c))) / 1e4, jQuery("#" + e + " .result-summary").show(), jQuery("#" + e + " .result-summary .op").val(calc_finance_round(sumitog2.toFixed(2), r)), jQuery("#" + e + " .result-summary .profit").val(calc_finance_round(p.toFixed(2), r));
    var h = "1" == jQuery("#" + e + " .export_pdf").val(),
        Q = "1" == jQuery("#" + e + " .export_graph").val();
    jQuery("#" + e + " .resmore").html((h ? '<a class="pdf" style="word-wrap: initial; cursor:pointer;"><span class="pdf_icon" title="PDF" /></a>' : "") + (Q ? '<a class="graph" style="word-wrap: initial; cursor:pointer;"><span class="graph_icon" title="Graph" /></a>' : "") + "&nbsp;" + n), jQuery("#" + e + " .resmore").prop("display", "block"), Q && jQuery("#" + e + " .resmore a.graph").click(function() {
        var a = c3.generate({
            bindto: "#" + e + "_chart",
            data: {
                x: "x",
                columns: [m, j]
            },
            axis: {
                y: {
                    min: 1,
                    padding: {
                        top: 0,
                        bottom: 0
                    }
                }
            },
            color: {
                pattern: ["#3cc88c"]
            },
            tooltip: {
                format: {
                    title: function(e) {
                        return "Month: " + e
                    }
                }
            }
        });
        a.resize({
            height: 400,
            width: 750
        }), a.flush(), jQuery("#" + e + "_chart_modal").dialog({
            dialogClass: "wp-calc-finance-chart",
            width: 800
        })
    }), Q && jQuery("#" + e + " .resmore a.pdf").click(function() {
        var a = function() {
            var a = {
                watermark: {
                    text: jQuery("#" + e + " .watermark").val() + "  ",
                    color: "red",
                    opacity: .3
                },
                content: [{
                    table: {
                        headerRows: 1,
                        widths: ["*", "*"],
                        body: [
                            [y, d].slice(0)
                        ].concat(v)
                    }
                }]
            };
            pdfMake.createPdf(a).download("payments.pdf")
        };
        return "undefined" == typeof pdfMake ? jQuery.getScript(wpcalcfinancei18n.pdfmake_url).done(function() {
            jQuery.getScript(wpcalcfinancei18n.vfsfonts_url).done(function() {
                a()
            })
        }) : a(), !1
    })
}

function calc_finance_osago_ru(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = [
            [1.87, 1.87, 1.87, 1.66, 1.66, 0, 0, 0],
            [1.77, 1.77, 1.77, 1.04, 1.04, 1.04, 0, 0],
            [1.77, 1.69, 1.63, 1.04, 1.04, 1.04, 1.01, 0],
            [1.63, 1.63, 1.63, 1.04, 1.04, 1.01, .96, .96],
            [1.63, 1.63, 1.63, .99, .96, .96, .96, .96],
            [1.63, 1.63, 1.63, .96, .96, .96, .96, .96],
            [1.63, 1.63, 1.63, .96, .96, .96, .96, .96]
        ];
    osago = 0, osago2 = 0, kt_ = 1 * jQuery("#" + e + " .kt").val().split(":")[0], kt_tractor = 1 * jQuery("#" + e + " .kt").val().split(":")[1], km_ = 1 * jQuery("#" + e + " .km").val(), ko_ = 1 * jQuery("#" + e + " .ko").val(), kbm_ = 1 * jQuery("#" + e + " .kbm").val(), kvs_age = 1 * jQuery("#" + e + " .kvs_age").val(), kvs_exp = 1 * jQuery("#" + e + " .kvs_exp").val(), br_ = 1 * jQuery("#" + e + " .br").val(), 0 == kvs_age && kvs_exp > 4 ? kvs_exp = 4 : 1 == kvs_age && kvs_exp > 5 ? kvs_exp = 5 : 2 == kvs_age && kvs_exp > 6 && (kvs_exp = 6), kvs_ = r[kvs_age][kvs_exp], kp_ = 1 * jQuery("#" + e + " .kp").val(), ks_ = 1 * jQuery("#" + e + " .ks").val(), tb_ = 1 * jQuery("#" + e + " .tb").val().split(":")[2];
    var n = 1 * jQuery("#" + e + " .owner").val(),
        c = 1 * jQuery("#" + e + " .ins").val(),
        l = 1 * jQuery("#" + e + " .pr").val() == 1,
        o = jQuery("#" + e + " .tb option").index(jQuery("#" + e + " .tb option:selected"));
    if (10 == o && (kt_ = kt_tractor), l && (1 == o && 1.7 == n || 0 == o)) var i = 1.16;
    else if (l && 1 == o && 1 == n) var i = 1;
    else if (l && 3 == o) var i = 1.4;
    else if (l && 4 == o) var i = 1.25;
    else if (l && 10 == o) var i = 1.24;
    else var i = 1;
    1 == n ? (tb1 = 1 * jQuery("#" + e + " .tb").val().split(":")[br_], 1 == tb_ ? 1 == ko_ ? (jQuery("#" + e + " .kvs").prop("disabled", !1), jQuery("#" + e + " .kvs").css("background", "#ffffff"), osago = tb1 * kt_ * kvs_ * km_ * ks_ * kbm_ * kp_ * c * i) : (jQuery("#" + e + " .kvs").prop("disabled", !0), jQuery("#" + e + " .kvs").css("background", "#ccc"), osago = tb1 * kt_ * ko_ * km_ * ks_ * kbm_ * kp_ * c * i) : osago = 1 == ko_ ? tb1 * kt_ * kvs_ * ks_ * kbm_ * kp_ * c * i : tb1 * kt_ * ko_ * ks_ * kbm_ * kp_ * c * i) : (tb1 = 1 * jQuery("#" + e + " .tb").val().split(":")[br_], 1 == tb_ && (tb1 = 0 == br_ ? 2911 : 2058), jQuery("#" + e + " .kvs").prop("disabled", !0), calc_finance_switch_btn_click(jQuery("#" + e + " .unlimited"), 1.8, "ko", e), jQuery("#" + e + " .koval").text(jQuery("#" + e + " .ko").val()), ko_ = 1 * jQuery("#" + e + " .ko").val(), osago = 1 == tb_ ? tb1 * kt_ * ko_ * km_ * ks_ * kbm_ * kp_ * c * i : tb1 * kt_ * ko_ * ks_ * kbm_ * kp_ * c * i), osago = osago.toFixed(2), jQuery("#" + e + " input.result").val(calc_finance_round(osago, t))
}

function calc_finance_osago_ru_pre_2009(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2;
    osago = 0, osago2 = 0, kt_ = 1 * jQuery("#" + e + " .kt").val(), km_ = 1 * jQuery("#" + e + " .km").val(), ko_ = 1 * jQuery("#" + e + " .ko").val(), kbm_ = 1 * jQuery("#" + e + " .kbm").val(), kvs_ = 1 * jQuery("#" + e + " .kvs").val(), kp_ = 1 * jQuery("#" + e + " .kp").val(), ks_ = 1, tb_ = 1 * jQuery("#" + e + " .tb").val().split(":")[2], "1" == jQuery("#" + e + " .owner").val() ? (tb1 = 1 * jQuery("#" + e + " .tb").val().split(":")[0], 1 == tb_ ? 1 == ko_ ? (jQuery("#" + e + " .kvs").prop("disabled", !1), jQuery("#" + e + " .kvs").css("background", "#ffffff"), osago = tb1 * kt_ * kvs_ * km_ * ks_ * kbm_ * kp_) : (jQuery("#" + e + " .kvs").prop("disabled", !0), jQuery("#" + e + " .kvs").css("background", "#ccc"), osago = tb1 * kt_ * ko_ * km_ * ks_ * kbm_ * kp_) : osago = 1 == ko_ ? tb1 * kt_ * kvs_ * ks_ * kbm_ * kp_ : tb1 * kt_ * ko_ * ks_ * kbm_ * kp_) : (tb1 = 1 * jQuery("#" + e + " .tb").val().split(":")[1], jQuery("#" + e + " .kvs").prop("disabled", !0), calc_finance_switch_btn_click(jQuery("#" + e + " .unlimited"), 1.8, "ko", e), jQuery("#" + e + " .koval").text(jQuery("#" + e + " .ko").val()), ko_ = 1 * jQuery("#" + e + " .ko").val(), osago = 1 == tb_ ? tb1 * kt_ * ko_ * km_ * ks_ * kbm_ * kp_ : tb1 * kt_ * ko_ * ks_ * kbm_ * kp_), osago = osago.toFixed(2), jQuery("#" + e + " input.result").val(calc_finance_round(osago, t)), jQuery("#" + e + " .tbval").text("1" == jQuery("#" + e + " .owner").val() ? jQuery("#" + e + " .tb").val().split(":")[0] : jQuery("#" + e + " .tb").val().split(":")[1])
}

function calc_finance_osago_ua(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = jQuery("#" + e + " .f1").val(),
        n = jQuery("#" + e + " .f2").val(),
        c = jQuery("#" + e + " .f3").val(),
        l = jQuery("#" + e + " .f4").val(),
        o = jQuery("#" + e + " .f5").val(),
        i = jQuery("#" + e + " .f6").val(),
        u = (jQuery("#" + e + " .f2 option:selected").text(), jQuery("#" + e + " .f3 option:selected").text(), jQuery("#" + e + " .f4 option:selected").text(), jQuery("#" + e + " .f5 option:selected").text(), jQuery("#" + e + " .f6 option:selected").text(), r * n * c * l * o * i),
        s = Math.round(100 * u) / 100;
    jQuery("#" + e + " input.result").val(calc_finance_round(s.toFixed(2), t))
}

function calc_finance_vat(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = jQuery("#" + e + " .inVatAction").val(),
        n = calc_finance_format_float(jQuery("#" + e + " .inSumm").val()),
        c = calc_finance_format_float(jQuery("#" + e + " .inInterest").val());
    if (1 == r) {
        var l = (n / (c / 100 + 1)).toFixed(2),
            o = (n - l).toFixed(2);
        jQuery("#" + e + " input.sum").val(calc_finance_round(n, t)), jQuery("#" + e + " input.vat").val(calc_finance_round(o, t)), jQuery("#" + e + " input.sumWithoutVat").val(calc_finance_round(l, t))
    }
    if (2 == r) {
        var o = (n * c / 100).toFixed(2),
            l = (1 * n + 1 * o).toFixed(2);
        jQuery("#" + e + " input.sum").val(calc_finance_round(l, t)), jQuery("#" + e + " input.vat").val(calc_finance_round(o, t)), jQuery("#" + e + " input.sumWithoutVat").val(calc_finance_round(n, t))
    }
}

function calc_finance_guarantee_fee_ru(e) {
    var a = "1" == jQuery("#" + e + " .round_up_calc").val(),
        t = a ? 0 : 2,
        r = jQuery("#" + e + " .l").val(),
        n = jQuery("#" + e + " .n").val(),
        c = Math.ceil(n / 30),
        l = 0,
        o = r > 18e6 && c > 24,
        i = .0027,
        u = {
            "3+": !1,
            prepayment: !1,
            "185-FZ": !1,
            "223-FZ": !1,
            "contract-term+": !1,
            "perf-analysis+": !1
        };
    try {
        u = {
            "3+": jQuery("#" + e + " .addCond1").prop("checked"),
            prepayment: jQuery("#" + e + " .addCond2").prop("checked"),
            "185-FZ": jQuery("#" + e + " .addCond3").prop("checked"),
            "223-FZ": jQuery("#" + e + " .addCond4").prop("checked"),
            "contract-term+": jQuery("#" + e + " .addCond5").prop("checked"),
            "perf-analysis+": jQuery("#" + e + " .addCond6").prop("checked")
        }
    } catch (s) {}
    var _ = {},
        p = [
            [0, 5e4],
            [50001, 1e5],
            [100001, 15e4],
            [150001, 2e5],
            [200001, 3e5],
            [300001, 4e5],
            [400001, 5e5],
            [500001, 6e5],
            [600001, 7e5],
            [700001, 8e5],
            [800001, 9e5],
            [900001, 1e6]
        ],
        f = [
            [0, 4],
            [4, 6],
            [6, 8],
            [8, 10],
            [10, 12]
        ],
        d = [
            [1950, 1950, 1950, 1950, 1950],
            [2500, 2700, 3100, 3500, 3800],
            [4e3, 4200, 4500, 5100, 5600],
            [6e3, 6200, 6400, 6900, 7700],
            [8500, 8700, 9100, 10200, 11200],
            [10500, 11500, 12400, 14e3, 15500],
            [12200, 13500, 15200, 17100, 19e3],
            [13e3, 14200, 18e3, 20500, 22700],
            [14500, 16500, 19e3, 23700, 26400],
            [17500, 20900, 23500, 26900, 29800],
            [19800, 22500, 27200, 30600, 34e3],
            [22e3, 25e3, 30400, 34200, 36e3]
        ],
        y = 0,
        v = 0,
        m = 0;
    if (o) {
        for (_ = {
                "3+": .1,
                prepayment: .15,
                "185-FZ": .1,
                "223-FZ": .05,
                "contract-term+": .15,
                "perf-analysis+": .05
            }, m = 0; m < f.length; m++)
            if (c > f[m][0] && c <= f[m][1]) {
                v = m;
                break
            } for (m = 0; m < p.length; m++)
            if (r >= p[m][0] && r <= p[m][1]) {
                y = m;
                break
            }
    } else {
        _ = {
            "3+": .1,
            prepayment: .1,
            "185-FZ": .05,
            "223-FZ": .1,
            "contract-term+": .15,
            "perf-analysis+": .05
        }, p = [
            [0, 5e4],
            [5e4, 1e5],
            [1e5, 15e4],
            [15e4, 2e5],
            [2e5, 3e5],
            [3e5, 4e5],
            [4e5, 5e5],
            [5e5, 6e5],
            [6e5, 7e5],
            [7e5, 8e5],
            [8e5, 9e5],
            [9e5, 1e6],
            [1e6, 18e6]
        ];
        var d = [
            [1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950],
            [2500, 2500, 2500, 2500, 2700, 2700, 3100, 3100, 3500, 3500, 3500, 3500],
            [4e3, 4e3, 4e3, 4e3, 4200, 4200, 4500, 4500, 4500, 4500, 4500, 4500],
            [4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900],
            [4900, 4900, 4900, 4900, 4900, 4900, 4900, 4900, 5500, 5500, 6500, 6500],
            [4900, 4900, 4900, 4900, 5200, 5200, 6800, 6800, 8400, 8400, 9300, 9300],
            [4900, 4900, 4900, 4900, 6900, 6900, 9e3, 9e3, 11500, 11500, 13500, 13500],
            [9900, 9900, 9900, 9900, 9900, 9900, 11500, 11500, 14e3, 14e3, 17e3, 17e3],
            [9900, 9900, 9900, 9900, 1e4, 1e4, 13500, 13500, 17e3, 17e3, 2e4, 2e4],
            [9900, 9900, 9900, 9900, 11300, 11300, 16e3, 16e3, 2e4, 2e4, 23500, 23500],
            [9900, 9900, 9900, 9900, 13800, 13800, 18e3, 18e3, 23e3, 23e3, 27500, 27500],
            [10400, 10400, 10400, 10400, 15600, 15600, 2e4, 2e4, 26e3, 26e3, 31e3, 31e3],
            [15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3, 15e3]
        ];
        for (v = c - 1, m = 0; m < p.length; m++)
            if (r > p[m][0] && r <= p[m][1]) {
                y = m;
                break
            }
    }
    var j = {},
        h = 0;
    for (var Q in u) u.hasOwnProperty(Q) && (j[Q] = u[Q] ? _[Q] : 0, h += j[Q]);
    var b = r * i * c,
        k = b * (1 + h),
        x = d[y][v];
    if (o) {
        var g = 25e3,
            w = 12 >= c && 1e6 >= r ? x : g;
        l = w > k && 12 >= c ? w : k
    } else l = x > k ? x : k;
    o || (l -= l / 100 * 10), jQuery("#" + e + " .op").val(calc_finance_dis_num(l, t, ".", " "))
}

function calc_finance_dis_num(a, t, r, n) {
    if (a = Math.round(a * Math.pow(10, t)) / Math.pow(10, t), e = a + "", f = e.split("."), f[0] || (f[0] = "0"), f[1] || (f[1] = ""), f[1].length < t) {
        for (g = f[1], i = f[1].length + 1; t >= i; i++) g += "0";
        f[1] = g
    }
    if ("" != n && f[0].length > 3) {
        for (h = f[0], f[0] = "", j = 3; j < h.length; j += 3) i = h.slice(h.length - j, h.length - j + 3), f[0] = n + i + f[0] + "";
        j = h.substr(0, h.length % 3 == 0 ? 3 : h.length % 3), f[0] = j + f[0]
    }
    return r = 0 >= t ? "" : r, f[0] + r + f[1]
}

function calc_finance_round(e, a) {
    var t = Math.round(e * Math.pow(10, a));
    return t = 0 > t ? "" : t.toString(), t = t.substring(0, t.length - a) + (a > 0 ? "." + t.substring(t.length - a, t.length) : ""), "." == t.charAt(0) && (t = "0" + t), "." == t.charAt(t.length - 1) && (t += "0"), t
}

function calc_finance_cn(e) {
    var a = e.value;
    isNaN(a) && (a = a.substring(0, a.length - 1), e.value = a)
}

function calc_finance_switch_btn_click(e, a, t, r) {
    jQuery("#" + r + " ." + t).val(a);
    var n = jQuery(e);
    n.parent().children().each(function() {
        jQuery(this).removeClass("switcher__button--active")
    }), n.addClass("switcher__button--active")
}

function calc_finance_format_money(e, a) {
    thousand_sep = " ", decimal_sep = ".";
    var t = e.toFixed(a).split(".");
    return t[0].split("").reverse().reduce(function(e, a, t) {
        return "-" == a ? e : a + (!t || t % 3 ? "" : thousand_sep) + e
    }, "") + (t.length > 1 ? decimal_sep + t[1] : "")
}

function calc_finance_newtonRaphson(e, a, t) {
    function r(r) {
        return e * r * Math.pow(1 + r, t) / (Math.pow(1 + r, t) - 1) - a
    }

    function n(a) {
        return e * (Math.pow(1 + a, t) / (-1 + Math.pow(1 + a, t)) - t * a * Math.pow(1 + a, -1 + 2 * t) / Math.pow(-1 + Math.pow(1 + a, t), 2) + t * a * Math.pow(1 + a, -1 + t) / (-1 + Math.pow(1 + a, t)))
    }
    var c, l = Math.pow(10, -5),
        o = .05 / 12;
    for (k = 0; 20 > k; ++k) {
        var c = o;
        o = c - r(c) / n(c);
        var i = Math.abs(o - c);
        if (l > i) break
    }
    return rate = Math.round(12 * o * 1e4) / 100
}
jQuery(document).ready(function() {
    ! function(e) {
        e.fn.numeric = function(a, t) {
            "boolean" == typeof a && (a = {
                decimal: a
            }), a = a || {}, "undefined" == typeof a.negative && (a.negative = !0);
            var r = a.decimal === !1 ? "" : a.decimal || ".",
                n = a.negative === !0 ? !0 : !1,
                t = "function" == typeof t ? t : function() {};
            return this.data("numeric.decimal", r).data("numeric.negative", n).data("numeric.callback", t).keyup(e.fn.numeric.keyup)
        }, e.fn.numeric.keyup = function() {
            var a = this.value;
            if (a.length > 0) {
                var t = e.fn.getSelectionStart(this),
                    r = e.data(this, "numeric.decimal"),
                    n = e.data(this, "numeric.negative");
                if ("" != r) {
                    var c = a.indexOf(r);
                    0 == c && (this.value = "0" + a), 1 == c && "-" == a.charAt(0) && (this.value = "-0" + a.substring(1)), a = this.value
                }
                for (var l = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "-", r], o = a.length, i = o - 1; i >= 0; i--) {
                    var u = a.charAt(i);
                    0 != i && "-" == u ? a = a.substring(0, i) + a.substring(i + 1) : 0 != i || n || "-" != u || (a = a.substring(1));
                    for (var s = !1, _ = 0; _ < l.length; _++)
                        if (u == l[_]) {
                            s = !0;
                            break
                        } s && " " != u || (a = a.substring(0, i) + a.substring(i + 1))
                }
                var p = a.indexOf(r);
                if (p > 0)
                    for (var i = o - 1; i > p; i--) {
                        var u = a.charAt(i);
                        u == r && (a = a.substring(0, i) + a.substring(i + 1))
                    }
                this.value = a, e.fn.setSelection(this, t)
            }
        }, e.fn.getSelectionStart = function(e) {
            if (e.createTextRange) {
                var a = document.selection.createRange().duplicate();
                return a.moveEnd("character", e.value.length), "" == a.text ? e.value.length : e.value.lastIndexOf(a.text)
            }
            return e.selectionStart
        }, e.fn.setSelection = function(e, a) {
            if ("number" == typeof a && (a = [a, a]), a && a.constructor == Array && 2 == a.length)
                if (e.createTextRange) {
                    var t = e.createTextRange();
                    t.collapse(!0), t.moveStart("character", a[0]), t.moveEnd("character", a[1]), t.select()
                } else e.setSelectionRange && (e.focus(), e.setSelectionRange(a[0], a[1]))
        }
    }(jQuery)
});