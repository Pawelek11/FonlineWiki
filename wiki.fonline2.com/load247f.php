function isCompatible(ua){return!!((function(){'use strict';return!this&&Function.prototype.bind&&window.JSON;}())&&'querySelector'in document&&'localStorage'in window&&'addEventListener'in window&&!ua.match(/MSIE 10|NetFront|Opera Mini|S40OviBrowser|MeeGo|Android.+Glass|^Mozilla\/5\.0 .+ Gecko\/$|googleweblight|PLAYSTATION|PlayStation/));}if(!isCompatible(navigator.userAgent)){document.documentElement.className=document.documentElement.className.replace(/(^|\s)client-js(\s|$)/,'$1client-nojs$2');while(window.NORLQ&&NORLQ[0]){NORLQ.shift()();}NORLQ={push:function(fn){fn();}};RLQ={push:function(){}};}else{if(window.performance&&performance.mark){performance.mark('mwStartup');}(function(){'use strict';var mw,log,con=window.console;function logError(topic,data){var msg,e=data.exception;if(con.log){msg=(e?'Exception':'Error')+' in '+data.source+(data.module?' in module '+data.module:'')+(e?':':'.');con.log(msg);if(e&&con.warn){con.warn(e);}}}function Map(){this.values=Object.create(null);}
Map.prototype={constructor:Map,get:function(selection,fallback){var results,i;fallback=arguments.length>1?fallback:null;if(Array.isArray(selection)){results={};for(i=0;i<selection.length;i++){if(typeof selection[i]==='string'){results[selection[i]]=selection[i]in this.values?this.values[selection[i]]:fallback;}}return results;}if(typeof selection==='string'){return selection in this.values?this.values[selection]:fallback;}if(selection===undefined){results={};for(i in this.values){results[i]=this.values[i];}return results;}return fallback;},set:function(selection,value){if(arguments.length>1){if(typeof selection==='string'){this.values[selection]=value;return!0;}}else if(typeof selection==='object'){for(var s in selection){this.values[s]=selection[s];}return!0;}return!1;},exists:function(selection){return typeof selection==='string'&&selection in this.values;}};log=function(){};log.warn=con.warn?Function.prototype.bind.call(con.warn,con):function(){};mw={now:function(){var perf=
window.performance,navStart=perf&&perf.timing&&perf.timing.navigationStart;mw.now=navStart&&perf.now?function(){return navStart+perf.now();}:Date.now;return mw.now();},trackQueue:[],track:function(topic,data){mw.trackQueue.push({topic:topic,data:data});},trackError:function(topic,data){mw.track(topic,data);logError(topic,data);},Map:Map,config:new Map(),messages:new Map(),templates:new Map(),log:log};window.mw=window.mediaWiki=mw;}());(function(){'use strict';var StringSet,store,loader,hasOwn=Object.hasOwnProperty;function defineFallbacks(){StringSet=window.Set||function(){var set=Object.create(null);return{add:function(value){set[value]=!0;},has:function(value){return value in set;}};};}defineFallbacks();function fnv132(str){var hash=0x811C9DC5,i=0;for(;i<str.length;i++){hash+=(hash<<1)+(hash<<4)+(hash<<7)+(hash<<8)+(hash<<24);hash^=str.charCodeAt(i);}hash=(hash>>>0).toString(36).slice(0,5);while(hash.length<5){hash='0'+hash;}return hash;}var isES6Supported=typeof Promise===
'function'&&Promise.prototype.finally&&/./g.flags==='g'&&(function(){try{new Function('var \ud800\udec0;');return!0;}catch(e){return!1;}}());var registry=Object.create(null),sources=Object.create(null),handlingPendingRequests=!1,pendingRequests=[],queue=[],jobs=[],willPropagate=!1,errorModules=[],baseModules=["jquery","mediawiki.base"],marker=document.querySelector('meta[name="ResourceLoaderDynamicStyles"]'),lastCssBuffer,rAF=window.requestAnimationFrame||setTimeout;function newStyleTag(text,nextNode){var el=document.createElement('style');el.appendChild(document.createTextNode(text));if(nextNode&&nextNode.parentNode){nextNode.parentNode.insertBefore(el,nextNode);}else{document.head.appendChild(el);}return el;}function flushCssBuffer(cssBuffer){if(cssBuffer===lastCssBuffer){lastCssBuffer=null;}newStyleTag(cssBuffer.cssText,marker);for(var i=0;i<cssBuffer.callbacks.length;i++){cssBuffer.callbacks[i]();}}function addEmbeddedCSS(cssText,callback){if(!lastCssBuffer||cssText.
slice(0,7)==='@import'){lastCssBuffer={cssText:'',callbacks:[]};rAF(flushCssBuffer.bind(null,lastCssBuffer));}lastCssBuffer.cssText+='\n'+cssText;lastCssBuffer.callbacks.push(callback);}function getCombinedVersion(modules){var hashes=modules.reduce(function(result,module){return result+registry[module].version;},'');return fnv132(hashes);}function allReady(modules){for(var i=0;i<modules.length;i++){if(loader.getState(modules[i])!=='ready'){return!1;}}return!0;}function allWithImplicitReady(module){return allReady(registry[module].dependencies)&&(baseModules.indexOf(module)!==-1||allReady(baseModules));}function anyFailed(modules){for(var i=0;i<modules.length;i++){var state=loader.getState(modules[i]);if(state==='error'||state==='missing'){return modules[i];}}return!1;}function doPropagation(){var module,i,job,didPropagate=!0;while(didPropagate){didPropagate=!1;while(errorModules.length){var errorModule=errorModules.shift(),baseModuleError=baseModules.indexOf(errorModule)
!==-1;for(module in registry){if(registry[module].state!=='error'&&registry[module].state!=='missing'){if(baseModuleError&&baseModules.indexOf(module)===-1){registry[module].state='error';didPropagate=!0;}else if(registry[module].dependencies.indexOf(errorModule)!==-1){registry[module].state='error';errorModules.push(module);didPropagate=!0;}}}}for(module in registry){if(registry[module].state==='loaded'&&allWithImplicitReady(module)){execute(module);didPropagate=!0;}}for(i=0;i<jobs.length;i++){job=jobs[i];var failed=anyFailed(job.dependencies);if(failed!==!1||allReady(job.dependencies)){jobs.splice(i,1);i-=1;try{if(failed!==!1&&job.error){job.error(new Error('Failed dependency: '+failed),job.dependencies);}else if(failed===!1&&job.ready){job.ready();}}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'load-callback'});}didPropagate=!0;}}}willPropagate=!1;}function setAndPropagate(module,state){registry[module].state=state;if(state==='ready'){
store.add(module);}else if(state==='error'||state==='missing'){errorModules.push(module);}else if(state!=='loaded'){return;}if(willPropagate){return;}willPropagate=!0;mw.requestIdleCallback(doPropagation,{timeout:1});}function sortDependencies(module,resolved,unresolved){var e;if(!(module in registry)){e=new Error('Unknown module: '+module);e.name='DependencyError';throw e;}if(!isES6Supported&&registry[module].requiresES6){e=new Error('Module requires ES6 but ES6 is not supported: '+module);e.name='ES6Error';throw e;}if(typeof registry[module].skip==='string'){var skip=(new Function(registry[module].skip)());registry[module].skip=!!skip;if(skip){registry[module].dependencies=[];setAndPropagate(module,'ready');return;}}if(!unresolved){unresolved=new StringSet();}var deps=registry[module].dependencies;unresolved.add(module);for(var i=0;i<deps.length;i++){if(resolved.indexOf(deps[i])===-1){if(unresolved.has(deps[i])){e=new Error('Circular reference detected: '+module+' -> '+deps[i]);e.
name='DependencyError';throw e;}sortDependencies(deps[i],resolved,unresolved);}}resolved.push(module);}function resolve(modules){var resolved=baseModules.slice(),i=0;for(;i<modules.length;i++){sortDependencies(modules[i],resolved);}return resolved;}function resolveStubbornly(modules){var saved,resolved=baseModules.slice(),i=0;for(;i<modules.length;i++){saved=resolved.slice();try{sortDependencies(modules[i],resolved);}catch(err){resolved=saved;if(err.name==='ES6Error'){mw.log.warn('Skipped ES6-only module '+modules[i]);}else{mw.log.warn('Skipped unresolvable module '+modules[i]);if(modules[i]in registry){mw.trackError('resourceloader.exception',{exception:err,source:'resolve'});}}}}return resolved;}function resolveRelativePath(relativePath,basePath){var relParts=relativePath.match(/^((?:\.\.?\/)+)(.*)$/);if(!relParts){return null;}var baseDirParts=basePath.split('/');baseDirParts.pop();var prefixes=relParts[1].split('/');prefixes.pop();var prefix;while((prefix=prefixes.pop())!==
undefined){if(prefix==='..'){baseDirParts.pop();}}return(baseDirParts.length?baseDirParts.join('/')+'/':'')+relParts[2];}function makeRequireFunction(moduleObj,basePath){return function require(moduleName){var fileName=resolveRelativePath(moduleName,basePath);if(fileName===null){return loader.require(moduleName);}if(hasOwn.call(moduleObj.packageExports,fileName)){return moduleObj.packageExports[fileName];}var scriptFiles=moduleObj.script.files;if(!hasOwn.call(scriptFiles,fileName)){throw new Error('Cannot require undefined file '+fileName);}var result,fileContent=scriptFiles[fileName];if(typeof fileContent==='function'){var moduleParam={exports:{}};fileContent(makeRequireFunction(moduleObj,fileName),moduleParam);result=moduleParam.exports;}else{result=fileContent;}moduleObj.packageExports[fileName]=result;return result;};}function addScript(src,callback){var script=document.createElement('script');script.src=src;script.onload=script.onerror=function(){if(script.parentNode){script.
parentNode.removeChild(script);}if(callback){callback();callback=null;}};document.head.appendChild(script);}function queueModuleScript(src,moduleName,callback){pendingRequests.push(function(){if(moduleName!=='jquery'){window.require=loader.require;window.module=registry[moduleName].module;}addScript(src,function(){delete window.module;callback();if(pendingRequests[0]){pendingRequests.shift()();}else{handlingPendingRequests=!1;}});});if(!handlingPendingRequests&&pendingRequests[0]){handlingPendingRequests=!0;pendingRequests.shift()();}}function addLink(url,media,nextNode){var el=document.createElement('link');el.rel='stylesheet';if(media){el.media=media;}el.href=url;if(nextNode&&nextNode.parentNode){nextNode.parentNode.insertBefore(el,nextNode);}else{document.head.appendChild(el);}}function domEval(code){var script=document.createElement('script');if(mw.config.get('wgCSPNonce')!==!1){script.nonce=mw.config.get('wgCSPNonce');}script.text=code;document.head.appendChild(script);
script.parentNode.removeChild(script);}function enqueue(dependencies,ready,error){if(allReady(dependencies)){if(ready){ready();}return;}var failed=anyFailed(dependencies);if(failed!==!1){if(error){error(new Error('Dependency '+failed+' failed to load'),dependencies);}return;}if(ready||error){jobs.push({dependencies:dependencies.filter(function(module){var state=registry[module].state;return state==='registered'||state==='loaded'||state==='loading'||state==='executing';}),ready:ready,error:error});}dependencies.forEach(function(module){if(registry[module].state==='registered'&&queue.indexOf(module)===-1){queue.push(module);}});loader.work();}function execute(module){var key,value,media,i,siteDeps,siteDepErr,cssPending=0;if(registry[module].state!=='loaded'){throw new Error('Module in state "'+registry[module].state+'" may not execute: '+module);}registry[module].state='executing';var runScript=function(){var script=registry[module].script;var markModuleReady=function(){
setAndPropagate(module,'ready');};var nestedAddScript=function(arr,j){if(j>=arr.length){markModuleReady();return;}queueModuleScript(arr[j],module,function(){nestedAddScript(arr,j+1);});};try{if(Array.isArray(script)){nestedAddScript(script,0);}else if(typeof script==='function'){if(module==='jquery'){script();}else{script(window.$,window.$,loader.require,registry[module].module);}markModuleReady();}else if(typeof script==='object'&&script!==null){var mainScript=script.files[script.main];if(typeof mainScript!=='function'){throw new Error('Main file in module '+module+' must be a function');}mainScript(makeRequireFunction(registry[module],script.main),registry[module].module);markModuleReady();}else if(typeof script==='string'){domEval(script);markModuleReady();}else{markModuleReady();}}catch(e){setAndPropagate(module,'error');mw.trackError('resourceloader.exception',{exception:e,module:module,source:'module-execute'});}};if(registry[module].messages){mw.messages.set(registry[module].
messages);}if(registry[module].templates){mw.templates.set(module,registry[module].templates);}var cssHandle=function(){cssPending++;return function(){cssPending--;if(cssPending===0){var runScriptCopy=runScript;runScript=undefined;runScriptCopy();}};};if(registry[module].style){for(key in registry[module].style){value=registry[module].style[key];media=undefined;if(key!=='url'&&key!=='css'){if(typeof value==='string'){addEmbeddedCSS(value,cssHandle());}else{media=key;key='bc-url';}}if(Array.isArray(value)){for(i=0;i<value.length;i++){if(key==='bc-url'){addLink(value[i],media,marker);}else if(key==='css'){addEmbeddedCSS(value[i],cssHandle());}}}else if(typeof value==='object'){for(media in value){var urls=value[media];for(i=0;i<urls.length;i++){addLink(urls[i],media,marker);}}}}}if(module==='user'){try{siteDeps=resolve(['site']);}catch(e){siteDepErr=e;runScript();}if(!siteDepErr){enqueue(siteDeps,runScript,runScript);}}else if(cssPending===0){runScript();}}function sortQuery(o){var key,
sorted={},a=[];for(key in o){a.push(key);}a.sort();for(key=0;key<a.length;key++){sorted[a[key]]=o[a[key]];}return sorted;}function buildModulesString(moduleMap){var p,prefix,str=[],list=[];function restore(suffix){return p+suffix;}for(prefix in moduleMap){p=prefix===''?'':prefix+'.';str.push(p+moduleMap[prefix].join(','));list.push.apply(list,moduleMap[prefix].map(restore));}return{str:str.join('|'),list:list};}function makeQueryString(params){return Object.keys(params).map(function(key){return encodeURIComponent(key)+'='+encodeURIComponent(params[key]);}).join('&');}function batchRequest(batch){if(!batch.length){return;}var b,group,i,sourceLoadScript,currReqBase,moduleMap,l;function doRequest(){var query=Object.create(currReqBase),packed=buildModulesString(moduleMap);query.modules=packed.str;query.version=getCombinedVersion(packed.list);query=sortQuery(query);addScript(sourceLoadScript+'?'+makeQueryString(query));}batch.sort();var reqBase={"lang":"en","skin":"vector"};var splits=
Object.create(null);for(b=0;b<batch.length;b++){var bSource=registry[batch[b]].source,bGroup=registry[batch[b]].group;if(!splits[bSource]){splits[bSource]=Object.create(null);}if(!splits[bSource][bGroup]){splits[bSource][bGroup]=[];}splits[bSource][bGroup].push(batch[b]);}for(var source in splits){sourceLoadScript=sources[source];for(group in splits[source]){var modules=splits[source][group];currReqBase=Object.create(reqBase);if(group===0&&mw.config.get('wgUserName')!==null){currReqBase.user=mw.config.get('wgUserName');}var currReqBaseLength=makeQueryString(currReqBase).length+23;l=currReqBaseLength;moduleMap=Object.create(null);var currReqModules=[];for(i=0;i<modules.length;i++){var lastDotIndex=modules[i].lastIndexOf('.'),prefix=modules[i].substr(0,lastDotIndex),suffix=modules[i].slice(lastDotIndex+1),bytesAdded=moduleMap[prefix]?suffix.length+3:modules[i].length+3;if(currReqModules.length&&l+bytesAdded>loader.maxQueryLength){doRequest();l=currReqBaseLength;moduleMap=Object.create(
null);currReqModules=[];}if(!moduleMap[prefix]){moduleMap[prefix]=[];}l+=bytesAdded;moduleMap[prefix].push(suffix);currReqModules.push(modules[i]);}if(currReqModules.length){doRequest();}}}}function asyncEval(implementations,cb){if(!implementations.length){return;}mw.requestIdleCallback(function(){try{domEval(implementations.join(';'));}catch(err){cb(err);}});}function getModuleKey(module){return module in registry?(module+'@'+registry[module].version):null;}function splitModuleKey(key){var index=key.indexOf('@');if(index===-1){return{name:key,version:''};}return{name:key.slice(0,index),version:key.slice(index+1)};}function registerOne(module,version,dependencies,group,source,skip){if(module in registry){throw new Error('module already registered: '+module);}version=String(version||'');var requiresES6=version.slice(-1)==='!';if(requiresES6){version=version.slice(0,-1);}registry[module]={module:{exports:{}},packageExports:{},version:version,requiresES6:requiresES6,dependencies:
dependencies||[],group:typeof group==='undefined'?null:group,source:typeof source==='string'?source:'local',state:'registered',skip:typeof skip==='string'?skip:null};}mw.loader=loader={moduleRegistry:registry,maxQueryLength:2000,addStyleTag:newStyleTag,enqueue:enqueue,resolve:resolve,work:function(){store.init();var q=queue.length,storedImplementations=[],storedNames=[],requestNames=[],batch=new StringSet();while(q--){var module=queue[q];if(loader.getState(module)==='registered'&&!batch.has(module)){registry[module].state='loading';batch.add(module);var implementation=store.get(module);if(implementation){storedImplementations.push(implementation);storedNames.push(module);}else{requestNames.push(module);}}}queue=[];asyncEval(storedImplementations,function(err){store.stats.failed++;store.clear();mw.trackError('resourceloader.exception',{exception:err,source:'store-eval'});var failed=storedNames.filter(function(name){return registry[name].state==='loading';});batchRequest(failed);});
batchRequest(requestNames);},addSource:function(ids){for(var id in ids){if(id in sources){throw new Error('source already registered: '+id);}sources[id]=ids[id];}},register:function(modules){if(typeof modules!=='object'){registerOne.apply(null,arguments);return;}function resolveIndex(dep){return typeof dep==='number'?modules[dep][0]:dep;}var i,j,deps;for(i=0;i<modules.length;i++){deps=modules[i][2];if(deps){for(j=0;j<deps.length;j++){deps[j]=resolveIndex(deps[j]);}}registerOne.apply(null,modules[i]);}},implement:function(module,script,style,messages,templates){var split=splitModuleKey(module),name=split.name,version=split.version;if(!(name in registry)){loader.register(name);}if(registry[name].script!==undefined){throw new Error('module already implemented: '+name);}if(version){registry[name].version=version;}registry[name].script=script||null;registry[name].style=style||null;registry[name].messages=messages||null;registry[name].templates=templates||null;if(registry[name].state!==
'error'&&registry[name].state!=='missing'){setAndPropagate(name,'loaded');}},load:function(modules,type){if(typeof modules==='string'&&/^(https?:)?\/?\//.test(modules)){if(type==='text/css'){addLink(modules);}else if(type==='text/javascript'||type===undefined){addScript(modules);}else{throw new Error('Invalid type '+type);}}else{modules=typeof modules==='string'?[modules]:modules;enqueue(resolveStubbornly(modules));}},state:function(states){for(var module in states){if(!(module in registry)){loader.register(module);}setAndPropagate(module,states[module]);}},getState:function(module){return module in registry?registry[module].state:null;},getModuleNames:function(){return Object.keys(registry);},require:function(moduleName){if(loader.getState(moduleName)!=='ready'){throw new Error('Module "'+moduleName+'" is not loaded');}return registry[moduleName].module.exports;}};var hasPendingWrites=!1;function flushWrites(){store.prune();while(store.queue.length){store.set(store.queue.shift());}
try{localStorage.removeItem(store.key);var data=JSON.stringify(store);localStorage.setItem(store.key,data);}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-update'});}hasPendingWrites=!1;}loader.store=store={enabled:null,items:{},queue:[],stats:{hits:0,misses:0,expired:0,failed:0},toJSON:function(){return{items:store.items,vary:store.vary,asOf:Math.ceil(Date.now()/1e7)};},key:"MediaWikiModuleStore:Brightside_wiki",vary:"vector:1:en",init:function(){if(this.enabled===null){this.enabled=!1;if(!1||/Firefox/.test(navigator.userAgent)){this.clear();}else{this.load();}}},load:function(){try{var raw=localStorage.getItem(this.key);this.enabled=!0;var data=JSON.parse(raw);if(data&&data.vary===this.vary&&data.items&&Date.now()<(data.asOf*1e7)+259e7){this.items=data.items;}}catch(e){}},get:function(module){if(this.enabled){var key=getModuleKey(module);if(key in this.items){this.stats.hits++;return this.items[key];}this.stats.misses++;}return!1
;},add:function(module){if(this.enabled){this.queue.push(module);this.requestUpdate();}},set:function(module){var args,encodedScript,descriptor=registry[module],key=getModuleKey(module);if(key in this.items||!descriptor||descriptor.state!=='ready'||!descriptor.version||descriptor.group===1||descriptor.group===0||[descriptor.script,descriptor.style,descriptor.messages,descriptor.templates].indexOf(undefined)!==-1){return;}try{if(typeof descriptor.script==='function'){encodedScript=String(descriptor.script);}else if(typeof descriptor.script==='object'&&descriptor.script&&!Array.isArray(descriptor.script)){encodedScript='{'+'main:'+JSON.stringify(descriptor.script.main)+','+'files:{'+Object.keys(descriptor.script.files).map(function(file){var value=descriptor.script.files[file];return JSON.stringify(file)+':'+(typeof value==='function'?value:JSON.stringify(value));}).join(',')+'}}';}else{encodedScript=JSON.stringify(descriptor.script);}args=[JSON.stringify(key),encodedScript,JSON.
stringify(descriptor.style),JSON.stringify(descriptor.messages),JSON.stringify(descriptor.templates)];}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-json'});return;}var src='mw.loader.implement('+args.join(',')+');';if(src.length>1e5){return;}this.items[key]=src;},prune:function(){for(var key in this.items){if(getModuleKey(key.slice(0,key.indexOf('@')))!==key){this.stats.expired++;delete this.items[key];}}},clear:function(){this.items={};try{localStorage.removeItem(this.key);}catch(e){}},requestUpdate:function(){if(!hasPendingWrites){hasPendingWrites=!0;setTimeout(function(){mw.requestIdleCallback(flushWrites);},2000);}}};}());mw.requestIdleCallbackInternal=function(callback){setTimeout(function(){var start=mw.now();callback({didTimeout:!1,timeRemaining:function(){return Math.max(0,50-(mw.now()-start));}});},1);};mw.requestIdleCallback=window.requestIdleCallback?window.requestIdleCallback.bind(window):mw.requestIdleCallbackInternal;(
function(){var queue;mw.loader.addSource({"local":"/load.php"});mw.loader.register([["site","mmgn5",[1]],["site.styles","7j293",[],2],["noscript","r22l1",[],3],["user","k1cuu",[],0],["user.styles","8fimp",[],0],["user.defaults","1ddbn"],["user.options","1hzgi",[5],1],["mediawiki.skinning.elements","banck"],["mediawiki.skinning.content","oi817"],["mediawiki.skinning.interface","jb5jf"],["jquery.makeCollapsible.styles","dm1ye"],["mediawiki.skinning.content.parsoid","15gt9"],["mediawiki.skinning.content.externallinks","6l04w"],["jquery","4dvbv"],["es6-polyfills","150sy",[],null,null,"return Array.prototype.find\u0026\u0026Array.prototype.findIndex\u0026\u0026Array.prototype.includes\u0026\u0026typeof Promise==='function'\u0026\u0026Promise.prototype.finally;"],["fetch-polyfill","5vxes",[14],null,null,"return typeof fetch==='function';"],["mediawiki.base","159y7",[13]],["jquery.client","fn93f"],["jquery.confirmable","11aay",[88]],["jquery.cookie","1smd3"],["jquery.highlightText","1tsxs",[
68]],["jquery.i18n","29w1w",[87]],["jquery.lengthLimit","1llrz",[59]],["jquery.makeCollapsible","astbu",[10]],["jquery.mw-jump","r425l"],["jquery.spinner","17j37",[26]],["jquery.spinner.styles","o62ui"],["jquery.suggestions","9e98z",[20]],["jquery.tablesorter","qji78",[29,89,68]],["jquery.tablesorter.styles","nu5sg"],["jquery.textSelection","152er",[17]],["jquery.throttle-debounce","xl0tk"],["jquery.ui","1p6iu"],["moment","d6rz2",[85,68]],["vue","1y3pm"],["vuex","c4upc",[14,34]],["wvui","bm4ga",[34]],["wvui-search","1n0td",[34]],["mediawiki.template","xae8l"],["mediawiki.template.mustache","nyt38",[38]],["mediawiki.apipretty","o6hd1"],["mediawiki.api","1gaiy",[63,88]],["mediawiki.content.json","1f0f4"],["mediawiki.confirmCloseWindow","86m8t"],["mediawiki.diff.styles","1yb3i"],["mediawiki.feedback","1mkju",[285,165]],["mediawiki.ForeignApi","191mv",[47]],["mediawiki.ForeignApi.core","bd8b3",[66,41,153]],["mediawiki.helplink","1xyfo"],["mediawiki.hlist","7nynt"],["mediawiki.htmlform",
"1xh7h",[22,68]],["mediawiki.htmlform.ooui","14rir",[157]],["mediawiki.htmlform.styles","n60o9"],["mediawiki.htmlform.ooui.styles","kkxv5"],["mediawiki.icon","j5ayk"],["mediawiki.inspect","obqgk",[59,68]],["mediawiki.notification","1htx6",[68,75]],["mediawiki.notification.convertmessagebox","3la3s",[56]],["mediawiki.notification.convertmessagebox.styles","wj24b"],["mediawiki.String","1rrm1"],["mediawiki.pulsatingdot","qol8q"],["mediawiki.searchSuggest","1lq7b",[27,41]],["mediawiki.storage","187em"],["mediawiki.Title","f4bbu",[59,68]],["mediawiki.toc","ckf9m",[72]],["mediawiki.toc.styles","b3wyt"],["mediawiki.Uri","sqmr8",[68]],["mediawiki.user","burcp",[41,72]],["mediawiki.util","1g8n3",[17]],["mediawiki.viewport","1vq57"],["mediawiki.checkboxtoggle","2yuhf"],["mediawiki.checkboxtoggle.styles","15kl9"],["mediawiki.cookie","1cxyw",[19]],["mediawiki.experiments","1ogti"],["mediawiki.editfont.styles","1cz1l"],["mediawiki.visibleTimeout","aconv"],["mediawiki.action.edit","1nmzj",[30,77,41,
74,133]],["mediawiki.action.edit.styles","1o85j"],["mediawiki.action.history","vgbiv",[23]],["mediawiki.action.history.styles","1ji9u"],["mediawiki.action.view.categoryPage.styles","1054m"],["mediawiki.action.view.redirect","19xk3",[17]],["mediawiki.action.view.redirectPage","15q17"],["mediawiki.action.edit.editWarning","1gdkg",[30,43,88]],["mediawiki.action.styles","64lwx"],["mediawiki.language","1l08f",[86]],["mediawiki.cldr","erqtv",[87]],["mediawiki.libs.pluralruleparser","pvwvv"],["mediawiki.jqueryMsg","m0wqk",[59,85,68,6]],["mediawiki.language.months","1mcng",[85]],["mediawiki.language.names","1bhbd",[85]],["mediawiki.language.specialCharacters","tqbmd",[85]],["mediawiki.libs.jpegmeta","c4xwo"],["mediawiki.page.gallery.styles","13e1k"],["mediawiki.page.ready","12ezf",[41]],["mediawiki.page.watch.ajax","6qhk0",[41]],["mediawiki.rcfilters.filters.base.styles","uslqu"],["mediawiki.rcfilters.filters.dm","18c4y",[66,67,153]],["mediawiki.rcfilters.filters.ui","e24aw",[23,97,128,166,173
,175,176,177,179,180]],["mediawiki.interface.helpers.styles","1m6am"],["mediawiki.special","113mp"],["mediawiki.special.apisandbox","jvv5z",[23,66,148,134,156,171,176]],["mediawiki.special.block","pg3o5",[50,131,147,138,148,145,171,173]],["mediawiki.misc-authed-ooui","hbxyk",[51,128,133]],["mediawiki.misc-authed-curate","18ydi",[18,25,41]],["mediawiki.special.changeslist","195oo"],["mediawiki.special.changeslist.watchlistexpiry","1jn93",[100]],["mediawiki.special.changeslist.legend","5o2l3"],["mediawiki.special.changeslist.legend.js","ntrpi",[23,72]],["mediawiki.special.contributions","wcllz",[23,88,131,156]],["mediawiki.special.import.styles.ooui","or2kd"],["mediawiki.special.preferences.ooui","178ed",[43,74,57,62,138,133]],["mediawiki.special.preferences.styles.ooui","otadd"],["mediawiki.special.recentchanges","13ytr",[128]],["mediawiki.special.revisionDelete","1a7mj",[22]],["mediawiki.special.search","1cmha",[149]],["mediawiki.special.search.commonsInterwikiWidget","1s9x8",[66,41]],
["mediawiki.special.search.interwikiwidget.styles","14p79"],["mediawiki.special.search.styles","1vcge"],["mediawiki.special.userlogin.common.styles","no33f"],["mediawiki.legacy.shared","11jmb"],["mediawiki.ui","ex344"],["mediawiki.ui.checkbox","1rjmw"],["mediawiki.ui.radio","13bcd"],["mediawiki.ui.anchor","w5in5"],["mediawiki.ui.button","ks7r4"],["mediawiki.ui.input","cj4cj"],["mediawiki.ui.icon","17ml3"],["mediawiki.widgets","1s7ga",[41,129,160,170]],["mediawiki.widgets.styles","rqacs"],["mediawiki.widgets.AbandonEditDialog","1abjz",[165]],["mediawiki.widgets.DateInputWidget","19nc2",[132,33,160,181]],["mediawiki.widgets.DateInputWidget.styles","17emz"],["mediawiki.widgets.visibleLengthLimit","hmfi9",[22,157]],["mediawiki.widgets.datetime","blwau",[68,157,180,181]],["mediawiki.widgets.expiry","19dtp",[134,33,160]],["mediawiki.widgets.CheckMatrixWidget","ohle3",[157]],["mediawiki.widgets.CategoryMultiselectWidget","slkpi",[46,160]],["mediawiki.widgets.SelectWithInputWidget","oe83m",[
139,160]],["mediawiki.widgets.SelectWithInputWidget.styles","1fufa"],["mediawiki.widgets.SizeFilterWidget","sawvf",[141,160]],["mediawiki.widgets.SizeFilterWidget.styles","15b9u"],["mediawiki.widgets.MediaSearch","serev",[46,160]],["mediawiki.widgets.Table","yxx6f",[160]],["mediawiki.widgets.TagMultiselectWidget","syz4w",[160]],["mediawiki.widgets.UserInputWidget","1oqp3",[41,160]],["mediawiki.widgets.UsersMultiselectWidget","1iec8",[41,160]],["mediawiki.widgets.NamespacesMultiselectWidget","1nuht",[160]],["mediawiki.widgets.TitlesMultiselectWidget","2tq85",[128]],["mediawiki.widgets.SearchInputWidget","1ri9j",[61,128,176]],["mediawiki.widgets.SearchInputWidget.styles","68its"],["mediawiki.watchstar.widgets","xg53g",[156]],["mediawiki.deflate","gu4pi"],["oojs","1ws4u"],["mediawiki.router","1f8qs",[155]],["oojs-router","1lb9j",[153]],["oojs-ui","yfxca",[163,160,165]],["oojs-ui-core","19tqj",[85,153,159,158,167]],["oojs-ui-core.styles","1q0ro"],["oojs-ui-core.icons","1kka4"],[
"oojs-ui-widgets","y9btg",[157,162]],["oojs-ui-widgets.styles","7zmm6"],["oojs-ui-widgets.icons","38pw1"],["oojs-ui-toolbars","177js",[157,164]],["oojs-ui-toolbars.icons","h7gzh"],["oojs-ui-windows","35max",[157,166]],["oojs-ui-windows.icons","10bin"],["oojs-ui.styles.indicators","98jn5"],["oojs-ui.styles.icons-accessibility","uptj3"],["oojs-ui.styles.icons-alerts","uko8f"],["oojs-ui.styles.icons-content","nwr96"],["oojs-ui.styles.icons-editing-advanced","12352"],["oojs-ui.styles.icons-editing-citation","12lci"],["oojs-ui.styles.icons-editing-core","50ldg"],["oojs-ui.styles.icons-editing-list","5iylu"],["oojs-ui.styles.icons-editing-styling","7it9b"],["oojs-ui.styles.icons-interactions","1npk0"],["oojs-ui.styles.icons-layout","3fjve"],["oojs-ui.styles.icons-location","m5nbe"],["oojs-ui.styles.icons-media","1gjtc"],["oojs-ui.styles.icons-moderation","1o1jw"],["oojs-ui.styles.icons-movement","1r3hf"],["oojs-ui.styles.icons-user","1m8s8"],["oojs-ui.styles.icons-wikimedia","hqv8j"],[
"skins.vector.styles.legacy","118g9"],["skins.vector.styles","1j83x"],["skins.vector.icons","1lsxm"],["ext.embedVideo","6xuy5"],["ext.embedVideo-evl","quz0f",[41]],["ext.embedVideo.styles","rpcea"],["socket.io","is39l"],["dompurify","1q6qs"],["color-picker","1qvmf"],["unicodejs","v19tp"],["papaparse","17t4y"],["rangefix","f32vh"],["spark-md5","11tzz"],["ext.visualEditor.supportCheck","13m8w",[],4],["ext.visualEditor.sanitize","13ex9",[191,211],4],["ext.visualEditor.progressBarWidget","n1l81",[],4],["ext.visualEditor.tempWikitextEditorWidget","gu744",[74,67],4],["ext.visualEditor.targetLoader","1hgg8",[210,208,30,66,62,67],4],["ext.visualEditor.mobileArticleTarget","pr85o",[214,219],4],["ext.visualEditor.collabTarget","kvj48",[212,218,74,128,176,177],4],["ext.visualEditor.collabTarget.mobile","zx9ne",[203,219,223],4],["ext.visualEditor.collabTarget.init","1z06m",[197,128,156],4],["ext.visualEditor.collabTarget.init.styles","xc7ez"],["ext.visualEditor.ve","dkzcj",[],4],[
"ext.visualEditor.track","1gi8o",[207],4],["ext.visualEditor.core.utils","dp9wo",[208,156],4],["ext.visualEditor.core.utils.parsing","l3da8",[207],4],["ext.visualEditor.base","2eo05",[209,210,193],4],["ext.visualEditor.mediawiki","1dhid",[211,201,28,282],4],["ext.visualEditor.mwsave","9uzbv",[222,22,25,44,176],4],["ext.visualEditor.articleTarget","1g8o6",[223,213,130],4],["ext.visualEditor.data","1cstn",[212]],["ext.visualEditor.core","1hy5d",[198,197,17,194,195,196],4],["ext.visualEditor.commentAnnotation","1gawd",[216],4],["ext.visualEditor.rebase","2c1dk",[192,232,217,182,190],4],["ext.visualEditor.core.mobile","oq4mx",[216],4],["ext.visualEditor.welcome","1i2s5",[156],4],["ext.visualEditor.switching","13mlq",[41,156,168,171,173],4],["ext.visualEditor.mwcore","rkmb6",[233,212,221,220,99,60,11,128],4],["ext.visualEditor.mwextensions","yfxca",[215,244,237,239,224,241,226,238,227,229],4],["ext.visualEditor.mwformatting","pqk1y",[222],4],["ext.visualEditor.mwimage.core","hy3nu",[222],4]
,["ext.visualEditor.mwimage","1wa72",[225,142,33,179,183],4],["ext.visualEditor.mwlink","1xbxv",[222],4],["ext.visualEditor.mwmeta","136xi",[227,82],4],["ext.visualEditor.mwtransclusion","1mhu3",[222,145],4],["treeDiffer","1c337"],["diffMatchPatch","qnj4i"],["ext.visualEditor.checkList","1d0dh",[216],4],["ext.visualEditor.diffing","1mucs",[231,216,230],4],["ext.visualEditor.diffPage.init.styles","z3r1s"],["ext.visualEditor.diffLoader","1w40j",[201],4],["ext.visualEditor.diffPage.init","x0f9t",[235,156,168,171],4],["ext.visualEditor.language","1dmyu",[216,282,90],4],["ext.visualEditor.mwlanguage","1pske",[216],4],["ext.visualEditor.mwalienextension","1aef6",[222],4],["ext.visualEditor.mwwikitext","di6l0",[227,74],4],["ext.visualEditor.mwgallery","1iqd2",[222,93,142,179],4],["ext.visualEditor.mwsignature","dtrv7",[229],4],["ext.visualEditor.experimental","yfxca",[],4],["ext.visualEditor.icons","yfxca",[245,246,169,170,171,173,174,175,176,177,180,181,182,167],4],[
"ext.visualEditor.moduleIcons","1y8es"],["ext.visualEditor.moduleIndicators","g6fkk"],["ext.betaFeatures","sxhvi",[17,157]],["ext.betaFeatures.styles","vu1t4"],["mobile.pagelist.styles","1dhyu"],["mobile.pagesummary.styles","e83mq"],["mobile.placeholder.images","1fsqk"],["mobile.userpage.styles","1y1na"],["mobile.startup.images","9urrk"],["mobile.init.styles","1j8e2"],["mobile.init","77u9t",[66,258]],["mobile.ooui.icons","sp38u"],["mobile.user.icons","1tfu9"],["mobile.startup","168fx",[31,95,154,62,39,125,127,67,256,249,250,251,253]],["mobile.editor.overlay","1s4cu",[43,74,56,126,130,260,258,257,156,173]],["mobile.editor.images","15uvp"],["mobile.talk.overlays","19d30",[124,259]],["mobile.mediaViewer","r95lw",[258]],["mobile.languages.structured","p4hsp",[258]],["mobile.site","yfxca",[0]],["mobile.site.styles","yfxca",[1]],["mobile.special.styles","3xkfx"],["mobile.special.watchlist.scripts","5nlbb",[258]],["mobile.special.mobileoptions.styles","1fhq9"],[
"mobile.special.mobileoptions.scripts","74bre",[258]],["mobile.special.nearby.styles","1jc6g"],["mobile.special.userlogin.scripts","mw9xg"],["mobile.special.nearby.scripts","9dpn0",[66,270,258]],["mobile.special.history.styles","fprtv"],["mobile.special.pagefeed.styles","dtop7"],["mobile.special.mobilediff.images","1w2hx"],["mobile.special.mobilediff.styles","tqhi1"],["ext.RevisionSlider.dialogImages","127sq"],["ext.confirmEdit.visualEditor","1ka14",[284]],["ext.confirmEdit.reCaptchaNoCaptcha.visualEditor","7qi0e"],["skins.timeless","19kyv"],["skins.timeless.js","tvvtt"],["jquery.uls.data","14rdf"],["mobile.editor.ve","16k4y",[202,259]],["ext.confirmEdit.CaptchaInputWidget","1uoji",[157]],["mediawiki.messagePoster","1wtgm",[46]]]);mw.config.set(window.RLCONF||{});mw.loader.state(window.RLSTATE||{});mw.loader.load(window.RLPAGEMODULES||[]);queue=window.RLQ||[];RLQ=[];RLQ.push=function(fn){if(typeof fn==='function'){fn();}else{RLQ[RLQ.length]=fn;}};while(queue[0]){RLQ.push(queue.shift())
;}NORLQ={push:function(){}};}());}
