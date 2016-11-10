<!doctype html>
 
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>jQuery UI Selectable - Serialize</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css" />
 
  <style>
     #feedback { font-size: 1.4em; }
     #selectable .ui-selecting { background: #FECA40; }
#selectable .ui-selected { background: #F39814; color: white; }
#selectable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#selectable li { margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
  </style>
  <script>
  $(function() {
	  $( "#selectable" ).selectable({
	      stop: function() {
		      var result = $( "#select-result" ).empty();
		      $( ".ui-selected", this ).each(function() {
			      var index = $( "#selectable li" ).index( this );
			      result.append( " #" + ( index + 1 ) );
			  });
		  }
	      });
      });
  </script>
</head>
<body>
 
<p id="feedback">
<span>Selected:</span> <span id="select-result">none</span>.
</p>

<div style = "width:800px;height:400px;overflow:auto"> 
<ol id="selectable">
  <li class="ui-widget-content">Item 1</li>
  <li class="ui-widget-content">Item 2</li>
  <li class="ui-widget-content">Item 3</li>
  <li class="ui-widget-content">Item 4</li>
  <li class="ui-widget-content">Item 5</li>
  <li class="ui-widget-content">Item 6</li>
</ol>
</div 
 
</body>
</html>