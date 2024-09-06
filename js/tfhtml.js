/**
 * tftable
 *
 * options:
 *   variables:
 *     qsHead
 *     qsHeadFields
 *     qsBodyBox
 *     qsBodyRows
 *     qsBodyRowFields
 *     qsPageBox
 *     qsPageButtons
 *
 *   functions:
 *     onMakeDataUrl: function(params)
 *     onMakeDataMethod: function()
 *     onMakeDataData: function()
 *     onProcessSuccess: function(data)
 *     onProcessError: function(xhr, status, error)
 *     onDrawData: function(data)
 *     onCreateBodyRow: function()
 *     onCreateBodyRowField: function()
 *     onDrawHead: function(params)
 *     onDrawPage: function(params)
 *     onCreatePageButton: function(params)
 *
 * public:
 *   load
 *   getDataUrlPN: function()
 *   getDataUrlSorts: function()
 *   sort
 *   page
 */
(function($){
    var createElement = function(tag){
        return $(document.createElement(tag));
    }, hasDataAttr = function(o, key){
        return o.hasAttribute("data-" + key);
    }, getDataAttr = function(o, key){
        return o.getAttribute("data-" + key);
    }, initSortData = function(hfs, sfs, sfvs){
        hfs.each(function(i, o){
            if((s = hasDataAttr(o, "sortable")) && (sf = getDataAttr(o, "sort-field"))){
                sfs[sf] = o;
                sfvs[sf] = getDataAttr(o, "sort-value");
                sfvs[sf] = sfvs[sf] == null ? "" : sfvs[sf];
            }
        });
    }, initPageNumber = function(pn){

    }, loadData = function(params){
        _loadData(params.a.onMakeDataUrl, params.a.onMakeDataMethod, params.a.onMakeDataData,
            params.a.onProcessSuccess, params.a.onProcessError,
            params.a.onDrawData, params.a.onCreateBodyRow, params.a.onCreateBodyRowField,
            params.a.onDrawHead, params.a.onDrawPage, params.a.onCreatePageButton, params.a.onDrawTable,
            params.o, params.s);
    }, _loadData = function(f1, f2, f3,
                            f4, f5,
                            f6, f7, f8,
                            f9, f10, f11, f12,
                            o, s){
        $.ajax({
            url: makeDataUrl(f1, o, {pn: s.getDataUrlPN(), sorts: s.getDataUrlSorts()}),
            method: makeDataMethod(f2, o),
            data: makeDataData(f3, o),
            success: function(d){
                return f4 ? f4.call(o, d) : processSuccess(f6, f7, f8, f9, f10, f11, f12, o, s, d) ;
            },
            error: function(xhr, st, err){
                return f5 ? f5.call(o, xhr, st, err) : processError(xhr, st, err) ;
            }
        });
    }, makeDataUrl = function(f1, o, p){
        return f1 ? f1.call(o, p) : "data/table.json?pn=" + p.pn + "&sorts=" + p.sorts;
    }, makeDataMethod = function(f1, o){
        return f1 ? f1.call(o) : "get";
    }, makeDataData = function(f1, o){
        return f1 ? f1.call(o) : {};
    }, processSuccess = function(f1, f2, f3, f4, f5, f6, f7, o, s, d){
        drawData(f1, f2, f3, f4, f5, f6, f7, o, s.params.a, d);
        eventSort(s, s.params.a);
        eventPage(s, s.params.a);
    }, processError = function(xhr, st, err){
        console.log(xhr, st, err);
    }, drawData = function(f1, f2, f3, f4, f5, f6, f7, o, a, d){
        if(f1) f1.call(o, d);
        else{
            drawTableHead(f4, o, a);
            drawTableData(f7, f2, f3, o, a, o.find(a.qsbb), d.data);
            drawTablePage(f5, f6, o, a, o.find(a.qspb), d.pagination);
        }
    }, drawTableHead = function(f1, o, a){
        if(f1) f1.call(o, a);
        else{
            for(var f in a.sfvs){
                var $f = $(a.sfs[f]), fv = a.sfvs[f];
                $f.find("i").remove();
                if (fv === "desc") $f.append("<i>(desc)</i>");
                else if (fv === "asc") $f.append("<i>(asc)</i>");
            }
        }
    }, drawTableData = function(f1, f2, f3, o, a, tb, d){
        if(f1) f1.call(o, {data: d});
        else{
            var i, f, tr, td;
            tb.find(a.qsbrs).not(a.qspb).remove();
            for(i=0;i<d.length;i++){
                j = 0;
                tr = createBodyRow(f2, o);
                for(f in d[i]){
                    td = createBodyRowField(f3, o);
                    td.html(d[i][f]);
                    tr.append(td);
                    j++;
                }
                tb.append(tr);
            }
        }
    }, createBodyRow = function(f1, o){
        return f1 ? f1.call(o) : createElement("TR");
    }, createBodyRowField = function(f1, o){
        return f1 ? f1.call(o) : createElement("TD");
    }, createPageButton = function(f1, o, pn, label){
        return f1 ? f1.call(o, {pn: pn, label: label}) : createElement("A").attr("data-pn", pn).html(label);
    }, replacePageData = function(info, pg){
        for(var p in pg){
            info = info.replace("{" + p + "}", pg[p]);
        }
        return info;
    }, drawTablePage = function(f1, f2, o, a, pb, pg){
        if(f1) f1.call(o, {page: pg});
        else{
            pb.children().remove();
            pb.append(createPageButton(f2, o, 1, a.textPageButtonHome || "home"));
            pb.append(createPageButton(f2, o, pg.currentpage-1, a.textPageButtonPrevious || "previous"));
            pb.append(createPageButton(f2, o, pg.currentpage+1, a.textPageButtonNext || "next"));
            pb.append(createPageButton(f2, o, pg.totalpage, a.textPageButtonLast || "last"));
            pb.append(a.textPageInfo ? replacePageData(a.textPageInfo, pg) : replacePageData("<span>total {total}, percent page {percentpage}, total pages {totalpage}</span>", pg));
        }
    }, eventSort = function(s, a){
        s.params.hb.find(a.qshfs).unbind("click").click(function(){
            s.sort(this);
        });
    }, eventPage = function(s, a){
        s.params.pb.find(a.qspis).unbind("click").click(function(){
            s.page(this);
        });
    }, sortData = function(a, sf){
        if(a.sfvs[sf] != null){
            if(a.sfvs[sf] === "asc") a.sfvs[sf] = "desc";
            else if(a.sfvs[sf] === "desc") a.sfvs[sf] = "";
            else a.sfvs[sf] = "asc";
        }
    }, pageNumber = function(a, pn){
        a.pn = pn ? pn : 1;
    }, init = function(params){
        params.s.params = params;
        initSortData(params.hb.find(params.a.qshfs), params.a.sfs = {}, params.a.sfvs = {});
        initPageNumber(params.a.pn = 1);
        loadData(params);
    }, _tfjqp = {
        load : function(){
            this.params.a.pn = 1;
            loadData(this.params);
        },
        refresh : function(){
            loadData(this.params);
        },
        getDataUrlPN : function(){
            return this.params.a.pn;
        },
        getDataUrlSorts : function(){
            return JSON.stringify(this.params.a.sfvs);
        },
        sort : function(e){
            sortData(this.params.a, getDataAttr(e, "sort-field"));
            loadData(this.params);
        },
        page : function(e){
            pageNumber(this.params.a, parseInt(getDataAttr(e, "pn")));
            loadData(this.params);
        },
        init : function(obj, opts){
            opts = opts||{};
            opts.qsh = opts.qsHead ? opts.qsHead : "thead tr";
            opts.qshfs = opts.qsHeadFields ? opts.qsHeadFields : "th";
            opts.qsbb = opts.qsBodyBox ? opts.qsBodyBox : "tbody";
            opts.qsbrs = opts.qsBodyRows ? opts.qsBodyRows : "tr";
            opts.qsbrfs = opts.qsBodyRowFields ? opts.qsBodyRowFields : "td";
            opts.qspb = opts.qsPageBox ? opts.qsPageBox : ".pagination";
            opts.qspis = opts.qsPageItems ? opts.qsPageItems : "a";
            init({s: this, o: $(obj), hb: $(obj).find(opts.qsh), bb: $(obj).find(opts.qsbb), pb: $(obj).find(opts.qspb), a: opts});
        }
    };
    _tfjqp.init.prototype = _tfjqp;
    $.fn.tftable = function(opts){
        return new _tfjqp.init(this, opts);
    };
})(jQuery);

