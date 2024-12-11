/*! For license information please see va_bulkfeaturemanager.import.bundle.js.LICENSE.txt */
(()=>{"use strict";class t{show(){this.progressModal.modal("show")}hide(){this.progressModal.modal("hide")}updateProgress(t,e){const s=parseInt(t,10),o=parseInt(e,10),r=this.progressBar,i=s/o*100;r.css("width",`${i}%`),r.find("> span").text(`${s}/${o}`)}updateProgressLabel(t){this.progressLabel.text(t)}setImportingProgressLabel(){this.updateProgressLabel(this.progressModal.find(".modal-body").data("importing-label"))}setImportedProgressLabel(){this.updateProgressLabel(this.progressModal.find(".modal-body").data("imported-label"))}showInfoMessages(t){this.showMessages(this.infoMessageBlock,t)}showWarningMessages(t){this.showMessages(this.warningMessageBlock,t)}showErrorMessages(t){this.showMessages(this.errorMessageBlock,t)}showSuccessMessage(){this.successMessageBlock.removeClass("d-none")}showPostLimitMessage(t){this.postLimitMessage.find("#post_limit_value").text(t),this.postLimitMessage.removeClass("d-none")}showMessages(t,e){let s=!1;Object.values(e).forEach((e=>{s=!0;const o=$("<div>");o.text(e),o.addClass("message"),t.append(o)})),s&&t.removeClass("d-none")}showContinueImportButton(){this.continueImportButton.removeClass("d-none")}hideContinueImportButton(){this.continueImportButton.addClass("d-none")}showAbortImportButton(){this.abortImportButton.removeClass("d-none")}hideAbortImportButton(){this.abortImportButton.addClass("d-none")}showCloseModalButton(){this.closeModalButton.removeClass("d-none")}hideCloseModalButton(){this.closeModalButton.addClass("d-none")}clearWarningMessages(){this.warningMessageBlock.addClass("d-none").find(".message").remove()}reset(){this.updateProgress(0,0),this.updateProgressLabel(this.progressLabel.attr("default-value")),this.continueImportButton.addClass("d-none"),this.abortImportButton.addClass("d-none"),this.closeModalButton.addClass("d-none"),this.successMessageBlock.addClass("d-none"),this.infoMessageBlock.addClass("d-none").find(".message").remove(),this.errorMessageBlock.addClass("d-none").find(".message").remove(),this.postLimitMessage.addClass("d-none"),this.clearWarningMessages()}get progressModal(){return $("#import_progress_modal")}get progressBar(){return this.progressModal.find(".progress-bar")}get infoMessageBlock(){return $(".js-import-info")}get errorMessageBlock(){return $(".js-import-errors")}get warningMessageBlock(){return $(".js-import-warnings")}get successMessageBlock(){return $(".js-import-success")}get postLimitMessage(){return $(".js-post-limit-warning")}get continueImportButton(){return $(".js-continue-import")}get abortImportButton(){return $(".js-abort-import")}get closeModalButton(){return $(".js-close-modal")}get progressLabel(){return $("#import_progress_bar").find(".progress-details-text")}}class e{constructor(){this.targetExecutionTime=5e3,this.maxAcceleration=4,this.minBatchSize=5,this.maxBatchSize=100,this.importStartTime=0,this.actualExecutionTime=0}markImportStart(){this.importStartTime=(new Date).getTime()}markImportEnd(){this.actualExecutionTime=(new Date).getTime()-this.importStartTime}calculateAcceleration(){return Math.min(this.maxAcceleration,this.targetExecutionTime/this.actualExecutionTime)}calculateBatchSize(t,e=0){if(!this.importStartTime)throw new Error("Import start is not marked.");if(!this.actualExecutionTime)throw new Error("Import end is not marked.");const s=[this.maxBatchSize,Math.max(this.minBatchSize,Math.floor(t*this.calculateAcceleration()))];return e>0&&s.push(e),Math.min(...s)}}class s{constructor(){this.postSizeLimitThreshold=.9}isReachingPostSizeLimit(t,e){return e>=t*this.postSizeLimitThreshold}getRequiredPostSizeInMegabytes(t){return parseInt(t/1048576,10)}}var o=Object.defineProperty,r=Object.getOwnPropertySymbols,i=Object.prototype.hasOwnProperty,a=Object.prototype.propertyIsEnumerable,n=(t,e,s)=>e in t?o(t,e,{enumerable:!0,configurable:!0,writable:!0,value:s}):t[e]=s,l=(t,e)=>{for(var s in e||(e={}))i.call(e,s)&&n(t,s,e[s]);if(r)for(var s of r(e))a.call(e,s)&&n(t,s,e[s]);return t};class h{constructor(){this.configuration={},this.importConfiguration={},this.progressModal=new t,this.batchSizeCalculator=new e,this.postSizeChecker=new s,this.importUrl="",this.totalRowsCount=0,this.hasWarnings=!1,this.hasErrors=!1,this.importCancelRequested=!1,this.defaultBatchSize=5}import(t,e){this.mergeConfiguration(e),this.importUrl=t,this.totalRowsCount=0,this.hasWarnings=!1,this.hasErrors=!1,this.progressModal.reset(),this.progressModal.show(),this.ajaxImport(0,this.defaultBatchSize)}ajaxImport(t,e,s=!0,o={}){this.mergeConfiguration({offset:t,limit:e,validateOnly:s?1:0,crossStepsVars:JSON.stringify(o)}),this.onImportStart(),$.post({url:this.importUrl,dataType:"json",data:this.configuration,success:o=>{if(this.importCancelRequested)return this.cancelImport(),!1;const r=o.errors&&o.errors.length,i=o.warnings&&o.warnings.length,a=o.notices&&o.notices.length;if(void 0!==o.totalCount&&o.totalCount&&(this.totalRowsCount=o.totalCount),this.progressModal.updateProgress(o.doneCount,this.totalRowsCount),s||this.progressModal.setImportingProgressLabel(),!s&&a&&this.progressModal.showInfoMessages(o.notices),r){if(this.hasErrors=!0,this.progressModal.showErrorMessages(o.errors),!s)return this.onImportStop(),!1}else i&&(this.hasWarnings=!0,this.progressModal.showWarningMessages(o.warnings));if(!o.isFinished){this.batchSizeCalculator.markImportEnd();const r=t+e,i=this.batchSizeCalculator.calculateBatchSize(e,this.totalRowsCount);return this.postSizeChecker.isReachingPostSizeLimit(o.postSizeLimit,o.nextPostSize)&&this.progressModal.showPostLimitMessage(this.postSizeChecker.getRequiredPostSizeInMegabytes(o.nextPostSize)),this.ajaxImport(r,i,s,o.crossStepsVariables)}return s?this.hasErrors?(this.onImportStop(),!1):this.hasWarnings?(this.progressModal.showContinueImportButton(),this.onImportStop(),!1):(this.progressModal.updateProgress(this.totalRowsCount,this.totalRowsCount),this.ajaxImport(0,this.defaultBatchSize,!1)):this.onImportFinish()},error:(t,e)=>{let s=e;"error"===s&&(s=`Error ${t.status} status: ${t.responseJSON.detail}`),"parsererror"===s&&(s="Technical error: Unexpected response returned by server. Import stopped."),this.onImportStop(),this.progressModal.showErrorMessages([s])}})}continueImport(){if(!this.configuration)throw new Error("Missing import configuration. Make sure the import had started before continuing.");this.progressModal.hideContinueImportButton(),this.progressModal.hideCloseModalButton(),this.progressModal.clearWarningMessages(),this.ajaxImport(0,this.defaultBatchSize,!1)}set configuration(t){this.importConfiguration=t}get configuration(){return this.importConfiguration}set progressModal(t){this.progressModal=t}get progressModal(){return this.progressModal}requestCancelImport(){this.importCancelRequested=!0}mergeConfiguration(t){this.importConfiguration=l(l({},this.importConfiguration),t)}cancelImport(){this.progressModal.hide(),this.importCancelRequested=!1}onImportStop(){this.progressModal.showCloseModalButton(),this.progressModal.hideAbortImportButton()}onImportFinish(){this.onImportStop(),this.progressModal.showSuccessMessage(),this.progressModal.setImportedProgressLabel(),this.progressModal.updateProgress(this.totalRowsCount,this.totalRowsCount)}onImportStart(){this.batchSizeCalculator.markImportStart(),this.progressModal.showAbortImportButton()}}class p{constructor(){$(document).on("click",".js-change-import-file-btn",(()=>this.changeImportFileHandler())),this.toggleSelectedFile()}toggleSelectedFile(){let t=$("#csv").val();t.length>0&&(this.showImportFileAlert(t),this.hideFileUploadBlock())}uploadFile(){this.hideImportFileError(),this.disableProcessImportButton(),$.growl.notice({title:"Please wait..",message:"File is sending..."});const t=$("#feature_file"),e=t.prop("files")[0];if(t.data("max-file-upload-size")<e.size)return void this.showImportFileError(e.name,e.size,"File is too large");const s=new FormData;return s.append("file",e),$.ajax({type:"POST",url:$(".js-import-form").data("file-upload-url"),data:s,cache:!1,contentType:!1,processData:!1}).then((t=>{if(t.error)return void this.showImportFileError(e.name,e.size,t.error);let s=t.file.name;$(".js-import-file-input").val(s),$.growl({title:"Please wait..",message:"File successfully upload!"}),this.showImportFileAlert(s),this.hideFileUploadBlock(),this.enableProcessImportButton()}))}hideImportFileError(){$(".js-import-file-error").addClass("d-none")}showImportFileError(t,e,s){const o=$(".js-import-file-error"),r=t+" ("+e+" kb)";o.find(".js-file-data").text(r),o.find(".js-error-message").text(s),o.removeClass("d-none")}showImportFileAlert(t){$(".js-import-file-alert").removeClass("d-none"),$(".js-import-file").text(t)}hideFileUploadBlock(){$(".js-file-upload-input-wrapper").addClass("d-none")}changeImportFileHandler(){this.hideImportFileAlert(),this.showFileUploadBlock()}showFileUploadBlock(){$(".js-file-upload-input-wrapper").removeClass("d-none")}hideImportFileAlert(){$(".js-import-file-alert").addClass("d-none")}disableProcessImportButton(){$(".js-process-import").attr("disabled",!0)}enableProcessImportButton(){$(".js-process-import").removeAttr("disabled")}}const d=window.$;function c(t){d(".choose-import-text").each(((e,s)=>{s.classList.add("d-none"),parseInt(d(t)[0].value)===e&&s.classList.remove("d-none")}))}c(".js-choose-import-type"),d((()=>{Object.defineProperty(h.prototype,"progressModal",{get(){return this._progressModal},set(t){this._progressModal=t}});const t=new h;t._progressModal=t.progressModal;const e=new p;d(document).on("change",".js-choose-import-type",(()=>c(".js-choose-import-type"))),d(document).off("click",".js-process-import"),d(document).on("click",".js-process-import",(e=>function(e){e.preventDefault();let s={};d("#import-features-product-form").find("input[name=skip]:checked, select[name^=type_value], #csv, #iso_lang, #entity,#truncate, #match_ref, #regenerate, input[name=forceIDs]:checked, #sendemail,#separator, #multiple_value_separator, #price_tin").each(((t,e)=>{s[d(e).attr("name")]=d(e).val()})),s.import_type=parseInt(d("#import_type")[0].value),t.import(d(e.currentTarget).data("import_url"),s)}(e))),d(document).on("click",".js-abort-import",(()=>t.requestCancelImport())),d(document).on("click",".js-close-modal",(()=>{t.progressModal.hide(),location.reload()})),d(document).on("click","js-continue-import",(()=>t.continueImport())),d(document).on("change",".js-import-file",(()=>{e.uploadFile()}))}))})();