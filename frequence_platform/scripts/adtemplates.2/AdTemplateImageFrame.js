/* Copr. (c) 2011, 4Mads */
var AdTemplateImageFrame=AdTemplateContainerFrame.extend({_unscaledWidth:0,_unscaledHeight:0,getContent:function(){return this._content},setContent:function(a,c,b){this._content&&this._content.parentNode&&this._content.parentNode.removeChild(this._content);this._content=a;if(!a)return;a.parentNode&&a.parentNode.removeChild(a);this.ensureChildren();this.children.appendChild(a);this._unscaledWidth=Number(a.getAttribute("unscaled-width"))||c||a.width||a.naturalWidth||a.getAttribute("width");this._unscaledHeight=Number(a.getAttribute("unscaled-height"))||b||a.height||a.naturalHeight||a.getAttribute("height");this.dirty()},clean:function(j){this._super(j);if(!this._unscaledWidth||!this._unscaledHeight)return;var d=this._clientSize.x,c=this._clientSize.y,a,b;switch(this.getImageScaleMode()){default:case"meet":a=b=Math.min(d/this._unscaledWidth,c/this._unscaledHeight);break;case"stretch":a=d/this._unscaledWidth;b=c/this._unscaledHeight;break;case"1:1":a=b=1;break;case"fill":a=b=Math.max(d/this._unscaledWidth,c/this._unscaledHeight)}a*=this._imageScale;b*=this._imageScale;var i=this._unscaledWidth*a,h=this._unscaledHeight*b,e;if(this.getAnchorLeft()==this.getAnchorRight())e=(d-i)/2;else if(this.getAnchorLeft())e=0;else if(this.getAnchorRight())e=d-i;var f;if(this.getAnchorTop()==this.getAnchorBottom())f=(c-h)/2;else if(this.getAnchorTop())f=0;else if(this.getAnchorBottom())f=c-h;this.moveContent(e,f,a,b);var g=this._content.scale9grid;if(g){g.setPreScale(this._preScale);g.setPreserveCorners(this._preserveBorders)}},moveContent:function(e,f,c,d){var b=this._rect={x:e,y:f,width:this._unscaledWidth*c,height:this._unscaledHeight*d,scaleX:c,scaleY:d},a=this._content.style;a.left=px(b.x*this._scaleX+this._leftPadding+this._panX+this._imageXOffset);a.top=px(b.y*this._scaleY+this._topPadding+this._panY+this._imageYOffset);a.width=pxa(b.width*this._scaleX);a.height=pxa(b.height*this._scaleY)},getContentRectangle:function(){return this._rect},getContentScaleX:function(){return this._rect.scaleX},getContentScaleY:function(){return this._rect.scaleY}},null,{imageScaleMode:null,imageScale:1,preScale:1,preserveBorders:false,imageXOffset:0,imageYOffset:0})