/**
 * tfform
 *
 * options:
 *   variables:
 *     defaultData: {}
 *     dataType
 *     validateRules: []
 *
 *   functions:
 *     onPostValidateError: function(rule)
 *     onMakeFormAction: function()
 *     onMakeFormMethod: function()
 *     onMakeFormData: function()
 *     onProcessSuccess: function(data)
 *     onProcessError: function(xhr, status, error)
 *
 * public:
 *
 */
(function($){
    var setFormData = function(params){
        setFormDefaultData(params.f, params.a.defaultData);
    }, setFormDefaultData = function(f, d){
        for(var n in d) setFormDefaultDataItems(f, f.find('[name="'+n+'"]'), d[n]);
    }, setFormDefaultDataItems = function(f, e, d){
        if(e.length) for(var i=0;i<e.length;i++) setFormDefaultDataItem(f, i, e[i], d);
    }, setFormDefaultDataItem = function(f, i, e, d){
        if(e.type && (e.type === "radio" || e.type === "checkbox")){
            e.checked = false;
            for(var j=0;j<d.length;j++){ if(e.value === d[j]) e.checked = true; }
        }
        else if(e.tagName === "SELECT") for(var j=0;j<e.options.length;j++) setFormDefaultDataItem2(f, j, e.options[j], d);
        else e.value = (d instanceof Array) ? d[i] : d;
    }, setFormDefaultDataItem2 = function(f, j, e, d){
        e.selected = false;
        for(var k=0;k<d.length;k++){ if(e.value === d[k]) e.selected = true; }
    }, submitForm = function(params){
        return (params.a.onSubmitForm) ? params.a.onSubmitForm.call(params.o) : _submitForm(params.a.onMakeFormAction, params.a.onMakeFormMethod, params.a.onMakeFormData,
            params.a.onPostValidateError,
            params.a.onProcessSuccess, params.a.onProcessError,
            params.f, params.a.validateRules, params.a.dataType);
    }, _submitForm = function(f1, f2, f3, f4, f5, f6, f, vrs, dt){
        makeFormData(f3, f);
        if(!validateFormData(f4, f, vrs)) return false;
        var args = {
            url: makeFormAction(f1, f),
            method: makeFormMethod(f2, f),
            data: null,
            contentType: false,
            processData: false,
            success: function(d){
                return f5 ? f5.call(f, d) : processSuccess(d) ;
            },
            error: function(x, s, e){
                return f6 ? f6.call(f, x, s, e) : processError(x, s, e) ;
            }
        };
        if(dt === "json"){
            args.data = JSON.stringify(f.fda);
            args.contentType = "application/json";
        }
        else{
            args.data = f.fd;
        }
        $.ajax(args);
        return true;
    }, validateFormData = function(f1, f, vrs){
        f.fda = {};
        f.fd.forEach(function(v, k){
            if(f.fda[k] == null) f.fda[k] = v;
            else{
                if(!(f.fda[k] instanceof Array)) f.fda[k] = [f.fda[k]];
                f.fda[k].push(v);
            }
        });
        for(var i=0;i<vrs.length;i++) if(!validateFormDataItem(f1, f, vrs[i])) return false;
        return true;
    }, validateFormDataItem = function(f1, f, vr){
        if(typeof f.fda[vr.name] !== "undefined"){
            switch(vr.type){
                case "empty":
                    if((typeof f.fda[vr.name] === "string" && !f.fda[vr.name])
                        || (typeof f.fda[vr.name] === "object" && !f.fda[vr.name].name)){
                        postValidateError(f1, f, vr);
                        return false;
                    }
                    break;
            }
        }
        else{
            switch(vr.type){
                case "unchecked":
                    postValidateError(f1, f, vr);
                    return false;
                case "unselected":
                    postValidateError(f1, f, vr);
                    return false;
            }
        }
        return true;
    }, postValidateError = function(f1, f, vr){
        if(f1) f1.call(f, vr);
        else{
            alert(vr.errmsg);
            f.find('[name="'+vr.name+'"]').focus();
        }
    }, makeFormAction = function(f1, f){
        return f1 ? f1.call(f) : f.attr("action");
    }, makeFormMethod = function(f1, f){
        return f1 ? f1.call(f) : f.attr("method");
    }, makeFormData = function(f1, f){
        return f1 ? (f.fd = f1.call(f)) : (f.fd = new FormData(f[0]));
    }, processSuccess = function(d){
        console.log(d);
    }, processError = function(x, s, e){
        console.log(x, s, e);
    }, init = function(params){
        if(typeof params.f == "undefined"){
            throw ("element 'form' is not found");
        }
        if(params.f.length > 1){
            throw ("elements 'form' are found");
        }
        params.f.fd = params.f.fda = null;
        setFormData(params);
        params.f.unbind().bind("submit", function(){
            submitForm(params);
            return false;
        });
    }, _tfjqp = {
        init : function(obj, opts){
            opts = opts||{};
            opts.validateRules = opts.validateRules||[];
            opts.defaultData = opts.defaultData||{};
            opts.dataType = opts.dataType||"form";
            init({o: $(obj), f: $(obj).find("form"), a: opts});
        }
    };
    _tfjqp.init.prototype = _tfjqp;
    $.fn.tfform = function(opts){
        return new _tfjqp.init(this, opts);
    };
})(jQuery);

