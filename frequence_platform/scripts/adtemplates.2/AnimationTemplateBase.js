/* Copr. (c) 2011, 4Mads */
var AnimationTemplateBase=FrameContainer.extend({_name:"template",_mainTimeline:null,init:function(d,f,e,a){this._mapTimelines={};this.allFrames={};this._super(document.createElement("div"),f,e);this.elt.className="template";this.elt.style.visibility="hidden";d.appendChild(this.elt);this._qs=[];var g=window.location.search.substring(1),c=g.split("&");while(c.length){var b=c.pop().split("=");this._qs[decodeURIComponent(b[0])]=decodeURIComponent(b[1])}this.setClickUrl(this._qs.clickTag||this._qs.clickTAG);while(a&&a.length)this.addTimeline(a.pop())},setClickUrl:function(a){if(a){this._clickUrl=a;this.elt.href=a;this.elt.target="_new"}},onBodyClick:function(){window.open(this._clickUrl)},getTimeline:function(a){return this._mapTimelines[a]},addTimeline:function(a){var b=a.getName();this._mapTimelines[b]=a;a.setTemplate(this);if(b=="main")this._mainTimeline=a;return a},removeTimeline:function(a){var b=a.getName();delete this._mapTimelines[b];if(this._mainTimeline==a)this._mainTimeline=null},processFrames:function(){this._super(this)},startSequence:function(){MySprite.addPendingCallback(MySprite.bindCall(this,a));function a(){this.elt.style.visibility="";var a=this._mainTimeline;if(a)if(a.getPaused())a.setPaused(false);else a.start();var b={x:this.getWidth(),y:this.getHeight()};this.clean(b);MySprite.clean()}},getParentContainer:function(){return null},getTemplate:function(){return this},getBannerWidth:MySprite.prototype.getWidth,setBannerWidth:MySprite.prototype.setWidth,getBannerHeight:MySprite.prototype.getHeight,setBannerHeight:MySprite.prototype.setHeight,clean:function(a){this._super(a);this.elt.style.width=pxa(this._width);this.elt.style.height=pxa(this._height)},cleanAll:function(){MySprite.clean()}},null,{trackingUrl:null})