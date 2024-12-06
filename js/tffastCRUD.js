var tffastCRUD = {};
tffastCRUD.table = function(myParams){
    myParams.opts.apiExtraQS = myParams.opts.apiExtraQS ? myParams.opts.apiExtraQS : "";
    myParams.tableParams = {
        onMakeDataUrl: function(params){
            return myParams.opts.makeDataUrl ? myParams.opts.makeDataUrl.call(this, params) : myParams.opts.apiPrefix + "/_search?pn=" + params.pn + "&sorts=" + params.sorts + ((myParams.opts.apiExtraQS) ? "&" + myParams.opts.apiExtraQS : "") + "&" + myParams.o.find("form").serialize();
        },
        onRetrieveData: function(data){
            for(var i=0;i<data.data.length;i++) data.data[i] = myParams.opts.makeRowData ? myParams.opts.makeRowData.call(this, data.data[i]) : data.data[i];
            return data;
        },
        onCreatePageButton: function(params){
            return myParams.opts.onCreatePageButton ? myParams.opts.onCreatePageButton.call(this, params) : "<li><a data-pn=\"" + params.pn + "\">" + params.label + "</a></li>";
        },
        afterProcessSuccess: function(data){
            if(myParams.opts.afterProcessSuccess) myParams.opts.afterProcessSuccess.call(this, data);
            myParams.o.find("[data-action]").click(function (){
                myParams.currClickAction = $(this).attr("data-action");
                myParams.self["doAction_" + myParams.currClickAction] ? myParams.self["doAction_" + myParams.currClickAction].call(myParams.self, myParams) : myParams.self.dialogForm(myParams.currClickAction, {}, myParams);
            });
            myParams.o.find("[data-row-action]").click(function(){
                myParams.currClickRowAction = $(this).attr("data-row-action");
                myParams.currClickRowID = $(this).attr("data-row-id");
                myParams.self["doRowAction_" + myParams.currClickRowAction] ? myParams.self["doRowAction_" + myParams.currClickRowAction].call(myParams.self, myParams.currClickRowID, myParams) : myParams.self.dialogFormWithID(myParams.currClickRowAction, {}, myParams.currClickRowID, myParams);
            });
        },
        textPageButtonHome: "home",
        textPageButtonPrevious: "previous",
        textPageButtonNext: "next",
        textPageButtonLast: "last",
        textPageInfo: "<li>total {total}, percent page {percentpage}, pages {totalpage}, current {currentpage}</li>"
    };
    for(var p in myParams.opts) if(!(myParams.opts[p] instanceof Function)) myParams.tableParams[p] = myParams.opts[p];
    if(myParams.opts.onDrawTableData) myParams.tableParams.onDrawTableData = myParams.opts.onDrawTableData;
    myParams.table = myParams.o.tftable(myParams.tableParams);
    myParams.o.tfform({
        onSubmitForm(){
            myParams.table.load();
            return false;
        }
    });
};
tffastCRUD.form = function(action, params, myParams){
    myParams.form = $(".form-" + action).tfform({
        onMakeFormAction: function(){
            return params.action ? params.action : myParams.opts.apiPrefix + "/_" + action + ((myParams.opts.apiExtraQS) ? "?" + myParams.opts.apiExtraQS : "");
        },
        onMakeFormMethod: function(){
            return params.method ? params.method : "post";
        },
        validateRules: params.validateRules ? params.validateRules : [],
        onProcessSuccess: function(data){
            if(data.errcode === 0 && data.errmsg === "OK"){
                myParams.table.refresh();
                if(!myParams.noResetForm) this.trigger("reset");
            }
            else this.tftips({text: data.errmsg});
            if(myParams.onSubmitForm) myParams.onSubmitForm.call(this, data);
        },
        onPostValidateError: function(rule){
            this.tftips({text: rule.errmsg});
            if(rule.name) this.find('[name="'+rule.name+'"]').focus();
        }
    });
};
tffastCRUD.formWithID = function(action, params, id, myParams){
    $.get(myParams.opts.apiPrefix + "/" + id + "/_detail?" + myParams.opts.apiExtraQS, function(data){
        if(myParams.onGetDetail) myParams.onGetDetail.call($(".form-" + action).find("form"), data);
        myParams.formData = data;
        myParams.form = $(".form-" + action).tfform({
            onMakeFormAction: function(){
                return params.action ? params.action : myParams.opts.apiPrefix + "/" + id + "/_" + action + ((myParams.opts.apiExtraQS) ? "?" + myParams.opts.apiExtraQS : "");
            },
            onMakeFormMethod: function(){
                return params.method ? params.method : "post";
            },
            validateRules: params.validateRules ? params.validateRules : [],
            defaultData: params.makeDefaultData ? params.makeDefaultData.call(myParams.self, myParams.dialogFormData) : myParams.dialogFormData,
            onProcessSuccess: function(data){
                if(data.errcode === 0 && data.errmsg === "OK"){
                    myParams.table.refresh();
                    if(!myParams.noResetForm) this.trigger("reset");
                }
                else this.tftips({text: data.errmsg});
                if(myParams.onSubmitForm) myParams.onSubmitForm.call(this, data);
            },
            onPostValidateError: function(rule){
                this.tftips({text: rule.errmsg});
                if(rule.name) this.find('[name="'+rule.name+'"]').focus();
            }
        });
    });
};
tffastCRUD.dialogForm = function(action, params, myParams){
    myParams.dialogForm = $(".form-" + action).tfdialog({
        onShow: function(){
            this.tfform({
                onMakeFormAction: function(){
                    return params.action ? params.action : myParams.opts.apiPrefix + "/_" + action + ((myParams.opts.apiExtraQS) ? "?" + myParams.opts.apiExtraQS : "");
                },
                onMakeFormMethod: function(){
                    return params.method ? params.method : "post";
                },
                validateRules: params.validateRules ? params.validateRules : [],
                onProcessSuccess: function(data){
                    if(data.errcode === 0 && data.errmsg === "OK"){
                        myParams.table.refresh();
                        myParams.dialogForm.hide();
                        this.trigger("reset");
                    }
                    else this.tftips({text: data.errmsg});
                    if(myParams.onSubmitForm) myParams.onSubmitForm.call(this, data);
                },
                onPostValidateError: function(rule){
                    this.tftips({text: rule.errmsg});
                    if(rule.name) this.find('[name="'+rule.name+'"]').focus();
                }
            });
        }
    });
};
tffastCRUD.dialogFormWithID = function(action, params, id, myParams){
    $.get(myParams.opts.apiPrefix + "/" + id + "/_detail?" + myParams.opts.apiExtraQS, function(data){
        if(myParams.onGetDetail) myParams.onGetDetail.call($(".form-" + action).find("form"), data);
        myParams.dialogFormData = data;
        myParams.dialogForm = $(".form-" + action).tfdialog({
            onShow: function(){
                this.tfform({
                    onMakeFormAction: function(){
                        return params.action ? params.action : myParams.opts.apiPrefix + "/" + id + "/_" + action + ((myParams.opts.apiExtraQS) ? "?" + myParams.opts.apiExtraQS : "");
                    },
                    onMakeFormMethod: function(){
                        return params.method ? params.method : "post";
                    },
                    validateRules: params.validateRules ? params.validateRules : [],
                    defaultData: params.makeDefaultData ? params.makeDefaultData.call(myParams.self, myParams.dialogFormData) : myParams.dialogFormData,
                    onProcessSuccess: function(data){
                        if(data.errcode === 0 && data.errmsg === "OK"){
                            myParams.table.refresh();
                            myParams.dialogForm.hide();
                            this.trigger("reset");
                        }
                        else this.tftips({text: data.errmsg});
                        if(myParams.onSubmitForm) myParams.onSubmitForm.call(this, data);
                    },
                    onPostValidateError: function(rule){
                        this.tftips({text: rule.errmsg});
                        if(rule.name) this.find('[name="'+rule.name+'"]').focus();
                    }
                });
            }
        });
    });
};
tffastCRUD.dialogFormWithIDList = function(action, params, idList, myParams){
    $.get(myParams.opts.apiPrefix + "/" + idList + "/_list_detail?" + myParams.opts.apiExtraQS, function(data){
        if(myParams.onGetDetail) myParams.onGetDetail.call($(".form-" + action).find("form"), data);
        myParams.dialogFormData = data;
        myParams.dialogForm = $(".form-" + action).tfdialog({
            onShow: function(){
                this.tfform({
                    onMakeFormAction: function(){
                        return params.action ? params.action : myParams.opts.apiPrefix + "/" + idList + "/_" + action + ((myParams.opts.apiExtraQS) ? "?" + myParams.opts.apiExtraQS : "");
                    },
                    onMakeFormMethod: function(){
                        return params.method ? params.method : "post";
                    },
                    validateRules: params.validateRules ? params.validateRules : [],
                    defaultData: params.makeDefaultData ? params.makeDefaultData.call(myParams.self, myParams.dialogFormData) : myParams.dialogFormData,
                    onProcessSuccess: function(data){
                        if(data.errcode === 0 && data.errmsg === "OK"){
                            myParams.table.refresh();
                            myParams.dialogForm.hide();
                            this.trigger("reset");
                        }
                        else this.tftips({text: data.errmsg});
                        if(myParams.onSubmitForm) myParams.onSubmitForm.call(this, data);
                    },
                    onPostValidateError: function(rule){
                        this.tftips({text: rule.errmsg});
                        if(rule.name) this.find('[name="'+rule.name+'"]').focus();
                    }
                });
            }
        });
    });
};
tffastCRUD.init = function($dataBox, options){
    this.table({o: $dataBox, self: this, opts: options});
};
tffastCRUD.init.prototype = tffastCRUD;