/**
 * tfdialog
 *
 * options:
 *   variables:
 *
 *   functions:
 *     onResizeWindow: function()
 *     onShow: function()
 *     onHide: function()
 *
 * public:
 *   show
 *   hide
 *
 */
(function($){
    var createElement = function(t){
        return $(document.createElement(t));
    }, onDialogShow = function(p){
        _onDialogShow(p, p.a.onShow)
    }, _onDialogShow = function(p, f1){
        p.b.css({overflow: "hidden"}).append((p.m = createElement("DIV")).css({display: "block", position: "absolute", left: 0, top: 0, background: "black", opacity: 0.16, zIndex: 1568}));
        p.b.append((p.f = createElement("DIV")).css({display: "block", position: "absolute", zIndex: 1569}));
        p.p = p.o.parent();
        p.a.osd = p.o.css("display");
        p.a.osv = p.o.css("visibility");
        p.f.append(p.o.css({display: "block"}));
        onResize.call(window, p);
        f1 ? f1.call(p.o) : null;
    }, onResize = function(p){
        _onResize(this, p.a.onResizeWindow, p.o, p.m, p.f);
    }, _onResize = function(w, f1, o, m, f){
        m.css({width: w.innerWidth, height: w.innerHeight});
        f.css({left: (w.innerWidth-o.width())/2, top: (w.innerHeight-o.height())/2});
        f1 ? f1.call(o) : null;
    }, onDialogHide = function(params){
        _onDialogHide(params.a.onHide, params.o.css({display: params.a.osd, visibility: params.a.osv}), params.p, params.m, params.f);
    }, _onDialogHide = function(f1, o, p, m, f){
        o.css({display: o.osd, visibility: o.osv});
        p.append(o);
        m.remove();
        f.remove();
        f1 ? f1.call(o) : null;
    }, onButtonClick = function(params){
        _onButtonClick(this, params.s);
    }, _onButtonClick = function(b, s){
        switch(b.getAttribute("data-dialog-button")){
            case "close":
                s.hide();
                break;
        }
    }, init = function(params){
        params.s.params = params;
        params.w.bind("resize", function(){
            onResize.call(this, params);
        });
        params.o.find("[data-dialog-button]").unbind().click(function(){
            onButtonClick.call(this, params);
        });
        params.s.show();
    }, _tfjqp = {
        show : function(){
            onDialogShow(this.params);
        },
        hide : function(){
            onDialogHide(this.params);
        },
        init : function(obj, opts){
            init({s: this, o: $(obj), w: $(window), b: $(document.body), a: opts||{}});
        }
    };
    _tfjqp.init.prototype = _tfjqp;
    $.fn.tfdialog = function(opts){
        return new _tfjqp.init(this, opts);
    };
})(jQuery);

