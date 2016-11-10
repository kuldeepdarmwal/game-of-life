<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<link rel="shortcut icon" href="<?php echo base_url('images/favicon.png');?>" />
   <link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css' />
   <script type="text/javascript" src="<?php echo base_url('assets/ckeditor/ckeditor.js');?>"></script>
   <script type="text/javascript" src="<?php echo base_url('js/jquery-1.7.1.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('js/proposal_pdf.js');?>"></script>
   <style id="big">
   @font-face {
   font-family: 'Bebas Neue';
 src: url('https://s3.amazonaws.com/brandcdn-assets/fonts/BebasNeue-webfont.eot');
 src: url('https://s3.amazonaws.com/brandcdn-assets/fonts/BebasNeue-webfont.eot?#iefix') format('embedded-opentype'),
   url('https://s3.amazonaws.com/brandcdn-assets/fonts/BebasNeue-webfont.woff') format('woff'),  	
   url('https://s3.amazonaws.com/brandcdn-assets/fonts/BebasNeue-webfont.ttf') format('truetype'),	
   url('https://s3.amazonaws.com/brandcdn-assets/fonts/BebasNeue-webfont.svg#Bebas Neue') format('svg');
   font-weight: normal;
   font-style: normal;

 }
   /*CSS Document*/
* {margin:0; padding:0;}
img{border:none;}
table
{
    border-collapse: collapse;
}
body {
    padding-top:70px;
}
.header_icon
{
    position:absolute;
    right:0px;
    height:225px;
    width:225px;
}
.header_img
{
    height:225px;
    width:90px;
}
.header_class
{
    height:225px;
}
#p2_wrapper
{
    height:1600px;
    width:100%;
}
.p2_subtext
{
    position:relative;
    font-family: 'Oxygen', sans-serif;
    font-size:23px;
    left:420px;
    top:-30px;
    color:grey;
    width:700px;
}
#p2_static1
{
    left:0px;
    left:43%;
    
}
.p2_group1
{
    position:relative;
}
.p2_group2
{
    position:relative;
}
.p2_group3
{
    position:relative;
}
.p2_group4
{
    position:relative;
}
.p2_bigtext
{
    position:relative;
    left:50px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:72px;
    top:-20px;
    color:#414142;
}
.p2_headtext
{
    position:relative;
    top:-32px;
    left:30px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    color:#414142;
}
.p3_headsubtext
{
    position:relative;
    font-family: 'Oxygen', sans-serif;
    left:43%;
    font-size:23px;
    color:grey;
    width:700px;
}
.p3_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    color:#414142;
}
.p3_subtext
{
    position:relative;
    font-family: 'Oxygen', sans-serif;
    font-size:23px;
    color:grey;
}

