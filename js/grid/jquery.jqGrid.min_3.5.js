/* 
* jqGrid  3.5.3 - jQuery Grid 
* Copyright (c) 2008, Tony Tomov, tony@trirand.com 
* Dual licensed under the MIT and GPL licenses 
* http://www.opensource.org/licenses/mit-license.php 
* http://www.gnu.org/licenses/gpl.html 
* Date:2009-09-06 
* Modules: grid.base.js; jquery.fmatter.js; grid.custom.js; grid.common.js; grid.formedit.js; jquery.searchFilter.js; grid.inlinedit.js; grid.celledit.js; jqModal.js; jqDnR.js; grid.subgrid.js; grid.treegrid.js; grid.import.js; JsonXml.js; grid.setcolumns.js; grid.postext.js; grid.tbltogrid.js; 
*/
(function($){
    $.jgrid=$.jgrid||{};$.extend($.jgrid,{
        htmlDecode:function(value){
            if(value=="&nbsp;"||value=="&#160;"||(value.length==1&&value.charCodeAt(0)==160)){
                return""
                }return !value?value:String(value).replace(/&amp;/g,"&").replace(/&gt;/g,">").replace(/&lt;/g,"<").replace(/&quot;/g,'"')
            },
        htmlEncode:function(value){
            return !value?value:String(value).replace(/&/g,"&amp;").replace(/>/g,"&gt;").replace(/</g,"&lt;").replace(/\"/g,"&quot;")
            },
        format:function(format){
            var args=$.makeArray(arguments).slice(1);return format.replace(/\{(\d+)\}/g,function(m,i){
                return args[i]
                })
            },
        getAbsoluteIndex:function(t,rInd){
            var cntnotv=0,cntv=0,cell,i;if($.browser.version>7){
                return rInd
                }for(i=0;i<t.cells.length;i++){
                cell=t.cells(i);if(cell.style.display=="none"){
                    cntnotv++
                }else{
                    cntv++
                }if(cntv>rInd){
                    return i
                    }
                }return i
            },
        stripHtml:function(v){
            var regexp=/<("[^"]*"|'[^']*'|[^'">])*>/gi;if(v){
                return v.replace(regexp,"")
                }else{
                return v
                }
            },
        stringToDoc:function(xmlString){
            var xmlDoc;if(typeof xmlString!=="string"){
                return xmlString
                }try{
                var parser=new DOMParser();xmlDoc=parser.parseFromString(xmlString,"text/xml")
                }catch(e){
                xmlDoc=new ActiveXObject("Microsoft.XMLDOM");xmlDoc.async=false;xmlDoc.loadXML(xmlString)
                }return(xmlDoc&&xmlDoc.documentElement&&xmlDoc.documentElement.tagName!="parsererror")?xmlDoc:null
            },
        parse:function(jsonString){
            var js=jsonString;if(js.substr(0,9)=="while(1);"){
                js=js.substr(9)
                }if(js.substr(0,2)=="/*"){
                js=js.substr(2,js.length-4)
                }if(!js){
                js="{}"
                }with(window){
                return eval("("+js+")")
                }
                },
        empty:function(){
            while(this.firstChild){
                this.removeChild(this.firstChild)
                }
            }
        });$.fn.jqGrid=function(p){
        p=$.extend(true,{
            url:"",
            height:150,
            page:1,
            rowNum:20,
            records:0,
            pager:"",
            pgbuttons:true,
            pginput:true,
            colModel:[],
            rowList:[],
            colNames:[],
            sortorder:"asc",
            sortname:"",
            datatype:"xml",
            mtype:"GET",
            altRows:false,
            selarrrow:[],
            savedRow:[],
            shrinkToFit:true,
            xmlReader:{},
            jsonReader:{},
            subGrid:false,
            subGridModel:[],
            reccount:0,
            lastpage:0,
            lastsort:0,
            selrow:null,
            beforeSelectRow:null,
            onSelectRow:null,
            onSortCol:null,
            ondblClickRow:null,
            onRightClickRow:null,
            onPaging:null,
            onSelectAll:null,
            loadComplete:null,
            gridComplete:null,
            loadError:null,
            loadBeforeSend:null,
            afterInsertRow:null,
            beforeRequest:null,
            onHeaderClick:null,
            viewrecords:false,
            loadonce:false,
            multiselect:false,
            multikey:false,
            editurl:null,
            search:false,
            caption:"",
            hidegrid:true,
            hiddengrid:false,
            postData:{},
            userData:{},
            treeGrid:false,
            treeGridModel:"nested",
            treeReader:{},
            treeANode:-1,
            ExpandColumn:null,
            tree_root_level:0,
            prmNames:{
                page:"page",
                rows:"rows",
                sort:"sidx",
                order:"sord",
                search:"_search",
                nd:"nd"
            },
            forceFit:false,
            gridstate:"visible",
            cellEdit:false,
            cellsubmit:"remote",
            nv:0,
            loadui:"enable",
            toolbar:[false,""],
            scroll:false,
            multiboxonly:false,
            deselectAfterSort:true,
            scrollrows:false,
            autowidth:false,
            scrollOffset:18,
            cellLayout:5,
            subGridWidth:20,
            multiselectWidth:20,
            gridview:false,
            rownumWidth:25,
            rownumbers:false,
            pagerpos:"center",
            recordpos:"right",
            footerrow:false,
            userDataOnFooter:false,
            hoverrows:true,
            altclass:"ui-priority-secondary",
            viewsortcols:[false,"vertical",true],
            resizeclass:"",
            autoencode:false
        },$.jgrid.defaults,p||{});var grid={
            headers:[],
            cols:[],
            footers:[],
            dragStart:function(i,x,y){
                this.resizing={
                    idx:i,
                    startX:x.clientX,
                    sOL:y[0]
                    };this.hDiv.style.cursor="col-resize";this.curGbox=$("#rs_m"+p.id,"#gbox_"+p.id);this.curGbox.css({
                    display:"block",
                    left:y[0],
                    top:y[1],
                    height:y[2]
                    });document.onselectstart=new Function("return false")
                },
            dragMove:function(x){
                if(this.resizing){
                    var diff=x.clientX-this.resizing.startX,h=this.headers[this.resizing.idx],newWidth=h.width+diff,hn,nWn;if(newWidth>33){
                        this.curGbox.css({
                            left:this.resizing.sOL+diff
                            });if(p.forceFit===true){
                            hn=this.headers[this.resizing.idx+p.nv];nWn=hn.width-diff;if(nWn>33){
                                h.newWidth=newWidth;hn.newWidth=nWn;this.newWidth=p.tblwidth
                                }
                            }else{
                            this.newWidth=p.tblwidth+diff;h.newWidth=newWidth
                            }
                        }
                    }
                },
            dragEnd:function(){
                this.hDiv.style.cursor="default";if(this.resizing){
                    var idx=this.resizing.idx,nw=this.headers[idx].newWidth||this.headers[idx].width;this.resizing=false;$("#rs_m"+p.id).css("display","none");p.colModel[idx].width=nw;this.headers[idx].width=nw;this.headers[idx].el.style.width=nw+"px";if(this.cols.length>0){
                        this.cols[idx].style.width=nw+"px"
                        }if(this.footers.length>0){
                        this.footers[idx].style.width=nw+"px"
                        }if(p.forceFit===true){
                        nw=this.headers[idx+p.nv].newWidth||this.headers[idx+p.nv].width;this.headers[idx+p.nv].width=nw;this.headers[idx+p.nv].el.style.width=nw+"px";if(this.cols.length>0){
                            this.cols[idx+p.nv].style.width=nw+"px"
                            }if(this.footers.length>0){
                            this.footers[idx+p.nv].style.width=nw+"px"
                            }p.colModel[idx+p.nv].width=nw
                        }else{
                        p.tblwidth=this.newWidth;$("table:first",this.bDiv).css("width",p.tblwidth+"px");$("table:first",this.hDiv).css("width",p.tblwidth+"px");this.hDiv.scrollLeft=this.bDiv.scrollLeft;if(p.footerrow){
                            $("table:first",this.sDiv).css("width",p.tblwidth+"px");this.sDiv.scrollLeft=this.bDiv.scrollLeft
                            }
                        }
                    }this.curGbox=null;document.onselectstart=new Function("return true")
                },
            scrollGrid:function(){
                if(p.scroll===true){
                    var scrollTop=this.bDiv.scrollTop;if(scrollTop!=this.scrollTop){
                        this.scrollTop=scrollTop;if((this.bDiv.scrollHeight-scrollTop-$(this.bDiv).height())<=0){
                            if(parseInt(p.page,10)+1<=parseInt(p.lastpage,10)){
                                p.page=parseInt(p.page,10)+1;this.populate()
                                }
                            }
                        }
                    }this.hDiv.scrollLeft=this.bDiv.scrollLeft;if(p.footerrow){
                    this.sDiv.scrollLeft=this.bDiv.scrollLeft
                    }
                }
            };return this.each(function(){
            if(this.grid){
                return
            }this.p=p;var i;if(this.p.colNames.length===0){
                for(i=0;i<this.p.colModel.length;i++){
                    this.p.colNames[i]=this.p.colModel[i].label||this.p.colModel[i].name
                    }
                }if(this.p.colNames.length!==this.p.colModel.length){
                alert($.jgrid.errors.model);return
            }var gv=$("<div class='ui-jqgrid-view'></div>"),ii,isMSIE=$.browser.msie?true:false,isSafari=$.browser.safari?true:false;$(gv).insertBefore(this);$(this).appendTo(gv).removeClass("scroll");var eg=$("<div class='ui-jqgrid ui-widget ui-widget-content ui-corner-all'></div>");$(eg).insertBefore(gv).attr("id","gbox_"+this.id);$(gv).appendTo(eg).attr("id","gview_"+this.id);if(isMSIE&&$.browser.version<=6){
                ii='<iframe style="display:block;position:absolute;z-index:-1;filter:Alpha(Opacity=\'0\');" src="javascript:false;"></iframe>'
                }else{
                ii=""
                }$("<div class='ui-widget-overlay jqgrid-overlay' id='lui_"+this.id+"'></div>").append(ii).insertBefore(gv);$("<div class='loading ui-state-default ui-state-active' id='load_"+this.id+"'>"+this.p.loadtext+"</div>").insertBefore(gv);$(this).attr({
                cellSpacing:"0",
                cellPadding:"0",
                border:"0",
                role:"grid",
                "aria-multiselectable":this.p.multiselect,
                "aria-labelledby":"gbox_"+this.id
                });var ts=this,bSR=$.isFunction(this.p.beforeSelectRow)?this.p.beforeSelectRow:false,ondblClickRow=$.isFunction(this.p.ondblClickRow)?this.p.ondblClickRow:false,onSortCol=$.isFunction(this.p.onSortCol)?this.p.onSortCol:false,loadComplete=$.isFunction(this.p.loadComplete)?this.p.loadComplete:false,loadError=$.isFunction(this.p.loadError)?this.p.loadError:false,loadBeforeSend=$.isFunction(this.p.loadBeforeSend)?this.p.loadBeforeSend:false,onRightClickRow=$.isFunction(this.p.onRightClickRow)?this.p.onRightClickRow:false,afterInsRow=$.isFunction(this.p.afterInsertRow)?this.p.afterInsertRow:false,onHdCl=$.isFunction(this.p.onHeaderClick)?this.p.onHeaderClick:false,beReq=$.isFunction(this.p.beforeRequest)?this.p.beforeRequest:false,onSC=$.isFunction(this.p.onCellSelect)?this.p.onCellSelect:false,sortkeys=["shiftKey","altKey","ctrlKey"],IntNum=function(val,defval){
                val=parseInt(val,10);if(isNaN(val)){
                    return defval?defval:0
                    }else{
                    return val
                    }
                },formatCol=function(pos,rowInd){
                var ral=ts.p.colModel[pos].align,result='style="';if(ral){
                    result+="text-align:"+ral+";"
                    }if(ts.p.colModel[pos].hidden===true){
                    result+="display:none;"
                    }if(rowInd===0){
                    result+="width: "+grid.headers[pos].width+"px;"
                    }return result+'"'
                },addCell=function(rowId,cell,pos,irow,srvr){
                var v,prp;v=formatter(rowId,cell,pos,srvr,"add");prp=formatCol(pos,irow);return'<td role="gridcell" '+prp+' title="'+$.jgrid.stripHtml(v)+'">'+v+"</td>"
                },formatter=function(rowId,cellval,colpos,rwdat,_act){
                var cm=ts.p.colModel[colpos],v;if(typeof cm.formatter!=="undefined"){
                    var opts={
                        rowId:rowId,
                        colModel:cm
                    };if($.isFunction(cm.formatter)){
                        v=cm.formatter(cellval,opts,rwdat,_act)
                        }else{
                        if($.fmatter){
                            v=$.fn.fmatter(cm.formatter,cellval,opts,rwdat,_act)
                            }else{
                            v=cellVal(cellval)
                            }
                        }
                    }else{
                    v=cellVal(cellval)
                    }return v
                },cellVal=function(val){
                return val===undefined||val===null||val===""?"&#160;":ts.p.autoencode?$.jgrid.htmlEncode(val+""):val+""
                },addMulti=function(rowid,pos,irow){
                var v='<input type="checkbox" id="jqg_'+rowid+'" class="cbox" name="jqg_'+rowid+'"/>',prp=formatCol(pos,irow);return"<td role='gridcell' "+prp+">"+v+"</td>"
                },addRowNum=function(pos,irow,pG,rN){
                var v=(parseInt(pG)-1)*parseInt(rN)+1+irow,prp=formatCol(pos,irow);return'<td role="gridcell" class="ui-state-default jqgrid-rownum" '+prp+">"+v+"</td>"
                },reader=function(datatype){
                var field,f=[],j=0,i;for(i=0;i<ts.p.colModel.length;i++){
                    field=ts.p.colModel[i];if(field.name!=="cb"&&field.name!=="subgrid"&&field.name!=="rn"){
                        f[j]=(datatype=="xml")?field.xmlmap||field.name:field.jsonmap||field.name;j++
                    }
                    }return f
                },addXmlData=function(xml,t,rcnt){
                var startReq=new Date();ts.p.reccount=0;if($.isXMLDoc(xml)){
                    if(ts.p.treeANode===-1&&ts.p.scroll===false){
                        var tBody=$("tbody:first",t);if(!ts.p.gridview){
                            $("*",tBody).children().unbind()
                            }if(isMSIE){
                            $.jgrid.empty.apply(tBody[0])
                            }else{
                            tBody[0].innerHTML=""
                            }tBody=null;rcnt=0
                        }else{
                        rcnt=rcnt>0?rcnt:0
                        }
                    }else{
                    return
                }var i,fpos,ir=0,v,row,gi=0,si=0,ni=0,idn,getId,f=[],rd={},rl=ts.rows.length,xmlr,rid,rowData=[],ari=0,cn=(ts.p.altRows===true)?ts.p.altclass:"",cn1;if(!ts.p.xmlReader.repeatitems){
                    f=reader("xml")
                    }if(ts.p.keyIndex===false){
                    idn=ts.p.xmlReader.id;if(idn.indexOf("[")===-1){
                        getId=function(trow,k){
                            return $(idn,trow).text()||k
                            }
                        }else{
                        getId=function(trow,k){
                            return trow.getAttribute(idn.replace(/[\[\]]/g,""))||k
                            }
                        }
                    }else{
                    getId=function(trow){
                        return(f.length-1>=ts.p.keyIndex)?$(f[ts.p.keyIndex],trow).text():$(ts.p.xmlReader.cell+":eq("+ts.p.keyIndex+")",trow).text()
                        }
                    }$(ts.p.xmlReader.page,xml).each(function(){
                    ts.p.page=this.textContent||this.text||1
                    });$(ts.p.xmlReader.total,xml).each(function(){
                    ts.p.lastpage=this.textContent||this.text||1
                    });$(ts.p.xmlReader.records,xml).each(function(){
                    ts.p.records=this.textContent||this.text||0
                    });$(ts.p.xmlReader.userdata,xml).each(function(){
                    ts.p.userData[this.getAttribute("name")]=this.textContent||this.text
                    });var gxml=$(ts.p.xmlReader.root+" "+ts.p.xmlReader.row,xml),gl=gxml.length,j=0;if(gxml&&gl){
                    var rn=ts.p.rowNum;while(j<gl){
                        xmlr=gxml[j];rid=getId(xmlr,j+1);cn1=j%2==1?cn:"";rowData[ari++]='<tr id="'+rid+'" role="row" class ="ui-widget-content jqgrow '+cn1+'">';if(ts.p.rownumbers===true){
                            rowData[ari++]=addRowNum(0,j,ts.p.page,ts.p.rowNum);ni=1
                            }if(ts.p.multiselect===true){
                            rowData[ari++]=addMulti(rid,ni,j);gi=1
                            }if(ts.p.subGrid===true){
                            rowData[ari++]=$(ts).addSubGridCell(gi+ni,j+rcnt);si=1
                            }if(ts.p.xmlReader.repeatitems===true){
                            $(ts.p.xmlReader.cell,xmlr).each(function(k){
                                v=this.textContent||this.text;rd[ts.p.colModel[k+gi+si+ni].name]=v;rowData[ari++]=addCell(rid,v,k+gi+si+ni,j+rcnt,xmlr)
                                })
                            }else{
                            for(i=0;i<f.length;i++){
                                v=$(f[i],xmlr).text();rd[ts.p.colModel[i+gi+si+ni].name]=v;rowData[ari++]=addCell(rid,v,i+gi+si+ni,j+rcnt,xmlr)
                                }
                            }rowData[ari++]="</tr>";if(ts.p.gridview===false){
                            if(ts.p.treeGrid===true){
                                fpos=ts.p.treeANode>=-1?ts.p.treeANode:0;row=$(rowData.join(""))[0];try{
                                    $(ts).setTreeNode(rd,row)
                                    }catch(e){}rl===0?$("tbody:first",t).append(row):$(ts.rows[j+fpos+rcnt]).after(row)
                                }else{
                                $("tbody:first",t).append(rowData.join(""))
                                }if(ts.p.subGrid===true){
                                try{
                                    $(ts).addSubGrid(ts.rows[ts.rows.length-1],gi+ni)
                                    }catch(e){}
                                }if(afterInsRow){
                                ts.p.afterInsertRow(rid,rd,xmlr)
                                }rowData=[];ari=0
                            }rd={};ir++;j++;if(rn!=-1&&ir>rn){
                            break
                        }
                        }
                    }if(ts.p.gridview===true){
                    $("table:first",t).append(rowData.join(""))
                    }ts.p.totaltime=new Date()-startReq;if(ir>0){
                    ts.grid.cols=ts.rows[0].cells;if(ts.p.records===0){
                        ts.p.records=gl
                        }
                    }rowData=null;if(!ts.p.treeGrid&&!ts.p.scroll){
                    ts.grid.bDiv.scrollTop=0;ts.p.reccount=ir
                    }ts.p.treeANode=-1;if(ts.p.userDataOnFooter){
                    $(ts).footerData("set",ts.p.userData,true)
                    }updatepager(false)
                },addJSONData=function(data,t,rcnt){
                var startReq=new Date();ts.p.reccount=0;if(data){
                    if(ts.p.treeANode===-1&&ts.p.scroll===false){
                        var tBody=$("tbody:first",t);if(!ts.p.gridview){
                            $("*",tBody).children().unbind()
                            }if(isMSIE){
                            $.jgrid.empty.apply(tBody[0])
                            }else{
                            tBody[0].innerHTML=""
                            }tBody=null;rcnt=0
                        }else{
                        rcnt=rcnt>0?rcnt:0
                        }
                    }else{
                    return
                }var ir=0,v,i,j,row,f=[],cur,gi=0,si=0,ni=0,len,drows,idn,rd={},fpos,rl=ts.rows.length,idr,rowData=[],ari=0,cn=(ts.p.altRows===true)?ts.p.altclass:"",cn1;ts.p.page=data[ts.p.jsonReader.page]||1;ts.p.lastpage=data[ts.p.jsonReader.total]||1;ts.p.records=data[ts.p.jsonReader.records]||0;ts.p.userData=data[ts.p.jsonReader.userdata]||{};if(!ts.p.jsonReader.repeatitems){
                    f=reader("json")
                    }if(ts.p.keyIndex===false){
                    idn=ts.p.jsonReader.id;if(f.length>0&&!isNaN(idn)){
                        idn=f[idn]
                        }
                    }else{
                    idn=f.length>0?f[ts.p.keyIndex]:ts.p.keyIndex
                    }drows=data[ts.p.jsonReader.root];if(drows){
                    len=drows.length,i=0;var rn=ts.p.rowNum;while(i<len){
                        cur=drows[i];idr=cur[idn];if(idr===undefined){
                            if(f.length===0){
                                if(ts.p.jsonReader.cell){
                                    var ccur=cur[ts.p.jsonReader.cell];idr=ccur[idn]||i+1;ccur=null
                                    }else{
                                    idr=i+1
                                    }
                                }else{
                                idr=i+1
                                }
                            }cn1=i%2==1?cn:"";rowData[ari++]='<tr id="'+idr+'" role="row" class= "ui-widget-content jqgrow '+cn1+'">';if(ts.p.rownumbers===true){
                            rowData[ari++]=addRowNum(0,i,ts.p.page,ts.p.rowNum);ni=1
                            }if(ts.p.multiselect){
                            rowData[ari++]=addMulti(idr,ni,i);gi=1
                            }if(ts.p.subGrid){
                            rowData[ari++]=$(ts).addSubGridCell(gi+ni,i+rcnt);si=1
                            }if(ts.p.jsonReader.repeatitems===true){
                            if(ts.p.jsonReader.cell){
                                cur=cur[ts.p.jsonReader.cell]
                                }for(j=0;j<cur.length;j++){
                                rowData[ari++]=addCell(idr,cur[j],j+gi+si+ni,i+rcnt,cur);rd[ts.p.colModel[j+gi+si+ni].name]=cur[j]
                                }
                            }else{
                            for(j=0;j<f.length;j++){
                                v=cur[f[j]];if(v===undefined){
                                    try{
                                        v=eval("cur."+f[j])
                                        }catch(e){}
                                    }rowData[ari++]=addCell(idr,v,j+gi+si+ni,i+rcnt,cur);rd[ts.p.colModel[j+gi+si+ni].name]=cur[f[j]]
                                }
                            }rowData[ari++]="</tr>";if(ts.p.gridview===false){
                            if(ts.p.treeGrid===true){
                                fpos=ts.p.treeANode>=-1?ts.p.treeANode:0;row=$(rowData.join(""))[0];try{
                                    $(ts).setTreeNode(rd,row)
                                    }catch(e){}rl===0?$("tbody:first",t).append(row):$(ts.rows[i+fpos+rcnt]).after(row)
                                }else{
                                $("tbody:first",t).append(rowData.join(""))
                                }if(ts.p.subGrid===true){
                                try{
                                    $(ts).addSubGrid(ts.rows[ts.rows.length-1],gi+ni)
                                    }catch(e){}
                                }if(afterInsRow){
                                ts.p.afterInsertRow(idr,rd,cur)
                                }rowData=[];ari=0
                            }rd={};ir++;i++;if(rn!=-1&&ir>rn){
                            break
                        }
                        }if(ts.p.gridview===true){
                        $("table:first",t).append(rowData.join(""))
                        }ts.p.totaltime=new Date()-startReq;if(ir>0){
                        ts.grid.cols=ts.rows[0].cells;if(ts.p.records===0){
                            ts.p.records=len
                            }
                        }
                    }if(!ts.p.treeGrid&&!ts.p.scroll){
                    ts.grid.bDiv.scrollTop=0;ts.p.reccount=ir
                    }ts.p.treeANode=-1;if(ts.p.userDataOnFooter){
                    $(ts).footerData("set",ts.p.userData,true)
                    }updatepager(false)
                },updatepager=function(rn){
                var cp,last,base,bs,from,to,tot,fmt;base=(parseInt(ts.p.page)-1)*parseInt(ts.p.rowNum);if(ts.p.pager){
                    if(ts.p.loadonce){
                        cp=last=1;ts.p.lastpage=ts.page=1;$(".selbox",ts.p.pager).attr("disabled",true)
                        }else{
                        cp=IntNum(ts.p.page);last=IntNum(ts.p.lastpage);$(".selbox",ts.p.pager).attr("disabled",false)
                        }if(ts.p.pginput===true){
                        $(".ui-pg-input",ts.p.pager).val(ts.p.page);$("#sp_1",ts.p.pager).html(ts.p.lastpage)
                        }if(ts.p.viewrecords){
                        bs=ts.p.scroll===true?0:base;if(ts.p.reccount===0){
                            $(".ui-paging-info",ts.p.pager).html(ts.p.emptyrecords)
                            }else{
                            from=bs+1;to=base+ts.p.reccount;tot=ts.p.records;if($.fmatter){
                                fmt=$.jgrid.formatter.integer||{};from=$.fmatter.util.NumberFormat(from,fmt);to=$.fmatter.util.NumberFormat(to,fmt);tot=$.fmatter.util.NumberFormat(tot,fmt)
                                }$(".ui-paging-info",ts.p.pager).html($.jgrid.format(ts.p.recordtext,from,to,tot))
                            }
                        }if(ts.p.pgbuttons===true){
                        if(cp<=0){
                            cp=last=1
                            }if(cp==1){
                            $("#first, #prev",ts.p.pager).addClass("ui-state-disabled").removeClass("ui-state-hover")
                            }else{
                            $("#first, #prev",ts.p.pager).removeClass("ui-state-disabled")
                            }if(cp==last){
                            $("#next, #last",ts.p.pager).addClass("ui-state-disabled").removeClass("ui-state-hover")
                            }else{
                            $("#next, #last",ts.p.pager).removeClass("ui-state-disabled")
                            }
                        }
                    }if(rn===true&&ts.p.rownumbers===true){
                    $("td.jqgrid-rownum",ts.rows).each(function(i){
                        $(this).html(base+1+i)
                        })
                    }if($.isFunction(ts.p.gridComplete)){
                    ts.p.gridComplete()
                    }
                },populate=function(){
                if(!ts.grid.hDiv.loading){
                    var prm={},dt,dstr,pN=ts.p.prmNames;if(pN.search!==null){
                        prm[pN.search]=ts.p.search
                        }if(pN.nd!=null){
                        prm[pN.nd]=new Date().getTime()
                        }if(pN.rows!==null){
                        prm[pN.rows]=ts.p.rowNum
                        }if(pN.page!==null){
                        prm[pN.page]=ts.p.page
                        }if(pN.sort!==null){
                        prm[pN.sort]=ts.p.sortname
                        }if(pN.order!==null){
                        prm[pN.order]=ts.p.sortorder
                        }$.extend(ts.p.postData,prm);var rcnt=ts.p.scroll===false?0:ts.rows.length-1;if($.isFunction(ts.p.datatype)){
                        ts.p.datatype(ts.p.postData,"load_"+ts.p.id);return
                    }else{
                        if(beReq){
                            ts.p.beforeRequest()
                            }
                        }dt=ts.p.datatype.toLowerCase();switch(dt){
                        case"json":case"jsonp":case"xml":case"script":$.ajax({
                            url:ts.p.url,
                            type:ts.p.mtype,
                            dataType:dt,
                            data:ts.p.postData,
                            complete:function(req,st){
                                if(st=="success"||(req.statusText=="OK"&&req.status=="200")){
                                    if(dt==="xml"){
                                        addXmlData(req.responseXML,ts.grid.bDiv,rcnt)
                                        }else{
                                        addJSONData($.jgrid.parse(req.responseText),ts.grid.bDiv,rcnt)
                                        }if(loadComplete){
                                        loadComplete(req)
                                        }
                                    }req=null;endReq()
                                },
                            error:function(xhr,st,err){
                                if(loadError){
                                    loadError(xhr,st,err)
                                    }endReq();xhr=null
                                },
                            beforeSend:function(xhr){
                                beginReq();if(loadBeforeSend){
                                    loadBeforeSend(xhr)
                                    }
                                }
                            });if(ts.p.loadonce||ts.p.treeGrid){
                            ts.p.datatype="local"
                            }break;case"xmlstring":beginReq();addXmlData(dstr=$.jgrid.stringToDoc(ts.p.datastr),ts.grid.bDiv);ts.p.datatype="local";if(loadComplete){
                            loadComplete(dstr)
                            }ts.p.datastr=null;endReq();break;case"jsonstring":beginReq();if(typeof ts.p.datastr=="string"){
                            dstr=$.jgrid.parse(ts.p.datastr)
                            }else{
                            dstr=ts.p.datastr
                            }addJSONData(dstr,ts.grid.bDiv);ts.p.datatype="local";if(loadComplete){
                            loadComplete(dstr)
                            }ts.p.datastr=null;endReq();break;case"local":case"clientside":beginReq();ts.p.datatype="local";sortArrayData();endReq();break
                            }
                    }
                },beginReq=function(){
                ts.grid.hDiv.loading=true;if(ts.p.hiddengrid){
                    return
                }switch(ts.p.loadui){
                    case"disable":break;case"enable":$("#load_"+ts.p.id).show();break;case"block":$("#lui_"+ts.p.id).show();$("#load_"+ts.p.id).show();break
                        }
                },endReq=function(){
                ts.grid.hDiv.loading=false;switch(ts.p.loadui){
                    case"disable":break;case"enable":$("#load_"+ts.p.id).hide();break;case"block":$("#lui_"+ts.p.id).hide();$("#load_"+ts.p.id).hide();break
                        }
                },sortArrayData=function(){
                var stripNum=/[\$,%]/g;var rows=[],col=0,st,sv,findSortKey,newDir=(ts.p.sortorder=="asc")?1:-1;$.each(ts.p.colModel,function(i,v){
                    if(this.index==ts.p.sortname||this.name==ts.p.sortname){
                        col=ts.p.lastsort=i;st=this.sorttype;return false
                        }
                    });if(st=="float"||st=="number"||st=="currency"){
                    findSortKey=function($cell){
                        var key=parseFloat($cell.replace(stripNum,""));return isNaN(key)?0:key
                        }
                    }else{
                    if(st=="int"||st=="integer"){
                        findSortKey=function($cell){
                            return IntNum($cell.replace(stripNum,""))
                            }
                        }else{
                        if(st=="date"){
                            findSortKey=function($cell){
                                var fd=ts.p.colModel[col].datefmt||"Y-m-d";return parseDate(fd,$cell).getTime()
                                }
                            }else{
                            findSortKey=function($cell){
                                return $.trim($cell.toUpperCase())
                                }
                            }
                        }
                    }$.each(ts.rows,function(index,row){
                    try{
                        sv=$.unformat($(row).children("td").eq(col),{
                            colModel:ts.p.colModel[col]
                            },col,true)
                        }catch(_){
                        sv=$(row).children("td").eq(col).text()
                        }row.sortKey=findSortKey(sv);rows[index]=this
                    });if(ts.p.treeGrid){
                    $(ts).SortTree(newDir)
                    }else{
                    rows.sort(function(a,b){
                        if(a.sortKey<b.sortKey){
                            return -newDir
                            }if(a.sortKey>b.sortKey){
                            return newDir
                            }return 0
                        });if(rows[0]){
                        $("td",rows[0]).each(function(k){
                            $(this).css("width",grid.headers[k].width+"px")
                            });grid.cols=rows[0].cells
                        }$.each(rows,function(index,row){
                        $("tbody",ts.grid.bDiv).append(row);row.sortKey=null
                        })
                    }if(ts.p.multiselect){
                    $("tbody tr",ts.grid.bDiv).removeClass("ui-state-highlight");$("[id^=jqg_]",ts.rows).attr("checked",false);$("#cb_jqg",ts.grid.hDiv).attr("checked",false);ts.p.selarrrow=[]
                    }ts.grid.bDiv.scrollTop=0
                },parseDate=function(format,date){
                var tsp={
                    m:1,
                    d:1,
                    y:1970,
                    h:0,
                    i:0,
                    s:0
                },k,hl,dM;date=date.split(/[\\\/:_;.\t\T\s-]/);format=format.split(/[\\\/:_;.\t\T\s-]/);var dfmt=$.jgrid.formatter.date.monthNames;for(k=0,hl=format.length;k<hl;k++){
                    if(format[k]=="M"){
                        dM=$.inArray(date[k],dfmt);if(dM!==-1&&dM<12){
                            date[k]=dM+1
                            }
                        }if(format[k]=="F"){
                        dM=$.inArray(date[k],dfmt);if(dM!==-1&&dM>11){
                            date[k]=dM+1-12
                            }
                        }tsp[format[k].toLowerCase()]=parseInt(date[k],10)
                    }tsp.m=parseInt(tsp.m,10)-1;var ty=tsp.y;if(ty>=70&&ty<=99){
                    tsp.y=1900+tsp.y
                    }else{
                    if(ty>=0&&ty<=69){
                        tsp.y=2000+tsp.y
                        }
                    }return new Date(tsp.y,tsp.m,tsp.d,tsp.h,tsp.i,tsp.s,0)
                },setPager=function(){
                var sep="<td class='ui-pg-button ui-state-disabled' style='width:4px;'><span class='ui-separator'></span></td>",pgid=$(ts.p.pager).attr("id")||"pager",pginp=(ts.p.pginput===true)?"<td>"+$.jgrid.format(ts.p.pgtext||"","<input class='ui-pg-input' type='text' size='2' maxlength='7' value='0' role='textbox'/>","<span id='sp_1'></span>")+"</td>":"",pgl="<table cellspacing='0' cellpadding='0' border='0' style='table-layout:auto;' class='ui-pg-table'><tbody><tr>",str,pgcnt,lft,cent,rgt,twd,tdw,i,clearVals=function(onpaging){
                    if($.isFunction(ts.p.onPaging)){
                        ts.p.onPaging(onpaging)
                        }ts.p.selrow=null;if(ts.p.multiselect){
                        ts.p.selarrrow=[];$("#cb_jqg",ts.grid.hDiv).attr("checked",false)
                        }ts.p.savedRow=[]
                    };pgcnt="pg_"+pgid;lft=pgid+"_left";cent=pgid+"_center";rgt=pgid+"_right";$(ts.p.pager).addClass("ui-jqgrid-pager corner-bottom").append("<div id='"+pgcnt+"' class='ui-pager-control' role='group'><table cellspacing='0' cellpadding='0' border='0' class='ui-pg-table' style='width:100%;table-layout:fixed;' role='row'><tbody><tr><td id='"+lft+"' align='left'></td><td id='"+cent+"' align='center' style='white-space:nowrap;'></td><td id='"+rgt+"' align='right'></td></tr></tbody></table></div>");if(ts.p.pgbuttons===true){
                    pgl+="<td id='first' class='ui-pg-button ui-corner-all'><span class='ui-icon ui-icon-seek-first'></span></td>";pgl+="<td id='prev' class='ui-pg-button ui-corner-all'><span class='ui-icon ui-icon-seek-prev'></span></td>";pgl+=pginp!=""?sep+pginp+sep:"";pgl+="<td id='next' class='ui-pg-button ui-corner-all'><span class='ui-icon ui-icon-seek-next'></span></td>";pgl+="<td id='last' class='ui-pg-button ui-corner-all'><span class='ui-icon ui-icon-seek-end'></span></td>"
                    }else{
                    if(pginp!=""){
                        pgl+=pginp
                        }
                    }if(ts.p.rowList.length>0){
                    str="<select class='ui-pg-selbox' role='listbox'>";for(i=0;i<ts.p.rowList.length;i++){
                        str+="<option role='option' value="+ts.p.rowList[i]+((ts.p.rowNum==ts.p.rowList[i])?" selected":"")+">"+ts.p.rowList[i]
                        }str+="</select>";pgl+="<td>"+str+"</td>"
                    }pgl+="</tr></tbody></table>";if(ts.p.viewrecords===true){
                    $("td#"+pgid+"_"+ts.p.recordpos,"#"+pgcnt).append("<div style='text-align:"+ts.p.recordpos+"' class='ui-paging-info'></div>")
                    }$("td#"+pgid+"_"+ts.p.pagerpos,"#"+pgcnt).append(pgl);tdw=$(".ui-jqgrid").css("font-size")||"11px";$("body").append("<div id='testpg' class='ui-jqgrid ui-widget ui-widget-content' style='font-size:"+tdw+";visibility:hidden;' ></div>");twd=$(pgl).clone().appendTo("#testpg").width();$("#testpg").remove();if(twd>0){
                    twd+=25;$("td#"+pgid+"_"+ts.p.pagerpos,"#"+pgcnt).width(twd)
                    }ts.p._nvtd=[];ts.p._nvtd[0]=twd?Math.floor((ts.p.width-twd)/2):Math.floor(ts.p.width/3);ts.p._nvtd[1]=0;pgl=null;$(".ui-pg-selbox","#"+pgcnt).bind("change",function(){
                    ts.p.page=Math.round(ts.p.rowNum*(ts.p.page-1)/this.value-0.5)+1;ts.p.rowNum=this.value;clearVals("records");populate();return false
                    });if(ts.p.pgbuttons===true){
                    $(".ui-pg-button","#"+pgcnt).hover(function(e){
                        if($(this).hasClass("ui-state-disabled")){
                            this.style.cursor="default"
                            }else{
                            $(this).addClass("ui-state-hover");this.style.cursor="pointer"
                            }
                        },function(e){
                        if($(this).hasClass("ui-state-disabled")){}else{
                            $(this).removeClass("ui-state-hover");this.style.cursor="default"
                            }
                        });$("#first, #prev, #next, #last",ts.p.pager).click(function(e){
                        var cp=IntNum(ts.p.page),last=IntNum(ts.p.lastpage),selclick=false,fp=true,pp=true,np=true,lp=true;if(last===0||last===1){
                            fp=false;pp=false;np=false;lp=false
                            }else{
                            if(last>1&&cp>=1){
                                if(cp===1){
                                    fp=false;pp=false
                                    }else{
                                    if(cp>1&&cp<last){}else{
                                        if(cp===last){
                                            np=false;lp=false
                                            }
                                        }
                                    }
                                }else{
                                if(last>1&&cp===0){
                                    np=false;lp=false;cp=last-1
                                    }
                                }
                            }if(this.id==="first"&&fp){
                            ts.p.page=1;selclick=true
                            }if(this.id==="prev"&&pp){
                            ts.p.page=(cp-1);selclick=true
                            }if(this.id==="next"&&np){
                            ts.p.page=(cp+1);selclick=true
                            }if(this.id==="last"&&lp){
                            ts.p.page=last;selclick=true
                            }if(selclick){
                            clearVals(this.id);populate()
                            }return false
                        })
                    }if(ts.p.pginput===true){
                    $("input.ui-pg-input","#"+pgcnt).keypress(function(e){
                        var key=e.charCode?e.charCode:e.keyCode?e.keyCode:0;if(key==13){
                            ts.p.page=($(this).val()>0)?$(this).val():ts.p.page;clearVals("user");populate();return false
                            }return this
                        })
                    }
                },sortData=function(index,idxcol,reload,sor){
                if(!ts.p.colModel[idxcol].sortable){
                    return
                }var imgs,so;if(ts.p.savedRow.length>0){
                    return
                }if(!reload){
                    if(ts.p.lastsort==idxcol){
                        if(ts.p.sortorder=="asc"){
                            ts.p.sortorder="desc"
                            }else{
                            if(ts.p.sortorder=="desc"){
                                ts.p.sortorder="asc"
                                }
                            }
                        }else{
                        ts.p.sortorder="asc"
                        }ts.p.page=1
                    }if(sor){
                    if(ts.p.lastsort==idxcol&&ts.p.sortorder==sor){
                        return
                    }else{
                        ts.p.sortorder=sor
                        }
                    }var thd=$("thead:first",ts.grid.hDiv).get(0);$("tr th:eq("+ts.p.lastsort+") span.ui-grid-ico-sort",thd).addClass("ui-state-disabled");$("tr th:eq("+ts.p.lastsort+")",thd).attr("aria-selected","false");$("tr th:eq("+idxcol+") span.ui-icon-"+ts.p.sortorder,thd).removeClass("ui-state-disabled");$("tr th:eq("+idxcol+")",thd).attr("aria-selected","true");if(!ts.p.viewsortcols[0]){
                    if(ts.p.lastsort!=idxcol){
                        $("tr th:eq("+ts.p.lastsort+") span.s-ico",thd).hide();$("tr th:eq("+idxcol+") span.s-ico",thd).show()
                        }
                    }ts.p.lastsort=idxcol;index=index.substring(5);ts.p.sortname=ts.p.colModel[idxcol].index||index;so=ts.p.sortorder;if(onSortCol){
                    onSortCol(index,idxcol,so)
                    }if(ts.p.datatype=="local"){
                    if(ts.p.deselectAfterSort){
                        $(ts).resetSelection()
                        }
                    }else{
                    ts.p.selrow=null;if(ts.p.multiselect){
                        $("#cb_jqg",ts.grid.hDiv).attr("checked",false)
                        }ts.p.selarrrow=[];ts.p.savedRow=[]
                    }if(ts.p.scroll===true){
                    $("tbody tr",ts.grid.bDiv).remove()
                    }if(ts.p.subGrid&&ts.p.datatype=="local"){
                    $("td.sgexpanded","#"+ts.p.id).each(function(){
                        $(this).trigger("click")
                        })
                    }populate();if(ts.p.sortname!=index&&idxcol){
                    ts.p.lastsort=idxcol
                    }
                },setColWidth=function(){
                var initwidth=0,brd=ts.p.cellLayout,vc=0,lvc,scw=ts.p.scrollOffset,cw,hs=false,aw,tw=0,gw=0,msw=ts.p.multiselectWidth,sgw=ts.p.subGridWidth,rnw=ts.p.rownumWidth,cl=ts.p.cellLayout,cr;$.each(ts.p.colModel,function(i){
                    if(typeof this.hidden==="undefined"){
                        this.hidden=false
                        }if(this.hidden===false){
                        initwidth+=IntNum(this.width);vc++
                    }
                    });if(isNaN(ts.p.width)){
                    ts.p.width=grid.width=initwidth
                    }else{
                    grid.width=ts.p.width
                    }ts.p.tblwidth=initwidth;if(ts.p.shrinkToFit===false&&ts.p.forceFit===true){
                    ts.p.forceFit=false
                    }if(ts.p.shrinkToFit===true){
                    if(isSafari){
                        brd=0;msw+=cl;sgw+=cl;rnw+=cl
                        }if(ts.p.multiselect){
                        tw=msw;gw=msw+brd;vc--
                    }if(ts.p.subGrid){
                        tw+=sgw;gw+=sgw+brd;vc--
                    }if(ts.p.rownumbers){
                        tw+=rnw;gw+=rnw+brd;vc--
                    }aw=grid.width-brd*vc-gw;if(isNaN(ts.p.height)){}else{
                        aw-=scw;hs=true
                        }initwidth=0;$.each(ts.p.colModel,function(i){
                        if(this.hidden===false&&this.name!=="cb"&&this.name!=="subgrid"&&this.name!=="rn"){
                            cw=Math.floor(aw/(ts.p.tblwidth-tw)*this.width);this.width=cw;initwidth+=cw;lvc=i
                            }
                        });cr=0;if(hs&&grid.width-gw-(initwidth+brd*vc)!==scw){
                        cr=grid.width-gw-(initwidth+brd*vc)-scw
                        }else{
                        if(!hs&&Math.abs(grid.width-gw-(initwidth+brd*vc))!==1){
                            cr=grid.width-gw-(initwidth+brd*vc)
                            }
                        }ts.p.colModel[lvc].width+=cr;ts.p.tblwidth=initwidth+tw+cr
                    }
                },nextVisible=function(iCol){
                var ret=iCol,j=iCol,i;for(i=iCol+1;i<ts.p.colModel.length;i++){
                    if(ts.p.colModel[i].hidden!==true){
                        j=i;break
                    }
                    }return j-ret
                },getOffset=function(iCol){
                var i,ret={},brd1=isSafari?0:ts.p.cellLayout;ret[0]=ret[1]=ret[2]=0;for(i=0;i<=iCol;i++){
                    if(ts.p.colModel[i].hidden===false){
                        ret[0]+=ts.p.colModel[i].width+brd1
                        }
                    }ret[0]=ret[0]-ts.grid.bDiv.scrollLeft;if($(ts.grid.cDiv).is(":visible")){
                    ret[1]+=$(ts.grid.cDiv).height()+parseInt($(ts.grid.cDiv).css("padding-top"))+parseInt($(ts.grid.cDiv).css("padding-bottom"))
                    }if(ts.p.toolbar[0]==true&&(ts.p.toolbar[1]=="top"||ts.p.toolbar[1]=="both")){
                    ret[1]+=$(ts.grid.uDiv).height()+parseInt($(ts.grid.uDiv).css("border-top-width"))+parseInt($(ts.grid.uDiv).css("border-bottom-width"))
                    }ret[2]+=$(ts.grid.bDiv).height()+$(ts.grid.hDiv).height();return ret
                };this.p.id=this.id;if($.inArray(ts.p.multikey,sortkeys)==-1){
                ts.p.multikey=false
                }ts.p.keyIndex=false;for(i=0;i<ts.p.colModel.length;i++){
                if(ts.p.colModel[i].key===true){
                    ts.p.keyIndex=i;break
                }
                }ts.p.sortorder=ts.p.sortorder.toLowerCase();if(this.p.treeGrid===true){
                try{
                    $(this).setTreeGrid()
                    }catch(_){}
                }if(this.p.subGrid){
                try{
                    $(ts).setSubGrid()
                    }catch(_){}
                }if(this.p.multiselect){
                this.p.colNames.unshift("<input id='cb_jqg' class='cbox' type='checkbox'/>");this.p.colModel.unshift({
                    name:"cb",
                    width:isSafari?ts.p.multiselectWidth+ts.p.cellLayout:ts.p.multiselectWidth,
                    sortable:false,
                    resizable:false,
                    hidedlg:true,
                    search:false,
                    align:"center"
                })
                }if(this.p.rownumbers){
                this.p.colNames.unshift("");this.p.colModel.unshift({
                    name:"rn",
                    width:ts.p.rownumWidth,
                    sortable:false,
                    resizable:false,
                    hidedlg:true,
                    search:false,
                    align:"center"
                })
                }ts.p.xmlReader=$.extend({
                root:"rows",
                row:"row",
                page:"rows>page",
                total:"rows>total",
                records:"rows>records",
                repeatitems:true,
                cell:"cell",
                id:"[id]",
                userdata:"userdata",
                subgrid:{
                    root:"rows",
                    row:"row",
                    repeatitems:true,
                    cell:"cell"
                }
                },ts.p.xmlReader);ts.p.jsonReader=$.extend({
                root:"rows",
                page:"page",
                total:"total",
                records:"records",
                repeatitems:true,
                cell:"cell",
                id:"id",
                userdata:"userdata",
                subgrid:{
                    root:"rows",
                    repeatitems:true,
                    cell:"cell"
                }
                },ts.p.jsonReader);if(ts.p.scroll===true){
                ts.p.pgbuttons=false;ts.p.pginput=false;ts.p.rowList=[]
                }var thead="<thead><tr class='ui-jqgrid-labels' role='rowheader'>",tdc,idn,w,res,sort,td,ptr,tbody,imgs,iac="",idc="";if(ts.p.shrinkToFit===true&&ts.p.forceFit===true){
                for(i=ts.p.colModel.length-1;i>=0;i--){
                    if(!ts.p.colModel[i].hidden){
                        ts.p.colModel[i].resizable=false;break
                    }
                    }
                }if(ts.p.viewsortcols[1]=="horizontal"){
                iac=" ui-i-asc";idc=" ui-i-desc"
                }tdc=isMSIE?"class='ui-th-div-ie'":"";imgs="<span class='s-ico' style='display:none'><span sort='asc' class='ui-grid-ico-sort ui-icon-asc"+iac+" ui-state-disabled ui-icon ui-icon-triangle-1-n'></span>";imgs+="<span sort='desc' class='ui-grid-ico-sort ui-icon-desc"+idc+" ui-state-disabled ui-icon ui-icon-triangle-1-s'></span></span>";for(i=0;i<this.p.colNames.length;i++){
                thead+="<th role='columnheader' class='ui-state-default ui-th-column'>";idn=ts.p.colModel[i].index||ts.p.colModel[i].name;thead+="<div id='jqgh_"+ts.p.colModel[i].name+"' "+tdc+">"+ts.p.colNames[i];if(idn==ts.p.sortname){
                    ts.p.lastsort=i
                    }thead+=imgs+"</div></th>"
                }thead+="</tr></thead>";$(this).append(thead);$("thead tr:first th",this).hover(function(){
                $(this).addClass("ui-state-hover")
                },function(){
                $(this).removeClass("ui-state-hover")
                });if(this.p.multiselect){
                var onSA=true,emp=[],chk;if(typeof ts.p.onSelectAll!=="function"){
                    onSA=false
                    }$("#cb_jqg",this).bind("click",function(){
                    if(this.checked){
                        $("[id^=jqg_]",ts.rows).attr("checked",true);$(ts.rows).each(function(i){
                            if(!$(this).hasClass("subgrid")){
                                $(this).addClass("ui-state-highlight").attr("aria-selected","true");ts.p.selarrrow[i]=ts.p.selrow=this.id
                                }
                            });chk=true;emp=[]
                        }else{
                        $("[id^=jqg_]",ts.rows).attr("checked",false);$(ts.rows).each(function(i){
                            if(!$(this).hasClass("subgrid")){
                                $(this).removeClass("ui-state-highlight").attr("aria-selected","false");emp[i]=this.id
                                }
                            });ts.p.selarrrow=[];ts.p.selrow=null;chk=false
                        }if(onSA){
                        ts.p.onSelectAll(chk?ts.p.selarrrow:emp,chk)
                        }
                    })
                }$.each(ts.p.colModel,function(i){
                if(!this.width){
                    this.width=150
                    }
                });if(ts.p.autowidth===true){
                var pw=$(eg).innerWidth();ts.p.width=pw>0?pw:"nw"
                }setColWidth();$(eg).css("width",grid.width+"px").append("<div class='ui-jqgrid-resize-mark' id='rs_m"+ts.p.id+"'>&nbsp;</div>");$(gv).css("width",grid.width+"px");thead=$("thead:first",ts).get(0);var tfoot="<table role='grid' style='width:"+ts.p.tblwidth+"px' class='ui-jqgrid-ftable' cellspacing='0' cellpadding='0' border='0'><tbody><tr role='row' class='ui-widget-content footrow'>";$("tr:first th",thead).each(function(j){
                var ht=$("div",this)[0];w=ts.p.colModel[j].width;if(typeof ts.p.colModel[j].resizable==="undefined"){
                    ts.p.colModel[j].resizable=true
                    }res=document.createElement("span");$(res).html("&#160;");if(ts.p.colModel[j].resizable){
                    $(this).addClass(ts.p.resizeclass);$(res).mousedown(function(e){
                        if(ts.p.forceFit===true){
                            ts.p.nv=nextVisible(j)
                            }grid.dragStart(j,e,getOffset(j));return false
                        }).addClass("ui-jqgrid-resize")
                    }else{
                    res=""
                    }$(this).css("width",w+"px").prepend(res);if(ts.p.colModel[j].hidden){
                    $(this).css("display","none")
                    }grid.headers[j]={
                    width:w,
                    el:this
                };sort=ts.p.colModel[j].sortable;if(typeof sort!=="boolean"){
                    ts.p.colModel[j].sortable=true;sort=true
                    }var nm=ts.p.colModel[j].name;if(!(nm=="cb"||nm=="subgrid"||nm=="rn")){
                    if(ts.p.viewsortcols[2]==false){
                        $(".ui-grid-ico-sort",this).click(function(){
                            sortData(ht.id,j,true,$(this).attr("sort"));return false
                            })
                        }else{
                        $("div",this).addClass("ui-jqgrid-sortable").click(function(){
                            sortData(ht.id,j);return false
                            })
                        }
                    }if(sort){
                    if(ts.p.viewsortcols[0]){
                        $("div span.s-ico",this).show();if(j==ts.p.lastsort){
                            $("div span.ui-icon-"+ts.p.sortorder,this).removeClass("ui-state-disabled")
                            }
                        }else{
                        if(j==ts.p.lastsort){
                            $("div span.s-ico",this).show();$("div span.ui-icon-"+ts.p.sortorder,this).removeClass("ui-state-disabled")
                            }
                        }
                    }tfoot+="<td role='gridcell' "+formatCol(j,0)+">&nbsp;</td>"
                });tfoot+="</tr></tbody></table>";tbody=document.createElement("tbody");this.appendChild(tbody);$(this).addClass("ui-jqgrid-btable");var hTable=$("<table class='ui-jqgrid-htable' style='width:"+ts.p.tblwidth+"px' role='grid' aria-labelledby='gbox_"+this.id+"' cellspacing='0' cellpadding='0' border='0'></table>").append(thead),hg=(ts.p.caption&&ts.p.hiddengrid===true)?true:false,hb=$("<div class='ui-jqgrid-hbox'></div>");grid.hDiv=document.createElement("div");$(grid.hDiv).css({
                width:grid.width+"px"
                }).addClass("ui-state-default ui-jqgrid-hdiv").append(hb);$(hb).append(hTable);if(hg){
                $(grid.hDiv).hide()
                }ts.p._height=0;if(ts.p.pager){
                if(typeof ts.p.pager=="string"){
                    if(ts.p.pager.substr(0,1)!="#"){
                        ts.p.pager="#"+ts.p.pager
                        }
                    }$(ts.p.pager).css({
                    width:grid.width+"px"
                    }).appendTo(eg).addClass("ui-state-default ui-jqgrid-pager");ts.p._height+=parseInt($(ts.p.pager).height(),10);if(hg){
                    $(ts.p.pager).hide()
                    }setPager()
                }if(ts.p.cellEdit===false&&ts.p.hoverrows===true){
                $(ts).bind("mouseover",function(e){
                    ptr=$(e.target).parents("tr.jqgrow");if($(ptr).attr("class")!=="subgrid"){
                        $(ptr).addClass("ui-state-hover")
                        }return false
                    }).bind("mouseout",function(e){
                    ptr=$(e.target).parents("tr.jqgrow");$(ptr).removeClass("ui-state-hover");return false
                    })
                }var ri,ci;$(ts).before(grid.hDiv).click(function(e){
                td=e.target;var scb=$(td).hasClass("cbox");ptr=$(td,ts.rows).parents("tr.jqgrow");if($(ptr).length===0){
                    return this
                    }var cSel=true;if(bSR){
                    cSel=bSR(ptr[0].id,e)
                    }if(td.tagName=="A"||((td.tagName=="INPUT"||td.tagName=="TEXTAREA"||td.tagName=="OPTION"||td.tagName=="SELECT")&&!scb)){
                    return true
                    }if(cSel===true){
                    if(ts.p.cellEdit===true){
                        if(ts.p.multiselect&&scb){
                            $(ts).setSelection(ptr[0].id,true)
                            }else{
                            ri=ptr[0].rowIndex;ci=!$(td).is("td")?$(td).parents("td:first")[0].cellIndex:td.cellIndex;if(isMSIE){
                                ci=$.jgrid.getAbsoluteIndex(ptr[0],ci)
                                }try{
                                $(ts).editCell(ri,ci,true)
                                }catch(e){}
                            }
                        }else{
                        if(!ts.p.multikey){
                            if(ts.p.multiselect&&ts.p.multiboxonly){
                                if(scb){
                                    $(ts).setSelection(ptr[0].id,true)
                                    }else{
                                    $(ts.p.selarrrow).each(function(i,n){
                                        var ind=ts.rows.namedItem(n);$(ind).removeClass("ui-state-highlight");$("#jqg_"+n.replace(".","\\."),ind).attr("checked",false)
                                        });ts.p.selarrrow=[];$("#cb_jqg",ts.grid.hDiv).attr("checked",false);$(ts).setSelection(ptr[0].id,true)
                                    }
                                }else{
                                $(ts).setSelection(ptr[0].id,true)
                                }
                            }else{
                            if(e[ts.p.multikey]){
                                $(ts).setSelection(ptr[0].id,true)
                                }else{
                                if(ts.p.multiselect&&scb){
                                    scb=$("[id^=jqg_]",ptr).attr("checked");$("[id^=jqg_]",ptr).attr("checked",!scb)
                                    }
                                }
                            }
                        }if(onSC){
                        ri=ptr[0].id;ci=!$(td).is("td")?$(td).parents("td:first")[0].cellIndex:td.cellIndex;if(isMSIE){
                            ci=$.jgrid.getAbsoluteIndex(ptr[0],ci)
                            }onSC(ri,ci,$(td).html(),td)
                        }
                    }e.stopPropagation()
                }).bind("reloadGrid",function(e){
                if(ts.p.treeGrid===true){
                    ts.p.datatype=ts.p.treedatatype
                    }if(ts.p.datatype=="local"){
                    $(ts).resetSelection()
                    }else{
                    if(!ts.p.treeGrid){
                        ts.p.selrow=null;if(ts.p.multiselect){
                            ts.p.selarrrow=[];$("#cb_jqg",ts.grid.hDiv).attr("checked",false)
                            }if(ts.p.cellEdit){
                            ts.p.savedRow=[]
                            }
                        }
                    }if(ts.p.scroll===true){
                    $("tbody tr",ts.grid.bDiv).remove()
                    }ts.grid.populate();return false
                });if(ondblClickRow){
                $(this).dblclick(function(e){
                    td=(e.target);ptr=$(td,ts.rows).parents("tr.jqgrow");if($(ptr).length===0){
                        return false
                        }ri=ptr[0].rowIndex;ci=!$(td).is("td")?$(td).parents("td:first")[0].cellIndex:td.cellIndex;if(isMSIE){
                        ci=$.jgrid.getAbsoluteIndex(ptr[0],ci)
                        }ts.p.ondblClickRow($(ptr).attr("id"),ri,ci);return false
                    })
                }if(onRightClickRow){
                $(this).bind("contextmenu",function(e){
                    td=e.target;ptr=$(td,ts.rows).parents("tr.jqgrow");if($(ptr).length===0){
                        return false
                        }if(!ts.p.multiselect){
                        $(ts).setSelection(ptr[0].id,true)
                        }ri=ptr[0].rowIndex;ci=!$(td).is("td")?$(td).parents("td:first")[0].cellIndex:td.cellIndex;if(isMSIE){
                        ci=$.jgrid.getAbsoluteIndex(ptr[0],ci)
                        }ts.p.onRightClickRow($(ptr).attr("id"),ri,ci);return false
                    })
                }grid.bDiv=document.createElement("div");$(grid.bDiv).append(this).addClass("ui-jqgrid-bdiv").css({
                height:ts.p.height+(isNaN(ts.p.height)?"":"px"),
                width:(grid.width)+"px"
                }).scroll(function(e){
                grid.scrollGrid()
                });$("table:first",grid.bDiv).css({
                width:ts.p.tblwidth+"px"
                });if(isMSIE){
                if($("tbody",this).size()==2){
                    $("tbody:first",this).remove()
                    }if(ts.p.multikey){
                    $(grid.bDiv).bind("selectstart",function(){
                        return false
                        })
                    }
                }else{
                if(ts.p.multikey){
                    $(grid.bDiv).bind("mousedown",function(){
                        return false
                        })
                    }
                }if(hg){
                $(grid.bDiv).hide()
                }grid.cDiv=document.createElement("div");var arf=ts.p.hidegrid===true?$("<a role='link' href='javascript:void(0)'/>").addClass("ui-jqgrid-titlebar-close HeaderButton").hover(function(){
                arf.addClass("ui-state-hover")
                },function(){
                arf.removeClass("ui-state-hover")
                }).append("<span class='ui-icon ui-icon-circle-triangle-n'></span>"):"";$(grid.cDiv).append(arf).append("<span class='ui-jqgrid-title'>"+ts.p.caption+"</span>").addClass("ui-jqgrid-titlebar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix");$(grid.cDiv).insertBefore(grid.hDiv);if(ts.p.toolbar[0]){
                grid.uDiv=document.createElement("div");if(ts.p.toolbar[1]=="top"){
                    $(grid.uDiv).insertBefore(grid.hDiv)
                    }else{
                    if(ts.p.toolbar[1]=="bottom"){
                        $(grid.uDiv).insertAfter(grid.hDiv)
                        }
                    }if(ts.p.toolbar[1]=="both"){
                    grid.ubDiv=document.createElement("div");$(grid.uDiv).insertBefore(grid.hDiv).addClass("ui-userdata ui-state-default").attr("id","t_"+this.id);$(grid.ubDiv).insertAfter(grid.hDiv).addClass("ui-userdata ui-state-default").attr("id","tb_"+this.id);ts.p._height+=IntNum($(grid.ubDiv).height());if(hg){
                        $(grid.ubDiv).hide()
                        }
                    }else{
                    $(grid.uDiv).width(grid.width).addClass("ui-userdata ui-state-default").attr("id","t_"+this.id)
                    }ts.p._height+=IntNum($(grid.uDiv).height());if(hg){
                    $(grid.uDiv).hide()
                    }
                }if(ts.p.footerrow){
                grid.sDiv=document.createElement("div");hb=$("<div class='ui-jqgrid-hbox'></div>");$(grid.sDiv).addClass("ui-jqgrid-sdiv").append(hb).insertAfter(grid.hDiv).width(grid.width);$(hb).append(tfoot);grid.footers=$(".ui-jqgrid-ftable",grid.sDiv)[0].rows[0].cells;if(ts.p.rownumbers){
                    grid.footers[0].className="ui-state-default jqgrid-rownum"
                    }if(hg){
                    $(grid.sDiv).hide()
                    }
                }if(ts.p.caption){
                ts.p._height+=parseInt($(grid.cDiv,ts).height(),10);var tdt=ts.p.datatype;if(ts.p.hidegrid===true){
                    $(".ui-jqgrid-titlebar-close",grid.cDiv).click(function(){
                        if(ts.p.gridstate=="visible"){
                            $(".ui-jqgrid-bdiv, .ui-jqgrid-hdiv","#gview_"+ts.p.id).slideUp("fast");if(ts.p.pager){
                                $(ts.p.pager).slideUp("fast")
                                }if(ts.p.toolbar[0]===true){
                                if(ts.p.toolbar[1]=="both"){
                                    $(grid.ubDiv).slideUp("fast")
                                    }$(grid.uDiv).slideUp("fast")
                                }if(ts.p.footerrow){
                                $(".ui-jqgrid-sdiv","#gbox_"+ts.p.id).slideUp("fast")
                                }$("span",this).removeClass("ui-icon-circle-triangle-n").addClass("ui-icon-circle-triangle-s");ts.p.gridstate="hidden";if(onHdCl){
                                if(!hg){
                                    ts.p.onHeaderClick(ts.p.gridstate)
                                    }
                                }
                            }else{
                            if(ts.p.gridstate=="hidden"){
                                $(".ui-jqgrid-hdiv, .ui-jqgrid-bdiv","#gview_"+ts.p.id).slideDown("fast");if(ts.p.pager){
                                    $(ts.p.pager).slideDown("fast")
                                    }if(ts.p.toolbar[0]===true){
                                    if(ts.p.toolbar[1]=="both"){
                                        $(grid.ubDiv).slideDown("fast")
                                        }$(grid.uDiv).slideDown("fast")
                                    }if(ts.p.footerrow){
                                    $(".ui-jqgrid-sdiv","#gbox_"+ts.p.id).slideDown("fast")
                                    }$("span",this).removeClass("ui-icon-circle-triangle-s").addClass("ui-icon-circle-triangle-n");if(hg){
                                    ts.p.datatype=tdt;populate();hg=false
                                    }ts.p.gridstate="visible";if(onHdCl){
                                    ts.p.onHeaderClick(ts.p.gridstate)
                                    }
                                }
                            }return false
                        });if(hg){
                        ts.p.datatype="local";$(".ui-jqgrid-titlebar-close",grid.cDiv).trigger("click")
                        }
                    }
                }else{
                $(grid.cDiv).hide()
                }$(grid.hDiv).after(grid.bDiv);$(".ui-jqgrid-labels",grid.hDiv).bind("selectstart",function(){
                return false
                }).mousemove(function(e){
                if(grid.resizing){
                    grid.dragMove(e)
                    }return false
                });ts.p._height+=parseInt($(grid.hDiv).height(),10);$(document).mouseup(function(e){
                if(grid.resizing){
                    grid.dragEnd();return false
                    }return true
                });this.updateColumns=function(){
                var r=this.rows[0],self=this;if(r){
                    $("td",r).each(function(k){
                        $(this).css("width",self.grid.headers[k].width+"px")
                        });this.grid.cols=r.cells
                    }return this
                };ts.formatCol=function(a,b){
                return formatCol(a,b)
                };ts.sortData=function(a,b,c){
                sortData(a,b,c)
                };ts.updatepager=function(a){
                updatepager(a)
                };ts.formatter=function(rowId,cellval,colpos,rwdat,act){
                return formatter(rowId,cellval,colpos,rwdat,act)
                };$.extend(grid,{
                populate:function(){
                    populate()
                    }
                });this.grid=grid;ts.addXmlData=function(d){
                addXmlData(d,ts.grid.bDiv)
                };ts.addJSONData=function(d){
                addJSONData(d,ts.grid.bDiv)
                };populate();ts.p.hiddengrid=false;$(window).unload(function(){
                $(this).empty();this.grid=null;this.p=null
                })
            })
        };$.fn.extend({
        getGridParam:function(pName){
            var $t=this[0];if(!$t.grid){
                return
            }if(!pName){
                return $t.p
                }else{
                return $t.p[pName]?$t.p[pName]:null
                }
            },
        setGridParam:function(newParams){
            return this.each(function(){
                if(this.grid&&typeof(newParams)==="object"){
                    $.extend(true,this.p,newParams)
                    }
                })
            },
        getDataIDs:function(){
            var ids=[],i=0,len;this.each(function(){
                len=this.rows.length;if(len&&len>0){
                    while(i<len){
                        ids[i]=this.rows[i].id;i++
                    }
                    }
                });return ids
            },
        setSelection:function(selection,onsr){
            return this.each(function(){
                var $t=this,stat,pt,olr,ner,ia,tpsr;if(selection===undefined){
                    return
                }onsr=onsr===false?false:true;pt=$t.rows.namedItem(selection);if(pt==null){
                    return
                }if($t.p.selrow&&$t.p.scrollrows===true){
                    olr=$t.rows.namedItem($t.p.selrow).rowIndex;ner=$t.rows.namedItem(selection).rowIndex;if(ner>=0){
                        if(ner>olr){
                            scrGrid(ner,"d")
                            }else{
                            scrGrid(ner,"u")
                            }
                        }
                    }if(!$t.p.multiselect){
                    if($(pt).attr("class")!=="subgrid"){
                        if($t.p.selrow){
                            $("tr#"+$t.p.selrow.replace(".","\\."),$t.grid.bDiv).removeClass("ui-state-highlight").attr("aria-selected","false")
                            }$t.p.selrow=selection;$(pt).addClass("ui-state-highlight").attr("aria-selected","true");if($t.p.onSelectRow&&onsr){
                            $t.p.onSelectRow($t.p.selrow,true)
                            }
                        }
                    }else{
                    $t.p.selrow=selection;ia=$.inArray($t.p.selrow,$t.p.selarrrow);if(ia===-1){
                        if($(pt).attr("class")!=="subgrid"){
                            $(pt).addClass("ui-state-highlight").attr("aria-selected","true")
                            }stat=true;$("#jqg_"+$t.p.selrow.replace(".","\\."),$t.rows).attr("checked",stat);$t.p.selarrrow.push($t.p.selrow);if($t.p.onSelectRow&&onsr){
                            $t.p.onSelectRow($t.p.selrow,stat)
                            }
                        }else{
                        if($(pt).attr("class")!=="subgrid"){
                            $(pt).removeClass("ui-state-highlight").attr("aria-selected","false")
                            }stat=false;$("#jqg_"+$t.p.selrow.replace(".","\\."),$t.rows).attr("checked",stat);$t.p.selarrrow.splice(ia,1);if($t.p.onSelectRow&&onsr){
                            $t.p.onSelectRow($t.p.selrow,stat)
                            }tpsr=$t.p.selarrrow[0];$t.p.selrow=(tpsr===undefined)?null:tpsr
                        }
                    }function scrGrid(iR,tp){
                    var ch=$($t.grid.bDiv)[0].clientHeight,st=$($t.grid.bDiv)[0].scrollTop,nROT=$t.rows[iR].offsetTop+$t.rows[iR].clientHeight,pROT=$t.rows[iR].offsetTop;if(tp=="d"){
                        if(nROT>=ch){
                            $($t.grid.bDiv)[0].scrollTop=st+nROT-pROT
                            }
                        }if(tp=="u"){
                        if(pROT<st){
                            $($t.grid.bDiv)[0].scrollTop=st-nROT+pROT
                            }
                        }
                    }
                })
            },
        resetSelection:function(){
            return this.each(function(){
                var t=this,ind;if(!t.p.multiselect){
                    if(t.p.selrow){
                        $("tr#"+t.p.selrow.replace(".","\\."),t.grid.bDiv).removeClass("ui-state-highlight").attr("aria-selected","false");t.p.selrow=null
                        }
                    }else{
                    $(t.p.selarrrow).each(function(i,n){
                        ind=t.rows.namedItem(n);$(ind).removeClass("ui-state-highlight").attr("aria-selected","false");$("#jqg_"+n.replace(".","\\."),ind).attr("checked",false)
                        });$("#cb_jqg",t.grid.hDiv).attr("checked",false);t.p.selarrrow=[]
                    }t.p.savedRow=[]
                })
            },
        getRowData:function(rowid){
            var res={};this.each(function(){
                var $t=this,nm,ind;ind=$t.rows.namedItem(rowid);if(!ind){
                    return res
                    }$("td",ind).each(function(i){
                    nm=$t.p.colModel[i].name;if(nm!=="cb"&&nm!=="subgrid"){
                        if($t.p.treeGrid===true&&nm==$t.p.ExpandColumn){
                            res[nm]=$.jgrid.htmlDecode($("span:first",this).html())
                            }else{
                            try{
                                res[nm]=$.unformat(this,{
                                    colModel:$t.p.colModel[i]
                                    },i)
                                }catch(e){
                                res[nm]=$.jgrid.htmlDecode($(this).html())
                                }
                            }
                        }
                    })
                });return res
            },
        delRowData:function(rowid){
            var success=false,rowInd,ia,ri;this.each(function(){
                var $t=this;rowInd=$t.rows.namedItem(rowid);if(!rowInd){
                    return false
                    }else{
                    ri=rowInd.rowIndex;$(rowInd).remove();$t.p.records--;$t.p.reccount--;$t.updatepager(true);success=true;if(rowid==$t.p.selrow){
                        $t.p.selrow=null
                        }ia=$.inArray(rowid,$t.p.selarrrow);if(ia!=-1){
                        $t.p.selarrrow.splice(ia,1)
                        }
                    }if(ri==0&&success){
                    $t.updateColumns()
                    }if($t.p.altRows===true&&success){
                    var cn=$t.p.altclass;$($t.rows).each(function(i){
                        if(i%2==1){
                            $(this).addClass(cn)
                            }else{
                            $(this).removeClass(cn)
                            }
                        })
                    }
                });return success
            },
        setRowData:function(rowid,data){
            var nm,success=false;this.each(function(){
                var t=this,vl,ind;if(!t.grid){
                    return false
                    }ind=t.rows.namedItem(rowid);if(!ind){
                    return false
                    }if(data){
                    $(this.p.colModel).each(function(i){
                        nm=this.name;if(data[nm]!=undefined){
                            vl=t.formatter(rowid,data[nm],i,data,"edit");if(t.p.treeGrid===true&&nm==t.p.ExpandColumn){
                                $("td:eq("+i+") > span:first",ind).html(vl).attr("title",$.jgrid.stripHtml(vl))
                                }else{
                                $("td:eq("+i+")",ind).html(vl).attr("title",$.jgrid.stripHtml(vl))
                                }success=true
                            }
                        })
                    }
                });return success
            },
        addRowData:function(rowid,data,pos,src){
            if(!pos){
                pos="last"
                }var success=false,nm,row,gi=0,si=0,ni=0,sind,i,v,prp="";if(data){
                this.each(function(){
                    var t=this;rowid=typeof(rowid)!="undefined"?rowid+"":t.p.records+1;row='<tr id="'+rowid+'" role="row" class="ui-widget-content jqgrow">';if(t.p.rownumbers===true){
                        prp=t.formatCol(ni,1);row+='<td role="gridcell" class="ui-state-default jqgrid-rownum" '+prp+">0</td>";ni=1
                        }if(t.p.multiselect){
                        v='<input type="checkbox" id="jqg_'+rowid+'" class="cbox"/>';prp=t.formatCol(ni,1);row+='<td role="gridcell" '+prp+">"+v+"</td>";gi=1
                        }if(t.p.subGrid===true){
                        row+=$(t).addSubGridCell(gi+ni,1);si=1
                        }for(i=gi+si+ni;i<this.p.colModel.length;i++){
                        nm=this.p.colModel[i].name;v=t.formatter(rowid,data[nm],i,data,"add");prp=t.formatCol(i,1);row+='<td role="gridcell" '+prp+' title="'+$.jgrid.stripHtml(v)+'">'+v+"</td>"
                        }row+="</tr>";if(t.p.subGrid===true){
                        row=$(row)[0];$(t).addSubGrid(row,gi+ni)
                        }if(t.rows.length===0){
                        $("table:first",t.grid.bDiv).append(row)
                        }else{
                        switch(pos){
                            case"last":$(t.rows[t.rows.length-1]).after(row);break;case"first":$(t.rows[0]).before(row);break;case"after":sind=t.rows.namedItem(src);sind!=null?$(t.rows[sind.rowIndex+1]).hasClass("ui-subgrid")?$(t.rows[sind.rowIndex+1]).after(row):$(sind).after(row):"";break;case"before":sind=t.rows.namedItem(src);if(sind!=null){
                                $(sind).before(row);sind=sind.rowIndex
                                }break
                            }
                        }t.p.records++;t.p.reccount++;if(pos==="first"||(pos==="before"&&sind==0)||t.rows.length===1){
                        t.updateColumns()
                        }if(t.p.altRows===true){
                        var cn=t.p.altclass;if(pos=="last"){
                            if(t.rows.length%2==1){
                                $(t.rows[t.rows.length-1]).addClass(cn)
                                }
                            }else{
                            $(t.rows).each(function(i){
                                if(i%2==1){
                                    $(this).addClass(cn)
                                    }else{
                                    $(this).removeClass(cn)
                                    }
                                })
                            }
                        }try{
                        t.p.afterInsertRow(rowid,data)
                        }catch(e){}t.updatepager(true);success=true
                    })
                }return success
            },
        footerData:function(action,data,format){
            var nm,success=false,res={};function isEmpty(obj){
                for(var i in obj){
                    return false
                    }return true
                }if(typeof(action)=="undefined"){
                action="get"
                }if(typeof(format)!="boolean"){
                format=true
                }action=action.toLowerCase();this.each(function(){
                var t=this,vl,ind;if(!t.grid||!t.p.footerrow){
                    return false
                    }if(action=="set"){
                    if(isEmpty(data)){
                        return false
                        }
                    }success=true;$(this.p.colModel).each(function(i){
                    nm=this.name;if(action=="set"){
                        if(data[nm]!=undefined){
                            vl=format?t.formatter("",data[nm],i,data,"edit"):data[nm];$("tr.footrow td:eq("+i+")",t.grid.sDiv).html(vl).attr("title",$.jgrid.stripHtml(vl));success=true
                            }
                        }else{
                        if(action=="get"){
                            res[nm]=$("tr.footrow td:eq("+i+")",t.grid.sDiv).html()
                            }
                        }
                    })
                });return action=="get"?res:success
            },
        ShowHideCol:function(colname,show){
            return this.each(function(){
                var $t=this,fndh=false;if(!$t.grid){
                    return
                }if(typeof colname==="string"){
                    colname=[colname]
                    }show=show!="none"?"":"none";var sw=show==""?true:false;$(this.p.colModel).each(function(i){
                    if($.inArray(this.name,colname)!==-1&&this.hidden===sw){
                        $("tr",$t.grid.hDiv).each(function(){
                            $("th:eq("+i+")",this).css("display",show)
                            });$($t.rows).each(function(j){
                            $("td:eq("+i+")",$t.rows[j]).css("display",show)
                            });if($t.p.footerrow){
                            $("td:eq("+i+")",$t.grid.sDiv).css("display",show)
                            }if(show=="none"){
                            $t.p.tblwidth-=this.width
                            }else{
                            $t.p.tblwidth+=this.width
                            }this.hidden=!sw;fndh=true
                        }
                    });if(fndh===true){
                    $("table:first",$t.grid.hDiv).width($t.p.tblwidth);$("table:first",$t.grid.bDiv).width($t.p.tblwidth);$t.grid.hDiv.scrollLeft=$t.grid.bDiv.scrollLeft;if($t.p.footerrow){
                        $("table:first",$t.grid.sDiv).width($t.p.tblwidth);$t.grid.sDiv.scrollLeft=$t.grid.bDiv.scrollLeft
                        }
                    }
                })
            },
        hideCol:function(colname){
            return this.each(function(){
                $(this).ShowHideCol(colname,"none")
                })
            },
        showCol:function(colname){
            return this.each(function(){
                $(this).ShowHideCol(colname,"")
                })
            },
        setGridWidth:function(nwidth,shrink){
            return this.each(function(){
                var $t=this,cw,initwidth=0,brd=$t.p.cellLayout,lvc,vc=0,isSafari,hs=false,scw=$t.p.scrollOffset,aw,gw=0,tw=0,msw=$t.p.multiselectWidth,sgw=$t.p.subGridWidth,rnw=$t.p.rownumWidth,cl=$t.p.cellLayout,cr;if(!$t.grid){
                    return
                }if(typeof shrink!="boolean"){
                    shrink=$t.p.shrinkToFit
                    }if(isNaN(nwidth)){
                    return
                }if(nwidth==$t.grid.width){
                    return
                }else{
                    $t.grid.width=$t.p.width=nwidth
                    }$("#gbox_"+$t.p.id).css("width",nwidth+"px");$("#gview_"+$t.p.id).css("width",nwidth+"px");$($t.grid.bDiv).css("width",nwidth+"px");$($t.grid.hDiv).css("width",nwidth+"px");if($t.p.pager){
                    $($t.p.pager).css("width",nwidth+"px")
                    }if($t.p.toolbar[0]===true){
                    $($t.grid.uDiv).css("width",nwidth+"px");if($t.p.toolbar[1]=="both"){
                        $($t.grid.ubDiv).css("width",nwidth+"px")
                        }
                    }if($t.p.footerrow){
                    $($t.grid.sDiv).css("width",nwidth+"px")
                    }if(shrink===false&&$t.p.forceFit==true){
                    $t.p.forceFit=false
                    }if(shrink===true){
                    $.each($t.p.colModel,function(i){
                        if(this.hidden===false){
                            initwidth+=parseInt(this.width,10);vc++
                        }
                        });isSafari=$.browser.safari?true:false;if(isSafari){
                        brd=0;msw+=cl;sgw+=cl;rnw+=cl
                        }if($t.p.multiselect){
                        tw=msw;gw=msw+brd;vc--
                    }if($t.p.subGrid){
                        tw+=sgw;gw+=sgw+brd;vc--
                    }if($t.p.rownumbers){
                        tw+=rnw;gw+=rnw+brd;vc--
                    }$t.p.tblwidth=initwidth;aw=nwidth-brd*vc-gw;if(!isNaN($t.p.height)){
                        if($($t.grid.bDiv)[0].clientHeight<$($t.grid.bDiv)[0].scrollHeight){
                            hs=true;aw-=scw
                            }
                        }initwidth=0;var cle=$t.grid.cols.length>0;$.each($t.p.colModel,function(i){
                        var tn=this.name;if(this.hidden===false&&tn!=="cb"&&tn!=="subgrid"&&tn!=="rn"){
                            cw=Math.floor((aw)/($t.p.tblwidth-tw)*this.width);this.width=cw;initwidth+=cw;$t.grid.headers[i].width=cw;$t.grid.headers[i].el.style.width=cw+"px";if($t.p.footerrow){
                                $t.grid.footers[i].style.width=cw+"px"
                                }if(cle){
                                $t.grid.cols[i].style.width=cw+"px"
                                }lvc=i
                            }
                        });cr=0;if(hs&&nwidth-gw-(initwidth+brd*vc)!==scw){
                        cr=nwidth-gw-(initwidth+brd*vc)-scw
                        }else{
                        if(Math.abs(nwidth-gw-(initwidth+brd*vc))!==1){
                            cr=nwidth-gw-(initwidth+brd*vc)
                            }
                        }$t.p.colModel[lvc].width+=cr;cw=$t.p.colModel[lvc].width;$t.grid.headers[lvc].width=cw;$t.grid.headers[lvc].el.style.width=cw+"px";if(cl>0){
                        $t.grid.cols[lvc].style.width=cw+"px"
                        }$t.p.tblwidth=initwidth+tw+cr;$("table:first",$t.grid.bDiv).css("width",initwidth+tw+cr+"px");$("table:first",$t.grid.hDiv).css("width",initwidth+tw+cr+"px");$t.grid.hDiv.scrollLeft=$t.grid.bDiv.scrollLeft;if($t.p.footerrow){
                        $t.grid.footers[lvc].style.width=cw+"px";$("table:first",$t.grid.sDiv).css("width",initwidth+tw+cr+"px")
                        }
                    }
                })
            },
        setGridHeight:function(nh){
            return this.each(function(){
                var $t=this;if(!$t.grid){
                    return
                }$($t.grid.bDiv).css({
                    height:nh+(isNaN(nh)?"":"px")
                    });$t.p.height=nh
                })
            },
        setCaption:function(newcap){
            return this.each(function(){
                this.p.caption=newcap;$("span.ui-jqgrid-title",this.grid.cDiv).html(newcap);$(this.grid.cDiv).show()
                })
            },
        setLabel:function(colname,nData,prop,attrp){
            return this.each(function(){
                var $t=this,pos=-1;if(!$t.grid){
                    return
                }if(isNaN(colname)){
                    $($t.p.colModel).each(function(i){
                        if(this.name==colname){
                            pos=i;return false
                            }
                        })
                    }else{
                    pos=parseInt(colname,10)
                    }if(pos>=0){
                    var thecol=$("tr.ui-jqgrid-labels th:eq("+pos+")",$t.grid.hDiv);if(nData){
                        $("div",thecol).html(nData);$t.p.colNames[pos]=nData
                        }if(prop){
                        if(typeof prop==="string"){
                            $(thecol).addClass(prop)
                            }else{
                            $(thecol).css(prop)
                            }
                        }if(typeof attrp==="object"){
                        $(thecol).attr(attrp)
                        }
                    }
                })
            },
        setCell:function(rowid,colname,nData,cssp,attrp){
            return this.each(function(){
                var $t=this,pos=-1,v;if(!$t.grid){
                    return
                }if(isNaN(colname)){
                    $($t.p.colModel).each(function(i){
                        if(this.name==colname){
                            pos=i;return false
                            }
                        })
                    }else{
                    pos=parseInt(colname,10)
                    }if(pos>=0){
                    var ind=$t.rows.namedItem(rowid);if(ind){
                        var tcell=$("td:eq("+pos+")",ind);if(nData!==""){
                            v=$t.formatter(rowid,nData,pos,ind,"edit");$(tcell).html(v).attr("title",$.jgrid.stripHtml(v))
                            }if(cssp){
                            if(typeof cssp==="string"){
                                $(tcell).addClass(cssp)
                                }else{
                                $(tcell).css(cssp)
                                }
                            }if(typeof attrp==="object"){
                            $(tcell).attr(attrp)
                            }
                        }
                    }
                })
            },
        getCell:function(rowid,col){
            var ret=false;this.each(function(){
                var $t=this,pos=-1;if(!$t.grid){
                    return
                }if(isNaN(col)){
                    $($t.p.colModel).each(function(i){
                        if(this.name===col){
                            pos=i;return false
                            }
                        })
                    }else{
                    pos=parseInt(col,10)
                    }if(pos>=0){
                    var ind=$t.rows.namedItem(rowid);if(ind){
                        try{
                            ret=$.unformat($("td:eq("+pos+")",ind),{
                                colModel:$t.p.colModel[pos]
                                },pos)
                            }catch(e){
                            ret=$.jgrid.htmlDecode($("td:eq("+pos+")",ind).html())
                            }
                        }
                    }
                });return ret
            },
        getCol:function(col,obj){
            var ret=[],val;obj=obj==false?false:true;this.each(function(){
                var $t=this,pos=-1;if(!$t.grid){
                    return
                }if(isNaN(col)){
                    $($t.p.colModel).each(function(i){
                        if(this.name===col){
                            pos=i;return false
                            }
                        })
                    }else{
                    pos=parseInt(col,10)
                    }if(pos>=0){
                    var ln=$t.rows.length,i=0;if(ln&&ln>0){
                        while(i<ln){
                            val=$t.rows[i].cells[pos].innerHTML;obj?ret.push({
                                id:$t.rows[i].id,
                                value:val
                            }):ret[i]=val;i++
                        }
                        }
                    }
                });return ret
            },
        clearGridData:function(clearfooter){
            return this.each(function(){
                var $t=this;if(!$t.grid){
                    return
                }if(typeof clearfooter!="boolean"){
                    clearfooter=false
                    }$("tbody:first tr",$t.grid.bDiv).remove();if($t.p.footerrow&&clearfooter){
                    $(".ui-jqgrid-ftable td",$t.grid.sDiv).html("&nbsp;")
                    }$t.p.selrow=null;$t.p.selarrrow=[];$t.p.savedRow=[];$t.p.records=0;$t.p.page="0";$t.p.lastpage="0";$t.p.reccount=0;$t.updatepager(true)
                })
            },
        getInd:function(rowid,rc){
            var ret=false,rw;this.each(function(){
                rw=this.rows.namedItem(rowid);if(rw){
                    ret=rc===true?rw:rw.rowIndex
                    }
                });return ret
            }
        })
    })(jQuery);(function(c){
    c.fmatter={};c.fn.fmatter=function(g,h,f,d,e){
        f=c.extend({},c.jgrid.formatter,f);return a(g,h,f,d,e)
        };c.fmatter.util={
        NumberFormat:function(f,d){
            if(!isNumber(f)){
                f*=1
                }if(isNumber(f)){
                var h=(f<0);var n=f+"";var k=(d.decimalSeparator)?d.decimalSeparator:".";var l;if(isNumber(d.decimalPlaces)){
                    var m=d.decimalPlaces;var g=Math.pow(10,m);n=Math.round(f*g)/g+"";l=n.lastIndexOf(".");if(m>0){
                        if(l<0){
                            n+=k;l=n.length-1
                            }else{
                            if(k!=="."){
                                n=n.replace(".",k)
                                }
                            }while((n.length-1-l)<m){
                            n+="0"
                            }
                        }
                    }if(d.thousandsSeparator){
                    var p=d.thousandsSeparator;l=n.lastIndexOf(k);l=(l>-1)?l:n.length;var o=n.substring(l);var e=-1;for(var j=l;j>0;j--){
                        e++;if((e%3===0)&&(j!==l)&&(!h||(j>1))){
                            o=p+o
                            }o=n.charAt(j-1)+o
                        }n=o
                    }n=(d.prefix)?d.prefix+n:n;n=(d.suffix)?n+d.suffix:n;return n
                }else{
                return f
                }
            },
        DateFormat:function(I,L,O,x){
            var m=/\\.|[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/g,C=/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,K=/[^-+\dA-Z]/g,J=function(j,i){
                j=String(j);i=parseInt(i)||2;while(j.length<i){
                    j="0"+j
                    }return j
                },d={
                m:1,
                d:1,
                y:1970,
                h:0,
                i:0,
                s:0
            },g=0,q,E,f,D=["i18n"];D.i18n={
                dayNames:x.dayNames,
                monthNames:x.monthNames
                };if(I in x.masks){
                I=x.masks[I]
                }L=L.split(/[\\\/:_;.\t\T\s-]/);I=I.split(/[\\\/:_;.\t\T\s-]/);for(E=0,f=I.length;E<f;E++){
                if(I[E]=="M"){
                    q=c.inArray(L[E],D.i18n.monthNames);if(q!==-1&&q<12){
                        L[E]=q+1
                        }
                    }if(I[E]=="F"){
                    q=c.inArray(L[E],D.i18n.monthNames);if(q!==-1&&q>11){
                        L[E]=q+1-12
                        }
                    }d[I[E].toLowerCase()]=parseInt(L[E],10)
                }d.m=parseInt(d.m)-1;var M=d.y;if(M>=70&&M<=99){
                d.y=1900+d.y
                }else{
                if(M>=0&&M<=69){
                    d.y=2000+d.y
                    }
                }g=new Date(d.y,d.m,d.d,d.h,d.i,d.s,0);if(O in x.masks){
                O=x.masks[O]
                }else{
                if(!O){
                    O="Y-m-d"
                    }
                }var t=g.getHours(),H=g.getMinutes(),F=g.getDate(),B=g.getMonth()+1,A=g.getTimezoneOffset(),y=g.getSeconds(),v=g.getMilliseconds(),r=g.getDay(),e=g.getFullYear(),l=(r+6)%7+1,p=(new Date(e,B-1,F)-new Date(e,0,1))/86400000,h={
                d:J(F),
                D:D.i18n.dayNames[r],
                j:F,
                l:D.i18n.dayNames[r+7],
                N:l,
                S:x.S(F),
                w:r,
                z:p,
                W:l<5?Math.floor((p+l-1)/7)+1:Math.floor((p+l-1)/7)||((new Date(e-1,0,1).getDay()+6)%7<4?53:52),
                F:D.i18n.monthNames[B-1+12],
                m:J(B),
                M:D.i18n.monthNames[B-1],
                n:B,
                t:"?",
                L:"?",
                o:"?",
                Y:e,
                y:String(e).substring(2),
                a:t<12?x.AmPm[0]:x.AmPm[1],
                A:t<12?x.AmPm[2]:x.AmPm[3],
                B:"?",
                g:t%12||12,
                G:t,
                h:J(t%12||12),
                H:J(t),
                i:J(H),
                s:J(y),
                u:v,
                e:"?",
                I:"?",
                O:(A>0?"-":"+")+J(Math.floor(Math.abs(A)/60)*100+Math.abs(A)%60,4),
                P:"?",
                T:(String(g).match(C)||[""]).pop().replace(K,""),
                Z:"?",
                c:"?",
                r:"?",
                U:Math.floor(g/1000)
                };return O.replace(m,function(i){
                return i in h?h[i]:i.substring(1)
                })
            }
        };c.fn.fmatter.defaultFormat=function(e,d){
        return(isValue(e)&&e!=="")?e:d.defaultValue?d.defaultValue:"&#160;"
        };c.fn.fmatter.email=function(e,d){
        if(!isEmpty(e)){
            return'<a href="mailto:'+e+'">'+e+"</a>"
            }else{
            return c.fn.fmatter.defaultFormat(e,d)
            }
        };c.fn.fmatter.checkbox=function(g,e){
        var h=c.extend({},e.checkbox),f;if(!isUndefined(e.colModel.formatoptions)){
            h=c.extend({},h,e.colModel.formatoptions)
            }if(h.disabled===true){
            f="disabled"
            }else{
            f=""
            }g=g+"";g=g.toLowerCase();var d=g.search(/(false|0|no|off)/i)<0?" checked='checked' ":"";return'<input type="checkbox" '+d+' value="'+g+'" offval="no" '+f+"/>"
        },c.fn.fmatter.link=function(f,d){
        var g={
            target:d.target
            };var e="";if(!isUndefined(d.colModel.formatoptions)){
            g=c.extend({},g,d.colModel.formatoptions)
            }if(g.target){
            e="target="+g.target
            }if(!isEmpty(f)){
            return"<a "+e+' href="'+f+'">'+f+"</a>"
            }else{
            return c.fn.fmatter.defaultFormat(f,d)
            }
        };c.fn.fmatter.showlink=function(f,d){
        var g={
            baseLinkUrl:d.baseLinkUrl,
            showAction:d.showAction,
            addParam:d.addParam||"",
            target:d.target,
            idName:d.idName
            },e="";if(!isUndefined(d.colModel.formatoptions)){
            g=c.extend({},g,d.colModel.formatoptions)
            }if(g.target){
            e="target="+g.target
            }idUrl=g.baseLinkUrl+g.showAction+"?"+g.idName+"="+d.rowId+g.addParam;if(isString(f)){
            return"<a "+e+' href="'+idUrl+'">'+f+"</a>"
            }else{
            return c.fn.fmatter.defaultFormat(f,d)
            }
        };c.fn.fmatter.integer=function(e,d){
        var f=c.extend({},d.integer);if(!isUndefined(d.colModel.formatoptions)){
            f=c.extend({},f,d.colModel.formatoptions)
            }if(isEmpty(e)){
            return f.defaultValue
            }return c.fmatter.util.NumberFormat(e,f)
        };c.fn.fmatter.number=function(e,d){
        var f=c.extend({},d.number);if(!isUndefined(d.colModel.formatoptions)){
            f=c.extend({},f,d.colModel.formatoptions)
            }if(isEmpty(e)){
            return f.defaultValue
            }return c.fmatter.util.NumberFormat(e,f)
        };c.fn.fmatter.currency=function(e,d){
        var f=c.extend({},d.currency);if(!isUndefined(d.colModel.formatoptions)){
            f=c.extend({},f,d.colModel.formatoptions)
            }if(isEmpty(e)){
            return f.defaultValue
            }return c.fmatter.util.NumberFormat(e,f)
        };c.fn.fmatter.date=function(f,e,d){
        var g=c.extend({},e.date);if(!isUndefined(e.colModel.formatoptions)){
            g=c.extend({},g,e.colModel.formatoptions)
            }if(!g.reformatAfterEdit&&d=="edit"){
            return c.fn.fmatter.defaultFormat(f,e)
            }else{
            if(!isEmpty(f)){
                return c.fmatter.util.DateFormat(g.srcformat,f,g.newformat,g)
                }else{
                return c.fn.fmatter.defaultFormat(f,e)
                }
            }
        };c.fn.fmatter.select=function(h,d,m){
        if(!h){
            h=""
            }var f=false;if(!isUndefined(d.colModel.editoptions)){
            f=d.colModel.editoptions.value
            }if(f){
            var l=[],p=d.colModel.editoptions.multiple===true?true:false,n=[],o;if(p){
                n=h.split(",");n=c.map(n,function(i){
                    return c.trim(i)
                    })
                }if(isString(f)){
                var e=f.split(";"),g=0;for(var k=0;k<e.length;k++){
                    o=e[k].split(":");if(p){
                        if(jQuery.inArray(o[0],n)>-1){
                            l[g]=o[1];g++
                        }
                        }else{
                        if(c.trim(o[0])==c.trim(h)){
                            l[0]=o[1];break
                        }
                        }
                    }
                }else{
                if(isObject(f)){
                    if(p){
                        l=jQuery.map(n,function(q,j){
                            return f[q]
                            })
                        }else{
                        l[0]=f[h]||""
                        }
                    }
                }return l.join(", ")
            }
        };c.unformat=function(g,n,k,e){
        var j,h=n.colModel.formatter,i=n.colModel.formatoptions||{},o,m=/([\.\*\_\'\(\)\{\}\+\?\\])/g;unformatFunc=n.colModel.unformat||(c.fn.fmatter[h]&&c.fn.fmatter[h].unformat);if(typeof unformatFunc!=="undefined"&&isFunction(unformatFunc)){
            j=unformatFunc(c(g).text(),n)
            }else{
            if(typeof h!=="undefined"&&isString(h)){
                var d=c.jgrid.formatter||{},l;switch(h){
                    case"integer":i=c.extend({},d.integer,i);o=i.thousandsSeparator.replace(m,"\\$1");l=new RegExp(o,"g");j=c(g).text().replace(l,"");break;case"number":i=c.extend({},d.number,i);o=i.thousandsSeparator.replace(m,"\\$1");l=new RegExp(o,"g");j=c(g).text().replace(i.decimalSeparator,".").replace(l,"");break;case"currency":i=c.extend({},d.currency,i);o=i.thousandsSeparator.replace(m,"\\$1");l=new RegExp(o,"g");j=c(g).text().replace(i.decimalSeparator,".").replace(i.prefix,"").replace(i.suffix,"").replace(l,"");break;case"checkbox":var f=(n.colModel.editoptions)?n.colModel.editoptions.value.split(":"):["Yes","No"];j=c("input",g).attr("checked")?f[0]:f[1];break;case"select":j=c.unformat.select(g,n,k,e);break;default:j=c(g).text();break
                        }
                }
            }return j?j:e===true?c(g).text():c.jgrid.htmlDecode(c(g).html())
        };c.unformat.select=function(h,s,n,e){
        var m=[];var q=c(h).text();if(e==true){
            return q
            }var l=c.extend({},s.colModel.editoptions);if(l.value){
            var f=l.value,r=l.multiple===true?true:false,p=[],o;if(r){
                p=q.split(",");p=c.map(p,function(i){
                    return c.trim(i)
                    })
                }if(isString(f)){
                var d=f.split(";"),g=0;for(var k=0;k<d.length;k++){
                    o=d[k].split(":");if(r){
                        if(jQuery.inArray(o[1],p)>-1){
                            m[g]=o[0];g++
                        }
                        }else{
                        if(c.trim(o[1])==c.trim(q)){
                            m[0]=o[0];break
                        }
                        }
                    }
                }else{
                if(isObject(f)){
                    if(!r){
                        p[0]=q
                        }m=jQuery.map(p,function(j){
                        var i;c.each(f,function(t,u){
                            if(u==j){
                                i=t;return false
                                }
                            });if(i){
                            return i
                            }
                        })
                    }
                }return m.join(", ")
            }else{
            return q||""
            }
        };function a(h,i,g,d,e){
        h=h.toLowerCase();var f=i;if(c.fn.fmatter[h]){
            f=c.fn.fmatter[h](i,g,e)
            }return f
        }function b(d){
        if(window.console&&window.console.log){
            window.console.log(d)
            }
        }isValue=function(d){
        return(isObject(d)||isString(d)||isNumber(d)||isBoolean(d))
        };isBoolean=function(d){
        return typeof d==="boolean"
        };isNull=function(d){
        return d===null
        };isNumber=function(d){
        return typeof d==="number"&&isFinite(d)
        };isString=function(d){
        return typeof d==="string"
        };isEmpty=function(d){
        if(!isString(d)&&isValue(d)){
            return false
            }else{
            if(!isValue(d)){
                return true
                }
            }d=c.trim(d).replace(/\&nbsp\;/ig,"").replace(/\&#160\;/ig,"");return d===""
        };isUndefined=function(d){
        return typeof d==="undefined"
        };isObject=function(d){
        return(d&&(typeof d==="object"||isFunction(d)))||false
        };isFunction=function(d){
        return typeof d==="function"
        }
    })(jQuery);(function(a){
    a.fn.extend({
        getColProp:function(d){
            var b={},f=this[0];if(!f.grid){
                return
            }var e=f.p.colModel;for(var c=0;c<e.length;c++){
                if(e[c].name==d){
                    b=e[c];break
                }
                }return b
            },
        setColProp:function(c,b){
            return this.each(function(){
                if(this.grid){
                    if(b){
                        var e=this.p.colModel;for(var d=0;d<e.length;d++){
                            if(e[d].name==c){
                                a.extend(this.p.colModel[d],b);break
                            }
                            }
                        }
                    }
                })
            },
        sortGrid:function(c,b){
            return this.each(function(){
                var g=this,d=-1;if(!g.grid){
                    return
                }if(!c){
                    c=g.p.sortname
                    }for(var f=0;f<g.p.colModel.length;f++){
                    if(g.p.colModel[f].index==c||g.p.colModel[f].name==c){
                        d=f;break
                    }
                    }if(d!=-1){
                    var e=g.p.colModel[d].sortable;if(typeof e!=="boolean"){
                        e=true
                        }if(typeof b!=="boolean"){
                        b=false
                        }if(e){
                        g.sortData("jqgh_"+c,d,b)
                        }
                    }
                })
            },
        GridDestroy:function(){
            return this.each(function(){
                if(this.grid){
                    if(this.p.pager){
                        a(this.p.pager).remove()
                        }var c=this.id;try{
                        a("#gbox_"+c).remove()
                        }catch(b){}
                    }
                })
            },
        GridUnload:function(){
            return this.each(function(){
                if(!this.grid){
                    return
                }var d={
                    id:a(this).attr("id"),
                    cl:a(this).attr("class")
                    };if(this.p.pager){
                    a(this.p.pager).empty().removeClass("ui-state-default ui-jqgrid-pager corner-bottom")
                    }var b=document.createElement("table");a(b).attr({
                    id:d.id
                    });b.className=d.cl;var c=this.id;a(b).removeClass("ui-jqgrid-btable");if(a(this.p.pager).parents("#gbox_"+c).length===1){
                    a(b).insertBefore("#gbox_"+c).show();a(this.p.pager).insertBefore("#gbox_"+c)
                    }else{
                    a(b).insertBefore("#gbox_"+c).show()
                    }a("#gbox_"+c).remove()
                })
            },
        setGridState:function(b){
            return this.each(function(){
                if(!this.grid){
                    return
                }$t=this;if(b=="hidden"){
                    a(".ui-jqgrid-bdiv, .ui-jqgrid-hdiv","#gview_"+$t.p.id).slideUp("fast");if($t.p.pager){
                        a($t.p.pager).slideUp("fast")
                        }if($t.p.toolbar[0]===true){
                        if($t.p.toolbar[1]=="both"){
                            a($t.grid.ubDiv).slideUp("fast")
                            }a($t.grid.uDiv).slideUp("fast")
                        }if($t.p.footerrow){
                        a(".ui-jqgrid-sdiv","#gbox_"+$s.p.id).slideUp("fast")
                        }a(".ui-jqgrid-titlebar-close span",$t.grid.cDiv).removeClass("ui-icon-circle-triangle-n").addClass("ui-icon-circle-triangle-s");$t.p.gridstate="hidden"
                    }else{
                    if(b=="visible"){
                        a(".ui-jqgrid-hdiv, .ui-jqgrid-bdiv","#gview_"+$t.p.id).slideDown("fast");if($t.p.pager){
                            a($t.p.pager).slideDown("fast")
                            }if($t.p.toolbar[0]===true){
                            if($t.p.toolbar[1]=="both"){
                                a($t.grid.ubDiv).slideDown("fast")
                                }a($t.grid.uDiv).slideDown("fast")
                            }if($t.p.footerrow){
                            a(".ui-jqgrid-sdiv","#gbox_"+$t.p.id).slideDown("fast")
                            }a(".ui-jqgrid-titlebar-close span",$t.grid.cDiv).removeClass("ui-icon-circle-triangle-s").addClass("ui-icon-circle-triangle-n");$t.p.gridstate="visible"
                        }
                    }
                })
            },
        updateGridRows:function(e,c,d){
            var b,f=false;this.each(function(){
                var h=this,j,k,i,g;if(!h.grid){
                    return false
                    }if(!c){
                    c="id"
                    }if(e&&e.length>0){
                    a(e).each(function(m){
                        i=this;k=h.rows.namedItem(i[c]);if(k){
                            g=i[c];if(d===true){
                                if(h.p.jsonReader.repeatitems===true){
                                    if(h.p.jsonReader.cell){
                                        i=i[h.p.jsonReader.cell]
                                        }for(var l=0;l<i.length;l++){
                                        j=h.formatter(g,i[l],l,i,"edit");if(h.p.treeGrid===true&&b==h.p.ExpandColumn){
                                            a("td:eq("+l+") > span:first",k).html(j).attr("title",a.jgrid.stripHtml(j))
                                            }else{
                                            a("td:eq("+l+")",k).html(j).attr("title",a.jgrid.stripHtml(j))
                                            }
                                        }f=true;return true
                                    }
                                }a(h.p.colModel).each(function(n){
                                b=d===true?this.jsonmap||this.name:this.name;if(i[b]!=undefined){
                                    j=h.formatter(g,i[b],n,i,"edit");if(h.p.treeGrid===true&&b==h.p.ExpandColumn){
                                        a("td:eq("+n+") > span:first",k).html(j).attr("title",a.jgrid.stripHtml(j))
                                        }else{
                                        a("td:eq("+n+")",k).html(j).attr("title",a.jgrid.stripHtml(j))
                                        }f=true
                                    }
                                })
                            }
                        })
                    }
                });return f
            },
        filterGrid:function(c,b){
            b=a.extend({
                gridModel:false,
                gridNames:false,
                gridToolbar:false,
                filterModel:[],
                formtype:"horizontal",
                autosearch:true,
                formclass:"filterform",
                tableclass:"filtertable",
                buttonclass:"filterbutton",
                searchButton:"Search",
                clearButton:"Clear",
                enableSearch:false,
                enableClear:false,
                beforeSearch:null,
                afterSearch:null,
                beforeClear:null,
                afterClear:null,
                url:"",
                marksearched:true
            },b||{});return this.each(function(){
                var l=this;this.p=b;if(this.p.filterModel.length==0&&this.p.gridModel===false){
                    alert("No filter is set");return
                }if(!c){
                    alert("No target grid is set!");return
                }this.p.gridid=c.indexOf("#")!=-1?c:"#"+c;var d=a(this.p.gridid).getGridParam("colModel");if(d){
                    if(this.p.gridModel===true){
                        var e=a(this.p.gridid)[0];var g;a.each(d,function(o,p){
                            var m=[];this.search=this.search===false?false:true;if(this.editrules&&this.editrules.searchhidden===true){
                                g=true
                                }else{
                                if(this.hidden===true){
                                    g=false
                                    }else{
                                    g=true
                                    }
                                }if(this.search===true&&g===true){
                                if(l.p.gridNames===true){
                                    m.label=e.p.colNames[o]
                                    }else{
                                    m.label=""
                                    }m.name=this.name;m.index=this.index||this.name;m.stype=this.edittype||"text";if(m.stype!="select"){
                                    m.stype="text"
                                    }m.defval=this.defval||"";m.surl=this.surl||"";m.sopt=this.editoptions||{};m.width=this.width;l.p.filterModel.push(m)
                                }
                            })
                        }else{
                        a.each(l.p.filterModel,function(o,p){
                            for(var m=0;m<d.length;m++){
                                if(this.name==d[m].name){
                                    this.index=d[m].index||this.name;break
                                }
                                }if(!this.index){
                                this.index=this.name
                                }
                            })
                        }
                    }else{
                    alert("Could not get grid colModel");return
                }var h=function(){
                    var q={},p=0,n;var o=a(l.p.gridid)[0],m;o.p.searchdata={};if(a.isFunction(l.p.beforeSearch)){
                        l.p.beforeSearch()
                        }a.each(l.p.filterModel,function(t,v){
                        m=this.index;switch(this.stype){
                            case"select":n=a("select[name="+m+"]",l).val();if(n){
                                q[m]=n;if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).addClass("dirty-cell")
                                    }p++
                            }else{
                                if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).removeClass("dirty-cell")
                                    }try{
                                    delete o.p.postData[this.index]
                                }catch(u){}
                                }break;default:n=a("input[name="+m+"]",l).val();if(n){
                                q[m]=n;if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).addClass("dirty-cell")
                                    }p++
                            }else{
                                if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).removeClass("dirty-cell")
                                    }try{
                                    delete o.p.postData[this.index]
                                }catch(u){}
                                }
                            }
                        });var r=p>0?true:false;a.extend(o.p.postData,q);var s;if(l.p.url){
                        s=a(o).getGridParam("url");a(o).setGridParam({
                            url:l.p.url
                            })
                        }a(o).setGridParam({
                        search:r,
                        page:1
                    }).trigger("reloadGrid");if(s){
                        a(o).setGridParam({
                            url:s
                        })
                        }if(a.isFunction(l.p.afterSearch)){
                        l.p.afterSearch()
                        }
                    };var k=function(){
                    var q={},n,p=0;var o=a(l.p.gridid)[0],m;if(a.isFunction(l.p.beforeClear)){
                        l.p.beforeClear()
                        }a.each(l.p.filterModel,function(t,w){
                        m=this.index;n=(this.defval)?this.defval:"";if(!this.stype){
                            this.stype=="text"
                            }switch(this.stype){
                            case"select":var v;a("select[name="+m+"] option",l).each(function(x){
                                if(x==0){
                                    this.selected=true
                                    }if(a(this).text()==n){
                                    this.selected=true;v=a(this).val();return false
                                    }
                                });if(v){
                                q[m]=v;if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).addClass("dirty-cell")
                                    }p++
                            }else{
                                if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).removeClass("dirty-cell")
                                    }try{
                                    delete o.p.postData[this.index]
                                }catch(u){}
                                }break;case"text":a("input[name="+m+"]",l).val(n);if(n){
                                q[m]=n;if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).addClass("dirty-cell")
                                    }p++
                            }else{
                                if(l.p.marksearched){
                                    a("#jqgh_"+this.name,o.grid.hDiv).removeClass("dirty-cell")
                                    }try{
                                    delete o.p.postData[this.index]
                                }catch(u){}
                                }break
                            }
                        });var r=p>0?true:false;a.extend(o.p.postData,q);var s;if(l.p.url){
                        s=a(o).getGridParam("url");a(o).setGridParam({
                            url:l.p.url
                            })
                        }a(o).setGridParam({
                        search:r,
                        page:1
                    }).trigger("reloadGrid");if(s){
                        a(o).setGridParam({
                            url:s
                        })
                        }if(a.isFunction(l.p.afterClear)){
                        l.p.afterClear()
                        }
                    };var i=function(){
                    var q=document.createElement("tr");var n,s,m,o,r,p;if(l.p.formtype=="horizontal"){
                        a(f).append(q)
                        }a.each(l.p.filterModel,function(A,v){
                        o=document.createElement("td");a(o).append("<label for='"+this.name+"'>"+this.label+"</label>");r=document.createElement("td");var z=this;if(!this.stype){
                            this.stype="text"
                            }switch(this.stype){
                            case"select":if(this.surl){
                                a(r).load(this.surl,function(){
                                    if(z.defval){
                                        a("select",this).val(z.defval)
                                        }a("select",this).attr({
                                        name:z.index||z.name,
                                        id:"sg_"+z.name
                                        });if(z.sopt){
                                        a("select",this).attr(z.sopt)
                                        }if(l.p.gridToolbar===true&&z.width){
                                        a("select",this).width(z.width)
                                        }if(l.p.autosearch===true){
                                        a("select",this).change(function(E){
                                            h();return false
                                            })
                                        }
                                    })
                                }else{
                                if(z.sopt.value){
                                    var t=z.sopt.value;var w=document.createElement("select");a(w).attr({
                                        name:z.index||z.name,
                                        id:"sg_"+z.name
                                        }).attr(z.sopt);if(typeof t==="string"){
                                        var u=t.split(";"),D,x;for(var y=0;y<u.length;y++){
                                            D=u[y].split(":");x=document.createElement("option");x.value=D[0];x.innerHTML=D[1];if(D[1]==z.defval){
                                                x.selected="selected"
                                                }w.appendChild(x)
                                            }
                                        }else{
                                        if(typeof t==="object"){
                                            for(var C in t){
                                                A++;x=document.createElement("option");x.value=C;x.innerHTML=t[C];if(t[C]==z.defval){
                                                    x.selected="selected"
                                                    }w.appendChild(x)
                                                }
                                            }
                                        }if(l.p.gridToolbar===true&&z.width){
                                        a(w).width(z.width)
                                        }a(r).append(w);if(l.p.autosearch===true){
                                        a(w).change(function(E){
                                            h();return false
                                            })
                                        }
                                    }
                                }break;case"text":var B=this.defval?this.defval:"";a(r).append("<input type='text' name='"+(this.index||this.name)+"' id='sg_"+this.name+"' value='"+B+"'/>");if(z.sopt){
                                a("input",r).attr(z.sopt)
                                }if(l.p.gridToolbar===true&&z.width){
                                if(a.browser.msie){
                                    a("input",r).width(z.width-4)
                                    }else{
                                    a("input",r).width(z.width-2)
                                    }
                                }if(l.p.autosearch===true){
                                a("input",r).keypress(function(F){
                                    var E=F.charCode?F.charCode:F.keyCode?F.keyCode:0;if(E==13){
                                        h();return false
                                        }return this
                                    })
                                }break
                            }if(l.p.formtype=="horizontal"){
                            if(l.p.gridToolbar===true&&l.p.gridNames===false){
                                a(q).append(r)
                                }else{
                                a(q).append(o).append(r)
                                }a(q).append(r)
                            }else{
                            n=document.createElement("tr");a(n).append(o).append(r);a(f).append(n)
                            }
                        });r=document.createElement("td");if(l.p.enableSearch===true){
                        s="<input type='button' id='sButton' class='"+l.p.buttonclass+"' value='"+l.p.searchButton+"'/>";a(r).append(s);a("input#sButton",r).click(function(){
                            h();return false
                            })
                        }if(l.p.enableClear===true){
                        m="<input type='button' id='cButton' class='"+l.p.buttonclass+"' value='"+l.p.clearButton+"'/>";a(r).append(m);a("input#cButton",r).click(function(){
                            k();return false
                            })
                        }if(l.p.enableClear===true||l.p.enableSearch===true){
                        if(l.p.formtype=="horizontal"){
                            a(q).append(r)
                            }else{
                            n=document.createElement("tr");a(n).append("<td>&nbsp;</td>").append(r);a(f).append(n)
                            }
                        }
                    };var j=a("<form name='SearchForm' style=display:inline;' class='"+this.p.formclass+"'></form>");var f=a("<table class='"+this.p.tableclass+"' cellspacing='0' cellpading='0' border='0'><tbody></tbody></table>");a(j).append(f);i();a(this).append(j);this.triggerSearch=function(){
                    h()
                    };this.clearSearch=function(){
                    k()
                    }
                })
            },
        filterToolbar:function(b){
            b=a.extend({
                autosearch:true,
                beforeSearch:null,
                afterSearch:null,
                beforeClear:null,
                afterClear:null,
                searchurl:""
            },b||{});return this.each(function(){
                var g=this;var c=function(){
                    var o={},n=0,m,l;g.p.searchdata={};if(a.isFunction(b.beforeSearch)){
                        b.beforeSearch()
                        }a.each(g.p.colModel,function(r,t){
                        l=this.index||this.name;switch(this.stype){
                            case"select":m=a("select[name="+l+"]",g.grid.hDiv).val();if(m){
                                o[l]=m;n++
                            }else{
                                try{
                                    delete g.p.postData[l]
                                }catch(s){}
                                }break;case"text":m=a("input[name="+l+"]",g.grid.hDiv).val();if(m){
                                o[l]=m;n++
                            }else{
                                try{
                                    delete g.p.postData[l]
                                }catch(s){}
                                }break
                            }
                        });var p=n>0?true:false;a.extend(g.p.postData,o);var q;if(g.p.searchurl){
                        q=g.p.url;a(g).setGridParam({
                            url:g.p.searchurl
                            })
                        }a(g).setGridParam({
                        search:p,
                        page:1
                    }).trigger("reloadGrid");if(q){
                        a(g).setGridParam({
                            url:q
                        })
                        }if(a.isFunction(b.afterSearch)){
                        b.afterSearch()
                        }
                    };var j=function(){
                    var o={},m,n=0,l;if(a.isFunction(b.beforeClear)){
                        b.beforeClear()
                        }a.each(g.p.colModel,function(r,u){
                        m=(this.searchoptions&&this.searchoptions.defaultValue)?this.searchoptions.defaultValue:"";l=this.index||this.name;switch(this.stype){
                            case"select":var t;a("select[name="+l+"] option",g.grid.hDiv).each(function(v){
                                if(v==0){
                                    this.selected=true
                                    }if(a(this).text()==m){
                                    this.selected=true;t=a(this).val();return false
                                    }
                                });if(t){
                                o[l]=t;n++
                            }else{
                                try{
                                    delete g.p.postData[l]
                                }catch(s){}
                                }break;case"text":a("input[name="+l+"]",g.grid.hDiv).val(m);if(m){
                                o[l]=m;n++
                            }else{
                                try{
                                    delete g.p.postData[l]
                                }catch(s){}
                                }break
                            }
                        });var p=n>0?true:false;a.extend(g.p.postData,o);var q;if(g.p.searchurl){
                        q=g.p.url;a(g).setGridParam({
                            url:g.p.searchurl
                            })
                        }a(g).setGridParam({
                        search:p,
                        page:1
                    }).trigger("reloadGrid");if(q){
                        a(g).setGridParam({
                            url:q
                        })
                        }if(a.isFunction(b.afterClear)){
                        b.afterClear()
                        }
                    };var k=function(){
                    var l=a("tr.ui-search-toolbar",g.grid.hDiv);if(l.css("display")=="none"){
                        l.show()
                        }else{
                        l.hide()
                        }
                    };function f(l,n){
                    var m=a(l);if(m[0]!=null){
                        jQuery.each(n,function(){
                            if(this.data!=null){
                                m.bind(this.type,this.data,this.fn)
                                }else{
                                m.bind(this.type,this.fn)
                                }
                            })
                        }
                    }var h=a("<tr class='ui-search-toolbar' role='rowheader'></tr>"),d,i,e;a.each(g.p.colModel,function(s,o){
                    var u=this;d=a("<th role='columnheader' class='ui-state-default ui-th-column'></th>");i=a("<div style='width:100%;position:relative;height:100%;padding-right:0.3em;'></div>");if(this.hidden===true){
                        a(d).css("display","none")
                        }this.search=this.search===false?false:true;if(typeof this.stype=="undefined"){
                        this.stype="text"
                        }e=a.extend({},this.searchoptions||{});if(this.search){
                        switch(this.stype){
                            case"select":if(this.surl){
                                a(i).load(this.surl,{
                                    _nsd:(new Date().getTime())
                                    },function(){
                                    if(e.defaultValue){
                                        a("select",this).val(e.defaultValue)
                                        }a("select",this).attr({
                                        name:u.index||u.name,
                                        id:"gs_"+u.name
                                        });if(e.attr){
                                        a("select",this).attr(e.attr)
                                        }a("select",this).css({
                                        width:"100%"
                                    });if(e.dataInit!=null){
                                        e.dataInit(a("select",this)[0])
                                        }if(e.dataEvents!=null){
                                        f(a("select",this)[0],e.dataEvents)
                                        }if(b.autosearch===true){
                                        a("select",this).change(function(n){
                                            c();return false
                                            })
                                        }
                                    })
                                }else{
                                if(u.editoptions&&u.editoptions.value){
                                    var l=u.editoptions.value,p=document.createElement("select");p.style.width="100%";a(p).attr({
                                        name:u.index||u.name,
                                        id:"gs_"+u.name
                                        });if(typeof l==="string"){
                                        var m=l.split(";"),w,q;for(var r=0;r<m.length;r++){
                                            w=m[r].split(":");q=document.createElement("option");q.value=w[0];q.innerHTML=w[1];p.appendChild(q)
                                            }
                                        }else{
                                        if(typeof l==="object"){
                                            for(var v in l){
                                                s++;q=document.createElement("option");q.value=v;q.innerHTML=l[v];p.appendChild(q)
                                                }
                                            }
                                        }if(e.defaultValue){
                                        a(p).val(e.defaultValue)
                                        }if(e.attr){
                                        a(p).attr(e.attr)
                                        }if(e.dataInit!=null){
                                        e.dataInit(p)
                                        }if(e.dataEvents!=null){
                                        f(p,e.dataEvents)
                                        }a(i).append(p);if(b.autosearch===true){
                                        a(p).change(function(n){
                                            c();return false
                                            })
                                        }
                                    }
                                }break;case"text":var t=e.defaultValue?e.defaultValue:"";a(i).append("<input type='text' style='width:95%;padding:0px;' name='"+(u.index||u.name)+"' id='gs_"+u.name+"' value='"+t+"'/>");if(e.attr){
                                a("input",i).attr(e.attr)
                                }if(e.dataInit!=null){
                                e.dataInit(a("input",i)[0])
                                }if(e.dataEvents!=null){
                                f(a("input",i)[0],e.dataEvents)
                                }if(b.autosearch===true){
                                a("input",i).keypress(function(x){
                                    var n=x.charCode?x.charCode:x.keyCode?x.keyCode:0;if(n==13){
                                        c();return false
                                        }return this
                                    })
                                }break
                            }
                        }a(d).append(i);a(h).append(d)
                    });a("table thead",g.grid.hDiv).append(h);this.triggerToolbar=function(){
                    c()
                    };this.clearToolbar=function(){
                    j()
                    };this.toggleToolbar=function(){
                    k()
                    }
                })
            }
        })
    })(jQuery);var showModal=function(a){
    a.w.show()
    };var closeModal=function(a){
    a.w.hide().attr("aria-hidden","true");if(a.o){
        a.o.remove()
        }
    };var createModal=function(i,d,a,k,m,l){
    var h=document.createElement("div");h.className="ui-widget ui-widget-content ui-corner-all ui-jqdialog";h.id=i.themodal;var b=document.createElement("div");b.className="ui-jqdialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix";b.id=i.modalhead;jQuery(b).append("<span class='ui-jqdialog-title'>"+a.caption+"</span>");var j=jQuery("<a href='javascript:void(0)' class='ui-jqdialog-titlebar-close ui-corner-all'></a>").hover(function(){
        j.addClass("ui-state-hover")
        },function(){
        j.removeClass("ui-state-hover")
        }).append("<span class='ui-icon ui-icon-closethick'></span>");jQuery(b).append(j);var g=document.createElement("div");jQuery(g).addClass("ui-jqdialog-content ui-widget-content").attr("id",i.modalcontent);jQuery(g).append(d);h.appendChild(g);jQuery(h).prepend(b);if(l===true){
        jQuery("body").append(h)
        }else{
        jQuery(h).insertBefore(k)
        }if(typeof a.jqModal==="undefined"){
        a.jqModal=true
        }if(jQuery.fn.jqm&&a.jqModal===true){
        if(a.left==0&&a.top==0){
            var f=[];f=findPos(m);a.left=f[0]+4;a.top=f[1]+4
            }
        }jQuery("a.ui-jqdialog-titlebar-close",b).click(function(o){
        var n=jQuery("#"+i.themodal).data("onClose")||a.onClose;var p=jQuery("#"+i.themodal).data("gbox")||a.gbox;hideModal("#"+i.themodal,{
            gb:p,
            jqm:a.jqModal,
            onClose:n
        });return false
        });if(a.width==0||!a.width){
        a.width=300
        }if(a.height==0||!a.height){
        a.height=200
        }if(!a.zIndex){
        a.zIndex=950
        }jQuery(h).css({
        top:a.top+"px",
        left:a.left+"px",
        width:isNaN(a.width)?"auto":a.width+"px",
        height:isNaN(a.height)?"auto":a.height+"px",
        zIndex:a.zIndex,
        overflow:"hidden"
    }).attr({
        tabIndex:"-1",
        role:"dialog",
        "aria-labelledby":i.modalhead,
        "aria-hidden":"true"
    });if(typeof a.drag=="undefined"){
        a.drag=true
        }if(typeof a.resize=="undefined"){
        a.resize=true
        }if(a.drag){
        jQuery(b).css("cursor","move");if(jQuery.fn.jqDrag){
            jQuery(h).jqDrag(b)
            }else{
            try{
                jQuery(h).draggable({
                    handle:jQuery("#"+b.id)
                    })
                }catch(c){}
            }
        }if(a.resize){
        if(jQuery.fn.jqResize){
            jQuery(h).append("<div class='jqResize ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se ui-icon-grip-diagonal-se'></div>");jQuery("#"+i.themodal).jqResize(".jqResize",i.scrollelm?"#"+i.scrollelm:false)
            }else{
            try{
                jQuery(h).resizable({
                    handles:"se",
                    alsoResize:i.scrollelm?"#"+i.scrollelm:false
                    })
                }catch(c){}
            }
        }if(a.closeOnEscape===true){
        jQuery(h).keydown(function(o){
            if(o.which==27){
                var n=jQuery("#"+i.themodal).data("onClose")||a.onClose;hideModal(this,{
                    gb:a.gbox,
                    jqm:a.jqModal,
                    onClose:n
                })
                }
            })
        }
    };var viewModal=function(a,c){
    c=jQuery.extend({
        toTop:true,
        overlay:10,
        modal:false,
        onShow:showModal,
        onHide:closeModal,
        gbox:"",
        jqm:true,
        jqM:true
    },c||{});if(jQuery.fn.jqm&&c.jqm==true){
        if(c.jqM){
            jQuery(a).attr("aria-hidden","false").jqm(c).jqmShow()
            }else{
            jQuery(a).attr("aria-hidden","false").jqmShow()
            }
        }else{
        if(c.gbox!=""){
            jQuery(".jqgrid-overlay:first",c.gbox).show();jQuery(a).data("gbox",c.gbox)
            }jQuery(a).show().attr("aria-hidden","false");try{
            jQuery(":input:visible",a)[0].focus()
            }catch(b){}
        }
    };var hideModal=function(a,d){
    d=jQuery.extend({
        jqm:true,
        gb:""
    },d||{});if(d.onClose){
        var b=d.onClose(a);if(typeof b=="boolean"&&!b){
            return
        }
        }if(jQuery.fn.jqm&&d.jqm===true){
        jQuery(a).attr("aria-hidden","true").jqmHide()
        }else{
        if(d.gb!=""){
            try{
                jQuery(".jqgrid-overlay:first",d.gb).hide()
                }catch(c){}
            }jQuery(a).hide().attr("aria-hidden","true")
        }
    };function info_dialog(k,f,b,j){
    var g={
        width:290,
        height:"auto",
        dataheight:"auto",
        drag:true,
        resize:false,
        caption:"<b>"+k+"</b>",
        left:250,
        top:170,
        jqModal:true,
        closeOnEscape:true,
        align:"center",
        buttonalign:"center"
    };jQuery.extend(g,j||{});var c=g.jqModal;if(jQuery.fn.jqm&&!c){
        c=false
        }var h=isNaN(g.dataheight)?g.dataheight:g.dataheight+"px",i="text-align:"+g.align+";";var a="<div id='info_id'>";a+="<div id='infocnt' style='margin:0px;padding-bottom:1em;width:100%;overflow:auto;position:relative;height:"+h+";"+i+"'>"+f+"</div>";a+=b?"<div class='ui-widget-content ui-helper-clearfix' style='text-align:"+g.buttonalign+";padding-bottom:0.8em;padding-top:0.5em;background-image: none;border-width: 1px 0 0 0;'><a href='javascript:void(0)' id='closedialog' class='fm-button ui-state-default ui-corner-all'>"+b+"</a></div>":"";a+="</div>";try{
        jQuery("#info_dialog").remove()
        }catch(d){}createModal({
        themodal:"info_dialog",
        modalhead:"info_head",
        modalcontent:"info_content",
        scrollelm:"infocnt"
    },a,g,"","",true);jQuery("#closedialog","#info_id").click(function(l){
        hideModal("#info_dialog",{
            jqm:c
        });return false
        });jQuery(".fm-button","#info_dialog").hover(function(){
        jQuery(this).addClass("ui-state-hover")
        },function(){
        jQuery(this).removeClass("ui-state-hover")
        });viewModal("#info_dialog",{
        onHide:function(e){
            e.w.hide().remove();if(e.o){
                e.o.remove()
                }
            },
        modal:true,
        jqm:c
    })
    }function findPos(a){
    var b=curtop=0;if(a.offsetParent){
        do{
            b+=a.offsetLeft;curtop+=a.offsetTop
            }while(a=a.offsetParent)
    }return[b,curtop]
    }function isArray(a){
    if(a.constructor.toString().indexOf("Array")==-1){
        return false
        }else{
        return true
        }
    }function createEl(h,r,f,s){
    var g="";if(r.defaultValue){
        delete r.defaultValue
        }function o(i,e){
        if(jQuery.isFunction(e.dataInit)){
            i.id=e.id;e.dataInit(i);delete e.id;delete e.dataInit
            }if(e.dataEvents){
            jQuery.each(e.dataEvents,function(){
                if(this.data!=null){
                    jQuery(i).bind(this.type,this.data,this.fn)
                    }else{
                    jQuery(i).bind(this.type,this.fn)
                    }
                });delete e.dataEvents
            }return e
        }switch(h){
        case"textarea":g=document.createElement("textarea");if(s){
            if(!r.cols){
                jQuery(g).css({
                    width:"98%"
                })
                }
            }else{
            if(!r.cols){
                r.cols=20
                }
            }if(!r.rows){
            r.rows=2
            }if(f=="&nbsp;"||f=="&#160;"||(f.length==1&&f.charCodeAt(0)==160)){
            f=""
            }g.value=f;r=o(g,r);jQuery(g).attr(r);break;case"checkbox":g=document.createElement("input");g.type="checkbox";if(!r.value){
            var p=f.toLowerCase();if(p.search(/(false|0|no|off|undefined)/i)<0&&p!==""){
                g.checked=true;g.defaultChecked=true;g.value=f
                }else{
                g.value="on"
                }jQuery(g).attr("offval","off")
            }else{
            var a=r.value.split(":");if(f===a[0]){
                g.checked=true;g.defaultChecked=true
                }g.value=a[0];jQuery(g).attr("offval",a[1]);try{
                delete r.value
                }catch(l){}
            }r=o(g,r);jQuery(g).attr(r);break;case"select":g=document.createElement("select");var q=r.multiple===true?true:false;if(r.dataUrl!=null){
            jQuery.get(r.dataUrl,{
                _nsd:(new Date().getTime())
                },function(t){
                try{
                    delete r.dataUrl;delete r.value
                    }catch(u){}var i=jQuery(t).html();jQuery(g).append(i);r=o(g,r);if(typeof r.size==="undefined"){
                    r.size=q?3:1
                    }jQuery(g).attr(r);setTimeout(function(){
                    jQuery("option",g).each(function(e){
                        if(jQuery(this).text()==f||jQuery(this).html()==f){
                            this.selected="selected";return false
                            }
                        })
                    },0)
                },"html")
            }else{
            if(r.value){
                var j=[],k;if(q){
                    j=f.split(",");j=jQuery.map(j,function(e){
                        return jQuery.trim(e)
                        });if(typeof r.size==="undefined"){
                        r.size=3
                        }
                    }else{
                    r.size=1
                    }if(typeof r.value==="function"){
                    r.value=r.value()
                    }if(typeof r.value==="string"){
                    var c=r.value.split(";"),n,d;for(k=0;k<c.length;k++){
                        n=c[k].split(":");d=document.createElement("option");d.value=n[0];d.innerHTML=n[1];if(!q&&(n[0]==f||n[1]==f)){
                            d.selected="selected"
                            }if(q&&(jQuery.inArray(n[1],j)>-1||jQuery.inArray(n[0],j)>-1)){
                            d.selected="selected"
                            }g.appendChild(d)
                        }
                    }else{
                    if(typeof r.value==="object"){
                        var b=r.value;k=0;for(var m in b){
                            k++;d=document.createElement("option");d.value=m;d.innerHTML=b[m];if(!q&&(m==f||b[m]==f)){
                                d.selected="selected"
                                }if(q&&(jQuery.inArray(b[m],j)>-1||jQuery.inArray(m,j)>-1)){
                                d.selected="selected"
                                }g.appendChild(d)
                            }
                        }
                    }r=o(g,r);try{
                    delete r.value
                    }catch(l){}jQuery(g).attr(r)
                }
            }break;case"text":case"password":case"button":g=document.createElement("input");g.type=h;g.value=jQuery.jgrid.htmlDecode(f);r=o(g,r);if(h!="button"){
            if(s){
                if(!r.size){
                    jQuery(g).css({
                        width:"98%"
                    })
                    }
                }else{
                if(!r.size){
                    r.size=20
                    }
                }
            }jQuery(g).attr(r);break;case"image":case"file":g=document.createElement("input");g.type=h;r=o(g,r);jQuery(g).attr(r);break
            }return g
    }function checkValues(c,l,j){
    var f,h,m;if(typeof(l)=="string"){
        for(h=0,len=j.p.colModel.length;h<len;h++){
            if(j.p.colModel[h].name==l){
                f=j.p.colModel[h].editrules;l=h;try{
                    m=j.p.colModel[h].formoptions.label
                    }catch(k){}break
            }
            }
        }else{
        if(l>=0){
            f=j.p.colModel[l].editrules
            }
        }if(f){
        if(!m){
            m=j.p.colNames[l]
            }if(f.required===true){
            if(c.match(/^s+$/)||c==""){
                return[false,m+": "+jQuery.jgrid.edit.msg.required,""]
                }
            }var d=f.required===false?false:true;if(f.number===true){
            if(!(d===false&&isEmpty(c))){
                if(isNaN(c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.number,""]
                    }
                }
            }if(typeof f.minValue!="undefined"&&!isNaN(f.minValue)){
            if(parseFloat(c)<parseFloat(f.minValue)){
                return[false,m+": "+jQuery.jgrid.edit.msg.minValue+" "+f.minValue,""]
                }
            }if(typeof f.maxValue!="undefined"&&!isNaN(f.maxValue)){
            if(parseFloat(c)>parseFloat(f.maxValue)){
                return[false,m+": "+jQuery.jgrid.edit.msg.maxValue+" "+f.maxValue,""]
                }
            }var a;if(f.email===true){
            if(!(d===false&&isEmpty(c))){
                a=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;if(!a.test(c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.email,""]
                    }
                }
            }if(f.integer===true){
            if(!(d===false&&isEmpty(c))){
                if(isNaN(c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.integer,""]
                    }if((c%1!=0)||(c.indexOf(".")!=-1)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.integer,""]
                    }
                }
            }if(f.date===true){
            if(!(d===false&&isEmpty(c))){
                var b=j.p.colModel[l].datefmt||"Y-m-d";if(!checkDate(b,c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.date+" - "+b,""]
                    }
                }
            }if(f.time===true){
            if(!(d===false&&isEmpty(c))){
                if(!checkTime(c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.date+" - hh:mm (am/pm)",""]
                    }
                }
            }if(f.url===true){
            if(!(d===false&&isEmpty(c))){
                a=/^(((https?)|(ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?)/i;if(!a.test(c)){
                    return[false,m+": "+jQuery.jgrid.edit.msg.url,""]
                    }
                }
            }
        }return[true,"",""]
    }function checkDate(l,c){
    var e={},n;l=l.toLowerCase();if(l.indexOf("/")!=-1){
        n="/"
        }else{
        if(l.indexOf("-")!=-1){
            n="-"
            }else{
            if(l.indexOf(".")!=-1){
                n="."
                }else{
                n="/"
                }
            }
        }l=l.split(n);c=c.split(n);if(c.length!=3){
        return false
        }var f=-1,m,g=-1,d=-1;for(var h=0;h<l.length;h++){
        var b=isNaN(c[h])?0:parseInt(c[h],10);e[l[h]]=b;m=l[h];if(m.indexOf("y")!=-1){
            f=h
            }if(m.indexOf("m")!=-1){
            d=h
            }if(m.indexOf("d")!=-1){
            g=h
            }
        }if(l[f]=="y"||l[f]=="yyyy"){
        m=4
        }else{
        if(l[f]=="yy"){
            m=2
            }else{
            m=-1
            }
        }var a=DaysArray(12);var k;if(f===-1){
        return false
        }else{
        k=e[l[f]].toString();if(m==2&&k.length==1){
            m=1
            }if(k.length!=m||e[l[f]]==0){
            return false
            }
        }if(d===-1){
        return false
        }else{
        k=e[l[d]].toString();if(k.length<1||e[l[d]]<1||e[l[d]]>12){
            return false
            }
        }if(g===-1){
        return false
        }else{
        k=e[l[g]].toString();if(k.length<1||e[l[g]]<1||e[l[g]]>31||(e[l[d]]==2&&e[l[g]]>daysInFebruary(e[l[f]]))||e[l[g]]>a[e[l[d]]]){
            return false
            }
        }return true
    }function daysInFebruary(a){
    return(((a%4==0)&&((!(a%100==0))||(a%400==0)))?29:28)
    }function DaysArray(b){
    for(var a=1;a<=b;a++){
        this[a]=31;if(a==4||a==6||a==9||a==11){
            this[a]=30
            }if(a==2){
            this[a]=29
            }
        }return this
    }function isEmpty(a){
    if(a.match(/^s+$/)||a==""){
        return true
        }else{
        return false
        }
    }function checkTime(c){
    var b=/^(\d{1,2}):(\d{2})([ap]m)?$/,a;if(!isEmpty(c)){
        a=c.match(b);if(a){
            if(a[3]){
                if(a[1]<1||a[1]>12){
                    return false
                    }
                }else{
                if(a[1]>23){
                    return false
                    }
                }if(a[2]>59){
                return false
                }
            }else{
            return false
            }
        }return true
    };(function(b){
    var a=null;b.fn.extend({
        searchGrid:function(c){
            c=b.extend({
                recreateFilter:false,
                drag:true,
                sField:"searchField",
                sValue:"searchString",
                sOper:"searchOper",
                sFilter:"filters",
                beforeShowSearch:null,
                afterShowSearch:null,
                onInitializeSearch:null,
                closeAfterSearch:false,
                closeOnEscape:false,
                multipleSearch:false,
                sopt:null,
                onClose:null
            },b.jgrid.search,c||{});return this.each(function(){
                var l=this;if(!l.grid){
                    return
                }if(b.fn.searchFilter){
                    var g="fbox_"+l.p.id;if(c.recreateFilter===true){
                        b("#"+g).remove()
                        }if(b("#"+g).html()!=null){
                        if(b.isFunction(c.beforeShowSearch)){
                            c.beforeShowSearch(b("#"+g))
                            }f();if(b.isFunction(c.afterShowSearch)){
                            c.afterShowSearch(b("#"+g))
                            }
                        }else{
                        var n=[],u=jQuery("#"+l.p.id).getGridParam("colNames"),r=jQuery("#"+l.p.id).getGridParam("colModel"),t=["eq","ne","lt","le","gt","ge","bw","bn","in","ni","ew","en","cn","nc"],i,q,h;b.each(r,function(x,C){
                            var z=(typeof C.search==="undefined")?true:C.search,y=(C.hidden===true),k=b.extend({},{
                                text:u[x],
                                value:C.index||C.name
                                },this.searchoptions),w=(k.searchhidden===true)||true;if(typeof k.sopt=="undefined"){
                                k.sopt=t
                                }h=0;k.ops=[];for(i=0;i<k.sopt.length;i++){
                                if((q=b.inArray(k.sopt[i],t))!=-1){
                                    k.ops[h]={
                                        op:k.sopt[i],
                                        text:c.odata[q]
                                        };h++
                                }
                                }if(typeof(this.stype)==="undefined"){
                                this.stype="text"
                                }if(this.stype=="select"){
                                if(k.dataUrl!=null){}else{
                                    if(this.editoptions){
                                        var j=this.editoptions.value;if(j){
                                            k.dataValues=[];if(typeof(j)==="string"){
                                                var e=j.split(";"),B;for(i=0;i<e.length;i++){
                                                    B=e[i].split(":");k.dataValues[i]={
                                                        value:B[0],
                                                        text:B[1]
                                                        }
                                                    }
                                                }else{
                                                if(typeof(j)==="object"){
                                                    i=0;for(var A in j){
                                                        k.dataValues[i]={
                                                            value:A,
                                                            text:j[A]
                                                            };i++
                                                    }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }if((w&&z)||(z&&!y)){
                                n.push(k)
                                }
                            });if(n.length>0){
                            var p=jQuery.fn.searchFilter.defaults.operators;if(c.sopt!=null){
                                p=[];h=0;for(i=0;c.sopt.length<0;i++){
                                    if((q=b.inArray(c.sopt[i],t))!=-1){
                                        p[h]={
                                            op:c.sopt[i],
                                            text:c.odata[q]
                                            };h++
                                    }
                                    }
                                }b("<div id='"+g+"' role='dialog' tabindex='-1'></div>").insertBefore("#gview_"+l.p.id);jQuery("#"+g).searchFilter(n,{
                                groupOps:c.groupOps,
                                operators:p,
                                onClose:d,
                                resetText:c.Reset,
                                searchText:c.Find,
                                windowTitle:c.caption,
                                rulesText:c.rulesText,
                                matchText:c.matchText,
                                onSearch:s,
                                onReset:m,
                                stringResult:c.multipleSearch
                                });b(".ui-widget-overlay","#"+g).remove();if(c.drag===true){
                                b("#"+g+" table thead tr:first td:first").css("cursor","move");if(jQuery.fn.jqDrag){
                                    jQuery("#"+g).jqDrag(b("#"+g+" table thead tr:first td:first"))
                                    }else{
                                    try{
                                        b("#"+g).draggable({
                                            handle:jQuery("#"+g+" table thead tr:first td:first")
                                            })
                                        }catch(o){}
                                    }
                                }if(c.multipleSearch===false){
                                b(".ui-del, .ui-add, .ui-del, .ui-add-last, .matchText, .rulesText","#"+g).hide();b("select[name='groupOp']","#"+g).hide()
                                }if(b.isFunction(c.onInitializeSearch)){
                                c.onInitializeSearch(b("#"+g))
                                }if(b.isFunction(c.beforeShowSearch)){
                                c.beforeShowSearch(b("#"+g))
                                }f();if(b.isFunction(c.afterShowSearch)){
                                c.afterShowSearch(b("#"+g))
                                }if(c.closeOnEscape===true){
                                jQuery("#"+g).keydown(function(j){
                                    if(j.which==27){
                                        d(b("#"+g))
                                        }
                                    })
                                }
                            }
                        }
                    }function s(v){
                    var e=(v!==undefined),k=jQuery("#"+l.p.id),j={};if(c.multipleSearch===false){
                        j[c.sField]=v.rules[0].field;j[c.sValue]=v.rules[0].data;j[c.sOper]=v.rules[0].op
                        }else{
                        j[c.sFilter]=v
                        }k[0].p.search=e;b.extend(k[0].p.postData,j);k[0].p.page=1;k.trigger("reloadGrid");if(c.closeAfterSearch){
                        d(b("#"+g))
                        }
                    }function m(v){
                    var e=(v!==undefined),k=jQuery("#"+l.p.id),j=[];k[0].p.search=e;if(c.multipleSearch===false){
                        j[c.sField]=j[c.sValue]=j[c.sOper]=""
                        }else{
                        j[c.sFilter]=""
                        }b.extend(k[0].p.postData,j);k[0].p.page=1;k.trigger("reloadGrid")
                    }function d(e){
                    if(c.onClose){
                        var j=c.onClose(e);if(typeof j=="boolean"&&!j){
                            return
                        }
                        }e.hide();b(".jqgrid-overlay","#gbox_"+l.p.id).hide()
                    }function f(){
                    b("#"+g).show();b(".jqgrid-overlay","#gbox_"+l.p.id).show();try{
                        b(":input:visible","#"+g)[0].focus()
                        }catch(e){}
                    }
                })
            },
        editGridRow:function(c,d){
            d=b.extend({
                top:0,
                left:0,
                width:300,
                height:"auto",
                dataheight:"auto",
                modal:false,
                drag:true,
                resize:true,
                url:null,
                mtype:"POST",
                closeAfterAdd:false,
                clearAfterAdd:true,
                closeAfterEdit:false,
                reloadAfterSubmit:true,
                onInitializeForm:null,
                beforeInitData:null,
                beforeShowForm:null,
                afterShowForm:null,
                beforeSubmit:null,
                afterSubmit:null,
                onclickSubmit:null,
                afterComplete:null,
                onclickPgButtons:null,
                afterclickPgButtons:null,
                editData:{},
                recreateForm:false,
                jqModal:true,
                closeOnEscape:false,
                addedrow:"first",
                topinfo:"",
                bottominfo:"",
                saveicon:[],
                closeicon:[],
                savekey:[false,13],
                navkeys:[false,38,40],
                checkOnSubmit:false,
                checkOnUpdate:false,
                _savedData:{},
                onClose:null
            },b.jgrid.edit,d||{});a=d;return this.each(function(){
                var e=this;if(!e.grid||!c){
                    return
                }var B=e.p.id,x="FrmGrid_"+B,t="TblGrid_"+B,h={
                    themodal:"editmod"+B,
                    modalhead:"edithd"+B,
                    modalcontent:"editcnt"+B,
                    scrollelm:x
                },C=b.isFunction(a.beforeShowForm)?a.beforeShowForm:false,N=b.isFunction(a.afterShowForm)?a.afterShowForm:false,M=b.isFunction(a.beforeInitData)?a.beforeInitData:false,n=b.isFunction(a.onInitializeForm)?a.onInitializeForm:false,H=null,I=1,p=0,u,D,E,Q,G,A;if(c=="new"){
                    c="_empty";d.caption=d.addCaption
                    }else{
                    d.caption=d.editCaption
                    }if(d.recreateForm===true&&b("#"+h.themodal).html()!=null){
                    b("#"+h.themodal).remove()
                    }var j=true;if(d.checkOnUpdate&&d.jqModal&&!d.modal){
                    j=false
                    }if(b("#"+h.themodal).html()!=null){
                    b(".ui-jqdialog-title","#"+h.modalhead).html(d.caption);b("#FormError","#"+t).hide();if(M){
                        M(b("#"+x))
                        }m(c,e,x);if(c=="_empty"){
                        b("#pData, #nData","#"+t+"_2").hide()
                        }else{
                        b("#pData, #nData","#"+t+"_2").show()
                        }if(d.processing===true){
                        d.processing=false;b("#sData","#"+t).removeClass("ui-state-active")
                        }if(b("#"+x).data("disabled")===true){
                        b(".confirm","#"+h.themodal).hide();b("#"+x).data("disabled",false)
                        }if(C){
                        C(b("#"+x))
                        }b("#"+h.themodal).data("onClose",a.onClose);viewModal("#"+h.themodal,{
                        gbox:"#gbox_"+B,
                        jqm:d.jqModal,
                        jqM:false,
                        closeoverlay:j,
                        modal:d.modal
                        });if(!j){
                        b(".jqmOverlay").click(function(){
                            if(!f()){
                                return false
                                }hideModal("#"+h.themodal,{
                                gb:"#gbox_"+B,
                                jqm:d.jqModal,
                                onClose:a.onClose
                                });return false
                            })
                        }if(N){
                        N(b("#"+x))
                        }
                    }else{
                    b(e.p.colModel).each(function(V){
                        var W=this.formoptions;I=Math.max(I,W?W.colpos||0:0);p=Math.max(p,W?W.rowpos||0:0)
                        });var q=isNaN(d.dataheight)?d.dataheight:d.dataheight+"px";var L,S=b("<form name='FormPost' id='"+x+"' class='FormGrid' style='width:100%;overflow:auto;position:relative;height:"+q+";'></form>").data("disabled",false),z=b("<table id='"+t+"' class='EditTable' cellspacing='0' cellpading='0' border='0'><tbody></tbody></table>");b(S).append(z);L=b("<tr id='FormError' style='display:none'><td class='ui-state-error' colspan='"+(I*2)+"'></td></tr>");L[0].rp=0;b(z).append(L);if(a.topinfo){
                        L=b("<tr><td class='topinfo' colspan='"+(I*2)+"'>"+a.topinfo+"</td></tr>");L[0].rp=0;b(z).append(L)
                        }if(M){
                        M(b("#"+x))
                        }var y=r(c,e,z,I),k="<a href='javascript:void(0)' id='pData' class='fm-button ui-state-default ui-corner-left'><span class='ui-icon ui-icon-triangle-1-w'></span></div>",l="<a href='javascript:void(0)' id='nData' class='fm-button ui-state-default ui-corner-right'><span class='ui-icon ui-icon-triangle-1-e'></span></div>",g="<a href='javascript:void(0)' id='sData' class='fm-button ui-state-default ui-corner-all'>"+d.bSubmit+"</a>",s="<a href='javascript:void(0)' id='cData' class='fm-button ui-state-default ui-corner-all'>"+d.bCancel+"</a>";var P="<table border='0' class='EditTable' id='"+t+"_2'><tbody><tr id='Act_Buttons'><td class='navButton ui-widget-content'>"+k+l+"</td><td class='EditButton ui-widget-content'>"+g+"&nbsp;"+s+"</td></tr>";if(a.bottominfo){
                        P+="<tr><td class='bottominfo' colspan='2'>"+a.bottominfo+"</td></tr>"
                        }P+="</tbody></table>";if(p>0){
                        var w=[];b.each(b(z)[0].rows,function(V,W){
                            w[V]=W
                            });w.sort(function(W,V){
                            if(W.rp>V.rp){
                                return 1
                                }if(W.rp<V.rp){
                                return -1
                                }return 0
                            });b.each(w,function(V,W){
                            b("tbody",z).append(W)
                            })
                        }d.gbox="#gbox_"+B;var o=false;if(d.closeOnEscape===true){
                        d.closeOnEscape=false;o=true
                        }var O=b("<span></span>").append(S).append(P);createModal(h,O,d,"#gview_"+e.p.id,b("#gview_"+e.p.id)[0]);O=null;P=null;jQuery("#"+h.themodal).keydown(function(V){
                        if(b("#"+x).data("disabled")===true){
                            return false
                            }if(a.savekey[0]===true&&V.which==a.savekey[1]){
                            b("#sData","#"+t+"_2").trigger("click");return false
                            }if(V.which===27){
                            if(!f()){
                                return false
                                }if(o){
                                hideModal(this,{
                                    gb:d.gbox,
                                    jqm:d.jqModal,
                                    onClose:a.onClose
                                    })
                                }return false
                            }if(a.navkeys[0]===true){
                            if(b("#id_g","#"+t).val()=="_empty"){
                                return true
                                }if(V.which==a.navkeys[1]){
                                b("#pData","#"+t+"_2").trigger("click");return false
                                }if(V.which==a.navkeys[2]){
                                b("#nData","#"+t+"_2").trigger("click");return false
                                }
                            }
                        });if(d.checkOnUpdate){
                        b("a.ui-jqdialog-titlebar-close span","#"+h.themodal).removeClass("jqmClose");b("a.ui-jqdialog-titlebar-close","#"+h.themodal).unbind("click").click(function(){
                            if(!f()){
                                return false
                                }hideModal("#"+h.themodal,{
                                gb:"#gbox_"+B,
                                jqm:d.jqModal,
                                onClose:a.onClose
                                });return false
                            })
                        }d.saveicon=b.extend([true,"left","ui-icon-disk"],d.saveicon);d.closeicon=b.extend([true,"left","ui-icon-close"],d.closeicon);if(d.saveicon[0]==true){
                        b("#sData","#"+t+"_2").addClass(d.saveicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+d.saveicon[2]+"'></span>")
                        }if(d.closeicon[0]==true){
                        b("#cData","#"+t+"_2").addClass(d.closeicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+d.closeicon[2]+"'></span>")
                        }if(a.checkOnSubmit||a.checkOnUpdate){
                        g="<a href='javascript:void(0)' id='sNew' class='fm-button ui-state-default ui-corner-all' style='z-index:1002'>"+d.bYes+"</a>";l="<a href='javascript:void(0)' id='nNew' class='fm-button ui-state-default ui-corner-all' style='z-index:1002'>"+d.bNo+"</a>";s="<a href='javascript:void(0)' id='cNew' class='fm-button ui-state-default ui-corner-all' style='z-index:1002'>"+d.bExit+"</a>";var F,v=d.zIndex||999;v++;if(b.browser.msie&&b.browser.version==6){
                            F='<iframe style="display:block;position:absolute;z-index:-1;filter:Alpha(Opacity=\'0\');" src="javascript:false;"></iframe>'
                            }else{
                            F=""
                            }b("<div class='ui-widget-overlay jqgrid-overlay confirm' style='z-index:"+v+";display:none;'>&nbsp;"+F+"</div><div class='confirm ui-widget-content ui-jqconfirm' style='z-index:"+(v+1)+"'>"+d.saveData+"<br/><br/>"+g+l+s+"</div>").insertAfter("#"+x);b("#sNew","#"+h.themodal).click(function(){
                            i([true,"",""]);b("#"+x).data("disabled",false);b(".confirm","#"+h.themodal).hide();return false
                            });b("#nNew","#"+h.themodal).click(function(){
                            b(".confirm","#"+h.themodal).hide();b("#"+x).data("disabled",false);setTimeout(function(){
                                b(":input","#"+x)[0].focus()
                                },0);return false
                            });b("#cNew","#"+h.themodal).click(function(){
                            b(".confirm","#"+h.themodal).hide();b("#"+x).data("disabled",false);hideModal("#"+h.themodal,{
                                gb:"#gbox_"+B,
                                jqm:d.jqModal,
                                onClose:a.onClose
                                });return false
                            })
                        }if(n){
                        n(b("#"+x))
                        }if(c=="_empty"){
                        b("#pData,#nData","#"+t+"_2").hide()
                        }else{
                        b("#pData,#nData","#"+t+"_2").show()
                        }if(C){
                        C(b("#"+x))
                        }b("#"+h.themodal).data("onClose",a.onClose);viewModal("#"+h.themodal,{
                        gbox:"#gbox_"+B,
                        jqm:d.jqModal,
                        closeoverlay:j,
                        modal:d.modal
                        });if(!j){
                        b(".jqmOverlay").click(function(){
                            if(!f()){
                                return false
                                }hideModal("#"+h.themodal,{
                                gb:"#gbox_"+B,
                                jqm:d.jqModal,
                                onClose:a.onClose
                                });return false
                            })
                        }if(N){
                        N(b("#"+x))
                        }b(".fm-button","#"+h.themodal).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });b("#sData","#"+t+"_2").click(function(V){
                        D={};Q={};b("#FormError","#"+t).hide();T();if(D.id=="_empty"){
                            i()
                            }else{
                            if(d.checkOnSubmit===true){
                                G=b.extend({},D,Q);A=J(G,a._savedData);if(A){
                                    b("#"+x).data("disabled",true);b(".confirm","#"+h.themodal).show()
                                    }else{
                                    i()
                                    }
                                }else{
                                i()
                                }
                            }return false
                        });b("#cData","#"+t+"_2").click(function(V){
                        if(!f()){
                            return false
                            }hideModal("#"+h.themodal,{
                            gb:"#gbox_"+B,
                            jqm:d.jqModal,
                            onClose:a.onClose
                            });return false
                        });b("#nData","#"+t+"_2").click(function(V){
                        if(!f()){
                            return false
                            }b("#FormError","#"+t).hide();var W=U();W[0]=parseInt(W[0]);if(W[0]!=-1&&W[1][W[0]+1]){
                            if(b.isFunction(d.onclickPgButtons)){
                                d.onclickPgButtons("next",b("#"+x),W[1][W[0]])
                                }m(W[1][W[0]+1],e,x);b(e).setSelection(W[1][W[0]+1]);if(b.isFunction(d.afterclickPgButtons)){
                                d.afterclickPgButtons("next",b("#"+x),W[1][W[0]+1])
                                }K(W[0]+1,W[1].length-1)
                            }return false
                        });b("#pData","#"+t+"_2").click(function(W){
                        if(!f()){
                            return false
                            }b("#FormError","#"+t).hide();var V=U();if(V[0]!=-1&&V[1][V[0]-1]){
                            if(b.isFunction(d.onclickPgButtons)){
                                d.onclickPgButtons("prev",b("#"+x),V[1][V[0]])
                                }m(V[1][V[0]-1],e,x);b(e).setSelection(V[1][V[0]-1]);if(b.isFunction(d.afterclickPgButtons)){
                                d.afterclickPgButtons("prev",b("#"+x),V[1][V[0]-1])
                                }K(V[0]-1,V[1].length-1)
                            }return false
                        })
                    }var R=U();K(R[0],R[1].length-1);function K(W,X,V){
                    if(W==0){
                        b("#pData","#"+t+"_2").addClass("ui-state-disabled")
                        }else{
                        b("#pData","#"+t+"_2").removeClass("ui-state-disabled")
                        }if(W==X){
                        b("#nData","#"+t+"_2").addClass("ui-state-disabled")
                        }else{
                        b("#nData","#"+t+"_2").removeClass("ui-state-disabled")
                        }
                    }function U(){
                    var W=b(e).getDataIDs(),V=b("#id_g","#"+t).val(),X=b.inArray(V,W);return[X,W]
                    }function f(){
                    var V=true;b("#FormError","#"+t).hide();if(a.checkOnUpdate){
                        D={};Q={};T();G=b.extend({},D,Q);A=J(G,a._savedData);if(A){
                            b("#"+x).data("disabled",true);b(".confirm","#"+h.themodal).show();V=false
                            }
                        }return V
                    }function T(){
                    b(".FormElement","#"+t).each(function(W){
                        switch(b(this).get(0).type){
                            case"checkbox":if(b(this).attr("checked")){
                                D[this.name]=b(this).val()
                                }else{
                                var V=b(this).attr("offval");D[this.name]=V;Q[this.name]=V
                                }break;case"select-one":D[this.name]=b("option:selected",this).val();Q[this.name]=b("option:selected",this).text();break;case"select-multiple":D[this.name]=b(this).val();if(D[this.name]){
                                D[this.name]=D[this.name].join(",")
                                }else{
                                D[this.name]=""
                                }var X=[];b("option:selected",this).each(function(Y,Z){
                                X[Y]=b(Z).text()
                                });Q[this.name]=X.join(",");break;case"password":case"text":case"textarea":case"button":D[this.name]=b(this).val();D[this.name]=!e.p.autoencode?D[this.name]:b.jgrid.htmlEncode(D[this.name]);break
                                }
                        });return true
                    }function r(Y,ae,ab,aj){
                    var V,W,ag,ah=0,al,am,af,ak=[],ac=false,ad,X,Z="<td class='CaptionTD ui-widget-content'>&nbsp;</td><td class='DataTD ui-widget-content' style='white-space:pre'>&nbsp;</td>",aa="";for(var ai=1;ai<=aj;ai++){
                        aa+=Z
                        }if(Y!="_empty"){
                        ac=b(ae).getInd(Y)
                        }b(ae.p.colModel).each(function(aq){
                        V=this.name;if(this.editrules&&this.editrules.edithidden==true){
                            W=false
                            }else{
                            W=this.hidden===true?true:false
                            }am=W?"style='display:none'":"";if(V!=="cb"&&V!=="subgrid"&&this.editable===true&&V!=="rn"){
                            if(ac===false){
                                al=""
                                }else{
                                if(V==ae.p.ExpandColumn&&ae.p.treeGrid===true){
                                    al=b("td:eq("+aq+")",ae.rows[ac]).text()
                                    }else{
                                    try{
                                        al=b.unformat(b("td:eq("+aq+")",ae.rows[ac]),{
                                            colModel:this
                                        },aq)
                                        }catch(ao){
                                        al=b("td:eq("+aq+")",ae.rows[ac]).html()
                                        }
                                    }
                                }var ap=b.extend({},this.editoptions||{},{
                                id:V,
                                name:V
                            });frmopt=b.extend({},{
                                elmprefix:"",
                                elmsuffix:"",
                                rowabove:false,
                                rowcontent:""
                            },this.formoptions||{}),ad=parseInt(frmopt.rowpos)||ah+1,X=parseInt((parseInt(frmopt.colpos)||1)*2);if(Y=="_empty"&&ap.defaultValue){
                                al=b.isFunction(ap.defaultValue)?ap.defaultValue():ap.defaultValue
                                }if(!this.edittype){
                                this.edittype="text"
                                }af=createEl(this.edittype,ap,al);if(al==""&&this.edittype=="checkbox"){
                                al=b(af).attr("offval")
                                }if(a.checkOnSubmit||a.checkOnUpdate){
                                a._savedData[V]=al
                                }b(af).addClass("FormElement");ag=b(ab).find("tr[rowpos="+ad+"]");if(frmopt.rowabove){
                                var ar=b("<tr><td class='contentinfo' colspan='"+(aj*2)+"'>"+frmopt.rowcontent+"</td></tr>");b(ab).append(ar);ar[0].rp=ad
                                }if(ag.length==0){
                                ag=b("<tr "+am+" rowpos='"+ad+"'></tr>").addClass("FormData").attr("id","tr_"+V);b(ag).append(aa);b(ab).append(ag);ag[0].rp=ad
                                }b("td:eq("+(X-2)+")",ag[0]).html(typeof frmopt.label==="undefined"?ae.p.colNames[aq]:frmopt.label);b("td:eq("+(X-1)+")",ag[0]).append(frmopt.elmprefix).append(af).append(frmopt.elmsuffix);ak[ah]=aq;ah++
                        }
                        });if(ah>0){
                        var an=b("<tr class='FormData' style='display:none'><td class='CaptionTD'></td><td colspan='"+(aj*2-1)+"' class='DataTD'><input class='FormElement' id='id_g' type='text' name='id' value='"+Y+"'/></td></tr>");an[0].rp=ah+999;b(ab).append(an);if(a.checkOnSubmit||a.checkOnUpdate){
                            a._savedData.id=Y
                            }
                        }return ak
                    }function m(W,ac,Y){
                    var ah,ae,Z=0,ad,ab,V,aa,af;if(a.checkOnSubmit||a.checkOnUpdate){
                        a._savedData={};a._savedData.id=W
                        }var ag=ac.p.colModel;if(W=="_empty"){
                        b(ag).each(function(ai){
                            ah=this.name.replace(".","\\.");V=b.extend({},this.editoptions||{});ab=b("#"+ah,"#"+Y);if(ab[0]!=null){
                                aa="";if(V.defaultValue){
                                    aa=b.isFunction(V.defaultValue)?V.defaultValue():V.defaultValue;if(ab[0].type=="checkbox"){
                                        af=aa.toLowerCase();if(af.search(/(false|0|no|off|undefined)/i)<0&&af!==""){
                                            ab[0].checked=true;ab[0].defaultChecked=true;ab[0].value=aa
                                            }else{
                                            ab.attr({
                                                checked:"",
                                                defaultChecked:""
                                            })
                                            }
                                        }else{
                                        ab.val(aa)
                                        }
                                    }else{
                                    if(ab[0].type=="checkbox"){
                                        ab[0].checked=false;ab[0].defaultChecked=false;aa=b(ab).attr("offval")
                                        }else{
                                        if(ab[0].type.substr(0,6)=="select"){
                                            ab[0].selectedIndex=0
                                            }else{
                                            ab.val(aa)
                                            }
                                        }
                                    }if(a.checkOnSubmit===true||a.checkOnUpdate){
                                    a._savedData[ah]=aa
                                    }
                                }
                            });b("#id_g","#"+Y).val("_empty");return
                    }var X=b(ac).getInd(W,true);if(!X){
                        return
                    }b("td",X).each(function(aj){
                        ah=ag[aj].name.replace(".","\\.");if(ag[aj].editrules&&ag[aj].editrules.edithidden===true){
                            ae=false
                            }else{
                            ae=ag[aj].hidden===true?true:false
                            }if(ah!=="cb"&&ah!=="subgrid"&&ag[aj].editable===true){
                            if(ah==ac.p.ExpandColumn&&ac.p.treeGrid===true){
                                ad=b(this).text()
                                }else{
                                try{
                                    ad=b.unformat(this,{
                                        colModel:ag[aj]
                                        },aj)
                                    }catch(ai){
                                    ad=b(this).html()
                                    }
                                }if(a.checkOnSubmit===true||a.checkOnUpdate){
                                a._savedData[ah]=ad
                                }switch(ag[aj].edittype){
                                case"password":case"text":case"button":case"image":ad=b.jgrid.htmlDecode(ad);b("#"+ah,"#"+Y).val(ad);break;case"textarea":if(ad=="&nbsp;"||ad=="&#160;"||(ad.length==1&&ad.charCodeAt(0)==160)){
                                    ad=""
                                    }b("#"+ah,"#"+Y).val(ad);break;case"select":b("#"+ah+" option","#"+Y).each(function(ak){
                                    if(!ag[aj].editoptions.multiple&&(ad==b(this).text()||ad==b(this).val())){
                                        this.selected=true
                                        }else{
                                        if(ag[aj].editoptions.multiple){
                                            if(b.inArray(b(this).text(),ad.split(","))>-1||b.inArray(b(this).val(),ad.split(","))>-1){
                                                this.selected=true
                                                }else{
                                                this.selected=false
                                                }
                                            }else{
                                            this.selected=false
                                            }
                                        }
                                    });break;case"checkbox":ad=ad.toLowerCase();if(ad.search(/(false|0|no|off|undefined)/i)<0&&ad!==""){
                                    b("#"+ah,"#"+Y).attr("checked",true);b("#"+ah,"#"+Y).attr("defaultChecked",true)
                                    }else{
                                    b("#"+ah,"#"+Y).attr("checked",false);b("#"+ah,"#"+Y).attr("defaultChecked","")
                                    }break
                                }Z++
                        }
                        });if(Z>0){
                        b("#id_g","#"+t).val(W)
                        }
                    }function i(){
                    var Y,W=[true,"",""],V={};for(var X in D){
                        W=checkValues(D[X],X,e);if(W[0]==false){
                            break
                        }
                        }if(W[0]){
                        if(b.isFunction(a.onclickSubmit)){
                            V=a.onclickSubmit(a,D)||{}
                            }if(b.isFunction(a.beforeSubmit)){
                            W=a.beforeSubmit(D,b("#"+x))
                            }
                        }u=a.url?a.url:e.p.editurl;if(W[0]){
                        if(!u){
                            W[0]=false;W[1]+=" "+b.jgrid.errors.nourl
                            }
                        }if(W[0]===false){
                        b("#FormError>td","#"+t).html(W[1]);b("#FormError","#"+t).show();return
                    }if(!d.processing){
                        d.processing=true;b("#sData","#"+t+"_2").addClass("ui-state-active");D.oper=D.id=="_empty"?"add":"edit";D=b.extend(D,a.editData,V);b.ajax({
                            url:u,
                            type:a.mtype,
                            data:D,
                            complete:function(aa,Z){
                                if(Z!="success"){
                                    W[0]=false;if(b.isFunction(a.errorTextFormat)){
                                        W[1]=a.errorTextFormat(aa)
                                        }else{
                                        W[1]=Z+" Status: '"+aa.statusText+"'. Error code: "+aa.status
                                        }
                                    }else{
                                    if(b.isFunction(a.afterSubmit)){
                                        W=a.afterSubmit(aa,D)
                                        }
                                    }if(W[0]===false){
                                    b("#FormError>td","#"+t).html(W[1]);b("#FormError","#"+t).show()
                                    }else{
                                    D=b.extend(D,Q);if(D.id=="_empty"){
                                        if(!W[2]){
                                            W[2]=parseInt(e.p.records)+1
                                            }D.id=W[2];if(a.closeAfterAdd){
                                            if(a.reloadAfterSubmit){
                                                b(e).trigger("reloadGrid")
                                                }else{
                                                b(e).addRowData(W[2],D,d.addedrow);b(e).setSelection(W[2])
                                                }hideModal("#"+h.themodal,{
                                                gb:"#gbox_"+B,
                                                jqm:d.jqModal,
                                                onClose:a.onClose
                                                })
                                            }else{
                                            if(a.clearAfterAdd){
                                                if(a.reloadAfterSubmit){
                                                    b(e).trigger("reloadGrid")
                                                    }else{
                                                    b(e).addRowData(W[2],D,d.addedrow)
                                                    }m("_empty",e,x)
                                                }else{
                                                if(a.reloadAfterSubmit){
                                                    b(e).trigger("reloadGrid")
                                                    }else{
                                                    b(e).addRowData(W[2],D,d.addedrow)
                                                    }
                                                }
                                            }
                                        }else{
                                        if(a.reloadAfterSubmit){
                                            b(e).trigger("reloadGrid");if(!a.closeAfterEdit){
                                                setTimeout(function(){
                                                    b(e).setSelection(D.id)
                                                    },1000)
                                                }
                                            }else{
                                            if(e.p.treeGrid===true){
                                                b(e).setTreeRow(D.id,D)
                                                }else{
                                                b(e).setRowData(D.id,D)
                                                }
                                            }if(a.closeAfterEdit){
                                            hideModal("#"+h.themodal,{
                                                gb:"#gbox_"+B,
                                                jqm:d.jqModal,
                                                onClose:a.onClose
                                                })
                                            }
                                        }if(b.isFunction(a.afterComplete)){
                                        Y=aa;setTimeout(function(){
                                            a.afterComplete(Y,D,b("#"+x));Y=null
                                            },500)
                                        }
                                    }d.processing=false;if(a.checkOnSubmit||a.checkOnUpdate){
                                    b("#"+x).data("disabled",false);if(a._savedData.id!="_empty"){
                                        a._savedData=D
                                        }
                                    }b("#sData","#"+t+"_2").removeClass("ui-state-active");try{
                                    b(":input:visible","#"+x)[0].focus()
                                    }catch(ab){}
                                },
                            error:function(ab,Z,aa){
                                b("#FormError>td","#"+t).html(Z+" : "+aa);b("#FormError","#"+t).show();d.processing=false;b("#"+x).data("disabled",false);b("#sData","#"+t+"_2").removeClass("ui-state-active")
                                }
                            })
                        }
                    }function J(Y,V){
                    var W=false,X;for(X in Y){
                        if(Y[X]!=V[X]){
                            W=true;break
                        }
                        }return W
                    }
                })
            },
        viewGridRow:function(c,d){
            d=b.extend({
                top:0,
                left:0,
                width:0,
                height:"auto",
                dataheight:"auto",
                modal:false,
                drag:true,
                resize:true,
                jqModal:true,
                closeOnEscape:false,
                labelswidth:"30%",
                closeicon:[],
                navkeys:[false,38,40],
                onClose:null
            },b.jgrid.view,d||{});return this.each(function(){
                var w=this;if(!w.grid||!c){
                    return
                }if(!d.imgpath){
                    d.imgpath=w.p.imgpath
                    }var q=w.p.id,y="ViewGrid_"+q,r="ViewTbl_"+q,i={
                    themodal:"viewmod"+q,
                    modalhead:"viewhd"+q,
                    modalcontent:"viewcnt"+q,
                    scrollelm:y
                },g=1,e=0;if(b("#"+i.themodal).html()!=null){
                    b(".ui-jqdialog-title","#"+i.modalhead).html(d.caption);b("#FormError","#"+r).hide();l(c,w);viewModal("#"+i.themodal,{
                        gbox:"#gbox_"+q,
                        jqm:d.jqModal,
                        jqM:false,
                        modal:d.modal
                        });j()
                    }else{
                    b(w.p.colModel).each(function(C){
                        var D=this.formoptions;g=Math.max(g,D?D.colpos||0:0);e=Math.max(e,D?D.rowpos||0:0)
                        });var x=isNaN(d.dataheight)?d.dataheight:d.dataheight+"px";var v,A=b("<form name='FormPost' id='"+y+"' class='FormGrid' style='width:100%;overflow:auto;position:relative;height:"+x+";'></form>"),k=b("<table id='"+r+"' class='EditTable' cellspacing='1' cellpading='2' border='0' style='table-layout:fixed'><tbody></tbody></table>");b(A).append(k);var u=m(c,w,k,g),s="<a href='javascript:void(0)' id='pData' class='fm-button ui-state-default ui-corner-left'><span class='ui-icon ui-icon-triangle-1-w'></span></div>",t="<a href='javascript:void(0)' id='nData' class='fm-button ui-state-default ui-corner-right'><span class='ui-icon ui-icon-triangle-1-e'></span></div>",B="<a href='javascript:void(0)' id='cData' class='fm-button ui-state-default ui-corner-all'>"+d.bClose+"</a>";if(e>0){
                        var f=[];b.each(b(k)[0].rows,function(C,D){
                            f[C]=D
                            });f.sort(function(D,C){
                            if(D.rp>C.rp){
                                return 1
                                }if(D.rp<C.rp){
                                return -1
                                }return 0
                            });b.each(f,function(C,D){
                            b("tbody",k).append(D)
                            })
                        }d.gbox="#gbox_"+q;var p=false;if(d.closeOnEscape===true){
                        d.closeOnEscape=false;p=true
                        }var z=b("<span></span>").append(A).append("<table border='0' class='EditTable' id='"+r+"_2'><tbody><tr id='Act_Buttons'><td class='navButton ui-widget-content' width='"+d.labelswidth+"'>"+s+t+"</td><td class='EditButton ui-widget-content'>"+B+"</td></tr></tbody></table>");createModal(i,z,d,"#gview_"+w.p.id,b("#gview_"+w.p.id)[0]);z=null;jQuery("#"+i.themodal).keydown(function(C){
                        if(C.which===27){
                            if(p){
                                hideModal(this,{
                                    gb:d.gbox,
                                    jqm:d.jqModal,
                                    onClose:d.onClose
                                    })
                                }return false
                            }if(d.navkeys[0]===true){
                            if(C.which===d.navkeys[1]){
                                b("#pData","#"+r+"_2").trigger("click");return false
                                }if(C.which===d.navkeys[2]){
                                b("#nData","#"+r+"_2").trigger("click");return false
                                }
                            }
                        });d.closeicon=b.extend([true,"left","ui-icon-close"],d.closeicon);if(d.closeicon[0]==true){
                        b("#cData","#"+r+"_2").addClass(d.closeicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+d.closeicon[2]+"'></span>")
                        }viewModal("#"+i.themodal,{
                        gbox:"#gbox_"+q,
                        jqm:d.jqModal,
                        modal:d.modal
                        });b(".fm-button:not(.ui-state-disabled)","#"+r+"_2").hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });j();b("#cData","#"+r+"_2").click(function(C){
                        hideModal("#"+i.themodal,{
                            gb:"#gbox_"+q,
                            jqm:d.jqModal,
                            onClose:d.onClose
                            });return false
                        });b("#nData","#"+r+"_2").click(function(C){
                        b("#FormError","#"+r).hide();var D=h();D[0]=parseInt(D[0]);if(D[0]!=-1&&D[1][D[0]+1]){
                            if(b.isFunction(d.onclickPgButtons)){
                                d.onclickPgButtons("next",b("#"+y),D[1][D[0]])
                                }l(D[1][D[0]+1],w);b(w).setSelection(D[1][D[0]+1]);if(b.isFunction(d.afterclickPgButtons)){
                                d.afterclickPgButtons("next",b("#"+y),D[1][D[0]+1])
                                }n(D[0]+1,D[1].length-1)
                            }j();return false
                        });b("#pData","#"+r+"_2").click(function(D){
                        b("#FormError","#"+r).hide();var C=h();if(C[0]!=-1&&C[1][C[0]-1]){
                            if(b.isFunction(d.onclickPgButtons)){
                                d.onclickPgButtons("prev",b("#"+y),C[1][C[0]])
                                }l(C[1][C[0]-1],w);b(w).setSelection(C[1][C[0]-1]);if(b.isFunction(d.afterclickPgButtons)){
                                d.afterclickPgButtons("prev",b("#"+y),C[1][C[0]-1])
                                }n(C[0]-1,C[1].length-1)
                            }j();return false
                        })
                    }function j(){
                    if(d.closeOnEscape===true||d.navkeys[0]===true){
                        setTimeout(function(){
                            b(".ui-jqdialog-titlebar-close","#"+i.modalhead).focus()
                            },0)
                        }
                    }var o=h();n(o[0],o[1].length-1);function n(D,E,C){
                    if(D==0){
                        b("#pData","#"+r+"_2").addClass("ui-state-disabled")
                        }else{
                        b("#pData","#"+r+"_2").removeClass("ui-state-disabled")
                        }if(D==E){
                        b("#nData","#"+r+"_2").addClass("ui-state-disabled")
                        }else{
                        b("#nData","#"+r+"_2").removeClass("ui-state-disabled")
                        }
                    }function h(){
                    var D=b(w).getDataIDs(),C=b("#id_g","#"+r).val(),E=b.inArray(C,D);return[E,D]
                    }function m(I,O,M,U){
                    var E,H,P,X,C,S=0,W,Y,V=[],N=false,K="<td class='CaptionTD ui-widget-content' width='"+d.labelswidth+"'>&nbsp;</td><td class='DataTD ui-helper-reset ui-widget-content' style='white-space:pre;'>&nbsp;</td>",L="",F="<td class='CaptionTD ui-widget-content'>&nbsp;</td><td class='DataTD ui-widget-content' style='white-space:pre;'>&nbsp;</td>",J=["integer","number","currency"],R=0,Q=0,G,D;for(var T=1;T<=U;T++){
                        L+=T==1?K:F
                        }b(O.p.colModel).each(function(aa){
                        if(this.editrules&&this.editrules.edithidden===true){
                            H=false
                            }else{
                            H=this.hidden===true?true:false
                            }if(!H&&this.align==="right"){
                            if(this.formatter&&b.inArray(this.formatter,J)!==-1){
                                R=Math.max(R,parseInt(this.width,10))
                                }else{
                                Q=Math.max(Q,parseInt(this.width,10))
                                }
                            }
                        });G=R!==0?R:Q!==0?Q:0;N=b(O).getInd(I);b(O.p.colModel).each(function(ab){
                        E=this.name;D=false;if(this.editrules&&this.editrules.edithidden===true){
                            H=false
                            }else{
                            H=this.hidden===true?true:false
                            }Y=H?"style='display:none'":"";if(E!=="cb"&&E!=="subgrid"&&this.editable===true){
                            if(N===false){
                                W=""
                                }else{
                                if(E==O.p.ExpandColumn&&O.p.treeGrid===true){
                                    W=b("td:eq("+ab+")",O.rows[N]).text()
                                    }else{
                                    W=b("td:eq("+ab+")",O.rows[N]).html()
                                    }
                                }D=this.align==="right"&&G!==0?true:false;var aa=b.extend({},this.editoptions||{},{
                                id:E,
                                name:E
                            }),af=b.extend({},{
                                rowabove:false,
                                rowcontent:""
                            },this.formoptions||{}),ac=parseInt(af.rowpos)||S+1,ae=parseInt((parseInt(af.colpos)||1)*2);if(af.rowabove){
                                var ad=b("<tr><td class='contentinfo' colspan='"+(U*2)+"'>"+af.rowcontent+"</td></tr>");b(M).append(ad);ad[0].rp=ac
                                }P=b(M).find("tr[rowpos="+ac+"]");if(P.length==0){
                                P=b("<tr "+Y+" rowpos='"+ac+"'></tr>").addClass("FormData").attr("id","trv_"+E);b(P).append(L);b(M).append(P);P[0].rp=ac
                                }b("td:eq("+(ae-2)+")",P[0]).html("<b>"+(typeof af.label==="undefined"?O.p.colNames[ab]:af.label)+"</b>");b("td:eq("+(ae-1)+")",P[0]).append("<span>"+W+"</span>").attr("id","v_"+E);if(D){
                                b("td:eq("+(ae-1)+") span",P[0]).css({
                                    "text-align":"right",
                                    width:G+"px"
                                    })
                                }V[S]=ab;S++
                        }
                        });if(S>0){
                        var Z=b("<tr class='FormData' style='display:none'><td class='CaptionTD'></td><td colspan='"+(U*2-1)+"' class='DataTD'><input class='FormElement' id='id_g' type='text' name='id' value='"+I+"'/></td></tr>");Z[0].rp=S+99;b(M).append(Z)
                        }return V
                    }function l(G,I){
                    var C,J,F=0,E,D,H;H=b(I).getInd(G,true);if(!H){
                        return
                    }b("td",H).each(function(K){
                        C=I.p.colModel[K].name.replace(".","\\.");if(I.p.colModel[K].editrules&&I.p.colModel[K].editrules.edithidden===true){
                            J=false
                            }else{
                            J=I.p.colModel[K].hidden===true?true:false
                            }if(C!=="cb"&&C!=="subgrid"&&I.p.colModel[K].editable===true){
                            if(C==I.p.ExpandColumn&&I.p.treeGrid===true){
                                E=b(this).text()
                                }else{
                                E=b(this).html()
                                }D=b.extend({},I.p.colModel[K].editoptions||{});C="v_"+C;b("#"+C+" span","#"+r).html(E);if(J){
                                b("#"+C,"#"+r).parents("tr:first").hide()
                                }F++
                        }
                        });if(F>0){
                        b("#id_g","#"+r).val(G)
                        }
                    }
                })
            },
        delGridRow:function(c,d){
            d=b.extend({
                top:0,
                left:0,
                width:240,
                height:"auto",
                dataheight:"auto",
                modal:false,
                drag:true,
                resize:true,
                url:"",
                mtype:"POST",
                reloadAfterSubmit:true,
                beforeShowForm:null,
                afterShowForm:null,
                beforeSubmit:null,
                onclickSubmit:null,
                afterSubmit:null,
                jqModal:true,
                closeOnEscape:false,
                delData:{},
                delicon:[],
                cancelicon:[],
                onClose:null
            },b.jgrid.del,d||{});a=d;return this.each(function(){
                var l=this;if(!l.grid){
                    return
                }if(!c){
                    return
                }var m=typeof d.beforeShowForm==="function"?true:false,g=typeof d.afterShowForm==="function"?true:false,e=l.p.id,f={},j="DelTbl_"+e,h={
                    themodal:"delmod"+e,
                    modalhead:"delhd"+e,
                    modalcontent:"delcnt"+e,
                    scrollelm:j
                };if(isArray(c)){
                    c=c.join()
                    }if(b("#"+h.themodal).html()!=null){
                    b("#DelData>td","#"+j).text(c);b("#DelError","#"+j).hide();if(d.processing===true){
                        d.processing=false;b("#dData","#"+j).removeClass("ui-state-active")
                        }if(m){
                        d.beforeShowForm(b("#"+j))
                        }viewModal("#"+h.themodal,{
                        gbox:"#gbox_"+e,
                        jqm:d.jqModal,
                        jqM:false,
                        modal:d.modal
                        });if(g){
                        d.afterShowForm(b("#"+j))
                        }
                    }else{
                    var n=isNaN(d.dataheight)?d.dataheight:d.dataheight+"px";var k="<div id='"+j+"' class='formdata' style='width:100%;overflow:auto;position:relative;height:"+n+";'>";k+="<table class='DelTable'><tbody>";k+="<tr id='DelError' style='display:none'><td class='ui-state-error'></td></tr>";k+="<tr id='DelData' style='display:none'><td >"+c+"</td></tr>";k+='<tr><td class="delmsg" style="white-space:pre;">'+d.msg+"</td></tr><tr><td >&nbsp;</td></tr>";k+="</tbody></table></div>";var i="<a href='javascript:void(0)' id='dData' class='fm-button ui-state-default ui-corner-all'>"+d.bSubmit+"</a>",o="<a href='javascript:void(0)' id='eData' class='fm-button ui-state-default ui-corner-all'>"+d.bCancel+"</a>";k+="<table cellspacing='0' cellpadding='0' border='0' class='EditTable' id='"+j+"_2'><tbody><tr><td class='DataTD ui-widget-content'></td></tr><tr style='display:block;height:3px;'><td></td></tr><tr><td class='DelButton EditButton'>"+i+"&nbsp;"+o+"</td></tr></tbody></table>";d.gbox="#gbox_"+e;createModal(h,k,d,"#gview_"+l.p.id,b("#gview_"+l.p.id)[0]);b(".fm-button","#"+j+"_2").hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });d.delicon=b.extend([true,"left","ui-icon-scissors"],d.delicon);d.cancelicon=b.extend([true,"left","ui-icon-cancel"],d.cancelicon);if(d.delicon[0]==true){
                        b("#dData","#"+j+"_2").addClass(d.delicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+d.delicon[2]+"'></span>")
                        }if(d.cancelicon[0]==true){
                        b("#eData","#"+j+"_2").addClass(d.cancelicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+d.cancelicon[2]+"'></span>")
                        }b("#dData","#"+j+"_2").click(function(s){
                        var q=[true,""];f={};var r=b("#DelData>td","#"+j).text();if(typeof d.onclickSubmit==="function"){
                            f=d.onclickSubmit(a)||{}
                            }if(typeof d.beforeSubmit==="function"){
                            q=d.beforeSubmit(r)
                            }var p=a.url?a.url:l.p.editurl;if(!p){
                            q[0]=false;q[1]+=" "+b.jgrid.errors.nourl
                            }if(q[0]===false){
                            b("#DelError>td","#"+j).html(q[1]);b("#DelError","#"+j).show()
                            }else{
                            if(!d.processing){
                                d.processing=true;b(this).addClass("ui-state-active");var t=b.extend({
                                    oper:"del",
                                    id:r
                                },d.delData,f);b.ajax({
                                    url:p,
                                    type:d.mtype,
                                    data:t,
                                    complete:function(x,v){
                                        if(v!="success"){
                                            q[0]=false;if(b.isFunction(a.errorTextFormat)){
                                                q[1]=a.errorTextFormat(x)
                                                }else{
                                                q[1]=v+" Status: '"+x.statusText+"'. Error code: "+x.status
                                                }
                                            }else{
                                            if(typeof a.afterSubmit==="function"){
                                                q=a.afterSubmit(x,r)
                                                }
                                            }if(q[0]===false){
                                            b("#DelError>td","#"+j).html(q[1]);b("#DelError","#"+j).show()
                                            }else{
                                            if(a.reloadAfterSubmit){
                                                if(l.p.treeGrid){
                                                    b(l).setGridParam({
                                                        treeANode:0,
                                                        datatype:l.p.treedatatype
                                                        })
                                                    }b(l).trigger("reloadGrid")
                                                }else{
                                                var u=[];u=r.split(",");if(l.p.treeGrid===true){
                                                    try{
                                                        b(l).delTreeNode(u[0])
                                                        }catch(y){}
                                                    }else{
                                                    for(var w=0;w<u.length;w++){
                                                        b(l).delRowData(u[w])
                                                        }
                                                    }l.p.selrow=null;l.p.selarrrow=[]
                                                }if(b.isFunction(a.afterComplete)){
                                                setTimeout(function(){
                                                    a.afterComplete(x,r)
                                                    },500)
                                                }
                                            }d.processing=false;b("#dData","#"+j+"_2").removeClass("ui-state-active");if(q[0]){
                                            hideModal("#"+h.themodal,{
                                                gb:"#gbox_"+e,
                                                jqm:d.jqModal,
                                                onClose:a.onClose
                                                })
                                            }
                                        },
                                    error:function(w,u,v){
                                        b("#DelError>td","#"+j).html(u+" : "+v);b("#DelError","#"+j).show();d.processing=false;b("#dData","#"+j+"_2").removeClass("ui-state-active")
                                        }
                                    })
                                }
                            }return false
                        });b("#eData","#"+j+"_2").click(function(p){
                        hideModal("#"+h.themodal,{
                            gb:"#gbox_"+e,
                            jqm:d.jqModal,
                            onClose:a.onClose
                            });return false
                        });if(m){
                        d.beforeShowForm(b("#"+j))
                        }viewModal("#"+h.themodal,{
                        gbox:"#gbox_"+e,
                        jqm:d.jqModal,
                        modal:d.modal
                        });if(g){
                        d.afterShowForm(b("#"+j))
                        }
                    }if(d.closeOnEscape===true){
                    setTimeout(function(){
                        b(".ui-jqdialog-titlebar-close","#"+h.modalhead).focus()
                        },0)
                    }
                })
            },
        navGrid:function(f,h,e,g,d,c,i){
            h=b.extend({
                edit:true,
                editicon:"ui-icon-pencil",
                add:true,
                addicon:"ui-icon-plus",
                del:true,
                delicon:"ui-icon-trash",
                search:true,
                searchicon:"ui-icon-search",
                refresh:true,
                refreshicon:"ui-icon-refresh",
                refreshstate:"firstpage",
                view:false,
                viewicon:"ui-icon-document",
                position:"left",
                closeOnEscape:true,
                afterRefresh:null
            },b.jgrid.nav,h||{});return this.each(function(){
                var j={
                    themodal:"alertmod",
                    modalhead:"alerthd",
                    modalcontent:"alertcnt"
                },n=this,m,s,o,k;if(!n.grid){
                    return
                }if(b("#"+j.themodal).html()==null){
                    if(typeof window.innerWidth!="undefined"){
                        m=window.innerWidth,s=window.innerHeight
                        }else{
                        if(typeof document.documentElement!="undefined"&&typeof document.documentElement.clientWidth!="undefined"&&document.documentElement.clientWidth!=0){
                            m=document.documentElement.clientWidth,s=document.documentElement.clientHeight
                            }else{
                            m=1024;s=768
                            }
                        }createModal(j,"<div>"+h.alerttext+"</div><span tabindex='0'><span tabindex='-1' id='jqg_alrt'></span></span>",{
                        gbox:"#gbox_"+n.p.id,
                        jqModal:true,
                        drag:true,
                        resize:true,
                        caption:h.alertcap,
                        top:s/2-25,
                        left:m/2-100,
                        width:200,
                        height:"auto",
                        closeOnEscape:h.closeOnEscape
                        },"","",true)
                    }var p,q=b("<table cellspacing='0' cellpadding='0' border='0' class='ui-pg-table navtable' style='float:left;table-layout:auto;'><tbody><tr></tr></tbody></table>"),r="<td class='ui-pg-button ui-state-disabled' style='width:4px;'><span class='ui-separator'></span></td>",l=b(n.p.pager).attr("id")||"pager";if(h.add){
                    g=g||{};p=b("<td class='ui-pg-button ui-corner-all'></td>");b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.addicon+"'></span>"+h.addtext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.addtitle||"",
                        id:g.id||"add_"+n.p.id
                        }).click(function(){
                        if(typeof h.addfunc=="function"){
                            h.addfunc()
                            }else{
                            b(n).editGridRow("new",g)
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }if(h.edit){
                    p=b("<td class='ui-pg-button ui-corner-all'></td>");e=e||{};b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.editicon+"'></span>"+h.edittext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.edittitle||"",
                        id:e.id||"edit_"+n.p.id
                        }).click(function(){
                        var t=n.p.selrow;if(t){
                            if(typeof h.editfunc=="function"){
                                h.editfunc(t)
                                }else{
                                b(n).editGridRow(t,e)
                                }
                            }else{
                            viewModal("#"+j.themodal,{
                                gbox:"#gbox_"+n.p.id,
                                jqm:true
                            });b("#jqg_alrt").focus()
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }if(h.view){
                    p=b("<td class='ui-pg-button ui-corner-all'></td>");i=i||{};b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.viewicon+"'></span>"+h.viewtext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.viewtitle||"",
                        id:i.id||"view_"+n.p.id
                        }).click(function(){
                        var t=n.p.selrow;if(t){
                            b(n).viewGridRow(t,i)
                            }else{
                            viewModal("#"+j.themodal,{
                                gbox:"#gbox_"+n.p.id,
                                jqm:true
                            });b("#jqg_alrt").focus()
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }if(h.del){
                    p=b("<td class='ui-pg-button ui-corner-all'></td>");d=d||{};b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.delicon+"'></span>"+h.deltext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.deltitle||"",
                        id:d.id||"del_"+n.p.id
                        }).click(function(){
                        var t;if(n.p.multiselect){
                            t=n.p.selarrrow;if(t.length==0){
                                t=null
                                }
                            }else{
                            t=n.p.selrow
                            }if(t){
                            b(n).delGridRow(t,d)
                            }else{
                            viewModal("#"+j.themodal,{
                                gbox:"#gbox_"+n.p.id,
                                jqm:true
                            });b("#jqg_alrt").focus()
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }if(h.add||h.edit||h.del||h.view){
                    b("tr",q).append(r)
                    }if(h.search){
                    p=b("<td class='ui-pg-button ui-corner-all'></td>");c=c||{};b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.searchicon+"'></span>"+h.searchtext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.searchtitle||"",
                        id:c.id||"search_"+n.p.id
                        }).click(function(){
                        b(n).searchGrid(c);return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }if(h.refresh){
                    p=b("<td class='ui-pg-button ui-corner-all'></td>");b(p).append("<div class='ui-pg-div'><span class='ui-icon "+h.refreshicon+"'></span>"+h.refreshtext+"</div>");b("tr",q).append(p);b(p,q).attr({
                        title:h.refreshtitle||"",
                        id:"refresh_"+n.p.id
                        }).click(function(){
                        n.p.search=false;try{
                            var u=n.p.id;b("#fbox_"+u).searchFilter().reset()
                            }catch(v){}switch(h.refreshstate){
                            case"firstpage":n.p.page=1;b(n).trigger("reloadGrid");break;case"current":var t=n.p.multiselect===true?n.p.selarrrow:n.p.selrow;b(n).trigger("reloadGrid");setTimeout(function(){
                                if(n.p.multiselect===true){
                                    if(t.length>0){
                                        for(var w=0;w<t.length;w++){
                                            b(n).setSelection(t[w],false)
                                            }
                                        }
                                    }else{
                                    if(t){
                                        b(n).setSelection(t,false)
                                        }
                                    }
                                },1000);break
                            }if(b.isFunction(h.afterRefresh)){
                            h.afterRefresh()
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        });p=null
                    }k=b(".ui-jqgrid").css("font-size")||"11px";b("body").append("<div id='testpg2' class='ui-jqgrid ui-widget ui-widget-content' style='font-size:"+k+";visibility:hidden;' ></div>");o=b(q).clone().appendTo("#testpg2").width();b("#testpg2").remove();b("#"+l+"_"+h.position,"#"+l).append(q);if(n.p._nvtd){
                    if(o>n.p._nvtd[0]){
                        b("#"+l+"_"+h.position,"#"+l).width(o);n.p._nvtd[0]=o
                        }n.p._nvtd[1]=o
                    }
                })
            },
        navButtonAdd:function(c,d){
            d=b.extend({
                caption:"newButton",
                title:"",
                buttonicon:"ui-icon-newwin",
                onClickButton:null,
                position:"last",
                cursor:"pointer"
            },d||{});return this.each(function(){
                if(!this.grid){
                    return
                }if(c.indexOf("#")!=0){
                    c="#"+c
                    }var e=b(".navtable",c)[0];if(e){
                    var f=b("<td></td>");b(f).addClass("ui-pg-button ui-corner-all").append("<div class='ui-pg-div'><span class='ui-icon "+d.buttonicon+"'></span>"+d.caption+"</div>");if(d.id){
                        b(f).attr("id",d.id)
                        }if(d.position=="first"){
                        if(e.rows[0].cells.length===0){
                            b("tr",e).append(f)
                            }else{
                            b("tr td:eq(0)",e).before(f)
                            }
                        }else{
                        b("tr",e).append(f)
                        }b(f,e).attr("title",d.title||"").click(function(g){
                        if(b.isFunction(d.onClickButton)){
                            d.onClickButton()
                            }return false
                        }).hover(function(){
                        b(this).addClass("ui-state-hover")
                        },function(){
                        b(this).removeClass("ui-state-hover")
                        }).css("cursor",d.cursor?d.cursor:"normal")
                    }
                })
            },
        navSeparatorAdd:function(c,d){
            d=b.extend({
                sepclass:"ui-separator",
                sepcontent:""
            },d||{});return this.each(function(){
                if(!this.grid){
                    return
                }if(c.indexOf("#")!=0){
                    c="#"+c
                    }var f=b(".navtable",c)[0];if(f){
                    var e="<td class='ui-pg-button ui-state-disabled' style='width:4px;'><span class='"+d.sepclass+"'></span>"+d.sepcontent+"</td>";b("tr",f).append(e)
                    }
                })
            },
        GridToForm:function(c,d){
            return this.each(function(){
                var g=this;if(!g.grid){
                    return
                }var f=b(g).getRowData(c);if(f){
                    for(var e in f){
                        if(b("[name="+e+"]",d).is("input:radio")||b("[name="+e+"]",d).is("input:checkbox")){
                            b("[name="+e+"]",d).each(function(){
                                if(b(this).val()==f[e]){
                                    b(this).attr("checked","checked")
                                    }else{
                                    b(this).attr("checked","")
                                    }
                                })
                            }else{
                            b("[name="+e+"]",d).val(f[e])
                            }
                        }
                    }
                })
            },
        FormToGrid:function(d,e,f,c){
            return this.each(function(){
                var i=this;if(!i.grid){
                    return
                }if(!f){
                    f="set"
                    }if(!c){
                    c="first"
                    }var g=b(e).serializeArray();var h={};b.each(g,function(j,k){
                    h[k.name]=k.value
                    });if(f=="add"){
                    b(i).addRowData(d,h,c)
                    }else{
                    if(f=="set"){
                        b(i).setRowData(d,h)
                        }
                    }
                })
            }
        })
    })(jQuery);jQuery.fn.searchFilter=function(a,c){
    function b(j,p,g){
        this.$=j;this.add=function(z){
            if(z==null){
                j.find(".ui-add-last").click()
                }else{
                j.find(".sf:eq("+z+") .ui-add").click()
                }return this
            };this.del=function(z){
            if(z==null){
                j.find(".sf:last .ui-del").click()
                }else{
                j.find(".sf:eq("+z+") .ui-del").click()
                }return this
            };this.search=function(z){
            j.find(".ui-search").click();return this
            };this.reset=function(z){
            j.find(".ui-reset").click();return this
            };this.close=function(){
            j.find(".ui-closer").click();return this
            };if(p!=null){
            function v(){
                jQuery(this).toggleClass("ui-state-hover");return false
                }function i(z){
                jQuery(this).toggleClass("ui-state-active",(z.type=="mousedown"));return false
                }function e(z,A){
                return"<option value='"+z+"'>"+A+"</option>"
                }function s(B,z,A){
                return"<select class='"+B+"'"+(A?" style='display:none;'":"")+">"+z+"</select>"
                }function w(z,B){
                var A=j.find("tr.sf td.data "+z);if(A[0]!=null){
                    B(A)
                    }
                }function q(z,B){
                var A=j.find("tr.sf td.data "+z);if(A[0]!=null){
                    jQuery.each(B,function(){
                        if(this.data!=null){
                            A.bind(this.type,this.data,this.fn)
                            }else{
                            A.bind(this.type,this.fn)
                            }
                        })
                    }
                }var n=jQuery.extend({},jQuery.fn.searchFilter.defaults,g);var y=-1;var x="";jQuery.each(n.groupOps,function(){
                x+=e(this.op,this.text)
                });x="<select name='groupOp'>"+x+"</select>";j.html("").addClass("ui-searchFilter").append("<div class='ui-widget-overlay' style='z-index: -1'>&nbsp;</div><table class='ui-widget-content ui-corner-all'><thead><tr><td colspan='5' class='ui-widget-header ui-corner-all' style='line-height: 18px;'><div class='ui-closer ui-state-default ui-corner-all ui-helper-clearfix' style='float: right;'><span class='ui-icon ui-icon-close'></span></div>"+n.windowTitle+"</td></tr></thead><tbody><tr class='sf'><td class='fields'></td><td class='ops'></td><td class='data'></td><td><div class='ui-del ui-state-default ui-corner-all'><span class='ui-icon ui-icon-minus'></span></div></td><td><div class='ui-add ui-state-default ui-corner-all'><span class='ui-icon ui-icon-plus'></span></div></td></tr><tr><td colspan='5' class='divider'><div>&nbsp;</div></td></tr></tbody><tfoot><tr><td colspan='3'><span class='ui-reset ui-state-default ui-corner-all' style='display: inline-block; float: left;'><span class='ui-icon ui-icon-arrowreturnthick-1-w' style='float: left;'></span><span style='line-height: 18px; padding: 0 7px 0 3px;'>"+n.resetText+"</span></span><span class='ui-search ui-state-default ui-corner-all' style='display: inline-block; float: right;'><span class='ui-icon ui-icon-search' style='float: left;'></span><span style='line-height: 18px; padding: 0 7px 0 3px;'>"+n.searchText+"</span></span><span class='matchText'>"+n.matchText+"</span> "+x+" <span class='rulesText'>"+n.rulesText+"</span></td><td>&nbsp;</td><td><div class='ui-add-last ui-state-default ui-corner-all'><span class='ui-icon ui-icon-plusthick'></span></div></td></tr></tfoot></table>");var k=j.find("tr.sf");var h=k.find("td.fields");var f=k.find("td.ops");var o=k.find("td.data");var r="";jQuery.each(n.operators,function(){
                r+=e(this.op,this.text)
                });r=s("default",r,true);f.append(r);var l="<input type='text' class='default' style='display:none;' />";o.append(l);var u="";var t=false;var d=false;jQuery.each(p,function(B){
                var A=B;u+=e(this.value,this.text);if(this.ops!=null){
                    t=true;var z="";jQuery.each(this.ops,function(){
                        z+=e(this.op,this.text)
                        });z=s("field"+A,z,true);f.append(z)
                    }if(this.dataUrl!=null){
                    if(B>y){
                        y=B
                        }d=true;var E=this.dataEvents;var C=this.dataInit;jQuery.get(this.dataUrl,function(G){
                        var F=jQuery("<div />").append(G);F.find("select").addClass("field"+A).hide();o.append(F.html());if(C){
                            w(".field"+B,C)
                            }if(E){
                            q(".field"+B,E)
                            }if(B==y){
                            j.find("tr.sf td.fields select[name='field']").change()
                            }
                        })
                    }else{
                    if(this.dataValues!=null){
                        d=true;var D="";jQuery.each(this.dataValues,function(){
                            D+=e(this.value,this.text)
                            });D=s("field"+A,D,true);o.append(D)
                        }else{
                        if(this.dataEvents!=null||this.dataInit!=null){
                            d=true;var D="<input type='text' class='field"+A+"' />";o.append(D)
                            }
                        }
                    }if(this.dataInit!=null&&B!=y){
                    w(".field"+B,this.dataInit)
                    }if(this.dataEvents!=null&&B!=y){
                    q(".field"+B,this.dataEvents)
                    }
                });u="<select name='field'>"+u+"</select>";h.append(u);var m=h.find("select[name='field']");if(t){
                m.change(function(B){
                    var A=B.target.selectedIndex;var C=jQuery(B.target).parents("tr.sf").find("td.ops");C.find("select").removeAttr("name").hide();var z=C.find(".field"+A);if(z[0]==null){
                        z=C.find(".default")
                        }z.attr("name","op").show()
                    })
                }else{
                f.find(".default").attr("name","op").show()
                }if(d){
                m.change(function(B){
                    var A=B.target.selectedIndex;var C=jQuery(B.target).parents("tr.sf").find("td.data");C.find("select,input").removeAttr("name").hide();var z=C.find(".field"+A);if(z[0]==null){
                        z=C.find(".default")
                        }z.attr("name","data").show()
                    })
                }else{
                o.find(".default").attr("name","data").show()
                }if(t||d){
                m.change()
                }j.find(".ui-state-default").hover(v,v).mousedown(i).mouseup(i);j.find(".ui-closer").click(function(z){
                n.onClose(jQuery(j.selector));return false
                });j.find(".ui-del").click(function(z){
                var A=jQuery(z.target).parents(".sf");if(A.siblings(".sf").length>0){
                    if(n.datepickerFix===true&&jQuery.fn.datepicker!==undefined){
                        A.find(".hasDatepicker").datepicker("destroy")
                        }A.remove()
                    }else{
                    A.find("select[name='field']")[0].selectedIndex=0;A.find("select[name='op']")[0].selectedIndex=0;A.find(".data input").val("");A.find(".data select").each(function(){
                        this.selectedIndex=0
                        });A.find("select[name='field']").change()
                    }return false
                });j.find(".ui-add").click(function(C){
                var D=jQuery(C.target).parents(".sf");var B=D.clone(true).insertAfter(D);B.find(".ui-state-default").removeClass("ui-state-hover ui-state-active");if(n.clone){
                    B.find("select[name='field']")[0].selectedIndex=D.find("select[name='field']")[0].selectedIndex;var A=(B.find("select[name='op']")[0]==null);if(!A){
                        B.find("select[name='op']").focus()[0].selectedIndex=D.find("select[name='op']")[0].selectedIndex
                        }var z=B.find("select[name='data']");if(z[0]!=null){
                        z[0].selectedIndex=D.find("select[name='data']")[0].selectedIndex
                        }
                    }else{
                    B.find(".data input").val("");B.find("select[name='field']").focus()
                    }if(n.datepickerFix===true&&jQuery.fn.datepicker!==undefined){
                    D.find(".hasDatepicker").each(function(){
                        var E=jQuery.data(this,"datepicker").settings;B.find("#"+this.id).unbind().removeAttr("id").removeClass("hasDatepicker").datepicker(E)
                        })
                    }B.find("select[name='field']").change();return false
                });j.find(".ui-search").click(function(C){
                var B=jQuery(j.selector);var z;var A=B.find("select[name='groupOp'] :selected").val();if(!n.stringResult){
                    z={
                        groupOp:A,
                        rules:[]
                    }
                    }else{
                    z='{"groupOp":"'+A+'","rules":['
                    }B.find(".sf").each(function(D){
                    var G=jQuery(this).find("select[name='field'] :selected").val();var F=jQuery(this).find("select[name='op'] :selected").val();var E=jQuery(this).find("input[name='data'],select[name='data'] :selected").val();if(!n.stringResult){
                        z.rules.push({
                            field:G,
                            op:F,
                            data:E
                        })
                        }else{
                        if(D>0){
                            z+=","
                            }z+='{"field":"'+G+'",';z+='"op":"'+F+'",';z+='"data":"'+E+'"}'
                        }
                    });if(n.stringResult){
                    z+="]}"
                    }n.onSearch(z);return false
                });j.find(".ui-reset").click(function(A){
                var z=jQuery(j.selector);z.find(".ui-del").click();z.find("select[name='groupOp']")[0].selectedIndex=0;n.onReset();return false
                });j.find(".ui-add-last").click(function(){
                var A=jQuery(j.selector+" .sf:last");var z=A.clone(true).insertAfter(A);z.find(".ui-state-default").removeClass("ui-state-hover ui-state-active");z.find(".data input").val("");z.find("select[name='field']").focus();if(n.datepickerFix===true&&jQuery.fn.datepicker!==undefined){
                    A.find(".hasDatepicker").each(function(){
                        var B=jQuery.data(this,"datepicker").settings;z.find("#"+this.id).unbind().removeAttr("id").removeClass("hasDatepicker").datepicker(B)
                        })
                    }z.find("select[name='field']").change();return false
                })
            }
        }return new b(this,a,c)
    };jQuery.fn.searchFilter.version="1.2.9";jQuery.fn.searchFilter.defaults={
    clone:true,
    datepickerFix:true,
    onReset:function(a){
        alert("Reset Clicked. Data Returned: "+a)
        },
    onSearch:function(a){
        alert("Search Clicked. Data Returned: "+a)
        },
    onClose:function(a){
        a.hide()
        },
    groupOps:[{
        op:"AND",
        text:"all"
    },{
        op:"OR",
        text:"any"
    }],
    operators:[{
        op:"eq",
        text:"is equal to"
    },{
        op:"ne",
        text:"is not equal to"
    },{
        op:"lt",
        text:"is less than"
    },{
        op:"le",
        text:"is less or equal to"
    },{
        op:"gt",
        text:"is greater than"
    },{
        op:"ge",
        text:"is greater or equal to"
    },{
        op:"in",
        text:"is in"
    },{
        op:"ni",
        text:"is not in"
    },{
        op:"bw",
        text:"begins with"
    },{
        op:"bn",
        text:"does not begin with"
    },{
        op:"ew",
        text:"ends with"
    },{
        op:"en",
        text:"does not end with"
    },{
        op:"cn",
        text:"contains"
    },{
        op:"nc",
        text:"does not contain"
    }],
    matchText:"match",
    rulesText:"rules",
    resetText:"Reset",
    searchText:"Search",
    stringResult:true,
    windowTitle:"Search Rules"
};(function(a){
    a.fn.extend({
        editRow:function(c,i,h,j,b,e,d,f,g){
            return this.each(function(){
                var n=this,s,o,l,m=0,r=null,q={},k,p;if(!n.grid){
                    return
                }k=a(n).getInd(c,true);if(k==false){
                    return
                }l=a(k).attr("editable")||"0";if(l=="0"){
                    p=n.p.colModel;a("td",k).each(function(w){
                        s=p[w].name;var v=n.p.treeGrid===true&&s==n.p.ExpandColumn;if(v){
                            o=a("span:first",this).html()
                            }else{
                            try{
                                o=a.unformat(this,{
                                    colModel:p[w]
                                    },w)
                                }catch(t){
                                o=a(this).html()
                                }
                            }if(s!="cb"&&s!="subgrid"&&s!="rn"){
                            q[s]=o;if(p[w].editable===true){
                                if(r===null){
                                    r=w
                                    }if(v){
                                    a("span:first",this).html("")
                                    }else{
                                    a(this).html("")
                                    }var u=a.extend({},p[w].editoptions||{},{
                                    id:c+"_"+s,
                                    name:s
                                });if(!p[w].edittype){
                                    p[w].edittype="text"
                                    }var x=createEl(p[w].edittype,u,o,true);a(x).addClass("editable");if(v){
                                    a("span:first",this).append(x)
                                    }else{
                                    a(this).append(x)
                                    }if(p[w].edittype=="select"&&p[w].editoptions.multiple===true&&a.browser.msie){
                                    a(x).width(a(x).width())
                                    }m++
                            }
                            }
                        });if(m>0){
                        q.id=c;n.p.savedRow.push(q);a(k).attr("editable","1");a("td:eq("+r+") input",k).focus();if(i===true){
                            a(k).bind("keydown",function(t){
                                if(t.keyCode===27){
                                    a(n).restoreRow(c,g)
                                    }if(t.keyCode===13){
                                    a(n).saveRow(c,j,b,e,d,f,g);return false
                                    }t.stopPropagation()
                                })
                            }if(a.isFunction(h)){
                            h(c)
                            }
                        }
                    }
                })
            },
        saveRow:function(h,g,e,f,d,c,b){
            return this.each(function(){
                var o=this,u,p={},l={},j,r,q,i;if(!o.grid){
                    return
                }i=a(o).getInd(h,true);if(i==false){
                    return
                }j=a(i).attr("editable");e=e?e:o.p.editurl;if(j==="1"&&e){
                    var t;a("td",i).each(function(v){
                        t=o.p.colModel[v];u=t.name;if(u!="cb"&&u!="subgrid"&&t.editable===true&&u!="rn"){
                            switch(t.edittype){
                                case"checkbox":var k=["Yes","No"];if(t.editoptions){
                                    k=t.editoptions.value.split(":")
                                    }p[u]=a("input",this).attr("checked")?k[0]:k[1];break;case"text":case"password":case"textarea":case"button":p[u]=!o.p.autoencode?a("input, textarea",this).val():a.jgrid.htmlEncode(a("input, textarea",this).val());break;case"select":if(!t.editoptions.multiple){
                                    p[u]=a("select>option:selected",this).val();l[u]=a("select>option:selected",this).text()
                                    }else{
                                    var w=a("select",this),x=[];p[u]=a(w).val();if(p[u]){
                                        p[u]=p[u].join(",")
                                        }else{
                                        p[u]=""
                                        }a("select > option:selected",this).each(function(y,z){
                                        x[y]=a(z).text()
                                        });l[u]=x.join(",")
                                    }if(t.formatter&&t.formatter=="select"){
                                    l={}
                                    }break
                                }q=checkValues(p[u],v,o);if(q[0]===false){
                                q[1]=p[u]+" "+q[1];return false
                                }
                            }
                        });if(q[0]===false){
                        try{
                            info_dialog(a.jgrid.errors.errcap,q[1],a.jgrid.edit.bClose)
                            }catch(s){
                            alert(q[1])
                            }return
                    }if(p){
                        p.id=h;if(f){
                            p=a.extend({},p,f)
                            }
                        }if(!o.grid.hDiv.loading){
                        o.grid.hDiv.loading=true;a("div.loading",o.grid.hDiv).fadeIn("fast");if(e=="clientArray"){
                            p=a.extend({},p,l);var n=a(o).setRowData(h,p);a(i).attr("editable","0");for(var m=0;m<o.p.savedRow.length;m++){
                                if(o.p.savedRow[m].id==h){
                                    r=m;break
                                }
                                }if(r>=0){
                                o.p.savedRow.splice(r,1)
                                }if(a.isFunction(d)){
                                d(h,n)
                                }
                            }else{
                            a.ajax({
                                url:e,
                                data:p,
                                type:"POST",
                                complete:function(x,y){
                                    if(y==="success"){
                                        var w;if(a.isFunction(g)){
                                            w=g(x)
                                            }else{
                                            w=true
                                            }if(w===true){
                                            p=a.extend({},p,l);a(o).setRowData(h,p);a(i).attr("editable","0");for(var v=0;v<o.p.savedRow.length;v++){
                                                if(o.p.savedRow[v].id==h){
                                                    r=v;break
                                                }
                                                }if(r>=0){
                                                o.p.savedRow.splice(r,1)
                                                }if(a.isFunction(d)){
                                                d(h,x.responseText)
                                                }
                                            }else{
                                            a(o).restoreRow(h,b)
                                            }
                                        }
                                    },
                                error:function(k,v){
                                    if(a.isFunction(c)){
                                        c(h,k,v)
                                        }else{
                                        alert("Error Row: "+h+" Result: "+k.status+":"+k.statusText+" Status: "+v)
                                        }
                                    }
                                })
                            }o.grid.hDiv.loading=false;a("div.loading",o.grid.hDiv).fadeOut("fast");a(i).unbind("keydown")
                        }
                    }
                })
            },
        restoreRow:function(c,b){
            return this.each(function(){
                var g=this,d,f;if(!g.grid){
                    return
                }f=a(g).getInd(c,true);if(f==false){
                    return
                }for(var e=0;e<g.p.savedRow.length;e++){
                    if(g.p.savedRow[e].id==c){
                        d=e;break
                    }
                    }if(d>=0){
                    a(g).setRowData(c,g.p.savedRow[d]);a(f).attr("editable","0");g.p.savedRow.splice(d,1)
                    }if(a.isFunction(b)){
                    b(c)
                    }
                })
            }
        })
    })(jQuery);(function(a){
    a.fn.extend({
        editCell:function(d,c,b){
            return this.each(function(){
                var j=this,m,k,g;if(!j.grid||j.p.cellEdit!==true){
                    return
                }c=parseInt(c,10);j.p.selrow=j.rows[d].id;if(!j.p.knv){
                    a(j).GridNav()
                    }if(j.p.savedRow.length>0){
                    if(b===true){
                        if(d==j.p.iRow&&c==j.p.iCol){
                            return
                        }
                        }var h=a("td:eq("+j.p.savedRow[0].ic+")>#"+j.p.savedRow[0].id+"_"+j.p.savedRow[0].name.replace(".","\\."),j.rows[j.p.savedRow[0].id]).val();if(j.p.savedRow[0].v!=h){
                        a(j).saveCell(j.p.savedRow[0].id,j.p.savedRow[0].ic)
                        }else{
                        a(j).restoreCell(j.p.savedRow[0].id,j.p.savedRow[0].ic)
                        }
                    }else{
                    window.setTimeout(function(){
                        a("#"+j.p.knv).attr("tabindex","-1").focus()
                        },0)
                    }m=j.p.colModel[c].name;if(m=="subgrid"||m=="cb"||m=="rn"){
                    return
                }if(j.p.colModel[c].editable===true&&b===true){
                    g=a("td:eq("+c+")",j.rows[d]);if(parseInt(j.p.iCol)>=0&&parseInt(j.p.iRow)>=0){
                        a("td:eq("+j.p.iCol+")",j.rows[j.p.iRow]).removeClass("edit-cell ui-state-highlight");a(j.rows[j.p.iRow]).removeClass("selected-row ui-state-hover")
                        }a(g).addClass("edit-cell ui-state-highlight");a(j.rows[d]).addClass("selected-row ui-state-hover");try{
                        k=a.unformat(g,{
                            colModel:j.p.colModel[c]
                            },c)
                        }catch(l){
                        k=a(g).html()
                        }if(!j.p.colModel[c].edittype){
                        j.p.colModel[c].edittype="text"
                        }j.p.savedRow.push({
                        id:d,
                        ic:c,
                        name:m,
                        v:k
                    });if(a.isFunction(j.p.formatCell)){
                        var i=j.p.formatCell(j.rows[d].id,m,k,d,c);if(i){
                            k=i
                            }
                        }var f=a.extend({},j.p.colModel[c].editoptions||{},{
                        id:d+"_"+m,
                        name:m
                    });var e=createEl(j.p.colModel[c].edittype,f,k,true);if(a.isFunction(j.p.beforeEditCell)){
                        j.p.beforeEditCell(j.rows[d].id,m,k,d,c)
                        }a(g).html("").append(e).attr("tabindex","0");window.setTimeout(function(){
                        a(e).focus()
                        },0);a("input, select, textarea",g).bind("keydown",function(n){
                        if(n.keyCode===27){
                            a(j).restoreCell(d,c)
                            }if(n.keyCode===13){
                            a(j).saveCell(d,c)
                            }if(n.keyCode==9){
                            if(n.shiftKey){
                                a(j).prevCell(d,c)
                                }else{
                                a(j).nextCell(d,c)
                                }
                            }n.stopPropagation()
                        });if(a.isFunction(j.p.afterEditCell)){
                        j.p.afterEditCell(j.rows[d].id,m,k,d,c)
                        }
                    }else{
                    if(parseInt(j.p.iCol)>=0&&parseInt(j.p.iRow)>=0){
                        a("td:eq("+j.p.iCol+")",j.rows[j.p.iRow]).removeClass("edit-cell ui-state-highlight");a(j.rows[j.p.iRow]).removeClass("selected-row ui-state-hover")
                        }a("td:eq("+c+")",j.rows[d]).addClass("edit-cell ui-state-highlight");a(j.rows[d]).addClass("selected-row ui-state-hover");if(a.isFunction(j.p.onSelectCell)){
                        k=a("td:eq("+c+")",j.rows[d]).html().replace(/\&nbsp\;/ig,"");j.p.onSelectCell(j.rows[d].id,m,k,d,c)
                        }
                    }j.p.iCol=c;j.p.iRow=d
                })
            },
        saveCell:function(c,b){
            return this.each(function(){
                var j=this,l;if(!j.grid||j.p.cellEdit!==true){
                    return
                }if(j.p.savedRow.length>=1){
                    l=0
                    }else{
                    l=null
                    }if(l!=null){
                    var g=a("td:eq("+b+")",j.rows[c]),q,o,r=j.p.colModel[b].name.replace(".","\\.");switch(j.p.colModel[b].edittype){
                        case"select":if(!j.p.colModel[b].editoptions.multiple){
                            q=a("#"+c+"_"+r+">option:selected",j.rows[c]).val();o=a("#"+c+"_"+r+">option:selected",j.rows[c]).text()
                            }else{
                            var d=a("#"+c+"_"+r,j.rows[c]),f=[];q=a(d).val();if(q){
                                q.join(",")
                                }else{
                                q=""
                                }a("option:selected",d).each(function(e,s){
                                f[e]=a(s).text()
                                });o=f.join(",")
                            }if(j.p.colModel[b].formatter){
                            o=q
                            }break;case"checkbox":var h=["Yes","No"];if(j.p.colModel[b].editoptions){
                            h=j.p.colModel[b].editoptions.value.split(":")
                            }q=a("#"+c+"_"+r.replace(".","\\."),j.rows[c]).attr("checked")?h[0]:h[1];o=q;break;case"password":case"text":case"textarea":case"button":q=!j.p.autoencode?a("#"+c+"_"+r.replace(".","\\."),j.rows[c]).val():a.jgrid.htmlEncode(a("#"+c+"_"+r.replace(".","\\."),j.rows[c]).val());o=q;break
                            }if(o!=j.p.savedRow[l].v){
                        if(a.isFunction(j.p.beforeSaveCell)){
                            var p=j.p.beforeSaveCell(j.rows[c].id,r,q,c,b);if(p){
                                q=p
                                }
                            }var i=checkValues(q,b,j);if(i[0]===true){
                            var k={};if(a.isFunction(j.p.beforeSubmitCell)){
                                k=j.p.beforeSubmitCell(j.rows[c].id,r,q,c,b);if(!k){
                                    k={}
                                    }
                                }if(o==""){
                                o=" "
                                }if(j.p.cellsubmit=="remote"){
                                if(j.p.cellurl){
                                    var n={};n[r]=q;n.id=j.rows[c].id;n=a.extend(k,n);a.ajax({
                                        url:j.p.cellurl,
                                        data:n,
                                        type:"POST",
                                        complete:function(e,t){
                                            if(t=="success"){
                                                if(a.isFunction(j.p.afterSubmitCell)){
                                                    var s=j.p.afterSubmitCell(e,n.id,r,q,c,b);if(s[0]===true){
                                                        a(g).empty();a(j).setCell(j.rows[c].id,b,o);a(g).addClass("dirty-cell");a(j.rows[c]).addClass("edited");if(a.isFunction(j.p.afterSaveCell)){
                                                            j.p.afterSaveCell(j.rows[c].id,r,q,c,b)
                                                            }j.p.savedRow.splice(0,1)
                                                        }else{
                                                        info_dialog(a.jgrid.errors.errcap,s[1],a.jgrid.edit.bClose);a(j).restoreCell(c,b)
                                                        }
                                                    }else{
                                                    a(g).empty();a(j).setCell(j.rows[c].id,b,o);a(g).addClass("dirty-cell");a(j.rows[c]).addClass("edited");if(a.isFunction(j.p.afterSaveCell)){
                                                        j.p.afterSaveCell(j.rows[c].id,r,q,c,b)
                                                        }j.p.savedRow.splice(0,1)
                                                    }
                                                }
                                            },
                                        error:function(e,s){
                                            if(a.isFunction(j.p.errorCell)){
                                                j.p.errorCell(e,s);a(j).restoreCell(c,b)
                                                }else{
                                                info_dialog(a.jgrid.errors.errcap,e.status+" : "+e.statusText+"<br/>"+s,a.jgrid.edit.bClose);a(j).restoreCell(c,b)
                                                }
                                            }
                                        })
                                    }else{
                                    try{
                                        info_dialog(a.jgrid.errors.errcap,a.jgrid.errors.nourl,a.jgrid.edit.bClose);a(j).restoreCell(c,b)
                                        }catch(m){}
                                    }
                                }if(j.p.cellsubmit=="clientArray"){
                                a(g).empty();a(j).setCell(j.rows[c].id,b,o);a(g).addClass("dirty-cell");a(j.rows[c]).addClass("edited");if(a.isFunction(j.p.afterSaveCell)){
                                    j.p.afterSaveCell(j.rows[c].id,r,q,c,b)
                                    }j.p.savedRow.splice(0,1)
                                }
                            }else{
                            try{
                                window.setTimeout(function(){
                                    info_dialog(a.jgrid.errors.errcap,q+" "+i[1],a.jgrid.edit.bClose)
                                    },100);a(j).restoreCell(c,b)
                                }catch(m){}
                            }
                        }else{
                        a(j).restoreCell(c,b)
                        }
                    }if(a.browser.opera){
                    a("#"+j.p.knv).attr("tabindex","-1").focus()
                    }else{
                    window.setTimeout(function(){
                        a("#"+j.p.knv).attr("tabindex","-1").focus()
                        },0)
                    }
                })
            },
        restoreCell:function(c,b){
            return this.each(function(){
                var h=this,d;if(!h.grid||h.p.cellEdit!==true){
                    return
                }if(h.p.savedRow.length>=1){
                    d=0
                    }else{
                    d=null
                    }if(d!=null){
                    var g=a("td:eq("+b+")",h.rows[c]);if(a.isFunction(a.fn.datepicker)){
                        try{
                            a.datepicker("hide")
                            }catch(f){
                            try{
                                a.datepicker.hideDatepicker()
                                }catch(f){}
                            }
                        }a(g).empty().attr("tabindex","-1");a(h).setCell(h.rows[c].id,b,h.p.savedRow[d].v);h.p.savedRow.splice(0,1)
                    }window.setTimeout(function(){
                    a("#"+h.p.knv).attr("tabindex","-1").focus()
                    },0)
                })
            },
        nextCell:function(c,b){
            return this.each(function(){
                var f=this,e=false;if(!f.grid||f.p.cellEdit!==true){
                    return
                }for(var d=b+1;d<f.p.colModel.length;d++){
                    if(f.p.colModel[d].editable===true){
                        e=d;break
                    }
                    }if(e!==false){
                    a(f).editCell(c,e,true)
                    }else{
                    if(f.p.savedRow.length>0){
                        a(f).saveCell(c,b)
                        }
                    }
                })
            },
        prevCell:function(c,b){
            return this.each(function(){
                var f=this,e=false;if(!f.grid||f.p.cellEdit!==true){
                    return
                }for(var d=b-1;d>=0;d--){
                    if(f.p.colModel[d].editable===true){
                        e=d;break
                    }
                    }if(e!==false){
                    a(f).editCell(c,e,true)
                    }else{
                    if(f.p.savedRow.length>0){
                        a(f).saveCell(c,b)
                        }
                    }
                })
            },
        GridNav:function(){
            return this.each(function(){
                var f=this;if(!f.grid||f.p.cellEdit!==true){
                    return
                }f.p.knv=a("table:first",f.grid.bDiv).attr("id")+"_kn";var e=a("<span style='width:0px;height:0px;background-color:black;' tabindex='0'><span tabindex='-1' style='width:0px;height:0px;background-color:grey' id='"+f.p.knv+"'></span></span>"),c;a(e).insertBefore(f.grid.cDiv);a("#"+f.p.knv).focus();a("#"+f.p.knv).keydown(function(g){
                    switch(g.keyCode){
                        case 38:if(f.p.iRow-1>=0){
                            d(f.p.iRow-1,f.p.iCol,"vu");a(f).editCell(f.p.iRow-1,f.p.iCol,false)
                            }break;case 40:if(f.p.iRow+1<=f.rows.length-1){
                            d(f.p.iRow+1,f.p.iCol,"vd");a(f).editCell(f.p.iRow+1,f.p.iCol,false)
                            }break;case 37:if(f.p.iCol-1>=0){
                            c=b(f.p.iCol-1,"lft");d(f.p.iRow,c,"h");a(f).editCell(f.p.iRow,c,false)
                            }break;case 39:if(f.p.iCol+1<=f.p.colModel.length-1){
                            c=b(f.p.iCol+1,"rgt");d(f.p.iRow,c,"h");a(f).editCell(f.p.iRow,c,false)
                            }break;case 13:if(parseInt(f.p.iCol,10)>=0&&parseInt(f.p.iRow,10)>=0){
                            a(f).editCell(f.p.iRow,f.p.iCol,true)
                            }break
                        }return false
                    });function d(o,m,n){
                    if(n.substr(0,1)=="v"){
                        var g=a(f.grid.bDiv)[0].clientHeight,p=a(f.grid.bDiv)[0].scrollTop,q=f.rows[o].offsetTop+f.rows[o].clientHeight,k=f.rows[o].offsetTop;if(n=="vd"){
                            if(q>=g){
                                a(f.grid.bDiv)[0].scrollTop=a(f.grid.bDiv)[0].scrollTop+f.rows[o].clientHeight
                                }
                            }if(n=="vu"){
                            if(k<p){
                                a(f.grid.bDiv)[0].scrollTop=a(f.grid.bDiv)[0].scrollTop-f.rows[o].clientHeight
                                }
                            }
                        }if(n=="h"){
                        var j=a(f.grid.bDiv)[0].clientWidth,i=a(f.grid.bDiv)[0].scrollLeft,h=f.rows[o].cells[m].offsetLeft+f.rows[o].cells[m].clientWidth,l=f.rows[o].cells[m].offsetLeft;if(h>=j+parseInt(i)){
                            a(f.grid.bDiv)[0].scrollLeft=a(f.grid.bDiv)[0].scrollLeft+f.rows[o].cells[m].clientWidth
                            }else{
                            if(l<i){
                                a(f.grid.bDiv)[0].scrollLeft=a(f.grid.bDiv)[0].scrollLeft-f.rows[o].cells[m].clientWidth
                                }
                            }
                        }
                    }function b(k,g){
                    var j,h;if(g=="lft"){
                        j=k+1;for(h=k;h>=0;h--){
                            if(f.p.colModel[h].hidden!==true){
                                j=h;break
                            }
                            }
                        }if(g=="rgt"){
                        j=k-1;for(h=k;h<f.p.colModel.length;h++){
                            if(f.p.colModel[h].hidden!==true){
                                j=h;break
                            }
                            }
                        }return j
                    }
                })
            },
        getChangedCells:function(c){
            var b=[];if(!c){
                c="all"
                }this.each(function(){
                var e=this,d;if(!e.grid||e.p.cellEdit!==true){
                    return
                }a(e.rows).each(function(f){
                    var g={};if(a(this).hasClass("edited")){
                        a("td",this).each(function(h){
                            d=e.p.colModel[h].name;if(d!=="cb"&&d!=="subgrid"){
                                if(c=="dirty"){
                                    if(a(this).hasClass("dirty-cell")){
                                        g[d]=a.jgrid.htmlDecode(a(this).html())
                                        }
                                    }else{
                                    g[d]=a.jgrid.htmlDecode(a(this).html())
                                    }
                                }
                            });g.id=this.id;b.push(g)
                        }
                    })
                });return b
            }
        })
    })(jQuery);(function(d){
    d.fn.jqm=function(f){
        var e={
            overlay:50,
            closeoverlay:true,
            overlayClass:"jqmOverlay",
            closeClass:"jqmClose",
            trigger:".jqModal",
            ajax:l,
            ajaxText:"",
            target:l,
            modal:l,
            toTop:l,
            onShow:l,
            onHide:l,
            onLoad:l
        };return this.each(function(){
            if(this._jqm){
                return k[this._jqm].c=d.extend({},k[this._jqm].c,f)
                }n++;this._jqm=n;k[n]={
                c:d.extend(e,d.jqm.params,f),
                a:l,
                w:d(this).addClass("jqmID"+n),
                s:n
            };if(e.trigger){
                d(this).jqmAddTrigger(e.trigger)
                }
            })
        };d.fn.jqmAddClose=function(f){
        return j(this,f,"jqmHide")
        };d.fn.jqmAddTrigger=function(f){
        return j(this,f,"jqmShow")
        };d.fn.jqmShow=function(e){
        return this.each(function(){
            d.jqm.open(this._jqm,e)
            })
        };d.fn.jqmHide=function(e){
        return this.each(function(){
            d.jqm.close(this._jqm,e)
            })
        };d.jqm={
        hash:{},
        open:function(B,A){
            var p=k[B],q=p.c,m="."+q.closeClass,v=(parseInt(p.w.css("z-index")));v=(v>0)?v:3000;var f=d("<div></div>").css({
                height:"100%",
                width:"100%",
                position:"fixed",
                left:0,
                top:0,
                "z-index":v-1,
                opacity:q.overlay/100
                });if(p.a){
                return l
                }p.t=A;p.a=true;p.w.css("z-index",v);if(q.modal){
                if(!a[0]){
                    setTimeout(function(){
                        i("bind")
                        },1)
                    }a.push(B)
                }else{
                if(q.overlay>0){
                    if(q.closeoverlay){
                        p.w.jqmAddClose(f)
                        }
                    }else{
                    f=l
                    }
                }p.o=(f)?f.addClass(q.overlayClass).prependTo("body"):l;if(c){
                d("html,body").css({
                    height:"100%",
                    width:"100%"
                });if(f){
                    f=f.css({
                        position:"absolute"
                    })[0];for(var w in {
                        Top:1,
                        Left:1
                    }){
                        f.style.setExpression(w.toLowerCase(),"(_=(document.documentElement.scroll"+w+" || document.body.scroll"+w+"))+'px'")
                        }
                    }
                }if(q.ajax){
                var e=q.target||p.w,x=q.ajax;e=(typeof e=="string")?d(e,p.w):d(e);x=(x.substr(0,1)=="@")?d(A).attr(x.substring(1)):x;e.html(q.ajaxText).load(x,function(){
                    if(q.onLoad){
                        q.onLoad.call(this,p)
                        }if(m){
                        p.w.jqmAddClose(d(m,p.w))
                        }h(p)
                    })
                }else{
                if(m){
                    p.w.jqmAddClose(d(m,p.w))
                    }
                }if(q.toTop&&p.o){
                p.w.before('<span id="jqmP'+p.w[0]._jqm+'"></span>').insertAfter(p.o)
                }(q.onShow)?q.onShow(p):p.w.show();h(p);return l
            },
        close:function(f){
            var e=k[f];if(!e.a){
                return l
                }e.a=l;if(a[0]){
                a.pop();if(!a[0]){
                    i("unbind")
                    }
                }if(e.c.toTop&&e.o){
                d("#jqmP"+e.w[0]._jqm).after(e.w).remove()
                }if(e.c.onHide){
                e.c.onHide(e)
                }else{
                e.w.hide();if(e.o){
                    e.o.remove()
                    }
                }return l
            },
        params:{}
    };var n=0,k=d.jqm.hash,a=[],c=d.browser.msie&&(d.browser.version=="6.0"),l=false,h=function(f){
        var e=d('<iframe src="javascript:false;document.write(\'\');" class="jqm"></iframe>').css({
            opacity:0
        });if(c){
            if(f.o){
                f.o.html('<p style="width:100%;height:100%"/>').prepend(e)
                }else{
                if(!d("iframe.jqm",f.w)[0]){
                    f.w.prepend(e)
                    }
                }
            }g(f)
        },g=function(f){
        try{
            d(":input:visible",f.w)[0].focus()
            }catch(e){}
        },i=function(e){
        d()[e]("keypress",b)[e]("keydown",b)[e]("mousedown",b)
        },b=function(o){
        var f=k[a[a.length-1]],m=(!d(o.target).parents(".jqmID"+f.s)[0]);if(m){
            g(f)
            }return !m
        },j=function(e,f,m){
        return e.each(function(){
            var o=this._jqm;d(f).each(function(){
                if(!this[m]){
                    this[m]=[];d(this).click(function(){
                        for(var p in {
                            jqmShow:1,
                            jqmHide:1
                        }){
                            for(var q in this[p]){
                                if(k[this[p][q]]){
                                    k[this[p][q]].w[p](this)
                                    }
                                }
                            }return l
                        })
                    }this[m].push(o)
                })
            })
        }
    })(jQuery);(function(g){
    g.fn.jqDrag=function(f){
        return c(this,f,"d")
        };g.fn.jqResize=function(i,f){
        return c(this,i,"r",f)
        };g.jqDnR={
        dnr:{},
        e:0,
        drag:function(f){
            if(h.k=="d"){
                e.css({
                    left:h.X+f.pageX-h.pX,
                    top:h.Y+f.pageY-h.pY
                    })
                }else{
                e.css({
                    width:Math.max(f.pageX-h.pX+h.W,0),
                    height:Math.max(f.pageY-h.pY+h.H,0)
                    });if(M1){
                    a.css({
                        width:Math.max(f.pageX-M1.pX+M1.W,0),
                        height:Math.max(f.pageY-M1.pY+M1.H,0)
                        })
                    }
                }return false
            },
        stop:function(){
            g().unbind("mousemove",b.drag).unbind("mouseup",b.stop)
            }
        };var b=g.jqDnR,h=b.dnr,e=b.e,a,c=function(l,j,i,f){
        return l.each(function(){
            j=(j)?g(j,l):l;j.bind("mousedown",{
                e:l,
                k:i
            },function(k){
                var o=k.data,n={};e=o.e;a=f?g(f):false;if(e.css("position")!="relative"){
                    try{
                        e.position(n)
                        }catch(m){}
                    }h={
                    X:n.left||d("left")||0,
                    Y:n.top||d("top")||0,
                    W:d("width")||e[0].scrollWidth||0,
                    H:d("height")||e[0].scrollHeight||0,
                    pX:k.pageX,
                    pY:k.pageY,
                    k:o.k
                    };if(a&&o.k!="d"){
                    M1={
                        X:n.left||f1("left")||0,
                        Y:n.top||f1("top")||0,
                        W:a[0].offsetWidth||f1("width")||0,
                        H:a[0].offsetHeight||f1("height")||0,
                        pX:k.pageX,
                        pY:k.pageY,
                        k:o.k
                        }
                    }else{
                    M1=false
                    }g().mousemove(g.jqDnR.drag).mouseup(g.jqDnR.stop);return false
                })
            })
        },d=function(f){
        return parseInt(e.css(f))||false
        };f1=function(f){
        return parseInt(a.css(f))||false
        }
    })(jQuery);(function(a){
    a.fn.extend({
        setSubGrid:function(){
            return this.each(function(){
                var c=this,b;c.p.colNames.unshift("");c.p.colModel.unshift({
                    name:"subgrid",
                    width:a.browser.safari?c.p.subGridWidth+c.p.cellLayout:c.p.subGridWidth,
                    sortable:false,
                    resizable:false,
                    hidedlg:true,
                    search:false
                });b=c.p.subGridModel;if(b[0]){
                    b[0].align=a.extend([],b[0].align||[]);for(i=0;i<b[0].name.length;i++){
                        b[0].align[i]=b[0].align[i]||"left"
                        }
                    }
                })
            },
        addSubGridCell:function(e,c){
            var b="",d;this.each(function(){
                b=this.formatCol(e,c);d=this.p.gridview
                });if(d===false){
                return"<td role='grid' class='ui-sgcollapsed sgcollapsed' "+b+"><a href='javascript:void(0);'><span class='ui-icon ui-icon-plus'></span></a></td>"
                }else{
                return"<td role='grid' "+b+"></td>"
                }
            },
        addSubGrid:function(b,c){
            return this.each(function(){
                var m=this;if(!m.grid){
                    return
                }var n,o,p,j,k,g,h;a("td:eq("+c+")",b).click(function(q){
                    if(a(this).hasClass("sgcollapsed")){
                        p=m.p.id;n=a(this).parent();j=c>=1?"<td colspan='"+c+"'>&nbsp;</td>":"";o=a(n).attr("id");h=true;if(a.isFunction(m.p.subGridBeforeExpand)){
                            h=m.p.subGridBeforeExpand(p+"_"+o,o)
                            }if(h===false){
                            return false
                            }k=0;a.each(m.p.colModel,function(s,r){
                            if(this.hidden===true||this.name=="rn"||this.name=="cb"){
                                k++
                            }
                            });g="<tr role='row' class='ui-subgrid'>"+j+"<td><span class='ui-icon ui-icon-carat-1-sw'/></td><td colspan='"+parseInt(m.p.colNames.length-1-k)+"' class='ui-widget-content subgrid-data'><div id="+p+"_"+o+" class='tablediv'>";a(this).parent().after(g+"</div></td></tr>");if(a.isFunction(m.p.subGridRowExpanded)){
                            m.p.subGridRowExpanded(p+"_"+o,o)
                            }else{
                            l(n)
                            }a(this).html("<a href='javascript:void(0);'><span class='ui-icon ui-icon-minus'></span></a>").removeClass("sgcollapsed").addClass("sgexpanded")
                        }else{
                        if(a(this).hasClass("sgexpanded")){
                            h=true;if(a.isFunction(m.p.subGridRowColapsed)){
                                n=a(this).parent();o=a(n).attr("id");h=m.p.subGridRowColapsed(p+"_"+o,o)
                                }if(h===false){
                                return false
                                }a(this).parent().next().remove(".ui-subgrid");a(this).html("<a href='javascript:void(0);'><span class='ui-icon ui-icon-plus'></span></a>").removeClass("sgexpanded").addClass("sgcollapsed")
                            }
                        }return false
                    });var l=function(u){
                    var t,q,v,s,r;q=a(u).attr("id");v={
                        id:q,
                        nd_:(new Date().getTime())
                        };if(!m.p.subGridModel[0]){
                        return false
                        }if(m.p.subGridModel[0].params){
                        for(r=0;r<m.p.subGridModel[0].params.length;r++){
                            for(s=0;s<m.p.colModel.length;s++){
                                if(m.p.colModel[s].name==m.p.subGridModel[0].params[r]){
                                    v[m.p.colModel[s].name]=a("td:eq("+s+")",u).text().replace(/\&nbsp\;/ig,"")
                                    }
                                }
                            }
                        }if(!m.grid.hDiv.loading){
                        m.grid.hDiv.loading=true;a("#load_"+m.p.id).show();if(!m.p.subgridtype){
                            m.p.subgridtype=m.p.datatype
                            }if(a.isFunction(m.p.subgridtype)){
                            m.p.subgridtype(v)
                            }switch(m.p.subgridtype){
                            case"xml":a.ajax({
                                type:m.p.mtype,
                                url:m.p.subGridUrl,
                                dataType:"xml",
                                data:v,
                                complete:function(w){
                                    d(w.responseXML,q);w=null
                                    }
                                });break;case"json":a.ajax({
                                type:m.p.mtype,
                                url:m.p.subGridUrl,
                                dataType:"text",
                                data:v,
                                complete:function(w){
                                    f(a.jgrid.parse(w.responseText),q);w=null
                                    }
                                });break
                            }
                        }return false
                    };var e=function(r,q,t){
                    var s=a("<td align='"+m.p.subGridModel[0].align[t]+"'></td>").html(q);a(r).append(s)
                    };var d=function(v,t){
                    var x,u,w,q,s=a("<table cellspacing='0' cellpadding='0' border='0'><tbody></tbody></table>"),r=a("<tr></tr>");for(u=0;u<m.p.subGridModel[0].name.length;u++){
                        x=a("<th class='ui-state-default ui-th-column'></th>");a(x).html(m.p.subGridModel[0].name[u]);a(x).width(m.p.subGridModel[0].width[u]);a(r).append(x)
                        }a(s).append(r);if(v){
                        q=m.p.xmlReader.subgrid;a(q.root+" "+q.row,v).each(function(){
                            r=a("<tr class='ui-widget-content ui-subtblcell'></tr>");if(q.repeatitems===true){
                                a(q.cell,this).each(function(A){
                                    e(r,a(this).text()||"&nbsp;",A)
                                    })
                                }else{
                                var z=m.p.subGridModel[0].mapping||m.p.subGridModel[0].name;if(z){
                                    for(u=0;u<z.length;u++){
                                        e(r,a(z[u],this).text()||"&nbsp;",u)
                                        }
                                    }
                                }a(s).append(r)
                            })
                        }var y=a("table:first",m.grid.bDiv).attr("id")+"_";a("#"+y+t).append(s);m.grid.hDiv.loading=false;a("#load_"+m.p.id).hide();return false
                    };var f=function(x,u){
                    var z,B,v,y,q,s=a("<table cellspacing='0' cellpadding='0' border='0'><tbody></tbody></table>"),r=a("<tr></tr>");for(v=0;v<m.p.subGridModel[0].name.length;v++){
                        z=a("<th class='ui-state-default ui-th-column'></th>");a(z).html(m.p.subGridModel[0].name[v]);a(z).width(m.p.subGridModel[0].width[v]);a(r).append(z)
                        }a(s).append(r);if(x){
                        q=m.p.jsonReader.subgrid;B=x[q.root];if(typeof B!=="undefined"){
                            for(v=0;v<B.length;v++){
                                y=B[v];r=a("<tr class='ui-widget-content ui-subtblcell'></tr>");if(q.repeatitems===true){
                                    if(q.cell){
                                        y=y[q.cell]
                                        }for(var t=0;t<y.length;t++){
                                        e(r,y[t]||"&nbsp;",t)
                                        }
                                    }else{
                                    var w=m.p.subGridModel[0].mapping||m.p.subGridModel[0].name;if(w.length){
                                        for(var t=0;t<w.length;t++){
                                            e(r,y[w[t]]||"&nbsp;",t)
                                            }
                                        }
                                    }a(s).append(r)
                                }
                            }
                        }var A=a("table:first",m.grid.bDiv).attr("id")+"_";a("#"+A+u).append(s);m.grid.hDiv.loading=false;a("#load_"+m.p.id).hide();return false
                    };m.subGridXml=function(r,q){
                    d(r,q)
                    };m.subGridJson=function(r,q){
                    f(r,q)
                    }
                })
            },
        expandSubGridRow:function(b){
            return this.each(function(){
                var e=this;if(!e.grid&&!b){
                    return
                }if(e.p.subGrid===true){
                    var c=a(this).getInd(b,true);if(c){
                        var d=a("td.sgcollapsed",c)[0];if(d){
                            a(d).trigger("click")
                            }
                        }
                    }
                })
            },
        collapseSubGridRow:function(b){
            return this.each(function(){
                var e=this;if(!e.grid&&!b){
                    return
                }if(e.p.subGrid===true){
                    var c=a(this).getInd(b,true);if(c){
                        var d=a("td.sgexpanded",c)[0];if(d){
                            a(d).trigger("click")
                            }
                        }
                    }
                })
            },
        toggleSubGridRow:function(b){
            return this.each(function(){
                var e=this;if(!e.grid&&!b){
                    return
                }if(e.p.subGrid===true){
                    var c=a(this).getInd(b,true);if(c){
                        var d=a("td.sgcollapsed",c)[0];if(d){
                            a(d).trigger("click")
                            }else{
                            d=a("td.sgexpanded",c)[0];if(d){
                                a(d).trigger("click")
                                }
                            }
                        }
                    }
                })
            }
        })
    })(jQuery);(function(a){
    a.fn.extend({
        setTreeNode:function(b,c){
            return this.each(function(){
                var g=this;if(!g.grid||!g.p.treeGrid){
                    return
                }var j=g.p.expColInd;var i=g.p.treeReader.expanded_field;var f=g.p.treeReader.leaf_field;var e=g.p.treeReader.level_field;c.level=b[e];if(g.p.treeGridModel=="nested"){
                    c.lft=b[g.p.treeReader.left_field];c.rgt=b[g.p.treeReader.right_field];if(!b[f]){
                        b[f]=(parseInt(c.rgt,10)===parseInt(c.lft,10)+1)?"true":"false"
                        }
                    }else{
                    c.parent_id=b[g.p.treeReader.parent_id_field]
                    }var k=parseInt(c.level,10),h,l;if(g.p.tree_root_level===0){
                    h=k+1;l=k
                    }else{
                    h=k;l=k-1
                    }var d="<div class='tree-wrap' style='width:"+(h*18)+"px;'>";d+="<div style='left:"+(l*18)+"px;' class='ui-icon ";if(b[f]=="true"||b[f]==true){
                    d+=g.p.treeIcons.leaf+" tree-leaf'";c.isLeaf=true
                    }else{
                    if(b[i]=="true"||b[i]==true){
                        d+=g.p.treeIcons.minus+" tree-minus treeclick'";c.expanded=true
                        }else{
                        d+=g.p.treeIcons.plus+" tree-plus treeclick'";c.expanded=false
                        }c.isLeaf=false
                    }d+="</div></div>";if(parseInt(b[e],10)!==parseInt(g.p.tree_root_level,10)){
                    if(!a(g).isVisibleNode(c)){
                        a(c).css("display","none")
                        }
                    }a("td:eq("+j+")",c).wrapInner("<span></span>").prepend(d);a(".treeclick",c).bind("click",function(o){
                    var n=o.target||o.srcElement;var m=a(n,g.rows).parents("tr.jqgrow")[0].rowIndex;if(!g.rows[m].isLeaf){
                        if(g.rows[m].expanded){
                            a(g).collapseRow(g.rows[m]);a(g).collapseNode(g.rows[m])
                            }else{
                            a(g).expandRow(g.rows[m]);a(g).expandNode(g.rows[m])
                            }
                        }return false
                    });if(g.p.ExpandColClick===true){
                    a("span",c).css("cursor","pointer").bind("click",function(o){
                        var n=o.target||o.srcElement;var m=a(n,g.rows).parents("tr.jqgrow")[0].rowIndex;if(!g.rows[m].isLeaf){
                            if(g.rows[m].expanded){
                                a(g).collapseRow(g.rows[m]);a(g).collapseNode(g.rows[m])
                                }else{
                                a(g).expandRow(g.rows[m]);a(g).expandNode(g.rows[m])
                                }
                            }a(g).setSelection(g.rows[m].id);return false
                        })
                    }
                })
            },
        setTreeGrid:function(){
            return this.each(function(){
                var d=this,c=0;if(!d.p.treeGrid){
                    return
                }if(!d.p.treedatatype){
                    a.extend(d.p,{
                        treedatatype:d.p.datatype
                        })
                    }d.p.subGrid=false;d.p.altRows=false;d.p.pgbuttons=false;d.p.pginput=false;d.p.multiselect=false;d.p.rowList=[];d.p.treeIcons=a.extend({
                    plus:"ui-icon-triangle-1-e",
                    minus:"ui-icon-triangle-1-s",
                    leaf:"ui-icon-radio-off"
                },d.p.treeIcons||{});if(d.p.treeGridModel=="nested"){
                    d.p.treeReader=a.extend({
                        level_field:"level",
                        left_field:"lft",
                        right_field:"rgt",
                        leaf_field:"isLeaf",
                        expanded_field:"expanded"
                    },d.p.treeReader)
                    }else{
                    if(d.p.treeGridModel=="adjacency"){
                        d.p.treeReader=a.extend({
                            level_field:"level",
                            parent_id_field:"parent",
                            leaf_field:"isLeaf",
                            expanded_field:"expanded"
                        },d.p.treeReader)
                        }
                    }for(var b in d.p.colModel){
                    if(d.p.colModel[b].name==d.p.ExpandColumn){
                        d.p.expColInd=c;break
                    }c++
                }if(!d.p.expColInd){
                    d.p.expColInd=0
                    }a.each(d.p.treeReader,function(e,f){
                    if(f){
                        d.p.colNames.push(f);d.p.colModel.push({
                            name:f,
                            width:1,
                            hidden:true,
                            sortable:false,
                            resizable:false,
                            hidedlg:true,
                            editable:true,
                            search:false
                        })
                        }
                    })
                })
            },
        expandRow:function(b){
            this.each(function(){
                var d=this;if(!d.grid||!d.p.treeGrid){
                    return
                }var c=a(d).getNodeChildren(b);a(c).each(function(e){
                    a(this).css("display","");if(this.expanded){
                        a(d).expandRow(this)
                        }
                    })
                })
            },
        collapseRow:function(b){
            this.each(function(){
                var d=this;if(!d.grid||!d.p.treeGrid){
                    return
                }var c=a(d).getNodeChildren(b);a(c).each(function(e){
                    a(this).css("display","none");a(d).collapseRow(this)
                    })
                })
            },
        getRootNodes:function(){
            var b=[];this.each(function(){
                var d=this;if(!d.grid||!d.p.treeGrid){
                    return
                }switch(d.p.treeGridModel){
                    case"nested":var c=d.p.treeReader.level_field;a(d.rows).each(function(e){
                        if(parseInt(this[c],10)===parseInt(d.p.tree_root_level,10)){
                            b.push(this)
                            }
                        });break;case"adjacency":a(d.rows).each(function(e){
                        if(this.parent_id==null||this.parent_id.toLowerCase()=="null"){
                            b.push(this)
                            }
                        });break
                    }
                });return b
            },
        getNodeDepth:function(c){
            var b=null;this.each(function(){
                var d=this;if(!this.grid||!this.p.treeGrid){
                    return
                }switch(d.p.treeGridModel){
                    case"nested":b=parseInt(c.level,10)-parseInt(this.p.tree_root_level,10);break;case"adjacency":b=a(d).getNodeAncestors(c).length;break
                        }
                });return b
            },
        getNodeParent:function(c){
            var b=null;this.each(function(){
                var g=this;if(!g.grid||!g.p.treeGrid){
                    return
                }switch(g.p.treeGridModel){
                    case"nested":var e=parseInt(c.lft,10),d=parseInt(c.rgt,10),f=parseInt(c.level,10);a(this.rows).each(function(){
                        if(parseInt(this.level,10)===f-1&&parseInt(this.lft)<e&&parseInt(this.rgt)>d){
                            b=this;return false
                            }
                        });break;case"adjacency":a(this.rows).each(function(){
                        if(this.id==c.parent_id){
                            b=this;return false
                            }
                        });break
                    }
                });return b
            },
        getNodeChildren:function(c){
            var b=[];this.each(function(){
                var g=this;if(!g.grid||!g.p.treeGrid){
                    return
                }switch(g.p.treeGridModel){
                    case"nested":var e=parseInt(c.lft,10),d=parseInt(c.rgt,10),f=parseInt(c.level,10);a(this.rows).each(function(h){
                        if(parseInt(this.level,10)===f+1&&parseInt(this.lft,10)>e&&parseInt(this.rgt,10)<d){
                            b.push(this)
                            }
                        });break;case"adjacency":a(this.rows).each(function(h){
                        if(this.parent_id==c.id){
                            b.push(this)
                            }
                        });break
                    }
                });return b
            },
        getFullTreeNode:function(c){
            var b=[];this.each(function(){
                var g=this;if(!g.grid||!g.p.treeGrid){
                    return
                }switch(g.p.treeGridModel){
                    case"nested":var e=parseInt(c.lft,10),d=parseInt(c.rgt,10),f=parseInt(c.level,10);a(this.rows).each(function(h){
                        if(parseInt(this.level,10)>=f&&parseInt(this.lft,10)>=e&&parseInt(this.lft,10)<=d){
                            b.push(this)
                            }
                        });break;case"adjacency":b.push(c);a(this.rows).each(function(h){
                        len=b.length;for(h=0;h<len;h++){
                            if(b[h].id==this.parent_id){
                                b.push(this);break
                            }
                            }
                        });break
                    }
                });return b
            },
        getNodeAncestors:function(c){
            var b=[];this.each(function(){
                if(!this.grid||!this.p.treeGrid){
                    return
                }var d=a(this).getNodeParent(c);while(d){
                    b.push(d);d=a(this).getNodeParent(d)
                    }
                });return b
            },
        isVisibleNode:function(c){
            var b=true;this.each(function(){
                var e=this;if(!e.grid||!e.p.treeGrid){
                    return
                }var d=a(e).getNodeAncestors(c);a(d).each(function(){
                    b=b&&this.expanded;if(!b){
                        return false
                        }
                    })
                });return b
            },
        isNodeLoaded:function(c){
            var b;this.each(function(){
                var d=this;if(!d.grid||!d.p.treeGrid){
                    return
                }if(c.loaded!==undefined){
                    b=c.loaded
                    }else{
                    if(c.isLeaf||a(d).getNodeChildren(c).length>0){
                        b=true
                        }else{
                        b=false
                        }
                    }
                });return b
            },
        expandNode:function(b){
            return this.each(function(){
                if(!this.grid||!this.p.treeGrid){
                    return
                }if(!b.expanded){
                    if(a(this).isNodeLoaded(b)){
                        b.expanded=true;a("div.treeclick",b).removeClass(this.p.treeIcons.plus+" tree-plus").addClass(this.p.treeIcons.minus+" tree-minus")
                        }else{
                        b.expanded=true;a("div.treeclick",b).removeClass(this.p.treeIcons.plus+" tree-plus").addClass(this.p.treeIcons.minus+" tree-minus");this.p.treeANode=b.rowIndex;this.p.datatype=this.p.treedatatype;if(this.p.treeGridModel=="nested"){
                            a(this).setGridParam({
                                postData:{
                                    nodeid:b.id,
                                    n_left:b.lft,
                                    n_right:b.rgt,
                                    n_level:b.level
                                    }
                                })
                            }else{
                            a(this).setGridParam({
                                postData:{
                                    nodeid:b.id,
                                    parentid:b.parent_id,
                                    n_level:b.level
                                    }
                                })
                            }a(this).trigger("reloadGrid");if(this.p.treeGridModel=="nested"){
                            a(this).setGridParam({
                                postData:{
                                    nodeid:"",
                                    n_left:"",
                                    n_right:"",
                                    n_level:""
                                }
                                })
                            }else{
                            a(this).setGridParam({
                                postData:{
                                    nodeid:"",
                                    parentid:"",
                                    n_level:""
                                }
                                })
                            }
                        }
                    }
                })
            },
        collapseNode:function(b){
            return this.each(function(){
                if(!this.grid||!this.p.treeGrid){
                    return
                }if(b.expanded){
                    b.expanded=false;a("div.treeclick",b).removeClass(this.p.treeIcons.minus+" tree-minus").addClass(this.p.treeIcons.plus+" tree-plus")
                    }
                })
            },
        SortTree:function(b){
            return this.each(function(){
                if(!this.grid||!this.p.treeGrid){
                    return
                }var f,c,g,e=[],h=this,d=a(this).getRootNodes();d.sort(function(j,i){
                    if(j.sortKey<i.sortKey){
                        return -b
                        }if(j.sortKey>i.sortKey){
                        return b
                        }return 0
                    });if(d[0]){
                    a("td",d[0]).each(function(i){
                        a(this).css("width",h.grid.headers[i].width+"px")
                        });h.grid.cols=d[0].cells
                    }for(f=0,c=d.length;f<c;f++){
                    g=d[f];e.push(g);a(this).collectChildrenSortTree(e,g,b)
                    }a.each(e,function(i,j){
                    a("tbody",h.grid.bDiv).append(j);j.sortKey=null
                    })
                })
            },
        collectChildrenSortTree:function(b,d,c){
            return this.each(function(){
                if(!this.grid||!this.p.treeGrid){
                    return
                }var g,e,h,f=a(this).getNodeChildren(d);f.sort(function(j,i){
                    if(j.sortKey<i.sortKey){
                        return -c
                        }if(j.sortKey>i.sortKey){
                        return c
                        }return 0
                    });for(g=0,e=f.length;g<e;g++){
                    h=f[g];b.push(h);a(this).collectChildrenSortTree(b,h,c)
                    }
                })
            },
        setTreeRow:function(c,d){
            var b,e=false;this.each(function(){
                var f=this;if(!f.grid||!f.p.treeGrid){
                    return
                }e=a(f).setRowData(c,d)
                });return e
            },
        delTreeNode:function(b){
            return this.each(function(){
                var f=this;if(!f.grid||!f.p.treeGrid){
                    return
                }var d=a(f).getInd(b,true);if(d){
                    var e=a(f).getNodeChildren(d);if(e.length>0){
                        for(var c=0;c<e.length;c++){
                            a(f).delRowData(e[c].id)
                            }
                        }a(f).delRowData(d.id)
                    }
                })
            }
        })
    })(jQuery);(function(a){
    a.fn.extend({
        jqGridImport:function(b){
            b=a.extend({
                imptype:"xml",
                impstring:"",
                impurl:"",
                mtype:"GET",
                impData:{},
                xmlGrid:{
                    config:"roots>grid",
                    data:"roots>rows"
                },
                jsonGrid:{
                    config:"grid",
                    data:"data"
                }
                },b||{});return this.each(function(){
                var f=this;var d=function(h,m){
                    var g=a(m.xmlGrid.config,h)[0];var l=a(m.xmlGrid.data,h)[0];if(xmlJsonClass.xml2json&&a.jgrid.parse){
                        var n=xmlJsonClass.xml2json(g," ");var n=a.jgrid.parse(n);for(var i in n){
                            var j=n[i]
                            }if(l){
                            var k=n.grid.datatype;n.grid.datatype="xmlstring";n.grid.datastr=h;a(f).jqGrid(j).setGridParam({
                                datatype:k
                            })
                            }else{
                            a(f).jqGrid(j)
                            }n=null;j=null
                        }else{
                        alert("xml2json or parse are not present")
                        }
                    };var e=function(h,k){
                    if(h&&typeof h=="string"){
                        var g=a.jgrid.parse(h);var l=g[k.jsonGrid.config];var i=g[k.jsonGrid.data];if(i){
                            var j=l.datatype;l.datatype="jsonstring";l.datastr=i;a(f).jqGrid(l).setGridParam({
                                datatype:j
                            })
                            }else{
                            a(f).jqGrid(l)
                            }
                        }
                    };switch(b.imptype){
                    case"xml":a.ajax({
                        url:b.impurl,
                        type:b.mtype,
                        data:b.impData,
                        dataType:"xml",
                        complete:function(g,h){
                            if(h=="success"){
                                d(g.responseXML,b);if(a.isFunction(b.importComplete)){
                                    b.importComplete(g)
                                    }
                                }g=null
                            }
                        });break;case"xmlstring":if(b.impstring&&typeof b.impstring=="string"){
                        var c=a.jgrid.stringToDoc(b.impstring);if(c){
                            d(c,b);if(a.isFunction(b.importComplete)){
                                b.importComplete(c)
                                }b.impstring=null
                            }c=null
                        }break;case"json":a.ajax({
                        url:b.impurl,
                        type:b.mtype,
                        data:b.impData,
                        dataType:"json",
                        complete:function(g,h){
                            if(h=="success"){
                                e(g.responseText,b);if(a.isFunction(b.importComplete)){
                                    b.importComplete(g)
                                    }
                                }g=null
                            }
                        });break;case"jsonstring":if(b.impstring&&typeof b.impstring=="string"){
                        e(b.impstring,b);if(a.isFunction(b.importComplete)){
                            b.importComplete(b.impstring)
                            }b.impstring=null
                        }break
                    }
                })
            },
        jqGridExport:function(c){
            c=a.extend({
                exptype:"xmlstring",
                root:"grid",
                ident:"\t"
            },c||{});var b=null;this.each(function(){
                if(!this.grid){
                    return
                }var e=a(this).getGridParam();if(e.rownumbers){
                    e.colNames.splice(0);e.colModel.splice(0)
                    }if(e.multiselect){
                    e.colNames.splice(0);e.colModel.splice(0)
                    }if(e.subgrid){
                    e.colNames.splice(0);e.colModel.splice(0)
                    }if(e.treeGrid){
                    for(var d in e.treeReader){
                        e.colNames.splice(e.colNames.length-1);e.colModel.splice(e.colModel.length-1)
                        }
                    }switch(c.exptype){
                    case"xmlstring":b="<"+c.root+">"+xmlJsonClass.json2xml(e,c.ident)+"</"+c.root+">";break;case"jsonstring":b="{"+xmlJsonClass.toJson(e,c.root,c.ident)+"}";break
                        }
                });return b
            }
        })
    })(jQuery);var xmlJsonClass={
    xml2json:function(b,d){
        if(b.nodeType===9){
            b=b.documentElement
            }var a=this.removeWhite(b);var e=this.toObj(a);var c=this.toJson(e,b.nodeName,"\t");return"{\n"+d+(d?c.replace(/\t/g,d):c.replace(/\t|\n/g,""))+"\n}"
        },
    json2xml:function(d,c){
        var e=function(q,f,h){
            var o="";var l,g;if(q instanceof Array){
                if(q.length===0){
                    o+=h+"<"+f+">__EMPTY_ARRAY_</"+f+">\n"
                    }else{
                    for(l=0,g=q.length;l<g;l+=1){
                        var p=h+e(q[l],f,h+"\t")+"\n";o+=p
                        }
                    }
                }else{
                if(typeof(q)==="object"){
                    var k=false;o+=h+"<"+f;var j;for(j in q){
                        if(q.hasOwnProperty(j)){
                            if(j.charAt(0)==="@"){
                                o+=" "+j.substr(1)+'="'+q[j].toString()+'"'
                                }else{
                                k=true
                                }
                            }
                        }o+=k?">":"/>";if(k){
                        for(j in q){
                            if(q.hasOwnProperty(j)){
                                if(j==="#text"){
                                    o+=q[j]
                                    }else{
                                    if(j==="#cdata"){
                                        o+="<![CDATA["+q[j]+"]]>"
                                        }else{
                                        if(j.charAt(0)!=="@"){
                                            o+=e(q[j],j,h+"\t")
                                            }
                                        }
                                    }
                                }
                            }o+=(o.charAt(o.length-1)==="\n"?h:"")+"</"+f+">"
                        }
                    }else{
                    if(typeof(q)==="function"){
                        o+=h+"<"+f+"><![CDATA["+q+"]]></"+f+">"
                        }else{
                        if(q.toString()==='""'||q.toString().length===0){
                            o+=h+"<"+f+">__EMPTY_STRING_</"+f+">"
                            }else{
                            o+=h+"<"+f+">"+q.toString()+"</"+f+">"
                            }
                        }
                    }
                }return o
            };var b="";var a;for(a in d){
            if(d.hasOwnProperty(a)){
                b+=e(d[a],a,"")
                }
            }return c?b.replace(/\t/g,c):b.replace(/\t|\n/g,"")
        },
    toObj:function(b){
        var g={};var f=/function/i;if(b.nodeType===1){
            if(b.attributes.length){
                var e;for(e=0;e<b.attributes.length;e+=1){
                    g["@"+b.attributes[e].nodeName]=(b.attributes[e].nodeValue||"").toString()
                    }
                }if(b.firstChild){
                var a=0,d=0,c=false;var h;for(h=b.firstChild;h;h=h.nextSibling){
                    if(h.nodeType===1){
                        c=true
                        }else{
                        if(h.nodeType===3&&h.nodeValue.match(/[^ \f\n\r\t\v]/)){
                            a+=1
                            }else{
                            if(h.nodeType===4){
                                d+=1
                                }
                            }
                        }
                    }if(c){
                    if(a<2&&d<2){
                        this.removeWhite(b);for(h=b.firstChild;h;h=h.nextSibling){
                            if(h.nodeType===3){
                                g["#text"]=this.escape(h.nodeValue)
                                }else{
                                if(h.nodeType===4){
                                    if(f.test(h.nodeValue)){
                                        g[h.nodeName]=[g[h.nodeName],h.nodeValue]
                                        }else{
                                        g["#cdata"]=this.escape(h.nodeValue)
                                        }
                                    }else{
                                    if(g[h.nodeName]){
                                        if(g[h.nodeName] instanceof Array){
                                            g[h.nodeName][g[h.nodeName].length]=this.toObj(h)
                                            }else{
                                            g[h.nodeName]=[g[h.nodeName],this.toObj(h)]
                                            }
                                        }else{
                                        g[h.nodeName]=this.toObj(h)
                                        }
                                    }
                                }
                            }
                        }else{
                        if(!b.attributes.length){
                            g=this.escape(this.innerXml(b))
                            }else{
                            g["#text"]=this.escape(this.innerXml(b))
                            }
                        }
                    }else{
                    if(a){
                        if(!b.attributes.length){
                            g=this.escape(this.innerXml(b));if(g==="__EMPTY_ARRAY_"){
                                g="[]"
                                }else{
                                if(g==="__EMPTY_STRING_"){
                                    g=""
                                    }
                                }
                            }else{
                            g["#text"]=this.escape(this.innerXml(b))
                            }
                        }else{
                        if(d){
                            if(d>1){
                                g=this.escape(this.innerXml(b))
                                }else{
                                for(h=b.firstChild;h;h=h.nextSibling){
                                    if(f.test(b.firstChild.nodeValue)){
                                        g=b.firstChild.nodeValue;break
                                    }else{
                                        g["#cdata"]=this.escape(h.nodeValue)
                                        }
                                    }
                                }
                            }
                        }
                    }
                }if(!b.attributes.length&&!b.firstChild){
                g=null
                }
            }else{
            if(b.nodeType===9){
                g=this.toObj(b.documentElement)
                }else{
                alert("unhandled node type: "+b.nodeType)
                }
            }return g
        },
    toJson:function(b,a,d){
        var j=a?('"'+a+'"'):"";if(b==="[]"){
            j+=(a?":[]":"[]")
            }else{
            if(b instanceof Array){
                var c,f;for(f=0,c=b.length;f<c;f+=1){
                    b[f]=this.toJson(b[f],"",d+"\t")
                    }j+=(a?":[":"[")+(b.length>1?("\n"+d+"\t"+b.join(",\n"+d+"\t")+"\n"+d):b.join(""))+"]"
                }else{
                if(b===null){
                    j+=(a&&":")+"null"
                    }else{
                    if(typeof(b)==="object"){
                        var g=[];var e;for(e in b){
                            if(b.hasOwnProperty(e)){
                                g[g.length]=this.toJson(b[e],e,d+"\t")
                                }
                            }j+=(a?":{":"{")+(g.length>1?("\n"+d+"\t"+g.join(",\n"+d+"\t")+"\n"+d):g.join(""))+"}"
                        }else{
                        if(typeof(b)==="string"){
                            var h=/(^-?\d+\.?\d*$)/;var k=/function/i;b=b.toString();if(h.test(b)||k.test(b)||b==="false"||b==="true"){
                                j+=(a&&":")+b
                                }else{
                                j+=(a&&":")+'"'+b+'"'
                                }
                            }else{
                            j+=(a&&":")+b.toString()
                            }
                        }
                    }
                }
            }return j
        },
    innerXml:function(d){
        var b="";if("innerHTML" in d){
            b=d.innerHTML
            }else{
            var a=function(j){
                var g="",f;if(j.nodeType===1){
                    g+="<"+j.nodeName;for(f=0;f<j.attributes.length;f+=1){
                        g+=" "+j.attributes[f].nodeName+'="'+(j.attributes[f].nodeValue||"").toString()+'"'
                        }if(j.firstChild){
                        g+=">";for(var h=j.firstChild;h;h=h.nextSibling){
                            g+=a(h)
                            }g+="</"+j.nodeName+">"
                        }else{
                        g+="/>"
                        }
                    }else{
                    if(j.nodeType===3){
                        g+=j.nodeValue
                        }else{
                        if(j.nodeType===4){
                            g+="<![CDATA["+j.nodeValue+"]]>"
                            }
                        }
                    }return g
                };for(var e=d.firstChild;e;e=e.nextSibling){
                b+=a(e)
                }
            }return b
        },
    escape:function(a){
        return a.replace(/[\\]/g,"\\\\").replace(/[\"]/g,'\\"').replace(/[\n]/g,"\\n").replace(/[\r]/g,"\\r")
        },
    removeWhite:function(b){
        b.normalize();var c;for(c=b.firstChild;c;){
            if(c.nodeType===3){
                if(!c.nodeValue.match(/[^ \f\n\r\t\v]/)){
                    var a=c.nextSibling;b.removeChild(c);c=a
                    }else{
                    c=c.nextSibling
                    }
                }else{
                if(c.nodeType===1){
                    this.removeWhite(c);c=c.nextSibling
                    }else{
                    c=c.nextSibling
                    }
                }
            }return b
        }
    };(function(a){
    a.fn.extend({
        setColumns:function(b){
            b=a.extend({
                top:0,
                left:0,
                width:200,
                height:"auto",
                dataheight:"auto",
                modal:false,
                drag:true,
                beforeShowForm:null,
                afterShowForm:null,
                afterSubmitForm:null,
                closeOnEscape:true,
                ShrinkToFit:false,
                jqModal:false,
                saveicon:[true,"left","ui-icon-disk"],
                closeicon:[true,"left","ui-icon-close"],
                onClose:null,
                colnameview:true,
                closeAfterSubmit:true,
                updateAfterCheck:false
            },a.jgrid.col,b||{});return this.each(function(){
                var j=this;if(!j.grid){
                    return
                }var k=typeof b.beforeShowForm==="function"?true:false;var d=typeof b.afterShowForm==="function"?true:false;var e=typeof b.afterSubmitForm==="function"?true:false;if(!b.imgpath){
                    b.imgpath=j.p.imgpath
                    }var c=j.p.id,h="ColTbl_"+c,f={
                    themodal:"colmod"+c,
                    modalhead:"colhd"+c,
                    modalcontent:"colcnt"+c,
                    scrollelm:h
                };if(a("#"+f.themodal).html()!=null){
                    if(k){
                        b.beforeShowForm(a("#"+h))
                        }viewModal("#"+f.themodal,{
                        gbox:"#gbox_"+c,
                        jqm:b.jqModal,
                        jqM:false,
                        modal:b.modal
                        });if(d){
                        b.afterShowForm(a("#"+h))
                        }
                    }else{
                    var l=isNaN(b.dataheight)?b.dataheight:b.dataheight+"px";var m="<div id='"+h+"' class='formdata' style='width:100%;overflow:auto;position:relative;height:"+l+";'>";m+="<table class='ColTable' cellspacing='1' cellpading='2' border='0'><tbody>";for(i=0;i<this.p.colNames.length;i++){
                        if(!j.p.colModel[i].hidedlg){
                            m+="<tr><td style='white-space: pre;'><input type='checkbox' style='margin-right:5px;' id='col_"+this.p.colModel[i].name+"' class='cbox' value='T' "+((this.p.colModel[i].hidden===false)?"checked":"")+"/><label for='col_"+this.p.colModel[i].name+"'>"+this.p.colNames[i]+((b.colnameview)?" ("+this.p.colModel[i].name+")":"")+"</label></td></tr>"
                            }
                        }m+="</tbody></table></div>";var g=!b.updateAfterCheck?"<a href='javascript:void(0)' id='dData' class='fm-button ui-state-default ui-corner-all'>"+b.bSubmit+"</a>":"",n="<a href='javascript:void(0)' id='eData' class='fm-button ui-state-default ui-corner-all'>"+b.bCancel+"</a>";m+="<table border='0' class='EditTable' id='"+h+"_2'><tbody><tr style='display:block;height:3px;'><td></td></tr><tr><td class='DataTD ui-widget-content'></td></tr><tr><td class='ColButton EditButton'>"+g+"&nbsp;"+n+"</td></tr></tbody></table>";b.gbox="#gbox_"+c;createModal(f,m,b,"#gview_"+j.p.id,a("#gview_"+j.p.id)[0]);if(b.saveicon[0]==true){
                        a("#dData","#"+h+"_2").addClass(b.saveicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+b.saveicon[2]+"'></span>")
                        }if(b.closeicon[0]==true){
                        a("#eData","#"+h+"_2").addClass(b.closeicon[1]=="right"?"fm-button-icon-right":"fm-button-icon-left").append("<span class='ui-icon "+b.closeicon[2]+"'></span>")
                        }if(!b.updateAfterCheck){
                        a("#dData","#"+h+"_2").click(function(p){
                            for(i=0;i<j.p.colModel.length;i++){
                                if(!j.p.colModel[i].hidedlg){
                                    var o=j.p.colModel[i].name.replace(".","\\.");if(a("#col_"+o,"#"+h).attr("checked")){
                                        a(j).showCol(j.p.colModel[i].name);a("#col_"+o,"#"+h).attr("defaultChecked",true)
                                        }else{
                                        a(j).hideCol(j.p.colModel[i].name);a("#col_"+o,"#"+h).attr("defaultChecked","")
                                        }
                                    }
                                }if(b.ShrinkToFit===true){
                                a(j).setGridWidth(j.grid.width-0.001,true)
                                }if(b.closeAfterSubmit){
                                hideModal("#"+f.themodal,{
                                    gb:"#gbox_"+c,
                                    jqm:b.jqModal,
                                    onClose:b.onClose
                                    })
                                }if(e){
                                b.afterSubmitForm(a("#"+h))
                                }return false
                            })
                        }else{
                        a(":input","#"+h).click(function(o){
                            var p=this.id.substr(4);if(p){
                                if(this.checked){
                                    a(j).showCol(p)
                                    }else{
                                    a(j).hideCol(p)
                                    }if(b.ShrinkToFit===true){
                                    a(j).setGridWidth(j.grid.width-0.001,true)
                                    }
                                }return this
                            })
                        }a("#eData","#"+h+"_2").click(function(o){
                        hideModal("#"+f.themodal,{
                            gb:"#gbox_"+c,
                            jqm:b.jqModal,
                            onClose:b.onClose
                            });return false
                        });a("#dData, #eData","#"+h+"_2").hover(function(){
                        a(this).addClass("ui-state-hover")
                        },function(){
                        a(this).removeClass("ui-state-hover")
                        });if(k){
                        b.beforeShowForm(a("#"+h))
                        }viewModal("#"+f.themodal,{
                        gbox:"#gbox_"+c,
                        jqm:b.jqModal,
                        jqM:true,
                        modal:b.modal
                        });if(d){
                        b.afterShowForm(a("#"+h))
                        }
                    }
                })
            }
        })
    })(jQuery);(function(a){
    a.fn.extend({
        getPostData:function(){
            var b=this[0];if(!b.grid){
                return
            }return b.p.postData
            },
        setPostData:function(b){
            var c=this[0];if(!c.grid){
                return
            }if(typeof(b)==="object"){
                c.p.postData=b
                }else{
                alert("Error: cannot add a non-object postData value. postData unchanged.")
                }
            },
        appendPostData:function(b){
            var c=this[0];if(!c.grid){
                return
            }if(typeof(b)==="object"){
                a.extend(c.p.postData,b)
                }else{
                alert("Error: cannot append a non-object postData value. postData unchanged.")
                }
            },
        setPostDataItem:function(b,c){
            var d=this[0];if(!d.grid){
                return
            }d.p.postData[b]=c
            },
        getPostDataItem:function(b){
            var c=this[0];if(!c.grid){
                return
            }return c.p.postData[b]
            },
        removePostDataItem:function(b){
            var c=this[0];if(!c.grid){
                return
            }delete c.p.postData[b]
        },
        getUserData:function(){
            var b=this[0];if(!b.grid){
                return
            }return b.p.userData
            },
        getUserDataItem:function(b){
            var c=this[0];if(!c.grid){
                return
            }return c.p.userData[b]
            }
        })
    })(jQuery);function tableToGrid(a,b){
    $(a).each(function(){
        if(this.grid){
            return
        }$(this).width("99%");var n=$(this).width();var p=$("input[type=checkbox]:first",$(this));var h=$("input[type=radio]:first",$(this));var d=p.length>0;var g=!d&&h.length>0;var j=d||g;var i=p.attr("name")||h.attr("name");var l=[];var o=[];$("th",$(this)).each(function(){
            if(l.length==0&&j){
                l.push({
                    name:"__selection__",
                    index:"__selection__",
                    width:0,
                    hidden:true
                });o.push("__selection__")
                }else{
                l.push({
                    name:$(this).attr("id")||$(this).html(),
                    index:$(this).attr("id")||$(this).html(),
                    width:$(this).width()||150
                    });o.push($(this).html())
                }
            });var f=[];var e=[];var m=[];$("tbody > tr",$(this)).each(function(){
            var r={};var q=0;$("td",$(this)).each(function(){
                if(q==0&&j){
                    var s=$("input",$(this));var t=s.attr("value");e.push(t||f.length);if(s.attr("checked")){
                        m.push(t)
                        }r[l[q].name]=s.attr("value")
                    }else{
                    r[l[q].name]=$(this).html()
                    }q++
            });if(q>0){
                f.push(r)
                }
            });$(this).empty();$(this).addClass("scroll");$(this).jqGrid($.extend({
            datatype:"local",
            width:n,
            colNames:o,
            colModel:l,
            multiselect:d
        },b||{}));for(var k=0;k<f.length;k++){
            var c=null;if(e.length>0){
                c=e[k];if(c&&c.replace){
                    c=encodeURIComponent(c).replace(/[.\-%]/g,"_")
                    }
                }if(c==null){
                c=k+1
                }$(this).addRowData(c,f[k])
            }for(var k=0;k<m.length;k++){
            $(this).setSelection(m[k])
            }
        })
    };