/**
 * tftips
 *
 * options:
 *   variables:
 *     text: ""
 *     class: ""
 *     timeout: 3000
 *
 *   functions:
 *
 * public:
 *
 */
(function($){
    var createElement = function(t){
        return $(document.createElement(t));
    }, onTipsShow = function(params){
        if(window.tftipsT) onTipsHideOther(params);
        window.tftipsT = window.setTimeout(function(){
            onTipsHide(params);
        }, params.a.timeout||3000);
        params.o.append((params.t = createElement("DIV")).addClass((params.a.class) ? "tips " + params.a.class : "tips").text(params.a.text));
        params.t.css({top: (params.o.height()-params.t.height())/2, left: (params.o.width()-params.t.width())/2, zIndex: 1588});
    }, onTipsHideOther = function(params){
        clearTimeout(window.tftipsT);
        window.tftipsT = null;
        params.o.find(".tips").remove();
    }, onTipsHide = function(params){
        params.t.remove();
    }, init = function(params){
        onTipsShow(params);
    }, _tfjqp = {
        init : function(obj, opts){
            init({s: this, o: $(obj), a: opts||{}});
        }
    };
    _tfjqp.init.prototype = _tfjqp;
    $.fn.tftips = function(opts){
        return new _tfjqp.init(this, opts);
    };
})(jQuery);
