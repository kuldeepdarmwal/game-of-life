/* Copr. (c) 2011, 4Mads */
var ScaleHide=Hide.extend({scalePercent:20,setPosition:function(d){this._super(d);var a=this.targetFrame;if(!a)return;var c=a.getClientSize(),g=c.x,f=c.y,e=this.scalePercent/100,b=e*this.easing(d,0,1,1);a.setPanX(-b*g/2);a.setPanY(-b*f/2);a.setScaleX(1+b);a.setScaleY(1+b)}})