/* Copr. (c) 2011, 4Mads */
var SlideIn=Animation.extend({direction:"T",offsetPercent:100,panFrameContent:false,setPosition:function(d){this._super(d);if(!this.targetFrame)return;var e=this.easing(d,1,-1,1),a=e*this.offsetPercent/100;if(this.panFrameContent){var c=this.targetFrame.getFrameSize();switch(this.direction){default:case"T":this.targetFrame.setPanY(a*-c.y);break;case"R":this.targetFrame.setPanX(a*c.x);break;case"B":this.targetFrame.setPanY(a*c.y);break;case"L":this.targetFrame.setPanX(a*-c.x)}}else{this.targetFrame.setVisible(d>0);if(!d)a=0;var b=this.targetFrame.getParentContainer().getVisibleSize();switch(this.direction){default:case"T":this.targetFrame.setOffsetY(a*-b.y);break;case"R":this.targetFrame.setOffsetX(a*b.x);break;case"B":this.targetFrame.setOffsetY(a*b.y);break;case"L":this.targetFrame.setOffsetX(a*-b.x)}}},reset:function(){this.updatePosition(1);this._super()}})