.p3_textgroup1
{
    position:relative;
    top:30px;
}
.p3_textgroup2
{
    position:relative;
    top:50px;
}
.p3_textgroup3
{
    position:relative;
    right:30px;
}
.p3_geogroup1
{
    position:relative;
}
#p3_icon
{
    position:relative;
    top:30px;
    left:0px;
}
#p3_icontext
{
    position:relative;
    left:0%;
    height:400px;
    text-align:right;
}
#p3_info
{
    /*position:absolute;*/
    position:relative;
    right:30px;
top:5px;

    height:150px;
    width:81px;
}
#p3_infotext
{
    height:300px;
    text-align:right;
}
.p3_bigtext
{
    position:relative;
    left:72px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:72px;
    top:-20px;
    color:#414142;
}
.p3_geospan
{
    position:relative;
    margin:-50px 0 50px 0;
    height:50px;
    background-color:#5BCAE9;
    text-align:right;
    
}
.p3_geospantext
{
    padding-right:10px;
    font-family: 'Oxygen', sans-serif;
    font-size:16px;
    line-height:50px;
    color:white;
}
.p4_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:352px;
    color:#414142;
    width:665px;
}
.p4_headtextinfo
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:420px;
    color:#414142;
    width:660px;
}
.p4_subtext
{
    position:relative;
    font-family: 'Oxygen', sans-serif;
    left:420px;
    font-size:23px;
    color:grey;
    width:600px;
}
#p4_breaker
{
    position:relative;
    padding-top:30px;
    padding-bottom:30px;
}
.break
{
    display: block;
    clear: both;
    page-break-after: always;
}
.p4_bigtext
{
    position:relative;
    left:72px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:72px;
    color:#414142;
    width:600px;
}
.p4_bigheadtext
{
        position:relative;
    left:250px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:72px;
    color:#414142;
    width:600px;
}
#p4_demoimg
{
    padding-top:30px;
    padding-bottom:30px;
}
#p4_demographiclist
{
    margin-top:100px;
    width:760px;
    height:400px;
    margin-left:0px;
    text-align:right;
}
.p4_sitetext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:20px;
    color:#414142; 
    width:660px;
}
#p4_placement
{
    position:relative;
    align:center;
}
.p4_sitesubtext
{
    position:relative;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    font-size:16px;
/*    left:100px;*/

}
.p4_continuetext
{
    font-family: 'Oxygen', sans-serif;
    font-size:27px;
    color:grey;
}
table.p4_fixed
{
    position:relative;
    left:5%;
    right:5%;
    table-layout:fixed;
    width:90%;
}
table.p4_fixed td
{
    overflow: hidden;
    word-wrap:break-word;
    
}
table.p4_fixed tr
{
}
tr.p4_siterow
{
    height:30px;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    font-size:16px;
}
tr.p4_siterow2
{
    height:30px;
    background-color:#F6F6F6;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    font-size:16px;
}
table.p4_fixed th
{
    height:120px;
    position:relative;
    /*bottom:-35px;*/
    white-space:nowrap;
}
.th_rotate {
    filter: alpha (opacity=100);
    opacity:1;
    border:none;
    background-color:transparent;
    color:white;
    vertical-align:middle;
    text-align:left;
    -moz-transform: rotate(270deg);  /* FF3.5 */
    -o-transform: rotate(270deg);  /* Opera 10.5 */
    -webkit-transform: rotate(270deg);  /* Saf3.1+, Chrome */
    filter:  progid:DXImageTransform.Microsoft.BasicImage(rotation=4.712388);  /* IE6,IE7 */
    -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=4.712388)"; /* IE8 */
}
#p4_tablehead
{
    background-color:#6F7173;
}
.p4_td
{
    height:15px;
    text-align:center;
}
.p4_td2
{
    height:15px;
    text-align:center;
 
}
.p4_th
{
    position:relative;
    vertical-align:inherit;
    color:white;
}
.head_tr
{
    font-family: 'Oxygen', sans-serif;
    background-color:#97989A;
    font-size:16px;
}
.p5_subtext
{
    font-size:27px;
    position:relative;
    text-align:center;
    font-family: 'Oxygen', sans-serif;
    color:#6F7173;
}
.p5_disptr
{
    background-color:#EAEAEA;
}
.p5_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:10%;
    color:#414142;
    width:660px;
}
.p6_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:0px;
    color:#414142;
}
.p6_displaytext
{
    font-size:23px;
    font-family: 'Oxygen', sans-serif;
    color:grey;
}
.p6_displayhead
{
    position:relative;
    left:15%;
    right:15%;
    width:70%;
}
.p6_conf
{
    position:relative;
    padding-top:100px;
}
.p6_confhead
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    color:#414142;
    left:7%;
    width:60%
}
.p6_conftext
{
    font-size:23px;
    position:relative;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    left:7%;
    width:80%;
}
.p6_subtext
{
    font-size:23px;
    position:relative;
    left:15%;
    right:15%;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    width:70%
}
.p6_vars
{
    position:relative;
    float:right;
    text-align:right;
}
.p7_subtext
{
    font-size:23px;
    position:relative;
    left:43%;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    width:660px;
}
.p7_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    top:0px;
    left:43%;
    color:#414142;
    width:660px;
}
.p8_headsubtext
{

    font-size:23px;
    position:relative;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    left:12%;
    width:960px;
}
.p8_subtext
{
    font-size:23px;
    position:relative;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    width:960px;
    
}
.p8_headtext
{
   position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;

    color:#414142; 
}
.p8_textgroup1
{
    position:relative;
    left:240px;
    top:50px;
    width:1000px;
}
.p8_customdesign
{
    float:left;
}
.p8_textgroup2
{
    position:relative;
    left:20px;
    top:50px;
    width:540px;
}
.p8_richmedia
{
    float:right;
}
.p8_textgroup3
{
    position:relative;
    left:240px;
    top:50px;
    width:1000px;

}
.p8_advancedplatform
{
    float:left;
}
.p8_textgroup4
{
    position:relative;
    left:20px;
    top:50px;
    width:540px;
}
.p8_guarantee
{
    float:right;
}
.p8_rowgroup
{
    position:relative;
    margin-top:200px;
}
.p9_subtext
{
    font-size:23px;
    position:relative;
    left:12%;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    width:760px;
}
.p9_headtext
{
    position:relative;
    font-family: 'Bebas Neue', sans-serif;
    font-size:55px;
    text-align:right;
    left:-10%;
    color:#414142; 
}
.p9_righttext
{
    font-size:23px;
    position:relative;
    text-align:right;
    left:-10%;
    font-family: 'Oxygen', sans-serif;
    color:grey;
}
.px_bigtext
{
    position:relative;
    left:50px;
    font-family: 'Bebas Neue', sans-serif;
    font-size:72px;
    top:-20px;
    color:#414142;
}
.px_subtext
{
    font-size:23px;
    position:relative;
    left:43%;
    font-family: 'Oxygen', sans-serif;
    color:grey;
    width:700px;
}
.px_advlogo
{
    position:relative;
    left:43%;
    width: 700px;
    font-family: 'Oxygen', sans-serif;
    color:grey;
}
.px_toc
{
    font-size:27px;
    /*position:relative;*/
    font-family: 'Oxygen', sans-serif;
    color:grey;
}
.px_index
{
    border-collapse:separate;
    position:relative;
    width:700px;
    left:43%
}
.px_th
{
    width:2.5%;
    height:20px;
}
table tr.separator
{
    height:70px;
 }
#px_th1
{
    background-color:#D04646;
}
#px_th2
{
    background-color:#5BCAE9;
}
#px_th3
{
    background-color:#FCBB7F;
}
#px_th4
{
    background-color:#43AA96;
}
#px_th5
{
    background-color:#C697C0;
}
#px_th6
{
    background-color:#702469;
}
#px_th7
{
    background-color:#D1B632;
}
#px_th8
{
    background-color:#4795D1;
}
.separator
{
    font-size:27px;
    font-family: 'Oxygen', sans-serif;
    color:grey;  
}
   </style>
   <style id="styles" type="text/css">
   div.editable
     {
     border: solid 2px Transparent;
     }
div.editable:hover
{
  border-color:black;
}
</style>


<title>Vantage Local | Proposal</title>
</head>
<body style="width:1240px;">
   